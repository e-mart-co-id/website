<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Ajax extends AJAX_Controller {

    public function __construct(){
        parent::__construct();
        //load model
    }

    public function accept(){

        if(!GroupAccess::isGranted('user',MANAGE_USERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = intval($this->input->get('id'));
        $this->mDeliveryModel->update_delivery_user_status($id,1);

        echo json_encode(array(Tags::SUCCESS=>1));return;
    }

    public function decline(){

        if(!GroupAccess::isGranted('user',MANAGE_USERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = intval($this->input->get('id'));
        $this->mDeliveryModel->update_delivery_user_status($id,-1);

        echo json_encode(array(Tags::SUCCESS=>1));return;
    }

    public function set_pid(){


        $id = $this->input->post("pid");

        if($id != ""){
            ConfigManager::setValue("DELIVERY_MODULE_PID",$id);
            echo json_encode(array(Tags::SUCCESS=>1));return;
        }

        echo json_encode(array(Tags::SUCCESS=>0));return;
    }

    public function saveDFeesConfig(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDER_CONFIG_ADMIN)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $DELIVERY_FEES_TYPE = $this->input->post("DELIVERY_FEES_TYPE");
        $DELIVERY_FEES_VALUE = $this->input->post("DELIVERY_FEES_VALUE");

        ConfigManager::setValue("DELIVERY_FEES_TYPE",$DELIVERY_FEES_TYPE);
        ConfigManager::setValue("DELIVERY_FEES_VALUE",$DELIVERY_FEES_VALUE);

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }

    public function saveDBannerConfig(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDER_CONFIG_ADMIN)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $DELIVERY_IOS_LINK = $this->input->post("DELIVERY_IOS_LINK");
        $DELIVERY_ANDROID_LINK = $this->input->post("DELIVERY_ANDROID_LINK");

        ConfigManager::setValue("DELIVERY_IOS_LINK",$DELIVERY_IOS_LINK);
        ConfigManager::setValue("DELIVERY_ANDROID_LINK",$DELIVERY_ANDROID_LINK);

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }

    public function delete_payout()
    {

        if (!GroupAccess::isGranted('delivery',MANAGE_DELIVERY_PAYOUTS)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $this->db->where('id', $this->input->post('id'));
        $this->db->delete('payouts');

        echo json_encode(array(Tags::SUCCESS=>1));
        return;
    }

    public function edit_payout()
    {

        if (!GroupAccess::isGranted('delivery',MANAGE_DELIVERY_PAYOUTS)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $params  = array(
            'id' => $this->input->post('id'),
            'method' => $this->input->post('method'),
            'note' => $this->input->post('note'),
            'user_id' => $this->input->post('user_id'),
            'currency' => $this->input->post('currency'),
            'status' => $this->input->post('status'),
        );


        $payout = $this->mOrder->getPayoutObject(intval($this->input->post('id')));


        if($payout['status'] == 'cancel'){
            echo json_encode(array(Tags::SUCCESS=>1,"url"=>admin_url("delivery/payouts")));
            return;
        }

        $result = $this->mOrder->editPayout($params);

        $result['url'] = admin_url("delivery/payouts");

        echo json_encode($result);
        return;
    }


    public function add_payout()
    {

        if (!GroupAccess::isGranted('delivery',MANAGE_DELIVERY_PAYOUTS)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $params  = array(
            'method' => $this->input->post('method'),
            'note' => $this->input->post('note'),
            'user_id' => $this->input->post('user_id'),
            'amount' => $this->input->post('amount'),
            'currency' => $this->input->post('currency'),
            'status' => "processing",
        );

        $result = $this->mOrder->addPayout($params);

        $result['url'] = admin_url("delivery/payouts");


        echo json_encode($result);
        return;
    }


    public function getDeliveryUsers(){


        if(!GroupAccess::isGranted('delivery',MANAGE_DELIVERY_PAYOUTS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $params = array(
            'limit' => 5,
            'search' => $this->input->get("q"),
        );

        $grp = $this->mDeliveryModel->getGrp();
        $params['grp_acc_id'] = $grp->id;
        $params['is_super'] = TRUE;

        $data = $this->mUserModel->getUsers($params,function ($params){
            $this->db->where('grp_access_id',$params['grp_acc_id']);
        });

        $json = array();
        foreach ($data[Tags::RESULT] as $obj) {

            $start = date("Y-m",time())."-01 00:00:00";
            $end = date("Y-m-t",time())." 24:00:00";

            $orders = $this->mDeliveryModel->getOrdersQuery(
                $start,
                $end,
                $obj['id_user']
            );


            $amount = 0;

            foreach ($orders as $order){
                $amount = $order['delivery_commission'] + $amount;
            }

            $json[] = array(
                "text" => $obj['name'] . ", @" . $obj['username']. ", "._lang("Balance").": " . Currency::parseCurrencyFormat($amount, PAYMENT_CURRENCY) ,
                "id" => $obj['id_user'],
                "balance" => $amount,
                "balance_wc" => Currency::parseCurrencyFormat($amount, PAYMENT_CURRENCY)
            );
        }

        echo json_encode($json);
        return;

    }

}

/* End of file UploaderDB.php */