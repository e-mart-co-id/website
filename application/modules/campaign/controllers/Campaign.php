<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Campaign extends MAIN_Controller {

    public function __construct(){
        parent::__construct();

        $this->init("campaign");

    }

    public function onLoad() //load model
    {


        $var = array(
            "store","offer","event"
        );

        define("CAMPAIGN_TYPES",json_encode($var)) ;

        define('PUSH_CAMPAIGNS','push_campaigns');
        define('EDIT_CAMPAIGN','edit');
        define('DELETE_CAMPAIGNS','delete');
        define('MANAGE_CAMPAIGNS','manage_campaigns');

        define('KS_PUSH_CAMPAIGN_AUTO','push_campaign_auto');
        define('KS_NBR_CAMPAIGN_MONTHLY','nbr_campaigns_monthly');

        define("CPT_SELECTOR_ENABLED",true);

        $this->load->model("campaign/campaign_model","mCampaignModel");
        $this->load->helper("campaign/campaign");
    }

    //call it after loading modules
    public function onCommitted($isEnabled)
    {
        parent::onCommitted($isEnabled); // TODO: Change the autogenerated stub

        if(!$isEnabled)
            return;

        //add menu to sidebar
        TemplateManager::registerMenu(
            'campaign',
            "campaign/menu",
            4
        );

        $settings = array(
            array(
                'field_name' => KS_PUSH_CAMPAIGN_AUTO,
                'field_type' => UserSettingSubscribeTypes::BOOLEAN,
                'field_default_value' => true,
                'config_key' => 'PUSH_CAMPAIGN_AUTO',
                'field_label' => 'Push Auto Campaigns',
                'field_comment' => '',
            ),
            array(
                'field_name' => KS_NBR_CAMPAIGN_MONTHLY,
                'field_type' => UserSettingSubscribeTypes::INT,
                'field_default_value' => -1,
                'config_key' => 'NBR_CAMPAIGNS_MONTHLY',
                'field_label' => 'Campaigns allowed monthly',
                'field_sub_label' => '( -1 Unlimited )',
                'field_comment' => '',
            ),
        );

        UserSettingSubscribe::setGroup('campaign',$settings);


        if($this->mUserBrowser->isLogged() && GroupAccess::isGranted('campaign')){

            $this->load->helper('cms/charts');

            SimpleChart::add('campaign','chart_v1_home',function ($months){

                if(GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)){
                    return $this->mCampaignModel->getCampaignsAnalytics($months);
                }else{
                    return $this->mCampaignModel->getCampaignsAnalytics($months,$this->mUserBrowser->getData('id_user'));
                }

            });
        }

        CMS_Display::set("campaigns_pending_list_v1","campaign/plug/header/html",NULL);


        //listen to nshistoric if status was changed to (read)
        ActionsManager::register('nshistoric','read_notification',function ($args){

           if($args != NULL){

               $campaign_id = $args['campaign_id'];

               $params = array(
                   "campaignId"  => intval($campaign_id),
                   "guest_id"  => 0,
                   "user_id"  => 0,
               );

               if($args['auth_type'] == "guest"){
                   $params['guest_id'] = intval($args['auth_id']);
               }

               if($args['auth_type'] == "user"){
                   $params['user_id'] = intval($args['auth_id']);
               }

               $this->mCampaignModel->markView($params);
           }

        });


        //use notification agreement
        ConfigManager::setValue("_NOTIFICATION_AGREEMENT_USE",FALSE,TRUE);

        //register setting component
        $this->registerSetting();
    }

    private function registerSetting(){

        //register component for setting viewer
        SettingViewer::register("campaign","campaign/setting_viewer/html",array(
            'title' => _lang("Campaign & Notification"),
        ));

    }

    private function registerModuleActions(){

        GroupAccess::registerActions("campaign",array(
            PUSH_CAMPAIGNS,
            EDIT_CAMPAIGN,
            DELETE_CAMPAIGNS,
            MANAGE_CAMPAIGNS,
        ));

    }

    public function cron(){

        parent::cron();

        $this->load->model("campaign/campaign_model");
        $this->campaign_model->pushPendingCampaigns();
        echo "Cron executed!";

    }


    public function onInstall()
    {
        parent::onInstall(); // TODO: Change the autogenerated stub

        $this->mCampaignModel->updateFields();
        $this->mCampaignModel->create_tracker_table();

        return TRUE;
    }

    public function onUpgrade()
    {
        $this->mCampaignModel->updateFields();
        $this->mCampaignModel->create_tracker_table();

        $this->registerModuleActions();


        return TRUE;
    }

    public function onEnable()
    {
        parent::onEnable(); // TODO: Change the autogenerated stub
        //Register Module Action
        $this->registerModuleActions();

        return TRUE;
    }

    public function onDisable()
    {
        parent::onDisable(); // TODO: Change the autogenerated stub


        return TRUE;
    }

    public function onUninstall()
    {
        parent::onUninstall(); // TODO: Change the autogenerated stub
    }



}

/* End of file CampaignDB.php */