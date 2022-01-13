<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

require_once FCPATH . "/application/modules/user/libraries/recaptchalib.php";

class User extends MAIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        /////// register module ///////
        $this->init("user");
    }


    public function onLoad()
    {
        define('reCAPTCHA', FALSE);
        //ACTIONS
        define('ADD_USERS', 'add');
        define('EDIT_USER', 'edit');
        define('DELETE_USERS', 'delete');
        define('USER_SETTING', 'user_setting');
        define('MANAGE_GROUP_ACCESS', 'manage_group_access');
        define('MANAGE_USERS', 'manage_users');
        define('DASHBOARD_ACCESSIBILITY', 'dashboard_accessibility');


        $this->load->model("user/group_access_model", "mGroupAccessModel");
        $this->load->helper("user/group_access");
        $this->load->helper("user/user");

        //load model
        $this->load->model("user_model", "mUserModel");
        $this->load->model("user_browser", "mUserBrowser");


    }

    //call it after loaded all main modules
    public function onCommitted($isEnabled)
    {

        if (!$isEnabled)
            return;

        if (SessionManager::isLogged()) {
            //update session
            $user_id = SessionManager::getData("id_user");
            $this->mUserBrowser->refreshData($user_id);
        }

        TemplateManager::registerMenu(
            'user',
            "user/menu",
            8
        );

        TemplateManager::registerMenuSetting(
            'user',
            "user/menu_setting",
            8
        );


        ConfigManager::setValue("USER_PHONE_VERIFICATION", TRUE, TRUE);
        ConfigManager::setValue("DEFAULT_USER_GRPAC", 0, TRUE);
        ConfigManager::setValue("DEFAULT_USER_GRPAC", 0, TRUE);
        ConfigManager::setValue("DEFAULT_USER_MOBILE_GRPAC", 0, TRUE);


        UserSettingSubscribe::set('user', array(
            'field_name' => 'user_settings_package',
            'field_type' => UserSettingSubscribeTypes::TEXT,
            'field_default_value' => "",
            'config_key' => 'USER_SETTINGS_PACKAGE', //<= use default value from config
            '_display' => 0
        ));

        UserSettingSubscribe::set('user', array(
            'field_name' => 'user_timezone',
            'field_type' => UserSettingSubscribeTypes::VARCHAR,
            'field_default_value' => "UTC",
            'config_key' => 'TIME_ZONE', //<= use default value from config
            '_display' => 0
        ));


        UserSettingSubscribe::set('user', array(
            'field_name' => 'user_language',
            'field_type' => UserSettingSubscribeTypes::VARCHAR,
            'field_default_value' => "en",
            'config_key' => 'DEFAULT_LANG', //<= use default value from config if needed
            '_display' => 0
        ));


        //$this->generateViewHomePage();



        if (!class_exists('SimpleChart'))
            $this->load->helper('cms/charts');

        if ($this->mUserBrowser->isLogged() && GroupAccess::isGranted('user', MANAGE_USERS)) {
            $this->load->helper('cms/charts');
            SimpleChart::add('user', 'chart_v1_home', function ($months) {
                return $this->mUserModel->getUsersAnalytics($months);
            });
        }


        CMS_Display::set('user_v1', 'user/plug/cms/header');


        //user notes
        NotesManager::addNew(
            TM_Note::newInstance("user",
                $this->userNotesHTML()
            )
        );

        //register setting component
        //$this->registerSetting();

    }

    private function registerSetting(){

        //register component for setting viewer
        SettingViewer::register("user","user/setting_viewer/user_setting",array(
            'title' => _lang("User settings"),
        ));

    }


    private function generateViewHomePage()
    {

        CMS_Display::setHTML(
            "widget_bottom",
            "<div class=\"row\">"
        );

        CMS_Display::set(
            "widget_bottom",
            "user/widget/latest_members"
        );

        CMS_Display::setHTML(
            "widget_bottom",
            "</div>"
        );

    }


    private function userNotesHTML()
    {
        return $this->load->view('user/plug/user_alerts/html', NULL, TRUE);
    }

    public function onEnable()
    {
        GroupAccess::registerActions("user", array(
            ADD_USERS,
            EDIT_USER,
            DELETE_USERS,
            USER_SETTING,
            MANAGE_GROUP_ACCESS,
            MANAGE_USERS,
            DASHBOARD_ACCESSIBILITY
        ));

    }

    public function onUpgrade()
    {
        // TODO: Implement onUpgrade() method.
        parent::onUpgrade();
        $this->mGroupAccessModel->createTableModuleActions();
        $this->mGroupAccessModel->createTableGroupAccess();
        $this->mGroupAccessModel->updateFields();

        $this->mUserModel->addFields();
        $this->mUserModel->updateFields();

        GroupAccess::registerActions("user", array(
            ADD_USERS,
            EDIT_USER,
            DELETE_USERS,
            USER_SETTING,
            MANAGE_GROUP_ACCESS,
            MANAGE_USERS,
            DASHBOARD_ACCESSIBILITY
        ));


        return TRUE;
    }

    public function onInstall()
    {
        parent::onInstall(); // TODO: Change the autogenerated stub
        $this->mGroupAccessModel->createTableModuleActions();
        $this->mGroupAccessModel->createTableGroupAccess();
        $this->mGroupAccessModel->updateFields();


        $this->mUserModel->addFields();
        $this->mUserModel->updateFields();

        return TRUE;
    }


    public function index()
    {


    }

    public function userConfirm()
    {


        $token = $this->input->get("id");
        $uid = $this->mUserModel->mailVerification($token);

        if ($uid > 0) {

            $user_data = $this->mUserModel->syncUser(
                array(
                    "user_id" => $uid,
                )
            );

            $user_data = $user_data[Tags::RESULT];

            if (count($user_data) > 0) {
                $this->mUserBrowser->setID($user_data[0]['id_user']);
                $this->mUserBrowser->setUserData($user_data[0]);
            }

        }

        redirect(site_url("user/verifEmail"));
    }


    //USER AUTH

    //USER AUTH

    public function signup(){

        if(USER_REGISTRATION==FALSE){
            redirect(admin_url("login"));
            return;
        }

        $lang = $this->input->get("lang");

        if($lang!=""){
            Translate::changeSessionLang($lang);
            redirect('user/signup');
        }

        if($this->mUserBrowser->isLogged()){
            redirect(admin_url(""));
        }else{
            $this->load->view("user/frontend/header");
            $this->load->view("user/frontend/html/signup");
            $this->load->view("user/frontend/footer");
        }


    }

    public function login(){

        $lang = $this->input->get("lang");

        if($lang!=""){
            Translate::changeSessionLang($lang);
            redirect('user/login');
        }


        if($this->mUserBrowser->isLogged()){
            redirect(admin_url(""));
        }else{
            $this->load->view("user/frontend/header");
            $this->load->view("user/frontend/html/login");
            $this->load->view("user/frontend/footer");
        }


    }

    public function verifEmail()
    {
        $this->load->view("user/frontend/header");
        $this->load->view("user/frontend/html/verifEmail");
        $this->load->view("user/frontend/footer");
    }


    public function logout()
    {

        if($this->mUserBrowser->isLogged()){

            $this->mUserBrowser->LogOut();
            redirect("user/login");

        }else{
            redirect("user/login");
        }

    }


    public function fpassword(){

        $this->load->view("user/frontend/header");
        $this->load->view("user/frontend/html/fpassword");
        $this->load->view("user/frontend/footer");

    }

    public function rpassword(){

        $this->load->view("user/frontend/header");
        $this->load->view("user/frontend/html/rpassword");
        $this->load->view("user/frontend/footer");

    }


    public function setupDefaultGroupAccess()
    {

        $this->mGroupAccessModel->setupDefaultGroupAccess();

    }

    public function createDefaultUser()
    {

        $login = $this->input->post("login");
        $email = $this->input->post("email");
        $password = $this->input->post("password");
        $name = $this->input->post("name");
        $timezone = $this->input->post("timezone");

        //create super admin account
        $result = $this->mUserModel->createDefaultAdmin($login, $password, $email, $name, $timezone);

        $modules = FModuleLoader::loadAllModules();
        //reload all grp modules for super admin
        foreach ($modules as $module) {
            //reload permission grp
            GroupAccess::reloadPermission($module, $result->grp_access_id);
        }


        //trigger onEnable callbacks
        foreach ($modules as $module) {
            if (method_exists($this->{$module}, 'onEnable')) {
                $this->{$module}->onEnable();
            }
        }

        //generate all necessary {group_access}
        $group_accesses = array(
            'BusinessOwner' => '{"modules_manager":{"manage_modules":0},"store":{"add":1,"edit":1,"delete":1,"manage_stores":0},"user":{"add":0,"edit":0,"delete":0,"user_setting":0,"manage_group_access":0,"manage_users":0,"dashboard_accessibility":1},"setting":{"change_app_setting":0,"manage_currencies":0},"product":{"add":1,"edit":1,"delete":1,"manage_products":0},"cf_manager":{"manage_custom_fields":0},"nstranslator":{"manage":0},"gallery":{"manage_gallery":1},"category":{"add":0,"edit":0,"delete":0},"nsorder":{"manage_orders":1,"manage_order_status":0,"manage_order_config":0},"payment":{"config_payment":0,"display_transactions":0,"display_billing":1,"manage_taxes":0},"campaign":{"push_campaigns":1,"edit":1,"delete":1,"manage_campaigns":0},"messenger":{"send_and_receive":1,"manage_messages":0},"event":{"add":1,"edit":1,"delete":1,"manage_events":0}}',
            'UserMobile' => '{"store":{"add":0,"edit":0,"delete":0,"manage_stores":0},"user":{"add":0,"edit":0,"delete":0,"user_setting":0,"manage_group_access":0,"manage_users":0,"dashboard_accessibility":0},"setting":{"change_app_setting":0,"manage_currencies":0},"product":{"add":0,"edit":0,"delete":0,"manage_products":0},"modules_manager":{"manage_modules":0},"nstranslator":{"manage":0},"gallery":{"manage_gallery":0},"category":{"add":0,"edit":0,"delete":0},"campaign":{"push_campaigns":0,"edit":0,"delete":0,"manage_campaigns":0},"messenger":{"send_and_receive":0,"manage_messages":0},"event":{"add":0,"edit":0,"delete":0,"manage_events":0}}'
        );

        $this->db->insert('group_access', array(
            'name' => 'BusinessOwner',
            'permissions' => $group_accesses['BusinessOwner'],
            'editable' => TRUE,
            'updated_at' => date('Y-m-d H:i:s', time()),
            'created_at' => date('Y-m-d H:i:s', time()),
        ));

        $id = $this->db->insert_id();
        ConfigManager::setValue('DEFAULT_USER_GRPAC', $id);

        $this->db->insert('group_access', array(
            'name' => 'UserMobile',
            'permissions' => $group_accesses['UserMobile'],
            'editable' => TRUE,
            'updated_at' => date('Y-m-d H:i:s', time()),
            'created_at' => date('Y-m-d H:i:s', time()),
        ));

        $id = $this->db->insert_id();
        ConfigManager::setValue('DEFAULT_USER_MOBILE_GRPAC', $id);


        if ($result != NULL) {
            echo json_encode(array(Tags::SUCCESS => 1));
        } else
            echo json_encode(array(Tags::SUCCESS => 0));

        return;


    }


}

/* End of file UserDB.php */