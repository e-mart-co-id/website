<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller  {

    public function __construct()
    {
        parent::__construct();


    }

    public function re_order_list(){

        $result = $this->mProduct_variants->re_order_list(array(
            'product_id'=> $this->input->post('product_id'),
            'user_id'=> SessionManager::getData('id_user'),
            'list'=> $this->input->post('list'),
        ));

        echo json_encode($result);return;

    }


    public function removeVariant(){

        $result = $this->mProduct_variants->removeVariant(array(
            'variant_id'=> $this->input->post('variant_id'),
            'user_id'=> SessionManager::getData('id_user'),
        ));

        echo json_encode($result);return;

    }

    public function createGroup(){

        $result = $this->mProduct_variants->createGrp(array(
            'user_id'=> SessionManager::getData('id_user'),
            'product_id'=> $this->input->post('product_id'),
            'label'=> $this->input->post('label'),
            'option_type'=> $this->input->post('option_type'),
        ));

        if($result[Tags::SUCCESS]==1){
            $data['grp'] = $result[Tags::RESULT];
            echo json_encode(array(
                Tags::SUCCESS=>1,
                Tags::RESULT=> $this->load->view('product_variants/plug/options/group_row',$data,TRUE)
            ));
        }else
            echo json_encode($result);


    }


    public function createOption(){

        $result = $this->mProduct_variants->createOption(array(
            'user_id'=> SessionManager::getData('id_user'),
            'product_id'=> $this->input->post('product_id'),
            'variant_id'=> $this->input->post('variant_id'),
            'option_price'=> $this->input->post('option_price'),
            'option_name'=> $this->input->post('option_name'),
        ));

        if($result[Tags::SUCCESS]==1){
            $data['opt'] = $result[Tags::RESULT];
            echo json_encode(array(
                Tags::SUCCESS=>1,
                Tags::RESULT=> $this->load->view('product_variants/plug/options/option_row',$data,TRUE)
            ));
        }else
            echo json_encode($result);


    }




}