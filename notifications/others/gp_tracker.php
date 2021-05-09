<?php
	include_once("../../config.php");
	include_once("../../functions/reporting-service.php");
	// include_once("../../PHPMailer/PHPMailerAutoload.php");
	include_once("../../email-config.php");

	//Embed Image
	$mail->AddEmbeddedImage("../../images/company_logo.png", "companyLogo");

	// Add a sender
	$mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');

	$mail->Subject = 'Monthly GP Report_'.date("m_Y",strtotime("this month"));

	//Report Logic

	if (date("m", strtotime("last day of last month")) == "12") {
		$reportTitleYear = date("Y", strtotime("last year"));
	} else {
		$reportTitleYear = date("Y");
	}

	$finalArray = $finalNewEmployee = $finalLeftEmployee = $finalGP = $finalRevenue = array();

	$lastMonth = date("m", strtotime("last day of last month"));

	for ($i=1; $i <= $lastMonth; $i++) {

		$totalNewEmployee = $totalLeftEmployee = $totalMarginArray = $totalGPArray = $totalRevenueArray = array();

		$loopMonth = sprintf("%02d", $i);
		
		$lastYear = date("Y", strtotime("last year"));
		
		$thisYear = date("Y");
		
		if ($lastMonth == "12") {
			$dateModified = $lastYear."-".$loopMonth;
		} else {
			$dateModified = $thisYear."-".$loopMonth;
		}

		$fromDate = date("Y-m-01", strtotime($dateModified));
		$toDate = date("Y-m-t", strtotime($dateModified));
		
		if ($loopMonth == $lastMonth) {
			//find current data

			if ($loopMonth == "01") {
				$firstMonthQUERY = mysqli_query($allConn, "SELECT
					gd.total_active
				FROM
					mis_reports.gp_data AS gd
				WHERE
					gd.from_date = '$lastYear-12-01'
				AND
					gd.to_date = '$lastYear-12-31'");
				
				$firstMonthROW = mysqli_fetch_array($firstMonthQUERY);

				$activeEmployee = $firstMonthROW["total_active"];
			}

			$newempQUERY = mysqli_query($allConn, "SELECT
				COUNT(DISTINCT e.id) AS total_new
			FROM
				vtechhrm.employees AS e
			WHERE
				e.status != 'Internal Employee'
			AND
				e.custom7 BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'");

			$newempROW = mysqli_fetch_array($newempQUERY);

			$leftempQUERY = mysqli_query($allConn, "SELECT
				COUNT(DISTINCT e.id) AS total_left
			FROM
				vtechhrm.employees as e
				LEFT JOIN vtechhrm.employeeprojects AS ep ON e.id = ep.employee
			WHERE
				ep.project != '6'
			AND
				(e.status = 'Terminated' OR e.status = 'Termination In_Vol' OR e.status = 'Termination Vol')
			AND
				e.termination_date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'");

			$leftempROW = mysqli_fetch_array($leftempQUERY);
			
			$newEmployee = $newempROW["total_new"];
			$leftEmployee = $leftempROW["total_left"];

			$totalNewEmployee[] = $newEmployee;
			$totalLeftEmployee[] = $leftEmployee;

			$openingBalance = $activeEmployee;
			$closingBalance = $activeEmployee + $newEmployee - $leftEmployee;

			$taxSettingsTableData = taxSettingsTable($allConn);
			$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$fromDate,$toDate);

			$mainQUERY = "SELECT
				e.id AS employee_id,
				e.status AS employee_status,
				e.custom1 AS benefit,
				e.custom2 AS benefit_list,
				CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
				CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
				es.id AS employment_id,
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
				LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
				LEFT JOIN cats.company AS comp ON si.c_company_id = comp.company_id
				LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
				LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
			WHERE
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
			GROUP BY employee_id";

			$mainRESULT = mysqli_query($allConn, $mainQUERY);
			if (mysqli_num_rows($mainRESULT) > 0) {
				while ($mainROW = mysqli_fetch_array($mainRESULT)) {

					$delimiter = array("","[","]",'"');

					$benefitList = str_replace($delimiter, $delimiter[0], $mainROW["benefit_list"]);

					//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$mainROW["benefit"],$benefitList,$mainROW["employment_id"],$mainROW["pay_rate"]), 2);

					$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$mainROW["benefit"],$benefitList,$mainROW["employment_id"],$mainROW["pay_rate"]), 2);

					$mspFees = round((($mainROW["client_msp_charge_percentage"] / 100) * $mainROW["bill_rate"]) + $mainROW["client_msp_charge_dollar"], 2);

					$primeCharges = round(((($mainROW["client_prime_charge_percentage"] / 100) * $mainROW["bill_rate"]) + (($mainROW["employee_prime_charge_percentage"] / 100) * $mainROW["bill_rate"]) + $mainROW["employee_prime_charge_dollar"] + $mainROW["employee_any_charge_dollar"] + $mainROW["client_prime_charge_dollar"]), 2);

					$candidateRate = round(($mainROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

					$grossMargin = round(($mainROW["bill_rate"] - $candidateRate), 2);

					//$totalHour = round(employeeWorkingHours($vtechhrmConn,$fromDate,$toDate,$mainROW["employee_id"]), 2);
					
					$totalHour = round(array_sum($employeeTimeEntryTableData[$mainROW["employee_id"]]), 2);

					$totalGP = round(($grossMargin * $totalHour), 2);
					
					$totalRevenue = round(($mainROW["bill_rate"] * $totalHour), 2);

					if ($totalHour > "0") {
						$totalMarginArray[] = $grossMargin;
						$totalGPArray[] = $totalGP;
						$totalRevenueArray[] = $totalRevenue;
					}
				}
			}

		} else {
			//find log data

			$findLockDataQUERY = mysqli_query($allConn, "SELECT
				gd.id,
				gd.total_new,
				gd.total_left,
				gd.total_active,
				gd.total_data
			FROM
				mis_reports.gp_data AS gd
			WHERE
				gd.from_date = '$fromDate'
			AND
				gd.to_date = '$toDate'
			GROUP BY gd.id");

			$findLockDataROW = mysqli_fetch_array($findLockDataQUERY);

			$activeEmployee = $findLockDataROW["total_active"];
			$newEmployee = $findLockDataROW["total_new"];
			$leftEmployee = $findLockDataROW["total_left"];
			
			$totalNewEmployee[] = $newEmployee;
			$totalLeftEmployee[] = $leftEmployee;
			
			$openingBalance = $activeEmployee + $leftEmployee - $newEmployee;
			$closingBalance = $activeEmployee;

			$dataObject = json_decode($findLockDataROW["total_data"], true);

			$totalMarginArray[] = $dataObject[0]["total_gp_per_hour"];
			$totalGPArray[] = $dataObject[0]["final_gp"];
			$totalRevenueArray[] = $dataObject[0]["final_revenue"];
		}

		$attrition = round(($leftEmployee / (($openingBalance + $newEmployee) / 2)) * 100, 2)."%";

		$finalArray[] = array(
			"sr_no" => $loopMonth,
			"month" => date("F", mktime(0, 0, 0, $loopMonth, 10)),
			"opening_balance" => $openingBalance,
			"new_employee" => $newEmployee,
			"left_employee" => $leftEmployee,
			"closing_balance" => $closingBalance,
			"total_margin" => array_sum($totalMarginArray),
			"total_gp" => number_format(array_sum($totalGPArray), 2),
			"total_revenue" => number_format(array_sum($totalRevenueArray), 2),
			"attrition" => $attrition
		);

		$finalNewEmployee[] = array_sum($totalNewEmployee);
		
		$finalLeftEmployee[] = array_sum($totalLeftEmployee);
		
		$finalGP[] = array_sum($totalGPArray);
		
		$finalRevenue[] = array_sum($totalRevenueArray);
	}

$mailContent = '<!DOCTYPE html>
<html>
<body>
	<table style="width: 100%;">
		<tr>
			<td style="text-align: center;"><img src="cid:companyLogo"></td>
		</tr>
		<tr>
			<td style="background-color: #2266AA;padding: 7px;"></td>
		</tr>
		<tr>
			<td style="text-align: center;background-color: #ccc;padding: 5px;font-size: 18px;font-weight: bold;">Monthly GP Report - '.$reportTitleYear.'</td>
		</tr>
		<tr>
			<td>
				<br>
				<center>
				<table style="width: 100%;border: 1px solid #ddd;">
					<thead>
						<tr style="background-color: #ccc;color: #000;">
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;" rowspan="2">Sr.<br>No.</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;" rowspan="2">Month</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;" rowspan="2">Opening<br>Balance</th>
							<th style="text-align: center;vertical-align: middle;" colspan="2">Employees</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;" rowspan="2">Closing<br>Balance</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;" rowspan="2">GP / Hour</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;" rowspan="2">Total<br>GP</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;" rowspan="2">Total<br>Revenue</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;" rowspan="2">Attrition %</th>
						</tr>
						<tr style="background-color: #ccc;color: #000;">
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">New</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">Left</th>
						</tr>
					</thead>
					<tbody>';

					foreach ($finalArray as $finalArrayKey => $finalArrayValue) {

						$mailContent.='<tr>
							<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$finalArrayValue["sr_no"].'</td>
							<td style="text-align: left;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$finalArrayValue["month"].'</td>
							<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$finalArrayValue["opening_balance"].'</td>
							<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$finalArrayValue["new_employee"].'</td>
							<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$finalArrayValue["left_employee"].'</td>
							<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$finalArrayValue["closing_balance"].'</td>
							<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$finalArrayValue["total_margin"].'</td>
							<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$finalArrayValue["total_gp"].'</td>
							<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$finalArrayValue["total_revenue"].'</td>
							<td style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">'.$finalArrayValue["attrition"].'</td>
						</tr>';

					}

					$mailContent.='</tbody>
					<tfoot>
						<tr style="background-color: #ccc;color: #000;font-size: 16px;">
							<th style="text-align: center;vertical-align: middle;" colspan="3"></th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($finalNewEmployee).'</th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($finalLeftEmployee).'</th>
							<th style="text-align: center;vertical-align: middle;" colspan="2"></th>
							<th style="text-align: center;vertical-align: middle;">'.number_format(array_sum($finalGP), 2).'</th>
							<th style="text-align: center;vertical-align: middle;">'.number_format(array_sum($finalRevenue), 2).'</th>
							<th style="text-align: center;vertical-align: middle;"></th>
						</tr>
					</tfoot>
				</table>
				</center>
			</td>
		</tr>
		<tr>
			<td style="color: #555;text-align: right;font-size: 13px;"><br>* Auto generated notification. Please DO NOT reply *<br><hr style="border: 1px dashed #ccc;"></td>
		</tr>
	</table>
</body>
</html>';

	// Add a recipient
	$mail->addAddress('haresh@vtechsolution.com');
	$mail->addAddress('kapil@vtechsolution.com');
	$mail->addBcc('ravip@vtechsolution.us');

	echo $mailContent;
	
	include("../../functions/email-send-config.php");
?>
