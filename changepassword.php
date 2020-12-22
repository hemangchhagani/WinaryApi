<?php
include_once './config/database.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$Id = '';
$password = '';
//$RoleId = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
$Id  = $data->Id;
$password = $data->password;
$confirmpassword = $data->confirmpassword;

$table_name = 'user';

/*$query = "SELECT Id, email , password FROM " . $table_name . " WHERE email = ? LIMIT 0,1";
$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $email);
$stmt->execute();
$num = $stmt->rowCount();
if($num > 0){
 $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $Id = $row['Id'];
*/
$query = "UPDATE " . $table_name . " SET password = :password WHERE Id =:Id ";

//echo  $query;exit;

$stmt = $conn->prepare($query);
//$stmt->bindParam(':RoleId', $RoleId);
$stmt->bindParam(':Id', $Id);

$password_hash = password_hash($password, PASSWORD_BCRYPT);
$stmt->bindParam(':password', $password_hash);

if($stmt->execute()){
    http_response_code(200);
    echo json_encode(array("Status"=>"True","message" => "Password was successfully Updated."));
}
else{
    http_response_code(400);
    echo json_encode(array("Status"=>"False","message" => "Unable to Update the Password."));
}

    
}
?>