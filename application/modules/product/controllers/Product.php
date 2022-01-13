<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Product extends MAIN_Controller {

    public function __construct(){
        parent::__construct();

        $this->init("product");
    }

    public function onLoad()
    {

        define('MAX_PRODUCT_IMAGES',6);
        define('KS_NBR_PRODUCTS_MONTHLY','nbr_products_monthly');

        define('ADD_PRODUCT','add');
        define('EDIT_PRODUCT','edit');
        define('DELETE_PRODUCT','delete');
        define('MANAGE_PRODUCTS','manage_products');

        //load model
        $this->load->model("product/product_model","mProductModel");
        //load helper
        $this->load->helper('product/product');
    }

    public function onCommitted($isEnabled)
    {
        parent::onCommitted($isEnabled); // TODO: Change the autogenerated stub

        if(!$isEnabled)
            return;

        $this->load->model("user/group_access_model","mGroupAccessModel");
        $this->load->helper("user/group_access");
        $this->load->helper("user/user");


        TemplateManager::registerMenu(
            'product',
            "product/menu",
            2
        );


        //Setup User Config
        UserSettingSubscribe::set('product',array(
            'field_name' => KS_NBR_PRODUCTS_MONTHLY,
            'field_type' => UserSettingSubscribeTypes::INT,
            'field_default_value' => -1,
            'config_key' => 'NBR_PRODUCTS_MONTHLY',
            'field_label' => 'Products allowed monthly',
            'field_sub_label' => '( -1 Unlimited )',
            'field_comment' => '',
        ));


        if($this->mUserBrowser->isLogged() && GroupAccess::isGranted('product')){
            $this->load->helper('cms/charts');
            SimpleChart::add('product','chart_v1_home',function ($months){
                if(GroupAccess::isGranted('product',MANAGE_PRODUCTS)){
                    return $this->mProductModel->getProductsAnalytics($months);
                }else{
                    return $this->mProductModel->getProductsAnalytics($months,$this->mUserBrowser->getData('id_user'));
                }

            });
        }

        StoreManager::subscribe('product','store_id');

        $this->register_actions();

    }

    private function register_actions(){

        //User action listener
        ActionsManager::register('user','user_switch_to',function ($args){
            $this->mProductModel->switchTo($args['from'], $args['to']);
        });


        //register event to campaign program
        CampaignManager::register(array(
            'module' => $this,
            'api'    => site_url('ajax/product/getProductsAjax'),
            'callback_input' => function($args){
                return $this->mProductModel->campaign_input($args);
            },
            'callback_output' => function($args){
                return $this->mProductModel->campaign_output($args);
            },

            'custom_parameters' => array(
                'html' => $this->load->view('store/backend/campaign/html',array('module'=>'product'),TRUE),
                'script' => $this->load->view('store/backend/campaign/script',array('module'=>'product'),TRUE),
                'var' => "product_custom_parameters",
            )
            /*'custom_parameters' => array(
                'platforms' => array(
                    'values' => array(
                        'iOS' => 1,
                        'android' => 2
                    ),
                    'type' => 'checkbox'
                ),
                'getting_option' => array(
                    'values' => array(
                        'nearby_connected' => 1,
                        'random' => 2
                    ),
                    'type' => 'radio'
                )
            )*/
        ));



        //store
        BookmarkLinkedModule::newInstance('product','getData',function ($args){

            $params = array(
                "product_id" => $args['id'],
                "limit" => 1,
            );

            $items =  $this->mProductModel->getProducts($params);

            if(isset($items[Tags::RESULT][0])){

                return array(
                    'currency' => $items[Tags::RESULT][0]['currency'],
                    'commission' => $items[Tags::RESULT][0]['commission'],
                    'label' => $items[Tags::RESULT][0]['name'],
                    'label_description' => $items[Tags::RESULT][0]['description'],
                    'image' => $items[Tags::RESULT][0]['images'],
                );
            }

            return NULL;
        });

        //register setting component
        $this->registerSetting();


        ActionsManager::register("store","func_getStores",function ($list){

            foreach ($list as $key => $value){
                $list[$key]['nbrProducts'] = $this->db->where("status", 1)->where("is_offer", 0)->where("hidden", 0)->where("store_id", $value['id_store'])->count_all_results("product");
            }

            return $list;
        });

        ActionsManager::register("setting","currency_changed",function ($currency){
            $this->mProductModel->update_product_currency($currency);
        });

    }

    private function registerSetting(){

        //register component for setting viewer
        SettingViewer::register("product","product/setting_viewer/product_config",array(
            'title' => _lang("Product"),
        ));


    }

    private function registerModuleActions(){

        GroupAccess::registerActions("product",array(
            ADD_PRODUCT,
            EDIT_PRODUCT,
            DELETE_PRODUCT,
            MANAGE_PRODUCTS
        ));

    }

	public function index()
	{

	}

    public function dp()
    {
        redirect(site_url(""));
    }


    public function id(){
        $this->load->library('user_agent');

        $id = intval($this->uri->segment(3));

        if($id==0)
            redirect("?err=1");

        $platform =  $this->agent->platform();

        if(/*Checker::user_agent_exist($user_agent,"ios")*/ strtolower($platform)=="ios"){

            $link = site_url("product/id/$id");
            $link = str_replace('www.', '', $link);
            $link = str_replace('http://', 'dsapp://', $link);
            $link = str_replace('https://', 'dsapp://', $link);

            $this->session->set_userdata(array(
                "redirect_to" =>  $link
            ));

            redirect("");
        }

        redirect("");

    }


    public function onUpgrade()
    {
        parent::onUpgrade(); // TODO: Change the autogenerated stub

        $this->mProductModel->emigrateDatabase();
        $this->mProductModel->updateFields();
        $this->registerModuleActions();

        return TRUE;
    }

    public function onInstall()
    {
        parent::onInstall(); // TODO: Change the autogenerated stub

        $this->mProductModel->emigrateDatabase();
        $this->mProductModel->updateFields();

        return TRUE;
    }

    public function cron(){
        //restore all discounted item if the offer is expired


    }

    public function onEnable()
    {
        $this->registerModuleActions();

    }



}

/* End of file ProductDB.php */