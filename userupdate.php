<?php
//include_once 'error.php';
include_once 'config/database.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

ini_set("allow_url_fopen", true);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$firstname = '';
$lastname = '';
$email = '';
$mobile = '';
$password = '';


$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$table_name = 'user';
$Id = $data->Id;
$firstname = $data->firstname;
$lastname = $data->lastname;
$password = $data->password;
$email = $data->email;
$mobile = $data->mobile;
/*
$query = "SELECT Id, email FROM " . $table_name . " WHERE email = ? LIMIT 0,1";
$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $email);
$stmt->execute();
$num = $stmt->rowCount();
if($num > 0){
*/

/*$row = $stmt->fetch(PDO::FETCH_ASSOC);
    $Id = $row['Id'];*/
    $query = "UPDATE " . $table_name . "
                SET firstname = :firstname,
                    lastname = :lastname,
                    password = :password,
                    email = :email,
                    mobile = :mobile
                    WHERE Id =:Id ";
                    //echo  $query;exit;

$stmt = $conn->prepare($query);
$stmt->bindParam(':firstname', $firstname);
$stmt->bindParam(':lastname', $lastname);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':mobile', $mobile);

$stmt->bindParam(':Id', $Id);

$password_hash = password_hash($password, PASSWORD_BCRYPT);
$stmt->bindParam(':password', $password_hash);

if($stmt->execute()){
    http_response_code(200);
    echo json_encode(array("Status" => "True", "message" => "User was successfully Updated."));
}
else{
    http_response_code(400);
    echo json_encode(array("Status" => "False","message" => "Unable to Update the user."));
}

    
//}
?>