<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller  {

    public function __construct(){
        parent::__construct();
        //load model

    }


    public function delete(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nsbanner')){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = $this->input->post("id");

        $this->db->where('id',intval($id));
        $this->db->delete('ns_banners');

        echo json_encode(array(Tags::SUCCESS=>1));return;

    }

    public function disable(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nsbanner')){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $id = $this->input->get("id");

        $this->db->where('id',intval($id));
        $this->db->update('ns_banners',array(
            'status' => 0
        ));


        redirect(admin_url('nsbanner/all'));
    }

    public function enable(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nsbanner')){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = $this->input->get("id");

        $this->db->where('id',intval($id));
        $this->db->update('ns_banners',array(
            'status' => 1
        ));


        redirect(admin_url('nsbanner/all'));

    }

    public function edit(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nsbanner',NS_BANNER_GRP_ACTION_ADD)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = $this->input->post("id");
        $module = $this->input->post("module");
        $module_id =  $this->input->post("module_id");
        $date_begin =  $this->input->post("date_begin");
        $date_end =  $this->input->post("date_end");
        $is_can_expire =  $this->input->post("is_can_expire");
        $title =  $this->input->post("title");
        $description =  $this->input->post("description");
        $images =  $this->input->post("images");

        $params = array(
            "id" => $id,
            "module" => $module,
            "module_id" => $module_id,
            "date_start" => $date_begin,
            "date_end" => $date_end,
            "is_can_expire" => $is_can_expire,
            "title" => $title,
            "description" => $description,
            "image" => $images,
        );

        echo json_encode(
            $this->nsbanner_model->edit($params)
        );return;

    }

    public function add(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nsbanner',NS_BANNER_GRP_ACTION_ADD)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $module = $this->input->post("module");
        $module_id =  $this->input->post("module_id");
        $date_begin =  $this->input->post("date_begin");
        $date_end =  $this->input->post("date_end");
        $is_can_expire =  $this->input->post("is_can_expire");
        $title =  $this->input->post("title");
        $description =  $this->input->post("description");
        $images =  $this->input->post("images");

        $params = array(
            "module" => $module,
            "module_id" => $module_id,
            "date_start" => $date_begin,
            "date_end" => $date_end,
            "is_can_expire" => $is_can_expire,
            "title" => $title,
            "description" => $description,
            "image" => $images,
        );

        echo json_encode(
            $this->nsbanner_model->add($params)
        );return;

    }





}