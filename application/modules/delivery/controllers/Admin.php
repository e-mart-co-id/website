<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();
        //load model
    }



    public function delivery_config(){

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");


        $data['title'] = _lang("Delivery config");
        $this->load->view("backend/header",$data);
        $this->load->view("delivery/backend/delivery-config");
        $this->load->view("backend/footer");

    }


    public function payouts(){

        if (!GroupAccess::isGranted('delivery'))
            redirect("error?page=permission");

        $status = $this->input->get('status');
        if($status=="")  $status = 2;
        else  $status = intval($status);

        $params = array(
            "status"  => $status,
            "page"  => intval($this->input->get('page')),
            "payout_id"  => intval($this->input->get('id')),
            "transaction_id"  => intval($this->input->get('transaction_id')),
            "limit"  => 15,
            "order_by_date"  => 1
        );

        if(!GroupAccess::isGranted('delivery',MANAGE_DELIVERY_PAYOUTS))
            $params['user_id'] = SessionManager::getData('id_user');

        $data['result'] = $this->mOrderModel->getPayout($params,array(
            'module' => 'delivery'
        ));

        $this->load->view("backend/header",$data);
        $this->load->view("delivery/backend/payouts/payouts_list");
        $this->load->view("backend/footer");


    }



    public function editPayout(){

        if (!GroupAccess::isGranted('delivery',MANAGE_DELIVERY_PAYOUTS))
            redirect("error?page=permission");

        $id = intval($this->input->get("id"));

        $p = $this->mOrder->getPayoutObject($id);

        if($p==NULL)
            redirect("error404");

        $data['payout'] = $p;

        $this->load->view("backend/header",$data);
        $this->load->view("delivery/backend/payouts/edit_payout");
        $this->load->view("backend/footer");

    }



    public function users()
    {

        if(!GroupAccess::isGranted('user',MANAGE_USERS))
            redirect("error?page=permission");

        $params = array(
            "page" => $this->input->get("page"),
            "id" => $this->input->get("id"),
            "search" => $this->input->get("search"),
            'limit' => NO_OF_ITEMS_PER_PAGE,
            "is_super" => TRUE,
            "user_id" => $this->mUserBrowser->getData("id_user")
        );

        $grp = $this->mDeliveryModel->getGrp();
        $params['grp_acc_id'] = $grp->id;
        $params['is_super'] = TRUE;

        $filter = $this->input->get("filter");

        if($filter=="balance")
            $params['filter-balance'] = TRUE;

        $data['data'] = $this->mUserModel->getUsers($params,function ($params){

            $this->db->where('user.grp_access_id',$params['grp_acc_id']);

            if(isset($params['filter-balance']) && $params['filter-balance']==TRUE){
                $this->db->join('wallet',"wallet.user_id=user.id_user");
                $this->db->where("balance >",0);
            }

        },function ($params){

            $this->db->order_by('user.status ASC, user.id_user DESC');

        });

        $data['title'] = _lang("Delivery users");

        $this->load->view("backend/header", $data);
        $this->load->view("delivery/backend/users");
        $this->load->view("backend/footer");

    }




}

/* End of file EventDB.php */