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

		$output = "<table class='table table-striped table-bordered access-controls-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th>User Name</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>";

			if ($catsStatus != "") {
				$query = mysqli_query($allConn, "SELECT 
			vfam.user_id AS userId,
			CONCAT(u.first_name,' ',u.last_name) AS candidate_name,
			DATE_FORMAT(vfam.created_at, '%m/%d/%Y %H:%i:%s') AS datec 
			FROM 
				vtech_tools.vtech_feature_access_mapping as vfam 
				LEFT JOIN vtech_tools.vtech_access_control_group as vacg ON vfam.group_id = vacg.id 
				LEFT JOIN cats.user AS u ON u.user_id = vfam.user_id
			WHERE 
				vacg.group_title = '$catsStatus' AND vfam.status = '1' AND vacg.status = '1' 
			AND 
				DATE_FORMAT(vfam.created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY userId, datec
			ORDER BY datec");
			}

			while ($row = mysqli_fetch_array($query)) {

				$output .= "<tr class='tbody-tr-style'>";
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'>".$row["candidate_name"]."</td>";
				$output .= "<td nowrap>".$row["datec"]."</td>
				</tr>";
			}
			
			$output .= "</tbody>
			</table>";
		echo $output;
	}
?>