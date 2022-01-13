<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();


    }



    public function cancelSubscription(){

        if(!SessionManager::isLogged())
            return;

        $uid = SessionManager::getData('id_user');

        if(!$this->stripe_recurring_model->hasSubscribe($uid))
            return;

        $subscription = $this->stripe_recurring_model->getSubscription($uid);

        if(!isset($subscription[0]))
            return;

        try {

            $stripe_secret_key = ConfigManager::getValue("STRIPE_SECRET_KEY");
            \Stripe\Stripe::setApiKey($stripe_secret_key);

            $object = \Stripe\Subscription::retrieve($subscription[0]->subscription_id);
            $object->delete();

            //cancel subscription
            $this->stripe_recurring_model->cancel_subscription(
                $subscription[0]->subscription_id
            );

            echo  json_encode(array(Tags::SUCCESS=>1));
            return;
        } catch (Exception $e) {
            echo  json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>$e->getMessage())));
            return;
        }

    }


}