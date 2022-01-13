<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 4/19/19
 * Time: 13:56
 */


class CMS_Display{

    private static $hook_data_list = array();

    public static function createHook($hook){
        self::$hook_data_list[$hook] = array();
    }

    public static function setHTML($hook, $html){
        self::$hook_data_list[$hook][] = array(
            'html' => $html
        );
    }

    public static function set($hook, $path, $data=array()){

        if(!isset(self::$hook_data_list[$hook]['replaced'])){
            self::$hook_data_list[$hook][] = array(
                'path' => $path,
                'data' => $data,
            );
        }else{
            /*self::$hook_data_list[$hook]['merged'] = array();
            self::$hook_data_list[$hook]['merged'][] = array(
                'path' => $path,
                'data' => $data,
            );*/
        }

    }

    public static function replace($hook, $path, $data=array()){
        self::$hook_data_list[$hook] = array();
        self::$hook_data_list[$hook]["replaced"] = array(
            'path' => $path,
            'data' => $data,
        );
    }


    public static function render($hook){
        if(isset(self::$hook_data_list[$hook])){

            foreach (self::$hook_data_list[$hook] as $key => $data){

                if(isset($data['path'])){
                    $context = &get_instance();
                    $context->load->view(
                        $data['path'],
                        $data['data']
                    );
                }else{
                    echo $data['html'];
                }

            }

        }
    }
}