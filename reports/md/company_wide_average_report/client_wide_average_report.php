<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../functions/reporting-service.php");
?>
	<script>
		$(document).ready(function(){
			var clientWideAverageReport = $(".client-wide-average-report").DataTable({
			    dom: "Bfrtip",
			    "bPaginate": false,
			    "bFilter": false,
			    bInfo: false,
		        buttons:[
		        ]
			});
		});
	</script>
<?php
	if ($_POST) {
		$output = $dateRangeType = "";
		$dateRange = $newEmployeeArray = $totalReport = array();

		if ($_POST["multipleMonth"] != "") {
			$dateRange = monthDateRange(array_unique(explode(",", $_POST["multipleMonth"])));
		} elseif ($_POST["multipleQuarter"] != "") {
			$dateRange = quarterDateRange(array_unique(explode(",", $_POST["multipleQuarter"])));
		} elseif ($_POST["startDate"] != "" && $_POST["endDate"] != "") {
			$dateRange = normalDateRange($_POST["startDate"], $_POST["endDate"]);
		}

		$dateRangeType = $dateRange["filter_type"];

		array_shift($dateRange);

		$taxSettingsTableData = taxSettingsTable($allConn);

		foreach ($dateRange as $dateRangeKey => $dateRangeValue) {
			$startDate = $dateRangeValue["start_date"];
			$endDate = $dateRangeValue["end_date"];

			$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$startDate,$endDate);

			$totalDays = round((strtotime($dateRangeValue["end_date"]) - strtotime($dateRangeValue["start_date"])) / (60 * 60 * 24) + 1);

			$filterValue = $dateRangeValue["filter_value"];

			$mainQUERY = "SELECT
				a.company_id,
				a.company_name,
				b.total_submission,
				b.total_interview,
				b.total_interview_decline,
				b.total_offer,
				b.total_failed_delivery,
				c.total_placed,
				d.total_extension,
				e.total_job,
				e.total_openings,
				e.total_unanswered_job,
				e.total_unanswered_job_openings,
				f.total_termination,
				g.resources_working,
				h.new_join
			FROM
			(SELECT
				comp.company_id,
				comp.name AS company_name
			FROM
				company AS comp
			GROUP BY comp.company_id) AS a
			LEFT JOIN
			(SELECT
				comp.company_id,
			    COUNT(CASE WHEN cjsh.status_to = '400' THEN 1 END) AS total_submission,
			    COUNT(CASE WHEN cjsh.status_to = '500' THEN 1 END) AS total_interview,
			    COUNT(CASE WHEN cjsh.status_to = '560' THEN 1 END) AS total_interview_decline,
			    COUNT(CASE WHEN cjsh.status_to = '600' THEN 1 END) AS total_offer,
			    COUNT(CASE WHEN cjsh.status_to = '900' THEN 1 END) AS total_failed_delivery
			FROM
				candidate_joborder_status_history AS cjsh
			    LEFT JOIN joborder AS job ON cjsh.joborder_id = job.joborder_id
				LEFT JOIN company AS comp ON job.company_id = comp.company_id
			WHERE
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS b ON b.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
			    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
			FROM
				candidate_joborder_status_history AS cjsh
			    LEFT JOIN joborder AS job ON cjsh.joborder_id = job.joborder_id
				LEFT JOIN company AS comp ON job.company_id = comp.company_id
			WHERE
				cjsh.status_to = '800'
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			AND
				cjsh.candidate_id NOT IN (SELECT
			    cjsh.candidate_id
			FROM
				candidate_joborder_status_history AS cjsh
			    LEFT JOIN joborder AS job ON cjsh.joborder_id = job.joborder_id
				LEFT JOIN company AS comp ON job.company_id = comp.company_id
			WHERE
				cjsh.status_to = '620'
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
			GROUP BY comp.company_id) AS c ON c.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
			    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_extension
			FROM
				candidate_joborder_status_history AS cjsh
			    LEFT JOIN joborder AS job ON cjsh.joborder_id = job.joborder_id
				LEFT JOIN company AS comp ON job.company_id = comp.company_id
			WHERE
				cjsh.status_to = '620'
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS d ON d.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
				COUNT(DISTINCT job.joborder_id) AS total_job,
				SUM(job.openings) AS total_openings,
			    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, 1)) AS total_unanswered_job,
			    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, job.openings)) AS total_unanswered_job_openings
			FROM
				company AS comp
				LEFT JOIN joborder AS job ON comp.company_id = job.company_id
			WHERE
				DATE_FORMAT(job.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS e ON e.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
				COUNT(DISTINCT e.id) AS total_termination
			FROM 
				company AS comp
				LEFT JOIN vtech_mappingdb.system_integration AS si ON comp.company_id = si.c_company_id
				LEFT JOIN vtechhrm.employees AS e ON si.h_employee_id = e.id
			WHERE
				e.status IN ('Terminated','Termination In_Vol','Termination Vol')
			AND
				DATE_FORMAT(e.termination_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS f ON f.company_id = a.company_id
			LEFT JOIN
			(SELECT
				si.c_company_id AS company_id,
				COUNT(DISTINCT e.id) AS resources_working
			FROM
				vtechhrm.employees AS e
				LEFT JOIN vtechhrm.employeeprojects AS ep ON e.id = ep.employee
				LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			    LEFT JOIN vtech_mappingdb.system_integration AS si ON e.id = si.h_employee_id
			WHERE
				ep.project != '6'
			AND
				IF('$includePeriod' = 'true', (DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDate') AND (DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'), (DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'))
			GROUP BY si.c_company_id) AS g ON g.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
				COUNT(DISTINCT e.id) AS new_join
			FROM 
				company AS comp
				LEFT JOIN vtech_mappingdb.system_integration AS si ON comp.company_id = si.c_company_id
				LEFT JOIN vtechhrm.employees AS e ON si.h_employee_id = e.id
			WHERE
				e.status = 'Active'
			AND
				DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS h ON h.company_id = a.company_id
			WHERE
				(b.total_submission != '' OR b.total_interview != '' OR b.total_interview_decline != '' OR b.total_offer != '' OR b.total_failed_delivery != '' OR c.total_placed != '' OR d.total_extension != '' OR e.total_openings != '' OR f.total_termination != '' OR g.resources_working != '' OR h.new_join != '')";
			$mainRESULT = mysqli_query($catsConn, $mainQUERY);
			if (mysqli_num_rows($mainRESULT) > 0) {
				while ($mainROW = mysqli_fetch_array($mainRESULT)) {

					$clientId = $mainROW["company_id"];
					
					$totalBillRate = $payC2C = $actualMargin = $newMargin = $lostMargin = array();
					
					$delimiter = array("","[","]",'"');

					$actualMarginQUERY = "SELECT
						e.id AS employee_id,
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
						LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
					    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
						LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
						LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
						LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
					WHERE
						comp.company_id = '$clientId'
					AND
						ep.project != '6'";
					
					if ($includePeriod == "true") {
						$actualMarginQUERY .= " AND
							DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDate'
						AND
							DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
						GROUP BY employee_id";
					} else {
						$actualMarginQUERY .= " AND
							DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
						GROUP BY employee_id";
					}

					$actualMarginRESULT = mysqli_query($vtechhrmConn, $actualMarginQUERY);

					$taxRate = $mspFees = $primeCharges = $candidateRate = "";
					
					if (mysqli_num_rows($actualMarginRESULT) > 0) {
						while ($actualMarginROW = mysqli_fetch_array($actualMarginRESULT)) {
										
							$benefitList = str_replace($delimiter, $delimiter[0], $actualMarginROW["benefit_list"]);

							//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$actualMarginROW["benefit"],$benefitList,$actualMarginROW["employment_id"],$actualMarginROW["pay_rate"]), 2);

							$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$actualMarginROW["benefit"],$benefitList,$actualMarginROW["employment_id"],$actualMarginROW["pay_rate"]), 2);

							$mspFees = round((($actualMarginROW["client_msp_charge_percentage"] / 100) * $actualMarginROW["bill_rate"]) + $actualMarginROW["client_msp_charge_dollar"], 2);

							$primeCharges = round(((($actualMarginROW["client_prime_charge_percentage"] / 100) * $actualMarginROW["bill_rate"]) + (($actualMarginROW["employee_prime_charge_percentage"] / 100) * $actualMarginROW["bill_rate"]) + $actualMarginROW["employee_prime_charge_dollar"] + $actualMarginROW["employee_any_charge_dollar"] + $actualMarginROW["client_prime_charge_dollar"]), 2);

							$candidateRate = round(($actualMarginROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

							//$totalHour = round(employeeWorkingHours($vtechhrmConn,$startDate,$endDate,$actualMarginROW["employee_id"]), 2);

							$totalHour = round(array_sum($employeeTimeEntryTableData[$actualMarginROW["employee_id"]]), 2);

							if ($totalHour > "0") {
								$totalBillRate[] = $actualMarginROW["bill_rate"];

								$payC2C[] = $candidateRate;

								$actualMargin[] = round(($actualMarginROW["bill_rate"] - $candidateRate), 2);
							}
						}
					}

					$newMarginQUERY = "SELECT
						e.id AS employee_id,
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
						LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
					    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
						LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
						LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
						LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
					WHERE
						comp.company_id = '$clientId'
					AND
						e.status = 'Active'
					AND
						DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
					GROUP BY employee_id";
					
					$newMarginRESULT = mysqli_query($vtechhrmConn, $newMarginQUERY);

					$taxRate = $mspFees = $primeCharges = $candidateRate = "";
					
					if (mysqli_num_rows($newMarginRESULT) > 0) {
						while ($newMarginROW = mysqli_fetch_array($newMarginRESULT)) {
										
							$benefitList = str_replace($delimiter, $delimiter[0], $newMarginROW["benefit_list"]);

							//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$newMarginROW["benefit"],$benefitList,$newMarginROW["employment_id"],$newMarginROW["pay_rate"]), 2);

							$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$newMarginROW["benefit"],$benefitList,$newMarginROW["employment_id"],$newMarginROW["pay_rate"]), 2);

							$mspFees = round((($newMarginROW["client_msp_charge_percentage"] / 100) * $newMarginROW["bill_rate"]) + $newMarginROW["client_msp_charge_dollar"], 2);

							$primeCharges = round(((($newMarginROW["client_prime_charge_percentage"] / 100) * $newMarginROW["bill_rate"]) + (($newMarginROW["employee_prime_charge_percentage"] / 100) * $newMarginROW["bill_rate"]) + $newMarginROW["employee_prime_charge_dollar"] + $newMarginROW["employee_any_charge_dollar"] + $newMarginROW["client_prime_charge_dollar"]), 2);

							$candidateRate = round(($newMarginROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

							$newMargin[] = round(($newMarginROW["bill_rate"] - $candidateRate), 2);
						}
					}
					
					$lostMarginQUERY = "SELECT
						e.id AS employee_id,
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
						LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
					WHERE
						comp.company_id = '$clientId'
					AND
						ep.project != '6'
					AND
						e.status IN ('Terminated','Termination In_Vol','Termination Vol')
					AND
						DATE_FORMAT(e.termination_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
					GROUP BY employee_id";
					
					$lostMarginRESULT = mysqli_query($vtechhrmConn, $lostMarginQUERY);

					$taxRate = $mspFees = $primeCharges = $candidateRate = "";
					
					if (mysqli_num_rows($lostMarginRESULT) > 0) {
						while ($lostMarginROW = mysqli_fetch_array($lostMarginRESULT)) {
										
							$benefitList = str_replace($delimiter, $delimiter[0], $lostMarginROW["benefit_list"]);

							//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$lostMarginROW["benefit"],$benefitList,$lostMarginROW["employment_id"],$lostMarginROW["pay_rate"]), 2);

							$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$lostMarginROW["benefit"],$benefitList,$lostMarginROW["employment_id"],$lostMarginROW["pay_rate"]), 2);

							$mspFees = round((($lostMarginROW["client_msp_charge_percentage"] / 100) * $lostMarginROW["bill_rate"]) + $lostMarginROW["client_msp_charge_dollar"], 2);

							$primeCharges = round(((($lostMarginROW["client_prime_charge_percentage"] / 100) * $lostMarginROW["bill_rate"]) + (($lostMarginROW["employee_prime_charge_percentage"] / 100) * $lostMarginROW["bill_rate"]) + $lostMarginROW["employee_prime_charge_dollar"] + $lostMarginROW["employee_any_charge_dollar"] + $lostMarginROW["client_prime_charge_dollar"]), 2);

							$candidateRate = round(($lostMarginROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

							$lostMargin[] = round(($lostMarginROW["bill_rate"] - $candidateRate), 2);
						}
					}

					$totalReport[$filterValue][] = array(
						"daterange_type" => $filterValue,
						"total_days" => $totalDays,
						"client" => $mainROW["company_id"],
						"job" => $mainROW["total_job"],
						"openings" => $mainROW["total_openings"],
						"unanswered_job" => $mainROW["total_unanswered_job"],
						"unanswered_job_openings" => $mainROW["total_unanswered_job_openings"],
						"submission" => $mainROW["total_submission"],
						"interview" => $mainROW["total_interview"],
						"interview_decline" => $mainROW["total_interview_decline"],
						"offer" => $mainROW["total_offer"],
						"placed" => $mainROW["total_placed"],
						"extension" => $mainROW["total_extension"],
						"failed_delivery" => $mainROW["total_failed_delivery"],
						"resources_working" => $mainROW["resources_working"],
						"bill_rate" => array_sum($totalBillRate),
						"c2c_rate" => array_sum($payC2C),
						"actual_margin" => round(array_sum($actualMargin), 2),
						"new_join" => $mainROW["new_join"],
						"new_margin" => round(array_sum($newMargin), 2),
						"termination" => $mainROW["total_termination"],
						"lost_margin" => round(array_sum($lostMargin), 2)
					);
				}
			}
		}

		$output = '<div class="row">
			<div class="col-md-4 col-md-offset-4 report-headline">
				Total Report
			</div>
			<div class="col-md-12">
				<table class="table table-striped table-bordered client-wide-average-report">
					<thead>
						<tr class="thead-tr-style">';
						if ($dateRangeType == "month") {
							$output .= '<th rowspan="2">Months</th>';
						} elseif ($dateRangeType == "quarter") {
							$output .= '<th rowspan="2">Quarters</th>';
						}
							$output .= '<th rowspan="2">Clients</th>
							<th colspan="2">Assigned</th>
							<th colspan="2">Unanswered</th>
							<th rowspan="2">Submission</th>
							<th rowspan="2">Interview</th>
							<th rowspan="2">Interview Decline</th>
							<th rowspan="2">Offer</th>
							<th rowspan="2">Placed</th>
							<th rowspan="2">Extension</th>
							<th rowspan="2">Delivery Failed</th>
							<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Candidate Working">Resources Working</th>
							<th rowspan="2">Bill Rate($)</th>
							<th rowspan="2">Pay C2C($)</th>
							<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Actual Margin / Resources Working">Average Margin</th>
							<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Based on Resources Working">Actual Margin</th>
							<th rowspan="2">New Join</th>
							<th rowspan="2">New Margin</th>
							<th rowspan="2">Termination</th>
							<th rowspan="2">Lost Margin</th>
						</tr>
						<tr class="thead-tr-style">
							<th data-toggle="tooltip" data-placement="top" title="Total Joborder">Joborder</th>
							<th data-toggle="tooltip" data-placement="top" title="Total Openings">Openings</th>
							<th data-toggle="tooltip" data-placement="top" title="Joborder which has Zero Submission!">Joborder</th>
							<th data-toggle="tooltip" data-placement="top" title="Openings which has Zero Submission!">Openings</th>
						</tr>
					</thead>
					<tbody>';
					foreach ($totalReport as $totalReportKey => $totalReportValue) {
						$output .= '<tr>';
							if ($dateRangeType != "daterange") {
								$output .= '<td>'.$totalReportKey.'</td>';
							}
							$output .= '<td>'.count(array_column($totalReportValue, "client")).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "job")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "openings")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "unanswered_job")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "unanswered_job_openings")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "submission")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "interview")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "interview_decline")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "offer")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "placed")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "extension")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "failed_delivery")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "resources_working")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "bill_rate")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "c2c_rate")), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "actual_margin")) / array_sum(array_column($totalReportValue, "resources_working"))), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "actual_margin")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "new_join")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "new_margin")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "termination")), 2).'</td>
							<td>'.round(array_sum(array_column($totalReportValue, "lost_margin")), 2).'</td>
						</tr>';
					}
				$output .= '</tbody>
				</table>
			</div>
		</div>';

		$output .= '<div class="row" style="margin-top: 10px;">
			<div class="col-md-4 col-md-offset-4 report-headline">
				Average Report
			</div>
			<div class="col-md-12">
				<table class="table table-striped table-bordered client-wide-average-report">
					<thead>
						<tr class="thead-tr-style">';
						if ($dateRangeType == "month") {
							$output .= '<th rowspan="2">Months</th>';
						} elseif ($dateRangeType == "quarter") {
							$output .= '<th rowspan="2">Quarters</th>';
						}
							$output .= '<th colspan="2">Assigned</th>
							<th colspan="2">Unanswered</th>
							<th rowspan="2">Submission</th>
							<th rowspan="2">Interview</th>
							<th rowspan="2">Interview Decline</th>
							<th rowspan="2">Offer</th>
							<th rowspan="2">Placed</th>
							<th rowspan="2">Extension</th>
							<th rowspan="2">Delivery Failed</th>
							<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Candidate Working">Resources Working</th>
							<th rowspan="2">Bill Rate($)</th>
							<th rowspan="2">Pay C2C($)</th>
							<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Actual Margin / Resources Working">Average Margin</th>
							<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Based on Resources Working">Actual Margin</th>
							<th rowspan="2">New Join</th>
							<th rowspan="2">New Margin</th>
							<th rowspan="2">Termination</th>
							<th rowspan="2">Lost Margin</th>
						</tr>
						<tr class="thead-tr-style">
							<th data-toggle="tooltip" data-placement="top" title="Total Joborder">Joborder</th>
							<th data-toggle="tooltip" data-placement="top" title="Total Openings">Openings</th>
							<th data-toggle="tooltip" data-placement="top" title="Joborder which has Zero Submission!">Joborder</th>
							<th data-toggle="tooltip" data-placement="top" title="Openings which has Zero Submission!">Openings</th>
						</tr>
					</thead>
					<tbody>';
					foreach ($totalReport as $totalReportKey => $totalReportValue) {
						$output .= '<tr>';
							if ($dateRangeType != "daterange") {
								$output .= '<td>'.$totalReportKey.'</td>';
							}
							$output .= '<td>'.round((array_sum(array_column($totalReportValue, "job")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "openings")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "unanswered_job")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "unanswered_job_openings")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "submission")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "interview")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "interview_decline")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "offer")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "placed")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "extension")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "failed_delivery")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "resources_working")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "bill_rate")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "c2c_rate")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round(((array_sum(array_column($totalReportValue, "actual_margin")) / array_sum(array_column($totalReportValue, "resources_working"))) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "actual_margin")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "new_join")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "new_margin")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "termination")) / count(array_column($totalReportValue, "client"))), 2).'</td>
							<td>'.round((array_sum(array_column($totalReportValue, "lost_margin")) / count(array_column($totalReportValue, "client"))), 2).'</td>
						</tr>';
					}
				$output .= '</tbody>
				</table>
			</div>
		</div>';

		echo $output;
	}
?>