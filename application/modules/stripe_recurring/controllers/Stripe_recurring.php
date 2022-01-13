<?php

class Stripe_recurring extends MAIN_Controller
{

    public function __construct()
    {
        parent::__construct();


        $this->init('stripe_recurring');

    }


    public function onLoad()
    {

        include APPPATH . 'modules/stripe_recurring/libraries/vendor/autoload.php';

        $this->load->model("stripe_recurring/stripe_recurring_model", "stripe_recurring_model");

    }

    public function onCommitted($isEnabled)
    {

        if (!$isEnabled)
            return;

        //setup payment
        if (ModulesChecker::isEnabled("payment")) {

            //display webook id
            TemplateManager::addScript($this->load->view('stripe_recurring/webhook_injection', NULL, TRUE));

            $payment_redirection = site_url("payment/make_payment");
            $payment_callback_success = site_url("stripe_recurring/complete");
            $payment_callback_error = site_url("stripe_recurring/error");

            $payments = array(
                array(
                    'id' => Stripe_recurring::STRIPE_RECURRING_ID,
                    'payment' => _lang("Stripe Recurring"),
                    'image' => TemplateManager::assets("payment", "img/stripe-logo.png"),
                    'description' => _lang('Pay subscription automatically using your Credit Card: Visa, MasterCard')
                ),
            );

            PaymentsProvider::replace("pack", $payments,
                $payment_redirection,
                $payment_callback_success,
                $payment_callback_error
            );

        }


        //process_payment the payment
        $this->process_payment();

        //inject view block into profile
        $this->setup_profile();

    }


    public function onUninstall()
    {
        return TRUE;
    }

    public function onEnable()
    {

        ConfigManager::setValue("STRIPE_WEBHOOK_ID", md5(time()), TRUE);
        ConfigManager::setValue("STRIPE_ENDPOINT_SECRET", "", TRUE);

        return parent::onEnable();
    }

    public function onInstall()
    {
        $this->stripe_recurring_model->create_table();
        return TRUE;
    }

    public function onUpgrade()
    {
        $this->stripe_recurring_model->create_table();
        return TRUE;
    }


    /*
     * Configure subscription
     */


    const  STRIPE_RECURRING_ID = 12;

    private function process_payment()
    {

        $p1 = $this->uri->segment(1);
        $p2 = $this->uri->segment(2);

        $pid = $this->input->get('mp');
        $invoice_id = intval($this->input->get('invoiceid'));

        if ($p1 == "payment" && $p2 == "process_payment"
            && $pid == Stripe_recurring::STRIPE_RECURRING_ID) {

            //then process the payment
            $this->create($invoice_id);


        }
    }

    private function create($invoice_id)
    {

        $invoice = $this->mPaymentModel->getInvoice($invoice_id);


        if ($this->stripe_recurring_model->hasSubscribe($invoice->user_id)) {
            $error = _lang("You are using the monthly payment plan. Cancel it if you want to change the package or change your payment method.");
            //redirect( site_url( "profile/index/package?error=".urlencode($error) ) );
            print_r($error);
            die();
        }

        if ($invoice == NULL) {
            redirect(site_url('payment/payment_error?invoiceid=' . $invoice_id));
        }

        if ($invoice->status == 1)
            redirect(admin_url());


        $key = md5(time());

        $this->session->set_userdata(array(
            'req_secret_key' => $key
        ));

        $callback = PaymentsProvider::getErrorCallback($invoice->module);

        $params = array(
            'currency' => $invoice->currency,
            'details_tax' => 0,
            'details_subtotal' => $invoice->amount,
            'callback_error_url' => $callback . '?invoiceid=' . $invoice_id,
        );

        $callback = PaymentsProvider::getSuccessCallback($invoice->module);

        $params['callback_success_url'] = $callback . '?invoiceid=' . $invoice_id . '&method=stripe&key=' . $key;

        $params['details_subtotal'] = 0;

        //adjust total
        $items = json_decode($invoice->items);
        foreach ($items as $k => $item) {
            $params['details_subtotal'] = $params['details_subtotal'] + ($item->price * $item->qty);
        }

        if (!TaxManager::isDisabled($invoice->module)) {

            if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0) {
                $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                if ($tax != NULL) {
                    $tax_value = (($tax['value'] / 100) * $params['details_subtotal']);
                    $params['details_subtotal'] = $params['details_subtotal'] + $tax_value;
                }
            } else if (defined('DEFAULT_TAX') and DEFAULT_TAX == -2) {
                if (defined('MULTI_TAXES') and count(MULTI_TAXES) > 0)
                    $litTaxes = json_decode(MULTI_TAXES, JSON_OBJECT_AS_ARRAY);
                $newAmount = $params['details_subtotal'];
                $multiTaxes = 0;
                foreach ($litTaxes as $value) {
                    $mTax = $this->mTaxModel->getTax($value);
                    if ($mTax != NULL) {
                        $tax_value = (($mTax['value'] / 100) * $params['details_subtotal']);
                        $multiTaxes = $multiTaxes + $tax_value;
                        $newAmount = $newAmount + $tax_value;
                    }
                }
                $params['details_subtotal'] = $newAmount;
            }
        }


