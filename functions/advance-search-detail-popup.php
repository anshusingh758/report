<?php
	error_reporting(0);
	include_once("../config.php");

	if ($_POST) {
		$companyId = $_POST["personnel_id"];
		$personnelName = $_POST["personnel_name"];
		$cats_Status = $_POST["status"];
		$allDate = $_POST["date"];
		$dateSplit = explode("/", $allDate);
		$startDate = $dateSplit[0];
		$endDate = $dateSplit[1];

		$jobStatus = array(
			"total_view" => "",
			"submission" => "400",
			"interview" => "500",
			"interview_declined" => "560",
			"offer" => "600",
			"placed" => "800",
			"extension" => "620",
			"delivery_failed" => "900"
		);
		$catsStatus = $jobStatus[$cats_Status];
		
		$output = "<table class='table table-striped table-bordered advance-search-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th>Candidate Name</th>";
				if ($catsStatus != 0){
				$output .= "<th>Joborder Name</th>";
				}
				$output .= "<th>Recruiter Name</th>
					<th>Recruiter Manager Name</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>";
			if ($catsStatus != "") {
				$query = mysqli_query($allConn, "SELECT
					ascl.cats_user_id AS recruiter_id,
					CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
					u.notes AS recruiter_manager_name,
					ascl.cats_candidate_id AS candidate_id,
					CONCAT(c.first_name,' ',c.last_name) AS candidate_name,
					j.joborder_id,
					j.title AS joborder_title,
					DATE_FORMAT(cjsh.date, '%m/%d/%Y %H:%i:%s') AS date_created
				FROM
					vtech_mappingdb.advance_search_candidate_log AS ascl
					LEFT JOIN cats.user AS u ON u.user_id = ascl.cats_user_id
					LEFT JOIN cats.candidate AS c ON c.candidate_id = ascl.cats_candidate_id
					LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.candidate_id = ascl.cats_candidate_id
					LEFT JOIN cats.joborder AS j ON j.joborder_id = cjsh.joborder_id
				WHERE
					cjsh.status_to = '$catsStatus'
				AND
					DATE_FORMAT(ascl.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				AND
					DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY ascl.cats_candidate_id
				ORDER BY date_created");
				} else {
				$query = mysqli_query($allConn, "SELECT
					ascl.cats_user_id AS recruiter_id,
					CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
					u.notes AS recruiter_manager_name,
					ascl.cats_candidate_id AS candidate_id,
					CONCAT(c.first_name,' ',c.last_name) AS candidate_name,
					DATE_FORMAT(ascl.date, '%m/%d/%Y %H:%i:%s') AS date_created
				FROM
					vtech_mappingdb.advance_search_candidate_log AS ascl
					LEFT JOIN cats.user AS u ON u.user_id = ascl.cats_user_id
					LEFT JOIN cats.candidate AS c ON c.candidate_id = ascl.cats_candidate_id
				WHERE
					DATE_FORMAT(ascl.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY ascl.cats_candidate_id
				ORDER BY date_created");
			}
			while ($row = mysqli_fetch_array($query)) {

				$output .= "<tr class='tbody-tr-style'>";
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=".$row["candidate_id"]."' target='_blank' class='hyper-link-text'>".$row["candidate_name"]."</a></td>";
				if ($catsStatus != 0){
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=".$row["joborder_id"]."' target='_blank' class='hyper-link-text'>".$row["joborder_title"]."</a></td>";
				}
				$output .= "<td nowrap>".$row["recruiter_name"]."</td>
					<td nowrap>".$row["recruiter_manager_name"]."</td>
					<td nowrap>".$row["date_created"]."</td>
				</tr>";

			}
			
			$output .= "</tbody>
			</table>";
		echo $output;
	}
?>