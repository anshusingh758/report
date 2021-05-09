<?php
	// include_once("../config.php");
	// include_once("../functions/reporting-service.php");
	// include_onc../../PHPMailer/PHPMailerAutoload.php");
	include_once("../functions/email-config.php");

	//Embed Image
	$mail->AddEmbeddedImage("../images/company_logo.png", "companyLogo");

	// Add a sender
	$mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');
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

		$mail->addAddress('meets@vtechsolution.us');
		$mail->addBcc('ravip@vtechsolution.us');

		echo $mailContent;
			// echo $mailContent;
		include("../functions/email-send-config.php");

?>
