<?php

	$mail->Body = $mailContent;

	// Send email
	if(!$mail->send()){
	   echo 'Mailer Error: ' . $mail->ErrorInfo;
	}

	$mail->clearAddresses();

?>