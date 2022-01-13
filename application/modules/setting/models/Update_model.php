<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Update_model extends CI_Model
{


    public $loadedFiles = "";


    public function __construct()
    {
        parent::__construct();

        if(preg_match("#android#i",FIRST_PLATFORM))
            define("PIDINDEX","ANDROID_PURCHASE_ID");
        else
            define("PIDINDEX","IOS_PURCHASE_ID");

        define("SITEM",APP_VERSION.",".SECOND_PLATFORM);

    }



    public function saveSettingDB($params=array()){



        foreach ($params as $key => $value){

            $this->db->where('_key',$key);
            $c = $this->db->count_all_results('app_config');

            $type = 'N/A';

            if(is_array($value)){
                $value = json_encode($value,JSON_FORCE_OBJECT);
                $type = 'json';
            }else if(is_numeric($value)){

                $value = Text::strToNumber($value);

                if(is_float($value)){
                    $value = floatval($value);
                    $type = 'float';
                }else if(is_integer($value)){
                    $value = intval($value);
                    $type = 'int';
                }else if(is_double('double')){
                    $value = doubleval($value);
                    $type = 'double';
                }

            }else if(is_string($value)){
                $type = 'string';
            }

            if($c==0){

                $this->db->insert('app_config',array(
                    '_key' => $key,
                    'value' => $value,
                    '_type' => $type,
                    'is_verified' => 0,
                    '_version' => APP_VERSION
                ));
            }

        }


    }


    public function checkVersion($version,$code,$type=""){

        for($i=1;$i<=10;$i++){
            if($version==$code.".".$i.$type){
                return TRUE;
            }
        }

        return FALSE;
    }

    public function checkAndPutPID(){

        if(file_exists("config/".PARAMS_FILE.".json")){
            $path = "config/".PARAMS_FILE.".json";
            $params = url_get_content(Path::getPath(array($path)));
        }else{
            $path = "config/params.json";
            $params = url_get_content(Path::getPath(array($path)));
        }

        $params = json_decode($params,JSON_OBJECT_AS_ARRAY);

        if(isset($params['_APP_VERSION'])){ //check version of config files
            $app_v_json = ($params['_APP_VERSION']);
            $app_v_php = (APP_VERSION);

            if($app_v_json==$app_v_php)
                return array(Tags::SUCCESS=>1);
        }

        $id = trim($this->input->get("spid"));

        if($id=="")
            $id = trim($this->input->get("pid"));
        $id = base64_decode($id);
        if($id!=""){
            $params[PIDINDEX] = $id;
            //update file config
            @file_put_contents(Path::getPath(array($path)),json_encode($params,JSON_FORCE_OBJECT));
        }

    }


    public function getPid(){

        $pid = $this->input->post("pid");

        return $pid;
    }


    public function verifyPurchaseId(){

        $pid = $this->input->post("pid");

        $result = MyCurl::run("https://api.droideve.com/api/api2/linker",array(
            "item"                  => PROJECT_NAME,
            "sitem"                  => SITEM,
            "pid"                   => $pid,
            "reqfile"               => 1,
            "update-settings"       => $this->loadedFiles,
        ));

        $data = json_decode($result,JSON_OBJECT_AS_ARRAY);

        if(isset($data[Tags::SUCCESS]) and $data[Tags::SUCCESS]==1){

            $filekey = "";
            if(isset($data["datasettings"])){

                $settings = base64_decode($data["datasettings"]);
                $settings = json_decode($settings,JSON_OBJECT_AS_ARRAY);

                try{
                    $this->mConfigModel->save('FILE',$settings["FILE"]);
                    $this->mConfigModel->save('HASLINKER',$settings['HASLINKER']);
                }catch (Exception $e){
                    return array(Tags::SUCCESS=>0);
                }

            }

            if(isset($data["linkerdata"]) AND isset($settings['HASLINKER'])){

                $linkerdata = base64_decode($data["linkerdata"]);
                $linkerdata = json_decode($linkerdata,JSON_OBJECT_AS_ARRAY);

                foreach ($linkerdata as $key => $value)
                    $this->mConfigModel->save($key,$value);
            }


        }

        return $result;
    }




    function parse($content = "", $args = array())
    {

        foreach ($args as $key => $value) {
            $content = preg_replace("#\{" . $key . "\}#", $value, $content);
        }
        return $content;

    }



}