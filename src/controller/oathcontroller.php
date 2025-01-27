<?php
require_once __DIR__."/../../src/dao/oauthdao.php";

class OAuthController {
    public static function post($body_fields){
        $response = (object) ["response_code"=>401, "response_data" => []];
        $validation = self::validate($body_fields);
        if($validation->status===true){
            $oauth_access_token = Models::oauth_access_token();
            $oauth_access_token->access_token = OAuthDAO::create_jwt_token($body_fields);
            $oauth_access_token->client_id = $body_fields->client_id;
            $oauth_access_token->id_user = $body_fields->id_user??null;
            $oauth_access_token->scope = $body_fields->scope??null;
            OAuthDAO::insert_oauth_access_token($oauth_access_token);
            $oauth_access_token = OAuthDAO::find_oauth_access_token($oauth_access_token->access_token);
            if(!empty($oauth_access_token->access_token??null)){
                $env = parse_ini_file(__DIR__."/../../.env");
                $response->response_code = 200;
                $validation->code = $response->response_code;
                $validation->msg = "token created";
                $validation->token = $oauth_access_token->access_token;
                $validation->token_type = $env["TOKEN_TYPE"];
                $response->response_data = $validation;
            } else {
                $response->response_code = 417;
                $response->response_data = (object)["status"=>false,"code"=>$response->response_code, "msg"=>"error on creation"];
            }
        } else{
            $validation->code = $response->response_code;
            $response->response_data = $validation;
        }
        return $response;
    }

    public static function validate($body_fields){
        $validate = false;
        $msg = '';
        $client_id = $body_fields->client_id??null;
        $client_secret = $body_fields->client_secret??null;
        $grant_type = $body_fields->grant_type??null;
        if(empty($client_id)){
            $msg = 'client_id is required';
        } else if(empty($client_secret)){
            $msg = 'client_secret is required';
        } else if(!OAuthDAO::check_client_credentials($client_id, $client_secret, $grant_type)){
            $msg = 'invalid credentials';
        } else {
            $validate = true;
        }
        return (object)["status"=>$validate,"msg"=>$msg];
    }

    public static function check_Autorization($Autorization){
        $response = (object) ["status"=>false, "code"=>401, "msg" => "Authorization token is missing"];
        $env = parse_ini_file(__DIR__."/../../.env");
        $access_token = str_replace(($env["TOKEN_TYPE"]." "),"",$Autorization);
        if(!empty($access_token)){
            $oauth_access_token = OAuthDAO::find_oauth_access_token($access_token);
            if(!empty($oauth_access_token->access_token??null)){
                if($oauth_access_token->expired??true){
                    $response->msg = "Authorization token expired";
                } else {
                    $response->code = 200;
                    $response->status = true;
                    $response->msg = "Authorization token valid";
                }
            }
        }
        return $response;
    }
}