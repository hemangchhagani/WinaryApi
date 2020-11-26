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
//echo "<pre>";print_r($data->Name);exit;
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];
if($jwt){

try {

$decoded = JWT::decode($jwt, $secret_key, array('HS256'));
$Id = "";
$Name =" ";
$Description =" ";
$WineryId =" ";
$WineryName=" ";
$WineTypeId =" ";
$WineTypeName =" ";

$Rate =" ";
$ModifiedById ="";

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$Id = $data->Id;
$Name = $data->Name;
$Description = $data->Description;
$WineryId = $data->WineryId;
$WineryName = $data->WineryName;
$WineTypeId = $data->WineTypeId;
$WineTypeName = $data->WineTypeName;
$Rate = $data->Rate;

$ModifiedById = '1';


$table_name = 'wine';
$query = "UPDATE " . $table_name . "
                SET Name = :Name,
                Description = :Description,
                WineryId = :WineryId,
                WineryName = :WineryName,
                WineTypeId = :WineTypeId,
                WineTypeName =:WineTypeName,
                Rate = :Rate
                    WHERE Id =:Id ";
                    //echo  $query;exit;

$stmt = $conn->prepare($query);
$stmt->bindParam(':Id', $Id);
$stmt->bindParam(':Name', $Name);
$stmt->bindParam(':Description', $Description);
$stmt->bindParam(':WineryId', $WineryId);
$stmt->bindParam(':WineryName', $WineryName);
$stmt->bindParam(':WineTypeId', $WineTypeId);
$stmt->bindParam(':WineTypeName', $WineTypeName);
$stmt->bindParam(':Rate', $Rate);


$stmt->execute();


//$stmt = $conn->prepare("CALL UpdateWine(?,?,?,?,?,?,?,?,?)");
//$array = array($Name,$Description,$WineryId,$WineryName,$WineTypeId,$Rate,'1','Active',$ModifiedById);

$Images = $data->Images;




// $table_name2 = 'wineimagemapping';

// $stmt2 = $conn->prepare("UPDATE ".$table_name2." SET StatusId = :StatusId, StatusName = :StatusName  WHERE WineId =:WineId ");
// $data = [
//     'WineId' =>$Id,
//     'StatusName' =>  'InActive',
//     'StatusId' => 0
// ];
// $stmt2->execute($data);



$ok = true;

foreach($Images as $image){

$OldFileName =$image->fileName;
$Filebase64 =$image->base64;
$Filextension = $image->filextension;

$datetime = date("Y-m-d h:i:s");
$timestamp = md5(uniqid(rand(), true));

$data = explode( ',', $Filebase64 );

$imgdata = base64_decode($data[1]);

$f = finfo_open();
$mime_type = finfo_buffer($f, $imgdata, FILEINFO_MIME_TYPE);
$temp=explode('/',$mime_type);

//path file
$path = "WineImage/$timestamp.$Filextension";

file_put_contents($path,base64_decode($data[1]));

//echo "Successfully Uploaded->>> $timestamp.$temp[1]";

$FileName = $timestamp.".".$Filextension;

$stmt3 = $conn->prepare("CALL InsWineimage('$Id','$OldFileName','$FileName','1','Active','1')");

$stmt3->execute();

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