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


          ModulesChecker::requireEnabled("setting");


    }

	public function index()
	{


	}


    public function messagesToTranslate(){

        echo "You need navigate in the website to save all messages<br><br>";

        if(isset($_SESSION['toTranslate'])){
            foreach ($_SESSION['toTranslate'] as $key => $item) {
                echo $key.": $item<br>";
            }
        }

    }

    public function translate(){

        $uri = $this->uri->segment(3);

        if($this->mUserBrowser->isLogged()) {

            if($uri==""){

                $data['default_language'] = Translate::loadLanguageFromYmlToTranslate(
                    $this->input->get("language")
                );

                $this->load->view("backend/header",$data);
                $this->load->view("setting/backend/application/translate");
                $this->load->view("backend/footer");

            }else if($uri=="android"){


            }

        }
    }

    public function currencies(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',MANAGE_CURRENCIES))
            redirect("error?page=permission");


        TemplateManager::set_settingActive('currencies');

        $data['currencies'] = $this->mCurrencyModel->getAllCurrencies();
        $data['config'] = $this->mConfigModel->getParams();

        $this->load->view("backend/header",$data);
        $this->load->view("setting/backend/html/currency");
        $this->load->view("backend/footer");


    }

    public function deeplinking(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting'))
            redirect("error?page=permission");


        TemplateManager::set_settingActive('deeplinking');


        $data = array();

        $this->load->view("backend/header",$data);
        $this->load->view("setting/backend/html/deeplinking");
        $this->load->view("backend/footer");

    }

    public function api_config(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        TemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();

        $this->load->view("backend/header",$data);
        $this->load->view("setting/backend/html/api_config");
        $this->load->view("backend/footer");


    }


    public function application(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        TemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();
        $data['components'] = SettingViewer::loadComponent();

        $this->load->view("backend/header",$data);
        $this->load->view("setting/backend/html/config");
        $this->load->view("backend/footer");


    }

    public function app_config_xml(){

        if (!GroupAccess::isGranted('setting'))
            redirect("error?page=permission");

        $this->load->view("backend/header");
        $this->load->view("setting/backend/application/app_config_xml");
        $this->load->view("backend/footer");

    }


    public function logs(){

        $this->load->view("backend/header");
        $this->load->view("setting/backend/html/logs");
        $this->load->view("backend/footer");

    }

    public function edit_timezone(){

        $c = $this->input->get('c');
        $tz = $this->input->get('tz');

        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        if(in_array($tz,$timezones)){
            $this->mUserModel->save_user_subscribe_setting('user_timezone',$tz);
        }

        //refresh session
        $this->mUserBrowser->refreshData(
            $this->mUserBrowser->getData('id_user')
        );

        $c = base64_decode($c);
        redirect($c);

    }


    public function clearAll(){


        if(GroupAccess::isGranted('setting')){

            $started_id = 112;
            $this->db->where('id_store >',$started_id);
            $this->db->select('id_store');
            $stores = $this->db->get('store');
            $stores = $stores->result();

            foreach ($stores as $store){
                $this->mStoreModel->delete($store->id_store);
            }

            echo count($stores)." was removed";


        }
    }


    public function cronjob(){

        if (!GroupAccess::isGranted('setting',MANAGE_CURRENCIES))
            redirect("error?page=permission");


        $this->load->view("backend/header");
        $this->load->view("setting/backend/html/cronjob");
        $this->load->view("backend/footer");

        //Modules::run("setting/cron_exe");
        //redirect(admin_url("?ex1cron=executed"));
    }


}

/* End of file SettingDB.php */