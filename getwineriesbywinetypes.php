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


$WineTypeIds =  "";

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$conn1 = $databaseService->getConnection();

//$Id =  $data->Id;
$WineTypeIds =  $data->WineTypeIds;
$in  = str_repeat('?,', count($WineTypeIds) - 1) . '?';
$stmt = $conn->prepare("Select winery.Id,winery.name,winery.Description,winery.AddressLine1,winery.AddressLine2,winery.Email,winery.PhoneNumber,winery.Mobile,winery.Latitude,winery.Longitude,winetypemapping.WineTypeId from winery inner join winetypemapping on winery.Id = winetypemapping.WineryId
WHERE winetypemapping.WineTypeId In ($in) AND winery.StatusId = 1 AND winetypemapping.StatusId =1");


if($stmt->execute($WineTypeIds)){
    
    $row = $stmt->fetchall(PDO::FETCH_ASSOC);
    $result = array() ;
    foreach($row as $value){
        
        
         $WineryId = $value[Id];
         $rowdetailsreturn = array(
        'Id'=>$value[Id],
        'Name'=>$value[name],
        'Description'=>$value[Description],
        'AddressLine1'=>$value[AddressLine1],
        'AddressLine2'=>$value[AddressLine2],
        'Email'=>$value[Email],
        'PhoneNumber'=>$value[PhoneNumber],
        'Mobile'=>$value[Mobile],
        'Latitude'=>$value[Latitude],
        'Longitude'=>$value[Longitude],
        'WineTypeId'=>$value[WineTypeId],
         
        );
        $stmtimage = $conn1->prepare("SELECT * FROM `wineryimagemapping` where `WineryId` = :WineryId  AND StatusId=1 ");
        
        $databind = array('WineryId'=>$WineryId );
        
        $stmtimage->execute($databind);
        $rowimage1 = $stmtimage->fetchall(PDO::FETCH_ASSOC);
        $rowdetailsreturn['Images'] = $rowimage1;
        
        
         array_push($result , $rowdetailsreturn);

    $i++;
    }
    
    
        http_response_code(200);
        echo json_encode(array(  "Data" => $result , "status" => "True", "message" => "Winery Details."));
}
else{
        http_response_code(400);
        echo json_encode(array("message" => "Unable to register the user."));
}

?>