<?php

    require_once '../init.php';
    require_once 'init.php';

    $base_url0 = APPURL;
    $base_url = APPURL . "/index.php";


    //purchase verification
    $api_endpoint = "https://api.droideve.com/api/api2";


    $post_data = array(
        "pid" => Input::post("pid"),
        "ip" => getIp(),
        "uri" => APPURL,
        "item" => PROJECT_NAME,
        "reqfile" => 1,
    );

    // Validate License Key
    $validation_url = $api_endpoint . "/pchecker";

    try {
        $validation = runCURL($validation_url, $post_data);
        $validation = json_decode($validation);
    } catch (Exception $e) {
        jsonecho("The API server is down! message error: \"" . $e->getMessage() . "\"", 105);
    }


    if (!isset($validation->success)) {
        jsonecho("Couldn't validate your license key!. (Error:011)", 104);
    }

    if ($validation->success != 1) {
        if ($validation->success == 0) {
            jsonecho("Couldn't validate your license key! (Error:012)".json_encode($validation), 105);
        } else {
            jsonecho($validation->error, 105);
        }
    }


    $new_settings = $validation->datasettings;

    //generate modules table & get existing modules
    $api_endpoint_get_modules = $base_url . '/modules_manager/ajax/get_modules';
    $_deployed_modules = runCURL($api_endpoint_get_modules, array());

    $deployed_modules = json_decode($_deployed_modules, JSON_OBJECT_AS_ARRAY);


    if (!isset($deployed_modules['result'])) { //
        $messages = "Something wrong! #UP01<br>";
        jsonecho($messages, 105);
    }


    //install main (user) modules
    $api_url = $base_url . '/modules_manager/ajax/install';
    $response = runCURL($api_url, array("module_id" => "user"));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if(!isset($response['success'])){
        jsonecho("Something went wrong during install (Code: 0UI),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='".$base_url0."/logs/LogViewer.php'>here</a>", 105);
    }


    //install all modules
    $api_url = $base_url . '/modules_manager/ajax/bulk_install';
    $_response = runCURL($api_url, array("modules" => json_encode($deployed_modules['result'])));
    $response = json_decode($_response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if(!isset($response['success'])){
        jsonecho("Something went wrong during install (Code: 0BI),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='".$base_url0."/logs/LogViewer.php'>here</a><br>".$_response, 105);
    }

    //enable main (user) modules
    $api_url = $base_url . '/modules_manager/ajax/enable';
    $response = runCURL($api_url, array("module_id" => "user"));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error(user):" . json_encode($response['errors']), 105);
    else if(!isset($response['success'])){
        jsonecho("Something went wrong during install (Code: 0UE), \n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='".$base_url0."/logs/LogViewer.php'>here</a>", 105);
    }

    //enable all modules
    $api_url = $base_url . '/modules_manager/ajax/bulk_enable';
    $response = runCURL($api_url, array("modules" => json_encode($deployed_modules['result'])));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);


    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if(!isset($response['success'])){
        jsonecho("Something went wrong during install (Code: 0BE),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='".$base_url0."/logs/LogViewer.php'>here</a>", 105);
    }


    //upgrade all modules
    $api_url = $base_url . '/modules_manager/ajax/bulk_upgrade';
    $response = runCURL($api_url, array("modules" => json_encode($deployed_modules['result'])));
    $response = json_decode($response, JSON_OBJECT_AS_ARRAY);


    if (isset($response['success']) && $response['success'] == 0
        && isset($response['errors']) && !empty($response['errors']))
        jsonecho("Error: " . json_encode($response['errors']), 105);
    else if(!isset($response['success'])){
        jsonecho("Something went wrong during install (Code: 0BU),\n please check your Logs ([SERVER PATH]/application/logs), See logs file <a target='_blank' href='".$base_url0."/logs/LogViewer.php'>here</a>", 105);
    }

    //enable main (user) modules
    $api_url = $base_url . '/setting/ajax/update_version';
    $response = runCURL($api_url, array("settings" => $new_settings));

    jsonecho("DONE", 1);

