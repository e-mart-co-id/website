<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load models
        $this->load->model("nsorder/Nsorder_model", "mOrderModel");
    }

    public function commission()
    {

        $this->load->view("backend/header");
        $this->load->view("nsorder/backend/commission");
        $this->load->view("backend/footer");

    }


    public function payouts()
    {

        if (!GroupAccess::isGranted('nsorder'))
            redirect("error?page=permission");

        $status = $this->input->get('status');
        if ($status == "") $status = 2;
        else  $status = intval($status);


        $params = array(
            "status" => $status,
            "page" => intval($this->input->get('page')),
            "payout_id" => intval($this->input->get('id')),
            "transaction_id" => intval($this->input->get('transaction_id')),
            "limit" => 15,
            "order_by_date" => 1
            //"user_id" => intval($this->mUserBrowser->getData("id_user"))
        );

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN))
            $params['user_id'] = SessionManager::getData('id_user');

        $data['result'] = $this->mOrderModel->getPayout($params);

        $this->load->view("backend/header", $data);
        $this->load->view("nsorder/backend/payouts/payouts_list");
        $this->load->view("backend/footer");


    }

    public function addPayout()
    {

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN))
            redirect("error?page=permission");

        $this->load->view("backend/header");
        $this->load->view("nsorder/backend/payouts/add_payout");
        $this->load->view("backend/footer");


    }


    public function editPayout()
    {

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN))
            redirect("error?page=permission");


        $id = intval($this->input->get("id"));

        $p = $this->mOrder->getPayoutObject($id);

        if ($p == NULL)
            redirect("error404");


        $data['payout'] = $p;

        $this->load->view("backend/header", $data);
        $this->load->view("nsorder/backend/payouts/edit_payout");
        $this->load->view("backend/footer");


    }


    public function view()
    {

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDERS)) {
            redirect(admin_url("error404"));
        }

        $data = array();

        $params = array(
            "order_id" => intval($this->input->get('id'))
        );


        $orders = $this->mOrderModel->getOrders($params);

        if (isset($orders[Tags::RESULT]) && count($orders[Tags::RESULT]) == 1) {
            $data['order'] = $orders[Tags::RESULT][0];
        } else
            redirect(admin_url("error404"));


        $tempStatusList = $this->mOrder->getList();

        switch ($data["order"]["status_id"]) {
            case 1: //pending
                $data['status'] = array($tempStatusList[1], $tempStatusList[5], $tempStatusList[6]);
                break;
            case 2: //confirmed
                $data['status'] = array($tempStatusList[2], $tempStatusList[3], $tempStatusList[5], $tempStatusList[6]);
                break;
            case 3: // on preparing
                $data['status'] = array($tempStatusList[3], $tempStatusList[5], $tempStatusList[6]);
                break;
            case 4: // on delivery
                $data['status'] = array($tempStatusList[4], $tempStatusList[5], $tempStatusList[6]);
                break;
            case 5: // delivred
                $data['status'] = array($tempStatusList[5], $tempStatusList[6]);
                break;
            case 6: // cancelled
                $data['status'] = array($tempStatusList[6]);
                break;
            case 7: // reported
                $data['status'] = array($tempStatusList[3], $tempStatusList[4], $tempStatusList[5], $tempStatusList[6]);
                break;
        }


        $this->load->view("backend/header", $data);
        $this->load->view("nsorder/backend/order_detail", $data);
        $this->load->view("backend/footer");

    }


    public
    function order_status()
    {

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_STATUS_LIST_ADMIN)) {
            redirect(admin_url("error404"));
        }

        $data = array();

        $data['list'] = $this->mOrder->getList();

        $this->load->view("backend/header", $data);
        $this->load->view("nsorder/backend/order_status/list");
        $this->load->view("backend/footer");

    }

    public
    function order_status_edit()
    {

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_STATUS_LIST_ADMIN)) {
            redirect(admin_url("error404"));
        }

        $data = array();

        $data['data'] = $this->mOrder->get(
            intval($this->input->get("id")),
            SessionManager::getData("id_user")
        );

        $this->load->view("backend/header", $data);
        $this->load->view("nsorder/backend/order_status/edit");
        $this->load->view("backend/footer");


    }

    public function my_orders()
    {
        /*
        *  CHECK USER PEMISSIONS
        */
        if (!GroupAccess::isGranted('nsorder'))
            redirect("error?page=permission");


        $params = array(
            "order_id" => $this->input->get("id"),
            "search" => $this->input->get("search"),
            "date_start" => $this->input->get("date_start"),
            "date_end" => $this->input->get("date_end"),
            "page" => intval($this->input->get("page")),
            "product_id" => intval($this->input->get("product_id")),
            "limit" => NO_OF_ITEMS_PER_PAGE,
        );

        $params['owner_id'] = SessionManager::getData("id_user");
        $data['data'] = $this->mOrderModel->getOrders($params);
        $data['pagination_url'] = admin_url("nsorder/my_orders");


        $this->load->view("backend/header", $data);
        $this->load->view("nsorder/backend/orders");
        $this->load->view("backend/footer");

    }

    public function all_orders()
    {
        /*
        *  CHECK USER PEMISSIONS
        */
        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN))
            redirect("error?page=permission");

        $limit = $this->input->get("limit");
        $limit = isset($limit) ? $limit : NO_OF_ITEMS_PER_PAGE;

        $params = array(
            "order_id" => $this->input->get("id"),
            "search" => $this->input->get("search"),
            "page" => intval($this->input->get("page")),
            "product_id" => intval($this->input->get("product_id")),
            "user_id" => intval($this->input->get("user_id")),
            "date_start" => $this->input->get("date_start"),
            "date_end" => $this->input->get("date_end"),
            "owner_id" => intval($this->input->get("owner_id")),
            "order_status" => intval($this->input->get("order_status")),
            "payment_status" => $this->input->get("payment_status"),
            "limit" => $limit,
        );

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN)) {
            $params['user_id'] = SessionManager::getData("id_user");
        }

        $data['data'] = $this->mOrderModel->getOrders($params);
        $data['pagination_url'] = admin_url("nsorder/all_orders");


        $this->load->view("backend/header", $data);
        $this->load->view("nsorder/backend/orders");
        $this->load->view("backend/footer");

    }


    public function print_order()
    {

        $order = intval($this->input->get('id'));
        $user_id = SessionManager::getData("id_user");


        $params = array(
            "order_id" => intval($this->input->get('id'))
        );

        if (!GroupAccess::isGranted('nsorder', MANAGE_ORDER_CONFIG_ADMIN))
            $params['owner_id'] = SessionManager::getData("id_user");

        $orders = $this->mOrderModel->getOrders(
            $params
        );

        if (isset($orders[Tags::RESULT]) && count($orders[Tags::RESULT]) == 1) {
            $order = $orders[Tags::RESULT][0];
        } else
            redirect(admin_url("error404"));


        $logo = ImageManagerUtils::getValidImages(APP_LOGO);
        $imageUrl = base_url("views/skin/backend/images/logo.png");
        if (!empty($logo)) {
            $imageUrl = $logo[0]["200_200"]["url"];
        }


        $data['logo'] = $imageUrl;
        $data['doc_name'] = Translate::sprint('Order');
        $data['no'] = $order['id'];
        $data['created_at'] = date("D M Y h:i:s A", time()) . ' UTC';
        $data['client_name'] = ucfirst($this->mUserModel->getFieldById("name", $order['user_id']));

        $data['client_data'] = "";


        $cf_id = intval($order['req_cf_id']);
        $order['req_cf_data'] = json_decode($order['req_cf_data'], JSON_OBJECT_AS_ARRAY);
        if (isset($order['req_cf_data'])) {

            $cf_object = CFManagerHelper::getByID($cf_id);
            $fields = json_decode($cf_object['fields'], JSON_OBJECT_AS_ARRAY);

            foreach ($fields as $key => $field) {

                $cf_data = $order['req_cf_data'][$field['label']];

                if ($cf_data == "") {
                    continue;
                }

                if (CFManagerHelper::getTypeByID($cf_id, $key) == "input.location") {
                    if ($key == "") {
                        $data['client_data'] = $data['client_data'] . "<span><strong>" . $field['label'] . "</strong>: -- </span><br>";
                    } else {

                        if (preg_match("#;#", $cf_data)) {
                            $l = explode(";", $cf_data);
                            $data['client_data'] = $data['client_data'] . "<span><strong>" . $field['label'] . "</strong>: " . $l[0] . "<br>";
                        } else {
                            $data['client_data'] = $data['client_data'] . "<span><strong>" . $field['label'] . "</strong>: $cf_data </span><br>";
                        }
                    }
                } else
                    $data['client_data'] = $data['client_data'] . "<span><strong>" . $field['label'] . "</strong>: $cf_data</span><br>";

            }


        }

        $data['items'] = json_decode($order['cart'], JSON_OBJECT_AS_ARRAY);

        $sub_total = 0;
        $currency = "USD";

        foreach ($data['items'] as $key => $item) {

            $sub_total = $sub_total + $item['amount'] * intval($item['qty']);

            $callback = BookmarkLinkedModule::find($item['module'], 'getData');
            if ($callback != NULL) {

                $params = array(
                    'id' => $item['module_id']
                );

                $result = call_user_func($callback, $params);
                $data['items'][$key]['label'] = $result['label'] . " x " . intval($item['qty']);


                if (isset($item['variants'])) {
                    $data['items'][$key]['label'] = $data['items'][$key]['label'] . "<span style='    font-size: 14px;
                            color: grey;'>" . OrderHelper::variantsBuilderString($item['variants']) . "</span>";

                }


                if (!empty($result['currency']) && is_array($result['currency'])) {
                    $currency = $result['currency']['code'];
                } else if (is_string($result['currency'])) {
                    $currency = $result['currency'];
                }

                $amount = $data['items'][$key]['amount'] * intval($item['qty']);

                $data['items'][$key]['amount'] = Currency::parseCurrencyFormat(
                    $amount,
                    $currency
                );
            }

        }


        $data['sub_amount'] = Currency::parseCurrencyFormat($sub_total, $currency);

        if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0) {

            $percent = 0;
            $tax = $this->mTaxModel->getTax(DEFAULT_TAX);

            if ($tax != NULL)
                $percent = $tax['value'];

            $taxed_amount = (($percent / 100) * $sub_total);

            $data['taxes_value'][] = array(
                'tax_value' => Currency::parseCurrencyFormat($taxed_amount, $currency),
                'tax_name' => $tax['name'],
            );

            $sub_total = $taxed_amount + $sub_total;

        }


        $invoice = $this->mOrderPayment->getInvoice($order['id']);
        $extras = json_decode($invoice->extras, JSON_OBJECT_AS_ARRAY);

        if (!empty($extras)){
            foreach ($extras as $key => $value) {
                $extras[$key] = Currency::parseCurrencyFormat($value, $currency);
                $sub_total = $value + $sub_total;
            }

            $data['extras'] = $extras;
        }

        $data['extras'] = $extras;

        $data['amount'] = Currency::parseCurrencyFormat($sub_total, $currency);
        $data['status'] = 0;

        if (intval($this->input->get('print')) == 0) {
            $data['frame'] = TRUE;
            $data['link'] = admin_url('nsorder/print_order?id=' . $order['id'] . '&user_id=' . $user_id . '&print=1');
        }

        $this->load->view('nsorder/print', $data);

    }


}

/* End of file EventDB.php */