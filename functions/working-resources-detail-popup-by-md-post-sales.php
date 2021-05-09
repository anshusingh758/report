<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");

	if ($_POST) {
		$data = json_decode($_POST["data"], true);

		$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>";
					foreach ($data as $key => $value) {
						$output .= "<th colspan='6' style='font-size: 15px;'>".$key."</th>";
					}
				$output .= "</tr>
				<tr class='thead-tr-style'>
					<th>Employee</th>
					<th>Client</th>
					<th>Total<br>Hours</th>
					<th>Total<br>Revenue</th>
					<th>Total<br>Pay</th>
					<th>Total<br>GP</th>
				</tr>
			</thead>
			<tbody>";

			$taxSettingsTableData = taxSettingsTable($allConn);

			foreach ($data as $key => $value) {
				foreach ($value as $list => $item) {

					$employeeId = $item["employee_id"];
					$startDateYMD = $item["start_date"];
					$endDateYMD = $item["end_date"];

					$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$startDateYMD,$endDateYMD);

					$query = "SELECT
					    e.id AS employee_id,
					    CONCAT(e.first_name,' ',e.last_name) AS employee_name,
					    c.company_id,
					    c.name AS company_name,
					    ehd.*
					FROM
						vtechhrm.employees AS e
						LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
					    LEFT JOIN vtech_mappingdb.employee_history_detail AS ehd ON ehd.employee_id = e.id
			    		LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			    		LEFT JOIN cats.company AS c ON c.company_id = si.c_company_id
					WHERE
					    e.id = '$employeeId'
					AND
						ehd.id IN (SELECT MAX(id) FROM vtech_mappingdb.employee_history_detail WHERE employee_id = ehd.employee_id AND ((DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '$startDateYMD' AND '$endDateYMD') OR (((DATE_FORMAT(created_at, '%Y-%m-%d') NOT BETWEEN '$startDateYMD' AND '$endDateYMD') AND DATE_FORMAT(created_at, '%Y-%m-%d') < '$startDateYMD') OR ((DATE_FORMAT(created_at, '%Y-%m-%d') NOT BETWEEN '$startDateYMD' AND '$endDateYMD') AND DATE_FORMAT(created_at, '%Y-%m-%d') > '$endDateYMD'))) GROUP BY employee_id)
					AND
						DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDateYMD' AND '$endDateYMD'
					GROUP BY e.id";

					$result = mysqli_query($allConn, $query);

					$delimiter = array("","[","]",'"');

					if (mysqli_num_rows($result) > 0) {
						while ($row = mysqli_fetch_array($result)) {
									
							$benefitList = str_replace($delimiter, $delimiter[0], $row["benefit_list"]);

							//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$row["benefit"],$benefitList,$row["employment_id"],$row["pay_rate"]), 2);

							$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$row["benefit"],$benefitList,$row["employment_id"],$row["pay_rate"]), 2);

							$mspFees = round((($row["client_msp_charge_percentage"] / 100) * $row["bill_rate"]) + $row["client_msp_charge_dollar"], 2);

							$primeCharges = round(((($row["client_prime_charge_percentage"] / 100) * $row["bill_rate"]) + (($row["employee_prime_charge_percentage"] / 100) * $row["bill_rate"]) + $row["employee_prime_charge_dollar"] + $row["employee_any_charge_dollar"] + $row["client_prime_charge_dollar"]), 2);

							$candidateRate = round(($row["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

							$grossMargin = round(($row["bill_rate"] - $candidateRate), 2);

							//$hours = round(employeeWorkingHours($vtechhrmConn,$startDateYMD,$endDateYMD,$row["employee_id"]), 2);
							
							$hours = round(array_sum($employeeTimeEntryTableData[$row["employee_id"]]), 2);

							if ($hours > "0") {

								$pay = round(($candidateRate * $hours), 2);

								$margin = round(($grossMargin * $hours), 2);
								
								$revenue = round(($row["bill_rate"] * $hours), 2);

								$totalResources[] = $row["employee_id"];
								
								$totalHours[] = $hours;

								$totalRevenue[] = $revenue;

								$totalPay[] = $pay;

								$totalMargin[] = $margin;
								
								$output .= "<tr class='tbody-tr-style'>
									<td nowrap>".$row["employee_name"]."</td>
									<td nowrap>".$row["company_name"]."</td>
									<td>".$hours."</td>
									<td>".$revenue."</td>
									<td>".$pay."</td>
									<td>".$margin."</td>
								</tr>";
							}
						}
					}
				}
			}

			$output .= "</tbody>
				<tfoot>
					<tr class='tfoot-tr-style'>
						<th>".count(array_unique($totalResources))."</th>
						<th></th>
						<th>".round(array_sum($totalHours), 2)."</th>
						<th>".round(array_sum($totalRevenue), 2)."</th>
						<th>".round(array_sum($totalPay), 2)."</th>
						<th>".round(array_sum($totalMargin), 2)."</th>
					</tr>
				</tfoot>
			</table>";
	
		echo $output;
	}
?>