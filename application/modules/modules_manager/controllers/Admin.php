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
        ModulesChecker::requireRegistred("modules_manager");

    }


    public function index(){

    }

    public function manage(){

        if(!GroupAccess::isGranted('modules_manager',MANAGE_MODULES))
            redirect("error?page=permission");

        TemplateManager::set_settingActive('modules_manager');


        $data['modules'] = ModuleManager::fetch();

        $this->load->view("backend/header",$data);
        $this->load->view("modules_manager/backend/html/list");
        $this->load->view("backend/footer");


    }


    public function add(){

        if(!GroupAccess::isGranted('modules_manager',MANAGE_MODULES))
            redirect("error?page=permission");

        TemplateManager::set_settingActive('modules_manager');

        $data['modules'] = ModuleManager::fetch();

        $this->load->view("backend/header",$data);
        $this->load->view("modules_manager/backend/html/add");
        $this->load->view("backend/footer");


    }


}

/* End of file CmsDB.php */