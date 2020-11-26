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


// Access is granted. Add code of the operation here 

	$email = '';
	$password = '';
	$confirmpassword = '';
	$otpverify = '';


$getConnection	 = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));
	
	$email = $data->email;
	$password = $data->password;
	$confpassword = $data->confirmpassword;
	$otpverify = $data->otpverify;

	//$RoleId = $data->RoleId;

	$table_name = 'user';
	$stmt = $conn->prepare("SELECT Id,email,password,otpverify,otpverifytime,count(*) as cntUser  FROM " . $table_name . " WHERE email=:email AND otpverify=:otpverify ");
	
	   $stmt->bindValue(':email', $email, PDO::PARAM_STR);
	   $stmt->bindValue(':otpverify', $otpverify, PDO::PARAM_STR);
	   $stmt->execute(); 
	   $row = $stmt->fetch(PDO::FETCH_ASSOC);
//var_dump( $row);
	if($row['cntUser'] > 0){
	    
  $Id = $row['Id']; 
    $query = "UPDATE " . $table_name . "
                SET password = :password
                    WHERE Id =:Id ";
                    //echo  $query;exit;

$stmt = $conn->prepare($query);

$stmt->bindParam(':Id', $Id);

$password_hash = password_hash($password, PASSWORD_BCRYPT);
$stmt->bindParam(':password', $password_hash);

if($stmt->execute()){
	http_response_code(200);
    echo json_encode(array("status" =>'true',"message" => "Your password has been reset successfully."));
}
    
	}



   

?>