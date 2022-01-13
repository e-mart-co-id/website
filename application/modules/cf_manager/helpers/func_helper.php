<?php


class CFManagerHelper{


    public static function getTypeByID($cf_id, $label){

        $ctx = &get_instance();
        $ctx->db->where('id',$cf_id);
        $field = $ctx->db->get("cf_list",1);
        $field = $field->result_array();


        if(isset($field[0])){

            $fields = $field[0]['fields'];
            $fields = json_decode($fields,JSON_OBJECT_AS_ARRAY);

            foreach ($fields as $k => $value){

                if(isset($value['label']) && $value['label']==$label){
                    return $value['type'];
                }
            }
        }

        return "";
    }

    public static function getByID($cf_id){

        $ctx = &get_instance();
        $ctx->db->where('id',$cf_id);
        $field = $ctx->db->get("cf_list",1);
        $field = $field->result_array();

        if(isset($field[0])){
            return $field[0];
        }

        return NULL;
    }


    public static function re_order($array,$fields){

        $data = array();

        foreach ($fields as $field){
            $data[ $field['order'] ] = $array[ $field['label'] ];
        }

        return $data;
    }




}