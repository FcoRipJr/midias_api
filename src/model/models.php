<?php

class Models {
    public static function user(){
        return (object)[
            "id" => null,
            "name" => null,
            "password" => null,
            "status" => null,
            "created" => null,
            "updated" => null,
            "created_formated" => null,
            "updated_formated" => null,
            "midias" => [],
            "sessions" => [],
            "comments" => [],
        ];
    }

    public static function completion(){
        return(object)[
            "id" => null,
            "description" => null,
            "order" => null,
            "created" => null,
            "updated" => null,
            "created_formated" => null,
            "updated_formated" => null,
        ];
    }
    
    public static function category(){
        return(object)[
            "id" => null,
            "description" => null,
            "created" => null,
            "updated" => null,
            "created_formated" => null,
            "updated_formated" => null,
            "midias" => [],
            "genres" => [],
        ];
    }

    public static function genre(){
        return(object)[
            "id" => null,
            "id_category" => null,
            "category" => null,
            "description" => null,
            "created" => null,
            "updated" => null,
            "created_formated" => null,
            "updated_formated" => null,
            "midias" => [],
        ];
    }

    public static function midia(){
        return(object)[
            "id" => null,
            "id_category" => null,
            "id_main_midia" => null,
            "category" => null,
            "main_midia" => null,
            "title" => null,
            "description" => null,
            "release_year" => null,
            "release_month" => null,
            "release_day" => null,
            "release" => null,
            "created" => null,
            "updated" => null,
            "release_formated" => null,
            "created_formated" => null,
            "updated_formated" => null,
            "users" => [],
            "genres" => [],
            "comments" => [],
        ];
    }

    public static function user_midia(){
        return(object)[
            "id" => null,
            "id_user" => null,
            "id_midia" => null,
            "id_completion" => null,
            "user" => null,
            "midia" => null,
            "completion" => null,
            "score" => null,
            "created" => null,
            "updated" => null,
            "created_formated" => null,
            "updated_formated" => null,
            "comments" => [],
        ];
    }

    public static function session(){
        return(object)[
            "id" => null,
            "id_user" => null,
            "id_midia" => null,
            "user" => null,
            "midia" => null,
            "code" => null,
            "start" => null,
            "end" => null,
            "created" => null,
            "updated" => null,
            "start_formated" => null,
            "end_formated" => null,
            "created_formated" => null,
            "updated_formated" => null,
            "comments" => [],
        ];
    }

    
    public static function comment(){
        return(object)[
            "id" => null,
            "id_user" => null,
            "id_midia" => null,
            "id_session" => null,
            "user" => null,
            "midia" => null,
            "session" => null,
            "text" => null,
            "created" => null,
            "updated" => null,
            "created_formated" => null,
            "updated_formated" => null,
        ];
    }

    public static function convert_date($date, $short = false){
        $new_date = null;
        if(!empty($date)){
            $format = $short ? "d/m/Y" : "d/m/Y h:d:s";
            $new_date = date($format,strtotime($date));
        }
        return $new_date;
    }

    public static function get_release($midia,$format = false){
        $release = null;
        $release_year = $midia->release_year??null;
        $release_month = $midia->release_month??null;
        $release_day = $midia->release_day??null;
        if( (!empty($release_year)) && (!empty($release_month)) && (!empty($release_day)) ){
            $release = $format ? "$release_day/$release_month/$release_year" : "$release_year-$release_month-$release_day";
        } else if( (!empty($release_year)) && (!empty($release_month)) && (empty($release_day)) ){
            $release = $format ? "$release_month/$release_year" : "$release_year-$release_month";
        } else if( (!empty($release_year)) && (empty($release_month)) && (empty($release_day)) ){
            $release = $release_year;
        } else if( (empty($release_year)) && (!empty($release_month)) && (!empty($release_day)) ){
            $release = $format ? "$release_day/$release_month" : "$release_month-$release_day";
        } else if( (empty($release_year)) && (!empty($release_month)) && (empty($release_day)) ){
            $release = self::get_month($release_month);
        } else if( (empty($release_year)) && (empty($release_month)) && (!empty($release_day)) ){
            $release = $release_day;
        }
        return $release;
    }

    public static function get_month($month, $lang = 'en', $short = false){
        $months = [
            "en"=>[
                "#01" => "January",
                "#02" => "February",
                "#03" => "March",
                "#04" => "April",
                "#05" => "May",
                "#06" => "June",
                "#07" => "July",
                "#08" => "August",
                "#09" => "September",
                "#10" => "October",
                "#11" => "November",
                "#12" => "December",
            ],
            "pt-br"=>[
                "#01" => "Janeiro",
                "#02" => "Fevereiro",
                "#03" => "Março",
                "#04" => "Abril",
                "#05" => "Maio",
                "#06" => "Junho",
                "#07" => "Julho",
                "#08" => "Agonto",
                "#09" => "Setembro",
                "#10" => "Outubro",
                "#11" => "Novembro",
                "#12" => "Dezembro",
            ],
        ];
        return $short ? (substr($months[$lang]["#$month"]??"",0,3)??"")  : ($months[$lang]["#$month"]??"");
    }

    public static function isInt($value) {
        return is_numeric($value) && floatval(intval($value)) === floatval($value);
    }

}
