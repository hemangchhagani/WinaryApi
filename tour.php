<?php
include_once './config/database.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$secret_key = "YOUR_SECRET_KEY";
$jwt = null;
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];
if($jwt){

try {

$decoded = JWT::decode($jwt, $secret_key, array('HS256'));


$TourDate = '';
$UserId = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

$TourDate = $data->TourDate;
$UserId = $data->UserId;

$stmt = $conn->prepare("CALL Instour('$TourDate','$UserId','1','Active','$CreatedById')");

if($stmt->execute()){
        http_response_code(200);
        echo json_encode(array("message" => "Wine added successfully."));
    }
    else{
        http_response_code(400);
        echo json_encode(array("message" => "Unable to register the user."));
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

$TourDate = $data->TourDate;
$UserId = $data->UserId;

$stmt = $conn->prepare("CALL Instour('$TourDate','$UserId','1','Active','$CreatedById')");

if($stmt->execute()){
        http_response_code(200);
        echo json_encode(array("message" => "Wine added successfully."));
    }
    else{
        http_response_code(400);
        echo json_encode(array("message" => "Unable to register the user."));
    }
    
}else{

    
$TourDate = $data->TourDate;
$UserId = $data->UserId;

$stmt = $conn->prepare("CALL Instour('$TourDate','$UserId','1','Active','$CreatedById')");

if($stmt->execute()){
        http_response_code(200);
        echo json_encode(array("message" => "Wine added successfully."));
    }
    else{
        http_response_code(400);
        echo json_encode(array("message" => "Unable to register the user."));
    }
}

}catch (Exception $e){

    http_response_code(401);

    echo json_encode(array(
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));

    // If user is super admin then register.
}

}

?>