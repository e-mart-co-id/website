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

    }


    public function save()
    {


       $user_id = trim($this->input->post("user_id"));
        $module_id = Security::decrypt($this->input->post("module_id"));
        $module = Security::decrypt($this->input->post("module"));


        $this->load->module('bookmark');

        $params = array(
            'user_id' => $user_id,
            'module_id' => $module_id,
            'module' => $module,
        );


        if($user_id>0){
            $params['guest_id'] = $this->mUserModel->getGuestIDByUserId($user_id);
        }


        if (!BookmarkManager::exist($params))
            $data["first_time"] = 1;
        else {
            $data["first_time"] = 0;
        }

        $data = BookmarkManager::add($params);

        echo json_encode($data);return;


    }


    function remove($id=0){

        $id = intval($this->input->post("id"));
        $user_id = intval($this->input->post("user_id"));

        $this->db->where('id',intval($id));
        $this->db->where('user_id',intval($user_id));
        $this->db->delete('bookmarks');

        echo json_encode(array(Tags::SUCCESS=> 1));return;
    }


    public function register_modules(){

        //event
        BookmarkLinkedModule::newInstance('store','getData',function ($args){

            $params = array(
                "store_id" => $args['id'],
                "limit" => 1,
            );

            $stores =  $this->mStoreModel->getStores($params);

            if(isset($stores[Tags::RESULT][0])){

                return array(
                    'label' => $stores[Tags::RESULT][0]['name'],
                    'label_description' => $stores[Tags::RESULT][0]['detail'],
                    'image' => $stores[Tags::RESULT][0]['images'],
                );
            }

            return NULL;
        });

        //store
        BookmarkLinkedModule::newInstance('event','getData',function ($args){

            $params = array(
                "event_id" => $args['id'],
                "limit" => 1,
            );

            $stores =  $this->mEventModel->getEvents($params);

            if(isset($stores[Tags::RESULT][0])){

                return array(
                    'label' => $stores[Tags::RESULT][0]['name'],
                    'label_description' => $stores[Tags::RESULT][0]['description'],
                    'image' => $stores[Tags::RESULT][0]['images'],
                );
            }

            return NULL;
        });
    }

    public function getBookmarks(){


        //register modules
       $this->register_modules();

        $limit = intval($this->input->post("limit"));
        $page = intval($this->input->post("page"));
        $device_date = $this->input->post("date");
        $device_timzone = $this->input->post("timezone");
        $module = $this->input->post("module");

        $user_id = $this->input->post("user_id");
        $guest_id = $this->input->post("guest_id");

        $params = array(
            "limit"             =>      $limit,
            "page"              =>      $page,
            "module"            =>      $module,
            "user_id"           =>      $user_id,
            "guest_id"          =>      $guest_id,
            "device_date"       =>      $device_date,
            "device_timezone"   =>      $device_timzone,
        );


       // print_r($params);

        $data =  $this->mBookmarkModel->getList($params);

        if($data[Tags::COUNT]>0){

            foreach ($data[Tags::RESULT] as $k => $bkm){

                $callback = BookmarkLinkedModule::find($bkm['module'],'getData');

                if($callback != NULL){

                    $params = array(
                        'id' => $bkm['module_id']
                    );

                    $result = call_user_func($callback,$params);

                    if($result != NULL){
                        $data[Tags::RESULT][$k]['label'] = $result['label'];
                        $data[Tags::RESULT][$k]['label_description'] = $result['label_description'];
                        $data[Tags::RESULT][$k]['image'] = $result['image'];
                    }else{
                        $data[Tags::RESULT][$k]['label'] = "";
                        $data[Tags::RESULT][$k]['label_description'] = "";
                        $data[Tags::RESULT][$k]['image'] = "";
                    }


                }else{
                    $data[Tags::RESULT][$k]['label'] = "";
                    $data[Tags::RESULT][$k]['label_description'] = "";
                    $data[Tags::RESULT][$k]['image'] = "";
                }

            }

        }



        if($data[Tags::SUCCESS]==1){
            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            echo Json::convertToJson($data[Tags::RESULT],  Tags::RESULT,TRUE,array(Tags::COUNT=>$data[Tags::COUNT]));
        }else{

            echo json_encode($data);
        }

    }




}

/* End of file UploaderDB.php */