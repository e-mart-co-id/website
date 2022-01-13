<?php


    //default false
    define("SAFE_MODE", true);
    //the app version
    define("APP_VERSION", "2.2.0");
    define("APP_CODE_VERSION", 220);
    ///////////////////////////////////
    define("_LOGS", 1);
    define("INDEX", "index.php");

    define("FIRST_PLATFORM", "df-android");//api item ID
    define("SECOND_PLATFORM", "df-ios");//api item ID
    define("PROJECT_NAME", APP_VERSION . "," . FIRST_PLATFORM);

    define("ENCODING", "UTF-8");

    /*
     * IMAGE CONFIGURATION
     */
    define("MAX_IMAGE_UPLOAD", 2); //by MB
    define("MAX_NBR_IMAGES", 6);
    define("MAX_STORE_IMAGES", 8);
    define("MAX_GALLERY_IMAGES", 20);


    /*
     * SESS CONFIGURATION
     */
    define("SESS_USE_LOCAL_CACHE", false); //by MB


    /*
     * DEMO
     */

    if(file_exists('demo.php')){
        require_once 'demo.php';
    }else{
        define("DEMO",false);
        define("DEMO_user_id",4);
    }

    /*
    * EXTRAS CONFIGURATION
    */






