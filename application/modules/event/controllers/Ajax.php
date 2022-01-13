<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of event_webservice
 *
 * @author idriss
 */
class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model("event/event_model", "mEventModel");
        $this->load->model("store/store_model", "mStoreModel");
        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("user/user_browser", "mUserBrowser");

    }

    public function getEventsAjax(){

        $params = array(
            "limit"   => 5,
            "store_id" => $this->input->get('store_id'),
            "search"  => $this->input->get('search'),
            "user_id"  => $this->mUserBrowser->getData('id_user'),
            "status"  => 1
        );

        $data = $this->mEventModel->getEvents($params);


        $result = array();

        if(isset($data[Tags::RESULT]))
            foreach ($data[Tags::RESULT] as $object){

                $o = array(
                    'text' =>  Text::output($object['name']).' ('.$object['store_name'].')',
                    'id' =>  $object['id_event'],

                    'title' =>  Text::output($object['name']),
                    'description' =>  strip_tags(Text::output($object['description'])),
                    'image' =>  ImageManagerUtils::getFirstImage( $object['images']),
                );

                if($object['store_name'] ==""){
                    $o['text'] =  Text::output($object['name']);
                }

                if(strlen($o['description'])>100){
                    $o['description'] = substr(strip_tags(Text::output($o['description'])),0,100).' ...';
                }

                $result['results'][] = $o;


            }

        echo json_encode($result,JSON_OBJECT_AS_ARRAY);return;
    }

    public function markAsFeatured(){

        //check if user have permission
        $this->enableDemoMode();

        if(!GroupAccess::isGranted('event',MANAGE_EVENTS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $user_id = $this->mUserBrowser->getData("user_id");

            $id   = intval($this->input->post("id"));
            $featured   = intval($this->input->post("featured"));


                echo json_encode(
                    $this->mEventModel->markAsFeatured(array(
                        "user_id"  => $user_id,
                        "id" => $id,
                        "featured" => $featured

                    ))
                );
                return;


        }

        echo json_encode(array(Tags::SUCCESS=>0));
    }




    public function create()
    {

        if(!GroupAccess::isGranted('event',ADD_EVENT)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


            $name = $this->input->post("name");
            $address = $this->input->post("address");
            $desc = $this->input->post("desc");
            $website = $this->input->post("website");
            $tel = $this->input->post("tel");
            $date_b = $this->input->post("date_b");
            $date_e = $this->input->post("date_e");
            //$user = Security::decrypt($this->input->post("id_user"));
            $images = $this->input->post("images");
            $store_id = intval($this->input->post("store_id"));
            $lat = doubleval($this->input->post("lat"));
            $lng = doubleval($this->input->post("lng"));


            echo json_encode($this->mEventModel->create(array(
                "name"      => $name,
                "address"   => $address,
                "desc"      => $desc,
                "website"   => $website,
                "lat"       => $lat,
                "lng"       => $lng,
                "tel"       => $tel,
                "date_b"    => $date_b,
                "date_e"    => $date_e,
                "images"    => $images,
                "store_id"  => $store_id,
                "user_id"   => $this->mUserBrowser->getData("id_user")
            )));
        }


    public function edit()
    {
        if(!GroupAccess::isGranted("event",EDIT_EVENT))
        {
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $id = intval($this->input->post("id"));
        $name = $this->input->post("name");
        $address = $this->input->post("address");
        $desc = $this->input->post("desc");
        $website = $this->input->post("website");
        $tel = $this->input->post("tel");
        $date_b = $this->input->post("date_b");
        $date_e = $this->input->post("date_e");
        //$user = Security::decrypt($this->input->post("id_user"));
        $images = $this->input->post("images");
        $store_id = intval($this->input->post("store_id"));
        $lat = doubleval($this->input->post("lat"));
        $lng = doubleval($this->input->post("lng"));


        $params = array(
            "id"        => $id,
            "name"      => $name,
            "address"   => $address,
            "desc"      => $desc,
            "website"   => $website,
            "lat"       => $lat,
            "lng"       => $lng,
            "tel"       => $tel,
            "date_b"    => $date_b,
            "date_e"    => $date_e,
            "images"    => $images,
            "store_id"  => $store_id,
            "user_id"   => $this->mUserBrowser->getData("id_user")
        );


        echo json_encode($this->mEventModel->updateEvent($params));


    }


    public function delete()
    {

        if(!GroupAccess::isGranted("event",DELETE_EVENT))
        {
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = intval($this->input->post("id"));;

        echo json_encode($this->mEventModel->delete(array(
            "id"        => $id,
            "user_id"         => $this->mUserBrowser->getData("id_user")
        )));


    }


    public function changeStatus()
    {

        if(!GroupAccess::isGranted('event',MANAGE_EVENTS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = intval($this->input->get("id"));

        $this->db->select("status");
        $this->db->from("event");
        $this->db->where("id_event", $id);
        $statusUser = $this->db->get();
        $statusUser = $statusUser->result_array();

        if (isset($statusUser[0]) && $statusUser[0]['status']==0) {
            $data['status'] = 1;
        } else if (isset($statusUser[0]) && $statusUser[0]['status']==1) {
            $data['status'] = 0;
        } else {
            $errors["status"] = Translate::sprint(Messages::STATUS_NOT_FOUND);
        }

        if (isset($data) AND empty($errors)) {

            $this->db->where("id_event", $id);
            $this->db->update("event", $data);

            echo json_encode(array(Tags::SUCCESS => 1, "url" => admin_url("event/all_events")));
            return;
        } else {
            echo json_encode(array(Tags::SUCCESS => 0, "errors" => $errors));
            return;
        }

    }


}
