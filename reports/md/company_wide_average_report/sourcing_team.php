<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../functions/reporting-service.php");
?>
	<script>
		$(document).ready(function(){
			var sourcingTeamReport = $(".sourcing-team-report").DataTable({
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
		$dateRange = $newEmployeeArray = $totalReport = $averageDailyReport = $averageReport = array();

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

			$totalDays = round((strtotime($dateRangeValue["end_date"]) - strtotime($dateRangeValue["start_date"])) / (60 * 60 * 24) + 1);

			$filterValue = $dateRangeValue["filter_value"];

			$newEmployeeQUERY = mysqli_query($allConn, "SELECT
			    u.user_id,
			    CONCAT(u.first_name,' ',u.last_name) AS user_name,
			    mes.date_of_joining,
				MIN(DATE_FORMAT(sub.date, '%Y-%m-%d')) AS first_submission_date,
				MIN(DATE_FORMAT(inter.date, '%Y-%m-%d')) AS first_interview_date,
				MIN(DATE_FORMAT(place.date, '%Y-%m-%d')) AS first_placement_date,
				MIN(DATE_FORMAT(ete.date_start, '%Y-%m-%d')) AS first_gp_date
			FROM
			    cats.user AS u
			    JOIN vtechhrm_in.main_users AS mu ON mu.emailaddress = u.email
			    JOIN vtechhrm_in.main_employees AS me ON me.user_id = mu.id
			    JOIN vtechhrm_in.main_employees_summary AS mes ON mes.user_id = mu.id
			    LEFT JOIN cats.candidate_joborder AS cj ON cj.sourced_by = u.user_id
			    LEFT JOIN cats.candidate_joborder_status_history AS sub ON sub.joborder_id = cj.joborder_id AND sub.candidate_id = cj.candidate_id AND sub.status_to = '400'
			    LEFT JOIN cats.candidate_joborder_status_history AS inter ON inter.joborder_id = cj.joborder_id AND inter.candidate_id = cj.candidate_id AND inter.status_to = '500'
			    LEFT JOIN cats.candidate_joborder_status_history AS place ON place.joborder_id = cj.joborder_id AND place.candidate_id = cj.candidate_id AND place.status_to = '800'
			    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.c_recruiter_id = u.user_id
			    LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = si.h_employee_id
			WHERE
				me.department_id IN (23)
			AND
			    mes.date_of_joining BETWEEN '$startDate' AND '$endDate'
			GROUP BY u.user_id");

			while ($newEmployeeROW = mysqli_fetch_array($newEmployeeQUERY)) {
				$newEmployeeArray[$newEmployeeROW["user_id"]] = array(
					"user_name" => ucwords($newEmployeeROW["user_name"]),
					"date_of_joining" => $newEmployeeROW["date_of_joining"],
					"first_submission_date" => $newEmployeeROW["first_submission_date"],
					"first_submission_days" => $newEmployeeROW["first_submission_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_submission_date"]) - strtotime($newEmployeeROW["date_of_joining"])) / (60 * 60 * 24)),
					"first_interview_date" => $newEmployeeROW["first_interview_date"],
					"first_interview_days" => $newEmployeeROW["first_interview_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_interview_date"]) - strtotime($newEmployeeROW["date_of_joining"])) / (60 * 60 * 24)),
					"first_placement_date" => $newEmployeeROW["first_placement_date"],
					"first_placement_days" => $newEmployeeROW["first_placement_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_placement_date"]) - strtotime($newEmployeeROW["date_of_joining"])) / (60 * 60 * 24)),
					"first_gp_date" => $newEmployeeROW["first_gp_date"],
					"first_gp_days" => $newEmployeeROW["first_gp_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_gp_date"]) - strtotime($newEmployeeROW["date_of_joining"])) / (60 * 60 * 24))
				);
			}

			$taxRate = $mspFees = $primeCharges = $candidateRate = "";

			$grossMargin = array();

			$delimiter = array("","[","]",'"');

			$subQUERY = "SELECT
				e.id AS employee_id,
				e.employee_id AS emp_id,
				e.status AS employee_status,
				e.custom1 AS benefit,
				e.custom2 AS benefit_list,
				CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
				CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
				es.id AS employment_id,
				es.name AS employment_type,
				si.c_company_id AS company_id,
				clf.mspChrg_pct AS client_msp_charge_percentage,
				clf.primechrg_pct AS client_prime_charge_percentage,
				clf.primeChrg_dlr AS client_prime_charge_dollar,
				clf.mspChrg_dlr AS client_msp_charge_dollar,
				cnf.c_primeCharge_pct AS employee_prime_charge_percentage,
				cnf.c_primeCharge_dlr AS employee_prime_charge_dollar,
				cnf.c_anyCharge_dlr AS employee_any_charge_dollar
			FROM
				cats.user AS u
				JOIN vtech_mappingdb.manage_cats_roles AS mcr ON mcr.user_id = u.user_id AND mcr.designation LIKE '%Sourcing%'
			    JOIN cats.candidate_joborder AS cj ON cj.sourced_by = u.user_id
				JOIN vtech_mappingdb.system_integration AS si ON si.c_candidate_id = cj.candidate_id AND si.c_joborder_id = cj.joborder_id
				JOIN vtechhrm.employees AS e ON e.id = si.h_employee_id
				JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
				JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
				JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
				LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = si.c_company_id
				LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
			WHERE
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY employee_id";

			$subRESULT = mysqli_query($allConn, $subQUERY);

			if (mysqli_num_rows($subRESULT) > 0) {
				while ($subROW = mysqli_fetch_array($subRESULT)) {
								
					$benefitList = str_replace($delimiter, $delimiter[0], $subROW["benefit_list"]);

					//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$subROW["benefit"],$benefitList,$subROW["employment_id"],$subROW["pay_rate"]), 2);

					$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$subROW["benefit"],$benefitList,$subROW["employment_id"],$subROW["pay_rate"]), 2);

					$mspFees = round((($subROW["client_msp_charge_percentage"] / 100) * $subROW["bill_rate"]) + $subROW["client_msp_charge_dollar"], 2);

					$primeCharges = round(((($subROW["client_prime_charge_percentage"] / 100) * $subROW["bill_rate"]) + (($subROW["employee_prime_charge_percentage"] / 100) * $subROW["bill_rate"]) + $subROW["employee_prime_charge_dollar"] + $subROW["employee_any_charge_dollar"] + $subROW["client_prime_charge_dollar"]), 2);

					$candidateRate = round(($subROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

					$grossMargin[] = round(($subROW["bill_rate"] - $candidateRate), 2);
				}
			}

			$totalGpPerHour = round(array_sum($grossMargin), 2);

			$mainQUERY = "SELECT
				COUNT(a.sourcing_personnel_id) AS total_personnel,
				SUM(a.personnel_live_status) AS total_active_personnel,
				IF((SUM(b.total_pipeline) IS NULL OR SUM(b.total_pipeline) = ''), 0, SUM(b.total_pipeline)) AS total_pipeline,
				IF((SUM(c.total_submission) IS NULL OR SUM(c.total_submission) = ''), 0, SUM(c.total_submission)) AS total_submission,
				IF((SUM(c.total_interview) IS NULL OR SUM(c.total_interview) = ''), 0, SUM(c.total_interview)) AS total_interview,
				IF((SUM(c.total_interview_decline) IS NULL OR SUM(c.total_interview_decline) = ''), 0, SUM(c.total_interview_decline)) AS total_interview_decline,
				IF((SUM(c.total_offer) IS NULL OR SUM(c.total_offer) = ''), 0, SUM(c.total_offer)) AS total_offer,
				IF((SUM(c.total_failed_delivery) IS NULL OR SUM(c.total_failed_delivery) = ''), 0, SUM(c.total_failed_delivery)) AS total_failed_delivery,
				IF((SUM(d.total_placed) IS NULL OR SUM(d.total_placed) = ''), 0, SUM(d.total_placed)) AS total_placed,
				IF((SUM(e.total_extension) IS NULL OR SUM(e.total_extension) = ''), 0, SUM(e.total_extension)) AS total_extension,
				IF((SUM(f.resources_working) IS NULL OR SUM(f.resources_working) = ''), 0, SUM(f.resources_working)) AS resources_working
			FROM
			(SELECT
				u.user_id AS sourcing_personnel_id,
				CONCAT(u.first_name,' ',u.last_name) AS sourcing_personnel_name,
				IF(u.access_level != 0, 1, 0) AS personnel_live_status
			FROM
				cats.user AS u
				JOIN vtech_mappingdb.manage_cats_roles AS mcr ON mcr.user_id = u.user_id
			WHERE
				mcr.designation LIKE '%Sourcing%'
			GROUP BY sourcing_personnel_id) AS a
			LEFT OUTER JOIN
			(SELECT
				u.user_id AS sourcing_personnel_id,
			    COUNT(cj.candidate_id) AS total_pipeline
			FROM
				cats.user AS u
			    JOIN cats.candidate_joborder AS cj ON cj.sourced_by = u.user_id
			WHERE
				DATE_FORMAT(cj.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY sourcing_personnel_id) AS b ON b.sourcing_personnel_id = a.sourcing_personnel_id
			LEFT OUTER JOIN
			(SELECT
				u.user_id AS sourcing_personnel_id,
			    COUNT(CASE WHEN cjsh.status_to = '400' THEN 1 END) AS total_submission,
			    COUNT(CASE WHEN cjsh.status_to = '500' THEN 1 END) AS total_interview,
			    COUNT(CASE WHEN cjsh.status_to = '560' THEN 1 END) AS total_interview_decline,
			    COUNT(CASE WHEN cjsh.status_to = '600' THEN 1 END) AS total_offer,
			    COUNT(CASE WHEN cjsh.status_to = '900' THEN 1 END) AS total_failed_delivery
			FROM
				cats.user AS u
			    JOIN cats.candidate_joborder AS cj ON cj.sourced_by = u.user_id
			    JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.candidate_id = cj.candidate_id AND cjsh.joborder_id = cj.joborder_id
			WHERE
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY sourcing_personnel_id) AS c ON c.sourcing_personnel_id = a.sourcing_personnel_id
			LEFT OUTER JOIN
			(SELECT
				u.user_id AS sourcing_personnel_id,
			    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
			FROM
				cats.user AS u
			    JOIN cats.candidate_joborder AS cj ON cj.sourced_by = u.user_id
			    JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.candidate_id = cj.candidate_id AND cjsh.joborder_id = cj.joborder_id
			WHERE
				cjsh.status_to = '800'
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			AND
				cjsh.candidate_id NOT IN (SELECT
			    cjsh.candidate_id
			FROM
				cats.user AS u
			    JOIN cats.candidate_joborder AS cj ON cj.sourced_by = u.user_id
			    JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.candidate_id = cj.candidate_id AND cjsh.joborder_id = cj.joborder_id
			WHERE
				cjsh.status_to = '620'
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
			GROUP BY sourcing_personnel_id) AS d ON d.sourcing_personnel_id = a.sourcing_personnel_id
			LEFT OUTER JOIN
			(SELECT
				u.user_id AS sourcing_personnel_id,
			    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_extension
			FROM
				cats.user AS u
			    JOIN cats.candidate_joborder AS cj ON cj.sourced_by = u.user_id
			    JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.candidate_id = cj.candidate_id AND cjsh.joborder_id = cj.joborder_id
			WHERE
				cjsh.status_to = '620'
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY sourcing_personnel_id) AS e ON e.sourcing_personnel_id = a.sourcing_personnel_id
			LEFT OUTER JOIN
			(SELECT
				u.user_id AS sourcing_personnel_id,
				COUNT(DISTINCT e.id) AS resources_working
			FROM
				cats.user AS u
			    JOIN cats.candidate_joborder AS cj ON cj.sourced_by = u.user_id
				JOIN vtech_mappingdb.system_integration AS si ON si.c_candidate_id = cj.candidate_id AND si.c_joborder_id = cj.joborder_id
				JOIN vtechhrm.employees AS e ON e.id = si.h_employee_id
				JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
				JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
				JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			WHERE
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY sourcing_personnel_id) AS f ON f.sourcing_personnel_id = a.sourcing_personnel_id
			WHERE
				(b.total_pipeline != '' OR c.total_submission != '' OR c.total_interview != '' OR c.total_interview_decline != '' OR c.total_offer != '' OR c.total_failed_delivery != '' OR d.total_placed != '' OR e.total_extension != '' OR f.resources_working != '')";

			$mainRESULT = mysqli_query($catsConn, $mainQUERY);

			if (mysqli_num_rows($mainRESULT) > 0) {
				while ($mainROW = mysqli_fetch_array($mainRESULT)) {
					$totalReport[] = array(
						"daterange_type" => $filterValue,
						"total_personnel" => $mainROW["total_personnel"],
						"total_active_personnel" => $mainROW["total_active_personnel"],
						"total_pipeline" => $mainROW["total_pipeline"],
						"total_submission" => $mainROW["total_submission"],
						"total_interview" => $mainROW["total_interview"],
						"total_interview_decline" => $mainROW["total_interview_decline"],
						"total_offer" => $mainROW["total_offer"],
						"total_failed_delivery" => $mainROW["total_failed_delivery"],
						"total_placed" => $mainROW["total_placed"],
						"total_extension" => $mainROW["total_extension"],
						"resources_working" => $mainROW["resources_working"],
						"total_gp_per_hour" => $totalGpPerHour,
						"total_new_employees" => count($newEmployeeArray)
					);

					$averageDailyReport[] = array(
						"daterange_type" => $filterValue,
						"total_pipeline" => round((($mainROW["total_pipeline"] / $mainROW["total_personnel"])), 2),
						"total_submission" => round((($mainROW["total_submission"] / $mainROW["total_personnel"])), 2),
						"total_interview" => round((($mainROW["total_interview"] / $mainROW["total_personnel"])), 2),
						"total_interview_decline" => round((($mainROW["total_interview_decline"] / $mainROW["total_personnel"])), 2),
						"total_offer" => round((($mainROW["total_offer"] / $mainROW["total_personnel"])), 2),
						"total_failed_delivery" => round((($mainROW["total_failed_delivery"] / $mainROW["total_personnel"])), 2),
						"total_placed" => round((($mainROW["total_placed"] / $mainROW["total_personnel"])), 2),
						"total_extension" => round((($mainROW["total_extension"] / $mainROW["total_personnel"])), 2),
						"resources_working" => round((($mainROW["resources_working"] / $mainROW["total_personnel"])), 2),
						"total_gp_per_hour" => round((($totalGpPerHour / $mainROW["total_personnel"])), 2),
						"average_submission" => round((array_sum(array_column($newEmployeeArray, "first_submission_days"))), 2),
						"average_interview" => round((array_sum(array_column($newEmployeeArray, "first_interview_days"))), 2),
						"average_placement" => round((array_sum(array_column($newEmployeeArray, "first_placement_days"))), 2),
						"average_gp_per_hour" => round((array_sum(array_column($newEmployeeArray, "first_gp_days"))), 2)
					);

					$averageReport[] = array(
						"daterange_type" => $filterValue,
						"total_pipeline" => round((($mainROW["total_pipeline"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_submission" => round((($mainROW["total_submission"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_interview" => round((($mainROW["total_interview"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_interview_decline" => round((($mainROW["total_interview_decline"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_offer" => round((($mainROW["total_offer"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_failed_delivery" => round((($mainROW["total_failed_delivery"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_placed" => round((($mainROW["total_placed"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_extension" => round((($mainROW["total_extension"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"resources_working" => round((($mainROW["resources_working"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_gp_per_hour" => round((($totalGpPerHour / $mainROW["total_personnel"]) / $totalDays), 2),
						"average_submission" => round((array_sum(array_column($newEmployeeArray, "first_submission_days")) / count($newEmployeeArray)), 2),
						"average_interview" => round((array_sum(array_column($newEmployeeArray, "first_interview_days")) / count($newEmployeeArray)), 2),
						"average_placement" => round((array_sum(array_column($newEmployeeArray, "first_placement_days")) / count($newEmployeeArray)), 2),
						"average_gp_per_hour" => round((array_sum(array_column($newEmployeeArray, "first_gp_days")) / count($newEmployeeArray)), 2)
					);
				}
			}
		}

		if (array_sum(array_column($totalReport, "total_personnel")) == 0) {
			$output = '<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<img src="'.IMAGE_PATH.'/no-record-found.png">
				</div>
			</div>';
		} else {
			$output = '<div class="row">
				<div class="col-md-4 col-md-offset-4 report-headline">
					Total Report
				</div>
				<div class="col-md-12">
					<table class="table table-striped table-bordered sourcing-team-report">
						<thead>
							<tr class="thead-tr-style">';
							if ($dateRangeType == "month") {
								$output .= '<th>Months</th>';
							} elseif ($dateRangeType == "quarter") {
								$output .= '<th>Quarters</th>';
							}
								$output .= '<th data-toggle="tooltip" data-placement="top" title="Active Employees">Team<br>Size</th>
								<th>Pipeline</th>
								<th>Submission</th>
								<th>Interview</th>
								<th>Interview Decline</th>
								<th>Offer</th>
								<th>Placed</th>
								<th>Extension</th>
								<th>Delivery Failed</th>
								<th data-toggle="tooltip" data-placement="top" title="Candidate Working">Resources Working</th>
								<th data-toggle="tooltip" data-placement="top" title="Based on Resources Working">GP (Per-Hour)</th>
								<th>New Team Members Joined</th>
							</tr>
						</thead>
						<tbody>';
						foreach ($totalReport as $totalReportKey => $totalReportValue) {
							$output .= '<tr>';
								if ($dateRangeType != "daterange") {
									$output .= '<td>'.$totalReportValue["daterange_type"].'</td>';
								}
								$output .= '<td>'.$totalReportValue["total_active_personnel"].'</td>
								<td>'.$totalReportValue["total_pipeline"].'</td>
								<td>'.$totalReportValue["total_submission"].'</td>
								<td>'.$totalReportValue["total_interview"].'</td>
								<td>'.$totalReportValue["total_interview_decline"].'</td>
								<td>'.$totalReportValue["total_offer"].'</td>
								<td>'.$totalReportValue["total_placed"].'</td>
								<td>'.$totalReportValue["total_extension"].'</td>
								<td>'.$totalReportValue["total_failed_delivery"].'</td>
								<td>'.$totalReportValue["resources_working"].'</td>
								<td>'.$totalReportValue["total_gp_per_hour"].'</td>
								<td>'.$totalReportValue["total_new_employees"].'</td>
							</tr>';
						}
					$output .= '</tbody>
					</table>
				</div>
			</div>';

			$output .= '<div class="row" style="margin-top: 30px;margin-bottom: 20px;">
				<div class="col-md-4 col-md-offset-4 report-headline">
					Average Daily Report
				</div>
				<div class="col-md-12">
					<table class="table table-striped table-bordered sourcing-team-report">
						<thead>
							<tr class="thead-tr-style">';
							if ($dateRangeType == "month") {
								$output .= '<th rowspan="2">Months</th>';
							} elseif ($dateRangeType == "quarter") {
								$output .= '<th rowspan="2">Quarters</th>';
							}
								$output .= '<th colspan="10">Average Daily</th>
								<th colspan="4">Average time taken to make first (in day)</th>
							</tr>
							<tr class="thead-tr-style">
								<th>Pipeline</th>
								<th>Submission</th>
								<th>Interview</th>
								<th>Interview Decline</th>
								<th>Offer</th>
								<th>Placed</th>
								<th>Extension</th>
								<th>Delivery Failed</th>
								<th data-toggle="tooltip" data-placement="top" title="Candidate Working">Resources Working</th>
								<th data-toggle="tooltip" data-placement="top" title="Based on Resources Working">GP (Per-Hour)</th>
								<th>Submission</th>
								<th>Interview</th>
								<th>Placement</th>
								<th>GP</th>
							</tr>
						</thead>
						<tbody>';
						foreach ($averageDailyReport as $averageDailyReportKey => $averageDailyReportValue) {
							$output .= '<tr>';
								if ($dateRangeType != "daterange") {
									$output .= '<td>'.$averageDailyReportValue["daterange_type"].'</td>';
								}
								$output .= '<td>'.$averageDailyReportValue["total_pipeline"].'</td>
								<td>'.$averageDailyReportValue["total_submission"].'</td>
								<td>'.$averageDailyReportValue["total_interview"].'</td>
								<td>'.$averageDailyReportValue["total_interview_decline"].'</td>
								<td>'.$averageDailyReportValue["total_offer"].'</td>
								<td>'.$averageDailyReportValue["total_placed"].'</td>
								<td>'.$averageDailyReportValue["total_extension"].'</td>
								<td>'.$averageDailyReportValue["total_failed_delivery"].'</td>
								<td>'.$averageDailyReportValue["resources_working"].'</td>
								<td>'.$averageDailyReportValue["total_gp_per_hour"].'</td>
								<td>'.$averageDailyReportValue["average_submission"].'</td>
								<td>'.$averageDailyReportValue["average_interview"].'</td>
								<td>'.$averageDailyReportValue["average_placement"].'</td>
								<td>'.$averageDailyReportValue["average_gp_per_hour"].'</td>
							</tr>';
						}
					$output .= '</tbody>
					</table>
				</div>
			</div>';

			$output .= '<div class="row" style="margin-top: 30px;margin-bottom: 20px;">
				<div class="col-md-4 col-md-offset-4 report-headline">
					Average Per Person Report
				</div>
				<div class="col-md-12">
					<table class="table table-striped table-bordered sourcing-team-report">
						<thead>
							<tr class="thead-tr-style">';
							if ($dateRangeType == "month") {
								$output .= '<th rowspan="2">Months</th>';
							} elseif ($dateRangeType == "quarter") {
								$output .= '<th rowspan="2">Quarters</th>';
							}
								$output .= '<th colspan="10">Average Daily (per Personnel)</th>
								<th colspan="4">Average time taken to make first by New Personnel (in day)</th>
							</tr>
							<tr class="thead-tr-style">
								<th>Pipeline</th>
								<th>Submission</th>
								<th>Interview</th>
								<th>Interview Decline</th>
								<th>Offer</th>
								<th>Placed</th>
								<th>Extension</th>
								<th>Delivery Failed</th>
								<th data-toggle="tooltip" data-placement="top" title="Candidate Working">Resources Working</th>
								<th data-toggle="tooltip" data-placement="top" title="Based on Resources Working">GP (Per-Hour)</th>
								<th>Submission</th>
								<th>Interview</th>
								<th>Placement</th>
								<th>GP</th>
							</tr>
						</thead>
						<tbody>';
						foreach ($averageReport as $averageReportKey => $averageReportValue) {
							$output .= '<tr>';
								if ($dateRangeType != "daterange") {
									$output .= '<td>'.$averageReportValue["daterange_type"].'</td>';
								}
								$output .= '<td>'.$averageReportValue["total_pipeline"].'</td>
								<td>'.$averageReportValue["total_submission"].'</td>
								<td>'.$averageReportValue["total_interview"].'</td>
								<td>'.$averageReportValue["total_interview_decline"].'</td>
								<td>'.$averageReportValue["total_offer"].'</td>
								<td>'.$averageReportValue["total_placed"].'</td>
								<td>'.$averageReportValue["total_extension"].'</td>
								<td>'.$averageReportValue["total_failed_delivery"].'</td>
								<td>'.$averageReportValue["resources_working"].'</td>
								<td>'.$averageReportValue["total_gp_per_hour"].'</td>
								<td>'.$averageReportValue["average_submission"].'</td>
								<td>'.$averageReportValue["average_interview"].'</td>
								<td>'.$averageReportValue["average_placement"].'</td>
								<td>'.$averageReportValue["average_gp_per_hour"].'</td>
							</tr>';
						}
					$output .= '</tbody>
					</table>
				</div>
			</div>';
		}

		echo $output;
	}
?>