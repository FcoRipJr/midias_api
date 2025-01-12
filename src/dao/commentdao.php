<?php
require_once __DIR__."/../../src/db/connect_db.php";
require_once __DIR__."/../../src/model/models.php";

class CommentDAO {
    public static function get(object $filter = null){
        $results = array();
        $param_where = "1=1";
        $order_field = "created";
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
            $param_where .= " and c.id_user = $id_user";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and c.id_midia = $id_midia";
        }
        if(!empty($filter->id_session)){
            $id_session = $filter->id_session;
            $param_where .= " and c.id_session = $id_session";
        }
        if(!empty($filter->text)){
            $text = $filter->text;
            $param_where .= " and c.text = '$text' ";
        }
        if(!empty($filter->text_like)){
            $text_like = $filter->text_like;
            $param_where .= " and c.text like '%$text_like%' ";
        }
        if(!empty($filter->session)){
            $session = $filter->session;
            $param_where .= " and s.code = '$session' ";
        }
        if(!empty($filter->session_like)){
            $session_like = $filter->session_like;
            $param_where .= " and s.code like '%$session_like%' ";
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
            $param_where .= " and (DATE(c.created) = DATE('$date') or DATE(c.updated) = DATE('$date') ) ";
        }
        if((!empty($filter->range_start)) && (!empty($filter->range_end))){
            $range_start = $filter->range_start;
            $range_end = $filter->range_end;
            $param_where .= " and ( (DATE(c.created) between DATE('$range_start') and DATE('$range_end')) or (DATE(c.updated) between DATE('$range_start') and DATE('$range_end')) ) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select c.*, u.name as user, m.title as midia, s.code as session from comment c 
            left join user u on u.id = c.id_user
            left join midia m on m.id = c.id_midia
            left join session s on s.id = c.id_session
            where $param_where
            order by $order $limit;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $objeto = Models::comment();
                $objeto->id = $row->id ?? null;
                $objeto->id_user = $row->id_user ?? null;
                $objeto->id_midia = $row->id_midia ?? null;
                $objeto->id_session = $row->id_session ?? null;
                $objeto->user = $row->user ?? null;
                $objeto->midia = $row->midia ?? null;
                $objeto->session = $row->session ?? null;
                $objeto->text = $row->text ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
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
            $param_where .= " and c.id_user = $id_user";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and c.id_midia = $id_midia";
        }
        if(!empty($filter->id_session)){
            $id_session = $filter->id_session;
            $param_where .= " and c.id_session = $id_session";
        }
        if(!empty($filter->text)){
            $text = $filter->text;
            $param_where .= " and c.text = '$text' ";
        }
        if(!empty($filter->text_like)){
            $text_like = $filter->text_like;
            $param_where .= " and c.text like '%$text_like%' ";
        }
        if(!empty($filter->session)){
            $session = $filter->session;
            $param_where .= " and s.code = '$session' ";
        }
        if(!empty($filter->session_like)){
            $session_like = $filter->session_like;
            $param_where .= " and s.code like '%$session_like%' ";
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
            $param_where .= " and (DATE(c.created) = DATE('$date') or DATE(c.updated) = DATE('$date') ) ";
        }
        if((!empty($filter->range_start)) && (!empty($filter->range_end))){
            $range_start = $filter->range_start;
            $range_end = $filter->range_end;
            $param_where .= " and ( (DATE(c.created) between DATE('$range_start') and DATE('$range_end')) or (DATE(c.updated) between DATE('$range_start') and DATE('$range_end')) ) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = " select count(*) as total from comment c 
                left join user u on u.id = c.id_user
                left join midia m on m.id = c.id_midia
                left join session s on s.id = c.id_session
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

    public static function find($id){
        $objeto = Models::comment();
        try {
            $PDO = connect_db::active();
            $sql = "select c.*, u.name as user, m.title as midia, s.code as session from comment c 
            left join user u on u.id = c.id_user
            left join midia m on m.id = c.id_midia
            left join session s on s.id = c.id_session
            where c.id = $id;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->id = $row->id ?? null;
                $objeto->id_user = $row->id_user ?? null;
                $objeto->id_midia = $row->id_midia ?? null;
                $objeto->id_session = $row->id_session ?? null;
                $objeto->user = $row->user ?? null;
                $objeto->midia = $row->midia ?? null;
                $objeto->session = $row->session ?? null;
                $objeto->text = $row->text ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }
    public static function insert($comment){
        try{
            $PDO = connect_db::active();
            $sql = "INSERT INTO comment (id_user, id_midia, id_session, text)
                            VALUE (:id_user, :id_midia, :id_session, :text);";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id_user", $comment->id_user ?? null);
            $stmt->bindValue(":id_midia", $comment->id_midia ?? null);
            $stmt->bindValue(":id_session", $comment->id_session ?? null);
            $stmt->bindValue(":text", $comment->text ?? null);
            $stmt->execute();
            $ReturnId = $PDO->lastInsertId();
            return $ReturnId;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function update($comment){
        try{
            $PDO = connect_db::active();
            $sql = "update comment set 
                text = :text,
                updated = current_timestamp()
                where id = :id ;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":text", $comment->text);
            $stmt->bindValue(":id", $comment->id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function delete($id){
        try {
            $PDO = connect_db::active();
            $sql = "delete from comment where id = :id;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }
}