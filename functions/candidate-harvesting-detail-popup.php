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

		$output = "<table class='table table-striped table-bordered candidate-harvesting-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th>Candidate Name</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>";

			if ($catsStatus != "") {
				$query = mysqli_query($allConn, "SELECT
                chr.ats_candidate_id AS candidate_id,
				CONCAT(c.first_name,' ',c.last_name) AS candidate_name,
                DATE_FORMAT(chr.created_date, '%m/%d/%Y %H:%i:%s') AS date_created
            FROM
                vtech_mappingdb.candidate_harvesting_records AS chr
				LEFT JOIN cats.candidate AS c ON c.candidate_id = chr.ats_candidate_id
            WHERE
                chr.type = '$catsStatus'
            AND
				DATE_FORMAT(chr.created_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			ORDER BY date_created");
            }
			
			while ($row = mysqli_fetch_array($query)) {

				$output .= "<tr class='tbody-tr-style'>";
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=".$row["candidate_id"]."' target='_blank' class='hyper-link-text'>".$row["candidate_name"]."</a></td>";
				$output .= "<td nowrap>".$row["date_created"]."</td>
				</tr>";

			}
			
			$output .= "</tbody>
			</table>";
		echo $output;
	}
?>