<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droideve Technology
 * Date: {date}
 * Time: {time}
 */

class Uploader_model extends CI_Model
{


    public function __construct()
    {
        parent::__construct();
    }


    public function uploadFiles($files=NULL){

        /*
         * path file
         * resize (true|false)
         * save in database (true|false)
         * base64 (true|false)
         */


        if (!isset($files) AND isset($_FILES['file'])){
            $file = $_FILES['file'];
        }else if(isset($files)) {
            $file =$files;
        }else{

            $errors = array();
            $errors[] = "Upload failed!";
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
        }


        $uploader = new FileUploader($this,$file);
        //$uploader->setCopyright(Path::getPath(array("template","copyright.png")));
        $result = $uploader->start();

        if(empty($uploader->getErrors()) and isset($result['result'])){

            $type = $this->getTypeFile($files['type']);

            $file = new FileDB();
            $file->directory =$result['result']['dir'];
            $file->filetype=strtolower($type);
            $file->save();
            $insertedId = $file->id;

            return array(Tags::CODE=>Codes::SUCCESS,"errors"=>$uploader->getErrors(),"results"=>$result);


        }

        return array(Tags::CODE=>Codes::FAILED,"errors"=>$uploader->getErrors());

    }


    public function delete($dirID){

        /*$this->db->where("image",$dirID);
        $this->db->delete("image");

        @_removeDir($dirID);*/

        return TRUE;
    }

