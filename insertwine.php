<?php
include_once 'error.php';
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
$Name =" ";
$Description =" ";
$WineryId =" ";
$WineryName=" ";
$WineTypeId =" ";
$WineTypeName = " ";
$Rate =" ";
$CreatedById ="";

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$Name = $data->Name;
$Description = $data->Description;
$WineryId = $data->WineryId;
$WineryName = $data->WineryName;
$WineTypeId = $data->WineTypeId;
$WineTypeName = $data->WineTypeName;
$Rate = $data->Rate;
$CreatedById = $data->CreatedById;

$stmt = $conn->prepare("CALL Inswine('$Name','$Description','$WineryId','$WineryName','$WineTypeId','$WineTypeName','$Rate','1','Active','$CreatedById',@Last_ID)");

$stmt->execute();

$Images = $data->Images;
$rs2 = $conn->query("SELECT @Last_ID  as id");
$row = $rs2->fetchObject();
$last_id = $row->id;

$ok = true;
foreach($Images as $image){
  /*  print_r( $image);
    exit;
    */
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

$path = "WineImage/$timestamp.$Filextension";

file_put_contents($path,base64_decode($data[1]));

//echo "Successfully Uploaded->>> $timestamp.$temp[1]";

$FileName = $timestamp.".".$Filextension;

$stmt4 = $conn->prepare("CALL InsWineimage('$last_id','$oldFileName','$FileName','1','Active','$CreatedById')");

$stmt4->execute();

}

if($ok){
   
    
        http_response_code(200);
       
         echo json_encode(array( "status" => "True","message" => "Wine added successfully."));
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