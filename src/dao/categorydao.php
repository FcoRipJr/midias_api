<?php
require_once __DIR__."/../../src/db/connect_db.php";
require_once __DIR__."/../../src/model/models.php";
require_once __DIR__."/../../src/dao/genredao.php";
require_once __DIR__."/../../src/dao/midiadao.php";

class CategoryDAO {
    public static function get(object $filter = null, $midias = false, $genres = false){
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
        try {
            $PDO = connect_db::active();
            $sql = "select * from category where $param_where order by $order $limit;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            while($row = $stmt -> fetch(PDO::FETCH_OBJ)) {
                $objeto = Models::category();
                $objeto->id = $row->id ?? null;
                $objeto->description = $row->description ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($midias===true){
                    $objeto->midias = MidiaDAO::get((object)["id_category"=>$objeto->id]);
                }
                if($genres===true){
                    $objeto->genres = GenreDAO::get((object)["id_category"=>$objeto->id]);
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
        try {
            $PDO = connect_db::active();
            $sql = "select count(*) as total from category
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

    public static function find($id, $midias = false, $genres = false){
        $objeto = Models::category();
        try {
            $PDO = connect_db::active();
            $sql = "select * from category where id = $id;";
            $stmt = $PDO->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!empty($row)) {
                $objeto->id = $row->id ?? null;
                $objeto->description = $row->description ?? null;
                $objeto->created = $row->created ?? null;
                $objeto->updated = $row->updated ?? null;
                $objeto->created_formated = Models::convert_date($objeto->created);
                $objeto->updated_formated = Models::convert_date($objeto->updated);
                if($midias===true){
                    $objeto->midias = MidiaDAO::get((object)["id_category"=>$objeto->id]);
                }
                if($genres===true){
                    $objeto->genres = GenreDAO::get((object)["id_category"=>$objeto->id]);
                }
            }
        } catch(Exception $e) {
            // throw new Exception($e->getMessage());
        }
        return $objeto;
    }

}