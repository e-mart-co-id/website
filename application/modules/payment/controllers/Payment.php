<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

require_once(APPPATH . 'modules/payment/libraries/paypal-php-sdk/autoload.php');


class Payment extends MAIN_Controller
{

    public $_api_context;

    const MODULE = "payment";
    const PAID_SUCCESS = "p_success";
    const PAID_FAILED = "p_failed";

    public function __construct()
    {
        parent::__construct();

        /////// register module ///////
        $this->init("payment");

    }

    public function onLoad()
    {
        define('CONFIG_PAYMENT', 'config_payment');
        define('DISPLAY_LIST_TRANSACTIONS', 'display_transactions');
        define('DISPLAY_LIST_BILLING', 'display_billing');
        define('MANAGE_TAXES', 'manage_taxes');


        //load model
        $this->load->model("payment/payment_model", 'mPaymentModel');
        $this->load->model("setting/currency_model", 'mCurrencyModel');
        $this->load->model("payment/tax_model", 'mTaxModel');
        $this->load->model("payment/wallet_model", 'mWalletModel');
        $this->load->helper("payment/payment");

    }


    public function onCommitted($isEnabled)
    {

        if (!$isEnabled)
            return;

        $this->load->config('config');

        //init config
        if (!defined('DEFAULT_TAX'))
            $this->mConfigModel->save('DEFAULT_TAX', 0);


        TemplateManager::registerMenu(
            'payment',
            "payment/menu",
            11
        );


        if ($this->mUserBrowser->isLogged()) {
            $user_id = $this->mUserBrowser->getData("id_user");
        }


        //setup payment for waller
        $payment_redirection = site_url("payment/make_payment");
        $payment_callback_success = site_url("payment/wallet_payment_confirm");
        $payment_callback_error = site_url("payment/payment_error");

        PaymentsProvider::provide("wallet", array(
            array(
                'id' => PaymentsProvider::PAYPAL_ID,
                'payment' => _lang("PayPal"),
                'image' => TemplateManager::assets("payment", "img/paypal-logo.png"),
                'description' => 'Pay using PayPal.com'
            ),
            array(
                'id'=> PaymentsProvider::STRIPE_ID,
                'payment'=>  _lang("Credit Card"),
                'image'=> TemplateManager::assets("payment","img/credit-card-logo.png"),
                'description'=>  _lang('Pay using your Credit Card: Visa, MasterCard')
            ),
            array(
                'id' => PaymentsProvider::RAZORPAY_ID,
                'payment' => _lang("Razorpay"),
                'image' => TemplateManager::assets("payment", "img/razorpay-logo.png"),
                'description' => _lang('Pay using razorpay.com')
            ),
           /* array(
                'id'=> PaymentsProvider::FLUTTERWAVE,
                'payment'=> _lang('Credit Card'),
                'image'=> TemplateManager::assets("payment","img/credit-card-logo.png"),
                'description'=> 'Pay using flutterwave.com'
            ),*/
        ),
            $payment_redirection,
            $payment_callback_success,
            $payment_callback_error
        );

        TaxManager::disable('wallet');

        TemplateManager::addScript($this->load->view('payment/plug/header/header_script', NULL, TRUE));


    }

    public function onEnable()
    {
        $this->registerModuleActions();
        $this->mPaymentModel->setup_config();
    }

    public function onInstall()
    {

        $this->mWalletModel->createTables();

        $this->mPaymentModel->createTables();
        $this->mPaymentModel->update_fields();
        $this->mPaymentModel->setup_config();

        return TRUE;
    }

    public function onUpgrade()
    {
        $this->mWalletModel->createTables();

        $this->mPaymentModel->createTables();
        $this->mPaymentModel->update_fields();
        $this->mPaymentModel->setup_config();

        return TRUE;
    }

    private function registerModuleActions()
    {

        GroupAccess::registerActions("payment", array(
            CONFIG_PAYMENT,
            DISPLAY_LIST_TRANSACTIONS,
            DISPLAY_LIST_BILLING,
            MANAGE_TAXES,
        ));

    }


    public function make_payment()
    {


        $css = TemplateManager::assets("payment", 'css/style.css');
        TemplateManager::addCssLibs($css);

        $id = $this->input->get("id");
        $data['invoice'] = $this->mPaymentModel->getInvoice($id);
        $data['title'] = Translate::sprint("Payment");

        $data['cancel_url'] = admin_url();

        if ($data['invoice'] != NULL) {
            $this->load->view("payment/client_view/html/make-payment", $data);
        } else
            redirect(admin_url());

    }

