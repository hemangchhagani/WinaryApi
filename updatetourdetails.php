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
//echo "<pre>";print_r($data->Name);exit;
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];
if($jwt){

try {

$decoded = JWT::decode($jwt, $secret_key, array('HS256'));
$Id = "";

$Feedback = "";
$Rating = "";
$SequenceOrder = "";
$StartTime= "";
$EndTime = "";

$StatusId =" ";
$ModifiedById ="";

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$Id = $data->Id;

$Feedback =$data->Feedback;
$Rating = $data->Rating;
$SequenceOrder = $data->SequenceOrder;
$StartTime = $data->StartTime;
$EndTime = $data->EndTime;


$ModifiedById = '1';


$table_name = 'tourdetails';
$query = "UPDATE " . $table_name . "
                SET Feedback = :Feedback,Rating = :Rating, SequenceOrder = :SequenceOrder,StartTime = :StartTime, EndTime = :EndTime   WHERE Id =:Id ";
                    //echo  $query;exit;

$stmt = $conn->prepare($query);
$stmt->bindParam(':Id', $Id);
$stmt->bindParam(':Feedback', $Feedback);
$stmt->bindParam(':Rating', $Rating);
$stmt->bindParam(':SequenceOrder', $SequenceOrder);
$stmt->bindParam(':StartTime', $StartTime);
$stmt->bindParam(':EndTime', $EndTime);

if($stmt->execute()){
       http_response_code(200);
       echo json_encode(array( "status" => "True","message" => "Tour Details Updated successfully."));
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