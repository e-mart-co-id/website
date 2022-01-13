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
class Api extends API_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("event/event_model", "mEventModel");

    }


    public function saveEventBK()
    {


       $user_id = trim($this->input->post("user_id"));
        $event_id = Security::decrypt($this->input->post("event_id"));


        $this->load->module('bookmark');

        $params = array(
            'user_id' => $user_id,
            'module_id' => $event_id,
            'module' => "event",
        );


        if($user_id>0){
            $params['guest_id'] = $this->mUserModel->getGuestIDByUserId($user_id);
        }


        if (!BookmarkManager::exist($params))
            $data["first_time"] = 1;
        else {
            $data["first_time"] = 0;
        }

        $data = BookmarkManager::add($params);

        echo json_encode($data);


    }


    public function removeEventBK()
    {

        $user_id = trim($this->input->post("user_id"));
        $event_id = Security::decrypt($this->input->post("event_id"));

        $this->load->module('bookmark');

        $params = array(
            'user_id' => $user_id,
            'module_id' => $event_id,
            'module' => "event",
        );


        $data = BookmarkManager::remove($params);

        echo json_encode($data);

    }


    public function getEvents()
    {


       $limit = intval($this->input->post("limit"));
        $page = intval($this->input->post("page"));
        $order_by = $this->input->post("order_by");

        $latitude = doubleval($this->input->post("latitude"));
        $longitude = doubleval($this->input->post("longitude"));

        $event_id = intval($this->input->post("event_id"));
        $search = $this->input->post("search");
        $mac_adr = $this->input->post("mac_adr");
        $event_ids = Security::decrypt($this->input->post("event_ids"));
        $radius = $this->input->post("radius");
        $date = $this->input->post("date");
        $timezone = $this->input->post("timezone");

        $params = array(
            "limit" => $limit,
            "page" => $page,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "event_id" => $event_id,
            "event_ids" => $event_ids,
            "search" => $search,
            "status" => 1,
            "mac_adr" => $mac_adr,
            "order_by" => $order_by,
            "radius" => $radius,
            "date_end" => $date,
        );

        $data = $this->mEventModel->getEvents($params);


        if ($data[Tags::SUCCESS] == 1) {

            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array(Tags::COUNT => $data[Tags::COUNT]));
        } else {

            echo json_encode($data);
        }

    }


}
