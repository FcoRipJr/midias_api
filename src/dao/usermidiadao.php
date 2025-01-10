<?php
require_once __DIR__."/../../src/db/connect_db.php";
require_once __DIR__."/../../src/model/models.php";
require_once __DIR__."/../../src/dao/comentdao.php";

class UserMidiaDAO {
    public static function get(object $filter = null, $coments = false){
        $results = array();
        $param_where = "1=1";
        $order_field = "midia";
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
            $param_where .= " and um.id_user = $id_user";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and um.id_midia = $id_midia";
        }
        if(!empty($filter->id_completion)){
            $id_completion = $filter->id_completion;
            $param_where .= " and um.id_completion = $id_completion";
        }
        if(!empty($filter->id_category)){
            $id_category = $filter->id_category;
            $param_where .= " and m.id_category = $id_category";
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
        if(!empty($filter->score)){
            $score = $filter->score;
            $param_where .= " and um.score = $score ";
        }
        if(!empty($filter->score_lower)){
            $score_lower = $filter->score_lower;
            $param_where .= " and um.score < $score_lower ";
        }
        if(!empty($filter->score_higher)){
            $score_higher = $filter->score_higher;
            $param_where .= " and um.score > $score_higher ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select um.*, u.name as user, m.title as midia, c.description as completion from user_midia um 
            left join user u on u.id = um.id_user
            left join midia m on m.id = um.id_midia
            left join completion c on c.id = um.id_completion
            where $param_where
            order by $order $limit;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $objeto = Models::user_midia();
                $objeto->id = $row->id ?? null;
                $objeto->id_user = $row->id_user ?? null;
                $objeto->id_midia = $row->id_midia ?? null;
                $objeto->id_completion = $row->id_completion ?? null;
                $objeto->user = $row->user ?? null;
                $objeto->midia = $row->midia ?? null;
                $objeto->completion = $row->completion ?? null;
                $objeto->score = $row->score ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($coments===true){
                    $objeto->coments = ComentDAO::get((object)["id_user"=>$objeto->id_user,"id_midia"=>$objeto->id_midia]);
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
            $param_where .= " and um.id_user = $id_user";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and um.id_midia = $id_midia";
        }
        if(!empty($filter->id_completion)){
            $id_completion = $filter->id_completion;
            $param_where .= " and um.id_completion = $id_completion";
        }
        if(!empty($filter->id_category)){
            $id_category = $filter->id_category;
            $param_where .= " and m.id_category = $id_category";
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
        if(!empty($filter->score)){
            $score = $filter->score;
            $param_where .= " and um.score = $score ";
        }
        if(!empty($filter->score_lower)){
            $score_lower = $filter->score_lower;
            $param_where .= " and um.score < $score_lower ";
        }
        if(!empty($filter->score_higher)){
            $score_higher = $filter->score_higher;
            $param_where .= " and um.score > $score_higher ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select count(*) as total from user_midia um 
            left join user u on u.id = um.id_user
            left join midia m on m.id = um.id_midia
            left join completion c on c.id = um.id_completion
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

    public static function find($id, $coments = false){
        $objeto = Models::user_midia();
        try {
            $PDO = connect_db::active();
            $sql = "select um.*, u.name as user, m.title as midia, c.description as completion from user_midia um 
            left join user u on u.id = um.id_user
            left join midia m on m.id = um.id_midia
            left join completion c on c.id = um.id_completion
            where um.id = $id;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->id = $row->id ?? null;
                $objeto->id_user = $row->id_user ?? null;
                $objeto->id_midia = $row->id_midia ?? null;
                $objeto->id_completion = $row->id_completion ?? null;
                $objeto->user = $row->user ?? null;
                $objeto->midia = $row->midia ?? null;
                $objeto->completion = $row->completion ?? null;
                $objeto->score = $row->score ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($coments===true){
                    $objeto->coments = ComentDAO::get((object)["id_user"=>$objeto->id_user,"id_midia"=>$objeto->id_midia]);
                }
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }
    public static function insert($user_midia){
        try{
            $PDO = connect_db::active();
            $sql = "INSERT INTO user_midia (id_user, id_midia, id_completion, score)
                            VALUE (:id_user, :id_midia, :id_completion, :score);";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id_user", $user_midia->id_user ?? null);
            $stmt->bindValue(":id_midia", $user_midia->id_midia ?? null);
            $stmt->bindValue(":id_completion", $user_midia->id_completion ?? null);
            $stmt->bindValue(":score", $user_midia->score ?? null);
            $stmt->execute();
            $ReturnId = $PDO->lastInsertId();
            return $ReturnId;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function update($user_midia){
        try{
            $PDO = connect_db::active();
            $sql = "update user_midia set 
                id_completion = :id_completion,
                score = :score,
                updated = current_timestamp()
                where id = :id ;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id_completion", $user_midia->id_completion);
            $stmt->bindValue(":score", $user_midia->score);
            $stmt->bindValue(":id", $user_midia->id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function delete($id){
        try {
            $PDO = connect_db::active();
            $sql = "delete from user_midia where id = :id;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }
}