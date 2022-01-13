<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */
class Api extends API_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load model
    }

    public function updateOrder()
    {

        $data = $this->mDeliveryModel->updateOrder(array(
            "order_id" => $this->input->post("order_id"),
            "status" => $this->input->post("status"),
            "delivery_id" => $this->input->post("delivery_id"),
            "message" => $this->input->post("message"),
        ));

        echo json_encode($data, JSON_FORCE_OBJECT);

    }


    public function getOrders()
    {


        $cf = $this->mDeliveryModel->getDefaultCF();

        if($cf != NULL)
            $cf_id = $cf['id'];
        else
            $cf_id = 0;

        $data = $this->mOrderModel->getOrders(array(
            "id" => $this->input->post("order_id"),
            "module" => $this->input->post("module"),
            "module_id" => intval($this->input->post("module_id")),
            "order_by" => $this->input->post("order_by"),
            "user_id" => intval($this->input->post("user_id")),
            "limit" => intval($this->input->post("limit")),
            "page" => intval($this->input->post("page")),
            "except" => $this->input->post("except"),
            "delivery_id" => intval($this->input->post("delivery_id")),
            "delivery_status" => intval($this->input->post("delivery_status")),
            "order_status" => intval($this->input->post("order_status")),
            "cf_id" => $cf_id,

        ), NULL, function ($params) {

            if (isset($params['except']) && $params['except'] != "") {

                $except = explode(";", $params['except']);

                foreach ($except as $k => $v) {
                    $except[$k] = intval($v);
                }

                $this->db->where_not_in("id", $except);
            }

            if (isset($params['delivery_status']) && $params['delivery_status'] >=0) {
                $this->db->where("order_list.delivery_status", intval($params['delivery_status']));
            }else if (isset($params['delivery_status']) && $params['delivery_status'] == -1){
                $this->db->where("order_list.delivery_id >", 0);
            }

            if (isset($params['order_status']) && $params['order_status'] != -1) {
                $this->db->where("order_list.status", intval($params['order_status']));
            }

            if (isset($params['delivery_id']) && $params['delivery_id'] > 0) {
                $this->db->where("order_list.delivery_id", intval($params['delivery_id']));
            }


            if (isset($params['delivery_id']) && $params['delivery_id'] > 0) {
                $this->db->where("order_list.delivery_id", intval($params['delivery_id']));
            }

            if (isset($params['cf_id']) && $params['cf_id'] > 0) {
                $this->db->where("order_list.req_cf_id", intval($params['cf_id']));
            }

        });

        echo json_encode($data, JSON_FORCE_OBJECT);
    }

    public function deliveryAnalytics(){


        $token = $this->input->post('user_token');
        $user_id = $this->input->post('user_id');

        if(TokenSetting::isValid($user_id,"logged",$token)){

            $token = TokenSetting::getValid($user_id,"logged",$token);
            if($token!=NULL){
                $this->mUserBrowser->refreshData($token->uid);
            }
        }else{
            echo json_encode(array(Tags::SUCCESS=>0));
            return;
        }


        $analytics = $this->mDeliveryModel->getDeliveredAnalytics();

        foreach ($analytics as $key => $value){
            if(isset($value['string'])){
                $analytics[$key] = $value['string'];
            }else{
                $analytics[$key] = $value;
            }
        }

        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$analytics));
        return;

    }

    public function signUp()
    {


        $username = $this->input->post("username");
        $password = Security::decrypt($this->input->post("password"));

        $first_name = $this->input->post("first_name");
        $last_name = $this->input->post("last_name");
        $email = $this->input->post("email");
        $phone = Text::input($this->input->post("phone"));
        $name =  $this->input->post("name");
        $mac_addr = Security::decrypt($this->input->post("mac_address"));
        $token = ($this->input->post("token"));
        $auth_type = ($this->input->post("auth_type"));

        $lat = Security::decrypt($this->input->post("lat"));
        $lng = Security::decrypt($this->input->post("lng"));
        $image = Security::decrypt($this->input->post("image"));

        $guest_id = Security::decrypt($this->input->post("guest_id"));


        $params = array(
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'phone' => $phone,
            'name' => $name,
            'lat' => $lat,
            'lng' => $lng,
            'image' => $image,
            'mac_address' => $mac_addr,
            'token' => $token,
            "auth_type" => $auth_type,
            "guest_id" => $guest_id,
            "typeAuth" => DEFAULT_USER_MOBILE_GRPAC
        );

        $data = $this->mUserModel->signUp($params, array(
            "name"
        ));

         //update GRP
         if (isset($data[Tags::RESULT][0])) {

             $this->mDeliveryModel->createDeliveryProfile($data[Tags::RESULT][0]['id_user']);
             $this->mDeliveryModel->updateGrp($data[Tags::RESULT][0]['id_user']);

             $data[Tags::RESULT][0]['status'] = -1;

         }

        //send message welcome
        if (MESSAGE_WELCOME != "" && isset($data[Tags::RESULT])) {

            $this->load->model("Messenger/MessengerModel", "mMessengerModel");
            $this->db->select("id_user");
            $this->db->order_by("id_user", "ASC");
            $user = $this->db->get("user", 1);
            $user = $user->result();

            $result = $this->mMessengerModel->sendMessage(array(
                "sender_id" => $user[0]->id_user,
                "receiver_id" => $data[Tags::RESULT][0]['id_user'],
                "discussion_id" => 0,
                "content" => Text::input(MESSAGE_WELCOME)
            ));

        }

        if ($data[Tags::SUCCESS] == 1) {
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, $data);
        } else {
            echo json_encode($data);
        }


    }

    public function signIn()
    {

        $login = Security::decrypt($this->input->post("login"));
        $mac_address = Security::decrypt($this->input->post("mac_address"));
        $password = Security::decrypt($this->input->post("password"));

        $lat = Security::decrypt($this->input->post("lat"));
        $lng = Security::decrypt($this->input->post("lng"));

        $guest_id = Security::decrypt($this->input->post("guest_id"));

        $params = array(
            "login" => $login,
            "password" => $password,
            "lat" => $lat,
            "lng" => $lng,
            "guest_id" => $guest_id,
            "mac_address" => $mac_address
        );

        $data = $this->mUserModel->signIn($params);

        //generate grp
        $this->mDeliveryModel->generate_db_grp();

        if (isset($data[Tags::RESULT][0])) {
            $grp = $this->mDeliveryModel->getGrp();
            if($grp != NULL && $grp->id != $data[Tags::RESULT][0]['grp_access_id']){
                echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>_lang("You don't have access to this application"))));
                return;
            }
        }

        if ($data[Tags::SUCCESS] == 1) {
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array());
        } else {
            echo json_encode($data);
        }

    }


}

/* End of file UploaderDB.php */