<?php
	error_reporting(0);
	include_once("../config.php");
    
	if ($_POST) {

		$status = $_POST["status"];
		$allDate = $_POST["date"];
		$dateSplit = explode("/", $allDate);
		$startDate = $dateSplit[0];
		$endDate = $dateSplit[1];

		$output = "<table class='table table-striped table-bordered get-rate-usage-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th>User Name</th>
					<th>Joborder Name</th>
					<th>Total Click</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>";
			if ($status == "get_rate_usage") {
					$query = mysqli_query($allConn, "SELECT 
						gru.cats_user_id, 
						gru.cats_joborder_id, 
						gru.date_created, 
						CONCAT(u.first_name,' ',u.last_name) AS username, 
						j.title AS job_name, 
						(SELECT COUNT(*) FROM vtech_mappingdb.get_rate_usage 
							WHERE cats_user_id = gru.cats_user_id 
							AND cats_joborder_id = gru.cats_joborder_id 
							AND DATE_FORMAT(date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') as totalClick 
					FROM 
						vtech_mappingdb.get_rate_usage AS gru 
						LEFT JOIN cats.user AS u ON u.user_id = gru.cats_user_id 
						LEFT JOIN cats.joborder AS j ON j.joborder_id = gru.cats_joborder_id
					WHERE
						DATE_FORMAT(gru.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
					GROUP BY 
						gru.cats_user_id,gru.cats_joborder_id 
					ORDER BY gru.date_created DESC");
			 

				while ($row = mysqli_fetch_array($query)) {

					$output .= "<tr class='tbody-tr-style'>";
					$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'>".$row["username"]."</td>";
					$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=".$row["cats_joborder_id"]."' target='_blank' class='hyper-link-text'>".$row["job_name"]."</a></td>";
					$output .= "<td nowrap>".$row["totalClick"]."</td>
						<td nowrap>".$row["date_created"]."</td>
					</tr>";

				}
			}
			$output .= "</tbody>
			</table>";
		echo $output;
	}
?>