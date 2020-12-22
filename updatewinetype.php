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
$Id = "";
$Name ="";
$UserTypes = "";


$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$Name = $data->Name;
$Id = $data->Id;
$UserTypes = $data->UserTypes;

//$CreatedById = $data->CreatedById;
$CreatedById = 1;

$query = "UPDATE winetype
                SET Name = :Name
                    WHERE Id =:Id ";
                    //echo  $query;exit;

$stmt = $conn->prepare($query);
$stmt->bindParam(':Id', $Id);
$stmt->bindParam(':Name', $Name);

$query3 = "DELETE FROM winetypeusermapping WHERE WineTypeId=:WineTypeId";

$stmt3 = $conn->prepare($query3);
$stmt3->bindParam(':WineTypeId', $Id);
$stmt3->execute();



$match = array_diff($UserTypes,$value2); 
        foreach($UserTypes as $key =>  $val){
            
            $query4 = "INSERT INTO winetypeusermapping
                            SET WineTypeId = :WineTypeId,
                                UserTypeId = :UserTypeId,
                                StatusId = 1,
                                StatusName = 'Active',
                                CreatedById = :CreatedById";
            
            $stmt4 = $conn->prepare($query4);
            $stmt4->bindParam(':WineTypeId', $Id);
            $stmt4->bindParam(':UserTypeId', $val );
            $stmt4->bindParam(':CreatedById', $CreatedById);
            $stmt4->execute();
        }

if($stmt->execute()){
http_response_code(200);
echo json_encode(array( "status" => "True","message" => "WineType Updated successfully."));
}
else{
        http_response_code(400);
        echo json_encode(array("message" => "Unable to complete request."));
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