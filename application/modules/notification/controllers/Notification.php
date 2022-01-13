<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notification extends MAIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->init('notification');
    }


    public function _register()
    {

        $this->load->model("notification/notification_model", "mNotificationModel");

        $name = Security::decrypt($this->input->post("name"));
        $email = Security::decrypt($this->input->post("email"));
        $regId = Security::decrypt($this->input->post("regId"));


        $params = array(
            "name" => $name,
            "email" => $email,
            "regId" => $regId
        );


        return json_encode($this->mNotificationModel->register($params));

    }

    public function sendNotification()
    {

        $this->load->model("notification/notification_model", "mNotificationModel");

        $rId = Security::decrypt($this->input->post("registerId"));

        $params = array(
            "registerId" => $rId,
        );


        return json_encode($this->mNotificationModel->send_notification($params));

    }


    public function onInstall()
    {
        return TRUE;
    }

    public function onUpgrade()
    {
        return TRUE;
    }

    public function onEnable()
    {
        return TRUE;
    }


}