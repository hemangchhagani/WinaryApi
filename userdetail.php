<?php
include_once 'error.php';
ini_set("allow_url_fopen", true);
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

$email = '';


$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();



$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
/*echo json_encode(array(
    "message" => "sd" .$arr[1]
));*/
$jwt = $arr[1];

if($jwt){

try {


$decoded = JWT::decode($jwt, $secret_key, array('HS256'));
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'];

$table_name = 'user';
$stmt = $conn->prepare("SELECT *  FROM " . $table_name . " WHERE email=:email");
   $stmt->bindValue(':email', $email, PDO::PARAM_STR);
   $stmt->execute(); 
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
   $data2['firstname'] = $row['firstname'];
   $data2['lastname'] = $row['lastname'];
    $data2['email'] = $row['email'];
     $data2['mobile'] = $row['mobile'];
   http_response_code(200);
   echo  json_encode(array("Status"=>"true",
       "message" => "User details." , "Data" => $data2
 ));

	//echo json_encode(array(
    //       "message" => "Access granted:",
            //"error" => $e->getMessage()
    //    ));

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