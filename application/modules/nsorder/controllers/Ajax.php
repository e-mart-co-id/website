<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();


    }


    public function saveCommissionConfig(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDER_CONFIG_ADMIN)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $ORDER_COMMISSION_ENABLED = $this->input->post("ORDER_COMMISSION_ENABLED");
        $ORDER_COMMISSION_VALUE = $this->input->post("ORDER_COMMISSION_VALUE");

        ConfigManager::setValue("ORDER_COMMISSION_ENABLED",$ORDER_COMMISSION_ENABLED);
        ConfigManager::setValue("ORDER_COMMISSION_VALUE",$ORDER_COMMISSION_VALUE);

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }

    public function query(){

        $url = $this->input->post('url');
        $query = $this->input->post("query");
        $string = (http_build_query($query, '', '&'));

        echo json_encode(array(Tags::SUCCESS=>1,'url'=>$url."?".$string));
    }


    public function delete_payout()
    {

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN)) {
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

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN)) {
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
            'amount' => $this->input->post('amount'),
            'currency' => $this->input->post('currency'),
            'status' => $this->input->post('status'),
        );

        echo json_encode($this->mOrder->editPayout($params));
        return;
    }


    public function add_payout()
    {

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN)) {
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
            'status' => $this->input->post('status'),
        );

        //print_r($params); die();

        echo json_encode($this->mOrder->addPayout($params));
        return;
    }



    public function payment_update_status(){

        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDER_CONFIG_ADMIN)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $order_id = intval($this->input->post('order_id'));
        $payment_status_id = $this->input->post('payment_status');
        $status = Order_payment::PAYMENT_STATUS;

        if(isset($status[$payment_status_id])){

            $this->db->where('id',$order_id);
            $this->db->update('order_list',array(
                'payment_status' => $payment_status_id
            ));

        }else{

            echo json_encode(array(Tags::SUCCESS=>0));
            return;

        }


        echo json_encode(array(Tags::SUCCESS=>1));
    }

    public function order_update_status(){

        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $order_id = $this->input->post('order_id');
        $order_status_id = $this->input->post('order_status');
        $message = $this->input->post('message');


        $this->mOrder->change_order_status($order_id, $order_status_id, $message);


        echo json_encode(array(Tags::SUCCESS=>1));
    }

    public function order_categories_edit(){

        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDER_CONFIG_ADMIN)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $cat_id = intval($this->input->post("cat_id"));
        $cf_id = intval($this->input->post("cf_id"));

        $this->db->where("id_category",$cat_id);
        $this->db->update("category",array(
            "cf_id"=>intval($cf_id)
        ));

        echo json_encode(array(Tags::SUCCESS=>1));

    }

    public function order_status_re_order(){


        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDER_STATUS_LIST_ADMIN)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $orders = $this->input->post("re_orders");
        $result = $this->mOrder->re_order($orders,SessionManager::getData("id_user"));

        echo json_encode(array(Tags::SUCCESS=>1));
    }

    public function order_status_edit(){

        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDER_STATUS_LIST_ADMIN)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDER_STATUS_LIST_ADMIN)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $order = $this->input->post("order");
        $label = $this->input->post("label");
        $color = $this->input->post("color");

        $id = intval($this->input->post("id"));

        $result = $this->mOrder->edit(array(
            "color" => $color,
            "order" => $order,
            "label" => $label,
            "user_id" => SessionManager::getData("id_user"),
            "id" => $id,
        ));

        echo json_encode($result);return;
    }


    public function order_status_add(){

        if(!GroupAccess::isGranted('nsorder',MANAGE_ORDER_STATUS_LIST_ADMIN)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $order = $this->input->post("order");
        $label = $this->input->post("label");
        $color = $this->input->post("color");

        $result = $this->mOrder->add(array(
            "order" => $order,
            "label" => $label,
            "color" => $color,
            "user_id" => SessionManager::getData("id_user"),
        ));

        echo json_encode($result);return;
    }



    public function order_delete(){

        if(!GroupAccess::isGranted('nsorder')){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $data = $this->mOrder->deleteOrder(
                array( "id" => intval($this->input->get("id")))
            );

            echo json_encode($data);

        }else{
            echo json_encode(array(Tags::SUCCESS=>0));
        }

    }


}
