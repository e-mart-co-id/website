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

        ModulesChecker::requireEnabled("nshistoric");

    }




}

/* End of file CampaignDB.php */