<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Offer_model extends CI_Model
{


    function __construct()
    {
        parent::__construct();

    }


    public function getUnverifiedOffersCount()
    {

        $this->db->where('verified', 0);
        $this->db->where('hidden', 0);
        $this->db->where('product_type', "percent");
        return $this->db->count_all_results("product");

    }


    public function campaign_input($args)
    {
        return $this->mProductModel->campaign_input($args);
    }

    public function campaign_output($campaign = array())
    {

        return $this->mProductModel->campaign_output($campaign);

    }

    public function getDefaultCurrencyCode()
    {
        return $this->mProductModel->getDefaultCurrencyCode();
    }


    public function getOffersAnalytics($months = array(), $owner_id = 0)
    {
        $analytics = array();

        foreach ($months as $key => $m) {

            $last_month = date("Y-m-t", strtotime($key));
            $start_month = date("Y-m-1", strtotime($key));

            $this->db->where("created_at >=", $start_month);
            $this->db->where("created_at <=", $last_month);

            $this->db->where('hidden', 0);
            $this->db->where('is_offer', 1);


            if ($owner_id > 0)
                $this->db->where('user_id', $owner_id);

            $count = $this->db->count_all_results("product");
            $analytics['months'][$key] = $count;

        }

        if ($owner_id > 0)
            $this->db->where('user_id', $owner_id);

        $this->db->where('hidden', 0);
        $this->db->where('is_offer', 1);

        $analytics['count'] = $this->db->count_all_results("product");

        $analytics['count_label'] = _lang("Total_offers");
        $analytics['color'] = "red";
        $analytics['icon_tag'] = "<i class=\"mdi mdi-sale\"></i>";
        $analytics['label'] =  _lang("Offer");
        $analytics['link'] = admin_url("offer/all_offers");


        return $analytics;
    }


    public function markAsFeatured($params = array())
    {

        return $this->mProductModel->markAsFeatured($params);
    }

    public function switchTo($old_owner = 0, $new_owner = 0)
    {

        return $this->mProductModel->switchTo($old_owner, $new_owner);
    }

    public function editOffersCurrency()
    {
        return $this->mProductModel->editProductsCurrency();
    }

    public function getCurrencyByCode($code)
    {

        return $this->mProductModel->getCurrencyByCode();
    }

    public function getDefaultCurrency()
    {
        return $this->mProductModel->getDefaultCurrency();
    }


    public function getMyAllOffers($params = array())
    {

        return $this->mProductModel->getMyAllProducts($params);
    }


    public function getOffers($params = array(), $whereArray = array(), $callback = NULL, $resultCallback = NULL)
    {

        $whereArray['is_offer'] = 1;

        if (isset($params['offer_id'])) {
            $params['product_id'] = intval($params['offer_id']);
        }

        $offers = $this->mProductModel->getProducts($params, $whereArray, $callback, $resultCallback);

        foreach ($offers[Tags::RESULT] as $key => $value) {

            if ($this->isSaved("offer", $value['id_product']))
                $offers[Tags::RESULT][$key]['saved'] = "1";
            else
                $offers[Tags::RESULT][$key]['saved'] = "0";

        }

        $object = ActionsManager::return_action("offer", "func_getOffers", $offers);
        if ($object != NULL)
            $offers = $object;


        return $offers;
    }


    private function isSaved($module, $module_id)
    {

        $user_id = Security::decrypt($this->input->get_request_header('Session-User-Id', 0));
        $guest_id = Security::decrypt($this->input->get_request_header('Session-Guest-Id', 0));

        if ($user_id > 0 && $guest_id > 0) {

            $this->db->where("module", $module);
            $this->db->where("module_id", $module_id);
            $this->db->where("(user_id = $user_id AND guest_id = $guest_id)", NULL, TRUE);
            $c = $this->db->count_all_results("bookmarks");

            if ($c > 0)
                return TRUE;

        } else if ($user_id > 0 && $guest_id == 0) {

            $this->db->where("module", $module);
            $this->db->where("module_id", $module_id);
            $this->db->where("(user_id = $user_id)", NULL, TRUE);

            $c = $this->db->count_all_results("bookmarks");

            if ($c > 0)
                return TRUE;

        } else if ($user_id == 0 && $guest_id > 0) {

            $this->db->where("module", $module);
            $this->db->where("module_id", $module_id);
            $this->db->where("(guest_id = $guest_id)", NULL, TRUE);
            $c = $this->db->count_all_results("bookmarks");

            if ($c > 0)
                return TRUE;


        }

        return FALSE;
    }

    public function addOffer($params = array())
    {

        $params['product_type'] = "percent";
        $params['order_enabled'] = 0;
        $params['order_cf_id'] = 0;
        $params['button_template'] = "";
        $params['stock'] = -1;
        $params['qty_value'] = -1;

        $result = $this->mProductModel->addProduct($params);

        if ($result[Tags::SUCCESS] == 1
            && isset($result[Tags::RESULT])
            && $result[Tags::RESULT] > 0) {

            $offer_id = intval($result[Tags::RESULT]);
            $this->db->where('id_product', $offer_id);
            $this->db->update('product', array(
                'is_offer' => 1,
                'parent_id' => 0
            ));

            //apply discount for products

            if (isset($params['percent'])
                && is_numeric($params['percent'])
                && count($params['products'])) {

                $discount = doubleval($params['percent']);
                $products = $this->apply_discount_to_products($params['products'], $discount);

                foreach ($products as $p) {
                    $this->db->where('id_product', $p->id_product);
                    $this->db->update('product', array(
                        'is_offer' => 0,
                        'parent_id' => $offer_id
                    ));
                }

            }

        }

        return $result;
    }


    private function apply_discount_to_products($products, $discount)
    {

        //then apply discount as a new discount
        $this->db->where_in('id_product', $products);
        $products = $this->db->get('product');
        $products = $products->result();

        foreach ($products as $value) {

            $original = $value->product_value;

            if ($discount < 0) {
                $discount = $discount * -1;
            }

            $d = $discount / 100;
            $cal = $original * $d;

            $discounted = $original - $cal;

            //check if there is no discount
            $this->db->where('original_value', 0);
            $this->db->where('id_product', $value->id_product);
            $count = $this->db->count_all_results('product');

            if ($count > 0) {

                $this->db->where('id_product', $value->id_product);
                $this->db->update('product', array(
                    'product_value' => $discounted,
                    'original_value' => $original,
                ));

            }

        }

        return $products;
    }


    public function changeStatus($params = array())
    {

        $errors = array();
        $data = array();
        extract($params);

        if (isset($offer_id) and $offer_id > 0) {

            $this->db->where("id_product", intval($offer_id));
            $product = $this->db->get("product", 1);
            $product = $product->result();

            if (count($product) > 0) {

                $status = $product[0]->status;

                if ($status == 1) {

                    $this->reset_discount_product($product[0]->id_product);

                    $this->db->where("id_product", intval($offer_id));
                    $this->db->update("product", array(
                        "status" => 0
                    ));
                } else {
                    $this->db->where("id_product", intval($offer_id));
                    $this->db->update("product", array(
                        "status" => 1
                    ));
                }

            }

        }
        return array(Tags::SUCCESS => 1);
    }


    private function reset_discount_product($product_id)
    {

        //restore price for all related products
        $this->db->query("UPDATE product SET product_value = original_value , original_value = 0 , parent_id = 0  WHERE parent_id = " . $product_id);

        /*$this->db->where('original_value !=', 0);
        $this->db->where('parent_id', $product_id);
        $products = $this->db->get('product', 1);
        $product = $products->result();

        if (!isset($product[0]))
            return;

        $this->db->where('original_value !=', 0);
        $this->db->where('parent_id', $product_id);
        $this->db->update('product', array(
            'product_value' => $product[0]->original_value,
            'original_value' => 0,
            'parent_id' => 0,
        ));*/

    }

    public function hideOffersOutOfDate()
    {
        $this->db->select("date_end,id_product");
        $this->db->where("status", 1);
        $this->db->where("is_deal", 1);
        $offers = $this->db->get("product");
        $offers = $offers->result_array();

        if (count($offers) > 0) {
            $currentDate = date("Y-m-d H:i:s", time());
            foreach ($offers as $value) {
                if (strtotime($value["date_end"]) < strtotime($currentDate)) {
                    $this->db->where("id_product", $value["id_product"]);
                    $this->db->update("product", array(
                        "status" => 0));
                }
            }
            return array(Tags::SUCCESS => 1);
        } else {
            return array(Tags::SUCCESS => 0);
        }
    }



    public function editOffer($params = array())
    {

        $params['product_type'] = "percent";
        $params['order_enabled'] = 0;
        $params['order_cf_id'] = 0;
        $params['button_template'] = "";
        $params['stock'] = -1;
        $params['qty_value'] = -1;

        $result = $this->mProductModel->editProduct($params);

        if ($result[Tags::SUCCESS] == 1
            && isset($result[Tags::RESULT]) && $result[Tags::RESULT] > 0) {

            $offer_id = intval($result[Tags::RESULT]);

            $this->db->where('id_product', $offer_id);
            $this->db->update('product', array(
                'is_offer' => 1
            ));

            //reset discount
            $this->reset_discount_product($offer_id);


            //apply discount for products
            if (isset($params['percent'])
                && is_numeric($params['percent'])
                && count($params['products'])) {

                $discount = doubleval($params['percent']);
                $products = $this->apply_discount_to_products($params['products'], $discount);

                foreach ($products as $p) {
                    $this->db->where('id_product', $p->id_product);
                    $this->db->update('product', array(
                        'is_offer' => 0,
                        'parent_id' => $offer_id
                    ));
                }

            }

        }

        return $result;
    }


    public function deleteOffer($params = array())
    {

        if (isset($params['offer_id']) && $params['offer_id'] > 0)
            $params['product_id'] = intval($params['offer_id']);

        $result = $this->mProductModel->deleteProduct($params);

        if (isset($result[Tags::SUCCESS]) && $result[Tags::SUCCESS] == 1) {
            $this->reset_discount_product(intval($params['offer_id']));
        }

        return $result;
    }

    public function getLinkedProducts($product_id)
    {

        $this->db->where('parent_id', $product_id);
        $products = $this->db->get('product');
        $products = $products->result_array();

        return $products;

    }

    public function verify($id, $accept)
    {
        return $this->mProductModel->verify($id, $accept);
    }

    public function updateFields()
    {


        if (!$this->db->field_exists('is_offer', 'product')) {
            $fields = array(
                'is_offer' => array('type' => 'INT', 'default' => '0'),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('product', $fields);
        }

        if (!$this->db->field_exists('is_offer', 'product')) {
            $fields = array(
                'is_offer' => array('type' => 'INT', 'default' => '0'),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('product', $fields);
        }


    }

    public function emigration_1_6()
    {

        $offers = $this->db->get('product');
        $offers = $offers->result_array();

        $cf = $this->mCFManager->getByName("Default_Delivery_Checkout_fields");

        if ($cf != NULL)
            $cf_id = $cf['id'];
        else
            $cf_id = 0;

        foreach ($offers as $offer) {

            if(isset($offer['product_type']) && $offer['product_type'] == "price"){

                $this->db->where('id_product', $offer['id_product']);
                $this->db->update('product', array(
                    'is_offer' => 0,
                    'order_enabled' => 1,
                    'cf_id' => $cf_id,
                ));

            } else {

                $this->db->where('id_product', $offer['id_product']);
                $this->db->update('product', array(
                    'is_offer' => 1,
                    'order_enabled' => 0,
                    'cf_id' => 0,
                ));

            }

        }


    }

}