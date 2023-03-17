<?php
require_once "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = '';
$db_name = "lab4_messageassignment";


$conn = new mysqli($servername, $username, $password,$db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM messages WHERE datetime <= '".date('Y-m-d H:i:s')."' AND status = 0";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      sendmil($row['email'],$row['message']);
      $sql2 = "UPDATE messages SET status=1 WHERE id=".$row['id'];
      if ($conn->query($sql2) === TRUE) {
        // echo "Record updated successfully";
      } 
    }
}


function sendmil($to,$message){
  $mail = new PHPMailer(); 
  
  
  $mail->IsSMTP(); 
  $mail->SMTPDebug = 1; 
  $mail->SMTPAuth = true; 
  $mail->SMTPSecure = 'ssl'; 
  $mail->Host = "smtp.gmail.com";
  
  $mail->Port = 465; 
  $mail->IsHTML(true);
  //Username to use for SMTP authentication
  $mail->Username   = "[SMTP_EMAIL_HERE]";
  $mail->Password   = "[PASSWORE_HERE]";
  //Set who the message is to be sent from
  $mail->setFrom('[FROM_EMAIL]', '[FROM_NAME]');
  //Set who the message is to be sent to
  $mail->addAddress($to);
  //Set the subject line
  $mail->Subject = '';
  //Read an HTML message body from an external file, convert referenced images to embedded,
  //convert HTML into a basic plain-text alternative body
  $mail->msgHTML($message);
  
  //send the message, check for errors
  if (!$mail->send()) {
      // echo "Mailer Error: " . $mail->ErrorInfo;
  } else {
      // echo "Message sent!";
  }
}




