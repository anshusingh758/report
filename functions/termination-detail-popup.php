<?php
	error_reporting(0);
	include_once("../config.php");

	if ($_POST) {
		
		$companyId = $_POST["personnel_id"];
		$personnelName = $_POST["personnel_name"];
		$startDate = $_POST["start_date"];
		$endDate = $_POST["end_date"];

		$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th colspan='8' style='font-size: 15px;'>".$personnelName."</th>
				</tr>
				<tr class='thead-tr-style'>
					<th>Candidate Name</th>
					<th>Job Title</th>
					<th>Start Date</th>
					<th>Termination Date</th>
				</tr>
			</thead>
			<tbody>";

			$query = mysqli_query($allConn, "SELECT
				e.id AS employee_id,
			    CONCAT(e.first_name,' ',e.last_name) AS employee_name,
			    c.candidate_id,
			    CONCAT(c.first_name,' ',c.last_name) AS candidate_name,
			    j.joborder_id,
			    j.title AS joborder_title,
			    DATE_FORMAT(e.custom7, '%m-%d-%Y') AS join_date,
			    DATE_FORMAT(e.termination_date, '%m-%d-%Y') AS termination_date
			FROM
				vtechhrm.employees AS e
			    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
			    LEFT JOIN cats.joborder AS j ON j.joborder_id = si.c_joborder_id
			    LEFT JOIN cats.candidate AS c ON c.candidate_id = si.c_candidate_id
			WHERE
				si.c_company_id = '$companyId'
			AND
				e.status IN ('Terminated','Termination In_Vol','Termination Vol')
			AND
				DATE_FORMAT(e.termination_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY e.id");

			while ($row = mysqli_fetch_array($query)) {

				$output .= "<tr class='tbody-tr-style'>";
				
				$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=".$row["candidate_id"]."' target='_blank' class='hyper-link-text'>".$row["candidate_name"]."</a></td>
					<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=".$row["joborder_id"]."' target='_blank' class='hyper-link-text'>".$row["joborder_title"]."</a></td>
					<td nowrap>".$row["join_date"]."</td>
					<td nowrap>".$row["termination_date"]."</td>
				</tr>";

			}
			
			$output .= "</tbody>
			</table>";
	
		echo $output;
	}
?>