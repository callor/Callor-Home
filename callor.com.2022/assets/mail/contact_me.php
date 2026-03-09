<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Check for empty fields
if(empty($_POST['name'])      ||
   empty($_POST['email'])     ||
   empty($_POST['phone'])     ||
   empty($_POST['message'])   ||
   !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
   {
   echo "No arguments Provided!";
   return false;
   }
   





$name = strip_tags(htmlspecialchars($_POST['name']));
$email_address = strip_tags(htmlspecialchars($_POST['email']));
$phone = strip_tags(htmlspecialchars($_POST['phone']));
$message = strip_tags(htmlspecialchars($_POST['message']));
   
// Create the email and send the message
$to = 'callor88@naver.com'; // Add your email address in between the '' replacing yourname@yourdomain.com - This is where the form will send a message to.
$email_subject = "홈페이지 문의:  $name";
$email_body = 
		"<h2 style='color:blue;'>문의내용</h2>".
		"<p>이름: $name</p>".
		"<p>Email: $email_address</p>".
		"<p>연락처: $phone</p>".
		"<p>메시지 : $message</p>";
// $headers = "From: webmaster<callor@callor.com>\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
// $headers .= "Reply-To: $email_address";   

$headers = implode("\r\n", [
	'From: webmaster <callor@callor.com> ',
	'Reply-To: callor@callor.com ',
	'X-Mailer: PHP/' . PHP_VERSION,
	'MIME-Version: 1.0',
	'Content-type: text/html; charset=utf-8'
]);

$result = mail($to,$email_subject,$email_body,$headers);
echo $headers . $result;
// return true;         
?>
