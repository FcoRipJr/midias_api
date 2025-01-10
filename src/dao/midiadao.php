<?php
require_once __DIR__."/../../src/db/connect_db.php";
require_once __DIR__."/../../src/model/models.php";
require_once __DIR__."/../../src/dao/comentdao.php";
require_once __DIR__."/../../src/dao/genredao.php";
require_once __DIR__."/../../src/dao/userdao.php";

class MidiaDAO {
    public static function get(object $filter = null, $genres = false, $users = false, $coments = false){
        $results = array();
        $param_where = "1=1";
        $order_field = "title";
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
        if($order_field == "release"){
            $order = "release_year $order_direction, release_month $order_direction, release_day $order_direction";
        }
        if(!empty($filter->id_category)){
            $id_category = $filter->id_category;
            $param_where .= " and m.id_category = $id_category";
        }
        if(!empty($filter->id_main_midia)){
            $id_main_midia = $filter->id_main_midia;
            $param_where .= " and m.id_main_midia = $id_main_midia";
        }
        if(!empty($filter->title)){
            $title = $filter->title;
            $param_where .= " and m.title = '$title' ";
        }
        if(!empty($filter->title_like)){
            $title_like = $filter->title_like;
            $param_where .= " and m.title like '%$title_like%' ";
        }
        if(!empty($filter->description)){
            $description = $filter->description;
            $param_where .= " and m.description = '$description' ";
        }
        if(!empty($filter->description_like)){
            $description_like = $filter->description_like;
            $param_where .= " and m.description like '%$description_like%' ";
        }
        if(!empty($filter->category_like)){
            $category_like = $filter->category_like;
            $param_where .= " and c.description like '%$category_like%' ";
        }
        if(!empty($filter->release_year)){
            $release_year = $filter->release_year;
            $param_where .= " and m.release_year = '$release_year' ";
        }
        if(!empty($filter->release_month)){
            $release_month = $filter->release_month;
            $param_where .= " and m.release_month = '$release_month' ";
        }
        if(!empty($filter->release_day)){
            $release_day = $filter->release_day;
            $param_where .= " and m.release_day = '$release_day' ";
        }
        if(!empty($filter->id_main_midia_null)){
            $param_where .= " and m.id_main_midia is null";
        }
        if(!empty($filter->id_genre)){
            $id_genre = $filter->id_genre;
            $param_where .= " and m.id in (select distinct mg.id_midia from midia_genre mg where mg.id_genre = $id_genre) ";
        }
        if(!empty($filter->id_user)){
            $id_user = $filter->id_user;
            $param_where .= " and m.id in (select distinct um.id_midia from user_midia um where um.id_user = $id_user) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select m.*, c.description as category, mm.title as main_midia from midia m 
            left join category c on c.id = m.id_category
            left join midia mm on mm.id = m.id_main_midia
            where $param_where
            order by $order $limit;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $objeto = Models::midia();
                $objeto->id = $row->id ?? null;
                $objeto->id_category = $row->id_category ?? null;
                $objeto->id_main_midia = $row->id_main_midia ?? null;
                $objeto->category = $row->category ?? null;
                $objeto->main_midia = $row->main_midia ?? null;
                $objeto->title = $row->title ?? null;
                $objeto->description = $row->description ?? null;
                $objeto->release_year = $row->release_year ?? null;
                $objeto->release_month = $row->release_month ?? null;
                $objeto->release_day = $row->release_day ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->release = Models::get_release($objeto);
                $objeto->release_formated = Models::get_release($objeto,true);
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($genres===true){
                    $objeto->genres = GenreDAO::get((object)["id_midia"=>$objeto->id]);
                }
                if($users===true){
                    $objeto->users = UserDAO::get((object)["id_midia"=>$objeto->id]);
                }
                if($coments===true){
                    $objeto->coments = ComentDAO::get((object)["id_midia"=>$objeto->id]);
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
        if(!empty($filter->id_category)){
            $id_category = $filter->id_category;
            $param_where .= " and m.id_category = $id_category";
        }
        if(!empty($filter->id_main_midia)){
            $id_main_midia = $filter->id_main_midia;
            $param_where .= " and m.id_main_midia = $id_main_midia";
        }
        if(!empty($filter->title)){
            $title = $filter->title;
            $param_where .= " and m.title = '$title' ";
        }
        if(!empty($filter->title_like)){
            $title_like = $filter->title_like;
            $param_where .= " and m.title like '%$title_like%' ";
        }
        if(!empty($filter->description)){
            $description = $filter->description;
            $param_where .= " and m.description = '$description' ";
        }
        if(!empty($filter->description_like)){
            $description_like = $filter->description_like;
            $param_where .= " and m.description like '%$description_like%' ";
        }
        if(!empty($filter->category_like)){
            $category_like = $filter->category_like;
            $param_where .= " and c.description like '%$category_like%' ";
        }
        if(!empty($filter->release_year)){
            $release_year = $filter->release_year;
            $param_where .= " and m.release_year = '$release_year' ";
        }
        if(!empty($filter->release_month)){
            $release_month = $filter->release_month;
            $param_where .= " and m.release_month = '$release_month' ";
        }
        if(!empty($filter->release_day)){
            $release_day = $filter->release_day;
            $param_where .= " and m.release_day = '$release_day' ";
        }
        if(!empty($filter->id_main_midia_null)){
            $param_where .= " and m.id_main_midia is null";
        }
        if(!empty($filter->id_genre)){
            $id_genre = $filter->id_genre;
            $param_where .= " and m.id in (select distinct mg.id_midia from midia_genre mg where mg.id_genre = $id_genre) ";
        }
        if(!empty($filter->id_user)){
            $id_user = $filter->id_user;
            $param_where .= " and m.id in (select distinct um.id_midia from user_midia um where um.id_user = $id_user) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select count(*) as total from midia m 
            left join category c on c.id = m.id_category
            left join midia mm on mm.id = m.id_main_midia
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

    public static function find($id, $genres = false, $users = false, $coments = false){
        $objeto = Models::midia();
        try {
            $PDO = connect_db::active();
            $sql = "select m.*, c.description as category, mm.title as main_midia from midia m 
            left join category c on c.id = m.id_category
            left join midia mm on mm.id = m.id_main_midia
            where m.id = $id;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->id = $row->id ?? null;
                $objeto->id_category = $row->id_category ?? null;
                $objeto->id_main_midia = $row->id_main_midia ?? null;
                $objeto->category = $row->category ?? null;
                $objeto->main_midia = $row->main_midia ?? null;
                $objeto->title = $row->title ?? null;
                $objeto->description = $row->description ?? null;
                $objeto->release_year = $row->release_year ?? null;
                $objeto->release_month = $row->release_month ?? null;
                $objeto->release_day = $row->release_day ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->release = Models::get_release($objeto);
                $objeto->release_formated = Models::get_release($objeto,true);
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($genres===true){
                    $objeto->genres = GenreDAO::get((object)["id_midia"=>$objeto->id]);
                }
                if($users===true){
                    $objeto->users = UserDAO::get((object)["id_midia"=>$objeto->id]);
                }
                if($coments===true){
                    $objeto->coments = ComentDAO::get((object)["id_midia"=>$objeto->id]);
                }
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }
    public static function insert($midia){
        try {
            $PDO = connect_db::active();
            $sql = "INSERT INTO midia (id_category, id_main_midia, title, description, release_year, release_month, release_day)
                            VALUE (:id_category, :id_main_midia, :title, :description, :release_year, :release_month, :release_day);";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id_category", $midia->id_category ?? null);
            $stmt->bindValue(":id_main_midia", $midia->id_main_midia ?? null);
            $stmt->bindValue(":title", $midia->title ?? null);
            $stmt->bindValue(":description", $midia->description ?? null);
            $stmt->bindValue(":release_year", $midia->release_year ?? null);
            $stmt->bindValue(":release_month", $midia->release_month ?? null);
            $stmt->bindValue(":release_day", $midia->release_day ?? null);
            $stmt->execute();
            $ReturnId = $PDO->lastInsertId();
            return $ReturnId;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function update($midia){
        try{
            $PDO = connect_db::active();
            $sql = "update midia set 
                title = :title,
                description = :description,
                release_year = :release_year,
                release_month = :release_month,
                release_day = :release_day,
                updated = current_timestamp()
                where id = :id ;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":title", $midia->title);
            $stmt->bindValue(":description", $midia->description);
            $stmt->bindValue(":release_year", $midia->release_year);
            $stmt->bindValue(":release_month", $midia->release_month);
            $stmt->bindValue(":release_day", $midia->release_day);
            $stmt->bindValue(":id", $midia->id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function delete($id){
        try {
            $PDO = connect_db::active();
            $sql = "delete from midia where id = :id;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id", $id);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function insert_genre($id_midia, $id_genre, $main = false){
        try {
            $PDO = connect_db::active();
            $sql = "INSERT INTO midia_genre (id_midia, id_genre, main)
                            VALUE (:id_midia, :id_genre, :main);";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id_midia", $id_midia ?? null);
            $stmt->bindValue(":id_genre", $id_genre ?? null);
            $stmt->bindValue(":main", $main ? 'yes' : 'no');
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function update_genre($id_midia, $id_genre, $main = false){
        try{
            $PDO = connect_db::active();
            $sql = "update midia_genre set 
                main = :main,
                updated = current_timestamp()
                where id_midia = :id_midia 
                and id_genre = :id_genre ;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":main", $main ? 'yes' : 'no');
            $stmt->bindValue(":id_midia", $id_midia);
            $stmt->bindValue(":id_genre", $id_genre);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function delete_genre($id_midia, $id_genre){
        try {
            $PDO = connect_db::active();
            $sql = "delete from midia_genre where id_midia = :id_midia and id_genre = :id_genre;";
            $stmt = $PDO->prepare($sql);
            $stmt->bindValue(":id_midia", $id_midia);
            $stmt->bindValue(":id_genre", $id_genre);
            $stmt->execute();
            return ($stmt->rowCount() > 0) ? $stmt->rowCount() : false;
        } catch(Exception $e) {
            return false;
        }
    }
}