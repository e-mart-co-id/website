<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */
class Wallet_model extends CI_Model
{

    const AMOUNTS = array(
        50000,150000,300000,600000,1200000,2000000
    );

    public function __construct()
    {
        parent::__construct();
    }


    public function autoRenew($user_id){

        $balance = $this->getBalance($user_id);

        $invoice = $this->mPaymentModel->getInvoice_by_user_id($user_id,0);

        if($invoice==NULL)
            return FALSE;

        if($balance >= $invoice->amount){//release balance


            $result = $this->mWalletModel->releaseBalance(
                SessionManager::getData("id_user"),
                $invoice->amount
            );

            $key = md5("abc-key".$invoice->id);
            $result = $this->mPaymentModel->updateInvoice(
                $invoice->id,
                "wallet",
                "w".date("Y-m-d/h:i:s-A",time()),
                $key,
                FALSE
            );

            if($result)
                return TRUE;

        }

        return FALSE;
    }

    public function create_invoice($user_id,$amount){

        $items = array();

        $items[] = array(
            'item_id'=> $user_id,
            'item_name'=> "Add balance of %s",
            'price'=> $amount,
            'qty' => 1,
            'unit' => 'item',
            'price_per_unit' => $amount,
        );

        if($amount==0)
            return array(Tags::SUCCESS=>0);

        $this->db->where('user_id',$user_id);
        $no = $this->db->count_all_results('invoice');
        $no++;

        $data = array(
            "method"    => "",
            "amount"    => $amount,
            "no"        => $no,
            "module"    => "wallet",
            "module_id"    => $user_id,
            "tax_id"       => 0,
            "items"       => json_encode($items,JSON_FORCE_OBJECT),
            "currency"  => PAYMENT_CURRENCY,
            "status"    => 0,
            "user_id"   => $user_id,
            "transaction_id"   => "",
            "created_at"        => date("Y-m-d H:i:s",time())
        );



        $this->db->where('module','wallet');
        $this->db->where('user_id',$user_id);
        $invoice = $this->db->get('invoice',1);
        $invoice = $invoice->result();

        if(!isset($invoice[0])){

            $data['created_at'] = date("Y-m-d H:i:s",time());
            $data['updated_at'] = date("Y-m-d H:i:s",time());

            $this->db->insert('invoice',$data);
            $id = $this->db->insert_id();

        }else{
            $this->db->where('id',$invoice[0]->id);
            $this->db->update('invoice',$data);
            $id = $invoice[0]->id;
        }


        return array(Tags::SUCCESS=>1,Tags::RESULT=>$id);

    }


    public function getBalance($user_id){

        $this->db->where('user_id',$user_id);
        $wallet = $this->db->get('wallet',1);
        $wallet = $wallet->result();

        if(isset($wallet[0])){
            return $wallet[0]->balance;
        }

        return 0;
    }

    public function releaseBalance($user_id,$amount){

        $this->db->where('user_id',$user_id);
        $this->db->where('balance >=',$amount);
        $wallet = $this->db->get('wallet',1);
        $wallet = $wallet->result();


        if(isset($wallet[0])){

            $this->db->where('id',$wallet[0]->id);
            $this->db->update('wallet',array(
                'balance' =>  $wallet[0]->balance-$amount,
                'updated_at' =>  date("Y-m-d H:i:s",time()),
            ));
            return TRUE;
        }

        return FALSE;
    }


    public function add_Balance($user_id,$amount){

        $this->db->where('user_id',$user_id);
        $wallet = $this->db->get('wallet',1);
        $wallet = $wallet->result();

        if(isset($wallet[0])){
            $this->db->where('id',$wallet[0]->id);
            $this->db->update('wallet',array(
                'balance' =>  $wallet[0]->balance+$amount,
                'updated_at' =>  date("Y-m-d H:i:s",time()),
            ));
            return TRUE;
        }

        $this->db->insert('wallet',array(
            'balance' =>  $amount,
            'user_id' =>  $user_id,
            'created_at' =>  date("Y-m-d H:i:s",time()),
            'updated_at' =>  date("Y-m-d H:i:s",time()),
        ));

        return TRUE;
    }


    public function createTables()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'balance' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),
            'currency' => array(
                'type' => 'VARCHAR(10)',
                'default' => NULL
            ),
            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            'updated_at' => array(
                'type' => 'DATETIME'
            ),
            'created_at' => array(
                'type' => 'DATETIME'
            ),
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('wallet', TRUE, $attributes);


    }



}