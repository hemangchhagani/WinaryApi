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
$Name ="";
$UserTypes = "";


$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$conn2 = $databaseService->getConnection();

$Name = $data->Name;
$UserTypes  = $data->UserTypes;


//$CreatedById = $data->CreatedById;
$CreatedById = 1;

$stmt = $conn->prepare("CALL Inswinetype('$Name','1','Active','$CreatedById',@Last_ID)");
$stmt->execute();

$rs2 = $conn->query("SELECT @Last_ID  as id");
$row = $rs2->fetchObject();
$last_id = $row->id;

$StatusId = 1;
$StatusName = "Active";
$CreatedById = "1";

$table_name2 = 'winetypeusermapping';
$ok = true;
foreach($UserTypes  as $value ){

$UserTypeId = $value;
$query = "INSERT INTO " . $table_name2 . "
                SET WineTypeId = :WineTypeId,
                    UserTypeId = :UserTypeId,
                    StatusId = :StatusId,
                    StatusName = :StatusName,
                    CreatedById = :CreatedById";

$stmt2 = $conn2->prepare($query);
$stmt2->bindParam(':WineTypeId', $last_id);
$stmt2->bindParam(':UserTypeId', $UserTypeId );
$stmt2->bindParam(':StatusId', $StatusId);
$stmt2->bindParam(':StatusName', $StatusName);
$stmt2->bindParam(':CreatedById', $CreatedById);
$stmt2->execute();

}
if($ok){
   
    
        http_response_code(200);
       
         echo json_encode(array( "status" => "True","message" => "WineType added successfully."));
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