<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends CI_Model
{

    private $limit = 10;

    public $button_templates = array(
        'order' => "Order now",
        'book' => "Book now",
        'get' => "Get now",
        'subscribe' => "Subscribe",
    );

    function __construct()
    {
        parent::__construct();
        define('MAX_CHARS_PRODUCTS_DESC', 2000);


        $this->load->model("setting/config_model", 'mConfigModel');
        if (!defined('PRODUCTS_IN_DATE'))
            $this->mConfigModel->save('PRODUCTS_IN_DATE', false);

    }

    public function update_product_currency($currency)
    {
        $this->db->update('product', array(
            'currency' => $currency
        ));
    }


    public function getUnverifiedProductsCount()
    {

        $this->db->where('verified', 0);
        $this->db->where('hidden', 0);
        $this->db->where('product_type', "price");
        return $this->db->count_all_results("product");

    }


    public function campaign_input($args)
    {


        $params = array(
            'limit' => LIMIT_PUSHED_GUESTS_PER_CAMPAIGN,
            'order' => 'last_activity',
        );

        //get store
        $this->db->select("store_id");
        $this->db->where("id_product", $args['module_id']);
        $this->db->where("user_id", $args['user_id']);

        if ($args['module_name'] == "offer") {
            $this->db->where("is_offer", 1);
        } else {
            $this->db->where("is_offer", 0);
        }

        $obj = $this->db->get("product", 1);
        $obj = $obj->result();

        if (count($obj) > 0) {
            $params['__module'] = "store";
            $params['__module_id'] = $obj[0]->store_id;
        }


        //custom parameter for option order by random guest or distance
        if (isset($args['custom_parameters']['getting_option'])
            && $args['custom_parameters']['getting_option'] == 1) {//

        } else if (isset($args['custom_parameters']['getting_option'])
            && $args['custom_parameters']['getting_option'] == 2) { //get guests by distance


            if (count($obj) > 0) {

                $store_id = $obj[0]->store_id;
                $this->db->select("latitude,longitude");
                $this->db->where("id_store", $store_id);
                $obj = $this->db->get("store", 1);
                $obj = $obj->result();

                if (count($obj) > 0) {
                    $params['lat'] = $obj[0]->latitude;
                    $params['lng'] = $obj[0]->longitude;
                }

            }

        } else if (isset($args['custom_parameters']['getting_option'])
            && $args['custom_parameters']['getting_option'] == 3) { //get guest by random and last_activity


        }


        //custom parameter for platforms
        if (isset($args['custom_parameters']['platforms'])
            && !empty($args['custom_parameters']['platforms'])) {

            foreach ($args['custom_parameters']['platforms'] as $key => $value) {
                if ($value == 1) {
                    $params['custom_parameter_platform'][] = $key;
                }
            }

            if (empty($params['custom_parameter_platform']))
                $params['custom_parameter_platform'][] = "unspecified";

        }


        $this->load->model("User/mUserModel");
        $data = $this->mUserModel->getGuests($params, function ($params) {

            if (ModulesChecker::isEnabled("bookmark") && _NOTIFICATION_AGREEMENT_USE == TRUE) {

                $this->db->select('guest_id');


                $this->db->where("module", $params['__module']);
                $this->db->where("module_id", $params['__module_id']);
                $this->db->where('notification_agreement', 1);
                $this->db->where('guest_id !=', "");
                $guests = $this->db->get('bookmarks');
                $guests = $guests->result_array();

                $ids = array(0);

                foreach ($guests as $g) {
                    $ids[] = $g['guest_id'];
                }

                if (!empty($ids))
                    $this->db->where_in('id', $ids);

            }

            if (isset($params['custom_parameter_platform'])
                && !empty($params['custom_parameter_platform'])) {
                $this->db->where_in('platform', $params['custom_parameter_platform']);
            }

        });


        return $data;
    }

    public function campaign_output($campaign = array())
    {

        $type = $campaign['module_name'];
        $module_id = $campaign['module_id'];

        $this->db->where("id_product", $module_id);
        $this->db->where("status", 1);
        $product = $this->db->get("product", 1);
        $product = $product->result_array();

        if (count($product) > 0) {

            $str_id = $product[0]['store_id'];

            $this->db->where("id_store", $str_id);
            $this->db->where("status", 1);
            $obj = $this->db->get("store", 1);
            $obj = $obj->result_array();

            if (count($obj) > 0) {

                $data['title'] = Text::output($campaign['name']);
                $data['sub-title'] = Text::output($campaign['text']);
                //$data['sub-title'] = Text::output($product[0]['name']);
                $data['id'] = $module_id;
                $data['type'] = $type;

                $content = json_decode($product[0]["content"], JSON_OBJECT_AS_ARRAY);
                $content['currency'] = DEFAULT_CURRENCY;
                $content['attachment'] = ImageManagerUtils::getImage($product[0]['images']);
                $content['store_name'] = $obj[0]['name'];


                $data['body'] = $content;
                $data['image'] = $content['attachment'];

                $imgJson = json_decode($product[0]['images'], JSON_OBJECT_AS_ARRAY);
                $data['image_id'] = $imgJson[0];

                return $data;
            }

        }


        return NULL;

    }

    public function getDefaultCurrencyCode()
    {
        return DEFAULT_CURRENCY;
    }


    public function getProductsAnalytics($months = array(), $owner_id = 0)
    {

        $analytics = array();

        foreach ($months as $key => $m) {

            $last_month = date("Y-m-t", strtotime($key));
            $start_month = date("Y-m-1", strtotime($key));

            $this->db->where("created_at >=", $start_month);
            $this->db->where("created_at <=", $last_month);

            if ($owner_id > 0)
                $this->db->where('user_id', $owner_id);

            $this->db->where('hidden', 0);
            $this->db->where('is_offer', 0);


            $count = $this->db->count_all_results("product");

            // $index = date("m", strtotime($start_month));

            $analytics['months'][$key] = $count;

        }

        if ($owner_id > 0)
            $this->db->where('user_id', $owner_id);

            $this->db->where('hidden', 0);
            $this->db->where('is_offer', 0);

        $analytics['count'] = $this->db->where("hidden", 0)->count_all_results("product");

        $analytics['count_label'] = _lang("Total_products");
        $analytics['color'] = "#009dff";
        $analytics['icon_tag'] = "<i class=\"mdi mdi-package-variant-closed\"></i>";
        $analytics['label'] = _lang("Product");
        $analytics['link'] = admin_url("product/all_products");


        return $analytics;

    }


    public function markAsFeatured($params = array())
    {

        extract($params);


        if (!isset($type) and !isset($id) and !isset($featured))
            return array(Tags::SUCCESS => 0);


        $this->db->where("id_product", $id);
        $this->db->update("product", array(
            "featured" => intval($featured)
        ));

        return array(Tags::SUCCESS => 1);
    }

    public function switchTo($old_owner = 0, $new_owner = 0)
    {

        if ($new_owner > 0) {

            $this->db->where("id_user", $new_owner);
            $c = $this->db->count_all_results("user");
            if ($c > 0) {

                $this->db->where("user_id", $old_owner);
                $this->db->update("product", array(
                    "user_id" => $new_owner
                ));

                return TRUE;
            }

        }

        return FALSE;
    }

    public function editProductsCurrency()
    {

        $this->db->select("content,id_product");
        $products = $this->db->get("product");
        $products = $products->result_array();

        foreach ($products as $value) {

            $content = $value['content'];

            if (!is_array($content))
                $content = json_decode($content, JSON_OBJECT_AS_ARRAY);

            print_r($content);

            if (isset($content['currency']['code'])) {

                $currencyObject = $this->getCurrencyByCode($content['currency']['code']);

                $content = json_encode(array(
                    "description" => $content['description'],
                    "price" => $content['price'],
                    "percent" => $content['percent'],
                    "currency" => $currencyObject
                ), JSON_FORCE_OBJECT);


                $this->db->where("id_product", $value['id_product']);
                $this->db->update("product", array(
                    "content" => $content
                ));

            }

        }


    }

    public function getCurrencyByCode($code)
    {

        $currencies = json_decode(CURRENCIES, JSON_OBJECT_AS_ARRAY);

        if (isset($currencies[$code])) {
            return $currencies[$code];
        }

        return $this->getDefaultCurrency();
    }

    public function getDefaultCurrency()
    {

        $currencies = json_decode(CURRENCIES, JSON_OBJECT_AS_ARRAY);
        $d = DEFAULT_CURRENCY;
        foreach ($currencies as $key => $value) {
            if ($key == $d) {
                return $value;
            }
        }

        return;
    }


    public function changeStatus($params = array())
    {

        $errors = array();
        $data = array();
        extract($params);

        if (isset($product_id) and $product_id > 0) {

            $this->db->where("id_product", intval($product_id));
            $product = $this->db->get("product", 1);
            $product = $product->result();

            if (count($product) > 0) {

                $status = $product[0]->status;

                if ($status == 1) {

                    $this->db->where("id_product", intval($product_id));
                    $this->db->update("product", array(
                        "status" => 0
                    ));
                } else {
                    $this->db->where("id_product", intval($product_id));
                    $this->db->update("product", array(
                        "status" => 1
                    ));
                }

            }

        }

        return array(Tags::SUCCESS => 1);
    }

    public function getMyAllProducts($params = array())
    {

        $errors = array();
        $data = array();

        extract($params);

        if (isset($user_id) and $user_id > 0) {

            $this->db->where("status", 1);
            $this->db->where("user_id", intval($user_id));
            $this->db->order_by("id_product", "DESC");
            $data = $this->db->get("product");
            $data = $data->result_array();

            return array(Tags::SUCCESS => 1, Tags::RESULT => $data);
        }

        return array(Tags::SUCCESS => 0);
    }

    public function getProducts($params = array(), $whereArray = array(), $callback = NULL, $resultCallback = NULL)
    {

        extract($params);
        $errors = array();
        $data = array();


        if (!isset($page)) {
            $page = 1;
        }

        if (!isset($limit)) {
            $limit = NO_OF_ITEMS_PER_PAGE;
        }

        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);

        $this->db->where('product.hidden', 0);
        $this->db->where('store.hidden', 0);

        if (isset($is_featured))
            $this->db->where('product.featured', $is_featured);


        if (isset($search) and $search != "") {
            $this->db->group_start();
            $this->db->like('product.name', $search);
            $this->db->or_like('store.name', $search);
            $this->db->or_like('store.address', $search);
            $this->db->or_like('product.description', $search);
            $this->db->group_end();
        }


        if (isset($price_min) and $price_min > 0) {
            $this->db->where('product.product_value >= ', intval($price_min));
        }

        if (isset($price_max) and $price_max > 0) {
            $this->db->where('product.product_value <= ', intval($price_max));
        }

        if (isset($store_id) and $store_id > 0) {
            $data ['product.store_id'] = intval($store_id);
        }

        if (isset($product_type) and $product_type == 'price') {
            $data ['product.product_type'] = 'price';
        } else if (isset($product_type) and $product_type == 'percent') {
            $data ['product.product_type'] = 'percent';
        }

        if (isset($product_type) and $product_type != 0) {
            $data ['product.product_type'] = doubleval($product_type);
        }

        if (isset($product_id) and $product_id > 0) {
            $data ['product.id_product'] = intval($product_id);
        }


        if (isset($product_type) and $product_type != "") {
            $data ['product.product_type'] = ($product_type);
        }


        if (isset($date_end) and $date_end != "" and Text::validateDate($date_end)) {
            $date_end = MyDateUtils::convert($date_end, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d");

            $this->db->where("product.date_end >=", $date_end);
        }


        if (isset($user_id) and $user_id > 0) {
            $this->db->where("store.user_id", intval($user_id));
        } else if (isset($is_super) and $is_super) {

        } else if (isset($statusM) and !empty($statusM)) {
            $this->db->where("product.status", $statusM);
        }

        if (isset($status) and !empty($filterBy)) {
            if ($status == 0) {
                $this->db->where("product.status", $status);
            } else if ($status == 1) {
                $current = date("Y-m-d H:i:s", time());
                //$current = MyDateUtils::convert($current, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d");
                $this->db->where("product.status", $status);
                if ($filterBy == "Published") {
                    $this->db->where("product.date_start > ", $current);
                } else if ($filterBy == "Started") {
                    $this->db->where("product.date_start < ", $current);
                    $this->db->where("product.date_end > ", $current);
                } else if ($filterBy == "Finished") {
                    $this->db->where("product.date_end > ", $current);
                }
            }
        }


        //distance
        $calcul_distance = "";
        if (
            isset($longitude)
            and
            isset($latitude)

        ) {

            $longitude = doubleval($longitude);
            $latitude = doubleval($latitude);


            $calcul_distance = " , IF( store.latitude = 0,99999,  (1000 * ( 6371 * acos (
                              cos ( radians(" . $latitude . ") )
                              * cos( radians( store.latitude ) )
                              * cos( radians( store.longitude ) - radians(" . $longitude . ") )
                              + sin ( radians(" . $latitude . ") )
                              * sin( radians( store.latitude ) )
                            )
                          ) ) ) as 'distance'  ";


        }


        if (isset($category_id) and $category_id > 0) {
            $this->db->where("store.category_id", $category_id);
        }


        $this->db->where($data);

        $this->db->join("store", "store.id_store=product.store_id");
        $this->db->join("user", "user.id_user=store.user_id");

        $count = $this->db->count_all_results("product");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        if ($count == 0)
            return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => array());

        $this->db->where('product.hidden', 0);
        $this->db->where('store.hidden', 0);


        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);

        if (isset($is_featured))
            $this->db->where('product.featured', $is_featured);

        if (isset($search) and $search != "") {
            $this->db->group_start();
            $this->db->like('product.name', $search);
            $this->db->or_like('store.name', $search);
            $this->db->or_like('store.address', $search);
            $this->db->or_like('product.description', $search);
            $this->db->group_end();
        }

        if (isset($price_min) and $price_min > 0) {
            $this->db->where('product.product_value >= ', intval($price_min));
        }

        if (isset($price_max) and $price_max > 0) {
            $this->db->where('product.product_value <= ', intval($price_max));
        }


        if (isset($store_id) and $store_id > 0) {
            $data ['product.store_id'] = intval($store_id);
        }


        if (isset($product_type) and $product_type != "") {
            $data ['product.product_type'] = ($product_type);
        }

        if (isset($product_id) and $product_id > 0) {
            $data ['product.id_product'] = intval($product_id);
        }

        if (isset($date_end) and $date_end != "" and Text::validateDate($date_end)) {
            $date_end = MyDateUtils::convert($date_end, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d");

            $this->db->where("product.date_end >=", $date_end);
        }

        if (isset($user_id) and $user_id > 0) {

            $this->db->where("store.user_id", intval($user_id));

        } else if (isset($is_super) and $is_super) {

        } else if (isset($statusM) and !empty($statusM)) {
            $this->db->where("product.status", $statusM);
        }

        // filter products by status
        if (isset($status) and !empty($filterBy)) {
            if ($status == 0) {
                $this->db->where("product.status", $status);
            } else if ($status == 1) {
                $current = date("Y-m-d H:i:s", time());
                //$current = MyDateUtils::convert($current, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d");
                $this->db->where("product.status", $status);
                if ($filterBy == "Published") {
                    $this->db->where("product.date_start > ", $current);
                } else if ($filterBy == "Started") {
                    $this->db->where("product.date_start < ", $current);
                    $this->db->where("product.date_end > ", $current);
                } else if ($filterBy == "Finished") {
                    $this->db->where("product.date_end < ", $current);
                }
            }
        }

        if (isset($category_id) and $category_id > 0) {
            $this->db->where("store.category_id", $category_id);
        }

        $this->db->join("store", "store.id_store=product.store_id");
        $this->db->join("user", "user.id_user=store.user_id");

        $this->db->select("product.*,store.config_order_enabled as 'store_order_enabled',store.config_order_based_op as 'store_order_based_on_op',store.latitude,store.longitude,store.name as 'store_name'" . $calcul_distance, FALSE);


        $this->db->where($data);
        $this->db->from("product");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());


        if (!empty($order_by) and $order_by != "") {
            if ($order_by == "recent")
                $this->db->order_by("product.created_at", "DESC");
            if ($order_by == "nearby")
                $this->db->order_by("distance", "ASC");
        } else {
            if ($calcul_distance == "")
                $this->db->order_by("product.id_product", "DESC");
            else
                $this->db->order_by("distance", "ASC");

        }


        if (isset($radius) and $radius > 0 && $calcul_distance != "")
            $this->db->having('distance <= ' . intval($radius), NULL, FALSE);

        $products = $this->db->get();
        $products = $products->result_array();


        if (count($products) < $limit) {
            $count = count($products);
        }

        foreach ($products as $key => $product) {

            if ($product['order_enabled'] > 0 && $product['cf_id'] > 0)
                $products[$key]['cf'] = $this->mCFManager->getList0($product['cf_id']);
            else
                $products[$key]['cf'] = array();


            $products[$key]['link'] = site_url("product/id/" . $product["id_product"]);
            $products[$key]['short_description'] = strip_tags(Text::output(Text::output($product['description'])));

            if (isset($product['images'])) {

                $images = (array)json_decode($product['images']);

                $products[$key]['images'] = array();
                // $new_stores_results[$key]['image'] = $store['images'];
                foreach ($images as $k => $v) {
                    $products[$key]['images'][] = _openDir($v);
                }

            } else {
                $products[$key]['images'] = array();
            }

            $products[$key]['currency'] = $this->mCurrencyModel->getCurrency($product['currency']);

        }

        $object = ActionsManager::return_action("product", "func_getProducts", $products);
        if ($object != NULL)
            $products = $object;

        if ($resultCallback != NULL)
            $products = call_user_func($resultCallback, $products);


        if ($calcul_distance != "" && $order_by != -2 && $order_by != -3) {
            $products = $this->re_order_featured_item($products);
        }


        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $products);
    }


    public function re_order_featured_item($data = array())
    {

        $new_data = array();

        foreach ($data as $key => $value) {
            if ($value['featured'] == 1) {
                $new_data[] = $data[$key];
                unset($data[$key]);
            }
        }


        foreach ($data as $value) {
            $new_data[] = $value;
        }

        /* usort($data,function($first, $second){
             return strtolower($first['featured']) < strtolower($second['featured']);
         });*/


        return $new_data;
    }

    public function dupplicate($product_id)
    {

        $this->db->where('id_product', $product_id);
        $product = $this->db->get('product', 1);
        $products = $product->result_array();

        foreach ($products as $key => $p) {
            unset($product[$key]['id_product']);
            $this->db->insert('product', $product[$key]);
        }

    }


    public function addProduct($params = array())
    {

        /*print_r($params);
        die();*/


        extract($params);


        $errors = array();
        $data = array();


        /*
         *  MANAGE PRODUCT IMAGES
         */
        if (isset($images) and !is_array($images))
            $images = json_decode($images, JSON_OBJECT_AS_ARRAY);

        if (!empty($images)) {
            $data["images"] = array();
            $i = 0;
            try {
                if (!empty($images)) {
                    foreach ($images as $value) {
                        $data["images"][$i] = $value;
                        $i++;
                    }
                    $data["images"] = json_encode($data["images"], JSON_FORCE_OBJECT);
                }
            } catch (Exception $e) {

            }

        }

        if (isset($data["images"]) and empty($data["images"])) {
            $errors['images'] = Translate::sprint("Please upload an image");
        }

        if (isset($store_id) and $store_id > 0) {
            $data['store_id'] = intval($store_id);
        } else {
            $errors['store_id'] = Translate::sprint(Messages::STORE_NOT_SPECIFIED);
        }


        if (isset($name) and $name != "") {
            $data['name'] = Text::input($name);
        } else {
            $errors['name'] = Translate::sprint("Product name is empty");
        }

        if (isset($description) and $description != "") {
            $data['description'] = Text::inputWithoutStripTags($description);
        } else {
            $errors['description'] = Translate::sprint(Messages::EVENT_DESCRIPTION_EMPTY);
        }

        //duplicate action
        if (isset($product_type) && isset($product_value)) {
            $data['product_value'] = $product_value;
            $data['product_type'] = $product_type;
        } else {
            if (isset($price) and doubleval($price) > 0) {
                $data['product_value'] = doubleval($price);
                $data['product_type'] = 'price';

                if (isset($currency) and $currency != "" and preg_match('#([a-zA-Z])#', $currency)) {
                    $data['currency'] = $currency;
                } else {
                    $data['currency'] = DEFAULT_CURRENCY;
                }

            } else if (isset($percent) and (intval($percent) > 0 || intval($percent) < 0)) {
                $data['product_value'] = doubleval($percent);
                $data['product_type'] = 'percent';
            } else {
                //Create a product with a non specified value type : e.g promo , free offre ...etc
                $data['product_type'] = 'unspecified';
                $data['product_value'] = 0;
            }
        }


        if (isset($currency) and $currency != "" and preg_match('#([a-zA-Z])#', $currency)) {
            $data['currency'] = $currency;
        } else {
            $data['currency'] = DEFAULT_CURRENCY;
        }

        if (isset($is_deal) && $is_deal == 1) {
            if (isset($date_start) and Text::validateDate($date_start)) {
                $data['date_start'] = $date_start;//
            } else {
                $errors['date_start'] = Translate::sprint(Messages::DATE_BEGIN_NOT_VALID);
            }

            if (isset($date_end) and Text::validateDate($date_end)) {
                $data['date_end'] = $date_end;
            } else {
                $errors['date_end'] = Text::_print("Date of end is not valid!");
            }

            $data['is_deal'] = intval($is_deal);
        }


        if (isset($user_id) and $user_id > 0) {
            $data['user_id'] = $user_id;
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_FOUND);
        }

        if (isset($stock) and $stock >= 0) {
            //$data['qty_enabled'] = intval($stock);
            $data['stock'] = intval($stock);
        } else {
            $data['stock'] = -1; //stock should be unlimited
        }

        if (isset($data['stock']) && isset($qty_value) && $qty_value >= 0) {
            $data['stock'] = intval($qty_value);
        }

        if (!isset($user_type) or (isset($user_type) and $user_type == "manager")) {

            if (empty($errors) and $store_id > 0) {

                $this->db->where("user_id", $user_id);
                $this->db->where("id_store", $store_id);
                $this->db->where("status", 1);
                $store = $this->db->get("store", 1);
                $store = $store->result_array();
                if (count($store) == 0) {
                    $errors['store'] = Translate::sprint(Messages::USER_NOT_FOUND);;
                }

            }

        }

        if (empty($errors) && isset($order_enabled) && $order_enabled == 1 && isset($user_id)) {

            if ($data['product_type'] != "price" && doubleval($data['product_value']) == 0) {
                $errors['err'] = _lang("You couldn't add this product, the product should has a specific price");
                return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
            }

            if (GroupAccess::isGrantedUser($user_id, "cf_manager")
                && isset($order_cf_id) && $order_cf_id > 0) {

                $data['order_enabled'] = 1;
                $data['cf_id'] = intval($order_cf_id);

            } else {//get from category

                $this->db->select("category_id,name");
                $this->db->where("id_store", $store_id);
                $this->db->where("status", 1);
                $store = $this->db->get("store", 1);
                $store = $store->result_array();

                if (count($store) > 0) {
                    $cat = $this->mStoreModel->getCategory($store[0]['category_id']);
                    if ($cat['cf_id'] > 0) {
                        $data['order_enabled'] = 1;
                        $data['cf_id'] = $cat['cf_id'];
                    } else {
                        $errors['cf'] = Translate::sprintf("This store (%s) unable to use order system, the reason is there is no custom fields linked with store's category", array($store[0]['name']));
                    }
                } else
                    $errors['store_id'] = Translate::sprint(Messages::STORE_NOT_SPECIFIED);

            }


            if (empty($errors) && isset($button_template)) {

                if (isset($this->button_templates[$button_template])) {
                    $data['order_button'] = $button_template;
                } else {
                    $data['order_button'] = "order";
                }

            }

        }


        if (empty($errors) and isset($user_id) and $user_id > 0) {

            $nbr_products_monthly = UserSettingSubscribe::getUDBSetting($user_id, KS_NBR_PRODUCTS_MONTHLY);

            if ($nbr_products_monthly > 0 || $nbr_products_monthly == -1) {

                //set status by default to 0 (not published )
                $data['status'] = ConfigManager::getValue('ENABLE_PRODUCT_AUTO');

                if (ConfigManager::getValue('ORDER_COMMISSION_ENABLED') == TRUE
                    && $data['product_type'] == "price" && $data['product_value'] > 0) {

                    $commission = (ConfigManager::getValue('ORDER_COMMISSION_VALUE') / 100) * $data['product_value'];
                    $data['commission'] = $commission;
                    $data['product_value'] = $data['product_value'] + $commission;

                }


                $date = date("Y-m-d H:i:s", time());
                $data['created_at'] = MyDateUtils::convert($date, TimeZoneManager::getTimeZone(), "UTC");


                $this->db->insert("product", $data);

                if ($nbr_products_monthly > 0) {
                    $nbr_products_monthly--;
                    UserSettingSubscribe::refreshUSetting($user_id, KS_NBR_PRODUCTS_MONTHLY, $nbr_products_monthly);
                }

                return array(Tags::SUCCESS => 1, Tags::RESULT => $this->db->insert_id());

            } else {
                $errors["products"] = Translate::sprint(Messages::EXCEEDED_MAX_NBR_STORES);
            }

        } else {
            $errors['store'] = Text::_print("Error!");
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }


    public function editProduct($params = array())
    {

        extract($params);


        $errors = array();
        $data = array();


        /*
        *  MANAGE PRODUCT IMAGES
        */

        if (isset($images) and !is_array($images))
            $images = json_decode($images, JSON_OBJECT_AS_ARRAY);

        if (!empty($images)) {
            $data["images"] = array();
            $i = 0;
            try {
                if (!empty($images)) {
                    foreach ($images as $value) {
                        $data["images"][$i] = $value;
                        $i++;
                    }
                    $data["images"] = json_encode($data["images"], JSON_FORCE_OBJECT);
                }
            } catch (Exception $e) {

            }
        } else {
            $data["images"] = json_decode("", JSON_OBJECT_AS_ARRAY);
        }

        if (isset($data["images"]) and empty($data["images"])) {
            $errors['images'] = Translate::sprint("Please upload an image");
        }

        if (isset($name) and $name != "") {
            $data['name'] = Text::input($name);
        } else {
            //$errors['store'] = Text::_print("Store id is messing");
        }

        if (isset($store_id) and $store_id > 0) {
            $data['store_id'] = intval($store_id);
        } else {
            $errors['store'] = Translate::sprint("Please_select_store", "Please select store");
        }

        if (isset($product_id) and $product_id > 0) {
            $data['id_product'] = intval($product_id);
        } else {
            $errors['id_product'] = Translate::sprint("Offer id is missing");
        }

        if (isset($description) and $description != "") {
            $data['description'] = Text::inputWithoutStripTags($description);
        } else {
            $errors['description'] = Translate::sprint(Messages::EVENT_DESCRIPTION_EMPTY);
        }


        if (isset($price) and doubleval($price) > 0) {

            $data['product_value'] = doubleval($price);
            $data['product_type'] = 'price';

            if (isset($currency) and $currency != "" and preg_match('#([a-zA-Z])#', $currency)) {
                $data['currency'] = $currency;
            } else {
                $data['currency'] = DEFAULT_CURRENCY;
            }

        } else if (isset($percent) and (intval($percent) > 0 || intval($percent) < 0)) {
            $data['product_value'] = doubleval($percent);
            $data['product_type'] = 'percent';
        } else {
            //Create an product with a non specified value type : e.g promo , free offre ...etc
            $data['product_type'] = 'unspecified';
            $data['product_value'] = 0;
        }


        if (isset($is_deal) && $is_deal == 1) {

            if (isset($date_start) and Text::validateDate($date_start)) {
                $data['date_start'] = $date_start;
            } else {
                $errors['date_start'] = Translate::sprint(Messages::DATE_BEGIN_NOT_VALID);
            }

            if (isset($date_end) and Text::validateDate($date_end)) {
                $data['date_end'] = $date_end;
            } else {
                $errors['date_end'] = Text::_print("Date of end is not valid!");
            }

            $data['is_deal'] = intval($is_deal);
        }


        if (isset($user_id) and intval($user_id) > 0) {
            $data['user_id'] = $user_id;
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_FOUND);
        }

        if (isset($stock) and $stock > 0) {
            //$data['qty_enabled'] = intval($stock);
            $data['stock'] = intval($stock);
        } else {
            $data['stock'] = -1; //stock should be unlimited
        }

        if (isset($data['stock']) && $data['stock'] > 0 && isset($qty_value) && $qty_value >= 0) {
            $data['stock'] = intval($qty_value);
        }


        if (empty($errors) and $store_id > 0) {

            $this->db->where("user_id", $user_id);
            $this->db->where("id_store", $store_id);
            $this->db->where("status", 1);
            $c = $this->db->count_all_results("store");
            if ($c == 0) {
                $errors['store'] = Translate::sprint(Messages::STORE_ID_NOT_VALID);
            }

        }


        if (empty($errors) && isset($order_enabled) && $order_enabled == 1 && isset($user_id)) {

            if (GroupAccess::isGrantedUser($user_id, "cf_manager")
                && isset($order_cf_id) && $order_cf_id > 0) {

                $data['order_enabled'] = 1;
                $data['cf_id'] = intval($order_cf_id);

            } else {//get from category

                $this->db->select("category_id,name");
                $this->db->where("id_store", $store_id);
                $this->db->where("status", 1);
                $store = $this->db->get("store", 1);
                $store = $store->result_array();

                if (count($store) > 0) {
                    $cat = $this->mStoreModel->getCategory($store[0]['category_id']);
                    if ($cat['cf_id'] > 0) {
                        $data['order_enabled'] = 1;
                        $data['cf_id'] = $cat['cf_id'];
                    } else {
                        $errors['cf'] = Translate::sprintf("This store (%s) unable to use order system, the reason is there is no custom fields linked with store's category", array($store[0]['name']));
                    }
                } else
                    $errors['store_id'] = Translate::sprint(Messages::STORE_NOT_SPECIFIED);

            }


            if (empty($errors) && isset($button_template)) {

                if (isset($this->button_templates[$button_template])) {
                    $data['order_button'] = $button_template;
                } else {
                    $data['order_button'] = "order";
                }

            }

        }


        if (empty($errors) and isset($user_id) and $user_id > 0) {

            $date = date("Y-m-d H:i:s", time());
            $data['updated_at'] = MyDateUtils::convert($date, TimeZoneManager::getTimeZone(), "UTC");


            if (ConfigManager::getValue('ORDER_COMMISSION_ENABLED') == TRUE
                && $data['product_type'] == "price" && $data['product_value'] > 0) {

                $commission = (ConfigManager::getValue('ORDER_COMMISSION_VALUE') / 100) * $data['product_value'];
                $data['commission'] = $commission;
                $data['product_value'] = $data['product_value'] + $commission;

            }


            //$data['status'] = 1;
            $this->db->where("id_product", $product_id);
            $this->db->where("user_id", $user_id);
            $this->db->update("product", $data);

            return array(Tags::SUCCESS => 1, Tags::RESULT => $product_id);

        } else {
            $errors['store'] = Text::_print("Error! ");
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }


    public function deleteProduct($params = array())
    {

        extract($params);
        $errors = array();
        $data = array();


        if (isset($product_id) and $product_id > 0) {

            $this->db->where("id_product", $product_id);
            $products = $this->db->get("product");
            $product = $products->result();

            //Delete all images from this products
            /*if (isset($productToDelete[0]->images)) {
                $images = (array)json_decode($productToDelete[0]->images);
                foreach ($images as $k => $v) {
                    _removeDir($v);
                }
            }*/

            $this->db->where("id_product", $product_id);
            $this->db->update("product", array(
                'hidden' => 1
            ));


            ActionsManager::add_action("product", "productRemoved", $product_id);


            return array(Tags::SUCCESS => 1);

        }

        return array(Tags::SUCCESS => 0);
    }


    public function verify($id, $accept)
    {

        $this->db->where('id_product', $id);
        $this->db->update('product', array(
            'verified' => 1,
            'status' => $accept,
        ));


        return array(Tags::SUCCESS => 1);
    }


    public function viewsCounter($id)
    {
        $this->db->where('id_product', $id);
        $this->db->set('views', 'views+1', FALSE);
        $this->db->update('product');

        $this->db->where('id_product', $id);
        $views = $this->db->get('product')->row()->views;

        return array(Tags::SUCCESS => 1, Tags::RESULT => $views);

    }


    public function downloadsCounter($id)
    {
        $this->db->where('id_product', $id);
        $this->db->set('downloads', 'downloads+1', FALSE);
        $this->db->update('product');

        $this->db->where('id_product', $id);
        $downloads = $this->db->get('product')->row()->downloads;

        return array(Tags::SUCCESS => 1, Tags::RESULT => $downloads);

    }

    function hiddenProductOutOfDate()
    {

        if (defined("PRODUCTS_IN_DATE") && !PRODUCTS_IN_DATE)
            return;

        $currentDate = date("Y-m-d H:i:s", time());
        $this->db->where("date_end <", $currentDate);
        $this->db->update("product", array(
            "status" => 0));

        return array(Tags::SUCCESS => 1);
    }

    public function duplicate($params = array())
    {


        extract($params);
        $errors = array();
        $data = array();

        if ((isset($product_id) and $product_id > 0) && (isset($user_id) && $user_id > 0)) {

            $this->db->where("user_id", $user_id);
            $this->db->where("id_product", $product_id);
            $product = $this->db->get("product", 1);
            $product = $product->result_array();


            if (count($product) > 0) {

                foreach ($product[0] as $key => $data) {
                    $product[0][$key] = Text::output($product[0][$key]);
                }

                //disable product from been published automatically
                $product[0]["status"] = 0;
                // add a copy tag for each duplicated product
                $product[0]["name"] = $product[0]["name"] . " ( " . Translate::sprint(" copy ") . " ) ";

                $newProduct = $this->addProduct($product[0]);

                //todo : amine
                //ActionsManager::add_action("product","duplicate_product",array("product_id"=>$product[0]->prodcut_id));

                return array(Tags::SUCCESS => 1);

            }
        }

        return array(Tags::SUCCESS => 0);
    }


    public function emigrateDatabase()
    {

        /*
         * table emigration from offer to product
         */

        if ($this->db->table_exists("offer") && !$this->db->table_exists("product")) {
            $this->dbforge->rename_table('offer', 'product');
        }


        if (!$this->db->field_exists('id_product', 'product')) {

            $fields = array(
                'id_offer' => array(
                    'name' => 'id_product',
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ),
            );

            $this->dbforge->modify_column('product', $fields);

        }

        if (!$this->db->field_exists('product_value', 'product')) {

            $fields = array(
                'offer_value' => array(
                    'name' => 'product_value',
                    'type' => 'DOUBLE',
                ),
            );

            $this->dbforge->modify_column('product', $fields);

        }



        if (!$this->db->field_exists('product_type', 'product')) {

            $fields = array(
                'value_type' => array(
                    'name' => 'product_type',
                    'type' => 'VARCHAR(30)',
                ),
            );

            $this->dbforge->modify_column('product', $fields);

        }


    }

    public function updateFields()
    {

        if (!$this->db->field_exists('views', 'product')) {
            $fields = array(
                'views' => array('type' => 'INT', 'default' => 0),
                'downloads' => array('type' => 'INT', 'default' => 0),
                'interests' => array('type' => 'INT', 'default' => 0),
            );
            $this->dbforge->add_column('product', $fields);
        }


        if (!$this->db->field_exists('stock', 'product')) {
            $fields = array(
                'stock' => array('type' => 'DOUBLE', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('product', $fields);
        }

        if (!$this->db->field_exists('original_value', 'product')) {
            $fields = array(
                'original_value' => array('type' => 'DOUBLE', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('product', $fields);
        }

        if (!$this->db->field_exists('parent_id', 'product')) {
            $fields = array(
                'parent_id' => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('product', $fields);
        }


        if (!$this->db->field_exists('hidden', 'product')) {
            $fields = array(
                'hidden' => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('product', $fields);
        }

    }




}
