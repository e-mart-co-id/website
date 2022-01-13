<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function saveConfig(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('setting',CHANGE_APP_SETTING)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $LOCATION_PICKER_HERE_MAPS_APP_ID = $this->input->post("LOCATION_PICKER_HERE_MAPS_APP_ID");
        $LOCATION_PICKER_HERE_MAPS_APP_CODE = $this->input->post("LOCATION_PICKER_HERE_MAPS_APP_CODE");
        $LOCATION_PICKER_OP_PICKER = $this->input->post("LOCATION_PICKER_OP_PICKER");
        $MAPS_API_KEY = $this->input->post("MAPS_API_KEY");
        $GOOGLE_PLACES_API_KEY = $this->input->post("GOOGLE_PLACES_API_KEY");

        ConfigManager::setValue("LOCATION_PICKER_HERE_MAPS_APP_ID",$LOCATION_PICKER_HERE_MAPS_APP_ID);
        ConfigManager::setValue("LOCATION_PICKER_HERE_MAPS_APP_CODE",$LOCATION_PICKER_HERE_MAPS_APP_CODE);
        ConfigManager::setValue("LOCATION_PICKER_OP_PICKER",$LOCATION_PICKER_OP_PICKER);
        ConfigManager::setValue("MAPS_API_KEY",$MAPS_API_KEY);
        ConfigManager::setValue("GOOGLE_PLACES_API_KEY",$GOOGLE_PLACES_API_KEY);

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }



    public function query(){

        $country = trim($this->input->get('country'));
        $country = urlencode($country);

        $q = trim($this->input->get('q'));
        $q = urlencode($q);

        if(ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==1){
            $result = $this->location_picker_model->getLocalitiesHEREMaps($country,$q,Translate::getDefaultLangCode());
        }else if(ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==2){
            $result = $this->location_picker_model->getGooglPlacesLocality($country,$q,Translate::getDefaultLangCode());
        }else{
            $result = array();
        }

        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$result));return;

    }


    public function getAddresses(){

        $country = trim($this->input->get('country'));
        $country = urlencode($country);

        $q = trim($this->input->get('q'));
        $q = urlencode($q);

        if(ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==1){
            $result = $this->location_picker_model->getLocalitiesHEREMaps($country,$q,"en");
        }else if(ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==2){
           // $result = $this->location_picker_model->getGooglPlacesLocality($country,$q,"en");
            $result = $this->location_picker_model->getLocalityAutocomplete($country,$q,"en");
        }else{
            $result = array();
        }

        echo json_encode($result);return;

    }


    public function getAddressDetail(){

        $latitude = $this->input->get('latitude');
        $longitude = $this->input->get('longitude');

        $result = $this->location_picker_model->getAddressDetail($latitude,$longitude);
        echo json_encode($result);return;

    }


    private function echoError(){

    }

}