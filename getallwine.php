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

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];
if($jwt){

try {

$decoded = JWT::decode($jwt, $secret_key, array('HS256'));


$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$conn1 = $databaseService->getConnection();



$stmt = $conn->prepare("CALL getWinealldetails()");


if($stmt->execute()){
    
    $row = $stmt->fetchall(PDO::FETCH_ASSOC);
     $result = array() ;
    foreach($row as $value){
        
        $rowdetailsreturn =  array("Id" =>$value[Id] ,"Name"=>$value[Name],
        "Description"=>$value[Description],
        "WineryId"=>$value[WineryId],
        "WineryName"=>$value[WineryName],
        "WineTypeId"=>$value[WineTypeId],
         "WineTypeName"=>$value[WineTypeName],
          "Rate"=>$value[Rate]);
        
        
        $Id  = $value[Id];
        $stmtimage = $conn1->prepare("SELECT * FROM `wineimagemapping` where `WineId` = :WineId  AND StatusId=1 ");
        
        $databind = array('WineId'=>$Id );
        
        $stmtimage->execute($databind);
        $rowimage1 = $stmtimage->fetchall(PDO::FETCH_ASSOC);
        $rowdetailsreturn['Images'] = $rowimage1;
        array_push($result , $rowdetailsreturn);
    }
   
        

 
        http_response_code(200);
        echo json_encode(array(  "Data" => $result , "status" => "True", "message" => "Wine Details."));
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