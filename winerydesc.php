<?php
include_once 'error.php';
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

        $Name =" ";
        $Description =" ";
        $WineryId =" ";
        $WineryName=" ";
        $WineTypeId =" ";
        $Rate =" ";
        $CreatedById ="";

        $databaseService = new DatabaseService();
        $conn = $databaseService->getConnection();

        $Name = $data->Name;
        $Description = $data->Description;
        $AddressLine1 = $data->AddressLine1;
        $AddressLine2 = $data->AddressLine2;
        $Email = $data->Email;
        $PhoneNumbe = $data->PhoneNumbe;
        $Mobile = $data->Mobile;
        $Latitude = $data->Latitude;
        $Longitude = $data->Longitude;
        $CreatedById = $data->CreatedById;

        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $stmt = $conn->prepare("CALL Inswinerydesc('$Name','$Description','$AddressLine1','$AddressLine2','$Email','$PhoneNumbe','$Mobile','$Latitude','$Longitude','1','Active','$CreatedById')");
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
            $stmt = $conn->prepare("CALL Inswinerydesc('$Name','$Description','$AddressLine1','$AddressLine2','$Email','$PhoneNumbe','$Mobile','$Latitude','$Longitude','1','Active','$CreatedById')");
            if($stmt->execute()){
                http_response_code(200);
                echo json_encode(array("message" => "Wine added successfully."));
            }
            else{
                http_response_code(400);
                echo json_encode(array("message" => "Unable to register the user."));
            }
        }
        else{

            $stmt = $conn->prepare("CALL Inswinerydesc('$Name','$Description','$AddressLine1','$AddressLine2','$Email','$PhoneNumbe','$Mobile','$Latitude','$Longitude','1','Active','$CreatedById')");
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