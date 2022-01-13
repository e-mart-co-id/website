<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_variants extends MAIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->init('product_variants');
    }

    public function onLoad()
    {

        $this->load->model("product_variants/Product_variants_model", "mProduct_variants");

    }

    public function onCommitted($isEnabled)
    {

        /*ActionsManager::register("product", "duplicate_product", function ($args) {
            //todo  get the params and $args["product_id"];


        });*/


    }


    public function plug($params = array())
    {

        $data['var'] = "result_" . rand(9999, 10000);
        $data['id'] = $params['id'];

        if (isset($params['label']))
            $data['label'] = $params['label'];

        if (isset($params['title']))
            $data['title'] = $params['title'];

        return array(
            'html' => $this->load->view('product_variants/plug/html', $data, TRUE),
            'script' => $this->load->view('product_variants/plug/script', $data, TRUE),
            'var' => $data['var'],
        );

    }


    public function onInstall()
    {

        $this->mProduct_variants->createTable();
        $this->mProduct_variants->updateFields();

        return TRUE;

    }

    public function onUpgrade()
    {

        $this->mProduct_variants->createTable();
        $this->mProduct_variants->updateFields();

        return TRUE;
    }

    public function onEnable()
    {
        return TRUE;
    }


}