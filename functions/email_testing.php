<?php
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\SMTP;

	require 'PHPMailer/src/Exception.php';
	require 'PHPMailer/src/PHPMailer.php';
	require 'PHPMailer/src/SMTP.php';
	require 'vendor/autoload.php';


	$mail = new PHPMailer(true);


	try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.office365.com';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'vtech.admin@vtechsolution.us';                     // SMTP username
    $mail->Password   = 'vTech@123';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    $mail->Port       = 587;                                    // TCP port to connect to

 	// $mail->Subject = 'Testing';

			$mailContent = '<!DOCTYPE html>
			<html>
			<body>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;"><img src="cid:companyLogo"></td>
					</tr>
					<tr>
						<td style="background-color: #2266AA;padding: 7px;"></td>
					</tr>
					<tr>
						<td style="background-color: #ccc;padding: 3px;"></td>
					</tr>
					<tr>
						<td style="font-size: 20px;font-weight: bold;">BDG Matrix Notification<span style="font-size: 16px;color: #449D44;"> ('.date("m-d-Y l",strtotime("-1 days")).')</span></td>
					</tr>
					<tr>
						<td><br>
							<table style="width: 100%;border: 1px solid #ddd;">
								<thead>
									<tr style="background-color: #ccc;color: #000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Recruiter</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Joborder</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Submission</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Interview</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Interview Decline</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Offer</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Place</th>
									</tr>
								</thead>
								<tbody>';
			
								$mailContent.='<tr>
										<td style="text-align: left;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"></td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"></td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"></td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"></td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"></td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"></td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"></td>
									</tr>';

								$mailContent.='</tbody>
								<tfoot>
									<tr style="background-color:#ccc;color:#000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">Total</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></th>
									</tr>
								</tfoot>
							</table>
						</td>
					</tr>
					<tr>
						<td style="color: #555;text-align: right;font-size: 13px;"><br>* Auto generated notification. Please DO NOT reply *<br><hr style="border: 1px dashed #ccc;"></td>
					</tr>
				</table>
			</body>
			</html>';

    //Recipients
    $mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');
    $mail->addAddress('chirag.b@vtechsolution.us');     // Add a recipient
    // $mail->addCC('meets@vtechsolution.us');
    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = $mailContent;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

 

    $mail->send();
    echo 'Message has been sent';
	} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
	
		
?>



