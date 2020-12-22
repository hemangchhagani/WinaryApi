<?php
//include_once 'error.php';
include_once 'config/database.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$secret_key = "YOUR_SECRET_KEY";
$jwt = null;
$Id = "";
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$data = json_decode(file_get_contents("php://input"));
$Id = $data->Id;
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$conn1 = $databaseService->getConnection();

$stmt = $conn->prepare("SELECT * FROM `UserType` ");

if($stmt->execute()){
    
    $row = $stmt->fetchall(PDO::FETCH_ASSOC);
    http_response_code(200);
        echo json_encode(array(  "Data" => $row , "status" => "True", "message" => "User Type Details."));
}
else{
        http_response_code(400);
        echo json_encode(array("message" => "Unable to register the user."));
}


?>