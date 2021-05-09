<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "3";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>

<!-- Form Submission Process START -->
<?php
	if (isset($_REQUEST["form-submit-button"])) {
		$fromDate = $toDate = $mainTableData = $optimizationMatrixClientInfo = $dipAndDullClient = array();
		
		if (isset($_REQUEST["customized-multiple-month"])) {
			$monthsData = array_unique(explode(",", $_REQUEST["customized-multiple-month"]));
			foreach ($monthsData as $monthsDataKey => $monthsDataValue) {
				$dateGiven = explode("/", $monthsDataValue);
				$dateModified = $dateGiven[1]."-".$dateGiven[0];
				
				$fromDate[] = date("Y-m-01", strtotime($dateModified));
				$toDate[] = date("Y-m-t", strtotime($dateModified));
			}
		} else {
			$fromDate[] = date("Y-m-d", strtotime($_REQUEST["customized-from-date"]));
			$toDate[] = date("Y-m-d", strtotime($_REQUEST["customized-to-date"]));
?>
			<script>
				$(".customized-date-picker").prop("required", true);
				$(".customized-multiple-month").prop("required", false);
				$(".customized-date-picker").prop("disabled", false);
				$(".customized-multiple-month").prop("disabled", true);
				$(".date-range-input").removeClass("hidden");
				$(".multiple-month-input").addClass("hidden");
				$(".months-button").addClass("smooth-button");
				$(".months-button").removeClass("dark-button");
				$(".date-range-button").addClass("dark-button");
				$(".date-range-button").removeClass("smooth-button");
			</script>
<?php
		}

		$clientData = "'".implode("', '",$_REQUEST['client-list'])."'";

		if (isset($_REQUEST["this-month-input"])) {
			$includePeriod = "true";
		} else {
			$includePeriod = "false";
		}

		$thisYearStartDate = date("Y")."-01-01";
?>
<?php

		// Client Optimization Matrix Array START //
		$getAllResourceAndMargin = $averageMarginColumnList = $resourceWorkingColumnList = $manageMatrixColumnList = array();

		$clientOptimizationMatrixQUERY = mysqli_query($allConn, "SELECT
			com.id,
			com.title,
			com.resource_from,
			com.resource_to,
			com.margin_min,
			com.margin_max,
			com.color
		FROM
			vtech_mappingdb.client_optimization_matrix AS com
		GROUP BY com.id");

		if (mysqli_num_rows($clientOptimizationMatrixQUERY) > 0) {
			while ($clientOptimizationMatrixROW = mysqli_fetch_array($clientOptimizationMatrixQUERY)) {
				$getAllResourceAndMargin[$clientOptimizationMatrixROW["title"]]["resource_from"] = $clientOptimizationMatrixROW["resource_from"];
		    	$getAllResourceAndMargin[$clientOptimizationMatrixROW["title"]]["resource_to"] = $clientOptimizationMatrixROW["resource_to"];
		    	$getAllResourceAndMargin[$clientOptimizationMatrixROW["title"]]["margin_min"] = $clientOptimizationMatrixROW["margin_min"];
		    	$getAllResourceAndMargin[$clientOptimizationMatrixROW["title"]]["margin_max"] = $clientOptimizationMatrixROW["margin_max"];
		    	$getAllResourceAndMargin[$clientOptimizationMatrixROW["title"]]["color"] = $clientOptimizationMatrixROW["color"];
		    	$getAllResourceAndMargin[$clientOptimizationMatrixROW["title"]]["title"] = $clientOptimizationMatrixROW["title"];

				$columnKey = $clientOptimizationMatrixROW["margin_min"]." - ".$clientOptimizationMatrixROW["margin_max"];
    		
    			$rowKey = $clientOptimizationMatrixROW["resource_from"]." - ".$clientOptimizationMatrixROW["resource_to"];

    			if (!isset($averageMarginColumnList[$columnKey])) {
    				$averageMarginColumnList[$columnKey] = array(
    					"margin_min" => $clientOptimizationMatrixROW["margin_min"],
    					"margin_max" => $clientOptimizationMatrixROW["margin_max"]
    				);
    			}

    			if (!isset($resourceWorkingColumnList[$rowKey])) {
    				$resourceWorkingColumnList[$rowKey] = array(
    					"resource_from" => $clientOptimizationMatrixROW["resource_from"],
    					"resource_to" => $clientOptimizationMatrixROW["resource_to"]
    				);
    			}
    			$manageMatrixColumnList[] = $clientOptimizationMatrixROW;
			}
		}

		$optimizationMatrix = $optimizationMatrixArray = array();

	    foreach ($resourceWorkingColumnList as $resourceKey => $resourceValue) {
	    	foreach ($averageMarginColumnList as $marginKey => $marginValue) {
	    		$rangeMatrixDetail = geRangeMatrixDeatil($manageMatrixColumnList, $marginValue, $resourceValue);
	    		if ($rangeMatrixDetail != '') {
	    			$optimizationMatrix[] = array("title" => $rangeMatrixDetail['title'], "color" => $rangeMatrixDetail['color'], "client_list" => array());
	    			$optimizationMatrixArray[$resourceKey]['title'][] = $rangeMatrixDetail['title'];
	    			$optimizationMatrixArray[$resourceKey]['color'][] = $rangeMatrixDetail['color'];
	    		}
	    	}
	    }
		// Client Optimization Matrix Array END //

	    $taxSettingsTableData = taxSettingsTable($allConn);

		foreach ($fromDate as $fromDateKey => $fromDateValue) {
			$startDate = $fromDate[$fromDateKey];
			$endDate = $toDate[$fromDateKey];

			$givenMonth = date("m/Y", strtotime($startDate));

			$mainQUERY = "SELECT
				a.company_id,
				a.company_name,
				a.company_manager,
				a.ps_personnel,
				h.total_canceled_job,
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
				e.total_salary,
				e.average_bill,
				f.total_termination,
				g.resources_working
			FROM
			(SELECT
				comp.company_id,
				comp.name AS company_name,
				CONCAT(u.first_name,' ',u.last_name) AS company_manager,
				ef.value AS ps_personnel
			FROM
				company AS comp
				LEFT JOIN user AS u ON u.user_id = comp.owner
				LEFT JOIN extra_field AS ef ON ef.data_item_id = comp.company_id AND ef.field_name = 'Inside Post Sales'
			WHERE
				comp.company_id IN ($clientData)
			GROUP BY comp.company_id) AS a
			LEFT JOIN
			(SELECT
				comp.company_id,
				COUNT(DISTINCT job.joborder_id) AS total_canceled_job
			FROM
				company AS comp
				LEFT JOIN joborder AS job ON job.company_id = comp.company_id
			WHERE
				comp.company_id IN ($clientData)
			AND
				job.status = 'Canceled'
			AND
				DATE_FORMAT(job.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS h ON h.company_id = a.company_id
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
			    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			    JOIN joborder AS job ON cj.joborder_id = job.joborder_id
				JOIN company AS comp ON job.company_id = comp.company_id
			WHERE
				comp.company_id IN ($clientData)
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS b ON b.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
			    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
			FROM
				candidate_joborder_status_history AS cjsh
			    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			    JOIN joborder AS job ON cj.joborder_id = job.joborder_id
				JOIN company AS comp ON job.company_id = comp.company_id
			WHERE
				cjsh.status_to = '800'
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			AND
				comp.company_id IN ($clientData)
			AND
				cjsh.candidate_id NOT IN (SELECT
			    cjsh.candidate_id
			FROM
				candidate_joborder_status_history AS cjsh
			    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			    JOIN joborder AS job ON cj.joborder_id = job.joborder_id
				JOIN company AS comp ON job.company_id = comp.company_id
			WHERE
				cjsh.status_to = '620'
			AND
				comp.company_id IN ($clientData)
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
			GROUP BY comp.company_id) AS c ON c.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
			    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_extension
			FROM
				candidate_joborder_status_history AS cjsh
			    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			    JOIN joborder AS job ON cj.joborder_id = job.joborder_id
				JOIN company AS comp ON job.company_id = comp.company_id
			WHERE
				cjsh.status_to = '620'
			AND
				comp.company_id IN ($clientData)
			AND
				DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS d ON d.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
				COUNT(DISTINCT job.joborder_id) AS total_job,
				SUM(job.openings) AS total_openings,
				SUM(CAST(replace(job.salary,'$','') AS DECIMAL (10,2))*(job.openings)) AS total_salary,
				CAST((SUM(CAST(replace(job.salary,'$','') AS DECIMAL (10,2))*(job.openings)) / SUM(job.openings)) AS DECIMAL (10,2)) AS average_bill,
			    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, 1)) AS total_unanswered_job,
			    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, job.openings)) AS total_unanswered_job_openings
			FROM
				company AS comp
				JOIN joborder AS job ON comp.company_id =job.company_id
			WHERE
				comp.company_id IN ($clientData)
			AND
				job.status != 'Canceled'
			AND
				DATE_FORMAT(job.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS e ON e.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
				COUNT(DISTINCT e.id) AS total_termination
			FROM 
				company AS comp
				JOIN vtech_mappingdb.system_integration AS si ON comp.company_id = si.c_company_id
				JOIN vtechhrm.employees AS e ON si.h_employee_id = e.id
			WHERE
				comp.company_id IN ($clientData)
			AND
				e.status IN ('Terminated','Termination In_Vol','Termination Vol')
			AND
				DATE_FORMAT(e.termination_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY comp.company_id) AS f ON f.company_id = a.company_id
			LEFT JOIN
			(SELECT
				comp.company_id,
				COUNT(DISTINCT e.id) AS resources_working
			FROM
				company AS comp
				JOIN vtech_mappingdb.system_integration AS si ON comp.company_id = si.c_company_id
				JOIN vtechhrm.employees AS e ON si.h_employee_id = e.id
				JOIN vtechhrm.employeeprojects AS ep ON e.id = ep.employee
				JOIN vtechhrm.employeetimeentry AS ete ON e.id = ete.employee
			WHERE
				comp.company_id IN ($clientData)
			AND
				ep.project != '6'
			AND
				IF('$includePeriod' = 'true', (DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDate') AND (DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'), (DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'))
			GROUP BY comp.company_id) AS g ON g.company_id = a.company_id
			WHERE
				(b.total_submission != '' OR b.total_interview != '' OR b.total_interview_decline != '' OR b.total_offer != '' OR b.total_failed_delivery != '' OR c.total_placed != '' OR d.total_extension != '' OR e.total_openings != '' OR e.total_salary != '' OR e.average_bill != '' OR f.total_termination != '' OR g.resources_working != '')";

			$mainRESULT = mysqli_query($catsConn, $mainQUERY);
			
			if (mysqli_num_rows($mainRESULT) > 0) {
				while ($mainROW = mysqli_fetch_array($mainRESULT)) {

					$clientId = $mainROW["company_id"];

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
						$subQUERY .= " AND
							DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDate'
						AND
							DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
						GROUP BY employee_id";
					} else {
						$subQUERY .= " AND
							DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
						GROUP BY employee_id";
					}

					$subRESULT = mysqli_query($vtechhrmConn, $subQUERY);
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
					if ($includePeriod == "true") {
						$marginDetailPopup = "objectType=Client&objectId=".$mainROW["company_id"]."&objectName=".$mainROW["company_name"]."&startDate=".$thisYearStartDate."&endDate=".$endDate;
					} else{
						$marginDetailPopup = "objectType=Client&objectId=".$mainROW["company_id"]."&objectName=".$mainROW["company_name"]."&startDate=".$startDate."&endDate=".$endDate;
					}
					
					$personnelType = "client";
					
					$personnelManager = "";
					
					if ($mainROW["company_manager"] != "") {
						$personnelManager = " (".$mainROW["company_manager"].")";
					}

					$joborderDetailPopup = "personnel_id=".$mainROW["company_id"]."&personnel_name=".$mainROW["company_name"]."".$personnelManager."&personnel_type=".$personnelType."&start_date=".$startDate."&end_date=".$endDate;


					$resourcesWorkingNumber = $mainROW["resources_working"];
					$employeeAverageMargin = round(array_sum($grossMargin) / $mainROW["resources_working"], 2);
					
					$optimizationMatrixTitle = "-";
					$optimizationMatrixColor = "none";

					if ($resourcesWorkingNumber > 0 || $employeeAverageMargin > 0) {
						foreach ($getAllResourceAndMargin as $getAllResourceAndMarginKey => $getAllResourceAndMarginItem) {
							if ((($resourcesWorkingNumber >= $getAllResourceAndMarginItem["resource_from"]) && ($resourcesWorkingNumber <= $getAllResourceAndMarginItem["resource_to"] || $getAllResourceAndMarginItem["resource_to"] == null)) && (($employeeAverageMargin >= $getAllResourceAndMarginItem["margin_min"]) && ($employeeAverageMargin <= $getAllResourceAndMarginItem["margin_max"] || $getAllResourceAndMarginItem["margin_max"] == null) )) {

								$finalArray[$getAllResourceAndMarginKey][] = $mainROW["company_id"];

								$optimizationMatrixTitle = $getAllResourceAndMarginItem["title"];
								$optimizationMatrixColor = $getAllResourceAndMarginItem["color"];

								$optimizationMatrixClientInfo[$getAllResourceAndMarginItem["title"]]["company_id"][] = $mainROW["company_id"];

								$optimizationMatrixClientInfo[$getAllResourceAndMarginItem["title"]]["company_name"][] = $mainROW["company_name"];
							}
						}
					}

					$mainTableData[] = array(
						"company_id" => $mainROW["company_id"],
						"company_name" => $mainROW["company_name"],
						"company_manager" => $mainROW["company_manager"] != "" ? $mainROW["company_manager"] : "---",
						"ps_personnel" => $mainROW["ps_personnel"] != "" ? $mainROW["ps_personnel"] : "---",
						"given_month" => $givenMonth,
						"total_canceled_job" => $mainROW["total_canceled_job"] != "" ? $mainROW["total_canceled_job"] : 0,
						"total_job" => $mainROW["total_job"] != "" ? $mainROW["total_job"] : 0,
						"total_openings" => $mainROW["total_openings"] != "" ? $mainROW["total_openings"] : 0,
						"total_unanswered_job" => $mainROW["total_unanswered_job"] != "" ? $mainROW["total_unanswered_job"] : 0,
						"joborder_detail_popup" => $joborderDetailPopup,
						"total_unanswered_job_openings" => $mainROW["total_unanswered_job_openings"] != "" ? $mainROW["total_unanswered_job_openings"] : 0,
						"total_submission" => $mainROW["total_submission"] != "" ? $mainROW["total_submission"] : 0,
						"total_interview" => $mainROW["total_interview"] != "" ? $mainROW["total_interview"] : 0,
						"total_interview_decline" => $mainROW["total_interview_decline"] != "" ? $mainROW["total_interview_decline"] : 0,
						"total_offer" => $mainROW["total_offer"] != "" ? $mainROW["total_offer"] : 0,
						"total_placed" => $mainROW["total_placed"] != "" ? $mainROW["total_placed"] : 0,
						"total_extension" => $mainROW["total_extension"] != "" ? $mainROW["total_extension"] : 0,
						"total_failed_delivery" => $mainROW["total_failed_delivery"] != "" ? $mainROW["total_failed_delivery"] : 0,
						"total_termination" => $mainROW["total_termination"] != "" ? $mainROW["total_termination"] : 0,
						"resources_working" => $mainROW["resources_working"] != "" ? $mainROW["resources_working"] : 0,
						"average_actual_margin" => round(array_sum($grossMargin) / $mainROW["resources_working"], 2),
						"final_actual_margin" => round(array_sum($grossMargin), 2),
						"margin_detail_popup" => $marginDetailPopup,
						"optimization_matrix_rank" => array(
							"optimization_matrix_title" => $optimizationMatrixTitle,
							"optimization_matrix_color" => $optimizationMatrixColor
						)
					);

					$dipAndDullClient[$mainROW["company_id"]]['company_name'] = $mainROW["company_name"];
					$dipAndDullClient[$mainROW["company_id"]]['month'][] = $givenMonth;
					$dipAndDullClient[$mainROW["company_id"]]['rank'][] = $optimizationMatrixTitle;
					$dipAndDullClient[$mainROW["company_id"]]['color'][] = $optimizationMatrixColor;

					// Maximum Value Month
					$dipAndDullClientRank =  max($dipAndDullClient[$mainROW["company_id"]]['rank']);
					
					$keyRank = array_search ($dipAndDullClientRank, $dipAndDullClient[$mainROW["company_id"]]['rank']);

					// Last Month Value
					$dipAndDullClientColor = $dipAndDullClient[$mainROW["company_id"]]['color'][$keyRank];

					if ($dipAndDullClientRank < $optimizationMatrixTitle && $dipAndDullClientColor != $optimizationMatrixColor) {
						$dipAndDullClient[$mainROW["company_id"]]['clientType'] = "Dip";
					} elseif($dipAndDullClientRank < $optimizationMatrixTitle && $dipAndDullClientColor == $optimizationMatrixColor) {
						$dipAndDullClient[$mainROW["company_id"]]['clientType'] = "Dull";
					} else {
						$dipAndDullClient[$mainROW["company_id"]]['clientType'] = "None";
					}
				}
			}
		}
	}

	foreach ($dipAndDullClient as $dipAndDullClientKey => $dipAndDullClientValue) {
		if ($dipAndDullClientValue["clientType"] == "Dip") {
			$listOfDipClient[] = array(
				"clientName" => $dipAndDullClientValue["company_name"],
				"month" =>$dipAndDullClientValue["month"],
				"rank" => $dipAndDullClientValue["rank"]
			);
			
		} elseif ($dipAndDullClientValue["clientType"] == "Dull") {
			$listOfDullClient[] = array(
				"clientName" => $dipAndDullClientValue["company_name"],
				"month" =>$dipAndDullClientValue["month"],
				"rank" => $dipAndDullClientValue["rank"]
			);
		}
	}
?>
<!-- Form Submission Process END -->

<!DOCTYPE html>
<html>
<head>
	<title>Client Performance Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th,
		table.dataTable tbody td {
			padding: 3px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td {
			padding: 2px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td:nth-child(1) {
			text-align: left;
		}
		table.dataTable thead tr:nth-child(2) th:last-child {
			border-right: 1px solid #ddd;
		}
		table.dataTable tfoot tr:nth-child(2) th {
			text-align: left;
			vertical-align: middle;
		}
		table.dataTable tbody td a {
			cursor: pointer;
			font-weight: bold;
		}
		table.scrollable-datatable thead tr:nth-child(1) {
			padding: 4px 0px;
			text-align: center;
			vertical-align: middle;
			font-size: 15px;
			color: #2266AA;
		}
		table.dataTable thead tr:nth-child(2) th:last-child,
		table.dataTable thead tr:nth-child(3) th:last-child {
			border-right: 1px solid #ddd;
		}
		.modal-header {
			color: #fff;
			font-size: 20px;
			font-weight: bold;
			background-color: #2266AA;
			padding: 10px;
			text-align: center;
		}
		.modal-close-button {
			color: #fff;
		}
		.dark-button,
		.dark-button:focus {
			outline: none;
			color: #fff;
			background-color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
		}
		.smooth-button,
		.smooth-button:focus {
			outline: none;
			background-color: #fff;
			color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
			font-weight: bold;
		}
		.logout-button {
			outline: none;
			color: #fff;
			background-color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
			padding: 5px 12px;
			float: right;
		}
		.report-title {
			color: #000;
			font-size: 27px;
			background-color: #aaa;
			padding: 10px;
			text-align: center;
		}
		.loading-image-style {
			display: block;
			margin: 0 auto;
			width: 250px;
		}
		.main-section {
			margin-top: 20px;
			margin-bottom: 80px;
		}
		.main-section-row {
			margin-top: 15px;
		}
		.main-section-submit-row {
			margin-top: 30px;
		}
		.input-group-addon {
			background-color: #2266AA;
			border-color: #2266AA;
			color: #fff;
		}
		.form-submit-button {
			background-color: #449D44;
			border-radius: 0px;
			border: #449D44;
			outline: none;
			color: #fff;
		}
		.report-bottom-style {
			margin-bottom: 50px;
		}
		.thead-tr-style th {
			background-color: #ccc;
			color: #000;
			font-size: 12px;
		}
		.tbody-tr-style td {
			color: #333;
			font-size: 12px;
		}
		.tfoot-tr-style th {
			background-color: #ccc;
			color: #000;
			font-size: 12px;
		}
		.scrollable-datatable .thead-tr-style,
		.scrollable-datatable .tfoot-tr-style {
			background-color: #ccc;
			color: #000;
			font-size: 12px;
		}
		.scrollable-datatable .tbody-tr-style {
			color: #333;
			font-size: 13px;
		}
		.this-month-div {
			float: left;
		}
		.this-month-div input {
			height:13px;width:13px;cursor: pointer;
		}
		.this-month-div label {
			cursor: pointer;
		}
		.view-joborder-detail-popup .modal-lg {
			width: calc(100% - 100px);
		}
		.view-joborder-detail-popup .modal-header {
			background-color: #2266AA;
			color: #fff;
			font-weight: bold;
			text-align: center;
		}
		table.scrollable-datatable thead tr:nth-child(1) {
			background-color: #ccc;
			padding: 4px 0px;
			text-align: center;
			vertical-align: middle;
			font-size: 18px;
			color: #2266AA;
		}
		table.scrollable-datatable thead th,
		table.scrollable-datatable tfoot th {
			background-color: #ccc;
			color: #000;
			text-align: center;
			vertical-align: middle;
			font-size: 14px;
		}
		table.scrollable-datatable tbody td:nth-child(1),
		table.scrollable-datatable tbody td {
			font-size: 13px;
			text-align: center;
			vertical-align: middle;
		}
		.hyper-link-text {
			font-weight: bold;
			cursor: pointer;
		}
        #divLoading{
        	display : none;
        }
        #divLoading.show{
            display : block;
            position : fixed;
            z-index: 100;
            background-image : url('<?php echo IMAGE_PATH; ?>/loadingLogo.gif');
            background-color:#666;
            opacity : 0.4;
            background-repeat : no-repeat;
            background-position : center;
            left : 0;
            bottom : 0;
            right : 0;
            top : 0;
        }
        .optimizationMatrixTable tr th {
        	text-align: center;
        	vertical-align: middle;
        }
        .optimization-matrix-client-info {
        	color: #333;
        	text-decoration: none;
        	outline: none;
        	cursor: pointer;
        }
        .vertical-text {
			writing-mode: tb-rl;
			font-weight: bold;
			transform: rotate(-180deg);
		}
	</style>
</head>
<body>

	<div id="divLoading"></div>

	<?php include_once("../../../popups.php"); ?>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">Client Performance Report</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-2 col-md-offset-4">
					<button type="button" class="form-control months-button dark-button">Months</button>
				</div>
				<div class="col-md-2">
					<button type="button" class="form-control date-range-button smooth-button">Date Range</button>
				</div>
				<div class="col-md-4">
					<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
				</div>
			</div>

			<form action="index_old.php" method="post">
				<div class="row">
					<div class="col-md-4">
					<?php
						if (isset($_REQUEST["form-submit-button"])) {
					?>
						<table class="table table-striped table-bordered optimizationMatrixTable">
							<tr>
								<th colspan="6">Optimization Matrix</th>
							</tr>
							<tr>
								<th rowspan="<?php echo (count($resourceWorkingColumnList) * 2); ?>"><center><span class="vertical-text">Resources Working</span></center></th>
							</tr>
							<?php
								foreach (array_reverse($resourceWorkingColumnList) as $key => $value) {
							?>
							<tr>
								<th><?php echo $key; ?></th>
								<?php
									for ($i=0; $i < count($resourceWorkingColumnList); $i++) {
								?>
									<th style="background-color: <?php echo $optimizationMatrixArray[$key]['color'][$i]; ?>">
										<a class="optimization-matrix-client-info" data-title="<?php echo $optimizationMatrixArray[$key]['title'][$i]; ?>" data-color="<?php echo $optimizationMatrixArray[$key]['color'][$i]; ?>">
										<?php echo $optimizationMatrixArray[$key]['title'][$i]." (".count($optimizationMatrixClientInfo[$optimizationMatrixArray[$key]['title'][$i]]["company_id"]).")"; ?>
										</a>
									</th>
								<?php
									}
								?>
							</tr>
							<tr>
							<?php
								}
							?>
							</tr>
							<tr>
								<th colspan="2"></th>
								<?php
									foreach ($averageMarginColumnList as $key => $value) {
								?>
									<th><?php echo $key; ?></th>
								<?php
									}
								?>
							</tr>
							<tr>
								<th colspan="6">Average Margin (USD)</th>
							</tr>
						</table>
					<?php
						}
					?>
					</div>
					<div class="col-md-4">
						<div class="row main-section-row multiple-month-input">
							<div class="col-md-12">
								<label>Select Months:</label>
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
									
									<input type="text" name="customized-multiple-month" class="form-control customized-multiple-month" value="<?php if (isset($_REQUEST['customized-multiple-month'])) { echo $_REQUEST['customized-multiple-month']; } ?>" placeholder="MM/YYYY" autocomplete="off" required>
									
									<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
								</div>
							</div>
						</div>
						<div class="row main-section-row date-range-input hidden">
							<div class="col-md-6">
								<label>Date From :</label>
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
									
									<input type="text" name="customized-from-date" class="form-control customized-date-picker" value="<?php if (isset($_REQUEST['customized-from-date'])) { echo $_REQUEST['customized-from-date']; }?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label>Date To :</label>
								<div class="input-group">
									<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
									
									<input type="text" name="customized-to-date" class="form-control customized-date-picker" value="<?php if (isset($_REQUEST['customized-to-date'])) { echo $_REQUEST['customized-to-date']; }?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
								</div>
							</div>
						</div>
						<div class="row main-section-row">
							<div class="col-md-6">
								<label>Filter By :</label>
								<select id="filter-by" class="customized-selectbox-without-all" name="filter-by">
									<option value="Select All">All Clients</option>
									<option value="Manager - Client Service">CS Manager</option>
									<option value="Inside Sales">Inside Sales</option>
									<option value="Inside Post Sales">Inside Post Sales</option>
									<option value="OnSite Sales Person">OnSite Sales</option>
									<option value="OnSite Post Sales">OnSite Post Sales</option>
								</select>
							</div>
							<div class="col-md-6">
								<label>Select Personnel :</label>
								<select id="personnel-list" class="customized-selectbox-with-all" name="personnel-list">
								</select>
							</div>
						</div>
						<div class="row main-section-row">
							<div class="col-md-12">
								<label>Select Client :</label>
								<select id="client-list" class="customized-selectbox-with-all" name="client-list[]" multiple required>
									<?php
										$clientList = catsClientList($catsConn);
										foreach ($clientList as $clientKey => $clientValue) {
											echo "<option value='".$clientValue['id']."'>".$clientValue['name']."</option>";
										}
									?>
								</select>
							</div>
						</div>
						<div class="row" style="margin-top: 15px;">
							<div class="col-md-12">
								<div class="this-month-div">
									<input name="this-month-input" type="checkbox" id="this-month-id" <?php if (isset($_REQUEST['this-month-input'])) { echo 'checked'; } ?>>
									<label for="this-month-id"> Only Candidate Started This Year <?php echo "(".date("Y").")"; ?></label>
								</div>
							</div>
						</div>
						<div class="row" style="margin-top: 20px;">
							<div class="col-md-4">
								<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Home</button>
							</div>
							<div class="col-md-4">
								<button type="submit" name="form-submit-button" class="form-control form-submit-button"><i class="fa fa-search"></i> Search</button>
							</div>
							<div class="col-md-4">
								<button type="button" onclick="location.href='<?php echo REPORT_PATH ?>/performance/client_performance_report/settings.php'" class="form-control dark-button form-control"><i class="fa fa-cog fa-spin fa-1x fa-fw"></i> Setting</button>
							</div>
						</div>
					<?php
						if (isset($_REQUEST["form-submit-button"])) {
					?>
						<div class="row" style="margin-top: 20px;">
							<div class="col-md-6">
								<button type="button" data-title = "Dip Client" class="dark-button form-control optimization-matrix-dip-client">Dip Clients(<?php echo count($listOfDipClient); ?>)</button>
							</div>
							<div class="col-md-6">
								<button type="button" data-title = "Dull Client" class="dark-button form-control optimization-matrix-dull-client">Dull Clients(<?php echo count($listOfDullClient); ?>)</button>
							</div>
						</div>
					<?php
						}
					?>
					</div>
					<div class="col-md-4">
						<div class="row main-section-row">
							<div class="col-md-6">
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	if (isset($_REQUEST["form-submit-button"])) {
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th rowspan="3">Client</th>
								<th rowspan="3">Client Manager</th>
								<th rowspan="3">PS Personnel</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th rowspan="3">Months</th>
							<?php } ?>
								<th colspan="14">Total No. of</th>
								<th rowspan="3" data-toggle="tooltip" data-placement="top" title="Total GP / Resources Working">Average Margin</th>
								<th rowspan="3" data-toggle="tooltip" data-placement="top" title="Based on Resources Working">Total GP<br>(Per-Hour)</th>
								<th rowspan="3">Rank</th>
							</tr>
							<tr class="thead-tr-style">
								<th colspan="3">Assigned</th>
								<th colspan="2">Unanswered</th>
								<th rowspan="2">Submission</th>
								<th rowspan="2">Interview</th>
								<th rowspan="2">Interview Decline</th>
								<th rowspan="2">Offer</th>
								<th rowspan="2">Placed</th>
								<th rowspan="2">Extension</th>
								<th rowspan="2">Delivery Failed</th>
								<th rowspan="2">Termination</th>
								<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Candidate Working">Resources Working</th>
							</tr>
							<tr class="thead-tr-style">
								<th data-toggle="tooltip" data-placement="top" title="Total Canceled Joborder">Cancelled</th>
								<th data-toggle="tooltip" data-placement="top" title="Total Joborder">Joborder</th>
								<th data-toggle="tooltip" data-placement="top" title="Total Openings">Openings</th>
								<th data-toggle="tooltip" data-placement="top" title="Joborder which has Zero Submission!">Joborder</th>
								<th data-toggle="tooltip" data-placement="top" title="Openings which has Zero Submission!">Openings</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($mainTableData as $mainTableDataKey => $mainTableDataValue) {
							?>
							<tr class="tbody-tr-style">
								<td>
								<?php
									echo $mainTableDataValue["company_name"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["company_manager"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["ps_personnel"];
								?>
								</td>
							<?php
								if (isset($_REQUEST["customized-multiple-month"])) {
							?>
								<td>
								<?php
									echo $mainTableDataValue["given_month"];
								?>
								</td>
							<?php
								}
							?>
								<td>
								<?php
									echo $mainTableDataValue["total_canceled_job"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_job"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_openings"];
								?>
								</td>
								<td>
								<?php
									if ($mainTableDataValue["total_unanswered_job"] != 0) {
								?>
									<a class="joborder-detail-popup hyper-link-text" data-popup="<?php echo $mainTableDataValue['joborder_detail_popup']; ?>">
								<?php
									echo $mainTableDataValue["total_unanswered_job"];
								?>
									</a>
								<?php
									} else {
										echo $mainTableDataValue["total_unanswered_job"];
									}
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_unanswered_job_openings"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_submission"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_interview"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_interview_decline"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_offer"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_placed"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_extension"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_failed_delivery"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["total_termination"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["resources_working"];
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["average_actual_margin"];
								?>
								</td>
								<td>
								<?php
									if ($user == "1" || $user == "3") {
								?>
									<a class="margin-detail-popup" data-popup="<?php echo $mainTableDataValue['margin_detail_popup']; ?>" data-titletype="Actual">
								<?php
									echo $mainTableDataValue["final_actual_margin"];
								?>
									</a>
								<?php
									} else {
										echo $mainTableDataValue["final_actual_margin"];
									}
								?>
								</td>
								<td style="color: #000;background-color: <?php echo $mainTableDataValue["optimization_matrix_rank"]["optimization_matrix_color"]; ?>">
								<?php
									echo $mainTableDataValue["optimization_matrix_rank"]["optimization_matrix_title"]; ?>
								</td>
							</tr>
							<?php
								}
							?>
						</tbody>
						<tfoot>
							<tr class="tfoot-tr-style">
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th colspan="4"></th>
							<?php } else { ?>
								<th colspan="3"></th>
							<?php } ?>
								<th><?php echo array_sum(array_column($mainTableData, "total_canceled_job")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_job")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_openings")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_unanswered_job")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_unanswered_job_openings")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_submission")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_interview")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_interview_decline")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_offer")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_placed")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_extension")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_failed_delivery")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "total_termination")); ?></th>
								<th><?php echo array_sum(array_column($mainTableData, "resources_working")); ?></th>
								<th></th>
								<th><?php echo array_sum(array_column($mainTableData, "final_actual_margin")); ?></th>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</section>
