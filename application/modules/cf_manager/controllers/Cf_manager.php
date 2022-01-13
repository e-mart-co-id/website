<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cf_manager extends MAIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->init('cf_manager');
    }

    public function onLoad()
    {
        $this->load->model("cf_manager/cf_manager_model","mCFManager");
        $this->load->helper("cf_manager/func");

        define('MANAGE_CF','manage_custom_fields');

    }

    public function onCommitted($isEnabled)
    {
        if(!$isEnabled)
            return;

        TemplateManager::registerMenu(
            'cf_manager',
            "cf_manager/menu",
            11
        );
    }

    public function onEnable()
    {

        GroupAccess::registerActions("cf_manager",array(
            MANAGE_CF
        ));

        $this->mCFManager->create_default_cf();

        return TRUE;
    }

    public function onUpgrade()
    {
        parent::onUpgrade(); // TODO: Change the autogenerated stub
        $this->mCFManager->createCFTable();
        $this->mCFManager->updateFields();

        return TRUE;
    }


    public function onInstall()
    {
        parent::onInstall(); // TODO: Change the autogenerated stub
        $this->mCFManager->createCFTable();

        return TRUE;
    }


}