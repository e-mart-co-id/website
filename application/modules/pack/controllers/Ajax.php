<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Ajax extends AJAX_Controller  {

    public function __construct(){
        parent::__construct();

        //load model
        $this->load->model("pack/pack_model",'mPack');
        $this->load->model("pack/user_browser",'mUserBrowser');

    }


    public function set_pid(){

        $id = $this->input->post("pid");

        if($id != ""){
            ConfigManager::setValue("DF_SUBSCRIPTION_PACK_PID",$id);
            echo json_encode(array(Tags::SUCCESS=>1));return;
        }

        echo json_encode(array(Tags::SUCCESS=>0));return;
    }

    public function select_pack(){


        if (SessionManager::isLogged()){
            $manager = SessionManager::getData("manager");
            if($manager==1){
                die("The manager has no permission to do this operation!");
            }
        }

        $pack_id = intval($this->input->post("pack-id"));
        $pack_duration = intval($this->input->post("pack-duration"));


        $up_acc_user_id = $this->session->userdata("up_acc_user_id");
        $up_acc_user_id = intval($up_acc_user_id);
        if($up_acc_user_id>0){
            if( $this->mUserBrowser->refreshData($up_acc_user_id)){
                $this->session->set_userdata(array(
                    "up_acc_user_id" => 0
                ));
            }
        }

        if($this->mUserBrowser->isLogged()){

            $user_id = intval($this->mUserBrowser->getData("id_user"));
            $pack = $this->mPack->getPack($pack_id);


            if ($pack != NULL) {

                $qty = intval($pack_duration);
                if($qty!=1 AND $qty!=12)
                    $qty = 1;




                if (!ModulesChecker::isEnabled('payment') OR ($pack->price == 0 && !$this->mPack->hadInvoices()) ) {

                    $this->mPack->updatePackAccount($pack_id, $user_id, TRUE);
                    echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>site_url("user/signup?q")));

                    return;
                } else {

                    if(ModulesChecker::isEnabled('payment')){
                        $id = $this->mPack->createInvoice($pack, $user_id, $qty);
                        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>site_url("payment/make_payment?id=" . $id)));
                        return;
                    }else{
                        $id = $this->mPack->createInvoice($pack, $user_id, $qty);
                        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>site_url("pack/paymentDisabled")));
                        return;
                    }


                }



            }
        }else{

            $this->session->set_userdata(array(
                "pack-id" => $pack_id,
                "pack-duration" => $pack_duration,
            ));

            $url = site_url("user/signup?s");

            echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$url));
            return;

        }


        echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>"Couldn't select the pack"));
        return;

    }


    public function add(){

        if(GroupAccess::isGranted('pack',ADD_PACK)){

            $params = array(
                "name"  => $this->input->post("name"),
                "group_access"  => $this->input->post("group_access"),
                "price"  => $this->input->post("price"),
                "order"  => $this->input->post("order"),
                "duration"  => $this->input->post("duration"),
                "description"  => $this->input->post("description"),
                "user_subscribe"  => $this->input->post("user_subscribe"),
                "recommended"  => $this->input->post("recommended"),
                "display" =>  $this->input->post("display"),
                "price_yearly" =>  $this->input->post("price_yearly"),
                "free" =>  $this->input->post("free"),
                "trial_period" =>  $this->input->post("trial_period")
            );
            $result = $this->mPack->add($params);


            echo json_encode($result); return;

        }else{

            echo json_encode(array(Tags::SUCCESS=>0)); return;

        }

    }


    public function edit(){

        if(GroupAccess::isGranted('pack',EDIT_PACK)){

            $result = $this->mPack->edit(array(
                "id"  => $this->input->post("id"),
                "name"  => $this->input->post("name"),
                "group_access"  => $this->input->post("group_access"),
                "price"  => $this->input->post("price"),
                "order"  => $this->input->post("order"),
                "duration"  => $this->input->post("duration"),
                "description"  => $this->input->post("description"),
                "user_subscribe"  => $this->input->post("user_subscribe"),
                "recommended"  => $this->input->post("recommended"),
                "display" =>  $this->input->post("display"),
                "price_yearly" =>  $this->input->post("price_yearly"),
                "free" =>  $this->input->post("free"),
                "trial_period" =>  $this->input->post("trial_period")
            ));

            echo json_encode($result); return;

        }else{

            echo json_encode(array(Tags::SUCCESS=>0)); return;

        }

    }

    public function delete(){

        if(!GroupAccess::isGranted('pack',DELETE_PACK)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = intval($this->input->post("id"));
        $result = $this->mPack->delete($id);
        echo json_encode($result); return;

    }

    public function changeOwnerPack(){

        if(!GroupAccess::isGranted('pack')){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $pack_id = intval($this->input->post("pack_id"));
        $user_id = intval($this->input->post("user_id"));
        $pack_duration = intval($this->input->post("pack_duration"));

        $pack = $this->mPack->getPack($pack_id);

        if($pack!=NULL){
            $this->mPack->updatePackAccount($pack_id, $user_id, TRUE, $pack_duration);
            echo json_encode(array(Tags::SUCCESS=>1));return;
        }
        echo json_encode(array(Tags::SUCCESS=>0));return;

    }

    public function packs(){



    }


}

/* End of file PackmanagerDB.php */