<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller  {

    public function __construct()
    {
        parent::__construct();

        $this->load->model("offer/offer_model","mOfferModel");
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
        ),function ($params){
            $this->db->where('product.original_value',0);
        });

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


    public function getOffersAjax(){

        $params = array(
            "limit"   => 5,
            "store_id" => $this->input->get('store_id'),
            "search"  => $this->input->get('search'),
            "user_id"  => $this->mUserBrowser->getData('id_user'),
            "status"  => 1
        );

        $data = $this->mOfferModel->getOffers($params);

        $result = array();

        if(isset($data[Tags::RESULT]))
            foreach ($data[Tags::RESULT] as $object){

                $o = array(
                    'text' =>  Text::output($object['name']).' ('.Text::output($object['store_name']).')',
                    'id' =>  $object['id_product'],

                    'title' =>  Text::output($object['name']),
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

        if(!GroupAccess::isGranted('offer',MANAGE_OFFERS)){
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
                $this->mOfferModel->markAsFeatured(array(
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

        if(!GroupAccess::isGranted('offer',DELETE_OFFER)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $data = $this->mOfferModel->deleteOffer(
                array( "offer_id" => intval($this->input->post("id")))
            );

            echo json_encode($data);

        }else{
            echo json_encode(array(Tags::SUCCESS=>0));
        }

    }

    public function changeStatus(){

        if(!GroupAccess::isGranted('offer',MANAGE_OFFERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $data = $this->mOfferModel->changeStatus(
                array( "offer_id" => intval($this->input->get("id")))
            );

            echo json_encode($data);
            exit();

        }

    }



    public function add(){


        if(!GroupAccess::isGranted('offer',ADD_OFFER)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $store_id = $this->input->post("store_id");
        $description =  $this->input->post("description",FALSE);
        $percent =  $this->input->post("percent");
        $date_start =  $this->input->post("date_start");
        $date_end =  $this->input->post("date_end");
        $name =  $this->input->post("name",FALSE);
        $user_id =  intval($this->mUserBrowser->getData("id_user"));
        $authType =  ($this->mUserBrowser->getData("typeAuth"));
        $images =  $this->input->post("images");
        $currency =  $this->input->post("currency");
        $is_deal =  $this->input->post("is_deal");
        $products =  $this->input->post("products");


        $params = array(
            "product_type" => "offer",
            "store_id" => $store_id,
            "description" => $description,
            "percent" => $percent,
            "date_start" => $date_start,
            "date_end" => $date_end,
            "user_id" => $user_id,
            "user_type" => $authType,
            "name" => $name,
            "images" => $images,
            "currency"=> $currency,
            "is_deal"=> $is_deal,
            "products"=> $products,
        );

        echo json_encode(
            $this->mOfferModel->addOffer($params)
        );return;

    }


    public function edit(){

        if(!GroupAccess::isGranted('offer',EDIT_OFFER)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $store_id = $this->input->post("store_id");
        $offer_id = $this->input->post("offer_id");

        $description =  $this->input->post("description",FALSE);
        $percent =  $this->input->post("percent");
        $date_start =  $this->input->post("date_start");
        $date_end =  $this->input->post("date_end");
        $name =  $this->input->post("name",FALSE);
        $user_id =  intval($this->mUserBrowser->getData("id_user"));
        $images =  $this->input->post("images");
        $currency =  $this->input->post("currency");
        $is_deal =  $this->input->post("is_deal");
        $products =  $this->input->post("products");

        $params = array(

            "product_type" => "offer",
            "product_id" => $offer_id,
            "store_id" => $store_id,
            "description" => $description,
            "percent" => $percent,
            "date_start" => $date_start,
            "date_end" => $date_end,
            "user_id" => $user_id,
            "name" => $name,
            "images" => $images,
            "currency"=> $currency,
            "is_deal"=> $is_deal,
            "products"=> $products,
        );

        echo  json_encode(
            $this->mOfferModel->editOffer($params)
        );

    }


    public function verify()
    {

        if (!GroupAccess::isGranted('offer',MANAGE_OFFERS))
            redirect("error?page=permission");

        $id = intval($this->input->get('id'));
        $accept = intval($this->input->get('accept'));

        $this->mOfferModel->verify($id,$accept);

        echo json_encode(array(Tags::SUCCESS => 1));
        return;

    }


}