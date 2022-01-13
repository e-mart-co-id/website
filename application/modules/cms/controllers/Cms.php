<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Cms extends MAIN_Controller {

    public function __construct(){
        parent::__construct();
        $this->init("cms");
    }



    public function onLoad()
    {

        /////// init module ///////
        $this->load->helper('cms/charts');
        $this->load->helper('cms/CMS');

        //init hook
        CMS_Display::createHook("overview_chart_months");
        CMS_Display::createHook("overview_counter");

        CMS_Display::createHook("widget_top");
        CMS_Display::createHook("widget_middle");
        CMS_Display::createHook("widget_bottom");

        //init charts
        SimpleChart::init("chart_v1_home");

    }

    public function onCommitted($isEnabled)
    {

        if(!$isEnabled)
            return;

        TemplateManager::registerMenu(
            'cms',
            "cms/menu",
            7
        );

    }


    public function error404(){

        if($this->mUserBrowser->isLogged()){
            $this->load->view("backend/header");
            $this->load->view("backend/error404");
            $this->load->view("backend/footer");
        }else{
            redirect(site_url("user/login"));
        }

    }

    public function onInstall()
    {

        ConfigManager::setValue("ENABLE_FRONT_END",FALSE);

        return TRUE;
    }

    public function onUpgrade()
    {

        ConfigManager::setValue("ENABLE_FRONT_END",FALSE);

        return TRUE;
    }

    public function onEnable()
    {
        return TRUE;
    }


}

/* End of file CmsDB.php */