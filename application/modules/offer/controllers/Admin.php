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

        ModulesChecker::requireEnabled("offer");
        $this->load->helper('url');
    }

	public function index()
	{

	}


    public function view(){

        if (!GroupAccess::isGranted('offer',MANAGE_OFFERS))
            redirect("error?page=permission");

        $data = array();


        $params = array(
            "offer_id"  => intval($this->input->get("id")),
            "limit"     => 1
        );

        $data['offer'] = $this->mOfferModel->getOffers($params);

        if (isset($data['offer'][Tags::RESULT]) and count($data['offer'][Tags::RESULT]) == 1) {
            $this->load->view("backend/header", $data);
            $this->load->view("offer/backend/html/edit");
            $this->load->view("backend/footer");
        }


    }

    public function edit(){

        if (!GroupAccess::isGranted('offer',EDIT_OFFER))
            redirect("error?page=permission");

        $data = array();

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id" => $this->mUserBrowser->getData("id_user")
        ));

        $params = array(
            "offer_id"  => intval($this->input->get("id")),
            "limit"     => 1,
            "user_id" => SessionManager::getData('id_user')
        );

        $data['offer'] = $this->mOfferModel->getOffers($params);

        $data['cf_list'] = $this->mCFManager->getList(
            SessionManager::getData("id_user")
        );

        if (isset($data['offer'][Tags::RESULT]) and count($data['offer'][Tags::RESULT]) == 1) {
            $this->load->view("backend/header", $data);
            $this->load->view("offer/backend/html/edit");
            $this->load->view("backend/footer");
        }


    }

    public function all_offers(){


        if (!GroupAccess::isGranted('offer',MANAGE_OFFERS)  )
            redirect("error?page=permission");


        $data = array();

        $params =array(
            "offer_id" => $this->input->get("offer_id"),
            "store_id" => $this->input->get("store_id"),
            "date_end" => $this->input->get("date_end"),
            "page" => $this->input->get("page"),
            "search" => $this->input->get("search"),
            "limit"     => NO_OF_ITEMS_PER_PAGE,
            "is_super"     => TRUE,
            "status"     => $this->input->get("status"), // filter offer by status
            "filterBy"     => $this->input->get("filterBy"),
        );

        $data['offers'] = $this->mOfferModel->getOffers($params);
        $data['list_title'] = "All offers";

        $this->load->view("backend/header",$data);
        $this->load->view("offer/backend/html/offers");
        $this->load->view("backend/footer");


    }

    public function my_offers(){


        if (!GroupAccess::isGranted('user')  )
            redirect("error?page=permission");

        $data = array();

        $params =array(
            "offer_id" => $this->input->get("offer_id"),
            "store_id" => $this->input->get("store_id"),
            "date_end" => $this->input->get("date_end"),
            "page"      => $this->input->get("page"),
            "search" => $this->input->get("search"),
            "limit"     => NO_OF_ITEMS_PER_PAGE,
            "user_id"     => SessionManager::getData('id_user'),
            "status"     => $this->input->get("status"), // filter offer by status
            "filterBy"     => $this->input->get("filterBy"),
        );

        $data['offers'] = $this->mOfferModel->getOffers($params);
        $data['list_title'] = "My offers";

        $this->load->view("backend/header",$data);
        $this->load->view("offer/backend/html/offers");
        $this->load->view("backend/footer");


    }

    public function add(){

        if (!GroupAccess::isGranted('offer',ADD_OFFER))
            redirect("error?page=permission");

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));

        $data['cf_list'] = $this->mCFManager->getList(
            SessionManager::getData("id_user")
        );

        $this->load->view("backend/header",$data);
        $this->load->view("offer/backend/html/add");
        $this->load->view("backend/footer");

    }




}

/* End of file OfferDB.php */