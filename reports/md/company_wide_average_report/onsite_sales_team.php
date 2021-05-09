<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../functions/reporting-service.php");
?>
	<script>
		$(document).ready(function(){
			var onsiteSalesTeamReport = $(".onsite-sales-team-report").DataTable({
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

			$joinedUserQUERY = mysqli_query($allConn, "SELECT
			    e.id AS user_id,
			    CONCAT(e.first_name,' ',e.last_name) AS user_name,
			    DATE_FORMAT(e.joined_date, '%Y-%m-%d') AS date_of_joining
			FROM
			    vtechhrm.employees AS e
			    JOIN vtechhrm.employeeprojects AS ep ON e.id = ep.employee
			WHERE
				ep.project = '6'
			AND
			    DATE_FORMAT(e.joined_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY e.id");

			while ($joinedUserROW = mysqli_fetch_array($joinedUserQUERY)) {
			    $joinedUserName = $joinedUserROW["user_name"];

			    $firstDayQUERY = mysqli_query($allConn, "SELECT
			        ef.value AS personnel,
			        MIN(DATE_FORMAT(sub.date, '%Y-%m-%d')) AS first_submission_date,
			        MIN(DATE_FORMAT(inter.date, '%Y-%m-%d')) AS first_interview_date,
			        MIN(DATE_FORMAT(place.date, '%Y-%m-%d')) AS first_placement_date,
			        MIN(DATE_FORMAT(ete.date_start, '%Y-%m-%d')) AS first_gp_date
			    FROM
			        cats.extra_field AS ef
			        LEFT JOIN cats.joborder AS j ON j.company_id = ef.data_item_id
			        LEFT JOIN cats.candidate_joborder_status_history AS sub ON sub.joborder_id = j.joborder_id AND sub.status_to = '400'
			        LEFT JOIN cats.candidate_joborder_status_history AS inter ON inter.joborder_id = j.joborder_id AND inter.status_to = '500'
			        LEFT JOIN cats.candidate_joborder_status_history AS place ON place.joborder_id = j.joborder_id AND place.status_to = '800'
			        LEFT JOIN vtech_mappingdb.system_integration AS si ON si.c_onsite_sales = ef.value
			        LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = si.h_employee_id
			    WHERE
			        ef.field_name = 'Onsite Sales Person'
			    AND
			        ef.value = '$joinedUserName'
			    GROUP BY personnel");

			    $firstDayROW = mysqli_fetch_array($firstDayQUERY);

			    $newEmployeeArray[$joinedUserROW["user_id"]] = array(
			        "user_name" => ucwords($joinedUserROW["user_name"]),
			        "date_of_joining" => $joinedUserROW["date_of_joining"],
			        "first_submission_date" => $firstDayROW["first_submission_date"],
			        "first_submission_days" => $firstDayROW["first_submission_date"] == "" ? "0" : round((strtotime($firstDayROW["first_submission_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
			        "first_interview_date" => $firstDayROW["first_interview_date"],
			        "first_interview_days" => $firstDayROW["first_interview_date"] == "" ? "0" : round((strtotime($firstDayROW["first_interview_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
			        "first_placement_date" => $firstDayROW["first_placement_date"],
			        "first_placement_days" => $firstDayROW["first_placement_date"] == "" ? "0" : round((strtotime($firstDayROW["first_placement_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
			        "first_gp_date" => $firstDayROW["first_gp_date"],
			        "first_gp_days" => $firstDayROW["first_gp_date"] == "" ? "0" : round((strtotime($firstDayROW["first_gp_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24))
			    );    
			}

			$personnelList = catsExtraFieldPersonnelList($catsConn,"Onsite Sales Person");

			$personnelGroup = "'".implode("', '", $personnelList)."'";

			$taxRate = $mspFees = $primeCharges = $candidateRate = "";

			$grossMargin = array();

			$delimiter = array("","[","]",'"');

			$subQUERY = "SELECT
				ef.value AS personnel,
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
				LEFT JOIN cats.extra_field AS ef ON ef.data_item_id = comp.company_id
				LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
				LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
			WHERE
				ef.field_name = 'Onsite Sales Person'
			AND
				ef.value IN ($personnelGroup)
			AND
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY employee_id,personnel";

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
				COUNT(DISTINCT a.personnel) AS total_personnel,
				SUM(a.personnel_live_status) AS total_active_personnel,
				IF((SUM(a.total_client) IS NULL OR SUM(a.total_client) = ''), 0, SUM(a.total_client)) AS total_client,
				IF((SUM(b.total_submission) IS NULL OR SUM(b.total_submission) = ''), 0, SUM(b.total_submission)) AS total_submission,
				IF((SUM(b.total_interview) IS NULL OR SUM(b.total_interview) = ''), 0, SUM(b.total_interview)) AS total_interview,
				IF((SUM(b.total_interview_decline) IS NULL OR SUM(b.total_interview_decline) = ''), 0, SUM(b.total_interview_decline)) AS total_interview_decline,
				IF((SUM(b.total_offer) IS NULL OR SUM(b.total_offer) = ''), 0, SUM(b.total_offer)) AS total_offer,
				IF((SUM(b.total_failed_delivery) IS NULL OR SUM(b.total_failed_delivery) = ''), 0, SUM(b.total_failed_delivery)) AS total_failed_delivery,
				IF((SUM(c.total_placed) IS NULL OR SUM(c.total_placed) = ''), 0, SUM(c.total_placed)) AS total_placed,
				IF((SUM(d.total_extension) IS NULL OR SUM(d.total_extension) = ''), 0, SUM(d.total_extension)) AS total_extension,
				IF((SUM(g.resources_working) IS NULL OR SUM(g.resources_working) = ''), 0, SUM(g.resources_working)) AS resources_working,
				IF((SUM(h.total_job) IS NULL OR SUM(h.total_job) = ''), 0, SUM(h.total_job)) AS total_job,
				IF((SUM(h.total_openings) IS NULL OR SUM(h.total_openings) = ''), 0, SUM(h.total_openings)) AS total_openings,
				IF((SUM(h.total_unanswered_job) IS NULL OR SUM(h.total_unanswered_job) = ''), 0, SUM(h.total_unanswered_job)) AS total_unanswered_job,
				IF((SUM(h.total_unanswered_job_openings) IS NULL OR SUM(h.total_unanswered_job_openings) = ''), 0, SUM(h.total_unanswered_job_openings)) AS total_unanswered_job_openings
			FROM
			(SELECT
				ef.value AS personnel,
				COUNT(DISTINCT comp.company_id) AS total_client,
				(SELECT IF(mu.isactive = 1, 1, 0) AS user_status FROM vtechhrm_in.main_users AS mu WHERE LOWER(CONCAT(TRIM(mu.firstname),' ',TRIM(mu.lastname))) = LOWER(TRIM(ef.value)) GROUP BY mu.id LIMIT 1) AS personnel_live_status
			FROM
				cats.company AS comp
				JOIN cats.extra_field AS ef ON comp.company_id = ef.data_item_id
			WHERE
				ef.field_name = 'Onsite Sales Person'
			AND
				ef.value IN ($personnelGroup)
			GROUP BY ef.value) AS a
			LEFT JOIN
			(SELECT
				ef.value AS personnel,
			    COUNT(CASE WHEN cjsh.status_to = '400' THEN 1 END) AS total_submission,
			    COUNT(CASE WHEN cjsh.status_to = '500' THEN 1 END) AS total_interview,
			    COUNT(CASE WHEN cjsh.status_to = '560' THEN 1 END) AS total_interview_decline,
			    COUNT(CASE WHEN cjsh.status_to = '600' THEN 1 END) AS total_offer,
			    COUNT(CASE WHEN cjsh.status_to = '900' THEN 1 END) AS total_failed_delivery
			FROM
				cats.candidate_joborder_status_history AS cjsh
			    JOIN cats.candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			    JOIN cats.joborder AS job ON cj.joborder_id = job.joborder_id
				JOIN cats.company AS comp ON job.company_id = comp.company_id
				JOIN cats.extra_field AS ef ON comp.company_id = ef.data_item_id
			WHERE
				ef.field_name = 'Onsite Sales Person'
			AND
				ef.value IN ($personnelGroup)
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY ef.value) AS b ON b.personnel = a.personnel
			LEFT JOIN
			(SELECT
				ef.value AS personnel,
			    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
			FROM
				cats.candidate_joborder_status_history AS cjsh
			    JOIN cats.candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			    JOIN cats.joborder AS job ON cj.joborder_id = job.joborder_id
				JOIN cats.company AS comp ON job.company_id = comp.company_id
				JOIN cats.extra_field AS ef ON comp.company_id = ef.data_item_id
			WHERE
				cjsh.status_to = '800'
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			AND
				ef.field_name = 'Onsite Sales Person'
			AND
				ef.value IN ($personnelGroup)
			AND
				cjsh.candidate_id NOT IN (SELECT
			    cjsh.candidate_id
			FROM
				cats.candidate_joborder_status_history AS cjsh
			    JOIN cats.candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			    JOIN cats.joborder AS job ON cj.joborder_id = job.joborder_id
				JOIN cats.company AS comp ON job.company_id = comp.company_id
				JOIN cats.extra_field AS ef ON comp.company_id = ef.data_item_id
			WHERE
				cjsh.status_to = '620'
			AND
				ef.field_name = 'Onsite Sales Person'
			AND
				ef.value IN ($personnelGroup)
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
			GROUP BY ef.value) AS c ON c.personnel = a.personnel
			LEFT JOIN
			(SELECT
				ef.value AS personnel,
			    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_extension
			FROM
				cats.candidate_joborder_status_history AS cjsh
			    JOIN cats.candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			    JOIN cats.joborder AS job ON cj.joborder_id = job.joborder_id
				JOIN cats.company AS comp ON job.company_id = comp.company_id
				JOIN cats.extra_field AS ef ON comp.company_id = ef.data_item_id
			WHERE
				cjsh.status_to = '620'
			AND
				ef.field_name = 'Onsite Sales Person'
			AND
				ef.value IN ($personnelGroup)
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY ef.value) AS d ON d.personnel = a.personnel
			LEFT JOIN
			(SELECT
				ef.value AS personnel,
				COUNT(DISTINCT e.id) AS resources_working
			FROM
				vtechhrm.employees AS e
				JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
				JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
				JOIN vtechhrm.employeetimeentry AS ete ON e.id = ete.employee
				JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
				JOIN cats.company AS comp ON comp.company_id = si.c_company_id
				JOIN cats.extra_field AS ef ON ef.data_item_id = comp.company_id
			WHERE
				ef.field_name = 'Onsite Sales Person'
			AND
				ef.value IN ($personnelGroup)
			AND
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY ef.value) AS g ON g.personnel = a.personnel
			LEFT JOIN
			(SELECT
				ef.value AS personnel,
				COUNT(DISTINCT job.joborder_id) AS total_job,
				SUM(job.openings) AS total_openings,
			    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, 1)) AS total_unanswered_job,
			    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, job.openings)) AS total_unanswered_job_openings
			FROM
				cats.extra_field AS ef
				LEFT JOIN cats.company AS comp ON comp.company_id = ef.data_item_id
				LEFT JOIN cats.joborder AS job ON job.company_id = comp.company_id
			WHERE
				ef.field_name = 'Onsite Sales Person'
			AND
				ef.value IN ($personnelGroup)
			AND
				DATE_FORMAT(job.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY ef.value) AS h ON h.personnel = a.personnel
			WHERE
				(b.total_submission != '' OR b.total_interview != '' OR b.total_interview_decline != '' OR b.total_offer != '' OR b.total_failed_delivery != '' OR c.total_placed != '' OR d.total_extension != '' OR g.resources_working != '')";

			$mainRESULT = mysqli_query($allConn, $mainQUERY);

			if (mysqli_num_rows($mainRESULT) > 0) {
				while ($mainROW = mysqli_fetch_array($mainRESULT)) {
					$totalReport[] = array(
						"daterange_type" => $filterValue,
						"total_personnel" => $mainROW["total_personnel"],
						"total_active_personnel" => $mainROW["total_active_personnel"],
						"total_client" => $mainROW["total_client"],
						"total_submission" => $mainROW["total_submission"],
						"total_interview" => $mainROW["total_interview"],
						"total_interview_decline" => $mainROW["total_interview_decline"],
						"total_offer" => $mainROW["total_offer"],
						"total_failed_delivery" => $mainROW["total_failed_delivery"],
						"total_placed" => $mainROW["total_placed"],
						"total_extension" => $mainROW["total_extension"],
						"resources_working" => $mainROW["resources_working"],
						"total_job" => $mainROW["total_job"],
						"total_openings" => $mainROW["total_openings"],
						"total_unanswered_job" => $mainROW["total_unanswered_job"],
						"total_unanswered_job_openings" => $mainROW["total_unanswered_job_openings"],
						"total_gp_per_hour" => $totalGpPerHour,
						"total_new_employees" => count($newEmployeeArray)
					);

					$averageDailyReport[] = array(
						"daterange_type" => $filterValue,
						"total_client" => round((($mainROW["total_client"] / $mainROW["total_personnel"])), 2),
						"total_submission" => round((($mainROW["total_submission"] / $mainROW["total_personnel"])), 2),
						"total_interview" => round((($mainROW["total_interview"] / $mainROW["total_personnel"])), 2),
						"total_interview_decline" => round(($mainROW["total_interview_decline"] / $mainROW["total_personnel"]), 2),
						"total_offer" => round((($mainROW["total_offer"] / $mainROW["total_personnel"])), 2),
						"total_failed_delivery" => round((($mainROW["total_failed_delivery"] / $mainROW["total_personnel"])), 2),
						"total_placed" => round((($mainROW["total_placed"] / $mainROW["total_personnel"])), 2),
						"total_extension" => round((($mainROW["total_extension"] / $mainROW["total_personnel"])), 2),
						"resources_working" => round((($mainROW["resources_working"] / $mainROW["total_personnel"])), 2),
						"total_job" => round((($mainROW["total_job"] / $mainROW["total_personnel"])), 2),
						"total_openings" => round((($mainROW["total_openings"] / $mainROW["total_personnel"])), 2),
						"total_unanswered_job" => round(($mainROW["total_unanswered_job"] / $mainROW["total_personnel"]), 2),
						"total_unanswered_job_openings" => round((($mainROW["total_unanswered_job_openings"] / $mainROW["total_personnel"])), 2),
						"total_gp_per_hour" => round((($totalGpPerHour / $mainROW["total_personnel"])), 2),
						"average_submission" => round((array_sum(array_column($newEmployeeArray, "first_submission_days"))), 2),
						"average_interview" => round((array_sum(array_column($newEmployeeArray, "first_interview_days"))), 2),
						"average_placement" => round((array_sum(array_column($newEmployeeArray, "first_placement_days"))), 2),
						"average_gp_per_hour" => round((array_sum(array_column($newEmployeeArray, "first_gp_days"))), 2)
					);

					$averageReport[] = array(
						"daterange_type" => $filterValue,
						"total_client" => round((($mainROW["total_client"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_submission" => round((($mainROW["total_submission"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_interview" => round((($mainROW["total_interview"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_interview_decline" => round(($mainROW["total_interview_decline"] / $mainROW["total_personnel"]), 2),
						"total_offer" => round((($mainROW["total_offer"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_failed_delivery" => round((($mainROW["total_failed_delivery"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_placed" => round((($mainROW["total_placed"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_extension" => round((($mainROW["total_extension"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"resources_working" => round((($mainROW["resources_working"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_job" => round((($mainROW["total_job"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_openings" => round((($mainROW["total_openings"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_unanswered_job" => round(($mainROW["total_unanswered_job"] / $mainROW["total_personnel"]), 2),
						"total_unanswered_job_openings" => round((($mainROW["total_unanswered_job_openings"] / $mainROW["total_personnel"]) / $totalDays), 2),
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
					<table class="table table-striped table-bordered onsite-sales-team-report">
						<thead>
							<tr class="thead-tr-style">';
							if ($dateRangeType == "month") {
								$output .= '<th rowspan="2">Months</th>';
							} elseif ($dateRangeType == "quarter") {
								$output .= '<th rowspan="2">Quarters</th>';
							}
								$output .= '<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Active Employees">Team<br>Size</th>
								<th rowspan="2">Clients</th>
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
								<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Based on Resources Working">GP (Per-Hour)</th>
								<th rowspan="2">New Team Members Joined</th>
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
									$output .= '<td>'.$totalReportValue["daterange_type"].'</td>';
								}
								$output .= '<td>'.$totalReportValue["total_active_personnel"].'</td>
								<td>'.$totalReportValue["total_client"].'</td>
								<td>'.$totalReportValue["total_job"].'</td>
								<td>'.$totalReportValue["total_openings"].'</td>
								<td>'.$totalReportValue["total_unanswered_job"].'</td>
								<td>'.$totalReportValue["total_unanswered_job_openings"].'</td>
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

			$output .= '<div class="row" style="margin-top: 10px;">
				<div class="col-md-4 col-md-offset-4 report-headline">
					Average Daily Report
				</div>
				<div class="col-md-12">
					<table class="table table-striped table-bordered onsite-sales-team-report">
						<thead>
							<tr class="thead-tr-style">';
							if ($dateRangeType == "month") {
								$output .= '<th rowspan="3">Months</th>';
							} elseif ($dateRangeType == "quarter") {
								$output .= '<th rowspan="3">Quarters</th>';
							}
								$output .= '<th rowspan="3">Clients</th>
								<th colspan="13">Average Daily (per Personnel)</th>
								<th colspan="4">Average time taken to make first (in day)</th>
							</tr>
							<tr class="thead-tr-style">
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
								<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Based on Resources Working">GP (Per-Hour)</th>
								<th rowspan="2">Submission</th>
								<th rowspan="2">Interview</th>
								<th rowspan="2">Placement</th>
								<th rowspan="2">GP</th>
							</tr>
							<tr class="thead-tr-style">
								<th data-toggle="tooltip" data-placement="top" title="Total Joborder">Joborder</th>
								<th data-toggle="tooltip" data-placement="top" title="Total Openings">Openings</th>
								<th data-toggle="tooltip" data-placement="top" title="Joborder which has Zero Submission!">Joborder</th>
								<th data-toggle="tooltip" data-placement="top" title="Openings which has Zero Submission!">Openings</th>
							</tr>
						</thead>
						<tbody>';
						foreach ($averageDailyReport as $averageDailyReportKey => $averageDailyReportValue) {
							$output .= '<tr>';
								if ($dateRangeType != "daterange") {
									$output .= '<td>'.$averageDailyReportValue["daterange_type"].'</td>';
								}
								$output .= '<td>'.$averageDailyReportValue["total_client"].'</td>
								<td>'.$averageDailyReportValue["total_job"].'</td>
								<td>'.$averageDailyReportValue["total_openings"].'</td>
								<td>'.$averageDailyReportValue["total_unanswered_job"].'</td>
								<td>'.$averageDailyReportValue["total_unanswered_job_openings"].'</td>
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

			$output .= '<div class="row" style="margin-top: 10px;">
				<div class="col-md-4 col-md-offset-4 report-headline">
					Average Per Person Report
				</div>
				<div class="col-md-12">
					<table class="table table-striped table-bordered onsite-sales-team-report">
						<thead>
							<tr class="thead-tr-style">';
							if ($dateRangeType == "month") {
								$output .= '<th rowspan="3">Months</th>';
							} elseif ($dateRangeType == "quarter") {
								$output .= '<th rowspan="3">Quarters</th>';
							}
								$output .= '<th rowspan="3">Clients</th>
								<th colspan="13">Average Daily (per Personnel)</th>
								<th colspan="4">Average time taken to make first by New Personnel (in day)</th>
							</tr>
							<tr class="thead-tr-style">
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
								<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Based on Resources Working">GP (Per-Hour)</th>
								<th rowspan="2">Submission</th>
								<th rowspan="2">Interview</th>
								<th rowspan="2">Placement</th>
								<th rowspan="2">GP</th>
							</tr>
							<tr class="thead-tr-style">
								<th data-toggle="tooltip" data-placement="top" title="Total Joborder">Joborder</th>
								<th data-toggle="tooltip" data-placement="top" title="Total Openings">Openings</th>
								<th data-toggle="tooltip" data-placement="top" title="Joborder which has Zero Submission!">Joborder</th>
								<th data-toggle="tooltip" data-placement="top" title="Openings which has Zero Submission!">Openings</th>
							</tr>
						</thead>
						<tbody>';
						foreach ($averageReport as $averageReportKey => $averageReportValue) {
							$output .= '<tr>';
								if ($dateRangeType != "daterange") {
									$output .= '<td>'.$averageReportValue["daterange_type"].'</td>';
								}
								$output .= '<td>'.$averageReportValue["total_client"].'</td>
								<td>'.$averageReportValue["total_job"].'</td>
								<td>'.$averageReportValue["total_openings"].'</td>
								<td>'.$averageReportValue["total_unanswered_job"].'</td>
								<td>'.$averageReportValue["total_unanswered_job_openings"].'</td>
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