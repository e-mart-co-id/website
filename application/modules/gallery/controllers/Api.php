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
        //load model
        $this->load->model("gallery/gallery_model");
        $this->load->model("user/user_model");

    }

    public function getGallery(){


        $limit = intval($this->input->post("limit"));
        $page = intval($this->input->post("page"));
        $module_id = intval($this->input->post("module_id"));
        $module = intval($this->input->post("module"));



        $params = array(
            "limit"       =>$limit,
            "page"        =>$page,
            "module_id"      =>$module_id,
            "module"        =>$module,

        );

        $data =  $this->mGalleryModel->getGallery($params);

        if($data[Tags::SUCCESS]==1){
            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            echo Json::convertToJson($data[Tags::RESULT],  Tags::RESULT,TRUE,array(Tags::COUNT=>$data[Tags::COUNT]));
        }else{

            echo json_encode($data);
        }

    }


}

/* End of file UploaderDB.php */