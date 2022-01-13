<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */
class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model("category/category_model", "mCategoryModel");

    }

    public function addCategory()
    {

        if(!GroupAccess::isGranted('category',ADD_CATEGORY)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $cat = trim($this->input->post("cat"));
        $image = $this->input->post("image");
        $icon = $this->input->post("icon");
        $color = $this->input->post("color");
        $cf_id = $this->input->post("cf_id");

        echo json_encode($this->mCategoryModel->addCategory(array(
            "cat" => $cat,
            "image" => $image,
            "icon" => $icon,
            "color" => $color,
            "cf_id" => $cf_id,
        )));

        return;

    }


    public function delete()
    {

        if(!GroupAccess::isGranted('category',DELETE_CATEGORY)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $id = intval($this->input->post("id"));

        echo json_encode($this->mCategoryModel->delete(
            $id
        ));
    }


    public function editCategory()
    {

        if(!GroupAccess::isGranted('category',EDIT_CATEGORY)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $cat = trim($this->input->post("cat"));
        $cat_id = intval(trim($this->input->post("id")));
        $image = $this->input->post("image");
        $icon = $this->input->post("icon");
        $color = $this->input->post("color");
        $cf_id = $this->input->post("cf_id");


        echo json_encode($this->mCategoryModel->editCategory(
            array(
                "cat" => $cat,
                "cat_id" => $cat_id,
                "image" => $image,
                "icon" => $icon,
                "color" => $color,
                "cf_id" => $cf_id,
            )
        ));
    }


}

/* End of file CategoryDB.php */