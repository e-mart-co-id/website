<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Ajax extends AJAX_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("setting/Update_model","mUpdateModel");
    }

    public function update_version(){
        ConfigManager::setValue('_APP_VERSION',APP_VERSION);
    }

    public function sverify(){

        if(!GroupAccess::isGranted('setting',CHANGE_APP_SETTING)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check version update
        $response = $this->mUpdateModel->verifyPurchaseId();
        $response = json_decode($response,JSON_OBJECT_AS_ARRAY);


        if(isset($response[Tags::SUCCESS]) and $response[Tags::SUCCESS]==0){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERROR=>$response[Tags::ERROR]));return;
        }else if(isset($response[Tags::SUCCESS]) and $response[Tags::SUCCESS]==1){
            echo json_encode($response);return;
        }else{
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERROR=>"There is some error in api server side, please try later or report it to our support"));
            return;
        }

    }



    public function addNewCurrency()
    {

        if(!GroupAccess::isGranted('setting',MANAGE_CURRENCIES)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        echo  json_encode($this->mCurrencyModel->addNewCurrency(
            $this->input->post()
        ));
    }

    public function editCurrency()
    {

        if(!GroupAccess::isGranted('setting',MANAGE_CURRENCIES)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        echo  json_encode($this->mCurrencyModel->editCurrency(
            $this->input->post()
        ));
    }

    public function deleteCurrency()
    {
        if(!GroupAccess::isGranted('setting',MANAGE_CURRENCIES)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        echo  json_encode($this->mCurrencyModel->deleteCurrency(
            $this->input->post()
        ));
    }

    public function saveAppConfig()
    {

        if(!GroupAccess::isGranted('setting',CHANGE_APP_SETTING)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }




        //check if user have permission
        $this->enableDemoMode();
        $params = $this->input->post();

        if(isset($params['DEFAULT_CURRENCY']) && $params['DEFAULT_CURRENCY']!=DEFAULT_CURRENCY){
            ActionsManager::add_action("setting","currency_changed",$params['DEFAULT_CURRENCY']);
        }



        echo  json_encode($this->mConfigModel->saveAppConfig($params));

    }




}

/* End of file SettingDB.php */