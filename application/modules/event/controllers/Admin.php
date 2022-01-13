<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();
        //load model

        ModulesChecker::checkRequirements('event');
        ModulesChecker::requireEnabled('event');

        // hide the product if the date

        if(defined('ENABLE_AUTO_HIDDEN_EVENTS')){
            if(ENABLE_AUTO_HIDDEN_EVENTS)
                $this->hiddenEventsOutOfDate();
        }else{
            $this->mConfigModel->save('ENABLE_AUTO_HIDDEN_EVENTS',FALSE);
        }



    }

    public function all_events(){

        if (!GroupAccess::isGranted('event',MANAGE_EVENTS)  )
            redirect("error?page=permission");

        $status = intval($this->input->get("status")) ;
        $typeAuth = intval($this->input->get("typeAuth")) ;
        $page = intval($this->input->get("page"));
        $search = $this->input->get("search");
        $limit = NO_OF_ITEMS_PER_PAGE;
        $store_id  = intval($this->input->get("store_id"));

        $params = array(
            "limit"   =>$limit,
            "page"    =>$page,
            "store_id" =>$store_id,
            "search"  => $search,
            "status"  => -1
        );

        $data['data'] = $this->mEventModel->getEvents($params);

        $this->load->view("backend/header",$data);
        $this->load->view("event/backend/html/events");
        $this->load->view("backend/footer");

    }

    public function my_events(){

        if (!GroupAccess::isGranted('event'))
            redirect("error?page=permission");

        $status = intval($this->input->get("status")) ;
        $typeAuth = intval($this->input->get("typeAuth")) ;
        $page = intval($this->input->get("page"));
        $search = $this->input->get("search");
        $limit = NO_OF_ITEMS_PER_PAGE;
        $store_id  = intval($this->input->get("store_id"));


        $params = array(
            "limit"   =>$limit,
            "page"    =>$page,
            "store_id" =>$store_id,
            "search"  => $search,
            "status"  => -1
        );

        $params['user_id'] = $this->mUserBrowser->getData("id_user");

        $data['data'] = $this->mEventModel->getEvents($params);

        $this->load->view("backend/header",$data);
        $this->load->view("event/backend/html/events");
        $this->load->view("backend/footer");

    }
    
    public function create(){

        if (!GroupAccess::isGranted('event',ADD_EVENT))
            redirect("error?page=permission");


        $data = array();

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));

        $this->load->view("backend/header");
        $this->load->view("event/backend/html/create",$data);
        $this->load->view("backend/footer");

    }


    public function view(){

        if (!GroupAccess::isGranted('event',MANAGE_EVENTS))
            redirect("error?page=permission");

        $event_id = $this->input->get("id");

        $data['dataEvents'] = $this->mEventModel->getEvents(array(
            "limit"     => 1,
            "event_id"   => $event_id,
        ));

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));

        $this->load->view("backend/header",$data);
        $this->load->view("event/backend/html/edit");
        $this->load->view("backend/footer");


    }


    public function edit(){

        if (!GroupAccess::isGranted('event',EDIT_EVENT))
            redirect("error?page=permission");

        $event_id = $this->input->get("id");
        $data['dataEvents'] = $this->mEventModel->getEvents(array(
            "limit"     => 1,
            "event_id"   => $event_id,
        ));

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));

        $this->load->view("backend/header",$data);
        $this->load->view("event/backend/html/edit");
        $this->load->view("backend/footer");


    }


    public function hiddenEventsOutOfDate()
    {
        $this->mEventModel->hiddenEventsOutOfDate();
    }


    public function verify()
    {

        if ($this->mUserBrowser->isLogged()) {

            if (!GroupAccess::isGranted('event',MANAGE_EVENTS))
                redirect("error?page=permission");


            $id = intval($this->input->get('id'));
            $accept = intval($this->input->get('accept'));


            $this->db->where('id_event',$id);
            $this->db->update('event',array(
                'verified' => 1,
                'status'   => $accept,
            ));


        }

        //redirect(admin_url('event/all_events'));

        echo json_encode(array(Tags::SUCCESS => 1));
        return;
    }




}

/* End of file EventDB.php */