<?php
	error_reporting(0);
	include_once("../config.php");

	if ($_POST) {
		
		$companyId = $_POST["personnel_id"];
		$personnelName = $_POST["personnel_name"];
		$catsStatus = $_POST["status"];
		$startDate = $_POST["start_date"];
		$endDate = $_POST["end_date"];

		$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th colspan='8' style='font-size: 15px;'>".$personnelName."</th>
				</tr>
				<tr class='thead-tr-style'>
					<th>Candidate Name</th>
					<th>Joborder Name</th>
					<th>Recruiter Name</th>
					<th>Recruiter Manager Name</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>";
			if ($catsStatus != "") {
				
				$query = mysqli_query($allConn, "SELECT
				    c.candidate_id,
				    CONCAT(c.first_name,' ',c.last_name) AS candidate_name,
				    j.joborder_id,
				    j.title AS joborder_title,
				    u.user_id AS recruiter_id,
				    CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
				    u.notes AS recruiter_manager_name,
				    DATE_FORMAT(cjsh.date, '%m-%d-%Y') AS log_date
				FROM
				    cats.company AS co
				    LEFT JOIN cats.joborder AS j ON j.company_id = co.company_id
				    LEFT JOIN cats.candidate_joborder AS cj ON cj.joborder_id = j.joborder_id
				    LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
				    LEFT JOIN cats.candidate AS c ON c.candidate_id = cj.candidate_id
				    LEFT JOIN cats.user AS u ON u.user_id = cj.added_by
				WHERE
				    co.company_id = '$companyId'
				AND
				    cjsh.status_to = '$catsStatus'
				AND
				    DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY cjsh.candidate_joborder_status_history_id");
			
			} 

			while ($row = mysqli_fetch_array($query)) {

				$output .= "<tr class='tbody-tr-style'>";
				
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=".$row["candidate_id"]."' target='_blank' class='hyper-link-text'>".$row["candidate_name"]."</a></td>
					<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=".$row["joborder_id"]."' target='_blank' class='hyper-link-text'>".$row["joborder_title"]."</a></td>
					<td nowrap>".$row["recruiter_name"]."</td>
					<td nowrap>".$row["recruiter_manager_name"]."</td>
					<td nowrap>".$row["log_date"]."</td>
				</tr>";

			}
			
			$output .= "</tbody>
			</table>";
	
		echo $output;
	}
?>