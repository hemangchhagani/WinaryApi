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
$Id = "";
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$data = json_decode(file_get_contents("php://input"));
$Id = $data->Id;
$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$arr = explode(" ", $authHeader);
$jwt = $arr[1];
if($jwt){

try {

$decoded = JWT::decode($jwt, $secret_key, array('HS256'));


$stmt = $conn->prepare("SELECT * FROM `user` where statusId ='1' ");

if($stmt->execute()){
    
    $row = $stmt->fetchall(PDO::FETCH_ASSOC);
    
    
    http_response_code(200);
    
   // $modifiedPro = $row->getIterator();
foreach($row as $key=>$row2) {
     $row3['Id'] = "{$row2[Id]}";
     $row3['Name'] = "{$row2[firstname]} {$row2[lastname]}";
     $row3['Firstname'] = "{$row2[firstname]}";
     $row3['Lastname'] = "{$row2[lastname]}";
     $row3['Email'] = "{$row2[email]}";
     $row3['Mobile'] = "{$row2[mobile]}";
      $row3['DateOfBirth'] = "{$row2[DateOfBirth]}";
      $row3['Password'] = "{$row2[password]}";
   
    
    //$modifiedPro[$key]->name ="{$row[firstname]} {$row[lastname]}";
    $modifiedPro[$key] =$row3;
  
}
        echo json_encode(array(  "Data" => $modifiedPro , "status" => "True", "message" => "User Details."));
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