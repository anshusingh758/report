<?php
	error_reporting(0);
	include_once("../config.php");

	if ($_POST) {
		
		$companyId = $_POST["personnel_id"];
		$personnelName = $_POST["personnel_name"];
		$jobStatus = $_POST["status"];
		$startDate = $_POST["start_date"];
		$endDate = $_POST["end_date"];

		$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th colspan='8' style='font-size: 15px;'>".$personnelName."</th>
				</tr>
				<tr class='thead-tr-style'>
					<th>Job Title</th>
					<th>Company Job Id</th>
					<th>Openings</th>
					<th>Create Date</th>
					<th>Due Date</th>
				</tr>
			</thead>
			<tbody>";
			if ($jobStatus != "") {
				
				$query = mysqli_query($allConn, "SELECT
					j.joborder_id,
					j.title AS job_title,
					IF((j.client_job_id = '' OR j.client_job_id IS NULL), '---', j.client_job_id) AS client_job_id,
					IF((CONCAT(ru.first_name,' ',ru.last_name) = '' OR CONCAT(ru.first_name,' ',ru.last_name) IS NULL), '---', CONCAT(ru.first_name,' ',ru.last_name)) AS recruiter_name,
					c.company_id,
					IF((c.name = '' OR c.name IS NULL), '---', c.name) AS company_name,
					IF((CONCAT(cmu.first_name,' ',cmu.last_name) = '' OR CONCAT(cmu.first_name,' ',cmu.last_name) IS NULL), '---', CONCAT(cmu.first_name,' ',cmu.last_name)) AS client_manager,
					j.status AS job_status,
					IF((j.openings = '' OR j.openings IS NULL), '---', j.openings) AS job_openings,
					DATE_FORMAT(j.date_created, '%m-%d-%Y') AS job_create_date,
					IF((ddef.value = '' OR ddef.value IS NULL), '---', ddef.value) AS job_due_date
				FROM
					cats.joborder AS j
					LEFT JOIN cats.user AS ru ON ru.user_id = j.recruiter
					LEFT JOIN cats.company AS c ON c.company_id = j.company_id
					LEFT JOIN cats.user AS cmu ON cmu.user_id = c.owner
					LEFT JOIN cats.extra_field AS ddef ON ddef.data_item_id = j.joborder_id AND ddef.field_name = 'Due Date'
				WHERE
					j.status = '$jobStatus'
				AND
					c.company_id = '$companyId'
				AND
					DATE_FORMAT(j.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY j.joborder_id");
			
			} 

			while ($row = mysqli_fetch_array($query)) {

				$output .= "<tr class='tbody-tr-style'>
				<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=".$row["joborder_id"]."' target='_blank' class='hyper-link-text'>".$row["job_title"]."</a></td>
					<td nowrap>".$row["client_job_id"]."</td>
					<td nowrap>".$row["job_openings"]."</td>
					<td nowrap>".$row["job_create_date"]."</td>
					<td nowrap>".$row["job_due_date"]."</td>
				</tr>";

			}
			
			$output .= "</tbody>
			</table>";
	
		echo $output;
	}
?>