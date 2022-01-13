<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {

    public function __construct()
    {
        parent::__construct();
    }


    public function getVariants(){

        $limit = intval($this->input->post("limit"));
        $page = intval($this->input->post("page"));
        $product_id = intval($this->input->post("product_id"));

        $result = $this->mProduct_variants->getGroupedList(
            $product_id
        );

        echo json_encode(
            array(
                Tags::SUCCESS=>1,
                Tags::RESULT=>$result
            )
        ,JSON_FORCE_OBJECT);

    }



}