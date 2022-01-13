<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Nsorder_model extends CI_Model
{


    const ORDER_STATUS_PENDING = 1;
    const ORDER_STATUS_CONFIRMED = 2;
    const ORDER_STATUS_PREPARING = 3;
    const ORDER_STATUS_ON_DELIVERY = 4;
    const ORDER_STATUS_DELIVERED = 5;
    const ORDER_STATUS_CANCELLED = 6;
    const ORDER_STATUS_REPORTED = 7;


    public function getPayoutObject($id)
    {


        $this->db->where("id", intval($id));
        $p = $this->db->get("payouts", 1);
        $p = $p->result_array();

        if (isset($p[0]))
            return $p[0];

        return NULL;

    }


    public function getPayout($params = array(),$whereArray=array())
    {

        extract($params);


        if (!isset($page))
            $page = 1;

        if (!isset($limit))
            $limit = 20;

        if (isset($user_id))
            $this->db->where("user_id", $user_id);

        if (isset($status) and $status != 2)
            $this->db->where("status", $status);

        if (isset($payout_id) and $payout_id > 0)
            $this->db->where("id", $payout_id);

        if (isset($transaction_id) and $transaction_id > 0)
            $this->db->where("id", $transaction_id);

        if (!empty($whereArray))
            $this->db->where($whereArray);

        $count = $this->db->count_all_results("payouts");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        if (isset($user_id))
            $this->db->where("user_id", $user_id);

        if (isset($status) and $status != 2)
            $this->db->where("status", $status);

        if (isset($payout_id) and $payout_id > 0)
            $this->db->where("id", $payout_id);

        if (isset($transaction_id) and $transaction_id > 0)
            $this->db->where("id", $transaction_id);

        if (!empty($whereArray))
            $this->db->where($whereArray);

        $this->db->from("payouts");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());

        //$this->db->group_by("payouts.created_at", "DESC");

        if (isset($order_by_date) and $order_by_date == 1)
            $this->db->order_by("payouts.created_at", "DESC");
        else
            $this->db->order_by("payouts.created_at", "ASC");

        $payout = $this->db->get();
        $payout = $payout->result_array();

        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $payout);
    }

    public function addPayout($params = array())
    {
        extract($params);

        if (isset($user_id) and $user_id > 0) {
            $data['user_id'] = intval($user_id);
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_SELECTED);
        }

        if (isset($amount) and doubleval($amount) > 0) {
            $data['amount'] = doubleval($amount);
        } else {
            $errors['amount'] = Translate::sprint(Messages::AMOUNT_NOT_SELECTED);
        }

        if (isset($method) and $method != "") {
            $data['method'] = Text::input($method);
        } else {
            $errors['method'] = Translate::sprint(Messages::METHOD_NOT_SELECTED);
        }

        if (isset($status) and $status != "") {
            $data['status'] = Text::input($status);
        } else {
            $errors['status'] = Translate::sprint(Messages::STATUS_NOT_SELECTED);
        }

        if (isset($currency) and $currency != "") {
            $data['currency'] = Text::input($currency);
        } else {
            $data['currency'] = DEFAULT_CURRENCY;
        }

        if (isset($note) and $note != "") {
            $data['note'] = Text::inputWithoutStripTags($note);
        }


        if (empty($errors) and !empty($data)) {

            $data['created_at'] = date("Y-m-d H:i:s", time());
            $data['created_at'] = MyDateUtils::convert($data['created_at'], TimeZoneManager::getTimeZone(), "UTC");
            $data['updated_at'] = $data['created_at'];

            $this->db->insert("payouts", $data);

            $payout_id = $this->db->insert_id();

            return array(Tags::SUCCESS => 1, Tags::RESULT => $payout_id, "url" => admin_url("nsorder/payouts"));

        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }

    public function editPayout($params = array())
    {
        extract($params);

        if (isset($id) and $id > 0) {
            $data['id'] = intval($id);
        } else {
            $errors['id'] = _lang("ID is missing!");
        }

        if (isset($user_id) and $user_id > 0) {
            $data['user_id'] = intval($user_id);
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_SELECTED);
        }

        if (isset($method) and $method != "") {
            $data['method'] = Text::input($method);
        } else {
            $errors['method'] = Translate::sprint(Messages::METHOD_NOT_SELECTED);
        }

        if (isset($status) and $status != "") {
            $data['status'] = Text::input($status);
        } else {
            $errors['status'] = Translate::sprint(Messages::STATUS_NOT_SELECTED);
        }

        if (isset($currency) and $currency != "") {
            $data['currency'] = Text::input($currency);
        } else {
            $data['currency'] = DEFAULT_CURRENCY;
        }

        if (isset($note) and $note != "") {
            $data['note'] = Text::inputWithoutStripTags($note);
        }


        if (empty($errors) and !empty($data)) {

            $data['created_at'] = date("Y-m-d H:i:s", time());
            $data['created_at'] = MyDateUtils::convert($data['created_at'], TimeZoneManager::getTimeZone(), "UTC");
            $data['updated_at'] = $data['created_at'];

            $this->db->where("id", $id);
            $this->db->update("payouts", $data);

            return array(Tags::SUCCESS => 1, "url" => admin_url("nsorder/payouts"));

        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }


    public function getOrdersAnalyticsToday($owner_id = 0)
    {

        $hours = array();

        for ($i = 0; $i <= 24; $i++) {
            if ($i < 10) {
                $hours[] = date("Y-m-d 0" . $i . ":00:00", time());
            } else if ($i == 24) {
                $hours[] = date("Y-m-d 23:59:59", time());
            } else {
                $hours[] = date("Y-m-d " . $i . ":00:00", time());
            }
        }

        $analytics = array();

        foreach ($hours as $d) {

            if ($owner_id) {
                $this->db->where("store.user_id", $owner_id);
                $this->db->join("store", "order_list.module_id=store.id_store");
            }

            $this->db->where('order_list.created_at >', date("Y-m-d H:00:00", strtotime($d)));
            $this->db->where('order_list.created_at <', date("Y-m-d H:59:59", strtotime($d)));

            $count = $this->db->count_all_results('order_list');
            $analytics[date("H:i", strtotime($d))] = $count;
        }

        return $analytics;

    }

    public function getOrdersSalesToday($owner_id = 0)
    {

        if ($owner_id) {
            $this->db->where("store.user_id", $owner_id);
            $this->db->join("store", "order_list.module_id=store.id_store");
        }

        $start = date("Y-m-d", time()) . " 00:00:00";
        $end = date("Y-m-d", time()) . " 23:59:59";

        $this->db->where('order_list.created_at >', $start);
        $this->db->where('order_list.created_at <', $end);


        $this->db->select('SUM(order_list.amount) as total, COUNT(*) as number', FALSE);
        $query = $this->db->get('order_list');
        $list = $query->result();

        return array(
            "total" => $list[0]->total,
            "count" => $list[0]->number,
        );

    }

    public function getOrdersSalesYear($owner_id = 0)
    {

        if ($owner_id) {
            $this->db->where("store.user_id", $owner_id);
            $this->db->join("store", "order_list.module_id=store.id_store");
        }

        $start = date("Y-01-01", time()) . " 00:00:00";
        $end = date("Y-12-31", time()) . " 23:59:59";

        $this->db->where('order_list.created_at >', $start);
        $this->db->where('order_list.created_at <', $end);

        $this->db->select('SUM(order_list.amount) as total, COUNT(*) as number', FALSE);
        $query = $this->db->get('order_list');
        $list = $query->result();

        return array(
            "total" => $list[0]->total,
            "count" => $list[0]->number,
        );

    }


    public function getOrdersSalesThisMonth($owner_id = 0)
    {

        if ($owner_id) {
            $this->db->where("store.user_id", $owner_id);
            $this->db->join("store", "order_list.module_id=store.id_store");
        }

        $start = date("Y-m", time()) . "-01 00:00:00";
        $end = date("Y-m-t", time()) . " 23:59:59";

        $this->db->where('order_list.created_at >', $start);
        $this->db->where('order_list.created_at <', $end);


        $this->db->select('SUM(order_list.amount) as total, COUNT(*) as number', FALSE);
        $query = $this->db->get('order_list');
        $list = $query->result();

        return array(
            "total" => $list[0]->total,
            "count" => $list[0]->number,
        );

    }


    public function getOrder($order_id)
    {

        $this->db->where("id", $order_id);
        $orders = $this->db->get("order_list", 1);
        $orders = $orders->result_array();

        if (isset($orders[0]))
            return $orders[0];

        return NULL;
    }


    public function getInvoiceID($order_id)
    {


        $this->db->where('module', 'order_payment');
        $this->db->where('module_id', $order_id);
        $invoice = $this->db->get('invoice', 1);
        $invoice = $invoice->result_array();

        if (isset($invoice[0]))
            return $invoice[0];

        return NULL;
    }

    public function getStoreFromCart($order_id)
    {

        $order = $this->getOrder($order_id);

        if ($order == NULL)
            return NULL;

        $this->db->where("id_store", $order["module_id"]);
        $obj = $this->db->get("store", 1);
        $obj = $obj->result_array();

        if (count($obj) > 0) {

            return $obj[0];
        }

        return NULL;
    }


    public function change_order_status($order_id, $order_status, $message)
    {

        $order = $this->getOrder($order_id);

        if ($order != NULL) {

            $old_status = $this->getStatus($order['status']);
            $new_status = $this->getStatus($order_status);

            if ($old_status != NULL && $new_status != NULL) {


                $timeline = json_decode($order['timeline'], JSON_OBJECT_AS_ARRAY);
                $updated_timeline = array();

                if (!empty($timeline))
                    foreach ($timeline as $tl) {
                        if ($tl['sid'] != $new_status->id) {
                            $updated_timeline[] = $tl;
                        } else {
                            break;
                        }
                    }


                $timeline = $updated_timeline;
                $last_key = intval(count($timeline));

                $timeline[($last_key)] = array(
                    "date" => date("Y-m-d H:i:s", time()),
                    "status" => $new_status->label,
                    "message" => $message,
                    "sid" => $new_status->id
                );

                $this->db->where("id", $order_id);
                $this->db->update("order_list", array(
                    'status' => $order_status,
                    'timeline' => json_encode($timeline, JSON_FORCE_OBJECT)
                ));


                //adjust stock if needed
                $this->adjust_stock($order, $new_status);


                $store = $this->getStoreFromCart($order_id);
                $store_name = $store["name"];
                $store_image = "";

                if (isset($store["images"])){

                    if(is_string($store["images"]))
                        $images  = json_decode($store["images"],JSON_OBJECT_AS_ARRAY);

                    if(isset($images[0]) && is_string($images[0])){
                        $images = _openDir($images[0]);
                        if(isset($images['name']))
                            $store_image = $images['name'];
                    }else if(isset($images[0]) && is_array($images[0])){
                        $images = $images[0];
                        if(isset($images['name']))
                            $store_image = $images['name'];
                    }
                }

                $status_name = $new_status->label;

                if ($message == "") {
                    $notif_body = Translate::sprintf("The status of your order #%s is changed  to %s", array(
                        $order_id,
                        $status_name
                    ));
                } else {
                    $notif_body = Translate::sprintf("The status of your order #%s is changed to %s , seller message   : %s", array(
                        $order_id,
                        $status_name,
                        $message
                    ));
                }


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


                //fcm ,  store_name, status name
                $guest_id = $this->mUserModel->getGuestIDByUserId($order['user_id']);
                $guest = $this->mUserModel->getGuestData($guest_id);

                if (empty($guest))
                    return;

                $fcm_id = $guest['fcm_id'];
                $fcm_platform = $guest['platform'];


                $this->load->model("notification/notification_model", "mNotificationModel");
                $this->mNotificationModel->sendCustomNotification($fcm_platform, $store_name, $notif_body, $fcm_id);


            }

        }

    }

    function adjust_stock($order, $status_obj)
    {

        $timeline = $order['timeline'];

        if (!is_array($timeline))
            $timeline = json_decode($timeline, JSON_OBJECT_AS_ARRAY);

        if ($status_obj->id == self::ORDER_STATUS_CONFIRMED
            && !$this->is_made(self::ORDER_STATUS_CONFIRMED, $timeline)) {

            $cart = $order['cart'];

            if (!is_array($cart))
                $cart = json_decode($cart, JSON_OBJECT_AS_ARRAY);

            foreach ($cart as $value) {
                if ($value['module'] == "product") {

                    $this->db->where('id_product', intval($value['module_id']));
                    $this->db->where('stock >=', 1);
                    $this->db->set('stock', 'stock-' . $value['qty'], FALSE);
                    $this->db->update('product');

                    $this->db->where('id_product', intval($value['module_id']));
                    $this->db->where('stock <', -1);
                    $this->db->set('stock', 0);
                    $this->db->update('product');

                }
            }

        } else if ($status_obj->id == self::ORDER_STATUS_CANCELLED
            && $this->is_made(self::ORDER_STATUS_CONFIRMED, $timeline)
            && !$this->is_made(self::ORDER_STATUS_CANCELLED, $timeline)) {

            $cart = $order['cart'];

            if (!is_array($cart))
                $cart = json_decode($cart, JSON_OBJECT_AS_ARRAY);

            foreach ($cart as $value) {
                if ($value['module'] == "product") {
                    $this->db->where('id_product', intval($value['module_id']));
                    $this->db->where('stock !=', -1);
                    $this->db->set('stock', 'stock+' . $value['qty'], FALSE);
                    $this->db->update('product');
                }
            }

        }

    }


    function is_made($sid = 0, $list = array())
    {

        if (!empty($list))
            foreach ($list as $v) {
                if ($v['sid'] == $sid)
                    return TRUE;
            }

        return FALSE;
    }


    public function re_order($list, $user_id)
    {


        if (!empty($list)) {

            foreach ($list as $key => $l) {

                $this->db->where("id", intval($l));
                $this->db->where("user_id", $user_id);
                $this->db->update("order_status", array(
                    "order" => intval($key)
                ));

            }

        }

    }


    public function get($id, $user_id = 0)
    {


        if ($user_id > 0)
            $this->db->where("user_id", $user_id);

        $this->db->where("id", $id);
        $cf = $this->db->get("order_status", 1);
        $cf = $cf->result_array();

        if (isset($cf[0]))
            return $cf[0];

        return NULL;

    }

    public function getList($user_id = 0)
    {

        if ($user_id > 0)
            $this->db->where("user_id", $user_id);

        $this->db->order_by("order", "asc");
        // $this->db->group_by("order");
        $cf = $this->db->get("order_status");

        return $cf->result_array();

    }

    public function add($param = array())
    {

        extract($param);
        $errors = array();

        $data['extras'] = array();

        if (isset($user_id) && $user_id > 0) {
            $data['user_id'] = intval($user_id);
        } else
            $errors[] = _lang("User ID is not valid");


        if (isset($label) && $label != "") {
            $data['label'] = trim($label);
        } else
            $errors[] = _lang("Please insert label");


        if (isset($color) && $color != "") {
            $data['extras']['color'] = $color;
        } else {
            $errors[] = _lang("Please insert color");
        }


        if (empty($errors)) {

            $data["created_at"] = date("Y-m-d H:i:s", time());
            $data["updated_at"] = date("Y-m-d H:i:s", time());
            $data["extras"] = json_encode($data["extras"]);

            $this->db->insert("order_status", $data);

            return array(Tags::SUCCESS => 1);
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
    }


    public function edit($param = array())
    {

        extract($param);
        $errors = array();

        $extras = array();

        if (isset($id) && $id > 0) {
            $data['id'] = intval($id);
        } else
            $errors[] = _lang("ID is not valid");


        if (isset($user_id) && $user_id > 0) {
            $data['user_id'] = intval($user_id);
        } else
            $errors[] = _lang("User ID is not valid");


        if (isset($label) && $label != "") {
            $data['label'] = trim($label);
        } else
            $errors[] = _lang("Please insert label");


        if (isset($color) && $color != "") {
            $extras['color'] = $color;
        } else {
            $errors[] = _lang("Please insert color");
        }

        if (empty($errors)) {

            $data["created_at"] = date("Y-m-d H:i:s", time());
            $data["updated_at"] = date("Y-m-d H:i:s", time());


            $this->db->where("id", intval($id));
            $this->db->where("user_id", intval($user_id));
            $status = $this->db->get("order_status", 1);
            $status = $status->result_array();

            if (isset($status[0])) {

                $status[0]["extras"] = json_decode($status[0]["extras"], JSON_OBJECT_AS_ARRAY);

                foreach ($extras as $key => $e) {
                    $status[0]["extras"][$key] = $e;
                }

                $data["extras"] = json_encode($status[0]["extras"]);

            }

            $this->db->where("id", intval($id));
            $this->db->where("user_id", intval($user_id));
            $this->db->update("order_status", $data);

            return array(Tags::SUCCESS => 1);
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
    }

    public function delete($id, $user_id)
    {

        $this->db->where("status", $id);
        $count = $this->db->count_all_results("order_list");

        if ($count > 0) {
            return array(Tags::SUCCESS => 0, Tags::ERRORS => array("err" => _lang("This status already linked with an product")));
        }


        $this->db->where("id", $id);
        $this->db->where("user_id", $user_id);
        $this->db->delete("order_status");

        return array(Tags::SUCCESS => 1);

    }


    public function createOrder($params = array())
    {

        extract($params);

        $errors = array();
        $data = array();

        if (isset($cart) and $cart != "") {

            if (!is_array($cart))
                $cart = json_decode($cart, JSON_OBJECT_AS_ARRAY);

            $data['cart'] = $cart;
        }

        if (isset($module_id) and $module_id > 0) {

            $data['module'] = "store";
            $data['module_id'] = intval($module_id);


        } else {
            $errors['module_id'] = Translate::sprint(Messages::STORE_ID_NOT_VALID);
        }

        if (isset($user_id) and $user_id > 0) {
            $data['user_id'] = intval($user_id);
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_SELECTED);
        }


        if (isset($amount) and $amount > 0) {
            $cart['amount'] = doubleval($amount);
        }

        if (isset($qte) and $qte > 0) {
            $cart['qte'] = intval($qte);
        }


        if (isset($amount) and doubleval($amount) > 0) {
            $cart['amount'] = doubleval($amount);
        }


        if (isset($req_cf_data) && $req_cf_data != "") {

            if (!is_array($req_cf_data))
                $data['req_cf_data'] = json_decode($req_cf_data, JSON_OBJECT_AS_ARRAY);

            $data['req_cf_data'] = $req_cf_data;

        } else {
            $errors['req_cf_data'] = Translate::sprint(Messages::CUSTOM_FIELDS_EMPTY);
        }

        if (isset($req_cf_id) && $req_cf_id > 0) {
            $data['req_cf_id'] = intval($req_cf_id);
        }else if(isset($data['module_id'])){

            //get cf id from category
            $this->db->select('category_id');
            $this->db->where('id_store',intval($data['module_id']));
            $stores = $this->db->get('store',1);
            $stores = $stores->result_array();

            if(isset($stores[0])){
                $category_id = $stores[0]['category_id'];
                $category = $this->mStoreModel->getCategory($category_id);
                $data['req_cf_id'] = $category['cf_id'];
            }
        }

        if (empty($errors)) {
            if ($status = $this->getFirstStatus($data['user_id'])) {
                $data['status'] = $status->id;
            } else {
                $data['status'] = 1;
            }
        }

        if (empty($errors) and !empty($data)) {

            if (is_array($data['cart']))
                $data['cart'] = json_encode($data['cart'],JSON_FORCE_OBJECT);

            if (is_array($data['cart']))
                $data['req_cf_data'] = json_encode($data['cart']);

            //store is disabled by default

            if (ModulesChecker::isEnabled("order_payment")) {
                $data['payment_status'] = "unpaid";
            }

            $date = date("Y-m-d H:i:s", time());
            $data['created_at'] = $date;
            $data['updated_at'] = $date;

            $this->db->insert("order_list", $data);
            $order_id = $this->db->insert_id();

            //parse json to array
            while (!is_array($data['cart'])){
                if(is_string($data['cart']))
                 $data['cart'] = json_decode($data['cart'],JSON_OBJECT_AS_ARRAY);
            }

            //save the cart in the db
            $this->create_order_cart($order_id, $data['cart']);

            //retrieve owner detail
            $ownerDetail = $this->mStoreModel->getBusinessOwnerFromStore($data['module_id']);
            if (isset($ownerDetail) && !empty($ownerDetail)) {
                //send a mail to the business owner
                $emailDataPrepare = array();
                $emailDataPrepare["order_id"] = $order_id;
                $emailDataPrepare["order_url"] = admin_url("nsorder/view?id=" . $order_id);
                $emailDataPrepare["email"] = $ownerDetail["email"];
                $emailDataPrepare["name"] = $ownerDetail["name"];
                $this->sendOderDetailToBO($emailDataPrepare);
            }

            ActionsManager::add_action("nsorder","order_created",$order_id);

            if (ModulesChecker::isEnabled("order_payment") && isset($user_token)
                && isset($payment_method)) {

                $link = $this->convert_order_to_invoice($order_id, $user_id, $user_token, $payment_method);
                //send the invoice throw email
                return array(Tags::SUCCESS => 1, Tags::RESULT => $order_id, "plink" => $link);

            }


            return array(Tags::SUCCESS => 1, Tags::RESULT => $order_id);
        }


        return array(Tags::SUCCESS => -1, Tags::ERRORS => $errors);
    }

    private function create_order_cart($order_id,$items){

        foreach ($items as $item){
            $this->db->insert('order_cart',array(
                'order_id' => $order_id,
                'item_id' => $item['module_id'],
                'amount' => $item['amount'],
                'qty' => $item['qty'],
                /*'variants' => $item['variants'],*/
                'updated_at' => date("Y-m-d H:i:s",time()),
                'created_at' => date("Y-m-d H:i:s",time()),
            ));
        }

    }


    private function sendOderDetailToBO($data)
    {

        $html_file = "order_bo_alert.html";

        $appLogo = _openDir(APP_LOGO);
        $imageUrl = "";
        if (!empty($appLogo)) {
            $imageUrl = $appLogo['200_200']['url'];
        }

        //send mail verification
        $messageText = Text::textParserHTML(array(
            "title" => _lang("New order"),
            "name" => $data["name"],
            "email" => $data['email'],
            "id" => $data['order_id'],
            "url" => $data['order_url'],
            "team_email" => DEFAULT_EMAIL,
            "appName" => strtolower(APP_NAME),
            "imageUrl" => $imageUrl,
        ), $this->load->view('nsorder/mailing/' . $html_file, NULL, TRUE));

        $messageText = ($messageText);

        $mail = new Mailer();

        $mail->setDistination($data['email']);
        $mail->setFrom(DEFAULT_EMAIL);
        $mail->setFrom_name(APP_NAME);
        $mail->setMessage($messageText);
        $mail->setReplay_to(DEFAULT_EMAIL);
        $mail->setReplay_to_name(APP_NAME);
        $mail->setType("html");
        $mail->setSubjet(_lang("New order"));

        if ($mail->send()) {
            return TRUE;
        }
    }


    function convert_order_to_invoice($order_id, $user_id, $user_token, $payment_method)
    {

        $result = $this->mOrderPayment->convert_order_to_invoice($user_id, $order_id);

        if ($result[Tags::SUCCESS] == 1 && $result[Tags::RESULT] > 0) {

            if (TokenSetting::isValid($user_id, "logged", $user_token)) {
                $token = TokenSetting::getValid($user_id, "logged", $user_token);
                if ($token != NULL) {
                    $this->mUserBrowser->refreshData($token->uid);
                }
            }

            //process_payment
            return site_url("payment/process_payment?invoiceid=" . $result[Tags::RESULT] . "&mp=" . $payment_method);
        }

        return;
    }

    public function getStatus($id)
    {

        $this->db->where("id", $id);
        $status = $this->db->get('order_status', 1);
        $status = $status->result();

        if (isset($status[0]))
            return $status[0];
        else
            return NULL;
    }

    public function getFirstStatus($user_id)
    {

        $this->db->order_by("order", "ASC");
        $status = $this->db->get('order_status', 1);
        $status = $status->result();

        if (isset($status[0]))
            return $status[0];
        else
            return NULL;
    }

    public function getOrders($params = array(), $whereArray = array(), $callback = NULL)
    {

        extract($params);
        $errors = array();

        if (!isset($page))
            $page = 1;

        if (!isset($limit))
            $limit = 30;

        if (!isset($order_by))
            $order_by = "recent";

        if (!isset($radius))
            $radius = RADUIS_TRAGET * 1000;


        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);

        $this->db->where("order_list.hidden", 0);

        if (isset($id) and $id > 0) {
            $this->db->where("order_list.id", $id);
        }


        if (isset($user_id) and $user_id > 0) {
            $this->db->where("order_list.user_id", $user_id);
        }


        if (isset($owner_id) and $owner_id > 0) {
            $this->db->where("store.user_id", $owner_id);
        }

        if (isset($date_start) && $date_start != "" && isset($date_end) && $date_end != "") {
            $this->db->where('order_list.updated_at BETWEEN "' . date('Y-m-d', strtotime($date_start)) . '" and "' . date('Y-m-d', strtotime($date_end)) . '"');
        }


        if (isset($order_status) and $order_status > 0) {
            $this->db->where("order_list.status", $order_status);
        }

        if (isset($payment_status) and $payment_status != "0") {
            $this->db->where("order_list.payment_status", $payment_status);
        }

        if (isset($order_id) and $order_id > 0) {
            $this->db->where("order_list.id", intval($order_id));
        }

        if (isset($user_id) and $user_id > 0) {
            $this->db->where("order_list.user_id", intval($user_id));
        }

        if (isset($search) and $search != "") {
            $this->db->group_start();
            $this->db->like('order_list.id', intval($search));
            $this->db->like('order_list.status', intval($search));
            $this->db->group_end();
        }


        $this->db->where('order_list.module', 'store');
        $this->db->where('store.status !=', -1);
        $this->db->join('store', 'store.id_store=order_list.module_id');


        $calculated_distance_q = "";

        if (isset($longitude) && isset($latitude) && isset($order_by) && $order_by == "nearby") {


            $longitude = doubleval($longitude);
            $latitude = doubleval($latitude);

            $calculated_distance_q = " , IF( store.latitude = 0,99999,  (1000 * ( 6371 * acos (
                              cos ( radians(" . $latitude . ") )
                              * cos( radians( store.latitude ) )
                              * cos( radians( store.longitude ) - radians(" . $longitude . ") )
                              + sin ( radians(" . $latitude . ") )
                              * sin( radians( store.latitude ) )
                            )
                          ) ) ) as 'distance'  ";


            if (isset($radius) and $radius > 0 && $calculated_distance_q != "")
                $this->db->having('distance <= ' . intval($radius), NULL, FALSE);

        }

        $count = $this->db->count_all_results("order_list");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        if ($count == 0)
            return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => array());


        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);


        $this->db->where("order_list.hidden", 0);


        if (isset($id) and $id > 0) {
            $this->db->where("order_list.id", $id);
        }

        if (isset($user_id) and $user_id > 0) {
            $this->db->where("order_list.user_id", $user_id);
        }


        if (isset($owner_id) and $owner_id > 0) {
            $this->db->where("store.user_id", $owner_id);
        }

        if (isset($date_start) && $date_start != "" && isset($date_end) && $date_end != "") {
            /* $this->db->where("order_list.updated_at <=", MyDateUtils::convert($date_start,"UTC",TimeZoneManager::getTimeZone(),"Y-m-d H:i:s"));
             $this->db->where("order_list.updated_at >=", MyDateUtils::convert($date_end,"UTC",TimeZoneManager::getTimeZone(),"Y-m-d H:i:s"));*/
            $this->db->where('order_list.updated_at BETWEEN "' . date('Y-m-d', strtotime($date_start)) . '" and "' . date('Y-m-d', strtotime($date_end)) . '"');
        }


        if (isset($order_status) and $order_status > 0) {
            $this->db->where("order_list.status", $order_status);
        }

        if (isset($payment_status) and $payment_status != "0") {
            $this->db->where("order_list.payment_status", $payment_status);
        }


        if (isset($order_id) and $order_id > 0) {
            $this->db->where("order_list.id", intval($order_id));
        }

        if (isset($user_id) and $user_id > 0) {
            $this->db->where("order_list.user_id", intval($user_id));
        }

        if (isset($search) and $search != "") {
            $this->db->group_start();
            $this->db->like('order_list.id', intval($search));
            $this->db->like('order_list.status', intval($search));
            $this->db->group_end();
        }


        if ($order_by == "nearby" && $calculated_distance_q != "") {
            $this->db->order_by("distance", "ASC");
        } else {
            $this->db->order_by("order_list.updated_at", "desc");
        }

        if (isset($radius) and $radius > 0 && $calculated_distance_q != "")
            $this->db->having('distance <= ' . intval($radius), NULL, FALSE);


        $this->db->where('order_list.module', 'store');
        $this->db->where('store.status !=', -1);
        $this->db->join('store', 'store.id_store=order_list.module_id');

        $this->db->select("order_list.*,store.id_store,store.name");
        $this->db->from("order_list");

        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());

        $orders = $this->db->get();

        $orders = $orders->result_array();

        $new_orders_results = array();
        foreach ($orders as $key => $order) {

            $new_orders_results[$key] = $order;
            $carts = json_decode($order["cart"], JSON_OBJECT_AS_ARRAY);

            foreach ($carts as $cart) {

                $items = $this->moduleDetailFromId(array(
                    "module_id" => $cart['module_id'],
                    "module" => $cart['module']
                ));
                $items["id"] = $cart['module_id'];
                $items["module"] = $cart['module'];
                $items["qty"] = $cart["qty"];
                $items["amount"] = $cart["amount"];

                $new_orders_results[$key]["items"][] = $items;
            }


            if ($order['status'] > 0) {

                $status = $this->getStatus($order['status']);

                if ($status != NULL) {
                    $extras = json_decode($status->extras, JSON_OBJECT_AS_ARRAY);
                    $new_orders_results[$key]['status'] = $status->label;
                    $new_orders_results[$key]['status_id'] = $status->id;

                    if (isset($extras['color']))
                        $new_orders_results[$key]['status'] = $new_orders_results[$key]['status'] . ";" . $extras['color'];
                } else {

                    $new_orders_results[$key]['status'] = "undefined;#eeeeee";
                    $new_orders_results[$key]['status_id'] = 999999;

                }


            }


        }


        if (ModulesChecker::isEnabled("order_payment")) {

            foreach ($new_orders_results as $key => $order) {
                $invoice = $this->mOrderModel->getInvoiceID($order['id']);

                if (($order['payment_status'] == "unpaid" or $order['payment_status'] == "") && $invoice != NULL) {
                    $new_orders_results[$key]['invoice'] = $invoice['id'];
                    $ps = Order_payment::PAYMENT_STATUS;
                    if (isset($ps[$order['payment_status']])) {
                        $new_orders_results[$key]['payment_status_data'] = _lang($ps[$order['payment_status']]['label']) . ";" . $ps[$order['payment_status']]['color'];
                    } else {
                        $new_orders_results[$key]['payment_status_data'] = _lang("unpaid") . ";" . $ps["unpaid"]['color'];
                    }
                } else {
                    $ps = Order_payment::PAYMENT_STATUS;
                    if (isset($ps[$order['payment_status']])) {
                        $new_orders_results[$key]['payment_status_data'] = _lang($ps[$order['payment_status']]['label']) . ";" . $ps[$order['payment_status']]['color'];
                    }
                }

                if( isset($invoice['extras']) && $invoice['extras']!= null)
                $new_orders_results[$key]['extras'] = $invoice['extras'];
            }

        }


        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $new_orders_results);

    }


    public function deleteOrder($params = array())
    {

        extract($params);
        $errors = array();
        $data = array();

        $user_id = $this->mUserBrowser->getData("id_user");

        if (isset($id) and $id > 0 && $user_id > 0) {
            $this->db->where("id", $id);
            $order = $this->db->get("order_list");
            $orderToDelete = $order->result();

            if (isset($orderToDelete[0])) {
                $this->db->where("id", $id);
                $this->db->delete("order_list");
            }
            return array(Tags::SUCCESS => 1);

        }

        return array(Tags::SUCCESS => 0);
    }


    public function countPendingOrders($isOwner = FALSE)
    {
        $owner_id = $this->mUserBrowser->getData("id_user");

        //get default status
        $defaultStatus = 0;
        if ($status = $this->getFirstStatus($owner_id)) {
            $defaultStatus = $status->id;
        }

        if ($owner_id > 0) {
            $this->db->where("order_list.hidden", 0);
            $this->db->where("order_list.module", "store");
            if ($isOwner) $this->db->where("store.user_id", $owner_id);
            $this->db->join("store", "order_list.module_id=store.id_store");
            $this->db->where("order_list.status", $defaultStatus);
            $count = $this->db->count_all_results("order_list");

            //echo $count;
            return array(
                Tags::SUCCESS => 1,
                Tags::COUNT => $count
            );
        }

        return array(Tags::SUCCESS => 0);
    }


    private function moduleDetailFromId($params = array())
    {


        $errors = array();
        $data = array();

        extract($params);


        if ((isset($module_id) && intval($module_id) > 0) && (isset($module) && $module != "")) {

            if ($module == "offer") {

                $module = "product";

                $this->db->where("id_" . $module, $module_id);
                $c = $this->db->count_all_results($module);

                if ($c > 0) {
                    $dataResult = array();
                    $this->db->where("id_" . $module, $module_id);
                    $dbResult = $this->db->get($module, 1);
                    $dbResult = $dbResult->result_array();

                    if (isset($dbResult)) {
                        $dataResult["name"] = $dbResult[0]["name"];
                        $dataResult['image'] = ImageManagerUtils::getFirstImage(
                            $dbResult[0]["images"],
                            ImageManagerUtils::IMAGE_SIZE_200
                        );
                        $dataResult["currency"] = $this->mCurrencyModel->getCurrency($dbResult[0]["currency"]);
                    }

                    return $dataResult;
                }

            } else {

                $this->db->where("id_" . $module, $module_id);
                $c = $this->db->count_all_results($module);

                if ($c > 0) {
                    $dataResult = array();
                    $this->db->where("id_" . $module, $module_id);
                    $dbResult = $this->db->get($module, 1);
                    $dbResult = $dbResult->result_array();

                    if (isset($dbResult)) {
                        $dataResult["name"] = $dbResult[0]["name"];
                        $dataResult['image'] = ImageManagerUtils::getFirstImage(
                            $dbResult[0]["images"],
                            ImageManagerUtils::IMAGE_SIZE_200
                        );
                        $dataResult["currency"] = $this->mCurrencyModel->getCurrency($dbResult[0]["currency"]);
                    }

                    return $dataResult;
                }
            }


        }

        return NULL;

    }


    public function create_attach_default_cf()
    {

        $count = $this->db->count_all_results("category");

        if ($count == 0) {

            $this->db->insert('category', array(
                'name' => 'Super Market',
            ));

            $this->db->insert('category', array(
                'name' => 'Restaurant',
            ));

        }

        $count = $this->db->count_all_results("cf_list");

        if ($count > 0) {

            $this->db->update('category', array(
                'cf_id' => 1
            ));
        }

    }


    public function init_order_status()
    {

        if (!SessionManager::isLogged())
            $user_id = 1;
        else
            $user_id = SessionManager::getData("id_user");

        //pending --
        //confirmed
        //preparing - on printing
        //shipped - on delivery - on ready
        //delivered
        //cancelled


        $this->db->where("user_id", $user_id);
        $status = $this->db->count_all_results("order_status");

        if ($status == 0) {

            $status_list = array();

            $status_list[1] = array(
                'label' => 'pending',
                'extras' => '{"color":"#ff8a1e"}',
            );

            $status_list[2] = array(
                'label' => 'confirmed',
                'extras' => '{"color":"#2197e0"}',
            );

            $status_list[3] = array(
                'label' => 'on preparing',
                'extras' => '{"color":"#f99836"}',
            );

            $status_list[4] = array(
                'label' => 'on delivery',
                'extras' => '{"color":"#359cfc"}',
            );

            $status_list[5] = array(
                'label' => 'delivered',
                'extras' => '{"color":"#4baa38"}',
            );

            $status_list[6] = array(
                'label' => 'cancelled',
                'extras' => '{"color":"#ff3535"}',
            );

            $status_list[7] = array(
                'label' => 'reported',
                'extras' => '{"color":"#ea5823"}',
            );

            foreach ($status_list as $id => $s) {

                $this->db->where("id", intval($id));
                $count = $this->db->count_all_results("order_status");

                if($count == 1)
                    continue;

                $s['id'] = intval($id);
                $s['user_id'] = intval($user_id);
                $s['order'] = intval($id);
                $s['created_at'] = date("Y-m-d H:i:s", time());
                $s['updated_at'] = date("Y-m-d H:i:s", time());
                $this->db->insert('order_status', $s);
            }

        }

    }

    public function createTables()
    {

        $this->load->dbforge();

        //creat e order table
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),

            'module_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'module' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),

            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'app_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'status' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'timeline' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'cart' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'amount' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),

            'req_cf_data' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'req_cf_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'updated_at' => array(
                'type' => 'DATETIME'
            ),

            'created_at' => array(
                'type' => 'DATETIME'
            ),
        );

        $this->dbforge->add_field($fields);
        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('order_list', TRUE, $attributes);


        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),

            'app_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'order' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'label' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'extras' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'updated_at' => array(
                'type' => 'DATETIME'
            ),

            'created_at' => array(
                'type' => 'DATETIME'
            ),
        );


        $this->dbforge->add_field($fields);
        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('order_status', TRUE, $attributes);


    }


    public function updateFields()
    {

        $this->load->dbforge();

        if (!$this->db->field_exists('user_id', 'order_list')) {
            $this->load->dbforge();
            $fields = array(
                'user_id' => array('type' => 'INT', 'after' => 'module_id', 'default' => NULL),
            );
            $this->dbforge->add_column('order_list', $fields);
        }

        if (!$this->db->field_exists('payment_status', 'order_list')) {
            $this->load->dbforge();
            $fields = array(
                'payment_status' => array('type' => 'VARCHAR(100)', 'default' => NULL),
            );
            $this->dbforge->add_column('order_list', $fields);
        }

        if (!$this->db->field_exists('button_template', 'category')) {
            $this->load->dbforge();
            $fields = array(
                'button_template' => array('type' => 'VARCHAR(30)', 'default' => NULL),
            );
            $this->dbforge->add_column('category', $fields);
        }


        if (!$this->db->field_exists('commission', 'order_list')) {
            $this->load->dbforge();
            $fields = array(
                'commission' => array('type' => 'DOUBLE', 'default' => NULL),
            );
            $this->dbforge->add_column('order_list', $fields);
        }


        if (!$this->db->field_exists('hidden', 'order_list')) {
            $this->load->dbforge();
            $fields = array(
                'hidden' => array('type' => 'INT', 'default' => 0),
            );
            $this->dbforge->add_column('order_list', $fields);
        }

    }


    public function createPayoutsTable()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),

            'method' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),

            'amount' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),

            'currency' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),

            'note' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'status' => array(
                'type' => 'VARCHAR(50)',
                'default' => NULL
            ),

            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'transaction_id' => array(
                'type' => 'VARCHAR(60)',
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
        $this->dbforge->create_table('payouts', TRUE, $attributes);

        //==========  Payment_transactions ==========/


    }


    public function createCartOrderTable()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),

            'order_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'item_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'qty' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),

            'amount' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),

            'variants' => array(
                'type' => 'TEXT',
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
        $this->dbforge->create_table('order_cart', TRUE, $attributes);

        //==========  Payment_transactions ==========/


    }

    public function emigration(){

        $orders = $this->db->get('order_list');
        $orders = $orders->result();

        foreach ($orders as $order){
            $cart = json_decode($order->cart,JSON_OBJECT_AS_ARRAY);
            foreach ($cart as $key => $item){
                $cart[$key]['module'] = 'product';
            }

            $cart = json_encode($cart,JSON_FORCE_OBJECT);

            $this->db->where('id',$order->id);
            $this->db->update('order_list',array(
                'cart' => $cart,
                'hidden' => 1,
            ));
        }

    }

}

