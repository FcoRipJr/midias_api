<?php
require_once __DIR__."/../../src/dao/sessiondao.php";

class SessionController {
    public static function get($get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $coments = ($get_fields->coments ?? "" ) == "true" ? true : false;
        $filter = $get_fields;
        $pages = 1;
        $page = Models::isInt($filter->page??null) ? $filter->page : null;
        $data = SessionDAO::get($filter, $coments);
        $total = count($data);
        $rows = count($data);
        $page_limit = $total;
        if(!empty($page)){
            $page_limit = Models::isInt($filter->page_limit??null) ? $filter->page_limit : 10;
            $total_obj = SessionDAO::get_count($filter,$page_limit);
            $total = $total_obj->total;
            $pages = $total_obj->pages;
        }
        $response->response_data = (object)["data"=>$data,"rows"=>$rows,"total"=>$total,"page"=>$page??1,"pages"=>$pages,"page_limit"=>$page_limit];
        return $response;
    }

    public static function find($id,$get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $coments = ($get_fields->coments ?? "" ) == "true" ? true : false;
        $response->response_data = SessionDAO::find($id, $coments);
        return $response;
    }

    public static function post($body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate($body_fields);
        if($validation->status===true){
            $session = Models::session();
            $session->id_user = $body_fields->id_user;
            $session->id_midia = $body_fields->id_midia;
            $session->start = $body_fields->start ?? null;
            $session->end = $body_fields->end ?? null;
            $id = SessionDAO::insert($session);
            if($id){
                $validation->code = $response->response_code;
                $validation->id = $id;
                $validation->session = SessionDAO::find($id);
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
        $id_user = $body_fields->id_user??null;
        $id_midia = $body_fields->id_midia??null;
        $start = $body_fields->start??null;
        $end = $body_fields->end??null;
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
        } else if((!empty($start)) && (strlen($start) != 10) && (strlen($start) != 19) ){
            $validate = false;
            $msg = 'start invalid';
        } else if((!empty($end)) && (strlen($end) != 10) && (strlen($end) != 19) ){
            $validate = false;
            $msg = 'end invalid';
        } else if( (!empty($start)) && (!empty($end)) && (strtotime($start)>strtotime($end)) ){
            $validate = false;
            $msg = 'start greater than end';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function put($id,$body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_put($id,$body_fields);
        if($validation->status===true){
            $session = SessionDAO::find($id);
            $session->start = $body_fields->start ?? null;
            $session->end = $body_fields->end ?? null;
            $updated_rows = SessionDAO::update($session);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->msg = 'session updated';
                $validation->id = $id;
                $validation->session = $session;
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
        $start = $body_fields->start??null;
        $end = $body_fields->end??null;
        if(empty($id)){
            $validate = false;
            $msg = 'id is required';
        } else if(!Models::isInt($id)){
            $validate = false;
            $msg = 'id invalid';
        } else if((!empty($start)) && (strlen($start) != 10) && (strlen($start) != 19) ){
            $validate = false;
            $msg = 'start invalid';
        } else if((!empty($end)) && (strlen($end) != 10) && (strlen($end) != 19) ){
            $validate = false;
            $msg = 'end invalid';
        } else if( (!empty($start)) && (!empty($end)) && (strtotime($start)>strtotime($end)) ){
            $validate = false;
            $msg = 'start greater than end';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function delete($id){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_delete($id);
        if($validation->status===true){
            $updated_rows = SessionDAO::delete($id);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->id = $id;
                $validation->msg = 'session deleted';
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
}