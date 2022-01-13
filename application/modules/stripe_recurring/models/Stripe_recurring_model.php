<?php
class Stripe_recurring_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	public $table_subscription = "subscription";

    /*
     * Disable subscription
     */

    public function getExpiredTrial(){

        $this->db->where('is_trail',1);
        $this->db->where('next_billing <=',date("Y-m-d H:i:s",time()));
        $subscription = $this->db->get($this->table_subscription,1);
        $subscription = $subscription->result_array();

        return $subscription;
    }

	public function cancel_subscription($subscription_id){

        $this->db->where('subscription_id',$subscription_id);
        $subscription = $this->db->get($this->table_subscription,1);
        $subscription = $subscription->result();

        if(!isset($subscription[0]))
            return;

        if(isset($subscription[0])){

            $this->mPack->cancelSubscription($subscription[0]->user_id,$subscription[0]->pack_id);

            $this->db->where('subscription_id',$subscription_id);
            $this->db->delete($this->table_subscription);

        }

    }

	public function saveSubscription($data=array()){

        $data['created_at'] = date("Y-m-d H:i:s",time());
        $data['updated_at'] = date("Y-m-d H:i:s",time());

	    $this->db->insert($this->table_subscription,$data);

	    return $this->db->insert_id();
    }

    /*
     * Renew subscription
     */

    public function updateSubscription($subscription_id,$data=array()){


        $this->db->where('subscription_id',$subscription_id);
        $subscription = $this->db->get($this->table_subscription,1);
        $subscription = $subscription->result_array();

        if(!isset($subscription[0])){
            return FALSE;
        }

        $plan = $subscription[0]['plan'];
        $plan = explode("-",$plan);

        $args = array(
            "invoiceId"         => intval($plan[0]),
            "transaction_id"         => $data['transaction_id'],
        );

        //update the invoice and apply the update
        $result = $this->mPack->confirmPayment($args);

        $this->update_invoice(
            intval($plan[0]),
            $data['transaction_id'],
            "stripe_recurring"
        );

        if(!$result)
            throw new Exception("Couldn't apply a pack for subscription ".json_encode($args));


        //update the subscription
        $data['updated_at'] = date("Y-m-d H:i:s",time());
        $data['is_trial'] = 0;

        $this->db->where('subscription_id',$subscription_id);
        $this->db->update($this->table_subscription,$data);

        $user_params = $this->getUserParams($subscription[0]['user_id']);

        $this->db->where('subscription_id',$subscription_id);
        $this->db->update($this->table_subscription,array(
            'next_billing' => $user_params["will_expired"],
            'is_trial' => 0
        ));

        return TRUE;
    }

    public function createSubscription($params=array(),$type="stripe_recurring"){


        $plan = explode("-",$params['plan_id']);
        $invoice_id = intval($plan[0]);

        $result = $this->getPackFromInvoice($invoice_id);

        if($result==NULL){
            echo "It couldn't get pack from invoice (iv_id=$invoice_id - createSubscription)";
            exit;
        }

        $subscription = [
            "type" => $params['type'],
            "pack_id" => $result['pack']->id,
            "plan" => $params['plan_id'],
            "user_id" => $result['invoice']->user_id,
            "subscription_id" => $params['subscription_id'],
            "customer_id" => $params['customer_id'],
        ];

        $id = $this->saveSubscription($subscription);

        return $id;
    }

    private function update_invoice($id,$tran_id,$method){

        $this->db->where("id", $id);
        $this->db->where("status", 0);
        $invoice = $this->db->get('invoice',1);
        $invoice = $invoice->result();
        $invoice = $invoice[0];

        $params = array(
            'transaction_id'    => $tran_id,
            'status'            => 1,
            'tax_id'            => 0,
            "method"            => $method,
            "updated_at"        => date("Y-m-d H:i:s",time())
        );

        if(!TaxManager::isDisabled($invoice->module)){
            if(defined('DEFAULT_TAX') and DEFAULT_TAX>0){
                $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                if($tax!=NULL){
                    $params['tax_id'] = $tax['id'];
                }
            }else if(defined('DEFAULT_TAX') and DEFAULT_TAX == -2){
                if(defined('MULTI_TAXES') and count(MULTI_TAXES)>0){
                    $params['tax_id'] = -2;
                    $params['taxes'] = MULTI_TAXES ;
                }
            }
        }

        $this->db->where("id", $id);
        $this->db->where("status", 0);
        $this->db->update('invoice',$params);

        $this->db->insert('payment_transactions',array(
            "agreement_id" => $tran_id,
            "transaction_id" => $tran_id,
            "user_id" => $invoice->user_id,
            "status" => "invoice_updated",
        ));

        return TRUE;
    }


    //check
    public function enableTrial($subscription_id){

        $this->db->where('subscription_id',$subscription_id);
        $subscription = $this->db->get($this->table_subscription,1);
        $subscription = $subscription->result_array();

        if(!isset($subscription[0])){
            return;
        }

        $plan = $subscription[0]['plan'];
        $plan = explode("-",$plan);

        $invoice_id = intval($plan[0]);

        $result = $this->getPackFromInvoice($invoice_id);

        if($result==NULL){
            echo "It couldn't get pack from invoice (iv_id=$invoice_id)";
            exit;
        }

        $pack = $result['pack'];
        $user_id = $result['user_id'];
        $invoice = $result['invoice'];

        $result = $this->mPack->enableTrialPeriod(
            $pack->id,
            $user_id,
            TRUE
        );

        if($result && $pack->trial_period > 0){

            $this->db->where('subscription_id',$subscription_id);
            $this->db->update($this->table_subscription,array(
                'next_billing' => date("Y-m-d H:i:s",strtotime(' +'.intval($pack->trial_period).'  day')),
                'is_trial' => 1
            ));

        }

        return TRUE;
    }

    private function getPackFromInvoice($invoiceId=0){

        $this->db->where('module',"pack");
        //$this->db->where('status',0);
        $this->db->where('id',$invoiceId);
        $invoice= $this->db->get("invoice",1);
        $invoice = $invoice->result();

        if(count($invoice)>0){

            $items = json_decode($invoice[0]->items,JSON_OBJECT_AS_ARRAY);
            $user_id = $invoice[0]->user_id;

            foreach ($items as $value){

                $pack = $this->mPack->getPack(intval($value['item_id']));

               return array(
                   'invoice'=> $invoice[0],
                   'pack'=> $pack,
                   'user_id'=> intval($user_id)
               );
            }

        }

        return NULL;
    }


    public function getSubscription($uid){

        $this->db->where('user_id',$uid);
        $subscription = $this->db->get('subscription',1);
        $subscription = $subscription->result();

        return $subscription;
    }

	public function hasSubscribe($uid){

	    $this->db->where('user_id',$uid);
	    $count = $this->db->count_all_results('subscription');

	    if($count>0)
	        return TRUE;

	    return FALSE;
    }

    public function getUserParams($uid){

        $this->db->where('user_id',$uid);
        $user_subscribe_setting = $this->db->get('user_subscribe_setting',1);
        $user_subscribe_setting = $user_subscribe_setting->result_array();

        return $user_subscribe_setting[0];
    }

    public function create_table()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'pack_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            'plan' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),
            'paid' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),
            'type' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'subscription_id' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'customer_id' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            'transaction_id' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'next_billing' => array(
                'type' => 'DATETIME',
                'default' => NULL
            ),

            'is_trial' => array(
                'type' => 'INT',
                'default' => 0
            ),

            'updated_at' => array(
                'type' => 'DATETIME',
            ),
            'created_at' => array(
                'type' => 'DATETIME'
            ),
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table($this->table_subscription, TRUE, $attributes);

    }
}
