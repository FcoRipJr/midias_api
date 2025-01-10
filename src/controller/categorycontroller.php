<?php
require_once __DIR__."/../../src/dao/categorydao.php";

class CategoryController {
    public static function get($get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $midias = ($get_fields->midias ?? "" ) == "true" ? true : false;
        $genres = ($get_fields->genres ?? "" ) == "true" ? true : false;
        $filter = $get_fields;
        $pages = 1;
        $page = Models::isInt($filter->page??null) ? $filter->page : null;
        $data = CategoryDAO::get($filter,$midias, $genres);
        $total = count($data);
        $rows = count($data);
        $page_limit = $total;
        if(!empty($page)){
            $page_limit = Models::isInt($filter->page_limit??null) ? $filter->page_limit : 10;
            $total_obj = CategoryDAO::get_count($filter,$page_limit);
            $total = $total_obj->total;
            $pages = $total_obj->pages;
        }
        $response->response_data = (object)["data"=>$data,"rows"=>$rows,"total"=>$total,"page"=>$page??1,"pages"=>$pages,"page_limit"=>$page_limit];
        return $response;
    }
    public static function find($id,$get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $midias = ($get_fields->midias ?? "" ) == "true" ? true : false;
        $genres = ($get_fields->genres ?? "" ) == "true" ? true : false;
        $response->response_data = CategoryDAO::find($id,$midias, $genres);
        return $response;
    }
}