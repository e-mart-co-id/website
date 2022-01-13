<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller
{


    public function __construct()
    {
        parent::__construct();
    }


    public function availableModules()
    {
        $data['modules'] = ModuleManager::fetch();
        //print_r($data['modules']); die();
        $availableMdules = array();
        if (!is_array($data['modules']))
            $data['modules'] = json_decode($data['modules'], JSON_OBJECT_AS_ARRAY);

        foreach ($data['modules'] as $module) {
            $availableMdules[] = array(
                "module_name" => $module["module_name"],
                "enabled" => $module["_enabled"],
            );

        }

        echo json_encode(array(Tags::SUCCESS => 1, Tags::RESULT => $availableMdules), JSON_FORCE_OBJECT);

    }

}