<?php

	$mail = new PHPMailer;

	// SMTP configuration
	$mail->isSMTP();
	$mail->Host = 'ssl://smtp.mail.yahoo.com';
	$mail->SMTPAuth = true;
	$mail->Username = 'vtech.admin@vtechsolution.us'; 
	$mail->Password = 'nvjfcuouvwkelida';
	$mail->SMTPSecure = 'ssl';
	$mail->Port = 465;

	// Set email format to HTML
	$mail->isHTML(true);

?>
