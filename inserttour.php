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
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

//echo "<pre>"; print_r($data);exit;
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];
if($jwt){

try {

$decoded = JWT::decode($jwt, $secret_key, array('HS256'));
$TourDate =" ";
$UserId =" ";
$StatusId =" ";
$CreatedById ="";
$TourDetails = "";

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$conn1 = $databaseService->getConnection();

$TourDate = $data->TourDate;
$UserId = $data->UserId;
$StatusId = $data->StatusId;
$CreatedById = $data->CreatedById;
$TourDetails =$data->TourDetails;


$stmt = $conn->prepare("CALL Instour('$TourDate','$UserId','1','Active','$CreatedById',@Last_ID)");
$stmt->execute();

$rs2 = $conn->query("SELECT @Last_ID  as id");
$row = $rs2->fetchObject();
$last_id = $row->id;


$ok = true;
foreach($TourDetails as $tourdetails){

    
$WineryId =$tourdetails->WineryId;
$Feedback =$tourdetails->Feedback;
$Rating = $tourdetails->Rating;
$SequenceOrder = $tourdetails->SequenceOrder;
$StartTime = $tourdetails->StartTime;
$EndTime = $tourdetails->EndTime;

$stmt4 = $conn1->prepare("CALL Instourdetails('$last_id','$WineryId','$Feedback','$Rating','$SequenceOrder','$StartTime','$EndTime','1','Active','1')");


$stmt4->execute();

}




if($ok){
    
   
    
        http_response_code(200);
       
         echo json_encode(array( "status" => "True","message" => "Tour added successfully."));
}
else{
        http_response_code(400);
        echo json_encode(array("message" => "Unable to register the user."));
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