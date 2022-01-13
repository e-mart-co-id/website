<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */
class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {

    }






    public function options(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        TemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();


        $this->load->view("backend/header",$data);
        $this->load->view("store/backend/html/options");
        $this->load->view("backend/footer");


    }


    public function cf_categories()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            redirect(admin_url("error404"));
        }

        $data = array();

        $data['data'] = $this->mCategoryModel->getByCategory();

        $this->load->view("backend/header", $data);
        $this->load->view("store/backend/html/cf_category/html/list");
        $this->load->view("backend/footer");

    }


    public function cf_categories_edit()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            redirect(admin_url("error404"));
        }

        $data = array();

        $idc = intval($this->input->get("id"));
        $data['category'] = $this->mCategoryModel->getByCategory($idc);

        if (isset($data['category']['cats'][0])) {

            $data['category'] = $data['category']['cats'][0];

            $this->load->view("backend/header", $data);
            $this->load->view("store/backend/html/cf_category/html/edit");
            $this->load->view("backend/footer");

        } else {
            redirect(admin_url("error404"));
        }


    }



    public function reviews()
    {

        if (!GroupAccess::isGranted('store'))
            redirect("error?page=permission");


        $id_store = intval(($this->input->get("id")));


        $params = array(
            "limit" => 1,
            "store_id" => $id_store,
        );

        if (!GroupAccess::isGranted("store", MANAGE_STORES))
            $params['user_id'] = $this->mUserBrowser->getData("id_user");

        $data["store"] = $this->mStoreModel->getStores($params);

        if (!isset($data["store"][Tags::RESULT][0]))
            redirect("error?page=permission");


        $page = intval($this->input->get("page"));

        $data['data'] = $this->mStoreModel->getReviews(array(
            'id_store' => $id_store,
            'page' => $page,
        ));


        $this->load->view("backend/header", $data);
        $this->load->view("store/backend/html/reviews");
        $this->load->view("backend/footer");

    }


    public function view()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES))
            redirect("error?page=permission");


        $params = array(
            "limit" => 1,
            "store_id" => intval($this->input->get('id')),
        );

        $data['dataStores'] = $this->mStoreModel->getStores($params);

        if ($data['dataStores'][Tags::SUCCESS] == 0) {
            redirect(admin_url("error404"));
        }

        $data['categories'] = $this->mCategoryModel->getCategories();

        if (GroupAccess::isGranted('gallery')
            && ModulesChecker::isRegistred("gallery"))
            $data['gallery'] = $this->mGalleryModel->getGallery(array(
                "limit" => $this->mGalleryModel->maxfiles,
                "module" => "store",
                "module_id" => $data['dataStores'][Tags::RESULT][0]['id_store']
            ));

        // css
        $libcssdp = TemplateManager::assets("store", "plugins/timepicker/jquery.timepicker.css");
        TemplateManager::addCssLibs($libcssdp);

        $this->load->view("backend/header", $data);
        $this->load->view("store/backend/html/edit");
        $this->load->view("backend/footer");


    }

    public function edit()
    {

        if (!GroupAccess::isGranted('store', EDIT_STORE))
            redirect("error?page=permission");


        $params = array(
            "limit" => 1,
            "store_id" => intval($this->input->get('id')),
           /* "user_id" => intval($this->mUserBrowser->getData("id_user")),*/
        );

        $data['dataStores'] = $this->mStoreModel->getStores($params);

        if (!isset($data['dataStores'][Tags::RESULT][0])) {
            redirect(admin_url("error404"));
        }

        $data['categories'] = $this->mCategoryModel->getCategories();


        if (GroupAccess::isGranted('gallery')
            && ModulesChecker::isRegistred("gallery"))
            $data['gallery'] = $this->mGalleryModel->getGallery(array(
                "limit" => $this->mGalleryModel->maxfiles,
                "module" => "store",
                "module_id" => $data['dataStores'][Tags::RESULT][0]['id_store']
            ));


        // css
        $libcssdp = TemplateManager::assets("store", "plugins/timepicker/jquery.timepicker.css");
        TemplateManager::addCssLibs($libcssdp);

        $this->load->view("backend/header", $data);
        $this->load->view("store/backend/html/edit");
        $this->load->view("backend/footer");


    }


    public function create()
    {

        if (!GroupAccess::isGranted('store', ADD_STORE))
            redirect("error?page=permission");

        // css
        $libcssdp = TemplateManager::assets("store", "plugins/timepicker/jquery.timepicker.css");
        TemplateManager::addCssLibs($libcssdp);

        $data['categories'] = $this->mCategoryModel->getCategories();

        $this->load->view("backend/header", $data);
        $this->load->view("store/backend/html/create");
        $this->load->view("backend/footer");

    }


    public function all_stores()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES))
            redirect("error?page=permission");

        $id_store = intval($this->input->get("id"));
        $page = intval($this->input->get("page"));
        $status = intval($this->input->get("status"));
        $search = $this->input->get("search");
        $category_id = intval($this->input->get("category_id"));

        $limit = NO_OF_STORE_ITEMS_PER_PAGE;

        $params = array(
            "limit" => $limit,
            "page" => $page,
            "search" => $search,
            "status" => -1,
            "category_id" => $category_id,
            "order_by" => "recent"
        );

        $owner_id = intval($this->input->get("owner_id"));
        $params["owner_id"] = $owner_id;

        $data["data"] = $this->mStoreModel->getStores($params);
        $data["paginate_url"] = admin_url("store/all_stores");
        $data["h1_title"] = Translate::sprint("All Stores");

        $this->load->view("backend/header", $data);
        $this->load->view("store/backend/html/stores");
        $this->load->view("backend/footer");


    }


    public function my_stores()
    {

        if (!GroupAccess::isGranted('store'))
            redirect("error?page=permission");

        $id_store = intval($this->input->get("id"));
        $page = intval($this->input->get("page"));
        $status = intval($this->input->get("status"));
        $search = $this->input->get("search");
        $category_id = intval($this->input->get("category_id"));
        $limit = NO_OF_STORE_ITEMS_PER_PAGE;

        $params = array(
            "limit" => $limit,
            "page" => $page,
            "search" => $search,
            "status" => -1,
            "category_id" => $category_id,
            "order_by" => "recent"
        );

        $params['user_id'] = intval($this->mUserBrowser->getData("id_user"));

        $data["data"] = $this->mStoreModel->getStores($params);
        $data["paginate_url"] = admin_url("store/my_stores");
        $data["h1_title"] = Translate::sprint("My Stores");

        $this->load->view("backend/header", $data);
        $this->load->view("store/backend/html/stores");
        $this->load->view("backend/footer");


    }


    public function verify()
    {

        if ($this->mUserBrowser->isLogged()) {

            if (!GroupAccess::isGranted('store', MANAGE_STORES))
                redirect("error?page=permission");


            $id = intval($this->input->get('id'));
            $accept = intval($this->input->get('accept'));


            $this->db->where('id_store', $id);
            $this->db->update('store', array(
                'verified' => 1,
                'status' => $accept,
            ));


        }

      //  redirect(admin_url('store/all_stores'));

        echo json_encode(array(Tags::SUCCESS => 1));
        return;
    }

    public function status()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $params = array( "id" => intval($this->input->get("id")));
        echo json_encode($this->mStoreModel->storeAccess($params));return;

    }

}

/* End of file StoreDB.php */