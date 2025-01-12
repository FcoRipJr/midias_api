<?php
require_once __DIR__."/../../src/dao/midiadao.php";

class MidiaController {
    public static function get($get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $genres = ($get_fields->genres ?? "" ) == "true" ? true : false;
        $users = ($get_fields->users ?? "" ) == "true" ? true : false;
        $comments = ($get_fields->comments ?? "" ) == "true" ? true : false;
        $filter = $get_fields;
        $pages = 1;
        $page = Models::isInt($filter->page??null) ? $filter->page : null;
        $data = MidiaDAO::get($filter,$genres, $users, $comments);
        $total = count($data);
        $rows = count($data);
        $page_limit = $total;
        if(!empty($page)){
            $page_limit = Models::isInt($filter->page_limit??null) ? $filter->page_limit : 10;
            $total_obj = MidiaDAO::get_count($filter,$page_limit);
            $total = $total_obj->total;
            $pages = $total_obj->pages;
        }
        $response->response_data = (object)["data"=>$data,"rows"=>$rows,"total"=>$total,"page"=>$page??1,"pages"=>$pages,"page_limit"=>$page_limit];
        return $response;
    }

    public static function find($id,$get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $genres = ($get_fields->genres ?? "" ) == "true" ? true : false;
        $users = ($get_fields->users ?? "" ) == "true" ? true : false;
        $comments = ($get_fields->comments ?? "" ) == "true" ? true : false;
        $response->response_data = MidiaDAO::find($id,$genres, $users, $comments);
        return $response;
    }

    public static function post($body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate($body_fields);
        if($validation->status===true){
            $midia = Models::midia();
            $midia->id_category = $body_fields->id_category;
            $midia->title = $body_fields->title;
            $midia->id_main_midia = $body_fields->id_main_midia??null;
            $midia->description = $body_fields->description??null;
            $midia->release_year = $body_fields->release_year??null;
            $midia->release_month = $body_fields->release_month??null;
            $midia->release_day = $body_fields->release_day??null;
            $id = MidiaDAO::insert($midia);
            if($id){
                $validation->code = $response->response_code;
                $validation->msg = "midia inserted";
                $validation->id = $id;
                $validation->midia = MidiaDAO::find($id);
                $response->response_data = $validation;
            } else {
                $response->response_code = 417;
                $response->response_data = (object)["status"=>false,"code"=>$response->response_code, "msg"=>"error on insert"];
            }
        } else{
            $response->response_code = 400;
            $validation->code = $response->response_code;
            $response->response_data = $validation;
        }
        return $response;
    }

