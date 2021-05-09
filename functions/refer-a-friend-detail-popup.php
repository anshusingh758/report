<?php
	error_reporting(0);
	include_once("../config.php");
    
	if ($_POST) {
		$catsStatus = $_POST["status"];
		$allDate = $_POST["date"];
		$dateSplit = explode("/", $allDate);
		$startDate = $dateSplit[0];
		$endDate = $dateSplit[1];
		
		$output = "<table class='table table-striped table-bordered refer-a-friend-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>";
				if ($catsStatus == "referred") {
					$output .=	"<th>Referral's Full Name</th>
					<th>Referral's E-mail</th>
					<th>Referrer Full Name</th>
					<th>Referrer E-mail</th>
					<th>Created Date</th>";
				} elseif ($catsStatus == "resume_upload") {
					$output .= "<th>Referrer Full Name</th>
					<th>Referrer E-mail</th>
					<th>Created Date</th>";
				}
				if ($catsStatus == "apply_count") {
					$output .= "<th>Referrer Full Name</th>
					<th>Referrer E-mail</th>
					<th>Recruiter Name</th>
					<th>Job Order</th>
					<th>Created Date</th>";
				}	
				$output .= "</tr>
			</thead>
			<tbody>";
			if ($catsStatus == 'referred') {
					$referredQuery = mysqli_query($allConn, "SELECT
					ri.name AS referral_name,
					ri.email AS referral_email,
					rfi.name AS referrer_name,
					rfi.email AS referrer_email,
					DATE_FORMAT(rfi.date_created, '%m/%d/%Y %H:%i:%s') AS date_created
				FROM
					vtech_mappingdb.referral_info AS rfi
					LEFT JOIN vtech_mappingdb.referrer_info AS ri ON ri.id = rfi.referrer_id
				WHERE
					DATE_FORMAT(rfi.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				ORDER BY date_created");
			}
			if ($catsStatus == "resume_upload") {
				$resumeUploadQuery = mysqli_query($allConn, "SELECT
					rfi.name AS referrer_name,
					rfi.email AS referrer_email,
					rci.cats_candidate_id AS candidate_id,
					DATE_FORMAT(rci.date_created, '%m/%d/%Y %H:%i:%s') AS date_created
				FROM
					vtech_mappingdb.referral_cats_info AS rci
					LEFT JOIN vtech_mappingdb.referral_info AS rfi ON rfi.id = rci.referral_id
				WHERE
					DATE_FORMAT(rci.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				ORDER BY date_created");
			}
			if ($catsStatus == "apply_count") {
				$applyCountQuery = mysqli_query($allConn, "SELECT
					rfi.name AS referrer_name,
					rfi.email AS referrer_email,
					rji.recruiter_name As recruiter_name,
					jb.title AS job_order,
					rci.cats_candidate_id AS candidate_id,
					DATE_FORMAT(rji.date_created, '%m/%d/%Y %H:%i:%s') AS date_created
				FROM
					vtech_mappingdb.referral_jobs_info AS rji
					LEFT JOIN vtech_mappingdb.referral_cats_info AS rci ON rci.referral_id = rji.referral_id
					LEFT JOIN cats.joborder AS jb ON jb.joborder_id = rji.joborder_id
					LEFT JOIN vtech_mappingdb.referral_info AS rfi ON rfi.id = rji.referral_id
				WHERE
					DATE_FORMAT(rji.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				ORDER BY date_created");
			}

			while ($row = mysqli_fetch_array($referredQuery)) {

				$output .= "<tr class='tbody-tr-style'>";
				if ($catsStatus == 'referred'){
				$output .= "<td nowrap>".$row["referral_name"]."</td>
					<td nowrap>".$row["referral_email"]."</td>
					<td nowrap>".$row["referrer_name"]."</td>
					<td nowrap>".$row["referrer_email"]."</td>
					<td nowrap>".$row["date_created"]."</td>
				</tr>";
				}
			}

			while ($row = mysqli_fetch_array($resumeUploadQuery)) {

				$output .= "<tr class='tbody-tr-style'>";
				if ($catsStatus == 'resume_upload'){
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=".$row["candidate_id"]."' target='_blank' class='hyper-link-text'>".$row["referrer_name"]."</a></td>
					<td nowrap>".$row["referrer_email"]."</td>
					<td nowrap>".$row["date_created"]."</td>
				</tr>";
				}
			}

			while ($row = mysqli_fetch_array($applyCountQuery)) {

				$output .= "<tr class='tbody-tr-style'>";
				if ($catsStatus == 'apply_count'){
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=".$row["candidate_id"]."' target='_blank' class='hyper-link-text'>".$row["referrer_name"]."</a></td>
					<td nowrap>".$row["referrer_email"]."</td>
					<td nowrap>".$row["recruiter_name"]."</td>
					<td nowrap>".$row["job_order"]."</td>
					<td nowrap>".$row["date_created"]."</td>
				</tr>";
				}
			}
	
			$output .= "</tbody>
			</table>";
		echo $output;
	}
?>