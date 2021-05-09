<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");

	if ($_POST) {
		$titleType = $_POST["titleType"];
		$objectType = $_POST["objectType"];
		if ($_POST["objectId"]) {
			$objectId = $_POST["objectId"];
		}
		$objectName = $_POST["objectName"];
		$startDate = $_POST["startDate"];
		$endDate = $_POST["endDate"];

		$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
						<thead>
							<tr class='thead-tr-style'>
								<th colspan='8'>".$objectName."</th>
							</tr>
							<tr class='thead-tr-style'>
								<th>Candidate</th>
								<th>Bill Rate</th>
								<th>Pay Rate</th>
								<th>Tax</th>
								<th>MSP Fees</th>
								<th>Vendor Fees</th>
								<th>Candidate Rate</th>
								<th data-toggle='tooltip' data-placement='top' title='Bill Rate - Candidate Rate'>Margin</th>
							</tr>
						</thead>
						<tbody>";

		$taxRate = $mspFees = $primeCharges = $candidateRate = $grossMargin = "";
		
		$totalGrossMargin = array();
		
		$delimiter = array("","[","]",'"');

		$taxSettingsTableData = taxSettingsTable($allConn);

		$marginQUERY = "SELECT
			e.id AS employee_id,
			CONCAT(e.first_name,' ',e.last_name) AS employee_name,
			e.employee_id AS emp_id,
			e.status AS employee_status,
			e.custom1 AS benefit,
			e.custom2 AS benefit_list,
			CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
			CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
			es.id AS employment_id,
			es.name AS employment_type,
			comp.company_id AS company_id,
			comp.name AS company_name,
			clf.mspChrg_pct AS client_msp_charge_percentage,
			clf.primechrg_pct AS client_prime_charge_percentage,
			clf.primeChrg_dlr AS client_prime_charge_dollar,
			clf.mspChrg_dlr AS client_msp_charge_dollar,
			cnf.c_primeCharge_pct AS employee_prime_charge_percentage,
			cnf.c_primeCharge_dlr AS employee_prime_charge_dollar,
			cnf.c_anyCharge_dlr AS employee_any_charge_dollar
		FROM
			vtechhrm.employees AS e
			LEFT JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
			LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
			LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
			LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
			LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
			LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id";

		if ($titleType == "Actual") {
			if ($objectType == "Client") {
				$marginQUERY .= " LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
				WHERE
					comp.company_id = '$objectId'
				AND
					ep.project != '6'
				AND
					DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY employee_id";
			}
		} elseif ($titleType == "New") {
			if ($objectType == "Client") {
				$marginQUERY .= " WHERE
					comp.company_id = '$objectId'
				AND
					e.status = 'Active'
				AND
					DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY employee_id";
			}
		} elseif ($titleType == "Lost") {
			if ($objectType == "Client") {
				$marginQUERY .= " WHERE
					comp.company_id = '$objectId'
				AND
					ep.project != '6'
				AND
					e.status IN ('Terminated','Termination In_Vol','Termination Vol')
				AND
					DATE_FORMAT(e.termination_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY employee_id";
			}
		}
		
		$marginRESULT = mysqli_query($vtechhrmConn, $marginQUERY);
		if (mysqli_num_rows($marginRESULT) > 0) {
			while ($marginROW = mysqli_fetch_array($marginRESULT)) {
							
				$benefitList = str_replace($delimiter, $delimiter[0], $marginROW["benefit_list"]);

				//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$marginROW["benefit"],$benefitList,$marginROW["employment_id"],$marginROW["pay_rate"]), 2);

				$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$marginROW["benefit"],$benefitList,$marginROW["employment_id"],$marginROW["pay_rate"]), 2);

				$mspFees = round((($marginROW["client_msp_charge_percentage"] / 100) * $marginROW["bill_rate"]) + $marginROW["client_msp_charge_dollar"], 2);

				$primeCharges = round(((($marginROW["client_prime_charge_percentage"] / 100) * $marginROW["bill_rate"]) + (($marginROW["employee_prime_charge_percentage"] / 100) * $marginROW["bill_rate"]) + $marginROW["employee_prime_charge_dollar"] + $marginROW["employee_any_charge_dollar"] + $marginROW["client_prime_charge_dollar"]), 2);

				$candidateRate = round(($marginROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

				$grossMargin = round(($marginROW["bill_rate"] - $candidateRate), 2);

				$totalGrossMargin[] = round(($marginROW["bill_rate"] - $candidateRate), 2);

				$output .= "<tr class='tbody-tr-style'>
					<td>".ucwords($marginROW["employee_name"])."</td>
					<td>".$marginROW["bill_rate"]."</td>
					<td>".$marginROW["pay_rate"]."</td>
					<td>".$taxRate."</td>
					<td>".$mspFees."</td>
					<td>".$primeCharges."</td>
					<td>".$candidateRate."</td>
					<td>".$grossMargin."</td>
				</tr>";
			}
		}

		$output .= "</tbody>
						<tfoot>
							<tr class='tfoot-tr-style'>
								<th colspan='7'></th>
								<th>".array_sum($totalGrossMargin)."</th>
							</tr>
						</tfoot>
					</table>";

		echo $output;
	}
?>