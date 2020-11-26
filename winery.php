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

        $firstname = '';
        $lastname = '';
        $email = '';
        $password = '';
        $mobile = '';
        $getConnection	 = null;

        $databaseService = new DatabaseService();
        $conn = $databaseService->getConnection();

        $data = json_decode(file_get_contents("php://input"));

        $Name =" ";
        $Description =" ";
        $WineryId =" ";
        $WineryName=" ";
        $WineTypeId =" ";
        $Rate =" ";
        $CreatedById ="";
        $ModifiedById = "";

        $databaseService = new DatabaseService();
        $conn = $databaseService->getConnection();

        $Name = $data->Name;
        $Description = $data->Description;
        $WineryId = $data->WineryId;
        $WineryName = $data->WineryName;
        $WineTypeId = $data->WineTypeId;
        $Rate = $data->Rate;
        $StatusId = $data->StatusId;
        $StatusName = $data->StatusName;
        $ModifiedById = $data->ModifiedById;
        $CreatedById = $data->CreatedById;



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $stmt = $conn->prepare("CALL Inswine('$Name','$Description','$WineryId','$WineryName','$WineTypeId','$Rate','1','Active','$CreatedById')");
            if($stmt->execute()){
                http_response_code(200);
                echo json_encode(array("message" => "Wine added successfully."));
            }
            else{
                http_response_code(400);
                echo json_encode(array("message" => "Unable to add the record."));
            }
        }
        else if ($_SERVER['REQUEST_METHOD'] == 'PUT') { 

            $stmt = $conn->prepare("CALL UpdateWine('$Name','$Description','$WineryId','$WineryName','$WineTypeId','$Rate','1','Active','$ModifiedById')");
            if($stmt->execute()){
                http_response_code(200);
                echo json_encode(array("message" => "Wine update successfully."));
            }
            else{
                http_response_code(400);
                echo json_encode(array("message" => "Unable to update the record."));
            }

            
        }
        else{
    // get method call 
          $stmt = $conn->prepare("CALL Inswine('$Name','$Description','$WineryId','$WineryName','$WineTypeId','$Rate','1','Active','$CreatedById')");
            if($stmt->execute()){
                http_response_code(200);
                echo json_encode(array("message" => "Wine added successfully."));
            }
            else{
                http_response_code(400);
                echo json_encode(array("message" => "Unable to add the record."));
            }
      }

	//echo json_encode(array(
       //    "message" => "Access granted:",
            //"error" => $e->getMessage()
        //));

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