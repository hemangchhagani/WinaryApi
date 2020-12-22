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
$UserTypeId = "";
$UserTypeId  = $data->UserTypeId;


$stmt = $conn->prepare("SELECT wtu.*,wt.Name AS WineTypeName
FROM `winetypeusermapping` AS wtu 
INNER JOIN winetype AS wt on wtu.WineTypeId = wt.Id
where wtu.UserTypeId =:UserTypeId And wtu.StatusId = 1 ");
$datarow = array("UserTypeId" => $UserTypeId);

if($stmt->execute($datarow)){
   
$row = $stmt->fetchall(PDO::FETCH_ASSOC);
        http_response_code(200);
        echo json_encode(array(  "Data" => $row , "status" => "True", "message" => "Winetypeuser Details."));
}
else{
        http_response_code(400);
        echo json_encode(array( "message" => "No data found."));
}

?>