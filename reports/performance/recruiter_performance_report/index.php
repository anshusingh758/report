<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "1";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Recruiter Performance Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
			padding: 4px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td {
			padding: 2px 1px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td:nth-child(1) {
			text-align: left;
		}
		table.dataTable thead tr:nth-child(2) th:last-child,
		table.dataTable thead tr:nth-child(3) th:last-child {
			border-right: 1px solid #ddd;
		}
		table.dataTable tfoot tr:nth-child(2) th {
			text-align: left;
			vertical-align: middle;
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
		.thead-tr-style {
			background-color: #ccc;
			color: #000;
			font-size: 12px;
		}
		.tbody-tr-style {
			color: #333;
			font-size: 12px;
		}
		.tfoot-tr-style {
			background-color: #ccc;
			color: #000;
			font-size: 12px;
		}
		.view-unanswered-joborder-detail-popup .modal-lg {
			width: calc(100% - 100px);
		}
		.view-unanswered-joborder-detail-popup .modal-header {
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
	</style>
</head>
<body>

	<div id="divLoading"></div>

	<?php include_once("../../../popups.php"); ?>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">Recruiter Performance Report</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container">
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

			<form action="index.php" method="post">
				<div class="row main-section-row multiple-month-input">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Months:</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							
							<input type="text" name="customized-multiple-month" class="form-control customized-multiple-month" value="<?php if (isset($_REQUEST['customized-multiple-month'])) { echo $_REQUEST['customized-multiple-month']; } ?>" placeholder="MM/YYYY" autocomplete="off" required>
							
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						</div>
					</div>
				</div>
				<div class="row main-section-row date-range-input hidden">
					<div class="col-md-2 col-md-offset-4">
						<label>Date From :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							
							<input type="text" name="customized-from-date" class="form-control customized-date-picker" value="<?php if (isset($_REQUEST['customized-from-date'])) { echo $_REQUEST['customized-from-date']; }?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
						</div>
					</div>
					<div class="col-md-2">
						<label>Date To :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							
							<input type="text" name="customized-to-date" class="form-control customized-date-picker" value="<?php if (isset($_REQUEST['customized-to-date'])) { echo $_REQUEST['customized-to-date']; }?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
						</div>
					</div>
				</div>
				<div class="row main-section-row">
					<div class="col-md-2 col-md-offset-4">
						<label>Select Manager :</label>
						<select id="manager-list" class="customized-selectbox-without-all" name="manager-list">
							<option value="">Select Option</option>
							<?php
								$personnelList = catsExtraFieldPersonnelList($catsConn,"Manager - Client Service");
								sort($personnelList);
								foreach ($personnelList as $personnelKey => $personnelValue) {
									echo "<option value='".$personnelValue."'>".ucwords($personnelValue)."</option>";
								}
							?>
						</select>
					</div>
					<div class="col-md-2">
						<label>Select Recruiter :</label>
						<select id="recruiter-list" class="customized-selectbox-with-all" name="recruiter-list[]" multiple required>
							<?php
								$nullVariable = "";
								$recruiterList = catsRecruiterList($catsConn,$nullVariable);
								foreach ($recruiterList as $recruiterKey => $recruiterValue) {
									echo "<option value='".$recruiterValue['id']."'>".$recruiterValue['name']." - ".$recruiterValue['status']."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row main-section-submit-row">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back to Home</button>
					</div>
					<div class="col-md-2">
						<button type="submit" name="form-submit-button" class="form-control form-submit-button"><i class="fa fa-search"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	if (isset($_REQUEST["form-submit-button"])) {
		$fromDate = $toDate = array();
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
		$recruiterData = "'".implode("', '",$_REQUEST['recruiter-list'])."'";
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th rowspan="3">Recruiter</th>
								<th rowspan="3">CS Manager</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th rowspan="3">Months</th>
							<?php } ?>
								<th colspan="13">Total No. of</th>
							<?php if ($user == "1" || $user == "3") { ?>
								<th rowspan="3" data-toggle="tooltip" data-placement="top" title="Total GP / Resources Working">Average Margin</th>
								<th rowspan="3" data-toggle="tooltip" data-placement="top" title="Based on Resources Working">Total GP<br>(Per-Hour)</th>
							<?php } ?>
							</tr>
							<tr class="thead-tr-style">
								<th colspan="2">Assigned</th>
								<th colspan="3">Unanswered</th>
								<th rowspan="2">Submission</th>
								<th rowspan="2">Interview</th>
								<th rowspan="2">Interview Decline</th>
								<th rowspan="2">Offer</th>
								<th rowspan="2">Placed</th>
								<th rowspan="2">Extension</th>
								<th rowspan="2">Delivery Failed</th>
								<th rowspan="2" data-toggle="tooltip" data-placement="top" title="Candidate Working">Resources Working</th>
							</tr>
							<tr class="thead-tr-style">
								<th data-toggle="tooltip" data-placement="top" title="Total Joborder">Joborder</th>
								<th data-toggle="tooltip" data-placement="top" title="Total Openings">Openings</th>
								<th data-toggle="tooltip" data-placement="top" title="Joborder which has Zero Submission!">Joborder</th>
								<th data-toggle="tooltip" data-placement="top" title="Openings which has Zero Submission!">Openings</th>
								<th>On Hold - Internal</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$totalJob = $totalOpenings = $totalUnansweredJob = $totalUnansweredJobOpenings = $totalOnholdInternal = $totalSubmission = $totalInterview = $totalInterviewDecline = $totalOffer = $totalPlaced = $totalExtension = $totalFailedDelivery = $resourcesWorking = $finalActualMargin = array();

								$taxSettingsTableData = taxSettingsTable($allConn);

								foreach ($fromDate as $fromDateKey => $fromDateValue) {
									$startDate = $fromDate[$fromDateKey];
									$endDate = $toDate[$fromDateKey];

									$givenMonth = date("m/Y", strtotime($startDate));

									$mainQUERY = "SELECT
										a.recruiter_id,
										a.recruiter_name,
										a.manager,
										b.total_submission,
										b.total_interview,
										b.total_interview_decline,
										b.total_offer,
										b.total_failed_delivery,
										c.total_placed,
										d.total_extension,
										g.resources_working,
										h.total_job,
										h.total_openings,
										h.total_unanswered_job,
										h.total_unanswered_job_openings,
										h.total_onhold_internal
									FROM
									(SELECT
										u.user_id AS recruiter_id,
										CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
										notes AS manager
									FROM
										user AS u
									WHERE
										u.user_id IN ($recruiterData)
									GROUP BY u.user_id) AS a
									LEFT OUTER JOIN
									(SELECT
										u.user_id AS recruiter_id,
									    COUNT(CASE WHEN cjsh.status_to = '400' THEN 1 END) AS total_submission,
									    COUNT(CASE WHEN cjsh.status_to = '500' THEN 1 END) AS total_interview,
									    COUNT(CASE WHEN cjsh.status_to = '560' THEN 1 END) AS total_interview_decline,
									    COUNT(CASE WHEN cjsh.status_to = '600' THEN 1 END) AS total_offer,
									    COUNT(CASE WHEN cjsh.status_to = '900' THEN 1 END) AS total_failed_delivery
									FROM
										candidate_joborder_status_history AS cjsh
									    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									    JOIN user AS u ON u.user_id = cj.added_by
									WHERE
										cj.added_by IN ($recruiterData)
									AND
										date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY u.user_id) AS b ON b.recruiter_id = a.recruiter_id
									LEFT OUTER JOIN
									(SELECT
										u.user_id AS recruiter_id,
									    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
									FROM
										candidate_joborder_status_history AS cjsh
									    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									    JOIN user AS u ON u.user_id = cj.added_by
									WHERE
										cjsh.status_to = '800'
									AND
										date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									AND
										cj.added_by IN ($recruiterData)
									AND
										cjsh.candidate_id NOT IN (SELECT
									    cjsh.candidate_id
									FROM
										candidate_joborder_status_history AS cjsh
									    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									    JOIN user AS u ON u.user_id = cj.added_by
									WHERE
										cjsh.status_to = '620'
									AND
										cj.added_by IN ($recruiterData)
									AND
										date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
									GROUP BY u.user_id) AS c ON c.recruiter_id = a.recruiter_id
									LEFT OUTER JOIN
									(SELECT
										u.user_id AS recruiter_id,
									    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_extension
									FROM
										candidate_joborder_status_history AS cjsh
									    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									    JOIN user AS u ON u.user_id = cj.added_by
									WHERE
										cjsh.status_to = '620'
									AND
										cj.added_by IN ($recruiterData)
									AND
										date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY u.user_id) AS d ON d.recruiter_id = a.recruiter_id
									LEFT OUTER JOIN
									(SELECT
										u.user_id AS recruiter_id,
										COUNT(DISTINCT e.id) AS resources_working
									FROM
										user AS u
										JOIN vtech_mappingdb.system_integration AS si ON u.user_id = si.c_recruiter_id
										JOIN vtechhrm.employees AS e ON si.h_employee_id = e.id
										JOIN vtechhrm.employeeprojects AS ep ON e.id = ep.employee
										JOIN vtechhrm.employeetimeentry AS ete ON e.id = ete.employee
									WHERE
										u.user_id IN ($recruiterData)
									AND
										ep.project != '6'
									AND
										date_format(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY u.user_id) AS g ON g.recruiter_id = a.recruiter_id
									LEFT OUTER JOIN
									(SELECT
										u.user_id AS recruiter_id,
										COUNT(DISTINCT job.joborder_id) AS total_job,
										SUM(job.openings) AS total_openings,
									    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, 1)) AS total_unanswered_job,
									    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, job.openings)) AS total_unanswered_job_openings,
									    COUNT(DISTINCT CASE WHEN job.status = 'On Hold - Internal' THEN job.joborder_id END) AS total_onhold_internal
									FROM
										cats.user AS u
										LEFT JOIN cats.joborder AS job ON job.recruiter = u.user_id
									WHERE
										u.user_id IN ($recruiterData)
									AND
										date_format(job.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY u.user_id) AS h ON h.recruiter_id = a.recruiter_id
									WHERE
										(b.total_submission != '' OR b.total_interview != '' OR b.total_interview_decline != '' OR b.total_offer != '' OR b.total_failed_delivery != '' OR c.total_placed != '' OR d.total_extension != '' OR g.resources_working != '')";
									$mainRESULT = mysqli_query($catsConn, $mainQUERY);
									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {

											$recruiterId = $mainROW["recruiter_id"];
											
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
											    LEFT JOIN cats.user AS u ON u.user_id = si.c_recruiter_id
												LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
												LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
											WHERE
												u.user_id = '$recruiterId'
											AND
												ep.project != '6'
											AND
												date_format(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
											GROUP BY employee_id";
											
											if ($user == "1" || $user == "3") {
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
											}
											
											$personnelType = "recruiter";

											$dataPopup = "personnel_id=".$mainROW["recruiter_id"]."&personnel_name=".ucwords($mainROW["recruiter_name"])."&personnel_type=".$personnelType."&start_date=".$startDate."&end_date=".$endDate;

							?>
							<tr class="tbody-tr-style">
								<td><?php echo ucwords($mainROW["recruiter_name"]); ?></td>
								<td><?php echo ucwords($mainROW["manager"]); ?></td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td><?php echo $givenMonth; ?></td>
							<?php } ?>
								<td>
								<?php
									if ($mainROW["total_job"] != "") {
										echo $totalJob[] = $mainROW["total_job"];
									} else {
										echo $totalJob[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_openings"] != "") {
										echo $totalOpenings[] = $mainROW["total_openings"];
									} else {
										echo $totalOpenings[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_unanswered_job"] != "" && $mainROW["total_unanswered_job"] != 0) {
								?>
									<a class="joborder-detail-popup hyper-link-text" data-popup="<?php echo $dataPopup; ?>">
								<?php
										echo $totalUnansweredJob[] = $mainROW["total_unanswered_job"];
								?>
									</a>
								<?php
									} else {
										echo $totalUnansweredJob[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_unanswered_job_openings"] != "") {
										echo $totalUnansweredJobOpenings[] = $mainROW["total_unanswered_job_openings"];
									} else {
										echo $totalUnansweredJobOpenings[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_onhold_internal"] != "") {
										echo $totalOnholdInternal[] = $mainROW["total_onhold_internal"];
									} else {
										echo $totalOnholdInternal[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_submission"] != "") {
										echo $totalSubmission[] = $mainROW["total_submission"];
									} else {
										echo $totalSubmission[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_interview"] != "") {
										echo $totalInterview[] = $mainROW["total_interview"];
									} else {
										echo $totalInterview[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_interview_decline"] != "") {
										echo $totalInterviewDecline[] = $mainROW["total_interview_decline"];
									} else {
										echo $totalInterviewDecline[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_offer"] != "") {
										echo $totalOffer[] = $mainROW["total_offer"];
									} else {
										echo $totalOffer[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_placed"] != "") {
										echo $totalPlaced[] = $mainROW["total_placed"];
									} else {
										echo $totalPlaced[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_extension"] != "") {
										echo $totalExtension[] = $mainROW["total_extension"];
									} else {
										echo $totalExtension[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_failed_delivery"] != "") {
										echo $totalFailedDelivery[] = $mainROW["total_failed_delivery"];
									} else {
										echo $totalFailedDelivery[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["resources_working"] != "") {
										echo $resourcesWorking[] = $mainROW["resources_working"];
									} else {
										echo $resourcesWorking[] = "0";
									}
								?>
								</td>
							<?php if ($user == "1" || $user == "3") { ?>
								<td><?php echo round(array_sum($grossMargin) / $mainROW["resources_working"], 2); ?></td>
								<td><?php echo $finalActualMargin[] = round(array_sum($grossMargin), 2); ?></td>
							<?php } ?>
							</tr>
							<?php
										}
									}
								}
							?>
						</tbody>
						<tfoot>
							<tr class="tfoot-tr-style">
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th colspan="3"></th>
							<?php } else { ?>
								<th colspan="2"></th>
							<?php } ?>
								<th><?php echo array_sum($totalJob); ?></th>
								<th><?php echo array_sum($totalOpenings); ?></th>
								<th><?php echo array_sum($totalUnansweredJob); ?></th>
								<th><?php echo array_sum($totalUnansweredJobOpenings); ?></th>
								<th><?php echo array_sum($totalOnholdInternal); ?></th>
								<th><?php echo array_sum($totalSubmission); ?></th>
								<th><?php echo array_sum($totalInterview); ?></th>
								<th><?php echo array_sum($totalInterviewDecline); ?></th>
								<th><?php echo array_sum($totalOffer); ?></th>
								<th><?php echo array_sum($totalPlaced); ?></th>
								<th><?php echo array_sum($totalExtension); ?></th>
								<th><?php echo array_sum($totalFailedDelivery); ?></th>
								<th><?php echo array_sum($resourcesWorking); ?></th>
							<?php if ($user == "1" || $user == "3") { ?>
								<th></th>
								<th><?php echo array_sum($finalActualMargin); ?></th>
							<?php } ?>
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

	$(document).on("change", "#manager-list", function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/search-recruiter-by-manager.php",
			data: "manager="+$("#manager-list").val(),
			success:function(response){
				$("#recruiter-list").html(response);
				$("#recruiter-list").multiselect("destroy");
				$("#recruiter-list").multiselect({
		            nonSelectedText: "Select Option",
		            numberDisplayed: 1,
		            enableFiltering: true,
		            enableCaseInsensitiveFiltering: true,
		            buttonWidth: "100%",
		            includeSelectAllOption: true,
		            maxHeight: 200
		        });
				$("#recruiter-list").multiselect("selectAll", false);
				$("#recruiter-list").multiselect("updateButtonText");
			}
		});
	});

	$(document).on("click", ".joborder-detail-popup", function(e){
		e.preventDefault();
		$("#divLoading").addClass("show");
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/unanswered-joborder-detail-popup.php",
			data: $(this).data("popup"),
			success: function(response) {
				$("#divLoading").removeClass("show");
				$(".view-unanswered-joborder-detail-popup").modal("show");
				$(".margin-table-section").html("");
				$(".margin-table-section").html(response);
				$(".scrollable-datatable").DataTable();
			}
		});
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
