<?php


// $mail = new PHPMailer;

// 	// SMTP configuration
// 	$mail->isSMTP();
// 	$mail->Host = 'ssl://smtp.mail.yahoo.com';
// 	$mail->SMTPAuth = true;
// 	$mail->Username = 'vtech.admin@vtechsolution.us'; 
// 	$mail->Password = 'nvjfcuouvwkelida';
// 	$mail->SMTPSecure = 'ssl';
// 	$mail->Port = 465;

// 	// Set email format to HTML
// 	$mail->isHTML(true);
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\SMTP;

	require 'PHPMailer/src/Exception.php';
	require 'PHPMailer/src/PHPMailer.php';
	require 'PHPMailer/src/SMTP.php';
	require 'vendor/autoload.php';


	$mail = new PHPMailer(true);

    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.office365.com';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'vtech.admin@vtechsolution.us';                     // SMTP username
    $mail->Password   = 'vTech@123';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    $mail->Port  = 587;   
    $mail->isHTML(true);
?>
