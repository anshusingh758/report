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
						$output .= "<th colspan='11' style='font-size: 15px;'>".$key."</th>";
					}
				$output .= "</tr>
				<tr class='thead-tr-style'>
					<th>Employee</th>
					<th>Client</th>
					<th>Shared<br>With</th>
					<th>Bill<br>Rate</th>
					<th>Pay<br>Rate</th>
					<th>Tax</th>
					<th>MSP<br>Fees</th>
					<th>Vendor<br>Fees</th>
					<th>Candidate<br>Rate</th>
					<th>Actual<br>Margin</th>
					<th>Shared<br>Margin</th>
				</tr>
			</thead>
			<tbody>";
			
			$thisYearStartDate = date("Y")."-01-01";

			foreach ($data as $key => $value) {
				foreach ($value as $list => $item) {

					$companyId = $item["company_id"];
					$totalShare = $item["total_share"];
					$sharedWith = $item["shared_with"];
					$startDateYMD = $item["start_date"];
					$endDateYMD = $item["end_date"];

					$query = "SELECT
					    hel.employee_id,
					    hel.employee_name,
					    hel.company_id,
					    hel.company_name,
					    '$totalShare' AS total_share,
					    '$sharedWith' AS shared_with,
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
					    hel.company_id = '$companyId'
					AND
						hel.id IN (SELECT MAX(id) FROM vtech_mappingdb.hrm_employee_log WHERE company_id = hel.company_id AND ((DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '$startDateYMD' AND '$endDateYMD') OR (((DATE_FORMAT(created_at, '%Y-%m-%d') NOT BETWEEN '$startDateYMD' AND '$endDateYMD') AND DATE_FORMAT(created_at, '%Y-%m-%d') < '$startDateYMD') OR ((DATE_FORMAT(created_at, '%Y-%m-%d') NOT BETWEEN '$startDateYMD' AND '$endDateYMD') AND DATE_FORMAT(created_at, '%Y-%m-%d') > '$endDateYMD'))) GROUP BY employee_id)";

					if ($type == "till") {
						$query .= " AND
							DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDateYMD' AND '$endDateYMD'
						GROUP BY hel.employee_id";
					} elseif ($type == "this") {
						$query .= " AND
							DATE_FORMAT(hel.join_date, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDateYMD'
						AND
							DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDateYMD'
						GROUP BY hel.employee_id";
					}

					$result = mysqli_query($allConn, $query);
					if (mysqli_num_rows($result) > 0) {
						while ($row = mysqli_fetch_array($result)) {
							
							$totalResources[] = $row["employee_id"];
							
							if ($row["shared_with"] != "---") {
								$totalSharing[] = $row["total_share"];
							}
							
							$totalClient[] = $row["company_id"];
							$totalMargin[] = $row["margin"];
							$sharedMargin = round(($row["margin"] / $row["total_share"]), 2);
							$totalSharedMargin[] = $sharedMargin;

							$output .= "<tr class='tbody-tr-style'>
								<td>".$row["employee_name"]."</td>
								<td>".$row["company_name"]."</td>
								<td>".$row["shared_with"]."</td>
								<td>".$row["bill_rate"]."</td>
								<td>".$row["pay_rate"]."</td>
								<td>".$row["tax_rate"]."</td>
								<td>".$row["msp_fees"]."</td>
								<td>".$row["prime_charges"]."</td>
								<td>".$row["candidate_rate"]."</td>
								<td>".$row["margin"]."</td>
								<td>".$sharedMargin."</td>
							</tr>";
						}
					}
				}
			}

			if ((count($totalSharing) / 2) != "0") {
				$totalSharedResouces = (count(array_unique($totalResources)) - (count($totalSharing) / 2))." + ".(count($totalSharing) / 2);
			} else {
				$totalSharedResouces = count(array_unique($totalResources));
			}

			$output .= "</tbody>
				<tfoot>
					<tr class='tfoot-tr-style'>
						<th>".$totalSharedResouces."</th>
						<th>".count(array_unique($totalClient))."</th>
						<th>".count($totalSharing)."</th>
						<th colspan='6' style='text-align: center;'>Average Margin (Shared) = ".round((array_sum($totalMargin) / count(array_unique($totalResources))), 2)."</th>
						<th>".round(array_sum($totalMargin), 2)."</th>
						<th>".round(array_sum($totalSharedMargin), 2)."</th>
					</tr>
				</tfoot>
			</table>";
	
		echo $output;
	}
?>