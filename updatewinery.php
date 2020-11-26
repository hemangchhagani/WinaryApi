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
//echo "<pre>"; print_r($data->Name); exit;

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];
if($jwt){

try {

$decoded = JWT::decode($jwt, $secret_key, array('HS256'));

$Name ="";
$Description ="";
$AddressLine1 ="";
$AddressLine2 ="";
$Email ="";
$PhoneNumber ="";
$Mobile ="";
$Latitude ="";
$Longitude ="";
$WineTypeIds ="";
//$CreatedById ="";
$Id ="";
$Images = "";
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$Name = $data->Name;
$Description = $data->Description;
$AddressLine1 = $data->AddressLine1;
$AddressLine2 = $data->AddressLine2;
$Email = $data->Email;
$PhoneNumber = $data->PhoneNumber;
$Mobile = $data->Mobile;
$Latitude = $data->Latitude;
$Longitude = $data->Longitude;
$WineTypeIds = $data->WineTypeIds;
$Id = $data->Id;
$Images = $data->Images;

//$stmt = $conn->prepare("CALL Updatewinerydesc('','','','','$Email','$PhoneNumber', '$Mobile' ,'$Latitude','$Longitude','1','Active','$ModifiedById' ,'$Id')");

$table_name = 'winery';

$stmt = $conn->prepare("UPDATE " . $table_name . "
                SET Name = :Name,
                Description = :Description,
                AddressLine1 = :AddressLine1,
                AddressLine2 = :AddressLine2,
                Email = :Email,
                PhoneNumber = :PhoneNumber,
                Mobile = :Mobile,
                Latitude = :Latitude,
                Longitude = :Longitude
                WHERE Id =:Id");

$stmt->bindParam(':Name', $Name );
$stmt->bindParam(':Description',$Description );
$stmt->bindParam(':AddressLine1', $AddressLine1 );
$stmt->bindParam(':AddressLine2', $AddressLine2 );
$stmt->bindParam(':Email', $Email );
$stmt->bindParam(':PhoneNumber', $PhoneNumber );
$stmt->bindParam(':Mobile', $Mobile );
$stmt->bindParam(':Latitude', $Latitude );
$stmt->bindParam(':Longitude', $Longitude );
$stmt->bindParam(':Id', $Id );
$stmt->execute();
$table_name2 = 'winetypemapping';


$stmt2 = $conn->prepare("UPDATE ".$table_name2." SET StatusId = :StatusId, StatusName = :StatusName  WHERE WineryId =:WineryId ");
$data = [
    'WineryId' =>$Id,
    'StatusName' =>  'InActive',
    'StatusId' => 0
];
$stmt2->execute($data);

$ok = true;
$StatusId = 1;
$StatusName = "Active";
$CreatedById = "1";
foreach($WineTypeIds  as $value ){

$WineTypeId = $value;
$query3 = "INSERT INTO " . $table_name2 . "
                SET WineryId = :WineryId,
                    WineTypeId = :WineTypeId,
                    StatusId = :StatusId,
                    StatusName = :StatusName,
                    CreatedById = :CreatedById";

$stmt3 = $conn->prepare($query3);
$stmt3->bindParam(':WineryId', $Id);
$stmt3->bindParam(':WineTypeId', $WineTypeId );
$stmt3->bindParam(':StatusId', $StatusId);
$stmt3->bindParam(':StatusName', $StatusName);
$stmt3->bindParam(':CreatedById', $CreatedById);
$stmt3->execute();
}
/*
$table_name2 = 'wineryimagemapping';

$stmt2 = $conn->prepare("UPDATE ".$table_name2." SET StatusId = :StatusId, StatusName = :StatusName  WHERE WineryId =:WineryId ");
$data = [
    'WineryId' =>$Id,
    'StatusName' =>  'InActive',
    'StatusId' => 0
];
$stmt2->execute($data);
*/
foreach($Images as $image){

$oldFileName =$image->fileName;
$Filebase64 =$image->base64;
$Filextension = $image->filextension;

$datetime = date("Y-m-d h:i:s");
$timestamp = md5(uniqid(rand(), true));

$data = explode( ',', $Filebase64 );

$imgdata = base64_decode($data[1]);

$f = finfo_open();
$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
$temp=explode('/',$mime_type);

$path = "WineryImage/$timestamp.$Filextension";

file_put_contents($path,base64_decode($data[1]));

//echo "Successfully Uploaded->>> $timestamp.$temp[1]";

$FileName = $timestamp.".".$Filextension;

$stmt = $conn->prepare("CALL Inswineryimage('$Id','$oldFileName','$FileName','1','Active','$CreatedById')");

$stmt->execute();

}

if($ok){
   
    
        http_response_code(200);
       
         echo json_encode(array( "status" => "True","message" => "Winery updated successfully."));
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