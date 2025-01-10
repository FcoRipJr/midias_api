<?php
require_once __DIR__."/../../src/db/connect_db.php";
require_once __DIR__."/../../src/model/models.php";
require_once __DIR__."/../../src/dao/midiadao.php";

class GenreDAO {
    public static function get(object $filter = null, $midias = false){
        $results = array();
        $param_where = "1=1";
        $order_field = "description";
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
        if(!empty($filter->id_category)){
            $id_category = $filter->id_category;
            $param_where .= " and g.id_category = $id_category";
        }
        if(!empty($filter->description)){
            $description = $filter->description;
            $param_where .= " and g.description = '$description' ";
        }
        if(!empty($filter->description_like)){
            $description_like = $filter->description_like;
            $param_where .= " and g.description like '%$description_like%' ";
        }
        if(!empty($filter->category_like)){
            $category_like = $filter->category_like;
            $param_where .= " and c.description like '%$category_like%' ";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and g.id in (select distinct mg.id_genre from midia_genre mg where mg.id_midia = $id_midia) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select g.*, c.description as category from genre g 
            left join category c on c.id = g.id_category
            where $param_where
            order by $order $limit;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $objeto = Models::genre();
                $objeto->id = $row->id ?? null;
                $objeto->id_category = $row->id_category ?? null;
                $objeto->category = $row->category ?? null;
                $objeto->description = $row->description ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($midias===true){
                    $objeto->midias = MidiaDAO::get((object)["id_genre"=>$objeto->id]);
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
            $param_where .= " and g.id_category = $id_category";
        }
        if(!empty($filter->description)){
            $description = $filter->description;
            $param_where .= " and g.description = '$description' ";
        }
        if(!empty($filter->description_like)){
            $description_like = $filter->description_like;
            $param_where .= " and g.description like '%$description_like%' ";
        }
        if(!empty($filter->category_like)){
            $category_like = $filter->category_like;
            $param_where .= " and c.description like '%$category_like%' ";
        }
        if(!empty($filter->id_midia)){
            $id_midia = $filter->id_midia;
            $param_where .= " and g.id in (select distinct mg.id_genre from midia_genre mg where mg.id_midia = $id_midia) ";
        }
        try {
            $PDO = connect_db::active();
            $sql = "select count(*) as total from genre g 
            left join category c on c.id = g.id_category
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

    public static function find($id, $midias = false){
        $objeto = Models::genre();
        try {
            $PDO = connect_db::active();
            $sql = "select g.*, c.description as category from genre g 
            left join category c on c.id = g.id_category where g.id = $id;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->id = $row->id ?? null;
                $objeto->id = $row->id ?? null;
                $objeto->id_category = $row->id_category ?? null;
                $objeto->category = $row->category ?? null;
                $objeto->description = $row->description ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($midias===true){
                    $objeto->midias = MidiaDAO::get((object)["id_genre"=>$objeto->id]);
                }
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }

}