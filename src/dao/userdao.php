<?php
require_once __DIR__."/../../src/db/connect_db.php";
require_once __DIR__."/../../src/model/models.php";
require_once __DIR__."/../../src/dao/comentdao.php";
require_once __DIR__."/../../src/dao/midiadao.php";
require_once __DIR__."/../../src/dao/sessiondao.php";

class UserDAO {
    public static function get(object $filter = null, $midias = false, $sessions = false, $coments = false){
        $results = array();
        $param_where = "1=1";
        $order_field = "name";
        $order_direction = "asc";
        $limit = "";
        $page_limit = 10;
        $page = "";
        if(!empty($filter->order_field)){
            $order_field = $filter->order_field;
        }
        if(!empty($filter->order_direction)){
            $order_direction = $filter->order_direction;
        }
        if(!empty($filter->page_limit)){
            $page_limit = $filter->page_limit;
        }
        if(!empty($filter->page)){
            $page = $filter->page;
            if(Models::isInt($page) && Models::isInt($page_limit)){
                $limit = " limit $page_limit offset ".((intval($page) - 1)*$page_limit);
            }
        }
        $order = "$order_field $order_direction";
        if(!empty($filter->name)){
            $name = $filter->name;
            $param_where .= " and name = '$name' ";
        }
        if(!empty($filter->name_like)){
            $name_like = $filter->name_like;
            $param_where .= " and name like '%$name_like%' ";
        }
        if(!empty($filter->status)){
            $status = $filter->status;
            $param_where .= " and status = '$status' ";
        }
        if(!empty($filter->status_in)){
            $status_in = $filter->status_in;
            $param_where .= " and status in ( $status_in ) ";
        }
        if(!empty($filter->status_notin)){
            $status_notin = $filter->status_notin;
            $param_where .= " and status not in ( $status_notin ) ";
        }
        if(!empty($filter->id_not_in)){
            $id_not_in = $filter->id_not_in;
            $param_where .= " and id not in ( $id_not_in ) ";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and id in (select distinct um.id_user from user_midia um where um.id_midia = $id_midia) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select * from user where $param_where order by $order $limit;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $objeto = Models::user();
                $objeto->id = $row->id ?? null;
                $objeto->name = $row->name ?? null;
                $objeto->status = $row->status ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($midias===true){
                    $objeto->midias = MidiaDAO::get((object)["id_user"=>$objeto->id]);
                }
                if($sessions===true){
                    $objeto->sessions = SessionDAO::get((object)["id_user"=>$objeto->id]);
                }
                if($coments===true){
                    $objeto->coments = ComentDAO::get((object)["id_user"=>$objeto->id]);
                }
                $results[] = $objeto;
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $results;
    }

    public static function get_count(object $filter = null, $page_limit){
        $objeto = (object)["total"=>0,"pages"=>0];
        $param_where = "1=1";
        if(!empty($filter->name)){
            $name = $filter->name;
            $param_where .= " and name = '$name' ";
        }
        if(!empty($filter->name_like)){
            $name_like = $filter->name_like;
            $param_where .= " and name like '%$name_like%' ";
        }
        if(!empty($filter->status)){
            $status = $filter->status;
            $param_where .= " and status = '$status' ";
        }
        if(!empty($filter->status_in)){
            $status_in = $filter->status_in;
            $param_where .= " and status in ( $status_in ) ";
        }
        if(!empty($filter->status_notin)){
            $status_notin = $filter->status_notin;
            $param_where .= " and status not in ( $status_notin ) ";
        }
        if(!empty($filter->id_not_in)){
            $id_not_in = $filter->id_not_in;
            $param_where .= " and id not in ( $id_not_in ) ";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and id in (select distinct um.id_user from user_midia um where um.id_midia = $id_midia) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select count(*) as total from user
            where $param_where ;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $objeto->total = $row->total ?? 0;
            }
            if($objeto->total>0){
                $objeto->pages = ceil(intval($objeto->total) / intval($page_limit));
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }

    public static function find($id, $midias = false, $sessions = false, $coments = false){
        $objeto = Models::user();
        try {
            $PDO = connect_db::active();
            $sql = "select * from user where id = $id;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->id = $row->id ?? null;
                $objeto->name = $row->name ?? null;
                $objeto->status = $row->status ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($midias===true){
                    $objeto->midias = MidiaDAO::get((object)["id_user"=>$objeto->id]);
                }
                if($sessions===true){
                    $objeto->sessions = SessionDAO::get((object)["id_user"=>$objeto->id]);
                }
                if($coments===true){
                    $objeto->coments = ComentDAO::get((object)["id_user"=>$objeto->id]);
                }
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }

    public static function login($name, $password, $midias = false, $sessions = false, $coments = false){
        $objeto = Models::user();
        try {
            $PDO = connect_db::active();
            $sql = "select * from user where name = '$name' and password = '$password' and status = 'active';";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->id = $row->id ?? null;
                $objeto->name = $row->name ?? null;
                $objeto->status = $row->status ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($midias===true){
                    $objeto->midias = MidiaDAO::get((object)["id_user"=>$objeto->id]);
                }
                if($sessions===true){
                    $objeto->sessions = SessionDAO::get((object)["id_user"=>$objeto->id]);
                }
                if($coments===true){
                    $objeto->coments = ComentDAO::get((object)["id_user"=>$objeto->id]);
                }
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }

    public static function find_password($id, $midias = false, $sessions = false, $coments = false){
        $objeto = Models::user();
        try {
            $PDO = connect_db::active();
            $sql = "select * from user where id = $id;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->id = $row->id ?? null;
                $objeto->name = $row->name ?? null;
                $objeto->password = $row->password ?? null;
                $objeto->status = $row->status ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($midias===true){
                    $objeto->midias = MidiaDAO::get((object)["id_user"=>$objeto->id]);
                }
                if($sessions===true){
                    $objeto->sessions = SessionDAO::get((object)["id_user"=>$objeto->id]);
                }
                if($coments===true){
                    $objeto->coments = ComentDAO::get((object)["id_user"=>$objeto->id]);
                }
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }

    public static function insert($user){
        try{
            $PDO = connect_db::active();
            $sql = "INSERT INTO user (name, password)
                            VALUE (:name, :password);";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":name", $user->name ?? null);
            $stmt->bindValue(":password", $user->password ?? null);
            $stmt->execute();
            $ReturnId = $PDO->lastInsertId();
            return $ReturnId;
        } catch(Exception $e) {
            return false;
        }
    }
    
    public static function update($user){
        try{
            $PDO = connect_db::active();
            $sql = "update user set 
                name = :name,
                status = :status,
                updated = current_timestamp()
                where id = :id ;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":name", $user->name);
            $stmt->bindValue(":status", $user->status);
            $stmt->bindValue(":id", $user->id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function update_password($user){
        try{
            $PDO = connect_db::active();
            $sql = "update user set 
                password = :password,
                updated = current_timestamp()
                where id = :id ;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":password", $user->password);
            $stmt->bindValue(":id", $user->id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function delete($id){
        try {
            $PDO = connect_db::active();
            $sql = "delete from user where id = :id;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }
}