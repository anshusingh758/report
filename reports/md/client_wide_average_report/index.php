<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "17";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Client Wide Average Report</title>

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
		table.dataTable thead tr:nth-child(2) th:last-child {
			border-right: 1px solid #ddd;
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
		table.dataTable tfoot tr:nth-child(2) th {
			text-align: left;
			vertical-align: middle;
		}
		table.dataTable thead tr:nth-child(2) th:last-child,
		table.dataTable thead tr:nth-child(3) th:last-child {
			border-right: 1px solid #ddd;
		}
		.modal-header {
			color: #fff;
			font-size: 20px;
			font-weight: bold;
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
			font-size: 11px;
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
	</style>
</head>
<body>

	<div id="divLoading"></div>

	<?php include_once("../../../popups.php"); ?>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">Client Wide Average Report</div>
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
					<div class="col-md-4 col-md-offset-4">
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
					<div class="col-md-4 col-md-offset-4">
						<div class="this-month-div">
							<input name="this-month-input" type="checkbox" id="this-month-id" <?php if (isset($_REQUEST['this-month-input'])) { echo 'checked'; } ?>>
							<label for="this-month-id"> Only Candidate Started This Year <?php echo "(".date("Y").")"; ?></label>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 20px;">
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
		$clientData = "'".implode("', '",$_REQUEST['client-list'])."'";

		if (isset($_REQUEST["this-month-input"])) {
			$includePeriod = "true";
		} else {
			$includePeriod = "false";
		}

		$thisYearStartDate = date("Y")."-01-01";
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th rowspan="3">Client</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th rowspan="3">Months</th>
							<?php } ?>
								<th colspan="11">Total</th>
								<th rowspan="3" data-toggle="tooltip" data-placement="top" title="Candidate Working">Resources Working</th>
								<th rowspan="3">Bill Rate($)</th>
								<th rowspan="3">Pay C2C($)</th>
								<th rowspan="3" data-toggle="tooltip" data-placement="top" title="Actual Margin / Resources Working">Average Margin</th>
								<th rowspan="3" data-toggle="tooltip" data-placement="top" title="Based on Resources Working">Actual Margin</th>
								<th colspan="4">Total</th>
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
						<tbody>
							<?php
								$totalJob = $totalOpenings = $totalUnansweredJob = $totalUnansweredJobOpenings = $totalSubmission = $totalInterview = $totalInterviewDecline = $totalOffer = $totalPlaced = $totalExtension = $totalFailedDelivery = $totalNewJoin = $finalBillRate = $totalPayC2C = $totalTermination = $totalResourcesWorking = $finalActualMargin = $finalNewMargin = $finalLostMargin = array();

								$taxSettingsTableData = taxSettingsTable($allConn);

								foreach ($fromDate as $fromDateKey => $fromDateValue) {
									$startDate = $fromDate[$fromDateKey];
									$endDate = $toDate[$fromDateKey];

									$givenMonth = date("m/Y", strtotime($startDate));

									$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$startDate,$endDate);

									$mainQUERY = "SELECT
										a.company_id,
										a.company_name,
										a.company_manager,
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
										comp.name AS company_name,
										CONCAT(u.first_name,' ',u.last_name) AS company_manager
									FROM
										company AS comp
										LEFT JOIN user AS u ON u.user_id = comp.owner
									WHERE
										comp.company_id IN ($clientData)
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
									    LEFT JOIN joborder AS job ON cjsh.joborder_id = job.joborder_id
										LEFT JOIN company AS comp ON job.company_id = comp.company_id
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
									    LEFT JOIN joborder AS job ON cjsh.joborder_id = job.joborder_id
										LEFT JOIN company AS comp ON job.company_id = comp.company_id
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
									    LEFT JOIN joborder AS job ON cjsh.joborder_id = job.joborder_id
										LEFT JOIN company AS comp ON job.company_id = comp.company_id
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
									    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, 1)) AS total_unanswered_job,
									    SUM(IF((SELECT COUNT(cjsh.candidate_joborder_status_history_id) FROM cats.candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to = '400' AND DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate') > 0, 0, job.openings)) AS total_unanswered_job_openings
									FROM
										company AS comp
										LEFT JOIN joborder AS job ON comp.company_id =job.company_id
									WHERE
										comp.company_id IN ($clientData)
									AND
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
										vtechhrm.employees AS e
										LEFT JOIN vtechhrm.employeeprojects AS ep ON e.id = ep.employee
										LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
									    LEFT JOIN vtech_mappingdb.system_integration AS si ON e.id = si.h_employee_id
										LEFT JOIN cats.company AS comp ON si.c_company_id = comp.company_id
									WHERE
										comp.company_id IN ($clientData)
									AND
										ep.project != '6'
									AND
										IF('$includePeriod' = 'true', (DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDate') AND (DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'), (DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'))
									GROUP BY comp.company_id) AS g ON g.company_id = a.company_id
									LEFT JOIN
									(SELECT
										comp.company_id,
										COUNT(DISTINCT e.id) AS new_join
									FROM 
										company AS comp
										LEFT JOIN vtech_mappingdb.system_integration AS si ON comp.company_id = si.c_company_id
										LEFT JOIN vtechhrm.employees AS e ON si.h_employee_id = e.id
									WHERE
										comp.company_id IN ($clientData)
									AND
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
												e.status IN ('Terminated','Termination In_Vol','Termination Vol')
											AND
												ep.project != '6'
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
											
											if ($includePeriod == "true") {
												$actualMarginDataPopUp = "objectType=Client&objectId=".$mainROW["company_id"]."&objectName=".$mainROW["company_name"]."&startDate=".$thisYearStartDate."&endDate=".$endDate;
											} else {
												$actualMarginDataPopUp = "objectType=Client&objectId=".$mainROW["company_id"]."&objectName=".$mainROW["company_name"]."&startDate=".$startDate."&endDate=".$endDate;
											}
											
											$dataPopUp = "objectType=Client&objectId=".$mainROW["company_id"]."&objectName=".$mainROW["company_name"]."&startDate=".$startDate."&endDate=".$endDate;
											
											$personnelType = "client";
											
											$personnelManager = "";
											
											if ($mainROW["company_manager"] != "") {
												$personnelManager = " (".$mainROW["company_manager"].")";
											}

											$jobDataPopup = "personnel_id=".$mainROW["company_id"]."&personnel_name=".$mainROW["company_name"]."".$personnelManager."&personnel_type=".$personnelType."&start_date=".$startDate."&end_date=".$endDate;

							?>
							<tr class="tbody-tr-style">
								<td><?php echo $mainROW["company_name"]; ?></td>
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
									<a class="joborder-detail-popup hyper-link-text" data-popup="<?php echo $jobDataPopup; ?>">
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
										echo $totalResourcesWorking[] = $mainROW["resources_working"];
									} else {
										echo $totalResourcesWorking[] = "0";
									}
								?>
								</td>
								<td><?php echo $finalBillRate[] = array_sum($totalBillRate); ?></td>
								<td><?php echo $totalPayC2C[] = array_sum($payC2C); ?></td>
								<td><?php echo round(array_sum($actualMargin) / $mainROW["resources_working"], 2); ?></td>
								<td>
								<?php
									if ($user == "1" || $user == "3") {
								?>
									<a class="margin-detail-popup" data-popup="<?php echo $actualMarginDataPopUp; ?>" data-titletype="Actual"><?php echo $finalActualMargin[] = round(array_sum($actualMargin), 2); ?></a>
								<?php
									} else {
										echo $finalActualMargin[] = round(array_sum($actualMargin), 2);
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["new_join"] != "") {
										echo $totalNewJoin[] = $mainROW["new_join"];
									} else {
										echo $totalNewJoin[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($user == "1" || $user == "3") {
								?>
									<a class="margin-detail-popup" data-popup="<?php echo $dataPopUp; ?>" data-titletype="New"><?php echo $finalNewMargin[] = round(array_sum($newMargin), 2); ?></a>
								<?php
									} else {
										echo $finalNewMargin[] = round(array_sum($newMargin), 2);
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_termination"] != "") {
										echo $totalTermination[] = $mainROW["total_termination"];
									} else {
										echo $totalTermination[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($user == "1" || $user == "3") {
								?>
									<a class="margin-detail-popup" data-popup="<?php echo $dataPopUp; ?>" data-titletype="Lost"><?php echo $finalLostMargin[] = round(array_sum($lostMargin), 2); ?></a>
								<?php
									} else {
										echo $finalLostMargin[] = round(array_sum($lostMargin), 2);
									}
								?>
								</td>
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
								<th colspan="2"></th>
							<?php } else { ?>
								<th></th>
							<?php } ?>
								<th><?php echo array_sum($totalJob); ?></th>
								<th><?php echo array_sum($totalOpenings); ?></th>
								<th><?php echo array_sum($totalUnansweredJob); ?></th>
								<th><?php echo array_sum($totalUnansweredJobOpenings); ?></th>
								<th><?php echo array_sum($totalSubmission); ?></th>
								<th><?php echo array_sum($totalInterview); ?></th>
								<th><?php echo array_sum($totalInterviewDecline); ?></th>
								<th><?php echo array_sum($totalOffer); ?></th>
								<th><?php echo array_sum($totalPlaced); ?></th>
								<th><?php echo array_sum($totalExtension); ?></th>
								<th><?php echo array_sum($totalFailedDelivery); ?></th>
								<th><?php echo array_sum($totalResourcesWorking); ?></th>
								<th><?php echo array_sum($finalBillRate); ?></th>
								<th><?php echo array_sum($totalPayC2C); ?></th>
								<th></th>
								<th><?php echo array_sum($finalActualMargin); ?></th>
								<th><?php echo array_sum($totalNewJoin); ?></th>
								<th><?php echo array_sum($finalNewMargin); ?></th>
								<th><?php echo array_sum($totalTermination); ?></th>
								<th><?php echo array_sum($finalLostMargin); ?></th>
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
				if (titleType == "New") {
					$(".modal-header").css("background-color", "#449D44");
				}
				if (titleType == "Lost") {
					$(".modal-header").css("background-color", "#FF0000");
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
