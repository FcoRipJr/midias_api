<?php
require_once __DIR__."/../../src/dao/commentdao.php";

class CommentController {
    public static function get($get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $filter = $get_fields;
        $pages = 1;
        $page = Models::isInt($filter->page??null) ? $filter->page : null;
        $data = CommentDAO::get($filter);
        $total = count($data);
        $rows = count($data);
        $page_limit = $total;
        if(!empty($page)){
            $page_limit = Models::isInt($filter->page_limit??null) ? $filter->page_limit : 10;
            $total_obj = CommentDAO::get_count($filter,$page_limit);
            $total = $total_obj->total;
            $pages = $total_obj->pages;
        }
        $response->response_data = (object)["data"=>$data,"rows"=>$rows,"total"=>$total,"page"=>$page??1,"pages"=>$pages,"page_limit"=>$page_limit];
        return $response;
    }

    public static function find($id,$get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $response->response_data = CommentDAO::find($id);
        return $response;
    }

    public static function post($body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate($body_fields);
        if($validation->status===true){
            $comment = Models::comment();
            $comment->id_user = $body_fields->id_user;
            $comment->id_midia = $body_fields->id_midia;
            $comment->text = $body_fields->text;
            $comment->id_session = $body_fields->id_session ?? null;
            $id = CommentDAO::insert($comment);
            if($id){
                $validation->code = $response->response_code;
                $validation->msg = "comment inserted";
                $validation->id = $id;
                $validation->comment = CommentDAO::find($id);
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
            $comment = CommentDAO::find($id);
            $comment->text = $body_fields->text;
            $updated_rows = CommentDAO::update($comment);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->msg = 'comment updated';
                $validation->id = $id;
                $validation->comment = $comment;
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
        $text = $body_fields->text??null;
        if(empty($id)){
            $validate = false;
            $msg = 'id is required';
        } else if(!Models::isInt($id)){
            $validate = false;
            $msg = 'id invalid';
        }if(empty($text)){
            $validate = false;
            $msg = 'text is required';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function delete($id){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_delete($id);
        if($validation->status===true){
            $updated_rows = CommentDAO::delete($id);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->id = $id;
                $validation->msg = 'comment deleted';
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
        $id_session = $body_fields->id_session??null;
        $text = $body_fields->text??null;
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
        } else if(!Models::isInt($id_session)){
            $validate = false;
            $msg = 'id_session invalid';
        } else if(empty($text)){
            $validate = false;
            $msg = 'text is required';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }
}