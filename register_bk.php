<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once './config/database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$firstname = '';
$lastname = '';
$email = '';
$password = '';
//$RoleId = '';
$mobile = '';
$getConnection	 = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$firstname = $data->firstname;
$lastname = $data->lastname;
$email = $data->email;
$password = $data->password;
$mobile = $data->mobile;
//$RoleId = $data->RoleId;

$table_name = 'user';
$stmt = $conn->prepare("SELECT count(*) as cntUser FROM " . $table_name . " WHERE email=:email");
   $stmt->bindValue(':email', $email, PDO::PARAM_STR);
   $stmt->execute(); 
   $count = $stmt->fetchColumn();

if($count > 0){
	$stmt->execute();
    http_response_code(400);
	echo json_encode(array("message" => "Email already exist"));
}
else{

$table_name = 'user';
$query = "INSERT INTO " . $table_name . "
                SET firstname = :firstname,
                    lastname = :lastname,
                    email = :email,
                    password = :password,
                    mobile = :mobile";

$stmt = $conn->prepare($query);
$stmt->bindParam(':firstname', $firstname);
$stmt->bindParam(':lastname', $lastname);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':mobile', $mobile);
//$stmt->bindParam(':RoleId', $RoleId);

$password_hash = password_hash($password, PASSWORD_BCRYPT);
$stmt->bindParam(':password', $password_hash);

if($stmt->execute()){
	http_response_code(200);
    echo json_encode(array("message" => "User was successfully registered."));
}
else{
    http_response_code(400);
	echo json_encode(array("message" => "Unable to register the user."));
}

}
?>