<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */
class Delivery_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }


    private $status = array(
        0 => 'pending',
        1 => 'ongoing',
        2 => 'picked_up',
        3 => 'delivered',
        4 => 'reported',
    );


    public function update_delivery_user_status($id,$status)
    {

        $this->db->where("id_user", $id);
        $this->db->update("user", array(
            'status' => intval($status)
        ));

        return TRUE;
    }

    public function update_products_cf(){


        $cf = $this->mDeliveryModel->getDefaultCF();

        if($cf != NULL)
            $cf_id = $cf['id'];
        else
            $cf_id = 0;

        $this->db->where('is_offer',0);
        $this->db->update('product',array(
            'cf_id' => $cf_id
        ));

    }

    public function getDefaultCF(){

        $this->db->where("label","Default_Delivery_Checkout_fields");
        $cf = $this->db->get("cf_list",1);
        $cf = $cf->result_array();

        if(isset($cf[0]))
            return $cf[0];

        return NULL;
    }

    public function disable_subscription(){


        $uri1 = $this->uri->segment(1);
        $uri2 = $this->uri->segment(2);
        $uri3 = $this->uri->segment(3);
        $id = intval($this->input->get("id"));

        if($uri1 == __ADMIN && $uri2 == "user" && $uri3 == "edit"){

            $grp = $this->getGrp();
            $this->db->where('grp_access_id',$grp->id);
            $this->db->where('id_user',$id);
            $count = $this->db->count_all_results('user');

            if($count > 0){
                 CMS_Display::replace("subscription_widget_v1","delivery/empty",NULL);
            }

        }

    }

    public function calculate_commission($invoice){

        $amount = $invoice->amount;

        $extras = array();

        if(ConfigManager::getValue('DELIVERY_FEES_TYPE') == "fixed"){

            $value = doubleval(ConfigManager::getValue('DELIVERY_FEES_VALUE'));

            $extras['delivery_fees'] = $value;


        }else if(ConfigManager::getValue('DELIVERY_FEES_TYPE') == "commissioned"){

            $value = doubleval(ConfigManager::getValue('DELIVERY_FEES_VALUE'));
            $value = ( ($value/100) * $amount );

            $extras['delivery_fees'] = $value;

        }


        $this->db->where('id',$invoice->id);
        $this->db->update('invoice',array(
            'extras' => json_encode($extras,JSON_FORCE_OBJECT)
        ));

        //add commission to an order
        if(isset($extras['delivery_fees'])){

            $this->db->where('id',$invoice->module_id);
            $this->db->update('order_list',array(
                'delivery_commission' => doubleval($extras['delivery_fees'])
            ));

            $this->db->where('id',$invoice->module_id);
            $this->db->update('order_list',array(
                'delivery_commission' => doubleval($extras['delivery_fees'])
            ));

        }

    }

    public function getPayoutObject($id)
    {

        $this->db->where("id", intval($id));
        $p = $this->db->get("payouts", 1);
        $p = $p->result_array();

        if (isset($p[0]))
            return $p[0];

        return NULL;

    }


    const ORDER_STATUS_PENDING = 1;
    const ORDER_STATUS_CONFIRMED = 2;
    const ORDER_STATUS_PREPARING = 3;
    const ORDER_STATUS_ON_DELIVERY = 4;
    const ORDER_STATUS_DELIVERED = 5;
    const ORDER_STATUS_CANCELLED = 6;
    const ORDER_STATUS_REPORTED = 7;

    public function getGrp()
    {

        $this->db->where('name', 'DeliveryBoy');
        $grp = $this->db->get('group_access', 1);
        $grp = $grp->result();

        if (count($grp) > 0) {
            return $grp[0];
        }

        return NULL;
    }

    public function updateGrp($user_id){

        $this->mDeliveryModel->update_grp($user_id);

        $this->db->where('id_user',$user_id);
        $this->db->where('user',array(
            'status' => -1
        ));


    }

    public function createDeliveryProfile($user_id){

        $this->db->insert('delivery_user',array(
            'user_id' => $user_id,
            'confirmed' => 0,
            'payment_method' => "",
            'payment_detail' => "",
            'updated_at' => date("Y-m-d H:i:s",time()),
            'created_at' => date("Y-m-d H:i:s",time()),
        ));

    }

    public function get_pending_users(){

        $grp = $this->getGrp();
        $this->db->where('grp_access_id',$grp->id);
        $this->db->where('status',0);
        $count = $this->db->count_all_results('user');
        return $count;
    }

    public function delivered_orders($user_id){

        $this->db->where('delivery_id',$user_id);
        $count = $this->db->count_all_results('order_list');
        return $count;

    }



    public function getOrdersToBeDelivered( $owner_id = 0)
    {

        $analytics = array();

        if ($owner_id > 0) {
            $this->db->join("store", "store.id_store=order_list.module_id", "inner");
            $this->db->join("user", "user.id_user=store.user_id", "inner");
            $this->db->where('order_list.module', "store");
            $this->db->where('user.id_user', $owner_id);
        }

        $this->db->where('order_list.status', self::ORDER_STATUS_PENDING);

        $this->db->select('COUNT(*) as number', FALSE);
        $query = $this->db->get('order_list');
        $list = $query->result();


        $analytics['count'] = $list[0]->number;
        $analytics['count_label'] = Translate::sprint("Order(s) to be delivered");
        $analytics['color'] = "#00a65a";
        $analytics['icon_tag'] = "<i class=\"mdi mdi-cart-outline\"></i>";
        $analytics['label'] = "Store";
        $analytics['link'] = admin_url("nsorder/all_orders");

        return $analytics;

    }


    public function getDeliveredAnalytics(){


        $data = array();

        $today_start = date("Y-m-d",time())." 00:00:00";
        $today_end = date("Y-m-d",time())." 24:00:00";

        $orders = $this->getOrdersQuery(
            $today_start,
            $today_end,
            intval(SessionManager::getData("id_user"))
        );

        //today
        $data['today'] = array(
            'amount' => 0,
            'count' => count($orders)
        );

        foreach ($orders as $order){
            $data['today']['amount'] = $order['delivery_commission'] + $data['today']['amount'];
        }

        $data['today']['amount'] = Currency::parseCurrencyFormat( $data['today']['amount'], PAYMENT_CURRENCY);

        $data['today']['string'] = Translate::sprintf("%s orders, %s",array(
            $data['today']['count'],
            $data['today']['amount']
        ));

        /*
         * Yesterday
         */

        $start = date("Y-m-d",strtotime("-1 day"))." 00:00:00";
        $end = date("Y-m-d",strtotime("-1 day"))." 24:00:00";

        $orders = $this->getOrdersQuery(
            $start,
            $end,
            SessionManager::getData('id_user')
        );


        $data['yesterday'] = array(
            'amount' => 0,
            'count' => count($orders)
        );

        foreach ($orders as $order){
            $data['yesterday']['amount'] = $order['delivery_commission'] + $data['yesterday']['amount'];
        }

        $data['yesterday']['amount'] = Currency::parseCurrencyFormat( $data['yesterday']['amount'], PAYMENT_CURRENCY);
        $data['yesterday']['string'] = Translate::sprintf("%s orders, %s",array(
            $data['yesterday']['count'],
            $data['yesterday']['amount']
        ));

        /*
        * END Yesterday
        */


        /*
         * This week
         */

        $start = date("Y-m-d H:i:s",strtotime("-7 day"));
        $end = date("Y-m-d H:i:s",time());


        $orders = $this->getOrdersQuery(
            $start,
            $end,
            SessionManager::getData('id_user')
        );




        $data['this_week'] = array(
            'amount' => 0,
            'count' => count($orders)
        );

        foreach ($orders as $order){
            $data['this_week']['amount'] = $order['delivery_commission'] + $data['this_week']['amount'];
        }

        $data['this_week']['amount'] = Currency::parseCurrencyFormat( $data['this_week']['amount'], PAYMENT_CURRENCY);
        $data['this_week']['string'] = Translate::sprintf("%s orders, %s",array(
            $data['this_week']['count'],
            $data['this_week']['amount']
        ));


        /*
        * END This week
        */


        /*
        * This month
        */

        $start = date("Y-m",time())."-01 00:00:00";
        $end = date("Y-m-t",time())." 24:00:00";

        $orders = $this->getOrdersQuery(
            $start,
            $end,
            SessionManager::getData('id_user')
        );


        $data['this_month'] = array(
            'amount' => 0,
            'count' => count($orders)
        );

        foreach ($orders as $order){
            $data['this_month']['amount'] = $order['delivery_commission'] + $data['this_month']['amount'];
        }

        $data['wallet'] = Currency::parseCurrencyFormat(  $data['this_month']['amount'] , PAYMENT_CURRENCY);

        $data['this_month']['amount'] = Currency::parseCurrencyFormat( $data['this_month']['amount'], PAYMENT_CURRENCY);
        $data['this_month']['string'] = Translate::sprintf("%s orders, %s",array(
            $data['this_month']['count'],
            $data['this_month']['amount']
        ));

        /*
        * END This month
        */


        /*
       * Last month
       */

        $start = date("Y-n-j", strtotime("first day of previous month"));
        $end = date("Y-n-j", strtotime("last day of previous month"));

        $orders = $this->getOrdersQuery(
            $start,
            $end,
            SessionManager::getData('id_user')
        );

        $data['last_month'] = array(
            'amount' => 0,
            'count' => count($orders)
        );

        foreach ($orders as $order){
            $data['last_month']['amount'] = $order['delivery_commission'] + $data['last_month']['amount'];
        }

        $data['last_month']['amount'] = Currency::parseCurrencyFormat( $data['last_month']['amount'], PAYMENT_CURRENCY);
        $data['last_month']['string'] = Translate::sprintf("%s orders, %s",array(
            $data['last_month']['count'],
            $data['last_month']['amount']
        ));

        /*
        * END last month
        */


        $last_payment = $this->get_last_payout(SessionManager::getData('id_user'));

        if($last_payment != NULL){

            $data['last_payment'] = Translate::sprintf("Your last payment was issued on %s. for an amount of %s",array(
                date("Y-m-d", strtotime($last_payment['updated_at'])),
                Currency::parseCurrencyFormat($last_payment['amount'], PAYMENT_CURRENCY)
            ));

        }else{

            $data['last_payment'] = "no payment sent";

        }

        return $data;
    }

    public function get_last_payout($user_id){

        $this->db->where('user_id',$user_id);
        $this->db->where('status',"paid");

        $payout = $this->db->get('payouts',1);
        $payout = $payout->result_array();

        if(isset($payout[0])){
            return $payout[0];
        }

        return NULL;
    }

    public function getOrdersQuery($start = NULL,$end = NULL, $dui = 0){

        $this->db->select("id,delivery_commission");
        $this->db->where('delivery_id',$dui);
        $this->db->where('delivered_at >=',$start);
        $this->db->where('delivered_at <=',$end);
        $this->db->where('status',self::ORDER_STATUS_DELIVERED);

        $orders = $this->db->get('order_list');
        $orders = $orders->result_array();

        return $orders;
    }

    public function update_grp($user_id)
    {

        $this->db->where('name', 'DeliveryBoy');
        $grp = $this->db->get('group_access', 1);
        $grp = $grp->result();

        if (count($grp) > 0) {

            $this->db->where('id_user', $user_id);
            $this->db->update('user', array(
                'grp_access_id' => $grp[0]->id
            ));

        } else {

            $grp_id = $this->generate_db_grp();

            $this->db->where('id_user', $user_id);
            $this->db->update('user', array(
                'grp_access_id' => $grp_id
            ));

        }


        return TRUE;
    }

    public function generate_db_grp()
    {

        $this->db->where('name', 'DeliveryBoy');
        $count = $this->db->count_all_results('group_access');

        if($count>0)
            return;

        $data = array();

        $actions = $this->db->get('module_actions');
        $actions = $actions->result_array();


        foreach ($actions as $action) {

            $data[$action['module']] = array();
            $ac = json_decode($action['actions'], JSON_OBJECT_AS_ARRAY);
            foreach ($ac as $value) {
                $data[$action['module']][$value] = 0;
            }

        }

        //$data
        $this->db->insert('group_access', array(
            'name' => 'DeliveryBoy',
            'permissions' => json_encode($data),
            'editable' => 0,
            'created_at' => date("Y-m-d H:i:s", time()),
            'updated_at' => date("Y-m-d H:i:s", time()),
        ));

        return $this->db->insert_id();

    }

    public function create_report_status()
    {

        $this->db->where('label', "reported");
        $this->db->where('id', 7);
        $count = $this->db->count_all_results('order_status');


        if($count == 1)
            return;

        $this->db->where('id', 7);
        $count = $this->db->count_all_results('order_status');

        $s = array();
        $s['user_id'] = 0;
        $s['order'] = 8;
        $s['label'] = "reported";
        $s['extras'] = '{"color":"#f99836"}';
        $s['created_at'] = date("Y-m-d H:i:s", time());
        $s['updated_at'] = date("Y-m-d H:i:s", time());

        if ($count > 0){
            $this->db->where('id', 7);
            $this->db->update('order_status', $s);
        }else{
            $s['id'] = 7;
            $this->db->insert('order_status', $s);
        }


    }

    public function check_delivery_order($order_id,$delivery_man_uid){

        $this->db->where('delivery_man_uid',$delivery_man_uid);
        $this->db->where('order_id',$order_id);
        $count = $this->db->count_all_results('delivery_order');

        if($count == 0){
            $this->db->insert('delivery_order',array(
                'order_id' => $order_id,
                'delivery_man_uid' => $delivery_man_uid,
                'delivery_status' => self::ORDER_STATUS_PENDING,
                'delivery_commission' => 0,
                'delivered_at' => date("Y-m-d H:i:s",time()),
                'updated_at' => date("Y-m-d H:i:s",time()),
                'created_at' => date("Y-m-d H:i:s",time()),
            ));
        }

    }

    public function updateOrder($params = array())
    {

        $errors = array();

        if (isset($params['order_id']) && $params['order_id'] == 0) {
            $errors[] = _lang("Order ID doesn't exists");
        }

        if (isset($params['status']) && !is_numeric($params['status'])
            && !in_array($params['status'], $this->status)) {
            $errors[] = _lang("Status invalid!");
        }

        if (isset($params['delivery_id']) && $params['delivery_id'] == 0) {
            $errors[] = _lang("Delivery ID invalid!");
        }


        if (empty($errors)) {

            $this->check_delivery_order($params['order_id'],intval($params['delivery_id']));

            $this->db->where('id', intval($params['order_id']));
            $order = $this->db->get('order_list');
            $order = $order->result_array();

            if (isset($order[0]) && $order[0]['delivery_status'] > 0) {

                $this->db->where('delivery_id', intval($params['delivery_id']));
                $this->db->where('id', intval($params['order_id']));
                $count = $this->db->count_all_results('order_list');

                if ($count == 0) {
                    return array(
                        Tags::SUCCESS => 0,
                        Tags::ERRORS => array("err" => _lang("Couldn't manage unassigned order")
                        )
                    );
                }

            }


            $this->db->where('id', intval($params['order_id']));
            $this->db->update('order_list', array(
                'delivery_id' => intval($params['delivery_id']),
                'delivery_status' => intval($params['status']),
                'updated_at' => date("Y-m-d H:i:s", time()),
            ));


            $status = intval($params['status']);

            if ($status == 0) { //pending



            } else if ($status == 1) { //ongoing



            } else if ($status == 2) { //picked_up

                //change order status to on delivery

                $this->update_order_timeline(
                    intval($params['order_id']),
                    4,
                    ""
                );

            } else if ($status == 3) { //delivered

                //change order status to on delivered

                $this->update_order_timeline(
                    intval($params['order_id']),
                    5,
                    ""
                );

                //push notification to the client
                $this->delivered_notification(intval($params['order_id']));


                //mark as paid if the payment COD
                $this->mark_as_paid(intval($params['order_id']));


                //update delivery date
                $this->db->where('id', intval($params['order_id']));
                $this->db->update('order_list', array(
                    'delivered_at' => date("Y-m-d H:i:s", time()),
                ));

            } else if ($status == 4) { //reported

                $message = "";

                if (isset($params['message'])) {
                    $message = $params['message'];
                }

                //change order status to reported
                $this->update_order_timeline(
                    intval($params['order_id']),
                    self::ORDER_STATUS_REPORTED,
                    $message
                );

                //send message to the business owner

                $store = $this->mOrder->getStoreFromCart(intval($params['order_id']));

                $this->load->model("Messenger/MessengerModel", "mMessengerModel");
                $this->db->select("id_user");
                $this->db->order_by("id_user", "ASC");
                $user = $this->db->get("user", 1);
                $user = $user->result();

                $result = $this->mMessengerModel->sendMessage(array(
                    "sender_id" =>  intval($params['delivery_id']),
                    "receiver_id" => $store['user_id'],
                    "discussion_id" => 0,
                    "content" => Text::input(Translate::sprintf("This order #%s was reported, the reason is: %s ",array(
                        intval($params['order_id']),$message
                    )))
                ));

            }

            return array(Tags::SUCCESS => 1, Tags::RESULT =>$params['order_id']);

        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }


    function mark_as_paid($order_id){

        if (ModulesChecker::isEnabled("order_payment")) {

            $this->db->where('id',$order_id);
            $this->db->update('order_list',array(
                'payment_status' => "cod_paid"
            ));

            $this->db->where('module','order_payment');
            $this->db->where('module_id',$order_id);
            $this->db->update('invoice',array(
                'paid' => 1,
                'status' => 1
            ));

        }

    }

    function manage_delivery_boy_balance($order_id){

        $order = $this->mOrder->getOrder($order_id);

        $result = $this->mWalletModel->add_Balance(
            $order['delivery_id'],
            $order['delivery_commission']
        );

    }


    function manage_payout(){


        $params = array(
            'limit' => 1,
        );


        $grp = $this->mDeliveryModel->getGrp();

        if($grp == NULL) return;

        
        $params['grp_acc_id'] = $grp->id;
        $params['is_super'] = TRUE;


        //get users count
        $this->db->where('grp_access_id',$grp->id);
        $users = $this->db->count_all_results('user');
        $params['limit'] = $users;

        //get users

        $data = $this->mUserModel->getUsers($params,function ($params){
            $this->db->where('grp_access_id',$params['grp_acc_id']);
        });


        foreach ($data[Tags::RESULT] as $obj) {

            $user_id = $obj['id_user'];

            $start = date("Y-m",strtotime("-1 month"))."-01 00:00:00";
            $end = date("Y-m-t",strtotime("-1 month"))." 23:59:59";


            $this->db->where('created_at <=',$end);
            $this->db->where('created_at >=',$start);
            $this->db->where('user_id',$user_id);
            $this->db->where('module','delivery');
            $count = $this->db->count_all_results('payouts');

            if($count == 0){
                //create payout
                $this->create_payout($user_id);
            }
        }


    }

    function create_payout($user_id){


        /*
         * This month
         */

        $start = date("Y-m",strtotime("-1 month"))."-01 00:00:00";
        $end = date("Y-m-t",strtotime("-1 month"))." 23:59:59";

        $orders = $this->getOrdersQuery(
            $start,
            $end,
            $user_id
        );



        $amount = 0;

        foreach ($orders as $order){
            $amount = $order['delivery_commission'] + $amount;
        }

        if($amount == 0)
            return;

        $params  = array(
            'method' => "--",
            'note' => "",
            'user_id' => $user_id,
            'amount' => $amount,
            'currency' => PAYMENT_CURRENCY,
            'status' => "processing",
        );

        $result = $this->mOrder->addPayout($params);


        if(isset($result[Tags::RESULT])){

            $this->db->where('id',intval($result[Tags::RESULT]));
            $this->db->update('payouts',array(
                'module'=> 'delivery',
                'created_at'=> $end,
                'updated_at'=> $end,
            ));

        }

        /*
        * END This month
        */

    }

    private function delivered_notification($order_id)
    {

        $order = $this->mOrder->getOrder($order_id);


        $store = $this->mOrder->getStoreFromCart($order_id);
        $store_name = $store["name"];
        $store_image = "";


        //fcm ,  store_name, status name
        $guest_id = $this->mUserModel->getGuestIDByUserId($order['user_id']);
        $guest = $this->mUserModel->getGuestData($guest_id);


        if (isset($store["images"][0]['name']))
            $store_image = $store["images"][0]['name'];


        if (empty($guest))
            return;


        $fcm_id = $guest['fcm_id'];
        $fcm_platform = $guest['platform'];


        $notif_body = Translate::sprint("We delivered your item");

        $this->load->model("notification/notification_model", "mNotificationModel");
        $this->mNotificationModel->sendCustomNotification($fcm_platform, $store_name, $notif_body, $fcm_id);

        //add historic
        $historic = NSHistoricManager::refresh(array(
            'module' => "nsorder",
            'module_id' => $order['id'],
            'auth_type' => "user",
            'auth_id' => $order['user_id'],
            'image' => json_encode(array($store_image)),
            'label' => $notif_body,
            'label_description' => $store_name,
        ));

    }

    private function update_order_timeline($order_id, $order_status, $message)
    {

        $this->mOrder->change_order_status($order_id, $order_status, $message);

    }


    public function create_default_checkout_fields()
    {

        $pdc_cf = ConfigManager::getValue("product_default_checkout_cf");
        $pdc_cf = intval($pdc_cf);
        if ($pdc_cf == 0) {

            $fields = array(
                0 => array(
                    "type" => "input.text",
                    "label" => "Full name",
                    "required" => 1,
                    "order" => 1,
                    "step" => 1,
                ),
                1 => array(
                    "type" => "input.phone",
                    "label" => "Phone",
                    "required" => 1,
                    "order" => 2,
                    "step" => 1,
                ),
                3 => array(
                    "type" => "input.location",
                    "label" => "Delivery to",
                    "required" => 1,
                    "order" => 4,
                    "step" => 1,
                ),
            );

            $label = "Default_Delivery_Checkout_fields";

            $result = $this->mCFManager->createCustomFields(array(
                "fields" => $fields,
                "label" => $label,
                "user_id" => SessionManager::getData("id_user"),
            ));

            if ($result[Tags::SUCCESS] == 1) {

                $id = $this->db->insert_id();

                $this->db->where("id", $id);
                $this->db->update("cf_list", array(
                    'editable' => 0
                ));


                return $id;
            }


        }

        return 0;
    }


    public function updateFields()
    {

        $this->load->dbforge();

        if (!$this->db->field_exists('delivery_id', 'order_list')) {
            $this->load->dbforge();
            $fields = array(
                'delivery_id' => array('type' => 'INT', 'default' => 0),
            );
            $this->dbforge->add_column('order_list', $fields);
        }

        if (!$this->db->field_exists('delivery_status', 'order_list')) {
            $this->load->dbforge();
            $fields = array(
                'delivery_status' => array('type' => 'INT', 'default' => 0),
            );
            $this->dbforge->add_column('order_list', $fields);
        }

        if (!$this->db->field_exists('delivery_commission', 'order_list')) {
            $this->load->dbforge();
            $fields = array(
                'delivery_commission' => array('type' => 'DOUBLE', 'default' => 0),
            );
            $this->dbforge->add_column('order_list', $fields);
        }

        if (!$this->db->field_exists('delivered_at', 'order_list')) {
            $this->load->dbforge();
            $fields = array(
                'delivered_at' => array('type' => 'DATETIME', 'default' => date("Y-m-d H:i:s",time())),
            );
            $this->dbforge->add_column('order_list', $fields);
        }

        if (!$this->db->field_exists('module', 'payouts')) {
            $this->load->dbforge();
            $fields = array(
                'module' => array('type' => 'VARCHAR(60)', 'default' => NULL),
            );
            $this->dbforge->add_column('payouts', $fields);
        }

    }


    public function verify_pid(){

        $pid = ConfigManager::getValue("DELIVERY_MODULE_PID");

        if($pid == ""){
            return array(Tags::SUCCESS=>0);
        }

        //execute api
        $api_endpoint = "https://api.droideve.com/api/api2/pchecker";
        $post_data = array(
            "pid" => $pid,
            "item" => "1.0,df-delivery-module",
            "reqfile" => 1,
        );


        $response = MyCurl::run($api_endpoint,$post_data);
        $response = json_decode($response,JSON_OBJECT_AS_ARRAY);

        $response[] = $pid;

        if(!isset($response[Tags::SUCCESS]))
            return array(Tags::SUCCESS=>0);

        if(isset($response[Tags::SUCCESS]) && $response[Tags::SUCCESS]==0)
            return $response;


        $sql = base64_decode($response['datasql']);
        $sql_list = array();

        if(preg_match("#;#",$sql)){
            $sql_list = explode(";",$sql);
        }else
            $sql_list[] = $sql;

        foreach ( $sql_list as $query) {
            if(trim($query)!="")
                $this->db->query($query);
        }


        return array(Tags::SUCCESS=>1);
    }


    public function createTables(){
        $this->createDeliveryOrderTable();
        $this->createDeliveryUserTable();
    }

    public function createDeliveryUserTable()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(

            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),

            'confirmed' => array(
                'type' => 'INT',
                'default' => 0
            ),

            'payment_method' => array(
                'type' => 'VARCHAR(100)',
                'default' => "bank"
            ),

            'payment_detail' => array(
                'type' => 'TEXT',
                'default' => ""
            ),

            'updated_at' => array(
                'type' => 'DATETIME'
            ),

            'created_at' => array(
                'type' => 'DATETIME'
            ),
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('user_id', TRUE);
        $this->dbforge->create_table('delivery_user', TRUE, $attributes);

        //==========   ==========/


    }


    public function createDeliveryOrderTable()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(

            'order_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),

            'delivery_man_uid' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'delivery_status' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'delivery_commission' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),

            'delivery_duration' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),

            'delivery_target' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),

            'delivered_at' => array(
                'type' => 'DATETIME'
            ),

            'updated_at' => array(
                'type' => 'DATETIME'
            ),

            'created_at' => array(
                'type' => 'DATETIME'
            ),
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('order_id', TRUE);
        $this->dbforge->create_table('delivery_order', TRUE, $attributes);

        //==========   ==========/


    }

}