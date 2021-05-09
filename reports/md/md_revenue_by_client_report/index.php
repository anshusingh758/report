<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "15";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>MD Revenue by Client Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
			padding: 3px 1px;
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
	</style>
</head>
<body>

	<?php include_once("../../../popups.php"); ?>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">MD Revenue by Client Report</div>
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

		$clientData = implode(",", $_REQUEST["client-list"]);

		if (isset($_REQUEST["this-month-input"])) {
			$includePeriod = "true";
		} else {
			$includePeriod = "false";
		}

		$thisYearStartDate = date("Y")."-01-01";

		$finalReportArray = $finalCountArray = array();

		$taxSettingsTableData = taxSettingsTable($allConn);

		foreach ($fromDate as $fromDateKey => $fromDateValue) {
			
			$resources = $hours = $revenue = $pay = $gp = array();

			$startDate = $fromDate[$fromDateKey];
			$endDate = $toDate[$fromDateKey];

			$givenMonth = date("m/Y", strtotime($startDate));

			$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$startDate,$endDate);

			$mainQUERY = "SELECT
				e.id AS employee_id,
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
				CONCAT(u.first_name,' ',u.last_name) AS cs_manager,
				inside_sales_person1.value AS inside_sales_person1,
				inside_sales_person2.value AS inside_sales_person2,
				research_by.value AS research_by,
				inside_post_sales.value AS inside_post_sales,
				onsite_sales_person.value AS onsite_sales_person,
				onsite_post_sales.value AS onsite_post_sales
			FROM
				vtechhrm.employees AS e
				LEFT JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
				LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
				LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
				LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
				LEFT JOIN cats.user AS u ON u.user_id = comp.owner
				LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
				LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
				LEFT JOIN cats.extra_field AS inside_sales_person1 ON inside_sales_person1.data_item_id = comp.company_id AND inside_sales_person1.field_name = 'Inside Sales Person1'
				LEFT JOIN cats.extra_field AS inside_sales_person2 ON inside_sales_person2.data_item_id = comp.company_id AND inside_sales_person2.field_name = 'Inside Sales Person2'
				LEFT JOIN cats.extra_field AS research_by ON research_by.data_item_id = comp.company_id AND research_by.field_name = 'Research By'
				LEFT JOIN cats.extra_field AS inside_post_sales ON inside_post_sales.data_item_id = comp.company_id AND inside_post_sales.field_name = 'Inside Post Sales'
				LEFT JOIN cats.extra_field AS onsite_sales_person ON onsite_sales_person.data_item_id = comp.company_id AND onsite_sales_person.field_name = 'OnSite Sales Person'
				LEFT JOIN cats.extra_field AS onsite_post_sales ON onsite_post_sales.data_item_id = comp.company_id AND onsite_post_sales.field_name = 'OnSite Post Sales'
			WHERE
				comp.company_id IN ($clientData)
			AND
				ep.project != '6'";

			if ($includePeriod == "true") {
				$mainQUERY .= " AND
					DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$endDate'
				AND
					DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY employee_id";
			} else {
				$mainQUERY .= " AND
					DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY employee_id";
			}

			$mainRESULT = mysqli_query($vtechhrmConn, $mainQUERY);
			if (mysqli_num_rows($mainRESULT) > 0) {
				while ($mainROW = mysqli_fetch_array($mainRESULT)) {
					
					$benefitList = str_replace($delimiter, $delimiter[0], $mainROW["benefit_list"]);

					//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$mainROW["benefit"],$benefitList,$mainROW["employment_id"],$mainROW["pay_rate"]), 2);

					$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$mainROW["benefit"],$benefitList,$mainROW["employment_id"],$mainROW["pay_rate"]), 2);

					$mspFees = round((($mainROW["client_msp_charge_percentage"] / 100) * $mainROW["bill_rate"]) + $mainROW["client_msp_charge_dollar"], 2);

					$primeCharges = round(((($mainROW["client_prime_charge_percentage"] / 100) * $mainROW["bill_rate"]) + (($mainROW["employee_prime_charge_percentage"] / 100) * $mainROW["bill_rate"]) + $mainROW["employee_prime_charge_dollar"] + $mainROW["employee_any_charge_dollar"] + $mainROW["client_prime_charge_dollar"]), 2);

					$candidateRate = round(($mainROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

					$grossMargin = round(($mainROW["bill_rate"] - $candidateRate), 2);

					//$totalHour = round(employeeWorkingHours($vtechhrmConn,$startDate,$endDate,$mainROW["employee_id"]), 2);

					$totalHour = round(array_sum($employeeTimeEntryTableData[$mainROW["employee_id"]]), 2);
					
					$totalPay = round(($candidateRate * $totalHour), 2);

					$totalGP = round(($grossMargin * $totalHour), 2);
					
					$totalRevenue = round(($mainROW["bill_rate"] * $totalHour), 2);

					$resources[] = $mainROW["employee_id"];
					$hours[] = $totalHour;
					$revenue[] = $totalRevenue;
					$pay[] = $totalPay;
					$gp[] = $totalGP;

					$finalReportArray[$mainROW["company_id"]][$givenMonth][] = array(
						"employee_id" => $mainROW["employee_id"],
						"company_name" => $mainROW["company_name"],
						"cs_manager" => $mainROW["cs_manager"],
						"inside_sales_person1" => $mainROW["inside_sales_person1"],
						"inside_sales_person2" => $mainROW["inside_sales_person2"],
						"research_by" => $mainROW["research_by"],
						"inside_post_sales" => $mainROW["inside_post_sales"],
						"onsite_sales_person" => $mainROW["onsite_sales_person"],
						"onsite_post_sales" => $mainROW["onsite_post_sales"],
						"total_hours" => $totalHour,
						"total_revenue" => $totalRevenue,
						"total_pay" => $totalPay,
						"total_gp" => $totalGP
					);

				}
			}

			$finalCountArray[$givenMonth][] = array(
				"resources" => count(array_unique($resources)),
				"hours" => array_sum($hours),
				"revenue" => array_sum($revenue),
				"pay" => array_sum($pay),
				"gp" => array_sum($gp)
			);
		
		}

