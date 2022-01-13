<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Location_picker_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getAddressDetail($latitude,$longitude){

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true&key=".ConfigManager::getValue("GOOGLE_PLACES_API_KEY");

        $response = MyCurl::get($url);
        $response = json_decode($response,JSON_OBJECT_AS_ARRAY);

        $addresses = array();

        if(isset($response['results'])){
            foreach ($response['results'] as $object){
                $address = array();
                foreach ($object['address_components'] as $address_component){
                    if(in_array('country',$address_component['types'],true)){
                        $address['country'] = $address_component['long_name'];
                        $address['country_code'] = $address_component['short_name'];
                    }else if(in_array('neighborhood',$address_component['types'],true)){
                        $address['district'] = $address_component['long_name'];
                    }else if(in_array('sublocality',$address_component['types'],true)){
                        $address['sublocality'] = $address_component['long_name'];
                    }else if(in_array('locality',$address_component['types'],true)){
                        $address['city'] = $address_component['long_name'];
                    }
                }


                if(!isset($address['district']))
                    $district = "";
                else
                    $district = $address['district'].", ";

                if(!isset($address['sublocality']))
                    $sublocality = "";
                else
                    $sublocality = $address['sublocality'].", ";

                if(!isset($address['city']))
                    $city = "";
                else
                    $city = $address['city'].", ";

                if(!isset($address['country']))
                    $country = "";
                else
                    $country = $address['country']."";

                if(isset($object['geometry']['location'])){
                    $address['latitude'] = $object['geometry']['location']['lat'];
                    $address['longitude'] = $object['geometry']['location']['lng'];
                }

                $address['label'] = $district.$sublocality.$city. $country;
                $address['address'] = $district.$sublocality.$city. $country;
                $addresses[] = $address;

            }
        }


        return $addresses;
    }


    public function getLocalityAutocomplete($country,$q,$language='en'){

        $data = array();

        $q = trim($q);
        $q = urlencode($q);

        $lang = strtolower($language)."_".strtoupper($language);

        $url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?language=$lang&input=".$q."&key=".ConfigManager::getValue("GOOGLE_PLACES_API_KEY")."&types=(cities)&sessiontoken=".session_id();

        $response = MyCurl::get($url);
        $response = json_decode($response,JSON_OBJECT_AS_ARRAY);

        $locations = array();

        if(isset($response['predictions'])){
            foreach ($response['predictions'] as $object){

                $country = end($object['terms']);

                $location = array(
                    'name' => $object['description'],
                    'id_location' => $object['id'],
                    'country_name' => $country['value'],
                );

                $locations[] = $location;

            }
        }

        return $locations;

    }

    public function getGooglPlacesLocality($country,$q,$language='en'){

        $addresses = array();

        $q = trim($q);
        $q = urlencode($q);

        $lang = strtolower($language)."_".strtoupper($language);

        $url = "https://maps.googleapis.com/maps/api/place/autocomplete/json?language=$lang&input=".$q."&key=".ConfigManager::getValue("GOOGLE_PLACES_API_KEY")."&types=(cities)&sessiontoken=".session_id();
        //$url = "https://maps.googleapis.com/maps/api/geocode/json?address=$q&key=".ConfigManager::getValue("GOOGLE_PLACES_API_KEY");

        $response = MyCurl::get($url);
        $response = json_decode($response,JSON_OBJECT_AS_ARRAY);

        if(isset($response['results'])){
            foreach ($response['results'] as $object){
                $address = array();
                foreach ($object['address_components'] as $address_component){
                    if(in_array('country',$address_component['types'],true)){
                        $address['country'] = $address_component['long_name'];
                        $address['country_code'] = $address_component['short_name'];
                    }else if(in_array('neighborhood',$address_component['types'],true)){
                        $address['district'] = $address_component['long_name'];
                    }else if(in_array('sublocality',$address_component['types'],true)){
                        $address['sublocality'] = $address_component['long_name'];
                    }else if(in_array('locality',$address_component['types'],true)){
                        $address['city'] = $address_component['long_name'];
                    }
                }



                if(!isset($address['district']))
                    $district = "";
                else
                    $district = $address['district'].", ";

                if(!isset($address['sublocality']))
                    $sublocality = "";
                else
                    $sublocality = $address['sublocality'].", ";

                if(!isset($address['city']))
                    $city = "";
                else
                    $city = $address['city'].", ";

                if(!isset($address['country']))
                    $country = "";
                else
                    $country = $address['country']."";

                if(isset($object['geometry']['location'])){
                    $address['latitude'] = $object['geometry']['location']['lat'];
                    $address['longitude'] = $object['geometry']['location']['lng'];
                }

                $address['label'] = $district.$sublocality.$city. $country;
                $address['address'] = $district.$sublocality.$city. $country;
                $addresses[] = $address;
            }
        }



       return $addresses;

    }


    public function getLocalitiesHEREMaps($country,$q,$language='en'){

        $url = "https://geocoder.api.here.com/6.2/geocode.json?app_id=".
            ConfigManager::getValue("LOCATION_PICKER_HERE_MAPS_APP_ID").
            "&app_code=".ConfigManager::getValue("LOCATION_PICKER_HERE_MAPS_APP_CODE").
            "&searchtext=$q&country=$country&language=".$language;

        $response = MyCurl::get($url);
        $response = json_decode($response,JSON_OBJECT_AS_ARRAY);

        if(!isset($response['Response']['View']) OR count($response['Response']['View'])==0)
            return array();

        $addresses = array();
        $result = $response['Response']['View'][0]['Result'];


        foreach ($result as $value){

            if(isset($value['MatchLevel']) && $value['MatchLevel']=="district"){
                $address = array(

                    "type" => 'district',
                    "label" => $value['Location']['Address']['Label'],
                    "city" => $value['Location']['Address']['City'],
                    "district" => $value['Location']['Address']['District'],

                    "latitude" => $value['Location']['DisplayPosition']['Latitude'],
                    "longitude" => $value['Location']['DisplayPosition']['Longitude'],

                );

                foreach ($value['Location']['Address']['AdditionalData'] as $k => $c){
                    if($c['key']=="CountryName"){
                        $address['country'] = $c['value'];
                        break;
                    }
                }


                $address['address'] = $address['district'].', '.$address['city'].', '.$address['country'];
                $addresses[] = $address;

            }else if(isset($value['MatchLevel']) && $value['MatchLevel']=="city"){

                $address = array(

                    "type" => 'city',
                    "label" => $value['Location']['Address']['Label'],
                    "city" => $value['Location']['Address']['City'],

                    "latitude" => $value['Location']['DisplayPosition']['Latitude'],
                    "longitude" => $value['Location']['DisplayPosition']['Longitude'],

                );

                foreach ($value['Location']['Address']['AdditionalData'] as $k => $c){
                    if($c['key']=="CountryName"){
                        $address['country'] = $c['value'];
                        break;
                    }
                }

                $address['address'] = $address['city'].', '.$address['country'];
                $addresses[] = $address;

            }else if(isset($value['MatchLevel']) && $value['MatchLevel']=="country"){

                $address = array(

                    "type" => 'country',
                    "label" => $value['Location']['Address']['Label'],
                    "latitude" => $value['Location']['DisplayPosition']['Latitude'],
                    "longitude" => $value['Location']['DisplayPosition']['Longitude'],
                    "city" => ""

                );

                foreach ($value['Location']['Address']['AdditionalData'] as $k => $c){
                    if($c['key']=="CountryName"){
                        $address['country'] = $c['value'];
                        break;
                    }
                }

                $address['address'] = $address['country'];
                $addresses[] = $address;

            }



        }

        return $addresses;
    }

    public function searchOnCountry($q){


        $url = "https://geocoder.api.here.com/6.2/geocode.json?app_id=".
            ConfigManager::getValue("LOCATION_PICKER_HERE_MAPS_APP_ID").
            "&app_code=".ConfigManager::getValue("LOCATION_PICKER_HERE_MAPS_APP_CODE").
            "&searchtext=$q&language=".Translate::getDefaultLangCode();

        $response = MyCurl::get($url);
        $response = json_decode($response,JSON_OBJECT_AS_ARRAY);

        if(!isset($response['Response']['View']) OR count($response['Response']['View'])==0)
            return array();

        $addresses = array();
        $result = $response['Response']['View'][0]['Result'];

        foreach ($result as $value){

            $address = array(

                "label" => $value['Location']['Address']['Label'],
                "city" => $value['Location']['Address']['City'],
                "district" => $value['Location']['Address']['District'],

                "latitude" => $value['Location']['DisplayPosition']['Latitude'],
                "longitude" => $value['Location']['DisplayPosition']['Longitude'],

            );

            foreach ($value['Location']['Address']['AdditionalData'] as $k => $c){
                if($c['key']=="CountryName"){
                    $address['country'] = $c['value'];
                    break;
                }
            }


            $address['address'] = $address['district'].', '.$address['city'].', '.$address['country'];

            $addresses[] = $address;
        }



        return $addresses;
    }


}

