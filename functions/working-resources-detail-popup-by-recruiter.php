<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");

	if ($_POST) {
		$type = $_POST["type"];
		$data = json_decode($_POST["data"], true);

		$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>";
					foreach ($data as $key => $value) {
						$output .= "<th colspan='9' style='font-size: 15px;'>".$value["recruiter_name"]."</th>";
					}
				$output .= "</tr>
				<tr class='thead-tr-style'>
					<th>Employee</th>
					<th>Client</th>
					<th>Bill<br>Rate</th>
					<th>Pay<br>Rate</th>
					<th>Tax</th>
					<th>MSP<br>Fees</th>
					<th>Vendor<br>Fees</th>
					<th>Candidate<br>Rate</th>
					<th>Margin</th>
				</tr>
			</thead>
			<tbody>";
			
			$thisYearStartDate = date("Y")."-01-01";

			foreach ($data as $key => $value) {

				$recruiterId = $value["recruiter_id"];
				$startDate = $value["start_date"];
				$endDate = $value["end_date"];

				$query = "SELECT
				    hel.employee_id,
				    hel.employee_name,
				    hel.company_id,
				    hel.company_name,
				    hel.bill_rate,
				    hel.pay_rate,
				    hel.tax_rate,
				    hel.msp_fees,
				    hel.prime_charges,
				    hel.candidate_rate,
				    hel.margin
				FROM
				    vtech_mappingdb.hrm_employee_log AS hel
					JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = hel.employee_id
				WHERE
				    hel.recruiter_id = '$recruiterId'
				AND
					hel.id IN (SELECT MAX(id) FROM vtech_mappingdb.hrm_employee_log WHERE recruiter_id = hel.recruiter_id AND ((DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') OR (((DATE_FORMAT(created_at, '%Y-%m-%d') NOT BETWEEN '$startDate' AND '$endDate') AND DATE_FORMAT(created_at, '%Y-%m-%d') < '$startDate') OR ((DATE_FORMAT(created_at, '%Y-%m-%d') NOT BETWEEN '$startDate' AND '$endDate') AND DATE_FORMAT(created_at, '%Y-%m-%d') > '$endDate'))) GROUP BY employee_id)";

				if ($type == "till") {
					$query .= " AND
						DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
					GROUP BY hel.employee_id";
				} elseif ($type == "this") {
					$query .= " AND
						DATE_FORMAT(hel.join_date, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDate'
					AND
						DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDate'
					GROUP BY hel.employee_id";
				}

				$result = mysqli_query($allConn, $query);
				if (mysqli_num_rows($result) > 0) {
					while ($row = mysqli_fetch_array($result)) {
						
						$totalResources[] = $row["employee_id"];
						
						$totalMargin[] = $row["margin"];

						$output .= "<tr class='tbody-tr-style'>
							<td>".$row["employee_name"]."</td>
							<td>".$row["company_name"]."</td>
							<td>".$row["bill_rate"]."</td>
							<td>".$row["pay_rate"]."</td>
							<td>".$row["tax_rate"]."</td>
							<td>".$row["msp_fees"]."</td>
							<td>".$row["prime_charges"]."</td>
							<td>".$row["candidate_rate"]."</td>
							<td>".$row["margin"]."</td>
						</tr>";
					}
				}
			}

			$output .= "</tbody>
				<tfoot>
					<tr class='tfoot-tr-style'>
						<th>".count(array_unique($totalResources))."</th>
						<th colspan='7'></th>
						<th>".round(array_sum($totalMargin), 2)."</th>
					</tr>
				</tfoot>
			</table>";
	
		echo $output;
	}
?>