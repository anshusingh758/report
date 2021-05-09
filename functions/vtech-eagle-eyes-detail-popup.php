<?php
	error_reporting(0);
	include_once("../config.php");
    
	if ($_POST) {
		$companyId = $_POST["personnel_id"];
		$personnelName = $_POST["personnel_name"];
		$catsStatus = $_POST["status"];
		$allDate = $_POST["date"];
		$dateSplit = explode("/", $allDate);
		$startDate = $dateSplit[0];
		$endDate = $dateSplit[1];

		$output = "<table class='table table-striped table-bordered vtech-eagle-eyes-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>";
				if ($catsStatus == 'candidate'){
				$output .=	"<th>Candidate Name</th>";
				} else {
				$output .=	"<th>Company Name</th>";
				}
				$output .= "<th>Recruiter Name</th>
					<th>Date</th>
				
				</tr>
			</thead>
			<tbody>";
			if ($catsStatus == "candidate") {
					$query = mysqli_query($allConn, "SELECT
					CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
					ltsvl.user_id AS candidate_id,
					CONCAT(c.first_name,' ',c.last_name) AS candidate_name,
					DATE_FORMAT(ltsvl.created_date, '%m/%d/%Y %H:%i:%s') AS date_created
				FROM
					vtech_mappingdb.live_tracker_status_view_log AS ltsvl
					LEFT JOIN cats.user AS u ON u.user_id = ltsvl.user_id
					LEFT JOIN cats.candidate AS c ON c.candidate_id = ltsvl.user_id
				WHERE
					ltsvl.type = '$catsStatus'
				AND
					DATE_FORMAT(ltsvl.created_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY ltsvl.user_id
				ORDER BY date_created");
			} else {
					$query = mysqli_query($allConn, "SELECT 
					CONCAT(u.first_name,' ',u.last_name) AS recruiter_name, 
					ltsvl.company_and_candidate_id AS company_id, 
					c.name AS company_name, 
					DATE_FORMAT(ltsvl.created_date, '%m/%d/%Y %H:%i:%s') AS date_created 
				FROM 
					vtech_mappingdb.live_tracker_status_view_log AS ltsvl 
					LEFT JOIN cats.user AS u ON u.user_id = ltsvl.user_id 
					LEFT JOIN cats.company AS c ON c.company_id = ltsvl.company_and_candidate_id 
				WHERE 
					ltsvl.type = '$catsStatus' 
				AND 
					DATE_FORMAT(ltsvl.created_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate' 
				GROUP BY ltsvl.user_id 
				ORDER BY date_created");
			}

			while ($row = mysqli_fetch_array($query)) {

				$output .= "<tr class='tbody-tr-style'>";
				if ($catsStatus == 'candidate'){
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=".$row["candidate_id"]."' target='_blank' class='hyper-link-text'>".$row["candidate_name"]."</a></td>";
				} else {
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID=".$row["company_id"]."' target='_blank' class='hyper-link-text'>".$row["company_name"]."</a></td>";
				}
				
				$output .= "<td nowrap>".$row["recruiter_name"]."</td>
					<td nowrap>".$row["date_created"]."</td>
				</tr>";

			}
	
			$output .= "</tbody>
			</table>";
		echo $output;
	}
?>