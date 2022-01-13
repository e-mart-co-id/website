<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 12/28/18
 * Time: 22:28
 */

class ImageManagerUtils{


    const IMAGE_SIZE_200 = "200_200";
    const IMAGE_SIZE_100 = "100_100";

    public static function getImage($dir){

        $images = _openDir($dir);
        if(isset($images['200_200']['url'])){
            return $images['200_200']['url'];
        }

        return "";
    }

    public static function getFirstImage($images,$size=self::IMAGE_SIZE_200){

        if(is_string($images))
            $images  = json_decode($images,JSON_OBJECT_AS_ARRAY);

        if(isset($images[0]) && is_string($images[0])){
            $images = _openDir($images[0]);
            if(isset($images[$size]['url'])){
                return $images[$size]['url'];
            }
        }else if(isset($images[0]) && is_array($images[0])){
            $images = $images[0];
            if(isset($images[$size]['url'])){
                return $images[$size]['url'];
            }
        }

        return "";
    }


    public static function checkAndClearImages(){

        $context = &get_instance();




    }

    public static function imageHTML($images){
        if (isset($images['100_100']['url'])) {
            return '<img src="' . $images['100_100']['url'] . '"width="50" height="50" alt="Product Image">';
        } else {
            return '<img src="' . base_url("views/skin/backend/images/def_logo.png") . '"width="50" height="50" alt="Product Image">';
        }
    }

    public static function getValidImages($userImageStr){

        if($userImageStr=="")
            return array();

        //convert from image ID or json to the array
        if (!is_array($userImageStr) and !preg_match('#^([0-9]+)$#',$userImageStr)) {
            $userImage = json_decode($userImageStr, JSON_OBJECT_AS_ARRAY);
        }else if(!is_array($userImageStr) and preg_match('#^([0-9]+)$#',$userImageStr)) {
            $userImage = array($userImageStr);
        }



        $array = array();

        if(isset($userImage)){
            foreach ($userImage as $dirName){

                $userImage = _openDir($dirName);

                if(!empty($userImage))
                    $array[] = $userImage;

            }
        }else{
            $array = $userImageStr;
        }

        //validate all images

        $new_arrays = array();

        foreach ($array as $key => $img){
            if(empty($img))
                unset($array[$key]);
            else
                $new_arrays[] = $img;
        }

        return $new_arrays;
    }




}