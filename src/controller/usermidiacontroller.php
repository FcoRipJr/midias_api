<?php
require_once __DIR__."/../../src/dao/usermidiadao.php";

class UserMidiaController {
    public static function get($get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $comments = ($get_fields->comments ?? "" ) == "true" ? true : false;        
        $filter = $get_fields;
        $pages = 1;
        $page = Models::isInt($filter->page??null) ? $filter->page : null;
        $data = UserMidiaDAO::get($filter, $comments);
        $total = count($data);
        $rows = count($data);
        $page_limit = $total;
        if(!empty($page)){
            $page_limit = Models::isInt($filter->page_limit??null) ? $filter->page_limit : 10;
            $total_obj = UserMidiaDAO::get_count($filter,$page_limit);
            $total = $total_obj->total;
            $pages = $total_obj->pages;
        }
        $response->response_data = (object)["data"=>$data,"rows"=>$rows,"total"=>$total,"page"=>$page??1,"pages"=>$pages,"page_limit"=>$page_limit];
        return $response;
    }

    public static function find($id,$get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $comments = ($get_fields->comments ?? "" ) == "true" ? true : false;
        $response->response_data = UserMidiaDAO::find($id, $comments);
        return $response;
    }

    public static function post($body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate($body_fields);
        if($validation->status===true){
            $user_midia = Models::user_midia();
            $user_midia->id_user = $body_fields->id_user;
            $user_midia->id_midia = $body_fields->id_midia;
            $user_midia->id_completion = $body_fields->id_completion ?? null;
            $user_midia->score = $body_fields->score ?? null;
            $id = UserMidiaDAO::insert($user_midia);
            if($id){
                $validation->code = $response->response_code;
                $validation->id = $id;
                $validation->user_midia = UserMidiaDAO::find($id);
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

    public static function put($id,$body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_put($id,$body_fields);
        if($validation->status===true){
            $user_midia = UserMidiaDAO::find($id);
            $user_midia->id_completion = $body_fields->id_completion ?? null;
            $user_midia->score = $body_fields->score ?? null;
            $updated_rows = UserMidiaDAO::update($user_midia);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->msg = 'user_midia updated';
                $validation->id = $id;
                $validation->user_midia = $user_midia;
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
        $score = $body_fields->score??null;
        $id_completion = $body_fields->id_completion??null;
        if(empty($id)){
            $validate = false;
            $msg = 'id is required';
        } else if(!Models::isInt($id)){
            $validate = false;
            $msg = 'id invalid';
        } else if( (!empty($score)) && (intval($score)<0||intval($score)>100) ){
            $validate = false;
            $msg = 'score invalid';
        } else if( (!empty($id_completion)) && (!Models::isInt($id_completion)) ){
            $validate = false;
            $msg = 'id_completion invalid';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function delete($id){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_delete($id);
        if($validation->status===true){
            $updated_rows = UserMidiaDAO::delete($id);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->id = $id;
                $validation->msg = 'user_midia deleted';
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
    
    public static function validate($body_fields){
        $validate = true;
        $msg = '';
        $id_user = $body_fields->id_user??null;
        $id_midia = $body_fields->id_midia??null;
        $score = $body_fields->score??null;
        $id_completion = $body_fields->id_completion??null;
        if(empty($id_user)){
            $validate = false;
            $msg = 'id_user is required';
        } else if(!Models::isInt($id_user)){
            $validate = false;
            $msg = 'id_user invalid';
        } else if(empty($id_midia)){
            $validate = false;
            $msg = 'id_midia is required';
        } else if(!Models::isInt($id_midia)){
            $validate = false;
            $msg = 'id_midia invalid';
        } else if( (!empty($score)) && (intval($score)<0||intval($score)>100) ){
            $validate = false;
            $msg = 'score invalid';
        } else if( (!empty($id_completion)) && (!Models::isInt($id_completion)) ){
            $validate = false;
            $msg = 'id_completion invalid';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    
}