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

$data = json_decode(file_get_contents("php://input"),TRUE);
//echo "<pre>"; print_r($data); exit;

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];
if($jwt){

try {

$decoded = JWT::decode($jwt, $secret_key, array('HS256'));
$Name ="";
$Description ="";
$StateId ="";
$StateName ="";
$CityId ="";
$CityName ="";
$AddressLine1 ="";
$AddressLine2 ="";
$Email ="";
$PhoneNumber ="";
$Mobile ="";
$Latitude ="";
$Longitude ="";
$CreatedById ="";
$WineTypeIds ="";
$Images = "";
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();


$Name = $data[Name];
$Description = $data[Description];
$StateId = $data[StateId];
$StateName = $data[StateName];
$CityId = $data[CityId];
$CityName = $data[CityName];
$WineTypeIds = $data[WineTypeIds];
$AddressLine1 = $data[AddressLine1];
$AddressLine2 = $data[AddressLine2];
$Email = $data[Email];
$PhoneNumber = $data[PhoneNumber];
$Mobile = $data[Mobile];
$Latitude = $data[Latitude];
$Longitude = $data[Longitude];
$CreatedById = $data[CreatedById];
$Images = $data[Images];

$stmt = $conn->prepare("CALL Inswinerydesc('$Name','$Description','$StateId' ,'$StateName','$CityId','$CityName','$AddressLine1','$AddressLine2','$Email','$PhoneNumber', '$Mobile' ,'$Latitude','$Longitude','1','Active','$CreatedById',@Last_ID)");
 $stmt->execute();
$rs2 = $conn->query("SELECT @Last_ID  as id");
$row = $rs2->fetchObject();
$last_id = $row->id;

$StatusId = 1;
$StatusName = "Active";
$CreatedById = "1";

$table_name2 = 'winetypemapping';
$ok = true;
foreach($WineTypeIds  as $value ){

$WineTypeId = $value;
$query = "INSERT INTO " . $table_name2 . "
                SET WineryId = :WineryId,
                    WineTypeId = :WineTypeId,
                    StatusId = :StatusId,
                    StatusName = :StatusName,
                    CreatedById = :CreatedById";

$stmt = $conn->prepare($query);
$stmt->bindParam(':WineryId', $last_id);
$stmt->bindParam(':WineTypeId', $WineTypeId );
$stmt->bindParam(':StatusId', $StatusId);
$stmt->bindParam(':StatusName', $StatusName);
$stmt->bindParam(':CreatedById', $CreatedById);
$stmt->execute();

}

foreach($Images as $image){

$oldFileName =$image[fileName];
$Filebase64 =$image[base64];
$Filextension = $image[filextension];

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

$stmt = $conn->prepare("CALL Inswineryimage('$last_id','$oldFileName','$FileName','1','Active','$CreatedById')");

$stmt->execute();

}
 
if($ok){
   
    
        http_response_code(200);
       
         echo json_encode(array( "status" => "True","message" => "Winery added successfully."));
}
else{
        http_response_code(400);
        echo json_encode(array( "status" => "False","message" => "Unable to process the request."));
}

}catch (Exception $e){

    http_response_code(401);

    echo json_encode(array( "status" => "False",
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));

    // If user is super admin then register.
}

}

?>