<?php
	}
?>
</body>

<script>
	$(document).ready(function(){
		$(".loading-image").hide();
		$(".main-section").removeClass("hidden");
		$(".customized-datatable-section").removeClass("hidden");

        $(".customized-multiple-month").datepicker({
            format: "mm/yyyy",
            startView: 1,
            minViewMode: 1,
            maxViewMode: 2,
            clearBtn: true,
            multidate: true,
            orientation: "top",
            autoclose: false
        });

        $(".customized-date-picker").datepicker({
            todayHighlight: true,
            clearBtn: true,
            orientation: "top",
            autoclose: true
        });

        $(".customized-selectbox-without-all").multiselect({
            nonSelectedText: "Select Option",
            numberDisplayed: 1,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: "100%",
            includeSelectAllOption: true,
            maxHeight: 200
        });

        $(".customized-selectbox-with-all").multiselect({
            nonSelectedText: "Select Option",
            numberDisplayed: 1,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: "100%",
            includeSelectAllOption: true,
            maxHeight: 200
        });
		$(".customized-selectbox-with-all").multiselect("selectAll", false);
		$(".customized-selectbox-with-all").multiselect("updateButtonText");

		var customizedDataTable = $(".customized-datatable").DataTable({
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		    dom: "Bfrtip",
		    "aaSorting": [[0,"asc"]],
	        buttons:[
	            "excel","pageLength"
	        ],
	        initComplete: function(){
	        	$("div.dataTables_filter input").css("width","250")
			}
		});
		customizedDataTable.button(0).nodes().css("background", "#2266AA");
		customizedDataTable.button(0).nodes().css("border", "#2266AA");
		customizedDataTable.button(0).nodes().css("color", "#fff");
		customizedDataTable.button(0).nodes().html("Download Report");
		customizedDataTable.button(1).nodes().css("background", "#449D44");
		customizedDataTable.button(1).nodes().css("border", "#449D44");
		customizedDataTable.button(1).nodes().css("color", "#fff");
	});

	$(document).on("click", ".months-button", function(e){
		e.preventDefault();
		$(".customized-multiple-month").prop("required", true);
		$(".customized-date-picker").prop("required", false);
		$(".customized-multiple-month").prop("disabled", false);
		$(".customized-date-picker").prop("disabled", true);
		$(".date-range-input").addClass("hidden");
		$(".multiple-month-input").removeClass("hidden");
		$(".months-button").addClass("dark-button");
		$(".months-button").removeClass("smooth-button");
		$(".date-range-button").addClass("smooth-button");
		$(".date-range-button").removeClass("dark-button");
	});

	$(document).on("click", ".date-range-button", function(e){
		e.preventDefault();
		$(".customized-date-picker").prop("required", true);
		$(".customized-multiple-month").prop("required", false);
		$(".customized-date-picker").prop("disabled", false);
		$(".customized-multiple-month").prop("disabled", true);
		$(".date-range-input").removeClass("hidden");
		$(".multiple-month-input").addClass("hidden");
		$(".months-button").addClass("smooth-button");
		$(".months-button").removeClass("dark-button");
		$(".date-range-button").addClass("dark-button");
		$(".date-range-button").removeClass("smooth-button");
	});

	$(document).on("change", "#filter-by", function(e){
		e.preventDefault();
		if ($("#filter-by").val() == "Select All") {
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/search-client-by-personnel.php",
				data: "item="+$("#filter-by").val(),
				success:function(response){
					$("#client-list").html(response);
					$("#client-list").multiselect("destroy");
					$("#client-list").multiselect({
			            nonSelectedText: "Select Option",
			            numberDisplayed: 1,
			            enableFiltering: true,
			            enableCaseInsensitiveFiltering: true,
			            buttonWidth: "100%",
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
					$("#client-list").multiselect("selectAll", false);
					$("#client-list").multiselect("updateButtonText");

					$("#personnel-list").html("");
					$("#personnel-list").multiselect("destroy");
					$("#personnel-list").multiselect({
			            nonSelectedText: "Select Option",
			            numberDisplayed: 1,
			            enableFiltering: true,
			            enableCaseInsensitiveFiltering: true,
			            buttonWidth: "100%",
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
				}
			});
		} else {
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/search-extra-field-personnel.php",
				data: "fieldName="+$("#filter-by").val(),
				success:function(response){
					$("#personnel-list").html(response);
					$("#personnel-list").multiselect("destroy");
					$("#personnel-list").multiselect({
			            nonSelectedText: "Select Option",
			            numberDisplayed: 1,
			            enableFiltering: true,
			            enableCaseInsensitiveFiltering: true,
			            buttonWidth: "100%",
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
				}
			});
		}
	});

	$(document).on("change", "#personnel-list", function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/search-client-by-personnel.php",
			data: "personnel="+$("#personnel-list").val()+"&type="+$("#filter-by").val(),
			success:function(response){
				$("#client-list").html(response);
				$("#client-list").multiselect("destroy");
				$("#client-list").multiselect({
		            nonSelectedText: "Select Option",
		            numberDisplayed: 1,
		            enableFiltering: true,
		            enableCaseInsensitiveFiltering: true,
		            buttonWidth: "100%",
		            includeSelectAllOption: true,
		            maxHeight: 200
		        });
				$("#client-list").multiselect("selectAll", false);
				$("#client-list").multiselect("updateButtonText");
			}
		});
	});

	$(document).on("click", ".margin-detail-popup", function(e){
		e.preventDefault();
		var titleType = $(this).data("titletype");
		$("#divLoading").addClass("show");
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/margin-detail-view-popup.php",
			data: "titleType="+titleType+"&"+$(this).data("popup"),
			success: function(response) {
				$("#divLoading").removeClass("show");
				if (titleType == "Actual") {
					$(".modal-header").css("background-color", "#2266AA");
				}
				$(".modal-title-type").html(titleType);
				$(".view-margin-detail").modal("show");
				$(".margin-table-section").html("");
				$(".margin-table-section").html(response);
				$(".scrollable-datatable").DataTable();
			}
		});
	});

	$(document).on("click", ".joborder-detail-popup", function(e){
		e.preventDefault();
		$("#divLoading").addClass("show");
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/joborder-detail-popup.php",
			data: $(this).data("popup"),
			success: function(response) {
				$("#divLoading").removeClass("show");
				$(".view-joborder-detail-popup").modal("show");
				$(".margin-table-section").html("");
				$(".margin-table-section").html(response);
				$(".scrollable-datatable").DataTable();
			}
		});
	});

	$(document).on("click", ".optimization-matrix-client-info", function(e){
		e.preventDefault();

		let selectedTitle = $(this).data("title");
		let selectedColor = $(this).data("color");

		let jsonData = <?php echo json_encode($optimizationMatrixClientInfo); ?>;

		$(".view-optimization-matrix-client-info .modal-header").css("color", "#000");
		$(".view-optimization-matrix-client-info .modal-header").css("background-color", selectedColor);

		$(".view-optimization-matrix-client-info .modal-title").text(selectedTitle);

		$(".view-optimization-matrix-client-info .modal-body .col-md-12").html("");

		console.log(jsonData);
		var counter = 0;
		$.each(jsonData, function (index, value) {
			if (index == selectedTitle) {
				var $table = $( "<table class='table table-striped table-bordered customized-datatable'><tr><th>No</th><th>Client Name</th></tr></table>");
				for (var i = 0; i < value.company_name.length; i++) {
					var slNo = ++counter;
				    var comapny = value.company_name[i];
				    var $line = $( "<tr></tr>" );
				    $line.append( $("<td></td>").html(slNo));
				     $line.append( $("<td></td>").html(comapny));
				    $table.append($line);
				}
			$(".view-optimization-matrix-client-info .modal-body .col-md-12").append($table);
			}
		});

		$(".view-optimization-matrix-client-info").modal("show");
	});

	$(document).on("click", ".optimization-matrix-dip-client", function(e){
		e.preventDefault();
		let jsonData = <?php echo json_encode($listOfDipClient); ?>;
		let selectedTitle = $(this).data("title");
		$(".view-dip-client .modal-body .col-md-12").html("");
		var table = $( "<table class='table table-striped table-bordered customized-datatable'><tr></tr><tr></tr></table>");
			$.each(jsonData, function (index, value) {
				var line = $( "<tr></tr>" );
				line.append( $("<td rowspan='2'></td>").html(value.clientName));
				var month = value.month;
				for (var i = 0; i < value.month.length; i++) {
				
					line.append( $("<td></td>").html(value.month[i]));
				}
				lineTwo = $( "<tr></tr>" );
				for (var i = 0; i < value.rank.length; i++) {
					lineTwo.append( $("<td></td>").html(value.rank[i]));
				}
				table.append(line);
				table.append(lineTwo);
			});
			if (jsonData != null) {
				$(".view-dip-client .modal-title").text(selectedTitle);
				$(".view-dip-client .modal-body .col-md-12").html(table);
				$(".view-dip-client").modal("show"); 	
			}
		});

	$(document).on("click", ".optimization-matrix-dull-client", function(e){
		e.preventDefault();
		let jsonData = <?php echo json_encode($listOfDullClient); ?>;
		let selectedTitle = $(this).data("title");
		$(".view-dull-client .modal-body .col-md-12").html("");
		var table = $( "<table class='table table-striped table-bordered customized-datatable'><tr></tr><tr></tr></table>");
		$.each(jsonData, function (index, value) {
			var line = $( "<tr></tr>" );
			line.append( $("<td rowspan='2'></td>").html(value.clientName));
			for (var i = 0; i < value.month.length; i++) {
			
				line.append( $("<td></td>").html(value.month[i]));
			}
			lineTwo = $( "<tr></tr>" );
			for (var i = 0; i < value.rank.length; i++) {
			
				lineTwo.append( $("<td></td>").html(value.rank[i]));
			}
			table.append(line);
			table.append(lineTwo);
		});
		if (jsonData != null) {
			$(".view-dull-client .modal-title").text(selectedTitle);
			$(".view-dull-client .modal-body .col-md-12").html(table);
			$(".view-dull-client").modal("show");
		}
	});
</script>
</html>
<?php
		} else {
			if ($userMember == "Admin") {
				header("Location:../../../admin.php");
			} elseif ($userMember == "User") {
				header("Location:../../../user.php");
			} else {
				header("Location:../../../index.php");
			}
		}
    } else {
        header("Location:../../../index.php");
    }
?>
