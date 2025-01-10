<?php
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
  header("Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS");


class connect_db {
    static $active;
    public static function active(){
        $env = parse_ini_file(__DIR__."/../../.env");
        $host = $env["DB_HOST"];
        $dbname = $env["DB_NAME"];
        $user =  $env["DB_USER"];
        $senha = $env["DB_PASSWORD"];
        $porta = $env["DB_PORT"];
        $active = new PDO("mysql:host=$host; port=$porta; dbname=$dbname; charset=utf8", $user, $senha);
        return $active;
    }
};