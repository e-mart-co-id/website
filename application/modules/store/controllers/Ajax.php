<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model("user/user_browser", "mUserBrowser");
        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("store/store_model", "mStoreModel");

    }

    public function saveConfig(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('setting',CHANGE_APP_SETTING)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $OPENING_TIME_ENABLED = $this->input->post("OPENING_TIME_ENABLED");

        ConfigManager::setValue("OPENING_TIME_ENABLED",$OPENING_TIME_ENABLED);

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }

    public function cf_categories_edit(){

        if(!GroupAccess::isGranted('store',MANAGE_STORES)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $cat_id = intval($this->input->post("cat_id"));
        $cf_id = intval($this->input->post("cf_id"));

        $this->db->where("id_category",$cat_id);
        $this->db->update("category",array(
            "cf_id"=>intval($cf_id)
        ));

        echo json_encode(array(Tags::SUCCESS=>1));

    }

    public function getStoresAjax2(){

        $params = array(
            "limit"   => $this->input->get('limit'),
            "search"  => $this->input->get('search'),
            "page"  => $this->input->get('page'),
            "status"  => 1
        );


        $data = $this->mStoreModel->getStores($params);

        echo json_encode($data,JSON_OBJECT_AS_ARRAY);return;
    }


    public function getStoresAjax()
    {

        $params = array(
            "limit" => 5,
            "search" => $this->input->get('search'),
            "status" => 1
        );

        if($this->mUserBrowser->getData("manager") != 1)
        $params["user_id"] = $this->mUserBrowser->getData('id_user');


        $data = $this->mStoreModel->getStores($params);


        $result = array();

        if (isset($data[Tags::RESULT]))
            foreach ($data[Tags::RESULT] as $object) {

                $o = array(
                    'text' => Text::output($object['name']),
                    'id' => $object['id_store'],

                    'title' => Text::output($object['name']),
                    'description' => strip_tags(Text::output($object['detail'])),
                    'image' => ImageManagerUtils::getFirstImage($object['images']),
                );

                if (strlen($o['description']) > 100) {
                    $o['description'] = substr(strip_tags(Text::output($o['description'])), 0, 100) . ' ...';
                }

                $result['results'][] = $o;


            }

        echo json_encode($result, JSON_OBJECT_AS_ARRAY);
        return;
    }

    public function markAsFeatured()
    {

        //check if user have permission
        $this->enableDemoMode();


        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if ($this->mUserBrowser->isLogged()) {

            $user_id = $this->mUserBrowser->getData("user_id");

            $id = intval($this->input->post("id"));
            $featured = intval($this->input->post("featured"));

            echo json_encode(
                $this->mStoreModel->markAsFeatured(array(
                    "user_id" => $user_id,
                    "id" => $id,
                    "featured" => $featured

                ))
            );
            return;

        }

        echo json_encode(array(Tags::SUCCESS => 0));
    }

    public function delete()
    {

        if (!GroupAccess::isGranted('store', DELETE_STORE)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $id_store = intval($this->input->post("id"));

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            $user_id = $this->mUserBrowser->getData('id_user');
        } else {
            $user_id = 0;
        }

        echo json_encode(
            $this->mStoreModel->delete($id_store, $user_id)
        );
        return;
    }

    public function deleteReview()
    {
        //check if user have permission
        $this->enableDemoMode();
        $id = intval($this->input->post("id"));
        echo json_encode(
            $this->mStoreModel->deleteReview($id)
        );
        return;
    }

    public function changeOwnership(){

        $id_store = intval($this->input->post("id"));
        $owner_id = intval($this->input->post("owner_id"));

        $data = $this->mStoreModel->changeOwnership(array(
            "store_id" => $id_store,
            "owner_id"     => $owner_id,
        ));

        echo json_encode($data);

    }

    public function edit()
    {

        if (!GroupAccess::isGranted('store', EDIT_STORE)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $times = $this->input->post("times");
        $id_store = intval(($this->input->post("id")));
        $name = $this->input->post("name");
        $address = $this->input->post("address");
        $detail = $this->input->post("detail");
        $tel = $this->input->post("tel");
        $user = intval($this->input->post("id_user"));
        $category = intval($this->input->post("cat"));
        $lat = doubleval($this->input->post("lat"));
        $lng = doubleval($this->input->post("lng"));
        $images = $this->input->post("images");
        $gallery = $this->input->post("gallery");
        $canChat = $this->input->post("canChat");
        $website = $this->input->post("website");
        $video_url = $this->input->post("video_url");
        $country = $this->input->post("country");
        $city = $this->input->post("city");
        $owner_id = $this->input->post("owner_id");

        $order_based_on_op = $this->input->post("order_based_on_op");
        $order_enabled = $this->input->post("order_enabled");


        $params = array(
            "store_id" => $id_store,
            "name" => $name,
            "address" => $address,
            "detail" => $detail,
            "tel" => $tel,
            "user_id" => $this->mUserBrowser->getData("id_user"),
            "category" => $category,
            "latitude" => $lat,
            "longitude" => $lng,
            "images"    => $images,
            "gallery"    => $gallery,
            "times"     => $times,
            "video_url" => $video_url,
            "timezone"  => $this->mUserBrowser->getData("user_timezone"),
            "typeAuth"  => $this->mUserBrowser->getData("typeAuth"),
            "country"  => $country,
            "city"  => $city,
            "canChat" => $canChat,
            "owner_id"     => $owner_id,
            "order_enabled" => $order_enabled,
            "order_based_on_op" => $order_based_on_op,
            "website" => $website,
        );

        //print_r($params); die();

        $data = $this->mStoreModel->updateStore($params);


        echo json_encode($data);


    }

    public function createStore()
    {

        if (!GroupAccess::isGranted('store', ADD_STORE)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $times = $this->input->post("times");
        $name = $this->input->post("name");
        $address = $this->input->post("address");
        $detail = $this->input->post("detail");
        $tel = $this->input->post("tel");
        $user = Security::decrypt($this->input->post("id_user"));
        $category = intval($this->input->post("cat"));
        $lat = doubleval($this->input->post("lat"));
        $lng = doubleval($this->input->post("lng"));
        $images = $this->input->post("images");
        $gallery = $this->input->post("gallery");
        $canChat = $this->input->post("canChat");

        $website = $this->input->post("website");
        $video_url = $this->input->post("video_url");
        $country = $this->input->post("country");
        $city = $this->input->post("city");


        $order_based_on_op = $this->input->post("order_based_on_op");
        $order_enabled = $this->input->post("order_enabled");


        $data = $this->mStoreModel->createStore(array(
            "name" => $name,
            "address" => $address,
            "detail" => $detail,
            "phone" => $tel,
            "video_url" => $video_url,
            "user_id" => $this->mUserBrowser->getData("id_user"),
            "category" => $category,
            "latitude" => $lat,
            "longitude" => $lng,
            "images" => $images,
            "gallery" => $gallery,
            "times" => $times,
            "timezone" => $this->mUserBrowser->getData("user_timezone"),
            "typeAuth" => $this->mUserBrowser->getData("typeAuth"),
            "canChat" => $canChat,
            "order_enabled" => $order_enabled,
            "order_based_on_op" => $order_based_on_op,
            "website" => $website,
            "country"  => $country,
            "city"  => $city,
        ));

        echo json_encode($data);
    }

    public function verify()
    {
        //$this->enableDemoMode();

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        if (!$this->mUserBrowser->isLogged()) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }



        $id = $this->input->get('id');
        $accept = $this->input->get('accept');

        if($this->mStoreModel->verify($id,$accept)){
            echo json_encode(array(Tags::SUCCESS=>1));return;
        }else{
            echo json_encode(array(Tags::SUCCESS=>0));return;
        }

    }



    public function status(){

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $params = array( "id" => intval($this->input->get("id")));

            $data = $this->mStoreModel->storeAccess($params);

            echo json_encode($data);
            exit();

        }

    }


}