<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");

	if ($_POST) {
		$type = $_POST["type"];
		$person = $_POST["person"];
		$startDate = $_POST["startDate"];
		$endDate = $_POST["endDate"];

		if ($type == "inside") {
			$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
							<thead>
								<tr class='thead-tr-style'>
									<th colspan='8'>".ucwords($person)."</th>
								</tr>
								<tr class='thead-tr-style'>
									<th>Candidate</th>
									<th>Client</th>
									<th>Inside<br>Sales 1</th>
									<th>Inside<br>Sales 2</th>
									<th>Research<br>By</th>
									<th>Actual<br>GP</th>
									<th>GP<br>Share</th>
									<th>Given<br>GP</th>
								</tr>
							</thead>
							<tbody>";
		} elseif ($type == "onsite") {
			$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
							<thead>
								<tr class='thead-tr-style'>
									<th colspan='5'>".ucwords($person)."</th>
								</tr>
								<tr class='thead-tr-style'>
									<th>Candidate</th>
									<th>Client</th>
									<th>Onsite Sales</th>
									<th>Onsite Post Sales</th>
									<th>Total GP</th>
								</tr>
							</thead>
							<tbody>";
		}

		$taxRate = $mspFees = $primeCharges = $candidateRate = $grossMargin = $totalHour = $actualGP = $givenGP = 0;
		
		$totalActualGP = $totalGivenGP = array();

		$delimiter = array("","[","]",'"');

		$currentDate = date("Ym");

		$taxSettingsTableData = taxSettingsTable($allConn);
		$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$startDate,$endDate);

		$marginQUERY = "SELECT
			e.id AS employee_id,
			CONCAT(e.first_name,' ',e.last_name) AS employee_name,
			e.custom1 AS benefit,
			e.custom2 AS benefit_list,
			CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
			CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
			es.id AS employment_id,
			comp.company_id AS company_id,
			comp.name AS company_name,
			CAST((PERIOD_DIFF($currentDate,DATE_FORMAT(comp.date_created, '%Y%m')) / 12) AS DECIMAL(10,1)) AS cats_company_age,
			DATE_FORMAT(comp.date_created, '%Y-%m-%d') AS cats_company_create_date,
			(SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'Matrix' AND ic.comment = 'Client Age') AS given_company_age,
			clf.mspChrg_pct AS client_msp_charge_percentage,
			clf.primechrg_pct AS client_prime_charge_percentage,
			clf.primeChrg_dlr AS client_prime_charge_dollar,
			clf.mspChrg_dlr AS client_msp_charge_dollar,
			cnf.c_primeCharge_pct AS employee_prime_charge_percentage,
			cnf.c_primeCharge_dlr AS employee_prime_charge_dollar,
			cnf.c_anyCharge_dlr AS employee_any_charge_dollar";

		if ($type == "inside") {
			$marginQUERY .= ",
				(SELECT value FROM cats.extra_field AS e WHERE e.data_item_id = comp.company_id AND e.field_name = 'Inside Sales Person1') AS inside_sales1,
				(SELECT value FROM cats.extra_field AS e WHERE e.data_item_id = comp.company_id AND e.field_name = 'Inside Sales Person2') AS inside_sales2,
				(SELECT value FROM cats.extra_field AS e WHERE e.data_item_id = comp.company_id AND e.field_name = 'Research By') AS research_by,
				(SELECT COUNT(*) AS share_amount FROM cats.extra_field AS ef WHERE ef.data_item_id = comp.company_id and ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By') AND ef.value != '') AS share_amount
			FROM
				vtechhrm.employees AS e
				LEFT JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
				LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
				LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
				LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
			    LEFT JOIN cats.extra_field AS ef ON ef.data_item_id = comp.company_id
			    LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
				LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
			WHERE
				LOWER(ef.value) = '$person'
			AND
			    (ef.field_name = 'Inside Sales Person1' OR ef.field_name = 'Inside Sales Person2' OR ef.field_name = 'Research By')
			AND
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY employee_id";
		} elseif ($type == "onsite") {
			$marginQUERY .= ",
				(SELECT value FROM cats.extra_field AS e WHERE e.data_item_id = comp.company_id AND e.field_name = 'OnSite Sales Person') AS onsite_sales,
				(SELECT value FROM cats.extra_field AS e WHERE e.data_item_id = comp.company_id AND e.field_name = 'OnSite Post Sales') AS onsite_post_sales
			FROM
				vtechhrm.employees AS e
				LEFT JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
				LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
				LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
				LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
			    LEFT JOIN cats.extra_field AS ef ON ef.data_item_id = comp.company_id
			    LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
				LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
			WHERE
				LOWER(ef.value) = '$person'
			AND
			    (ef.field_name = 'OnSite Sales Person' OR ef.field_name = 'OnSite Post Sales')
			AND
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY employee_id";
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

				//$totalHour = round(employeeWorkingHours($vtechhrmConn,$startDate,$endDate,$marginROW["employee_id"]), 2);

				$totalHour = round(array_sum($employeeTimeEntryTableData[$marginROW["employee_id"]]), 2);

				$actualGP = round(($grossMargin * $totalHour), 2);

				$givenGP = round(($actualGP / $marginROW["share_amount"]), 2);
				
				if ($type == "inside") {
					$totalActualGP[] = $actualGP;
					$totalGivenGP[] = $givenGP;
					
					$output .= "<tr class='tbody-tr-style'>
						<td nowrap>".ucwords($marginROW["employee_name"])."</td>
						<td nowrap>".$marginROW["company_name"]."</td>
						<td>".$marginROW["inside_sales1"]."</td>
						<td>".$marginROW["inside_sales2"]."</td>
						<td>".$marginROW["research_by"]."</td>
						<td>".$actualGP."</td>
						<td>".$marginROW["share_amount"]."</td>
						<td>".$givenGP."</td>
					</tr>";
				} elseif ($type == "onsite") {
					$totalActualGP[] = $actualGP;
					
					$output .= "<tr class='tbody-tr-style'>
						<td nowrap>".ucwords($marginROW["employee_name"])."</td>
						<td nowrap>".$marginROW["company_name"]."</td>
						<td>".$marginROW["onsite_sales"]."</td>
						<td>".$marginROW["onsite_post_sales"]."</td>
						<td>".$actualGP."</td>
					</tr>";
				}

			}
		}

		if ($type == "inside") {
			$output .= "</tbody>
						<tfoot>
							<tr class='tfoot-tr-style'>
								<th colspan='5'></th>
								<th>".array_sum($totalActualGP)."</th>
								<th></th>
								<th>".array_sum($totalGivenGP)."</th>
							</tr>
						</tfoot>
					</table>";
		} elseif ($type == "onsite") {
			$output .= "</tbody>
						<tfoot>
							<tr class='tfoot-tr-style'>
								<th colspan='4'></th>
								<th>".array_sum($totalActualGP)."</th>
							</tr>
						</tfoot>
					</table>";
		}
		
		echo $output;
	}
?>