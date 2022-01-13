<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller  {

    public function __construct()
    {
        parent::__construct();

        $this->load->model("product/product_model","mProductModel");
        $this->load->model("store/store_model","mStoreModel");
        $this->load->model("user/user_model","mUserModel");
        $this->load->model("user/user_browser","mUserBrowser");
    }

    public function getProductsAjax(){

        $params = array(
            "limit"   => 5,
            "store_id" => $this->input->get('store_id'),
            "search"  => $this->input->get('search'),
            "user_id"  => $this->mUserBrowser->getData('id_user'),
            "status"  => 1
        );

        $data = $this->mProductModel->getProducts($params,array(
            'is_offer' => 0
        ));

        $result = array();

        if(isset($data[Tags::RESULT]))
            foreach ($data[Tags::RESULT] as $object){


                $o = array(
                    'text' =>  $object['name'].' ('.$object['store_name'].')',
                    'id' =>  $object['id_product'],

                    'title' =>  $object['name'],
                    'description' =>  strip_tags(Text::output($object['description'])),
                    'image' =>  ImageManagerUtils::getFirstImage( $object['images']),
                );

                if(strlen($o['description'])>100){
                    $o['description'] = substr(strip_tags(Text::output($o['description'])),0,100).' ...';
                }

                $result['results'][] = $o;

            }

        echo json_encode($result,JSON_OBJECT_AS_ARRAY);return;
    }

    public function markAsFeatured(){

        //check if user have permission
        $this->enableDemoMode();

        if(!GroupAccess::isGranted('product',MANAGE_PRODUCTS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $user_id = $this->mUserBrowser->getData("user_id");

            $id   = intval($this->input->post("id"));
            $featured   = intval($this->input->post("featured"));

            echo json_encode(
                $this->mProductModel->markAsFeatured(array(
                    "user_id"  => $user_id,
                    "id" => $id,
                    "featured" => $featured

                ))
            );
            return;

        }

        echo json_encode(array(Tags::SUCCESS=>0));
    }

    public function delete(){

        if(!GroupAccess::isGranted('product',DELETE_PRODUCT)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $data = $this->mProductModel->deleteProduct(
                array( "product_id" => intval($this->input->post("id")))
            );

            echo json_encode($data);

        }else{
            echo json_encode(array(Tags::SUCCESS=>0));
        }

    }

    public function changeStatus(){

        if(!GroupAccess::isGranted('product',MANAGE_PRODUCTS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $data = $this->mProductModel->changeStatus(
                array( "product_id" => intval($this->input->get("id")))
            );

            echo json_encode($data);
            exit();

        }

    }

    public function verify()
    {
        //$this->enableDemoMode();

        if (!GroupAccess::isGranted('product', MANAGE_PRODUCTS)) {
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

        if($this->mProductModel->verify($id,$accept)){
            echo json_encode(array(Tags::SUCCESS=>1));return;
        }else{
            echo json_encode(array(Tags::SUCCESS=>0));return;
        }

    }


    public function duplicate()
    {

        if ($this->mUserBrowser->isLogged()) {

            $user_id = $this->mUserBrowser->getData("id_user");
            $product_id = $this->input->get("id");

            $data = $this->mProductModel->duplicate(array("product_id" => $product_id, "user_id" => $user_id));

            if ($data[Tags::SUCCESS] == 1) {
                echo json_encode($data);
            }

        } else {
            echo json_encode(array(Tags::SUCCESS => 0));
        }

    }


    public function add(){


        if(!GroupAccess::isGranted('product',ADD_PRODUCT)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $store_id = $this->input->post("store_id");
        $description =  $this->input->post("description",FALSE);
        $price =  $this->input->post("price");
        $percent =  $this->input->post("percent");
        $date_start =  $this->input->post("date_start");
        $date_end =  $this->input->post("date_end");
        $name =  $this->input->post("name",FALSE);
        $user_id =  intval($this->mUserBrowser->getData("id_user"));
        $authType =  ($this->mUserBrowser->getData("typeAuth"));
        $images =  $this->input->post("images");
        $currency =  $this->input->post("currency");
        $order_cf_id =  $this->input->post("order_cf_id");
        $button_template =  $this->input->post("button_template");
        $stock =  $this->input->post("stock");
        $qty_value =  $this->input->post("qty_value");


        $params = array(
            "product_type" => "product",
            "store_id" => $store_id,
            "description" => $description,
            "price" => $price,
            "percent" => $percent,
            "date_start" => $date_start,
            "date_end" => $date_end,
            "user_id" => $user_id,
            "user_type" => $authType,
            "name" => $name,
            "images" => $images,
            "currency"=> $currency,
            "is_deal"=> 0,
            "order_enabled"=> 1,
            "order_cf_id"=> $order_cf_id,

            "button_template"=> $button_template,
            "stock"=> $stock,
            "qty_value"=> $qty_value,
            "typeAuth"  => $this->mUserBrowser->getData("typeAuth")
        );

        echo json_encode(
            $this->mProductModel->addProduct($params)
        );return;

    }


    public function edit(){

        if(!GroupAccess::isGranted('product',EDIT_PRODUCT)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $store_id = $this->input->post("store_id");
        $product_id = $this->input->post("product_id");
        $description =  $this->input->post("description",FALSE);
        $price =  $this->input->post("price");
        $percent =  $this->input->post("percent");
        $name =  $this->input->post("name",FALSE);
        $user_id =  intval($this->mUserBrowser->getData("id_user"));
        $images =  $this->input->post("images");

        $currency =  $this->input->post("currency");
        $date_end =  $this->input->post("date_end");
        $date_start =  $this->input->post("date_start");

        $order_cf_id =  $this->input->post("order_cf_id");
        $button_template =  $this->input->post("button_template");

        $stock =  $this->input->post("stock");
        $qty_value =  $this->input->post("qty_value");
        
        $params = array(

            "store_id" => $store_id,
            "product_id" => $product_id,
            "description" => $description,
            "price" => $price,
            "date_end" => $date_end,
            "date_start" => $date_start,
            "percent" => $percent,
            "user_id" => $user_id,
            "images" => $images,
            "name" => $name,
            "currency"=> $currency,

            "is_deal"=> 0,
            "order_enabled"=> 1,

            "order_cf_id"=> $order_cf_id,
            "button_template"=> $button_template,
            "stock"=> $stock,
            "qty_value"=> $qty_value,

        );



        echo  json_encode(
            $this->mProductModel->editProduct($params)
        );

    }




}