    public function uploadImage64($files=NULL){



        if($files!=NULL and isset($files) AND is_string($files)){

            //$_FILES['image']['type']="image/jpg";
            $Upoader = new UploaderHelper($files);

            $r = $Upoader->start64();
            //echo json_encode(array("errors"=>$Upoader->getErrors(),"results"=>$r));

            $errors = array();
            $errors = $Upoader->getErrors();

            if(empty($errors)){

                if(isset($r['image']) AND $r['image']!=""){

                    $imageData = array("type"=>$r['type'],"image"=>$r['image']);
                    $this->db->insert("image",$imageData);
                    $id = $this->db->insert_id();

                    if(isset($imageData['image'])){
                        $imageData['images'] = _openDir($imageData['image']);
                    }


                    return array(Tags::SUCCESS=>1,Tags::RESULT=>$imageData,"image_id"=>$id);

                }else{
                    return array(Tags::SUCCESS=>0,  Tags::ERRORS=>array("add"=>Translate::sprint("Error")));
                }

            }else{


                return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors,"results"=>$r);
            }


        }else{

            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("select"=>Translate::sprint("Please Select image")));

        }
    }


    /*public function uploadImage($files=NULL){



        if($files!=NULL and isset($files)){

            //$_FILES['image']['type']="image/jpg";
            $Upoader = new UploaderHelper($files);

            $r = $Upoader->start();
            //echo json_encode(array("errors"=>$Upoader->getErrors(),"results"=>$r));



            $errors = array();
            $errors = $Upoader->getErrors();

            if(empty($errors)){

                if(isset($r['image']) AND $r['image']!=""){

                    $imageData = array("type"=>$r['type'],"image"=>$r['image']);
                    $this->db->insert("image",$imageData);
                    $id = $this->db->insert_id();

                    if(isset($imageData['image'])){
                        $imageData['images'] = _openDir($imageData['image']);
                    }



                    return array(Tags::SUCCESS=>1,"data"=>$imageData,"image_id"=>$id);

                }else{
                    return array(Tags::SUCCESS=>0,  Tags::ERRORS=>array("add"=>"Erreur dans l'ajout votre image de votre marque"));
                }

            }else{


                return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors,"results"=>$r);
            }


        }else{

            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("select"=>Translate::sprint("Please Select image")));

        }
    }*/

    public function uploadImage($files=NULL){



        if($files!=NULL and isset($files)){

            //$_FILES['image']['type']="image/jpg";
            $Upoader = new UploaderHelper($files);

            $r = $Upoader->start();
            //echo json_encode(array("errors"=>$Upoader->getErrors(),"results"=>$r));



            $errors = array();
            $errors = $Upoader->getErrors();

            if(empty($errors)){

                if(isset($r['image']) AND $r['image']!=""){

                    $imageData = array("type"=>$r['type'],"image"=>$r['image']);
                    $this->db->insert("image",$imageData);
                    $id = $this->db->insert_id();

                    if(isset($imageData['image'])){
                        $imageData['images'] = _openDir($imageData['image']);
                    }



                    return array(Tags::SUCCESS=>1,"data"=>$imageData,"image_id"=>$id);

                }else{
                    return array(Tags::SUCCESS=>0,  Tags::ERRORS=>array("add"=>"Erreur dans l'ajout votre image de votre marque"));
                }

            }else{


                return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors,"results"=>$r);
            }


        }else{

            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("select"=>Translate::sprint("Please Select image")));

        }
    }


    public function clear(){

        $images_key_from_db = array();




        //stores
        $this->db->select('images,id_store');
        $stores = $this->db->get('store');
        $stores = $stores->result();
        foreach ($stores as $store){

            $images = json_decode($store->images,JSON_OBJECT_AS_ARRAY);

            if(count($images)>0){

                foreach ($images as $image){
                    $images_key_from_db[] = $image;
                }
                $this->db->where('id_store',$store->id_store);
                $this->db->update('store', array(
                    'updated_at' => date('Y-m-d H:i:s',time())
                ));
            }

        }


        //events
        $this->db->select('images,id_event');
        $events = $this->db->get('event');
        $events = $events->result();
        foreach ($events as $event){

            $images = json_decode($event->images,JSON_OBJECT_AS_ARRAY);

            if(count($images)>0){
                foreach ($images as $image){
                    $images_key_from_db[] = $image;
                }
            }

        }



        //products
        $this->db->select('images,id_product');
        $products = $this->db->get('product');
        $products = $products->result();
        foreach ($products as $product){

            $images = json_decode($product->images,JSON_OBJECT_AS_ARRAY);


            if(is_array($images) &&  count($images)>0){
                foreach ($images as $image){
                    $images_key_from_db[] = $image;
                }
            }

        }




        //users
        $this->db->select('images,id_user');
        $users = $this->db->get('user');
        $users = $users->result();
        foreach ($users as $user){

            if(preg_match("#^([0-9]+)$#i",$user->images)){
                $images = array($user->images=>$user->images);
            }else{
                $images = json_decode($user->images,JSON_OBJECT_AS_ARRAY);
            }

            if(is_array($images) && count($images)>0){
                foreach ($images as $image){
                    $images_key_from_db[] = $image;
                }
            }

        }




        //gallery
        $images = $this->db->query('SELECT image FROM image WHERE id_image IN (SELECT image_id FROM gallery)');
        $images = $images->result();

        foreach ($images as $image){
            $images_key_from_db[] = $image->image;
        }



        //logo
        if(!is_array(APP_LOGO))
            $images = json_decode(APP_LOGO,JSON_OBJECT_AS_ARRAY);
        else
            $images = json_decode(APP_LOGO,JSON_OBJECT_AS_ARRAY);

        if(count($images)>0){
            foreach ($images as $key => $value)
                $images_key_from_db[] = $value;
        }




        ///////get all images from folder and check to remove
        $folders = getAllImgFolder();

        foreach ($folders as $folder){
            if(!in_array($folder,$images_key_from_db)){
               $this->delete($folder);
            }
        }


    }


    public function updateFields(){

        if (!$this->db->field_exists('updated_at', 'image'))
        {
            $fields = array(
                'updated_at'  => array('type' => 'DATETIME', 'after' => 'image','default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('image', $fields);
        }


        if (!$this->db->field_exists('created_at', 'image'))
        {
            $fields = array(
                'created_at'  => array('type' => 'DATETIME', 'after' => 'updated_at','default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('image', $fields);
        }


    }

    public function createTable(){

        if(!$this->db->table_exists('files')){


            $this->load->dbforge();
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ),
                'dir' => array(
                    'type' => 'VARCHAR(100)',
                    'default' => NULL
                ),

                'user_id' => array(
                    'type' => 'INT',
                    'default' => NULL
                ),

                'ext' => array(
                    'type' => 'VARCHAR(100)',
                    'default' => NULL
                ),

                'file' => array(
                    'type' => 'VARCHAR(100)',
                    'default' => NULL
                ),


                'updated_at' => array(
                    'type' => 'DATETIME',
                    'default' => NULL
                ),
                'created_at' => array(
                    'type' => 'DATETIME',
                    'default' => NULL
                ),
            ));

            $attributes = array('ENGINE' => 'InnoDB');
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('files', TRUE, $attributes);

        }

    }

}