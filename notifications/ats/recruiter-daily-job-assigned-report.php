<?php
	include_once("../../config.php");
	include_once("../../functions/reporting-service.php");
	// include_once("../../PHPMailer/PHPMailerAutoload.php");
	include_once("../../email-config.php");

	//Embed Image
	$mail->AddEmbeddedImage("../../images/company_logo.png", "companyLogo");

	// Add a sender
	$mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');

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

	$recruiterListQUERY = mysqli_query($allConn, "SELECT
		u.user_id AS recruiterId,
		CONCAT(u.first_name,' ',u.last_name) AS recruiterName,
		u.email AS recruiterEmail
	FROM
		cats.user AS u
		JOIN vtech_mappingdb.manage_cats_roles AS mcr ON mcr.user_id = u.user_id
	WHERE
		u.notes IN ($managerListNameFinalGroup)
	AND
		u.access_level != '0'
	AND
		mcr.department = 'CS Team'
	GROUP BY recruiterId
	ORDER BY recruiterName ASC");

	if(mysqli_num_rows($recruiterListQUERY) > 0) {
		while($recruiterListROW = mysqli_fetch_array($recruiterListQUERY)) {
			$recruiterId = $recruiterListROW["recruiterId"];
			$recruiterName = $recruiterListROW["recruiterName"];
			$recruiterEmail = $recruiterListROW["recruiterEmail"];
			// Email subject
			$mail->Subject = 'Daily Job Assigned to '.ucwords($recruiterName).'_'.date("m-d-Y");
			
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
						<td style="font-size: 20px;font-weight: bold;">Daily Job Assigned to '.ucwords($recruiterName).'<span style="font-size: 16px;color: #449D44;"> ('.date("m-d-Y l").')</span></td>
					</tr>
					<tr>
						<td><br>
							<table style="width: 100%;border: 1px solid #ddd;">
								<thead>
									<tr style="background-color: #ccc;color: #000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Joborder ID</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Joborder Title</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Client Name</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">No. of Openings</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Total Pipeline</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Total Submission</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Assigned Date</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Due Date</th>
									</tr>
								</thead>
								<tbody>';
								$dailyAssignmentQUERY = mysqli_query($catsConn, "SELECT
								    job.joborder_id,
								    job.title,
								    job.company_id,
								    (SELECT name FROM company WHERE company_id = job.company_id) AS clientName,
								    job.openings,
								    (SELECT COUNT(CASE WHEN notes='Added candidate to pipeline.' THEN 1 END) AS totPipeline FROM activity WHERE joborder_id = job.joborder_id) AS totPipeline,
								    (SELECT COUNT(CASE WHEN status_to='400' THEN 1 END) AS totsub FROM candidate_joborder_status_history WHERE joborder_id = job.joborder_id) AS totSub,
								    date_format(job.date_created, '%m-%d-%y') AS createDate,
								    (SELECT value FROM extra_field WHERE field_name = 'Due Date' AND data_item_id = job.joborder_id) AS dueDate
								FROM
									user AS u
								    JOIN joborder AS job ON job.recruiter = u.user_id
								WHERE
									job.status = 'Active'
								AND
									u.user_id = '$recruiterId'
								GROUP BY job.joborder_id  
								ORDER BY dueDate, job.title ASC");

								while ($dailyAssignmentROW = mysqli_fetch_array($dailyAssignmentQUERY)) {
								$mailContent.='<tr>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID='.$dailyAssignmentROW["joborder_id"].'" target="_blank">'.$dailyAssignmentROW["joborder_id"].'</a></td>
										<td style="text-align: left;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID='.$dailyAssignmentROW["joborder_id"].'" target="_blank">'.$dailyAssignmentROW["title"].'</a></td>
										<td style="text-align: left;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;"><a href="https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID='.$dailyAssignmentROW["company_id"].'" target="_blank">'.$dailyAssignmentROW["clientName"].'</a></td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$dailyAssignmentROW["openings"].'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$dailyAssignmentROW["totPipeline"].'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$dailyAssignmentROW["totSub"].'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$dailyAssignmentROW["createDate"].'</td>';
									if ($dailyAssignmentROW["dueDate"] == '') {
										$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">---</td>
									</tr>';
									} else {
										$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$dailyAssignmentROW["dueDate"].'</td>
									</tr>';
									}
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
			$mail->addAddress($recruiterEmail);
			$mail->addBcc('ravip@vtechsolution.us');

			echo $mailContent;
			
			include("../../functions/email-send-config.php");
		}
	}
?>
