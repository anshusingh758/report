<?php
	include_once("../../config.php");
	include_once("../../functions/reporting-service.php");
	// include_once("../../PHPMailer/PHPMailerAutoload.php");
	include_once("../../email-config.php");

	//Embed Image
	$mail->AddEmbeddedImage("../../images/company_logo.png", "companyLogo");

	// Add a sender
	$mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');

	$weekList = array(
		"past_week" => array(
			strtotime(date("Y-m-d",strtotime('last Monday'))),
			strtotime(date("Y-m-d",strtotime('last Sunday')))
		),
		"two_weeks_ago" => array(
			strtotime(date("Y-m-d",strtotime('last Monday -7 days'))),
			strtotime(date("Y-m-d",strtotime('last Sunday -7 days')))
		),
		"three_weeks_ago" => array(
			strtotime(date("Y-m-d",strtotime('last Monday -14 days'))),
			strtotime(date("Y-m-d",strtotime('last Sunday -14 days')))
		),
		"four_weeks_ago" => array(
			strtotime(date("Y-m-d",strtotime('last Monday -21 days'))),
			strtotime(date("Y-m-d",strtotime('last Sunday -21 days')))
		)
	);

	$managerListQuery = mysqli_query($catsConn, "SELECT
		extra_field_options
	FROM
		extra_field_settings
	WHERE
		field_name = 'Manager - Client Service'");

	$managerListRow = mysqli_fetch_array($managerListQuery);

	$managerListNameGroup = explode(",", str_replace("+", " ", $managerListRow['extra_field_options']));
	unset($managerListNameGroup[0]);
	$managerListNameFinalGroup = "'" . implode( "','",  $managerListNameGroup) . "'";

	$managerNameQUERY = mysqli_query($catsConn, "SELECT
	    user_id AS managerId,
	    concat(first_name,' ',last_name) AS managerName,
	    email AS managerEmail
	FROM
	    user
	WHERE
	    concat(first_name,' ',last_name) IN ($managerListNameFinalGroup)
	AND
	    access_level!='0'
	GROUP BY managerName");
	
	if(mysqli_num_rows($managerNameQUERY) > 0){
		while($managerNameROW = mysqli_fetch_array($managerNameQUERY)){

			$managerId = $managerNameROW["managerId"];
			
			// Email subject
			$mail->Subject = 'Four Weekly CSM Tracker Report_'.$managerNameROW["managerName"].'_'.$weekList["past_week"][0].'_'.$weekList["past_week"][1];
			
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
						<td style="font-size: 20px;font-weight: bold;">Four Weekly CSM Tracker Report<span style="color: #2266AA;"> ('.$managerNameROW["managerName"].')</span><span style="font-size: 16px;color: #449D44;"> ('.date("m-d-Y", strtotime($weekList["past_week"][0])).'<span style="color:#2266AA;"> --to-- </span>'.date("m-d-Y", strtotime($weekList["past_week"][1])).')</span></td>
					</tr>
					<tr>
						<td><br>
							<table style="width: 100%;border: 1px solid #ddd;">
								<thead>
									<tr style="background-color: #ccc;color: #000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Week</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Submission</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Interview</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Interview Decline</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Offer</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Place</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Delivery Failed</th>
									</tr>
								</thead>
								<tbody>';
								
								foreach ($weekList as $key => $value) {
									$startDate = $value[0];
									$endDate = $value[1];

									$totalQuery = mysqli_query($catsConn, "SELECT
									    COUNT(CASE WHEN cjsh.status_to='400' THEN 1 END) AS totsub,
									    COUNT(CASE WHEN cjsh.status_to='500' THEN 1 END) AS totiv,
									    COUNT(CASE WHEN cjsh.status_to='560' THEN 1 END) AS totivd,
									    COUNT(CASE WHEN cjsh.status_to='600' THEN 1 END) AS totoff,
									    COUNT(CASE WHEN cjsh.status_to='800' THEN 1 END) AS totjoin,
									    COUNT(CASE WHEN cjsh.status_to='900' THEN 1 END) AS totdf
									FROM
										joborder AS job
									    JOIN candidate_joborder AS cj ON job.joborder_id=cj.joborder_id
										JOIN candidate_joborder_status_history AS cjsh ON cj.joborder_id=cjsh.joborder_id AND cjsh.candidate_id=cj.candidate_id
										JOIN company AS comp ON comp.company_id=job.company_id
									WHERE
										comp.owner = '$managerId'
									AND
										date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'");
									$totalRow = mysqli_fetch_array($totalQuery);
									$totsub[] = $totalRow['totsub'];
									$totiv[] = $totalRow['totiv'];
									$totivd[] = $totalRow['totivd'];
									$totoff[] = $totalRow['totoff'];
									$totjoin[] = $totalRow['totjoin'];
									$totdf[] = $totalRow['totdf'];
								$mailContent.='<tr>
										<td style="text-align: left;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.ucwords(implode(" ", explode("_", $key))).'</td>';
								$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totalRow["totsub"].'</td>';
								$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totalRow["totiv"].'</td>';
								$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totalRow["totivd"].'</td>';
								$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totalRow["totoff"].'</td>';
								$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totalRow["totjoin"].'</td>';
								$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totalRow["totdf"].'</td>
									</tr>';
								}
								$mailContent.='</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="color: #555;text-align: right;font-size: 13px;"><br>* Auto generated notification. Please DO NOT reply *<br><hr style="border: 1px dashed #ccc;"></td>
					</tr>
				</table>
			</body>
			</html>';

			// Add a recipient
			//$mail->addAddress($row['email']);
			//$mail->addBcc('ravip@vtechsolution.us');

			echo $mailContent;
			
			//include("../../functions/email-send-config.php");
		}
	}
?>
