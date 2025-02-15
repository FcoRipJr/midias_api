<?php
header("Access-Control-Allow-Origin: *");  // Permite qualquer origem
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

require_once "./src/router/router.php";
// $localIP = getHostByName(getHostName());
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$uri = Router::clean_uri($uri);
$receivedData = file_get_contents("php://input");
$body_fields = json_decode($receivedData) ?? (object)[];
$get_fields = (object)$_GET;
$header_fields = (object)getallheaders();
$response = Router::redirect($uri, $method, $body_fields, $get_fields, $header_fields);
http_response_code($response->http_response_code);
echo json_encode($response->http_response);