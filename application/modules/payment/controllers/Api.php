<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {


    public function __construct()
    {
        parent::__construct();


    }


   public function getPayments(){

        $module = $this->input->post('module');

   }

}