    public static function validate($body_fields){
        $validate = true;
        $msg = '';
        $id_category = $body_fields->id_category??null;
        $title = $body_fields->title??null;
        $release_year = $body_fields->release_year??"";
        $release_month = $body_fields->release_month??"";
        $release_day = $body_fields->release_day??"";
        $id_main_midia = $body_fields->id_main_midia??null;
        if(empty($id_category)){
            $validate = false;
            $msg = 'id_category is required';
        } else if(!Models::isInt($id_category)){
            $validate = false;
            $msg = 'id_category invalid';
        } else if(empty($title)){
            $validate = false;
            $msg = 'title is required';
        } else if($release_year != "" && strlen($release_year) != 4){
            $validate = false;
            $msg = 'release_year invalid';
        } else if($release_month != "" && strlen($release_month) != 2){
            $validate = false;
            $msg = 'release_month invalid';
        } else if($release_day != "" && strlen($release_day) != 2){
            $validate = false;
            $msg = 'release_day invalid';
        } else if( (!empty($id_main_midia)) && (!Models::isInt($id_main_midia)) ){
            $validate = false;
            $msg = 'id_main_midia invalid';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function put($id,$body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_put($id,$body_fields);
        if($validation->status===true){
            $midia = MidiaDAO::find($id);
            $midia->title = $body_fields->title;
            $midia->description = $body_fields->description ?? null;
            $midia->release_year = $body_fields->release_year ?? null;
            $midia->release_month = $body_fields->release_month ?? null;
            $midia->release_day = $body_fields->release_day ?? null;
            $updated_rows = MidiaDAO::update($midia);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->msg = 'midia updated';
                $validation->id = $id;
                $validation->midia = $midia;
                $response->response_data = $validation;
            } else {
                $response->response_code = 417;
                $response->response_data = (object)["status"=>false,"code"=>$response->response_code, "msg"=>"error on update"];
            }
        } else{
            $response->response_code = 400;
            $validation->code = $response->response_code;
            $response->response_data = $validation;
        }
        return $response;
    }

    public static function validate_put($id,$body_fields){
        $validate = true;
        $msg = '';
        $title = $body_fields->title??null;
        $release_year = $body_fields->release_year??"";
        $release_month = $body_fields->release_month??"";
        $release_day = $body_fields->release_day??"";
        if(empty($id)){
            $validate = false;
            $msg = 'id is required';
        } else if(!Models::isInt($id)){
            $validate = false;
            $msg = 'id invalid';
        } else if(empty($title)){
            $validate = false;
            $msg = 'title is required';
        } else if($release_year != "" && strlen($release_year) != 4){
            $validate = false;
            $msg = 'release_year invalid';
        } else if($release_month != "" && strlen($release_month) != 2){
            $validate = false;
            $msg = 'release_month invalid';
        } else if($release_day != "" && strlen($release_day) != 2){
            $validate = false;
            $msg = 'release_day invalid';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function delete($id){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_delete($id);
        if($validation->status===true){
            $updated_rows = MidiaDAO::delete($id);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->id = $id;
                $validation->msg = 'midia deleted';
                $response->response_data = $validation;
            } else {
                $response->response_code = 417;
                $response->response_data = (object)["status"=>false,"code"=>$response->response_code, "msg"=>"error on delete"];
            }
        } else{
            $response->response_code = 400;
            $validation->code = $response->response_code;
            $response->response_data = $validation;
        }
        return $response;
    }

    public static function validate_delete($id){
        $validate = true;
        $msg = '';
        if(empty($id)){
            $validate = false;
            $msg = 'id is required';
        } else if(!Models::isInt($id)){
            $validate = false;
            $msg = 'id invalid';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function post_genre($id_midia, $body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $body_fields->id_midia = $id_midia;
        $validation = self::validate_genre($body_fields);
        if($validation->status===true){
            $id_midia = $body_fields->id_midia;
            $id_genre = $body_fields->id_genre;
            $main = ($body_fields->main??'false') == "true";
            $updated_rows = MidiaDAO::insert_genre($id_midia,$id_genre,$main);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->msg = "midia_genre inserted";
                $validation->id_midia = $id_midia;
                $validation->id_genre = $id_genre;
                $validation->main = $main;
                $validation->midia = MidiaDAO::find($id_midia,true);
                $response->response_data = $validation;
            } else {
                $response->response_code = 417;
                $response->response_data = (object)["status"=>false,"code"=>$response->response_code, "msg"=>"error on insert"];
            }
        } else{
            $response->response_code = 400;
            $validation->code = $response->response_code;
            $response->response_data = $validation;
        }
        return $response;
    }

    public static function validate_genre($body_fields){
        $validate = true;
        $msg = '';
        $id_genre = $body_fields->id_genre??null;
        $id_midia = $body_fields->id_midia??null;
        $main = $body_fields->main??'false';
        if(empty($id_midia)){
            $validate = false;
            $msg = 'id_midia is required';
        } else if(!Models::isInt($id_midia)){
            $validate = false;
            $msg = 'id_midia invalid';
        } else if(empty($id_genre)){
            $validate = false;
            $msg = 'id_genre is required';
        } else if(!Models::isInt($id_genre)){
            $validate = false;
            $msg = 'id_genre invalid';
        } else if( !in_array($main,['true','false']) ){
            $validate = false;
            $msg = 'main invalid';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function put_genre($id_midia, $id_genre, $body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_genre_put($id_midia, $id_genre,$body_fields);
        if($validation->status===true){
            $main = ($body_fields->main??'false') == "true";
            $updated_rows = MidiaDAO::update_genre($id_midia, $id_genre,$main);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->msg = 'midia_genre updated';
                $validation->id_midia = $id_midia;
                $validation->id_genre = $id_genre;
                $validation->main = $main;
                $validation->midia = MidiaDAO::find($id_midia,true);
                $response->response_data = $validation;
            } else {
                $response->response_code = 417;
                $response->response_data = (object)["status"=>false,"code"=>$response->response_code, "msg"=>"error on update"];
            }
        } else{
            $response->response_code = 400;
            $validation->code = $response->response_code;
            $response->response_data = $validation;
        }
        return $response;
    }

    public static function validate_genre_put($id_midia, $id_genre, $body_fields){
        $validate = true;
        $msg = '';
        $main = $body_fields->main??'false';
        if(empty($id_midia)){
            $validate = false;
            $msg = 'id_midia is required';
        } else if(!Models::isInt($id_midia)){
            $validate = false;
            $msg = 'id_midia invalid';
        }else if(empty($id_genre)){
            $validate = false;
            $msg = 'id_genre is required';
        } else if(!Models::isInt($id_genre)){
            $validate = false;
            $msg = 'id_genre invalid';
        } else if( !in_array($main,['true','false']) ){
            $validate = false;
            $msg = 'main invalid';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function delete_genre($id_midia, $id_genre){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_delete_genre($id_midia, $id_genre);
        if($validation->status===true){
            $updated_rows = MidiaDAO::delete_genre($id_midia, $id_genre);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->id_midia = $id_midia;
                $validation->id_genre = $id_genre;
                $validation->msg = 'midia_genre deleted';
                $response->response_data = $validation;
            } else {
                $response->response_code = 417;
                $response->response_data = (object)["status"=>false,"code"=>$response->response_code, "msg"=>"error on delete"];
            }
        } else{
            $response->response_code = 400;
            $validation->code = $response->response_code;
            $response->response_data = $validation;
        }
        return $response;
    }

    public static function validate_delete_genre($id_midia, $id_genre){
        $validate = true;
        $msg = '';
        if(empty($id_midia)){
            $validate = false;
            $msg = 'id_midia is required';
        } else if(!Models::isInt($id_midia)){
            $validate = false;
            $msg = 'id_midia invalid';
        } else if(empty($id_genre)){
            $validate = false;
            $msg = 'id_genre is required';
        } else if(!Models::isInt($id_genre)){
            $validate = false;
            $msg = 'id_genre invalid';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }
}