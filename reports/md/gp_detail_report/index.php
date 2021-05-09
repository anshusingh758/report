<?php
	$responseArray = array();
	$responseType = isset($_REQUEST["response_type"]) && $_REQUEST["response_type"] == 1 ? 1 : 0;

	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "48";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
			if ($responseType == 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>GP Detail Report</title>

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
			font-size: 13px;
		}
	</style>
</head>
<body>

	<?php include_once("../../../popups.php"); ?>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">GP Detail Report</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container">
			<div class="row">
				<form action="index.php" method="post">
					<div class="col-md-2">
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back to Home</button>
					</div>
					<div class="col-md-3 col-md-offset-2">
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							
							<input type="text" name="customized-multiple-month" class="form-control customized-multiple-month" value="<?php if (isset($_REQUEST['customized-multiple-month'])) { echo $_REQUEST['customized-multiple-month']; } ?>" placeholder="Select Month" autocomplete="off" required>
							
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						</div>
					</div>
					<div class="col-md-1">
						<button type="submit" name="form-submit-button" class="form-control form-submit-button">Search</button>
					</div>
					<div class="col-md-4">
						<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
					</div>
				</form>
			</div>
		</div>
	</section>

<?php
	}
	if (isset($_REQUEST["form-submit-button"])) {
		$dateGiven = explode("/", $_REQUEST["customized-multiple-month"]);
		$dateModified = $dateGiven[1]."-".$dateGiven[0];
		
		$fromDate = date("Y-m-01", strtotime($dateModified));
		$toDate = date("Y-m-t", strtotime($dateModified));

		if ($responseType == 0) {
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>Id</th>
								<th>Employee</th>
								<th>Status</th>
								<th>JoinDate</th>
								<th>Termination Date</th>
								<th>Client</th>
								<th>Client Manager</th>
								<th>Recruiter</th>
								<th>Recruiter Manager</th>
								<th>Inside Sales Person</th>
								<th>Inside Post Sales</th>
								<th>Onsite Sales Person</th>
								<th>Onsite Post Sales</th>
								<th>Employment Type</th>
								<th>Benefit</th>
								<th>Benefit List</th>
								<th>Bill Rate</th>
								<th>Pay Rate</th>
								<th>Tax</th>
								<th>MSP Fee</th>
								<th>Prime Vendor Fee</th>
								<th>Candidate Rate</th>
								<th>Client (Bill) Rate</th>
								<th>GP / Hour</th>
								<th>Total Hours</th>
								<th>Total GP</th>
								<th>Total Revenue</th>
							</tr>
						</thead>
						<?php
							}

							$findLockDataQUERY = mysqli_query($allConn, "SELECT
								gd.*
							FROM
								mis_reports.gp_data AS gd
							WHERE
								gd.from_date = '$fromDate'
							AND
								gd.to_date = '$toDate'");

							if (mysqli_num_rows($findLockDataQUERY) > 0) {
								$findLockDataROW = mysqli_fetch_array($findLockDataQUERY);
								$briefDataObject = json_decode($findLockDataROW["brief_data"], true);
								$totalDataObject = json_decode($findLockDataROW["total_data"], true);
								if ($responseType == 0) {
						?>
							<tbody class="tbody-tr-style">
								<?php
									foreach ($briefDataObject as $briefDataKey => $briefDataValue) {
								?>
								<tr class="thead-tr-style">
									<td>
									<?php
										echo $briefDataValue["employee_id"];
									?>
									</td>
								<?php
									if ($briefDataValue["status"] == "Active") {
								?>
									<td>
									<?php
										echo $briefDataValue["employee_name"];
									?>
									</td>
									<td style="color: #449D44;">
									<?php
										echo $briefDataValue["status"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["join_date"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["termination_date"];
									?>
									</td>
								<?php
									} else {
								?>
									<td style="color: #fc2828;">
									<?php
										echo $briefDataValue["employee_name"];
									?>
									</td>
									<td style="color: #fc2828;">
									<?php
										echo $briefDataValue["status"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["join_date"];
									?>
									</td>
									<td style="color: #fc2828;">
									<?php
										echo $briefDataValue["termination_date"];
									?>
									</td>
								<?php
									}
								?>
									<td>
									<?php
										echo $briefDataValue["client"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["client_manager"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["recruiter"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["recruiter_manager"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["inside_sales1"];
										echo ucwords($briefDataValue["inside_sales1"]);
										if ($briefDataValue["inside_sales2"] != "") {
											echo ", ".ucwords($briefDataValue["inside_sales2"]);
										}
										if ($briefDataValue["research_by"] != "") {
											echo ", ".ucwords($briefDataValue["research_by"]);
										}
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["inside_post_sales"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["onsite_sales"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["onsite_post_sales"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["employment_type"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["benefit"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["benefitlist"];
									?>
									</td>
								<?php
									if ($briefDataValue["total_hour"] <= "0") {
								?>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["billrate"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["payrate"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["tax"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["mspfee"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["prime_vendor_fee"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["candidate_rate"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["client_rate"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["gp_per_hour"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["total_hour"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["total_gp"];
									?>
									</td>
									<td style="background-color: red;color: #fff;font-weight: bold;">
									<?php
										echo $briefDataValue["total_revenue"];
									?>
									</td>
								<?php
									} else {
								?>
									<td>
									<?php
										echo $briefDataValue["billrate"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["payrate"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["tax"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["mspfee"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["prime_vendor_fee"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["candidate_rate"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["client_rate"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["gp_per_hour"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["total_hour"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["total_gp"];
									?>
									</td>
									<td>
									<?php
										echo $briefDataValue["total_revenue"];
									?>
									</td>
								<?php
									}
								?>
								</tr>
								<?php
									}
								?>
							</tbody>
							<tfoot>
								<tr class="tfoot-tr-style">
									<th colspan="16"></th>
									<th>
									<?php
										echo $totalDataObject[0]["total_bill_rate"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["total_pay_rate"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["total_tax"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["total_msp_fee"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["total_prime_vendor_fee"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["total_candidate_rate"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["total_client_rate"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["total_gp_per_hour"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["final_hour"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["final_gp"];
									?>
									</th>
									<th>
									<?php
										echo $totalDataObject[0]["final_revenue"];
									?>
									</th>
								</tr>
							</tfoot>
					<?php
							}
						} else {
							if ($responseType == 0) {
					?>
					<tbody>
					<?php
							}
							$totalBillRate = $totalPayRate = $totalTax = $totalMspFees = $totalPrimeCharges = $totalCandidateRate = $finalGrossProfit = $finalHour = $finalGP = $finalRevenue = array();

							$delimiter = array("","[","]",'"');

							$taxSettingsTableData = taxSettingsTable($allConn);
							$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$fromDate,$toDate);

							$mainQUERY = "SELECT
								e.id AS employee_id,
								CONCAT(e.first_name,' ',e.last_name) AS employee_name,
								e.status AS employee_status,
								DATE_FORMAT(e.custom7, '%m-%d-%Y') AS join_date,
								DATE_FORMAT(e.termination_date, '%m-%d-%Y') AS termination_date,
								e.custom1 AS benefit,
								e.custom2 AS benefit_list,
								CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
								CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
								es.id AS employment_id,
								es.name AS employment_type,
								comp.company_id,
								comp.name AS company_name,
								clf.mspChrg_pct AS client_msp_charge_percentage,
								clf.primechrg_pct AS client_prime_charge_percentage,
								clf.primeChrg_dlr AS client_prime_charge_dollar,
								clf.mspChrg_dlr AS client_msp_charge_dollar,
								cnf.c_primeCharge_pct AS employee_prime_charge_percentage,
								cnf.c_primeCharge_dlr AS employee_prime_charge_dollar,
								cnf.c_anyCharge_dlr AS employee_any_charge_dollar,
								CONCAT(cm.first_name,' ',cm.last_name) AS client_manager_name,
								CONCAT(r.first_name,' ',r.last_name) AS recruiter_name,
							    r.notes AS recruiter_manager_name,
							    is1.value AS inside_sales1,
							    is2.value AS inside_sales2,
							    rb.value AS research_by,
							    ips.value AS inside_post_sales,
							    os.value AS onsite_sales,
							    ops.value AS onsite_post_sales
							FROM
								vtechhrm.employees AS e
								LEFT JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
								LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
								LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
							    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
								LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
								LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
								LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
								LEFT JOIN cats.user AS cm ON cm.user_id = comp.owner
								LEFT JOIN cats.user AS r ON r.user_id = si.c_recruiter_id
								LEFT JOIN cats.extra_field AS is1 ON is1.data_item_id = comp.company_id AND is1.field_name = 'Inside Sales Person1'
								LEFT JOIN cats.extra_field AS is2 ON is2.data_item_id = comp.company_id AND is2.field_name = 'Inside Sales Person2'
								LEFT JOIN cats.extra_field AS rb ON rb.data_item_id = comp.company_id AND rb.field_name = 'Research By'
								LEFT JOIN cats.extra_field AS ips ON ips.data_item_id = comp.company_id AND ips.field_name = 'Inside Post Sales'
								LEFT JOIN cats.extra_field AS os ON os.data_item_id = comp.company_id AND os.field_name = 'Onsite Sales Person'
								LEFT JOIN cats.extra_field AS ops ON ops.data_item_id = comp.company_id AND ops.field_name = 'Onsite Post Sales'
							WHERE
								ep.project != '6'
							AND
								DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
							GROUP BY employee_id";

							$mainRESULT = mysqli_query($allConn, $mainQUERY);
							if (mysqli_num_rows($mainRESULT) > 0) {
								while ($mainROW = mysqli_fetch_array($mainRESULT)) {
									$benefitList = $taxRate = $mspFees = $primeCharges = $candidateRate = $grossMargin = $totalHour = $totalGP = $totalRevenue = 0;

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
										$totalBillRate[] = $mainROW["bill_rate"];
										$totalPayRate[] = $mainROW["pay_rate"];
										$totalTax[] = $taxRate;
										$totalMspFees[] = $mspFees;
										$totalPrimeCharges[] = $primeCharges;
										$totalCandidateRate[] = $candidateRate;
										$finalGrossProfit[] = $grossMargin;
										$finalHour[] = $totalHour;
										$finalGP[] = $totalGP;
										$finalRevenue[] = $totalRevenue;
									}

									if ($responseType == 0) {
					?>
							<tr class="tbody-tr-style">
								<td>
								<?php
									echo $mainROW["employee_id"];
								?>
								</td>
							<?php
								if ($mainROW["employee_status"] == "Active") {
							?>
								<td>
								<?php
									echo ucwords($mainROW["employee_name"]);
								?>
								</td>
								<td style="color: #449D44;">
								<?php
									echo $mainROW["employee_status"];
								?>
								</td>
								<td>
								<?php
									echo $mainROW["join_date"];
								?>
								</td>
								<td>---</td>
							<?php
								} else {
							?>
								<td style="color: #fc2828;">
								<?php
									echo ucwords($mainROW["employee_name"]);
								?>
								</td>
								<td style="color: #fc2828;">
								<?php
									echo $mainROW["employee_status"];
								?>
								</td>
								<td>
								<?php
									echo $mainROW["join_date"];
								?>
								</td>
								<td style="color: #fc2828;">
								<?php
									echo $mainROW["termination_date"];
								?>
								</td>
							<?php
								}
							?>
								<td>
								<?php
									echo $mainROW["company_name"];
								?>
								</td>
								<td>
								<?php
									echo ucwords($mainROW["client_manager_name"]);
								?>
								</td>
								<td>
								<?php
									echo ucwords($mainROW["recruiter_name"]);
								?>
								</td>
								<td>
								<?php
									echo ucwords($mainROW["recruiter_manager_name"]);
								?>
								</td>
								<td>
								<?php
									echo ucwords($mainROW["inside_sales1"]);
									if ($mainROW["inside_sales2"] != "") {
										echo ", ".ucwords($mainROW["inside_sales2"]);
									}
									if ($mainROW["research_by"] != "") {
										echo ", ".ucwords($mainROW["research_by"]);
									}
								?>
								</td>
								<td>
								<?php
									echo ucwords($mainROW["inside_post_sales"]);
								?>
								</td>
								<td>
								<?php
									echo ucwords($mainROW["onsite_sales"]);
								?>
								</td>
								<td>
								<?php
									echo ucwords($mainROW["onsite_post_sales"]);
								?>
								</td>
								<td>
								<?php
									echo $mainROW["employment_type"];
								?>
								</td>
								<td>
								<?php
									echo $mainROW["benefit"];
								?>
								</td>
								<td>
								<?php
									echo $benefitlist;
								?>
								</td>
							<?php
								if ($totalHour <= "0") {
							?>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $mainROW["bill_rate"];
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $mainROW["pay_rate"];
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $taxRate;
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $mspFees;
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $primeCharges;
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $candidateRate;
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $mainROW["bill_rate"];
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $grossMargin;
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $totalHour;
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $totalGP;
								?>
								</td>
								<td style="background-color: red;color: #fff;font-weight: bold;">
								<?php
									echo $totalRevenue;
								?>
								</td>
							<?php
								} else {
							?>
								<td>
								<?php
									echo $mainROW["bill_rate"];
								?>
								</td>
								<td>
								<?php
									echo $mainROW["pay_rate"];
								?>
								</td>
								<td>
								<?php
									echo $taxRate;
								?>
								</td>
								<td>
								<?php
									echo $mspFees;
								?>
								</td>
								<td>
								<?php
									echo $primeCharges;
								?>
								</td>
								<td>
								<?php
									echo $candidateRate;
								?>
								</td>
								<td>
								<?php
									echo $mainROW["bill_rate"];
								?>
								</td>
								<td>
								<?php
									echo $grossMargin;
								?>
								</td>
								<td>
								<?php
									echo $totalHour;
								?>
								</td>
								<td>
								<?php
									echo $totalGP;
								?>
								</td>
								<td>
								<?php
									echo $totalRevenue;
								?>
								</td>
							<?php
								}
							?>
							</tr>
					<?php
									} else {
										if($mainROW["status"] == "Active") {
											$terminationDate = "---";
										}else{
											$terminationDate = $mainROW["termination_date"];
										}
										$responseArray["briefList"][] = array(
											"employee_id" => $mainROW["employee_id"],
											"employee_name" => urlencode(trim(ucwords($mainROW["employee_name"]))),
											"status" => $mainROW["employee_status"],
											"join_date" => $mainROW["join_date"],
											"termination_date" => $terminationDate,
											"client" => urlencode(trim($mainROW["company_name"])),
											"client_manager" => urlencode(trim(ucwords($mainROW["client_manager_name"]))),
											"recruiter" => urlencode(trim(ucwords($mainROW["recruiter_name"]))),
											"recruiter_manager" => urlencode(trim(ucwords($mainROW["recruiter_manager_name"]))),
											"inside_sales1" => urlencode(trim(ucwords($mainROW["inside_sales1"]))),
											"inside_sales2" => urlencode(trim(ucwords($mainROW["inside_sales2"]))),
											"research_by" => urlencode(trim(ucwords($mainROW["research_by"]))),
											"inside_post_sales" => urlencode(trim(ucwords($mainROW["inside_post_sales"]))),
											"onsite_sales" => urlencode(trim(ucwords($mainROW["onsite_sales"]))),
											"onsite_post_sales" => urlencode(trim(ucwords($mainROW["onsite_post_sales"]))),
											"employment_type" => $mainROW["employment_type"],
											"benefit" => $mainROW["benefit"],
											"benefitlist" => $benefitlist,
											"billrate" => $mainROW["bill_rate"],
											"payrate" => $mainROW["pay_rate"],
											"tax" => $taxRate,
											"mspfee" => $mspFees,
											"prime_vendor_fee" => $primeCharges,
											"candidate_rate" => $candidateRate,
											"client_rate" => $mainROW["bill_rate"],
											"gp_per_hour" => $grossMargin,
											"total_hour" => $totalHour,
											"total_gp" => $totalGP,
											"total_revenue" => $totalRevenue
										);
									}
								}
							}
							if ($responseType == 0) {
					?>
						</tbody>
						<tfoot>
							<tr class="tfoot-tr-style">
								<th colspan="16"></th>
								<th>
								<?php
									echo array_sum($totalBillRate);
								?>
								</th>
								<th>
								<?php
									echo array_sum($totalPayRate);
								?>
								</th>
								<th>
								<?php
									echo array_sum($totalTax);
								?>
								</th>
								<th>
								<?php
									echo array_sum($totalMspFees);
								?>
								</th>
								<th>
								<?php
									echo array_sum($totalPrimeCharges);
								?>
								</th>
								<th>
								<?php
									echo array_sum($totalCandidateRate);
								?>
								</th>
								<th>
								<?php
									echo array_sum($totalBillRate);
								?>
								</th>
								<th>
								<?php
									echo array_sum($finalGrossProfit);
								?>
								</th>
								<th>
								<?php
									echo array_sum($finalHour);
								?>
								</th>
								<th>
								<?php
									echo number_format(array_sum($finalGP), 2);
								?>
								</th>
								<th>
								<?php
									echo number_format(array_sum($finalRevenue), 2);
								?>
								</th>
							</tr>
						</tfoot>
					<?php
							} else {
								$responseArray["totalList"][] = array(
									"total_bill_rate" => array_sum($totalBillRate),
									"total_pay_rate" => array_sum($totalPayRate),
									"total_tax" => array_sum($totalTax),
									"total_msp_fee" => array_sum($totalMspFees),
									"total_prime_vendor_fee" => array_sum($totalPrimeCharges),
									"total_candidate_rate" => array_sum($totalCandidateRate),
									"total_client_rate" => array_sum($totalBillRate),
									"total_gp_per_hour" => array_sum($finalGrossProfit),
									"final_hour" => array_sum($finalHour),
									"final_gp" => array_sum($finalGP),
									"final_revenue" => array_sum($finalRevenue)
								);
							}
						}
						if ($responseType == 0) {
					?>
					</table>
				</div>
			</div>
		</div>
	</section>
<?php
		}
	}
	if ($responseType == 0) {
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
            multidate: false,
            orientation: "top",
            autoclose: true
        });

		var customizedDataTable = $(".customized-datatable").DataTable({
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		    dom: "Bfrtip",
		    "aaSorting": [[1,"asc"]],
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

</script>
</html>
<?php
			} else {
				echo json_encode($responseArray);
			}
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
