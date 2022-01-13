<?php


class OrderHelper{

    public static function variantsBuilderString($variants){

        $string = "";

        if(!is_array($variants))
            $variants = json_decode($variants);

        if(!empty($variants))
            $string = "<br />";


        foreach ($variants as $grp_label => $options){

            $options = json_encode($options);
            $options = json_decode($options,JSON_OBJECT_AS_ARRAY);

            if(!empty($options))
                $string .= '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'.$grp_label.':<br/>';

            echo "</pre>";
               foreach ($options as $key => $value){
                   $string .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$key."</i><br />";
                }
        }

        return $string;
    }

}