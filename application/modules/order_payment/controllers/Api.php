<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {


    public function __construct()
    {
        parent::__construct();


    }

    public function get_payment_link()
    {

        $payment_method = $this->input->post('payment');

        //order to invoice
        $order_id = $this->input->post('order_id');
        $user_id = $this->input->post('user_id');
        $user_token = $this->input->post('user_token');


        $result = $this->mOrderPayment->convert_order_to_invoice($user_id,$order_id);

        if($result[Tags::SUCCESS]==1 && $result[Tags::RESULT]>0){

            if(TokenSetting::isValid($user_id,"logged",$user_token)){

                $token = TokenSetting::getValid($user_id,"logged",$user_token);
                if($token!=NULL){
                    $this->mUserBrowser->refreshData($token->uid);
                }
            }

            //process_payment
            $payment_link = site_url("payment/process_payment?invoiceid=".$result[Tags::RESULT]."&mp=".$payment_method);
            $result[Tags::RESULT] = $payment_link;
        }else{
            $result[Tags::RESULT] = "";
        }

        echo json_encode($result);

    }



    public function getPayments(){

        $payments = PaymentsProvider::getPayments("order_payment");

        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$payments),JSON_FORCE_OBJECT);return;

   }

}