<?php
require_once __DIR__."/../../src/db/connect_db.php";
require_once __DIR__."/../../src/model/models.php";
require_once __DIR__."/../../src/dao/commentdao.php";

class SessionDAO {
    public static function get(object $filter = null, $comments = false){
        $results = array();
        $param_where = "1=1";
        $order_field = "start";
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
        if(!empty($filter->id_user)){
            $id_user = $filter->id_user;
            $param_where .= " and s.id_user = $id_user";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and s.id_midia = $id_midia";
        }
        if(!empty($filter->code)){
            $code = $filter->code;
            $param_where .= " and s.code = '$code' ";
        }
        if(!empty($filter->code_like)){
            $code_like = $filter->code_like;
            $param_where .= " and s.code like '%$code_like%' ";
        }
        if(!empty($filter->user)){
            $user = $filter->user;
            $param_where .= " and u.name = '$user' ";
        }
        if(!empty($filter->user_like)){
            $user_like = $filter->user_like;
            $param_where .= " and u.name like '%$user_like%' ";
        }
        if(!empty($filter->midia)){
            $midia = $filter->midia;
            $param_where .= " and m.title = '$midia' ";
        }
        if(!empty($filter->midia_like)){
            $midia_like = $filter->midia_like;
            $param_where .= " and m.title like '%$midia_like%' ";
        }
        if(!empty($filter->date)){
            $date = $filter->date;
            $param_where .= " and (DATE(s.start) = DATE('$date') or DATE(s.end) = DATE('$date') ) ";
        }
        if((!empty($filter->range_start)) && (!empty($filter->range_end))){
            $range_start = $filter->range_start;
            $range_end = $filter->range_end;
            $param_where .= " and ( (DATE(s.start) between DATE('$range_start') and DATE('$range_end')) or (DATE(s.end) between DATE('$range_start') and DATE('$range_end')) ) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select s.*, u.name as user, m.title as midia from session s 
            left join user u on u.id = s.id_user
            left join midia m on m.id = s.id_midia
            where $param_where
            order by $order $limit;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $objeto = Models::session();
                $objeto->id = $row->id ?? null;
                $objeto->id_user = $row->id_user ?? null;
                $objeto->id_midia = $row->id_midia ?? null;
                $objeto->user = $row->user ?? null;
                $objeto->midia = $row->midia ?? null;
                $objeto->code = $row->code ?? null;
                $objeto->start = $row->start ?? null;
                $objeto->end = $row->end ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->start_formated = Models::convert_date($objeto->start);
                $objeto->end_formated = Models::convert_date($objeto->end);
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($comments===true){
                    $objeto->comments = CommentDAO::get((object)["id_session"=>$objeto->id]);
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
        if(!empty($filter->id_user)){
            $id_user = $filter->id_user;
            $param_where .= " and s.id_user = $id_user";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and s.id_midia = $id_midia";
        }
        if(!empty($filter->code)){
            $code = $filter->code;
            $param_where .= " and s.code = '$code' ";
        }
        if(!empty($filter->code_like)){
            $code_like = $filter->code_like;
            $param_where .= " and s.code like '%$code_like%' ";
        }
        if(!empty($filter->user)){
            $user = $filter->user;
            $param_where .= " and u.name = '$user' ";
        }
        if(!empty($filter->user_like)){
            $user_like = $filter->user_like;
            $param_where .= " and u.name like '%$user_like%' ";
        }
        if(!empty($filter->midia)){
            $midia = $filter->midia;
            $param_where .= " and m.title = '$midia' ";
        }
        if(!empty($filter->midia_like)){
            $midia_like = $filter->midia_like;
            $param_where .= " and m.title like '%$midia_like%' ";
        }
        if(!empty($filter->date)){
            $date = $filter->date;
            $param_where .= " and (DATE(s.start) = DATE('$date') or DATE(s.end) = DATE('$date') ) ";
        }
        if((!empty($filter->range_start)) && (!empty($filter->range_end))){
            $range_start = $filter->range_start;
            $range_end = $filter->range_end;
            $param_where .= " and ( (DATE(s.start) between DATE('$range_start') and DATE('$range_end')) or (DATE(s.end) between DATE('$range_start') and DATE('$range_end')) ) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select count(*) as total from session s 
            left join user u on u.id = s.id_user
            left join midia m on m.id = s.id_midia
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

    public static function find($id, $comments = false){
        $objeto = Models::session();
        try {
            $PDO = connect_db::active();
            $sql = "select s.*, u.name as user, m.title as midia from session s 
            left join user u on u.id = s.id_user
            left join midia m on m.id = s.id_midia
            where s.id = $id;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->id = $row->id ?? null;
                $objeto->id_user = $row->id_user ?? null;
                $objeto->id_midia = $row->id_midia ?? null;
                $objeto->user = $row->user ?? null;
                $objeto->midia = $row->midia ?? null;
                $objeto->code = $row->code ?? null;
                $objeto->start = $row->start ?? null;
                $objeto->end = $row->end ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->start_formated = Models::convert_date($objeto->start);
                $objeto->end_formated = Models::convert_date($objeto->end);
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($comments===true){
                    $objeto->comments = CommentDAO::get((object)["id_session"=>$objeto->id]);
                }
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }

    public static function insert($session){
        try {
            $PDO = connect_db::active();
            $code = self::generate_code();
            $sql = "INSERT INTO session (id_user, id_midia, code, start, end)
                            VALUE (:id_user, :id_midia, :code, :start, :end);";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id_user", $session->id_user ?? null);
            $stmt->bindValue(":id_midia", $session->id_midia ?? null);
            $stmt->bindValue(":code", $code);
            $stmt->bindValue(":start", $session->start ?? null);
            $stmt->bindValue(":end", $session->end ?? null);
            $stmt->execute();
            $ReturnId = $PDO->lastInsertId();
            return $ReturnId;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function update($session){
        try{
            $PDO = connect_db::active();
            $sql = "update session set 
                start = :start,
                end = :end,
                updated = current_timestamp()
                where id = :id ;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":start", $session->start);
            $stmt->bindValue(":end", $session->end);
            $stmt->bindValue(":id", $session->id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function delete($id){
        try {
            $PDO = connect_db::active();
            $sql = "delete from session where id = :id;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function generate_code(){
        $code = "";
        $string = "0123456789abcdefghijklmnopqrstuvwxyz";
        for ($i=0; $i < rand(3,20) ; $i++) { 
            $code .= $string[rand(0,strlen($string) - 1)];
        }
        if(count(self::get((object)['code'=>$code])) == 0){
            return $code;
        } else {
            return self::generate_code();
        }
    }
}