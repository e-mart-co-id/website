<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {



    public function __construct(){
        parent::__construct();
        //load model


        $this->load->model("nshistoric/notification_history_model","mHistoric");

    }

    public function turnOff(){


        $id = intval($this->input->post("id"));

        //id & status
        $data =  $this->mHistoric->remove($id);

        echo json_encode($data);return;

    }


    public function remove(){


        $id = intval($this->input->post("id"));

        //id & status
        $data =  $this->mHistoric->remove($id);

        echo json_encode($data);return;

    }

    public function changeStatus(){


        $id = intval($this->input->post("id"));
        $status = intval($this->input->post("status"));

        //id & status

        $data =  $this->mHistoric->changeStatus($id,$status);

        if($status == 1){

            $notification = $this->mHistoric->getNotification($id);
            if($notification != NULL){
                ActionsManager::add_action('nshistoric','read_notification',$notification);
            }

        }


        echo json_encode($data);return;

    }

    public function getCount(){

        $limit = intval($this->input->post("limit"));
        $page = intval($this->input->post("page"));

        $device_date = $this->input->post("date");
        $device_timzone = $this->input->post("timezone");


        $auth_type = $this->input->post("auth_type");
        $auth_id = $this->input->post("auth_id");
        $status = $this->input->post("status");


        $user_id = $this->input->post("user_id");
        $guest_id = $this->input->post("guest_id");

        $params = array(

            //single user guest or logged user
            "auth_type"         =>      $auth_type,
            "auth_id"           =>      $auth_id,

            //both users
            "user_id"           =>      $user_id,
            "guest_id"           =>      $guest_id,

            "status"            =>      $status,
            "limit"             =>      $limit,
            "page"              =>      $page,
            "device_date"       =>      $device_date,
            "device_timezone"   =>      $device_timzone,
        );

        $data =  $this->mHistoric->getCount($params);

        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$data));return;

    }


    public function countUnseenNotification(){



        $auth_type = $this->input->post("auth_type");
        $auth_id = $this->input->post("auth_id");

        $params = array(
            "auth_type"         =>      $auth_type,
            "auth_id"           =>      $auth_id
        );

        $data =  $this->mHistoric->countUnseenNotification($params);

        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$data));return;

    }

    public function getNotifications(){


        $limit = intval($this->input->post("limit"));
        $page = intval($this->input->post("page"));

        $auth_type = $this->input->post("auth_type");
        $auth_id = $this->input->post("auth_id");

        $device_date = $this->input->post("date");
        $device_timzone = $this->input->post("timezone");

        $user_id = $this->input->post("user_id");
        $guest_id = $this->input->post("guest_id");


        $params = array(
            "auth_type"         =>     $auth_type,
            "auth_id"           =>      $auth_id,

            //both users
            "user_id"           =>      $user_id,
            "guest_id"           =>      $guest_id,



            "limit"             =>      $limit,
            "page"              =>      $page,
            "device_date"       =>      $device_date,
            "device_timezone"   =>      $device_timzone,
        );

        $data =  $this->mHistoric->getNotifications($params);


        if($data[Tags::SUCCESS]==1){
            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            echo Json::convertToJson($data[Tags::RESULT],  Tags::RESULT,TRUE,array(Tags::COUNT=>$data[Tags::COUNT]));
        }else{

            echo json_encode($data);
        }

    }


}