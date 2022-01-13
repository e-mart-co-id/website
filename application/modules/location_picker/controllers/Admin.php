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
        //load model

        ModulesChecker::requireEnabled("location_picker");

    }


    public function config(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        TemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();

        $this->load->view("backend/header",$data);
        $this->load->view("location_picker/backend/html/config");
        $this->load->view("backend/footer");


    }







}

/* End of file CampaignDB.php */