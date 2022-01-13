<?php

class TimeZoneManager{

    public static function getTimeZone(){
        $context = &get_instance();
        $u_time_zone = $context->mConfigModel->get("TIME_ZONE");

        if(SessionManager::isLogged()){
            $u_time_zone = SessionManager::getData('user_timezone');
        }

        return $u_time_zone;
    }
}


class ConfigManager{


    public static function getValue($key=NULL){
        $context = &get_instance();
        return $context->mConfigModel->get($key);
    }


    public static function setValue($key=NULL,$value=NULL,$init=FALSE){
        $context = &get_instance();

        if($init == TRUE && defined($key))
            return TRUE;

        return $context->mConfigModel->save($key,$value);
    }


    public static function isIos(){
        if(defined("IOS_API"))
            return TRUE;
        return FALSE;
    }

    public static function isAndroid(){
        if(defined("ANDROID_API"))
            return TRUE;
        return FALSE;
    }


    public static function isAndriodNiOS(){
        if(defined("ANDROID_API") && defined("IOS_API"))
            return TRUE;
        return FALSE;
    }


}


class SettingViewer{

    private static $component = array();
    private static $module_order = array();



    public static function register($module,$path="",$data=array())
    {

        if(!isset(self::$component[$module])){
            self::$component[$module] = array();
            self::$component[$module][] = array(
                'path' => $path,
                'config' => $data,
            );
        }else{
            self::$component[$module][] = array(
                'path' => $path,
                'config' => $data,
            );
        }

    }

    public static function loadComponent()
    {


        $component = array();

        /*
         * Start
         */


        foreach (self::$component as $key => $v2){
            if($key=="setting"){

                $component[] = array(
                    'module' => $key,
                    'blocks' => self::$component[$key],
                );

                unset(self::$component[$key]);
                break;
            }
        }


        //re-order block depend on its saved order
        //
        $ordered_modules = FModuleLoader::getModules();

        foreach ($ordered_modules as $k => $value){

            if(!isset($component[$value['module_name']])
                && isset(self::$component[$value['module_name']])){

                $component[$k] = array(
                    'module' => $value['module_name'],
                    'blocks' => self::$component[$value['module_name']],
                );

            }

        }

        foreach (self::$component as $key => $v1){

            $m_exist = FALSE;

            foreach ($component as $v2){

                if($v2['module'] == $key){
                    $m_exist = TRUE;
                    break;
                }
            }

            if($m_exist == FALSE){

                $last_order = key(array_slice($component, -1, 1, true));
                $last_order++;

                $component[$last_order] = array(
                    'module' => $key,
                    'blocks' => self::$component[$key],
                );
            }

        }



        return $component;

    }

    public static function getRealPath($module,$path){



    }


}


class TokenSetting{


    public static function createToken($uid=0,$type="unspecified"){

        $context = &get_instance();
        $token = md5(time() . rand(0, 999));


        $context->db->insert('token', array(
            "id" => $token,
            "uid" => $uid,
            "type" => $type,
            "created_at" => date("Y-m-d", time())
        ));

        return $token;
    }

    public static function re_create_token($uid=0,$type="unspecified"){

        $context = &get_instance();
        $token = md5(time() . rand(0, 999));

        /*$context->db->where("uid",$uid);
        $context->db->where("type",$type);
        $context->db->delete("token");*/

        $context->db->insert('token', array(
            "id" => $token,
            "uid" => $uid,
            "type" => $type,
            "created_at" => date("Y-m-d", time())
        ));

        return $token;
    }


    public static function getValid($uid=0,$type="unspecified",$token=""){

        $context = &get_instance();

        $context->db->where("uid",$uid);
        $context->db->where("type",$type);
        $context->db->where("id",$token);
        $get = $context->db->get('token',1);
        $get = $get->result();
        if(isset($get[0]))
            return $get[0];

        return NULL;
    }

    public static function get_by_uid($uid=0,$type="unspecified"){

        $context = &get_instance();

        $context->db->where("uid",$uid);
        $context->db->where("type",$type);
        $get = $context->db->get('token',1);
        $get = $get->result();
        if(isset($get[0]))
            return $get[0];

        return NULL;
    }

    public static function get_by_token($token="",$type="unspecified"){

        $context = &get_instance();
        $context->db->where("id",$token);
        $context->db->where("type",$type);
        $get = $context->db->get('token',1);
        $get = $get->result();
        if(isset($get[0]))
            return $get[0];

        return NULL;
    }


    public static function isValid($uid=0,$type="unspecified",$token=""){

        $context = &get_instance();

        $context->db->where("uid",$uid);
        $context->db->where("type",$type);
        $context->db->where("id",$token);
        $count = $context->db->count_all_results('token');
        if($count==1)
            return TRUE;

        return FALSE;
    }


    public static function clear($token=""){

        $context = &get_instance();
        $context->db->where("id",$token);
        $context->db->delete('token');

    }


}

