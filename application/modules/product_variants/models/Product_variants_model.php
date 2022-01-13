<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_variants_model extends CI_Model {

    public $type = array("one_option","multi_options");

    // Change the above three vriables as per your app.
    public function __construct() {
        parent::__construct();
    }

    public function getGroupedList($product_id){

        $result = array();

        $group_list = $this->db->where('product_id',$product_id)
            ->where('parent_id',0)
            ->order_by('_order','ASC')
            ->get('variants')->result_array();

        foreach ($group_list as $grp){

            $options = $this->db->where('product_id',$product_id)
                ->where('parent_id',$grp['id'])
                ->order_by('_order','ASC')
                ->get('variants')->result_array();


            $result[] = array(
                'grp_id'=> $grp['id'],
                'product_id'=> $grp['product_id'],
                'order'=> $grp['_order'],
                'selection_type'=> $grp['option_type'],
                'options'=> $options
            );

        }

        return $result;
    }

    public function re_order_list($params=array()){

        $errors = array();
        $data = array();

        if(isset($params['product_id']) && $params['product_id']>0){

        }else{
            $errors[] = "err1";
        }

        if(isset($params['user_id']) && $params['user_id']>0){

        }else{
            $errors[] = "err1";
        }


        if(isset($params['list']) && !empty($params['list'])){

        }else{
            $errors[] = "err1";
        }


        if(empty($errors)){

            if(isset($params['list']))
            foreach ($params['list'] as $value){
                $this->db->where('id',intval($value['variant_id']));
                $this->db->update('variants',array(
                    '_order'=> intval($value['order'])
                ));
            }

        }

        return array(Tags::SUCCESS=>1);
    }

    public function loadGroupedVariants($product_id,$currency=DEFAULT_CURRENCY){

        $groups = $this->laodVariants($product_id);

        $grp_data = array();

        foreach ($groups as $grp){

            $grp_data[] = array(
                'group_label' => $grp['label'],
                'group_id' => $grp['id'],
                'type' => $grp['option_type'],
                'currency' =>  $this->mCurrencyModel->getCurrency($currency),
                'options' =>  $this->laodVariants($product_id,$grp['id'],$currency)
            );

        }

        return $grp_data;
    }

    public function laodVariants($product_id,$parent_id=0,$currency=DEFAULT_CURRENCY){

        if($parent_id>0)
            $this->db->where('parent_id',$parent_id);
        else
            $this->db->where('parent_id',0);


        $options = $this->db->where('product_id',$product_id)
            ->order_by('_order',"asc")->get('variants')->result_array();

        foreach ($options as $key => $value){
            $options[$key]['parsed_value'] = Currency::parseCurrencyFormat(
                $value['value'],
                $currency
            );
        }

       return $options;

    }

    public function removeVariant($params=array()){

        $errors = array();
        $data = array();

        if(isset($params['user_id']) && $params['user_id']>0){

        }else{
            $errors[] = _lang("user_id is not valid");
        }


        if(isset($params['variant_id']) && $params['variant_id']>0){
            $data['id'] = intval( $params['variant_id']);
        }else{
            $errors[] = _lang("Variant_id is not valid");
        }


        if(empty($errors)){

            $var = $this->db->where('id',$data['id'])->get('variants')->result_array();

            if(isset($var[0])){
                $this->db->where('id_product',$var[0]['product_id']);
                $this->db->where('user_id',intval($params['user_id']));
                $c = $this->db->count_all_results('product');
                if($c==0)
                    $errors[] = _lang("variant is not valid!");
            }
        }


        if(empty($errors)){

            $this->db->where($data);
            $this->db->delete('variants');

            $this->db->where("parent_id",$data['id']);
            $this->db->delete('variants');

            return array(Tags::SUCCESS=>1);

        }


        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }

    public function createOption($params=array()){


        $errors = array();
        $data = array();

        if(isset($params['user_id']) && $params['user_id']>0){

        }else{
            $errors[] = _lang("user_id is not valid");
        }

        if(isset($params['product_id']) && $params['product_id']>0){
            $data['product_id'] = intval( $params['product_id']);
        }else{
            $errors[] = _lang("Product_id is not valid");
        }


        if(isset($params['variant_id']) && $params['variant_id']>0){
            $data['parent_id'] = intval( $params['variant_id']);
        }else{
            $errors[] = _lang("Product_id is not valid");
        }


        if(isset($params['option_name']) && $params['option_name']!=""){
            $data['label'] = $params['option_name'];
        }else{
            $errors[] = _lang("Option name is not valid");
        }

        if(isset($params['option_price']) && doubleval($params['option_price'])!=0){
            $data['value'] = $params['option_price'];
        }else{
            $data['value'] = 0;
        }

        if(empty($errors)){

            $this->db->where('id_product',$data['product_id']);
            //$this->db->where('user_id',intval($params['user_id']));
            $c = $this->db->count_all_results('product');
            if($c==0)
                $errors[] = _lang("product is not exists!");

            $this->db->where('id',$data['parent_id']);
            $this->db->where('product_id',intval($data['product_id']));
            $c = $this->db->count_all_results('variants');
            if($c==0)
                $errors[] = _lang("Variant is not exists!");

        }

        if(empty($errors)){

            $data['created_at'] = date('Y-m-d H:i:s',time());
            $data['updated_at'] = date('Y-m-d H:i:s',time());

            $this->db->insert('variants',$data);

            $id = $this->db->insert_id();
            $opt = $this->db->where('id',$id)->get('variants')->result_array();
            $opt = $opt[0];

            return array(Tags::SUCCESS=>1,Tags::RESULT=>$opt);

        }

        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }

    public function createGrp($params=array()){

        $errors = array();
        $data = array();

        if(isset($params['user_id']) && $params['user_id']>0){

        }else{
            $errors[] = _lang("user_id is not valid");
        }

        if(isset($params['product_id']) && $params['product_id']>0){
            $data['product_id'] = intval( $params['product_id']);
        }else{
            $errors[] = _lang("Product_id is not valid");
        }

        if(isset($params['label']) && $params['label']!=""){
            $data['label'] = $params['label'];
        }else{
            $errors[] = _lang("Label is not valid");
        }

        if(isset($params['option_type']) && (in_array($params['option_type'],$this->type))){
            $data['option_type'] = $params['option_type'];
        }else{
            $errors[] = _lang("Options type is not valid!");
        }


        if(empty($errors)){

            $this->db->where('id_product',$data['product_id']);
            //$this->db->where('user_id',intval($params['user_id']));
            $c = $this->db->count_all_results('product');
            if($c==0)
                $errors[] = _lang("product is not exists!");

        }

        if(empty($errors)){

            $data['created_at'] = date('Y-m-d H:i:s',time());
            $data['updated_at'] = date('Y-m-d H:i:s',time());

            $this->db->insert('variants',$data);

            $id = $this->db->insert_id();
            $grp = $this->db->where('id',$id)->get('variants')->result_array();
            $grp = $grp[0];

            return array(Tags::SUCCESS=>1,Tags::RESULT=>$grp);

        }

        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }


    public function createTable()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'product_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'label' => array(
                'type' => 'VARCHAR(120)',
                'default' => NULL
            ),
            'value' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),
            'parent_id' => array(
                'type' => 'INT',
                'default' => 0
            ),
            '_order' => array(
                'type' => 'INT',
                'default' => 0
            ),
            'option_type' => array(
                'type' => 'VARCHAR(100)',
                'default' => $this->type[0]
            ),
            'updated_at' => array(
                'type' => 'DATETIME'
            ),
            'created_at' => array(
                'type' => 'DATETIME'
            ),
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('variants', TRUE, $attributes);

    }


    public function updateFields(){

        if (!$this->db->field_exists('variants', 'order_list'))
        {
            $fields = array(
                'variants'  => array('type' => 'TEXT', 'default' => ""),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('order_list', $fields);
        }

    }
    
  
}

