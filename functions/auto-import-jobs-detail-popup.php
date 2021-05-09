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

		$output = "<table class='table table-striped table-bordered auto-import-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th>Job order</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>";

			if ($catsStatus != "") {
				$query = mysqli_query($allConn, "SELECT
                amj.ats_joborder_id AS joborder_id,
                j.title AS source_title,
                DATE_FORMAT(amj.created_date, '%m/%d/%Y %H:%i:%s') AS date_created
            FROM
                vtech_mappingdb.ats_mapped_jobs AS amj
				LEFT JOIN cats.joborder AS j ON j.joborder_id = amj.ats_joborder_id
            WHERE
                amj.job_source = '$catsStatus'
            AND
                DATE_FORMAT(amj.created_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY amj.ats_joborder_id
			ORDER BY date_created");
			}
			
			while ($row = mysqli_fetch_array($query)) {

				$output .= "<tr class='tbody-tr-style'>";
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=".$row["joborder_id"]."' target='_blank' class='hyper-link-text'>".$row["source_title"]."</a></td>";
				$output .= "<td nowrap>".$row["date_created"]."</td>
				</tr>";

			}
			
			$output .= "</tbody>
			</table>";
		echo $output;
	}
?>