?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>Client</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th>Months</th>
							<?php } ?>
								<th>CS Manager</th>
								<th>Inside Sales Personnel1</th>
								<th>Inside Sales Personnel2</th>
								<th>Research By</th>
								<th>Inside PS Personnel</th>
								<th>Onsite Sales Personnel</th>
								<th>Onsite PS Personnel</th>
								<th data-toggle="tooltip" data-placement="top" title="Total Candidates Working">No. of Resource Working</th>
								<th>Percentage(%) of Total Candidate</th>
								<th>No. of Hours Work</th>
								<th>Percentage(%) of Total Hours Work</th>
								<th data-toggle="tooltip" data-placement="top" title="Hours * Bill Rate (Per Candidate)">Total Revenue</th>
								<th>Percentage(%) of Total Revenue</th>
								<th>Total Pay (C2C)</th>
								<th>Percentage(%) of Totat Pay (C2C)</th>
								<th>Total GP Revenue</th>
								<th>Percentage(%) of Total GP Revenue</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($finalReportArray as $finalReportKey => $finalReportValue) {
									
									foreach ($finalReportValue as $finalReportValueKey => $finalReportMonth) {
										
										$resourceArray = $hoursArray = $revenueArray = $payArray = $gpArray = array();

										$companyName = $csManager = $insideSalesPerson1 = $insideSalesPerson2 = $researchBy = $insidePostSales = $onsiteSalesPerson = $onsitePostSales = "";

										foreach ($finalReportMonth as $finalReportMonthKey => $finalReportMonthItem) {
											$resourceArray[] = $finalReportMonthItem["employee_id"];
											$hoursArray[] = $finalReportMonthItem["total_hours"];
											$revenueArray[] = $finalReportMonthItem["total_revenue"];
											$payArray[] = $finalReportMonthItem["total_pay"];
											$gpArray[] = $finalReportMonthItem["total_gp"];

											$companyName = $finalReportMonthItem["company_name"];
											$csManager = $finalReportMonthItem["cs_manager"];
											$insideSalesPerson1 = $finalReportMonthItem["inside_sales_person1"];
											$insideSalesPerson2 = $finalReportMonthItem["inside_sales_person2"];
											$researchBy = $finalReportMonthItem["research_by"];
											$insidePostSales = $finalReportMonthItem["inside_post_sales"];
											$onsiteSalesPerson = $finalReportMonthItem["onsite_sales_person"];
											$onsitePostSales = $finalReportMonthItem["onsite_post_sales"];
										}

										$resourcesPerc = $hoursPerc = $revenuePerc = $payPerc = $gpPerc = "";

										foreach ($finalCountArray[$finalReportValueKey] as $finalCountArrayKey => $finalCountArrayValue) {
											$resourcesPerc = $finalCountArrayValue["resources"];
											$hoursPerc = $finalCountArrayValue["hours"];
											$revenuePerc = $finalCountArrayValue["revenue"];
											$payPerc = $finalCountArrayValue["pay"];
											$gpPerc = $finalCountArrayValue["gp"];
										}
							?>
							<tr class="tbody-tr-style">
								<td><?php echo $companyName; ?></td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td><?php echo $finalReportValueKey; ?></td>
							<?php } ?>
								<td><?php echo $csManager; ?></td>
								<td><?php echo $insideSalesPerson1; ?></td>
								<td><?php echo $insideSalesPerson2; ?></td>
								<td><?php echo $researchBy; ?></td>
								<td><?php echo $insidePostSales; ?></td>
								<td><?php echo $onsiteSalesPerson; ?></td>
								<td><?php echo $onsitePostSales; ?></td>
								<td><?php echo $resourceArraySum[] = count(array_unique($resourceArray)); ?></td>
								<td><?php echo round(((count(array_unique($resourceArray)) * 100) / $resourcesPerc), 2)."%"; ?></td>
								<td><?php echo $hoursArraySum[] = array_sum($hoursArray); ?></td>
								<td><?php echo round(((array_sum($hoursArray) * 100) / $hoursPerc), 2)."%"; ?></td>
								<td><?php echo $revenueArraySum[] = array_sum($revenueArray); ?></td>
								<td><?php echo round(((array_sum($revenueArray) * 100) / $revenuePerc), 2)."%"; ?></td>
								<td><?php echo $payArraySum[] = array_sum($payArray); ?></td>
								<td><?php echo round(((array_sum($payArray) * 100) / $payPerc), 2)."%"; ?></td>
								<td><?php echo $gpArraySum[] = array_sum($gpArray); ?></td>
								<td><?php echo round(((array_sum($gpArray) * 100) / $gpPerc), 2)."%"; ?></td>
							</tr>
							<?php
									}
								}
							?>
						</tbody>
						<tfoot>
							<tr class="tfoot-tr-style">
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th colspan="9"></th>
							<?php } else { ?>
								<th colspan="8"></th>
							<?php } ?>
								<th><?php echo array_sum($resourceArraySum); ?></th>
								<th></th>
								<th><?php echo array_sum($hoursArraySum); ?></th>
								<th></th>
								<th><?php echo array_sum($revenueArraySum); ?></th>
								<th></th>
								<th><?php echo array_sum($payArraySum); ?></th>
								<th></th>
								<th><?php echo array_sum($gpArraySum); ?></th>
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
