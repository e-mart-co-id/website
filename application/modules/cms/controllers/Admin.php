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


        ModulesChecker::requireEnabled("cms");
    }

    public function home(){


        if($this->mUserBrowser->isLogged()){

            $myData['chart_v1_home'] = SimpleChart::get('chart_v1_home');

            if(!empty($myData['chart_v1_home'])){

                CMS_Display::set(
                    "widget_top",
                    "cms/backend/charts/counter_v1",
                    $myData
                );

                CMS_Display::set(
                    "widget_top",
                    "cms/backend/charts/charts_v1",
                    $myData
                );

            }

            $data = array();


            $this->load->view("backend/header",$data);
            $this->load->view("cms/backend/html/home");
            $this->load->view("backend/footer");

        }else{
            redirect(site_url("user/login"));
        }

    }

    public function error404(){
        $this->load->view("backend/header");
        $this->load->view("cms/backend/error404");
        $this->load->view("backend/footer");
    }



    public function groupAccessExampleAdmin(){


        $modules = GroupAccess::getModuleActions();

        $data = array();
        foreach ($modules as $key => $ac){
            $data[$key] = array();
            foreach ($ac as $key1 => $value){
                $data[$key][$value] = 1;
            }
        }

        echo "Admin<br><br>";
        echo json_encode($data,JSON_FORCE_OBJECT);

        $data = array();
        foreach ($modules as $key => $ac){
            $data[$key] = array();
            foreach ($ac as $key1 => $value){
                $data[$key][$value] = 0;
            }
        }

        echo "<br><br>MobileUser<br><br>";
        echo json_encode($data,JSON_FORCE_OBJECT);
        die();


    }

}

/* End of file CmsDB.php */