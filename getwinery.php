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
$Id ="";


$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$conn1 = $databaseService->getConnection();

$Id = $data->Id;

$stmt = $conn->prepare("SELECT * FROM `winery` where `Id` = :Id  AND StatusId=1 ");
        
        $databind = array('Id'=>$Id );
        
        $stmt->execute($databind);
      // $rowimage1 = $stmtimage->fetchall(PDO::FETCH_ASSOC);


if($stmt->execute()){
    $value = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $WineryId = $value[Id];
         $rowdetailsreturn = array(
        'Id'=>$value[Id],
        'Name'=>$value[Name],
        'Description'=>$value[Description],
        'StateId'=>$value[StateId],
        'StateName'=>$value[StateName],
        'CityId'=>$value[CityId],
        'CityName'=>$value[CityName],
        'AddressLine1'=>$value[AddressLine1],
        'AddressLine2'=>$value[AddressLine2],
        'Email'=>$value[Email],
        'PhoneNumber'=>$value[PhoneNumber],
        'Mobile'=>$value[Mobile],
        'Latitude'=>$value[Latitude],
        'Longitude'=>$value[Longitude],
        );
        $stmtimage = $conn1->prepare("SELECT * FROM `wineryimagemapping` where `WineryId` = :WineryId  AND StatusId=1 ");
        
        $databind = array('WineryId'=>$WineryId );
        
        $stmtimage->execute($databind);
        $rowimage1 = $stmtimage->fetchall(PDO::FETCH_ASSOC);
        $rowdetailsreturn['Images'] = $rowimage1;
        
        $stmtmapping = $conn1->prepare("SELECT * FROM `winetypemapping` where `WineryId` = :WineryId  AND StatusId=1 ");
        
        $databindmapping = array('WineryId'=>$WineryId );
        
        $stmtmapping->execute($databindmapping);
        $rowmappingtype = $stmtmapping->fetchall(PDO::FETCH_ASSOC);
        
        $rowdetailsreturn['WineTypeIds'] = $rowmappingtype;
       //  array_push($result , $rowdetailsreturn);


    
        http_response_code(200);
        echo json_encode(array(  "Data" => $rowdetailsreturn , "status" => "True", "message" => "Winery Details."));
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