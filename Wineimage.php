<?php
include_once 'error.php';
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
$jwt = $arr[1];
if($jwt){

try {

$decoded = JWT::decode($jwt, $secret_key, array('HS256'));

$WineId = '';
$OriginalFileName = '';
$FileName = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();
$file = $_FILES['wine_pic'];
        $allowedExtensions = array( "jpg","png","JPG","JPEG","jpeg","PNG","gif","GIF");
        function isAllowedExtension($file){
          global $allowedExtensions;
          return in_array(end(explode(".", $file)), $allowedExtensions);
        }
        if($file['error'] == UPLOAD_ERR_OK){
            
            if(isAllowedExtension($file['name'])){
                
                $target_path = 'WineImage/';
                $uploadfile1 = time().'_'.$_FILES['wine_pic']['name'];
                $target_path = $target_path .$uploadfile1; 
                if(move_uploaded_file($_FILES['wine_pic']['tmp_name'], $target_path)){

$WineId = $data->WineId;
$OriginalFileName = $data->OriginalFileName;
$FileName = $data->FileName;

$stmt = $conn->prepare("CALL InsWineimage('$WineId','$OriginalFileName','$FileName','1','Active','$CreatedById')");

if($stmt->execute()){
        http_response_code(200);
        echo json_encode(array("message" => "Wine added successfully."));
    }
    else{
        http_response_code(400);
        echo json_encode(array("message" => "Unable to register the user."));
        }

    }
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