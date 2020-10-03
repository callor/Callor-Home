<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
// Check for empty fields
// if(empty($_POST['name'])      ||
//    empty($_POST['email'])     ||
//    empty($_POST['phone'])     ||
//    empty($_POST['message'])   ||
//    !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
//    {
//    echo "No arguments Provided!";
//    return false;
//    }
   





$name = "홍길동"; //strip_tags(htmlspecialchars($_POST['name']));
$email_address = "callor88@naver.com"; // strip_tags(htmlspecialchars($_POST['email']));
$phone = "0101010101" ; // strip_tags(htmlspecialchars($_POST['phone']));
$message = "Hello" ; //strip_tags(htmlspecialchars($_POST['message']));
   
// Create the email and send the message
$to = 'callor88@naver.com'; // Add your email address in between the '' replacing yourname@yourdomain.com - This is where the form will send a message to.
$email_subject = "Website Contact Form:  $name";
$email_body = "현재 시간 : ". date("H:i:s")."<br/>"; // "You have received a new message from your website contact form.\n\n"."Here are the details:\n\nName: $name\n\nEmail: $email_address\n\nPhone: $phone\n\nMessage:\n$message";
$headers = "From:callor@callor.com\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
$headers .= "Reply-To: $email_address";   

$result = mail($to,$email_subject,$email_body,$headers,"-fcallor@callor.com");
echo $result;
// return true;         
?>
