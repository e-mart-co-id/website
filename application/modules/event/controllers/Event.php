<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Event extends MAIN_Controller {

    public function __construct(){
        parent::__construct();
        $this->init("event");
    }



    public function onLoad()
    {

        define('MAX_EVENT_IMAGES',6);
        define('KS_NBR_EVENTS_MONTHLY','nbr_events_monthly');

        define('ADD_EVENT','add');
        define('EDIT_EVENT','edit');
        define('DELETE_EVENT','delete');
        define('MANAGE_EVENTS','manage_events');

        $this->load->model('event/event_model','mEventModel');
        $this->load->helper('event/event');
    }

    public function onCommitted($isEnabled)
    {
        parent::onCommitted($isEnabled); // TODO: Change the autogenerated stub

        return;

        if(!$isEnabled)
            return;

        TemplateManager::registerMenu(
            'event',
            "event/menu",
            3
        );

        //Setup User Config
        UserSettingSubscribe::set('event',array(
            'field_name' => KS_NBR_EVENTS_MONTHLY,
            'field_type' => UserSettingSubscribeTypes::INT,
            'field_default_value' => -1,
            'config_key' => 'NBR_EVENTS_MONTHLY',
            'field_label' => 'Events Allowed Monthly',
            'field_sub_label' => '( -1 Unlimited )',
            'field_comment' => '',
        ));


        if($this->mUserBrowser->isLogged() && GroupAccess::isGranted('event')){

            $this->load->helper('cms/charts');

            SimpleChart::add('event','chart_v1_home',function ($months){

                if(GroupAccess::isGranted('event',MANAGE_EVENTS)){
                    return $this->mEventModel->getEventsAnalytics($months);
                }else{
                    return $this->mEventModel->getEventsAnalytics($months,$this->mUserBrowser->getData('id_user'));
                }

            });
        }


        //add FK to an store
        StoreManager::subscribe('event','store_id');

        //User action listener
        ActionsManager::register('user','user_switch_to',function ($args){
            $this->mEventModel->switchTo($args['from'], $args['to']);
        });


        //register event to campaign program
        CampaignManager::register(array(
            'module' => $this,
            'api'    => site_url('ajax/event/getEventsAjax'),
            'callback_input' => function($args){
               return $this->mEventModel->campaign_input($args);
            },
            'callback_output' => function($args){
                return $this->mEventModel->campaign_output($args);
            },

            'custom_parameters' => array(
                'html' => $this->load->view('store/backend/campaign/html',array('module'=>'event'),TRUE),
                'script' => $this->load->view('store/backend/campaign/script',array('module'=>'event'),TRUE),
                'var' => "event_custom_parameters",
            )


        ));

        //register setting component
        $this->registerSetting();
    }

    private function registerSetting(){

        //register component for setting viewer
        SettingViewer::register("event","event/setting_viewer/html",array(
            'title' => _lang("Event"),
        ));


    }

    private function registerModuleActions(){


        GroupAccess::registerActions("event",array(
            ADD_EVENT,
            EDIT_EVENT,
            DELETE_EVENT,
            MANAGE_EVENTS
        ));

    }

    public function index(){

    }

    public function id(){

        $this->load->library('user_agent');

        $id = intval($this->uri->segment(3));

        if($id==0)
            redirect("?err=1");

        $platform =  $this->agent->platform();

        if(/*Checker::user_agent_exist($user_agent,"ios")*/ strtolower($platform)=="ios"){

            $link = site_url("event/id/$id");
            $link = str_replace('www.', '', $link);
            $link = str_replace('http://', 'nsapp://', $link);
            $link = str_replace('https://', 'nsapp://', $link);

            $this->session->set_userdata(array(
                "redirect_to" =>  $link
            ));

            redirect("");
        }

        redirect("");

    }

    public function onEnable()
    {
        $this->registerModuleActions();
        return TRUE;
    }

    public function onInstall()
    {
        parent::onInstall(); // TODO: Change the autogenerated stub
        $this->mEventModel->updateFields();

        ConfigManager::setValue('ENABLE_AUTO_HIDDEN_EVENTS',FALSE,TRUE);

        return TRUE;
    }

    public function onUpgrade()
    {
        parent::onUpgrade(); // TODO: Change the autogenerated stub
        $this->mEventModel->updateFields();
        $this->registerModuleActions();

        ConfigManager::setValue('ENABLE_AUTO_HIDDEN_EVENTS',FALSE,TRUE);

        return TRUE;
    }




}

/* End of file EventDB.php */