<?php
require_once __DIR__."/../../src/controller/controllers.php";

class Router {
    public static function redirect($uri, $method, $body_fields, $get_fields, $header_fields){
        $http_response_code = 404;
        $http_response = (object)[];
        try {
            if ($method == 'POST' && $uri == '/token'){
                $response = OAuthController::post($body_fields);
                $http_response_code = $response->response_code;
                $http_response = $response->response_data;
            } else{
                $token_validation = self::check_credentials($header_fields);
                if(!($token_validation->status??false)){
                    $http_response->status = $token_validation->status;
                    $http_response->code = $token_validation->code;
                    $http_response->msg = $token_validation->msg;
                    $http_response_code = $http_response->code;
                } else {
                    switch ($method | $uri) {
                        ///completions
                        case ($method == 'GET' && $uri == '/completions'):
                            $response = CompletionController::get($get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'GET' && preg_match('/\/completions\/\d+/', $uri)):
                            $response = CompletionController::find(self::get_id_uri($uri, '/completions/'),$get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                    
                        ///category
                        case ($method == 'GET' && $uri == '/categories'):
                            $response = CategoryController::get($get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'GET' && preg_match('/\/categories\/\d+/', $uri)):
                            $response = CategoryController::find(self::get_id_uri($uri, '/categories/'),$get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
        
                        ///genres
                        case ($method == 'GET' && $uri == '/genres'):
                            $response = GenreController::get($get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'GET' && preg_match('/\/genres\/\d+/', $uri)):
                            $response = GenreController::find(self::get_id_uri($uri, '/genres/'),$get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                    
                        ///users
                        case ($method == 'GET' && $uri == '/users'):
                            $response = UserController::get($get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'GET' && preg_match('/\/users\/\d+/', $uri)):
                            $response = UserController::find(self::get_id_uri($uri, '/users/'), $get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'POST' && $uri == '/users'):
                            $response = UserController::post($body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'PUT' && preg_match('/\/users\/\d+/', $uri)):
                            $response = UserController::put(self::get_id_uri($uri, '/users/'),$body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'DELETE' && preg_match('/\/users\/\d+/', $uri)):
                            $response = UserController::delete(self::get_id_uri($uri, '/users/'));
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                    
                        ///midias
                        case ($method == 'POST' && preg_match('/\/midias\/\d+\/genres/', $uri)):
                            $response = MidiaController::post_genre(self::get_id_uri($uri, ['/midias/','/genres']), $body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'PUT' && preg_match('/\/midias\/\d+\/genres\/\d+/', $uri)):
                            $ids = explode('/genres/',self::get_id_uri($uri, '/midias/'));
                            $response = MidiaController::put_genre($ids[0],$ids[1],$body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'DELETE' && preg_match('/\/midias\/\d+\/genres\/\d+/', $uri)):
                            $ids = explode('/genres/',self::get_id_uri($uri, '/midias/'));
                            $response = MidiaController::delete_genre($ids[0],$ids[1]);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'GET' && $uri == '/midias'):
                            $response = MidiaController::get($get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'GET' && preg_match('/\/midias\/\d+/', $uri)):
                            $response = MidiaController::find(self::get_id_uri($uri, '/midias/'),$get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'POST' && $uri == '/midias'):
                            $response = MidiaController::post($body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'PUT' && preg_match('/\/midias\/\d+/', $uri)):
                            $response = MidiaController::put(self::get_id_uri($uri, '/midias/'),$body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'DELETE' && preg_match('/\/midias\/\d+/', $uri)):
                            $response = MidiaController::delete(self::get_id_uri($uri, '/midias/'));
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
        
                        ///sessions
                        case ($method == 'GET' && $uri == '/sessions'):
                            $response = SessionController::get($get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'GET' && preg_match('/\/sessions\/\d+/', $uri)):
                            $response = SessionController::find(self::get_id_uri($uri, '/sessions/'),$get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'POST' && $uri == '/sessions'):
                            $response = SessionController::post($body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'PUT' && preg_match('/\/sessions\/\d+/', $uri)):
                            $response = SessionController::put(self::get_id_uri($uri, '/sessions/'),$body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'DELETE' && preg_match('/\/sessions\/\d+/', $uri)):
                            $response = SessionController::delete(self::get_id_uri($uri, '/sessions/'));
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
        
                        ///user_midia
                        case ($method == 'GET' && $uri == '/user_midia'):
                            $response = UserMidiaController::get($get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'GET' && preg_match('/\/user_midia\/\d+/', $uri)):
                            $response = UserMidiaController::find(self::get_id_uri($uri, '/user_midia/'),$get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'POST' && $uri == '/user_midia'):
                            $response = UserMidiaController::post($body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'PUT' && preg_match('/\/user_midia\/\d+/', $uri)):
                            $response = UserMidiaController::put(self::get_id_uri($uri, '/user_midia/'),$body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'DELETE' && preg_match('/\/user_midia\/\d+/', $uri)):
                            $response = UserMidiaController::delete(self::get_id_uri($uri, '/user_midia/'));
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
        
                        ///comments
                        case ($method == 'GET' && $uri == '/comments'):
                            $response = CommentController::get($get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'GET' && preg_match('/\/comments\/\d+/', $uri)):
                            $response = CommentController::find(self::get_id_uri($uri, '/comments/'),$get_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'POST' && $uri == '/comments'):
                            $response = CommentController::post($body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'PUT' && preg_match('/\/comments\/\d+/', $uri)):
                            $response = CommentController::put(self::get_id_uri($uri, '/comments/'),$body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        case ($method == 'DELETE' && preg_match('/\/comments\/\d+/', $uri)):
                            $response = CommentController::delete(self::get_id_uri($uri, '/comments/'));
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
        
                        ///login
                        case ($method == 'POST' && $uri == '/login'):
                            $response = UserController::login($body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        ///change_password
                        case ($method == 'PUT' && $uri == '/change_password'):
                            $response = UserController::change_password($body_fields);
                            $http_response_code = $response->response_code;
                            $http_response = $response->response_data;
                            break;
                        default:
                            $http_response->uri = $uri;
                            $http_response->msg = "page not found";
                            break;
                    }
                }
            } 
        } catch (\Throwable $th) {
            $http_response_code = 500;
            $http_response = (object)[
                "status" => false,
                "code" => $http_response_code,
                "msg" => "internal error",
            ];
        }
        return (object)["http_response_code"=>$http_response_code, "http_response"=>$http_response];
    }

    public static function get_id_uri($uri, $base_url){
        return str_replace($base_url, "",$uri);
    }

    public static function clean_uri($uri){
        if(str_contains($uri,"?")){
            $uri = explode("?",$uri)[0];
        }
        while ($uri[strlen($uri)-1]=="/") {
            $uri = substr($uri, 0, -1);
        }
        if(str_contains($uri,"/midias_api")){
            $uri = explode("/midias_api",$uri)[1];
        }
        return $uri;
    }

    public static function check_credentials($header_fields){
        $response = (object)[
            "status"=> false,
            "code"=> 401,
            "msg"=> "Authorization is missing"
        ];
        if(!empty($header_fields->Authorization??null)){
            $Authorization = $header_fields->Authorization;
            $response_autorization = OAuthController::check_Autorization($Authorization);
            $response->status = $response_autorization->status;
            $response->code = $response_autorization->code;
            $response->msg = $response_autorization->msg;
        }
        return $response;
    }

}