    public function process_payment()
    {

        $id = intval($this->input->get("invoiceid"));

        $method_payment = intval($this->input->get("mp"));
        $invoice = $this->mPaymentModel->getInvoice($id);

        if ($invoice == NULL) {
            redirect(site_url('payment/payment_error?invoiceid=' . $id));
        }


        if ($invoice->status == 1){
            redirect("error404");
        }


        $key = md5("abc-key" . $id);

        $callback = PaymentsProvider::getErrorCallback($invoice->module);
       
        $params = array(
            'currency' => $invoice->currency,
            'details_tax' => $invoice->tax,
            'details_subtotal' => $invoice->amount,
            'callback_error_url' => $callback . '?invoiceid=' . $id,
        );


        $callback = PaymentsProvider::getSuccessCallback($invoice->module);

        if ($method_payment == 1)
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method=paypal&key=' . $key;
        else if ($method_payment == 2)
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method=stripe&key=' . $key;


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

        $extras = json_decode($invoice->extras, JSON_OBJECT_AS_ARRAY);
        if ($extras != null && is_array($extras)) {
            foreach ($extras as $k => $value) {
                $params['extras'][$k] = doubleval($value);
                $params['details_subtotal'] = $params['details_subtotal'] + doubleval($value);
            }
        }


        if (isset($params['details_subtotal'])) {
            $this->db->where('id', $invoice->id);
            $this->db->update('invoice', array(
                'amount' => $params['details_subtotal']
            ));
        }
        if ($method_payment == PaymentsProvider::TRANSFER_ID) {
            $this->mPaymentModel->updateInvoiceCOD($invoice->id, "TF");
            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $callback = $callback . '?invoiceid=' . $id . '&method=TF&key=' . $key;

            $result = Modules::run($invoice->module . '/payment_success', array(
                'invoiceId' => $invoice->id
            ));
            //if ($result == TRUE) {
            redirect($callback);
            //} else {
            //    $callback_error = PaymentsProvider::getErrorCallback($invoice->module);
            //    redirect($callback_error);
            //}
        }
        else if ($method_payment == PaymentsProvider::PAYPAL_ID) { //paypal

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;

            }


            Modules::run('payment/paypal/create_payment_with_paypal', $params);

        } else if ($method_payment == PaymentsProvider::STRIPE_ID) { //stripe

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;
            }

            $this->session->set_userdata(array(
                'payment_stripe_cart' => $params
            ));

