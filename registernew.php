<?php
//include_once 'error.php';

include_once './config/database.php';
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


/*echo json_encode(array(
    "message" => "sd" .$arr[1]
));*/

$jwt = $arr[1];

if($jwt){

    try {

        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        // Access is granted. Add code of the operation here 

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
                    mobile = :mobile,statusname = :statusname";

$stmt = $conn->prepare($query);
$stmt->bindParam(':firstname', $firstname);
$stmt->bindParam(':lastname', $lastname);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':mobile', $mobile);
$stmt->bindParam(':statusname', 'Active');
//$stmt->bindParam(':RoleId', $RoleId);

$password_hash = password_hash($password, PASSWORD_BCRYPT);
$stmt->bindParam(':password', $password_hash);

if($stmt->execute()){
	http_response_code(200);
    echo json_encode(array("Status" => "True","message" => "User was successfully registered."));
}
else{
    http_response_code(400);
	echo json_encode(array("Status" => "False","message" => "Unable to register the user."));
}

}





        //echo json_encode(array(
       //    "message" => "Access granted:",
            //"error" => $e->getMessage()
        //));

    }catch (Exception $e){

    http_response_code(401);

    echo json_encode(array(
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));
}

}

?>