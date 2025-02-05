<?php
require_once __DIR__."/../../src/db/connect_db.php";
require_once __DIR__."/../../src/model/models.php";
require_once __DIR__."/../../src/dao/oauthdao.php";

class OAuthDAO {
    public static function find_oauth_access_token($access_token){
        $objeto = Models::oauth_access_token();
        try {
            $PDO = connect_db::active();
            $sql = "select (CURRENT_TIMESTAMP() > oat.expires) as expired, u.name as user, oat.* from oauth_access_tokens oat 
            left join user u on u.id = oat.id_user
            where oat.access_token = '$access_token';";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->access_token = $row->access_token ?? null;
                $objeto->client_id = $row->client_id ?? null;
                $objeto->id_user = $row->id_user ?? null;
                $objeto->user = $row->user ?? null;
                $objeto->scope = $row->scope ?? null;
                $objeto->expires = $row->expires ?? null;
                $objeto->expired = $row->expired ?? null;
                $objeto->expires_formated = Models::convert_date($objeto->expires);
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }

    public static function check_client_credentials($client_id, $client_secret, $grant_type = null){
        $param_where = "1=1";
        if(!empty($grant_type)){
            $param_where .= " and grant_types like '%$grant_type%' ";
        }
        $check_credentials = 0;
        try {
            $PDO = connect_db::active();
            $sql = "select count(*) as check_credentials from oauth_clients where $param_where and client_id = '$client_id' and client_secret = '$client_secret' ;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $check_credentials = $row->check_credentials??0;
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $check_credentials == 1;
    }
    public static function check_client_id($client_id){
        $count_clients = 0;
        try {
            $PDO = connect_db::active();
            $sql = "select count(*) as count_clients from oauth_clients where client_id = '$client_id' ;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $count_clients = $row->count_clients??0;
            }
        } catch(Exception $e) {
            return false;
            // throw new Exception($e->getMessage());
        }
        return $count_clients == 0;
    }

    public static function insert_oauth_access_token($oauth_access_token){
        try{
            $PDO = connect_db::active();
            $sql = "INSERT INTO oauth_access_tokens (access_token, client_id, id_user, scope, expires)
                            VALUE (:access_token, :client_id, :id_user, :scope, (DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL 30 MINUTE)));";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":access_token", $oauth_access_token->access_token ?? null);
            $stmt->bindValue(":client_id", $oauth_access_token->client_id ?? null);
            $stmt->bindValue(":id_user", $oauth_access_token->id_user ?? null);
            $stmt->bindValue(":scope", $oauth_access_token->scope ?? null);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
            return false;
        }
    }

    public static function create_client_id(){
        $client_id = bin2hex(random_bytes(16));
        if(!self::check_client_id($client_id)){
            return self::create_client_id(); 
        } 
        return $client_id;
    }

    public static function create_client_secret(){
        return bin2hex(random_bytes(32));
    }

    public static function create_jwt_token($request_body){
        $env = parse_ini_file(__DIR__."/../../.env");
        $header = base64_encode(json_encode(['alg'=>'HS256','typ'=>'JWT']));
        $payload = base64_encode(json_encode($request_body));
        $signature = base64_encode(hash_hmac('sha256', "$header.$payload", $env["JWT_KEY"], true));
        return "$header.$payload.$signature";
    }

}