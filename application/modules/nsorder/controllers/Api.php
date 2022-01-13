<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */
class Api extends API_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("nsorder/Nsorder_model", "mOrderModel");
    }


    public function createOrder()
    {
        echo json_encode($this->mOrderModel->createOrder(array(
            "module" => $this->input->post("module"),
            "module_id" => intval($this->input->post("module_id")),
            "req_cf_data" => $this->input->post("req_cf_data"),

            "req_cf_id" => $this->input->post("req_cf_id"),
            "user_id" => $this->input->post("user_id"),
            "qte" => intval($this->input->post("qte")),
            "amount" => intval($this->input->post("amount")),
            "cart" => $this->input->post("cart"),

            "user_token" => $this->input->post("user_token"),
            "payment_method" => $this->input->post("payment_method"),

        )));

    }


    public function getOrders()
    {

        $result = $this->mOrderModel->getOrders(array(
            "id" => $this->input->post("order_id"),
            "module" => $this->input->post("module"),
            "module_id" => intval($this->input->post("module_id")),
            "user_id" => intval($this->input->post("user_id")),
            "limit" => intval($this->input->post("limit")),
            "page" => intval($this->input->post("page")),
            "except" => $this->input->post("except"),
            "order_by" => $this->input->post("order_by"),
        ));

        echo json_encode($result, JSON_FORCE_OBJECT);
    }


}

/* End of file CategoryDB.php */