        if (isset($params['details_subtotal'])) {
            $this->db->where('id', $invoice->id);
            $this->db->update('invoice', array(
                'amount' => $params['details_subtotal']
            ));
        }

        $items = json_decode($invoice->items, JSON_OBJECT_AS_ARRAY);
        $pack_id = $items[0]['item_id'];
        $pack_qty = $items[0]['qty'];
        $pack_months = $items[0]['months'];

        $pack = $this->mPack->getPack($pack_id);

        if (empty($pack))
            die("Pack undefined!");

        $user = $this->mUserModel->getUserData($invoice->user_id);

        if (empty($user))
            die("User undefined!");

        $config = array(
            'invoice_id' => $invoice_id,
            'pack_id' => $pack_id,
            'months' => ($pack_months * $pack_qty),
            'name' => $pack->name,
            'trial' => $pack->trial_period,
            'amount' => $params['details_subtotal'],
            'currency' => $invoice->currency,
            'user' => array(
                'uid' => $user['id_user'],
                'email' => $user['email'],
            ),
        );

        $this->configure($config, $params);


    }

//https://test.droideve.com/dealify-v1-0/index.php/stripe_recurring/payment_success?
//invoiceid=332&method=stripe&key=de8b528aeeec76594b8121a4d13d40eb


    public function configure($config, $params)
    {

        try {

            $stripe_secret_key = ConfigManager::getValue("STRIPE_SECRET_KEY");
            $stripe_publishable_key = ConfigManager::getValue("STRIPE_PUBLISHABLE_KEY");
            $stripe_m = ConfigManager::getValue("STRIPE_CONFIG_DEV_MODE");

            \Stripe\Stripe::setApiKey($stripe_secret_key);

            $customer_id = $this->session->userdata("stripe_customer_id");

            if (!$customer_id) {
                try {
                    $customer = \Stripe\Customer::create([
                        "email" => $config['user']['email'],
                        "metadata" => [
                            "user_id" => $config['user']['uid']
                        ]
                    ]);
                } catch (\Exception $e) {
                    echo "Couldn't create the new customer";
                    exit(0);
                }

                if (empty($customer->id)) {
                    echo "Couldn't create the new customer";
                    exit(0);
                }

                $customer_id = $customer->id;

                $customer_id = $this->session->set_userdata(array(
                    'stripe_customer_id' => $customer_id
                ));

            }

            if ($customer_id) {
                try {
                    $customer = \Stripe\Customer::retrieve($customer_id);
                } catch (\Exception $e) {
                    $customer_id = null;
                }

                if (!empty($customer->id)) {
                    $update = false;
                    if ($customer->email != $config['user']['email']) {
                        $customer->email = $config['user']['email'];
                        $update = true;
                    }

                    if (isset($customer->metadata->user_id) && $customer->metadata->user_id != $config['user']['uid']) {
                        $customer->metadata->user_id = $config['user']['uid'];
                        $update = true;
                    }

                    if ($update) {
                        $customer->save();
                    }
                }
            }

            $plan_id = $config['invoice_id']
                . "-" . $config['pack_id']
                . "-" . ($config['months'] == 1 ? "monthly" : "annualy")
                . "-" . $config['amount']
                . "-" . strtolower($config['currency']);

            try {
                $plan_result = \Stripe\Plan::retrieve($plan_id);
            } catch (\Exception $e) {
                $plan = null;
            }

            if (empty($plan_result)) {
                // Create new plan
                try {

                    // trial_period_days

                    $parameteres = array(
                        "id" => $plan_id,
                        "amount" => $config['amount'] * 100,
                        "interval" => ($config['months'] == 1 ? "month" : "year"),
                        "product" => [
                            "name" => $config['name']
                                . " - "
                                . ($config['months'] == 1 ? "monthly" : "annualy")
                        ],
                        "currency" => $config['currency']
                    );


                    if (isset($config['trial']) && $config['trial'] > 0) {

                        $data = $this->stripe_recurring_model->getUserParams($config['user']['uid']);

                        if ($data['trial_period_used'] == 0) {
                            $parameteres['trial_period_days'] = $config['trial'];
                        } else {
                            $config['trial'] = 0;
                        }

                    }

                    $plan_result = \Stripe\Plan::create($parameteres);


                } catch (\Exception $e) {
                    echo "#1 - ";
                    echo $e->getMessage();
                    exit(0);
                }
            }

            // Create subscription

            try {

                $sub_data = array(
                    'items' => [
                        ['plan' => $plan_id]
                    ]
                );

                if ($config['trial'] > 0) {
                    $sub_data['trial_period_days'] = intval($config['trial']);
                }

                $session = \Stripe\Checkout\Session::create([
                    'customer' => $customer_id,
                    'payment_method_types' => ['card'],
                    'subscription_data' => $sub_data,
                    "metadata" => [
                        "order_id" => $config['pack_id'],
                        "user_id" => $config['user']['uid']
                    ],
                    'success_url' => $params['callback_success_url'],
                    'cancel_url' => $params['callback_error_url'],
                ]);

                $config['plan'] = $plan_id;

                $this->session->set_userdata(array(
                    'stripe_recurring_check' => $session->id,
                    'stripe_recurring_config' => $config,
                ));

                $this->load->view("stripe_recurring/index", ["checkout_session_id" => $session->id]);

            } catch (\Exception $e) {
                echo "#2 - ";
                echo $e->getMessage();
                exit(0);
            }

        } catch (Exception $e) {
            echo $e->getMessage();
            exit(0);
        }
    }


    public function complete()
    {


        $stripe_recurring_config = $this->session->userdata("stripe_recurring_config");
        $stripe_recurring_check = $this->session->userdata("stripe_recurring_check");


        if (empty($stripe_recurring_config))
            die("Data undefined");

        if (empty($stripe_recurring_check))
            die("Payment error!");


        try {

            $stripe_secret_key = ConfigManager::getValue("STRIPE_SECRET_KEY");
            \Stripe\Stripe::setApiKey($stripe_secret_key);
            $payment = \Stripe\Checkout\Session::retrieve($stripe_recurring_check);

            if ($payment->customer) {

                $id = $this->stripe_recurring_model->createSubscription(array(
                    'subscription_id' => $payment->subscription,
                    'plan_id' => $stripe_recurring_config['plan'],
                    'customer_id' => $payment->customer,
                    'type' => "stripe_recurring",
                ));

                $this->session->set_userdata(array(
                    'stripe_recurring_check' => "",
                    'stripe_recurring_config' => ""
                ));

                //enable trial period if needed
                if (isset($stripe_recurring_config['trial'])
                    && $stripe_recurring_config['trial'] > 0) {
                    $this->stripe_recurring_model->enableTrial($payment->subscription);
                }

                redirect("stripe_recurring/success");

            } else {
                echo "UnSuccess!!";
            }


        } catch (Exception $e) {
            echo $e->getMessage();
            exit(0);
        }

    }

    public function success()
    {

        $data["title"] = Translate::sprint('Payment successful!');
        $this->load->view("stripe_recurring/client_view/html/success", $data);

    }

    public function error()
    {

        $data["title"] = Translate::sprint('Payment error!');
        $this->load->view("stripe_recurring/client_view/html/error", $data);

    }

    public function webhook()
    {

        $webhook_id = ConfigManager::getValue("STRIPE_WEBHOOK_ID");

        $id = $this->input->get('id');

        if($id!=$webhook_id){
            die("Webhook ID doesn't match");
        }


        $stripe_secret_key = ConfigManager::getValue("STRIPE_SECRET_KEY");
        \Stripe\Stripe::setApiKey($stripe_secret_key);

        $endpoint_secret = ConfigManager::getValue("STRIPE_ENDPOINT_SECRET");

        $payload = @file_get_contents('php://input');
        $sig_header = @$_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {

            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            print_r($e);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            print_r($e);
            exit();
        }

        switch ($event->type) {
            case "invoice.payment_succeeded": //Renew subscription
                $paymentIntent = $event->data->object;
                $this->whInvoicePaymentSucceeded($paymentIntent);
                break;
            case "customer.subscription.deleted": ////Desable subscription
                $paymentMethod = $event->data->object;
                $this->whSubscriptionDeleted($paymentMethod);
                break;
            default:
                break;
        }

        http_response_code(200);
    }

    private function whInvoicePaymentSucceeded($event)
    {

        if (empty($event->charge)) {
            echo "It couldn't handle the charge of this transaction";
            exit;
        }

        if (empty($event->subscription)) {
            http_response_code(400);
            exit;
        }

        $result = $this->stripe_recurring_model->updateSubscription(
            $event->subscription,
            array(
                'transaction_id' => $event->charge,
                'paid' => $event->amount_paid / 100
            )
        );

        //insert new subscription if there is no subscription
        if ($result==FALSE) {

            if(!isset($event->lines->data[0])){
                echo "#01020202";
               return;
            }

            //get plan ID
            $plan_id = $event->lines->data[0]->plan->id;

            $id = $this->stripe_recurring_model->createSubscription(array(
                'subscription_id' => $event->subscription,
                'plan_id' => $plan_id,
                'customer_id' => $event->customer,
                'type' => "stripe_recurring",
            ));

            if($id>0)
            $result = $this->stripe_recurring_model->updateSubscription(
                $event->subscription,
                array(
                    'transaction_id' => $event->charge,
                    'paid' => $event->amount_paid / 100
                )
            );

        }

        echo "Applied at $result => " . date("Y-m-d H:i:s", time());

    }

    public function trial_period_end()
    {

        $r = $this->input->get('r');
        $stripe_secret_key = ConfigManager::getValue("STRIPE_SECRET_KEY");
        \Stripe\Stripe::setApiKey($stripe_secret_key);

        \Stripe\Subscription::update($r, [
            'trial_end' => 'now',
        ]);

        echo "Applied at => " . date("Y-m-d H:i:s", time());

    }

    private function whSubscriptionDeleted($event)
    {

        try {

            \Stripe\Stripe::setApiKey(ConfigManager::getValue("STRIPE_SECRET_KEY"));

            $subscription = \Stripe\Subscription::retrieve(
                $event->id
            );
            $subscription->delete();

            $this->stripe_recurring_model->cancel_subscription($event->id);

            return true;
        } catch (Exception $e) {
            print_r($e);
            return false;
        }


    }

    private function setup_profile()
    {

        if (!SessionManager::isLogged())
            return;

        $uid = SessionManager::getData('id_user');

        if (!$this->stripe_recurring_model->hasSubscribe($uid))
            return;

        CMS_Display::replace("user_config_v1", "stripe_recurring/plug/profile_block", NULL);

    }

    public function cron()
    {
       /* //check upcoming billing
        $next_payments = $this->stripe_recurring_model->getExpiredTrial();

        $stripe_secret_key = ConfigManager::getValue("STRIPE_SECRET_KEY");
        \Stripe\Stripe::setApiKey($stripe_secret_key);

        foreach ($next_payments as $p) {
            \Stripe\Subscription::update($p['subscription_id'], [
                'trial_end' => 'now',
            ]);
        }*/
    }

}