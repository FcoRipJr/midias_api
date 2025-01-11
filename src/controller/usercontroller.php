<?php
require_once __DIR__."/../../src/dao/userdao.php";

class UserController {
    public static function get($get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $midias = ($get_fields->midias ?? "" ) == "true" ? true : false;
        $sessions = ($get_fields->sessions ?? "" ) == "true" ? true : false;
        $comments = ($get_fields->comments ?? "" ) == "true" ? true : false;
        $filter = $get_fields;
        $pages = 1;
        $page = Models::isInt($filter->page??null) ? $filter->page : null;
        $data = UserDAO::get($filter,$midias, $sessions, $comments);
        $total = count($data);
        $rows = count($data);
        $page_limit = $total;
        if(!empty($page)){
            $page_limit = Models::isInt($filter->page_limit??null) ? $filter->page_limit : 10;
            $total_obj = UserDAO::get_count($filter,$page_limit);
            $total = $total_obj->total;
            $pages = $total_obj->pages;
        }
        $response->response_data = (object)["data"=>$data,"rows"=>$rows,"total"=>$total,"page"=>$page??1,"pages"=>$pages,"page_limit"=>$page_limit];
        return $response;
    }

    public static function find($id,$get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $midias = ($get_fields->midias ?? "" ) == "true" ? true : false;
        $sessions = ($get_fields->sessions ?? "" ) == "true" ? true : false;
        $comments = ($get_fields->comments ?? "" ) == "true" ? true : false;
        $response->response_data = UserDAO::find($id,$midias, $sessions, $comments);
        return $response;
    }

    public static function post($body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate($body_fields);
        if($validation->status===true){
            $user = Models::user();
            $user->name = $body_fields->name;
            $user->password = empty($body_fields->password??null) ? null : md5(md5($body_fields->password));
            $id = UserDAO::insert($user);
            if($id){
                $validation->code = $response->response_code;
                $validation->id = $id;
                $validation->user = UserDAO::find($id);
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
        $name = $body_fields->name??null;
        if(empty($name)){
            $validate = false;
            $msg = 'name is required';
        } else if(count(UserDAO::get((object)["name"=>$name]))>0){
            $validate = false;
            $msg = 'name already registered';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function put($id,$body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_put($id,$body_fields);
        if($validation->status===true){
            $user = UserDAO::find($id);
            $user->name = $body_fields->name;
            $user->status = $body_fields->status;
            $updated_rows = UserDAO::update($user);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->msg = 'user updated';
                $validation->id = $id;
                $validation->user = $user;
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
        $name = $body_fields->name??null;
        $status = $body_fields->status??null;
        if(empty($id)){
            $validate = false;
            $msg = 'id is required';
        } else if(!Models::isInt($id)){
            $validate = false;
            $msg = 'id invalid';
        } else if(empty($name)){
            $validate = false;
            $msg = 'name is required';
        } else if(count(UserDAO::get((object)["name"=>$name,"id_not_in"=>$id]))>0){
            $validate = false;
            $msg = 'name already registered';
        } else if(empty($status)){
            $validate = false;
            $msg = 'status is required';
        } else if(!in_array($status, ['created','active','inactive','suspended','deleted'])){
            $validate = false;
            $msg = 'status invalid';
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function delete($id){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_delete($id);
        if($validation->status===true){
            $updated_rows = UserDAO::delete($id);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->id = $id;
                $validation->msg = 'user deleted';
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

    public static function login($body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_login($body_fields);
        if($validation->status===true){
            $user = UserDAO::login($body_fields->name,$body_fields->password, true, true);
            if(!empty($user->id??null)){
                $validation->code = $response->response_code;
                $validation->user = $user;
                $response->response_data = $validation;
            } else {
                $response->response_code = 401;
                $response->response_data = (object)["status"=>false,"code"=>$response->response_code, "msg"=>"bad credentials"];
            }
        } else{
            $response->response_code = 400;
            $validation->code = $response->response_code;
            $response->response_data = $validation;
        }
        return $response;
    }

    public static function validate_login($body_fields){
        $validate = true;
        $msg = '';
        $name = $body_fields->name;
        $password = empty($body_fields->password??null) ? null : md5(md5($body_fields->password));
        if(empty($name)){
            $validate = false;
            $msg = 'name required';
        } else if(empty($password)){
            $validate = false;
            $msg = 'password required';
        }
        return (object)["status"=>$validate,"msg"=>$msg]; 
    }

    public static function change_password($body_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $validation = self::validate_login($body_fields);
        if($validation->status===true){
            $user = Models::user();
            $user->id = $body_fields->id;
            $user->password = md5(md5($body_fields->new_password));
            $updated_rows = UserDAO::update_password($user);
            if($updated_rows){
                $validation->code = $response->response_code;
                $validation->msg = 'password updated';
                $validation->user = UserDAO::find($user->id);
                $response->response_data = $validation;
            } else {
                $response->response_code = 401;
                $response->response_data = (object)["status"=>false,"code"=>$response->response_code, "msg"=>"bad credentials"];
            }
        } else{
            $response->response_code = 400;
            $validation->code = $response->response_code;
            $response->response_data = $validation;
        }
        return $response;
    }

    public static function validate_change_password($body_fields){
        $validate = true;
        $msg = '';
        $id_user = $body_fields->id_user ?? null;
        $old_password = empty($body_fields->old_password??null) ? null : md5(md5($body_fields->old_password));
        $new_password = empty($body_fields->new_password??null) ? null : md5(md5($body_fields->new_password));
        if(empty($id_user)){
            $validate = false;
            $msg = 'id_user required';
        } else if(!Models::isInt($id_user)){
            $validate = false;
            $msg = 'id_user invalid';
        } else if(empty($new_password)){
            $validate = false;
            $msg = 'new_password required';
        } else if($old_password == $new_password){
            $validate = false;
            $msg = 'new_password iquals to old_password';
        } else {
            $user = UserDAO::find_password($id_user);
            if( (!empty($user->password)) && ($user->password != $old_password) ){
                $msg = 'old_password invalid';
            }
        }
        return (object)["status"=>$validate,"msg"=>$msg]; 
    }
}