<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function languages(){

        if (!GroupAccess::isGranted('nstranslator',TRANSLATOR_MANAGE))
            redirect("error?page=permission");

        //make setting side bar opening
        TemplateManager::set_settingActive('nstranslator');

        //get codes list
        $languages = Translate::getLangsCodes();
        $data['languages'] = $languages;

        $script = $this->load->view('nstranslator/backend/script/languages-script',$data,TRUE);
        TemplateManager::addScript($script);

        //render views
        $this->load->view("backend/header",$data);
        $this->load->view("nstranslator/backend/html/languages");
        $this->load->view("backend/footer");

    }

    public function remove()
    {

        if (!GroupAccess::isGranted('nstranslator', TRANSLATOR_MANAGE))
            redirect("error?page=permission");

        $code = $this->input->get('lang');

        Translate::remove($code);

        redirect(admin_url("nstranslator/languages"));

    }

    public function edit(){

        if (!GroupAccess::isGranted('nstranslator',TRANSLATOR_MANAGE))
            redirect("error?page=permission");

        //make setting side bar opening
        TemplateManager::set_settingActive('nstranslator');

        $lang = $this->input->get('lang');
        $data['lang'] = $lang;


        //check validate of input language
        if(!preg_match("#[a-zA-Z]{2}#",$lang))
            redirect("error?page=notfound");


        $language_cached = Translate::loadLanguageFromCache($lang);
        $language_uncached = Translate::loadLanguageFromYml($lang);
        $data['merged_data'] = Translate::merge($language_uncached,$language_cached);

        if(isset($data['merged_data']['config'])){
            $data['config'] = $data['merged_data']['config'];
        }else{
            $data['config'] = $language_uncached['config'];
        }


        //render views
        TemplateManager::addCssLibs(base_url("views/skin/backend/plugins/datatables/dataTables.bootstrap.css"));
        TemplateManager::addCssLibs(
            TemplateManager::assets('nstranslator','css/style.css')
        );

        $script = $this->load->view('nstranslator/backend/script/edit-script',$data,TRUE);
        TemplateManager::addScript($script);

        $this->load->view("backend/header",$data);
        $this->load->view("nstranslator/backend/html/edit");
        $this->load->view("backend/footer");


    }

}