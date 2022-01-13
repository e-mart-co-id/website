<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();
        //load model

        ModulesChecker::requireEnabled("campaign");

    }


    public function getCampaigns($params=array()){


        if (!GroupAccess::isGranted('campaign'))
            redirect("error?page=permission");

        $vToExtract = array_key_whitelist($params, [
            'type',
            'module_id',
            'page',
            'status',
            'owner',
            'limit',
            'campaign_id',
        ]);
        extract($vToExtract,EXTR_SKIP);

        $params = array(
            "type"  => $type,
            "module_id"  => $module_id,
            "page"  => $page,
            "status"  => $status,
            "limit"  => $limit,
            "campaign_id"  => $campaign_id,
        );


        if(!GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS) || (isset($owner) and $owner==1)){
            $params['user_id'] =  intval($this->mUserBrowser->getData("id_user"));
        }


        return $this->mCampaignModel->getCampaigns($params);

    }

    private function getStatusByAction(){

        $action = $this->input->get('action');

        switch ($action){
            case "all_campaigns":
                return 0;
            case "my_campaigns":
                return -2;
            case "pushed_campaigns":
                return 1;
            case "completed_campaigns":
                return 2;
            case "pending_campaigns":
                return -1;
        }

        return 0;
    }

    public function campaigns(){

        if (!GroupAccess::isGranted('campaign'))
            redirect("error?page=permission");

        $data = array();

        $cid =intval($this->input->get("push"));

        if($cid>0 && GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)){
            $this->mCampaignModel->validateAndPushCampaign($cid);
            redirect(admin_url("campaign/campaigns"));
        }

        $status = $this->getStatusByAction();

        if($status == -2){
            $data['campaigns'] =  $this->getCampaigns(array(
                "page" => $this->input->get("page"),
                "owner" => SessionManager::getData('id_user'),
            ));
        }else{
            $data['campaigns'] =  $this->getCampaigns(array(
                "page" => $this->input->get("page"),
                "status" => intval($status)
            ));
        }


        $this->load->view("backend/header",$data);
        $this->load->view("campaign/backend/html/list");
        $this->load->view("backend/footer");

    }


    public function report(){


        if (!GroupAccess::isGranted('campaign'))
            redirect("error?page=permission");

        $data = array();

        $cid = intval($this->input->get("id"));

        $params = array(
          'limit'=>1,
           'campaign_id'=>$cid,
        );

        if(GroupAccess::isGranted('campaign') && !GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)){
            $params['owner'] = SessionManager::getData('id_user');
        }

        $result = $this->getCampaigns($params);

        if(!isset($result[Tags::RESULT][0]))
            redirect("error404");

        $data['campaign'] = $result[Tags::RESULT][0];


        $data['report_last_week'] = $this->mCampaignModel->getCampaignReport($result[Tags::RESULT][0]['id'],Campaign_model::WEEK);
        $data['last_week'] = $this->mCampaignModel->getLastWeekD();


        $data['report_last_month'] = $this->mCampaignModel->getCampaignReport($result[Tags::RESULT][0]['id'],Campaign_model::MONTH);
        $data['last_month'] = $this->mCampaignModel->getLastMonthD();


        $data['report_last_24h'] = $this->mCampaignModel->getCampaignReport24($result[Tags::RESULT][0]['id']);
        $data['last_24h'] = $this->mCampaignModel->getLast24hD();


        $this->load->view("backend/header",$data);
        $this->load->view("campaign/backend/html/report");
        $this->load->view("backend/footer");

    }


    public function create(){

        if (!GroupAccess::isGranted('campaign'))
            redirect("error?page=permission");

        $data = array();

        $cid =intval($this->input->get("push"));

        if($cid>0 && GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)){
            $this->mCampaignModel->validateAndPushCampaign($cid);
            redirect(admin_url("campaign/campaigns"));
        }

        $data['campaigns'] =  $this->getCampaigns(array(
            "page" => $this->input->get("page"),
            "status" => intval($this->input->get("status")),
            "owner" => intval($this->input->get("owner")),
        ));

        // css
        $libcssdp = TemplateManager::assets("campaign", "css/style.css");
        TemplateManager::addCssLibs($libcssdp);

        $this->load->view("backend/header",$data);
        $this->load->view("campaign/backend/html/add");
        $this->load->view("backend/footer");

    }


    public function campaign_config(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        TemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();

        $this->load->view("backend/header",$data);
        $this->load->view("campaign/backend/html/campaign_config");
        $this->load->view("backend/footer");


    }

    public function campaign()
    {

        $this->load->view("backend/header");
        $this->load->view("campaign/backend/campaign");
        $this->load->view("backend/footer");

    }





}

/* End of file CampaignDB.php */