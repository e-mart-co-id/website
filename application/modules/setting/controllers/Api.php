<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Api extends API_Controller {

    public function __construct(){
        parent::__construct();

    }


    public function app_initialization(){
        echo json_encode(array(Tags::SUCCESS=>1,"token"=>CRYPTO_KEY));
    }


    public function getAppConfig()
    {
        $data = $this->mConfigModel->getAppConfig();
        echo json_encode(array(Tags::SUCCESS => 1, Tags::RESULT => $data), JSON_FORCE_OBJECT);

    }



    public function save_logs(){

        $key = $this->input->post('key');
        $value = $this->input->post('message');
        $platform = $this->input->post('platform');

        if(file_exists('logs/'.$platform.'.html')){
            $file_content = @url_get_content('logs/'.$platform.'.html');
        }else
            $file_content = "";

        $file_content = date("Y-m-d H:i:s")." --> <span style='color: #00a65a'>$key</span> --> <span style='color: red'>".$value."</span><br><br>".$file_content."<br><br>";

        @file_put_contents('logs/'.$platform.'.html', $file_content);

        echo json_encode(array(Tags::SUCCESS=>1));return;
    }

}

/* End of file SettingDB.php */