<?php
include_once 'error.php';
include_once 'config/database.php';
require "vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$email = '';

$getConnection	 = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
//echo $email;
//$RoleId = $data->RoleId;

$table_name = 'user';
$stmt = $conn->prepare("SELECT  Id,count(*) as cntUser FROM " . $table_name . " WHERE email=:email");
   $stmt->bindValue(':email', $email, PDO::PARAM_STR);
   $stmt->execute(); 
   $row = $stmt->fetch(PDO::FETCH_ASSOC);
  
   $Id =  $row['Id'];
  // $count = $stmt->fetchColumn();

if($row['cntUser'] > 0){
	

	
//echo(strtotime("now") . "<br>");

 $otpverifytime = strtotime("+1 hours");  
 $otpverify  = rand(10,1000000); 
  //echo json_encode(array(,"status" =>'true',"message" => "ok"));
  
$query = "UPDATE " . $table_name . "
   	SET otpverify = ".$otpverify.",otpverifytime = ".$otpverifytime."
                    WHERE Id = ".$Id." ";
                    //echo  $query;exit;

$stmt = $conn->prepare($query);
//$stmt->bindParam(':otpverify', $otpverify);
//$stmt->bindParam(':otpverifytime', $otpverifytime);
//$stmt->bindParam(':RoleId', $RoleId);
//$stmt->bindParam(':Id', $Id);
//print_r($stmt->execute());exit;


if($stmt->execute()){
	http_response_code(200);
    echo json_encode(array("status" =>'true',"message" => "Plese check your an email for password reset."));
}
    
  // the message
$msg = "Your otp is ".$otpverify."\n it will Expire in 1 hours";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
mail($email,"Reset Email",$msg);



}
else{

  http_response_code(400);
	echo json_encode(array("status" =>'false',"message" => "Unable to find the user."));

}/*
function SendMail($email,$name,$time){
    
    
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'phpmailer/vendor/autoload.php';

// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'mail.wineloversmap.com';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'sendemail@wineloversmap.com';                     // SMTP username
    $mail->Password   = 'gxYA&SJ{N2~j';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('sendemail@wineloversmap.com', 'Mailer');
    $mail->addAddress('hemangchhagani@gmail.com', 'Joe User');     // Add a recipient
    //$mail->addAddress('ellen@example.com');               // Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    // Attachments
   // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
   // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
    
}*/
?>