            $this->load->view("payment/stripe/charge");

        } else if ($method_payment == PaymentsProvider::COD_ID) {

            $this->mPaymentModel->updateInvoiceCOD($invoice->id, "cod");

            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $callback = $callback . '?invoiceid=' . $id . '&method=cod&key=' . $key;

            $result = Modules::run($invoice->module . '/payment_success', array(
                'invoiceId' => $invoice->id
            ));

            if ($result == TRUE) {
                redirect($callback);
            } else {
                $callback_error = PaymentsProvider::getErrorCallback($invoice->module);
                redirect($callback_error);
            }

        } else if ($method_payment == PaymentsProvider::WALLET_ID) {

            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $callback = $callback . '?invoiceid=' . $id . '&method=wallet&key=' . $key;

            $result = $this->mWalletModel->releaseBalance(
                SessionManager::getData("id_user"),
                $invoice->amount
            );

            if ($result == TRUE) {
                $this->mPaymentModel->updateInvoice($invoice->id, "wallet", "w" . date("Y-m-d/h:i:s-A", time()), $key);
                redirect($callback);
            } else {
                $callback_error = PaymentsProvider::getErrorCallback($invoice->module);
                redirect($callback_error);
            }

        } else if ($method_payment == PaymentsProvider::RAZORPAY_ID) { //stripe

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;
            }

            $this->session->set_userdata(array(
                'payment_razorpay_cart' => $params
            ));


            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $callback = $callback . '?invoiceid=' . $id . '&method=razorpay&key=' . $key;
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method=razorpay&key=' . $key;

            echo Modules::run("payment/razorpay/create_order", $params);


        } else if ($method_payment == PaymentsProvider::FLUTTERWAVE) { //FLUTTERWAVE

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;
            }

            $this->session->set_userdata(array(
                'payment_flutterwave_cart' => $params
            ));



            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $callback = $callback . '?invoiceid=' . $id . '&method=flutterwave&key=' . $key;
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method=flutterwave&key=' . $key;


            $_SESSION['payable_amount'] = $params['details_subtotal'];
            $_SESSION['callback_success_url'] = $params['callback_success_url'];
            $_SESSION['callback_error_url'] = $params['callback_error_url'];


            $this->load->view("payment/flutterwave/charge",$params);

        }

    }


    public function payment_success()
    {

        $id = intval($this->input->get("invoiceid"));
        $method = Text::input($this->input->get("method"));
        $transaction = Text::input($this->input->get("paymentId"));
        $key = Text::input($this->input->get("key"));

        $payerID = Text::input($this->input->get("PayerID"));
        $paymentId = Text::input($this->input->get("paymentId"));
        $token = Text::input($this->input->get("token"));


        if ($method == "paypal") {

            $params = array(
                'paymentId' => $paymentId,
                'payerID' => $payerID,
                'token' => $token
            );

            $result = Modules::run('payment/paypal/getPaymentStatus', $params);


            if ($result == 1) {
                $data["invoiceid"] = $id;
                $data["title"] = Translate::sprint('Payment successful');
                $this->load->view("payment/client_view/html/success", $data);
                $this->mPaymentModel->updateInvoice($id, $method, $transaction, $key);

            } else {
                $this->payment_error();
            }

        } else if ($method == "stripe") {

            $data["title"] = Translate::sprint('Payment successful');
            $this->load->view("payment/client_view/html/success", $data);
            $this->mPaymentModel->updateInvoice($id, $method, $transaction, $key);

        } else if ($method == "wallet") {

            $data["title"] = Translate::sprint('Payment successful');
            $this->load->view("payment/client_view/html/success", $data);


        } else if ($method == "razorpay") {

            $data["title"] = Translate::sprint('Payment successful');
            $this->load->view("payment/client_view/html/success", $data);

            $this->mPaymentModel->updateInvoice($id, $method, $transaction, $key);

        } else if ($method == "flutterwave") {

            $data["title"] = Translate::sprint('Payment successful');
            $this->load->view("payment/client_view/html/success", $data);

            $this->mPaymentModel->updateInvoice($id, $method, $transaction, $key);

        } else {

            $data["title"] = Translate::sprint('Payment successful');
            $this->load->view("payment/client_view/html/success", $data);

        }


    }

    public function payment_error()
    {

        $id = intval($this->input->get("invoiceid"));
        $data["title"] = Translate::sprint('Payment with error');
        $data["invoiceid"] = $id;
        $this->load->view("payment/client_view/html/error", $data);

    }


    public function wallet_payment_confirm()
    {

        $invoiceid = $this->input->get('invoiceid');
        $method = $this->input->get('method');
        $key = $this->input->get('key');
        $transaction = Text::input($this->input->get("paymentId"));

        $user_id = SessionManager::getData("id_user");

        $this->db->where("user_id", $user_id);
        $this->db->where("module", "wallet");
        $this->db->where("id", intval($invoiceid));

        $invoice = $this->db->get('invoice', 1);
        $invoice = $invoice->result();

        if ($method == "paypal" && isset($invoice[0])) {

            $payerID = Text::input($this->input->get("PayerID"));
            $paymentId = Text::input($this->input->get("paymentId"));
            $token = Text::input($this->input->get("token"));

            $params = array(
                'paymentId' => $paymentId,
                'payerID' => $payerID,
                'token' => $token
            );

            $result = Modules::run('payment/paypal/getPaymentStatus', $params);

            if ($result == 1) {

                $data["invoiceid"] = $invoiceid;

                $data["title"] = Translate::sprint('Payment successful');
                $this->load->view("payment/client_view/html/success", $data);

                //add balance
                $this->mWalletModel->add_Balance(SessionManager::getData('id_user'), $invoice[0]->amount);

                $this->mPaymentModel->updateInvoice($invoiceid, $method, $transaction, $key);

            } else {
                $this->payment_error();
            }

        } else if (isset($invoice[0])) {

            $data["title"] = Translate::sprint('Payment successful');
            $this->load->view("payment/client_view/html/success", $data);

            $this->mPaymentModel->updateInvoice(intval($invoiceid), $method, $transaction, $key);

            //add balance
            $this->mWalletModel->add_Balance(SessionManager::getData('id_user'), $invoice[0]->amount);

        }

        return FALSE;
    }
}
