<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();

        ModulesChecker::requireEnabled("product");

        $this->load->helper('url');
    }

	public function index()
	{

	}


    public function view(){

        if (!GroupAccess::isGranted('product',MANAGE_PRODUCTS))
            redirect("error?page=permission");

        $data = array();


        $params = array(
            "product_id"  => intval($this->input->get("id")),
            "limit"     => 1
        );

        if (GroupAccess::isGranted('product',MANAGE_PRODUCTS)  ){
            $params['is_super'] = TRUE;
        }

        $whereArray['is_offer'] = 0;


        $data['product'] = $this->mProductModel->getProducts($params,$whereArray);

        if (isset($data['product'][Tags::RESULT]) and count($data['product'][Tags::RESULT]) == 1) {
            $this->load->view("backend/header", $data);
            $this->load->view("product/backend/html/edit");
            $this->load->view("backend/footer");
        }


    }

    public function edit(){

        if (!GroupAccess::isGranted('product',EDIT_PRODUCT))
            redirect("error?page=permission");

        $data = array();

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id" => $this->mUserBrowser->getData("id_user")
        ));

        $params = array(
            "product_id"  => intval($this->input->get("id")),
            "limit"     => 1,
            "user_id" => SessionManager::getData('id_user')
        );

        $whereArray['is_offer'] = 0;

        if (GroupAccess::isGranted('product',MANAGE_PRODUCTS)  ){
            $params['is_super'] = TRUE;
            unset($params['user_id']);
        }

        $data['product'] = $this->mProductModel->getProducts($params,$whereArray);

        $data['cf_list'] = $this->mCFManager->getList(
            SessionManager::getData("id_user")
        );

        if (isset($data['product'][Tags::RESULT]) and count($data['product'][Tags::RESULT]) == 1) {
            $this->load->view("backend/header", $data);
            $this->load->view("product/backend/html/edit");
            $this->load->view("backend/footer");
        }


    }

    public function all_products(){


        if (!GroupAccess::isGranted('product',MANAGE_PRODUCTS)  )
            redirect("error?page=permission");


        $data = array();

        $params =array(
            "product_id" => $this->input->get("product_id"),
            "store_id" => $this->input->get("store_id"),
            "date_end" => $this->input->get("date_end"),
            "page" => $this->input->get("page"),
            "search" => $this->input->get("search"),
            "limit"     => NO_OF_ITEMS_PER_PAGE,
            "is_super"     => TRUE,
            "status"     => $this->input->get("status"), // filter product by status
            "filterBy"     => $this->input->get("filterBy"),
        );


        $whereArray['is_offer'] = 0;

        $data['products'] = $this->mProductModel->getProducts($params,$whereArray);

        $data['list_title'] = "All products";

        $this->load->view("backend/header",$data);
        $this->load->view("product/backend/html/products");
        $this->load->view("backend/footer");


    }

    public function my_products(){


        if (!GroupAccess::isGranted('user')  )
            redirect("error?page=permission");

        $data = array();

        $params =array(
            "product_id" => $this->input->get("product_id"),
            "store_id" => $this->input->get("store_id"),
            "date_end" => $this->input->get("date_end"),
            "page"      => $this->input->get("page"),
            "search" => $this->input->get("search"),
            "limit"     => NO_OF_ITEMS_PER_PAGE,
            "user_id"     => SessionManager::getData('id_user'),
            "status"     => $this->input->get("status"), // filter product by status
            "filterBy"     => $this->input->get("filterBy"),
        );

        $whereArray['is_offer'] = 0;

        $data['products'] = $this->mProductModel->getProducts($params,$whereArray);


        $data['list_title'] = "My products";

        $this->load->view("backend/header",$data);
        $this->load->view("product/backend/html/products");
        $this->load->view("backend/footer");


    }

    public function add(){

        if (!GroupAccess::isGranted('product',ADD_PRODUCT))
            redirect("error?page=permission");

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));


        $data['cf_list'] = $this->mCFManager->getList(
            SessionManager::getData("id_user")
        );

        $this->load->view("backend/header",$data);
        $this->load->view("product/backend/html/add");
        $this->load->view("backend/footer");

    }


    public function hiddenProductOutOfDate()
    {
        $this->load->model("product/product_model","mProductModel");
        $this->mProductModel->hiddenProductOutOfDate();
    }


    public function verify()
    {

        if (!GroupAccess::isGranted('product',MANAGE_PRODUCTS))
            redirect("error?page=permission");

        $status = $this->input->get('status');
        $id = intval($this->input->get('id'));
        $accept = intval($this->input->get('accept'));


        $this->mProductModel->verify($id,$accept);

        /*($status == 1) ? redirect(admin_url("product/my_products")) : redirect(admin_url("product/all_products"));*/
        echo json_encode(array(Tags::SUCCESS => 1));
        return;

    }


}

/* End of file ProductDB.php */