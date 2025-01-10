<?php
require_once __DIR__."/../../src/dao/completiondao.php";

class CompletionController {
    public static function get($get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $filter = $get_fields;
        $pages = 1;
        $page = Models::isInt($filter->page??null) ? $filter->page : null;
        $data = CompletionDAO::get($filter);
        $total = count($data);
        $rows = count($data);
        $page_limit = $total;
        if(!empty($page)){
            $page_limit = Models::isInt($filter->page_limit??null) ? $filter->page_limit : 10;
            $total_obj = CompletionDAO::get_count($filter,$page_limit);
            $total = $total_obj->total;
            $pages = $total_obj->pages;
        }
        $response->response_data = (object)["data"=>$data,"rows"=>$rows,"total"=>$total,"page"=>$page??1,"pages"=>$pages,"page_limit"=>$page_limit];
        return $response;
    }
    public static function find($id,$get_fields){
        $response = (object) ["response_code"=>200, "response_data" => []];
        $response->response_data = CompletionDAO::find($id);
        return $response;
    }
}