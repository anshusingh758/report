<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "21";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>MD Revenue by Inside Post Sales Report</title>

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
		.notes-popup-button,
		.notes-popup-button:focus {
			outline: none;
			font-weight: bold;
			color: #2266AA;
			background-color: #fff;
			border: 1px solid #2266AA;
			border-radius: 0px;
			padding: 5px 12px;
			float: left;
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

	<div class="modal fade notes-popup" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Report Guidelines</h4>
				</div>
				<div class="modal-body">
					<ul style="font-weight: bold;">
						<li>This Report is useful for viewing MD Revenue detail of inside post sales personnel for selected daterange.
							<ul>
								<li>Here, You can view individual personnel's performance on the basis of <span style="color: red;">history changes</span> between given daterange.</li>
								<li>You can also find no. of Working Resources in brief, by clicking on its count.</li>
							</ul>
						</li>
						<li>By selecting <span style="color: red;">Include this Year</span> CheckBox, You can find Resources Working Starting form current year, to selected end date.</li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">MD Revenue by Inside Post Sales Report</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<button type="button" class="notes-popup-button" data-toggle="modal" data-target=".notes-popup"><i class="fa fa-fw fa-commenting-o"></i> Guidelines</button>
				</div>
				<div class="col-md-2">
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
						<label>Select Personnel :</label>
						<select id="personnel-list" class="<?php if (isset($_REQUEST["personnel-list"])) { echo "customized-selectbox-without-all"; } else { echo "customized-selectbox-with-all"; } ?>" name="personnel-list[]" multiple required>
							<?php
								$isSelected = "";
								$personnelList = catsExtraFieldPersonnelList($catsConn,"Inside Post Sales");
								sort($personnelList);
								foreach ($personnelList as $personnelKey => $personnelValue) {
									if (in_array($personnelValue, $_REQUEST["personnel-list"])) {
										$isSelected = " selected";
									} else {
										$isSelected = "";
									}
									echo "<option value='".$personnelValue."'".$isSelected.">".$personnelValue."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row" style="margin-top: 15px;">
					<div class="col-md-4 col-md-offset-4">
						<div class="this-month-div">
							<input name="this-month-input" type="checkbox" id="this-month-id" <?php if (isset($_REQUEST['this-month-input'])) { echo 'checked'; } ?>>
							<label for="this-month-id"> Only Candidate Started Year : </label>
							<select name="this-month-value" style="cursor: pointer;" required>
							<?php
								for ($i=date("Y"); $i >= 2019; $i--) {
									$isSelected = "";
									$firstDate = $i."-01-01";
									if ($_REQUEST['this-month-value'] == $firstDate) {
										$isSelected = " selected";
									}
									
									echo "<option value='".$firstDate."'".$isSelected.">".$i."</option>";
								}
							?>
							</select>
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

			$totalMonth = 1;
		} else {
		
			$fromDate[] = date("Y-m-d", strtotime($_REQUEST["customized-from-date"]));
			$toDate[] = date("Y-m-d", strtotime($_REQUEST["customized-to-date"]));

			$firstYear = date('Y', strtotime($_REQUEST["customized-from-date"]));
			$secondYear = date('Y', strtotime($_REQUEST["customized-to-date"]));

			$firstMonth = date('m', strtotime($_REQUEST["customized-from-date"]));
			$secondMonth = date('m', strtotime($_REQUEST["customized-to-date"]));

			$totalMonth = ((($secondYear - $firstYear) * 12) + ($secondMonth - $firstMonth));
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

		$personnelData = "'".implode("', '",$_REQUEST['personnel-list'])."'";

		if (isset($_REQUEST["this-month-input"])) {
			$includePeriod = "true";
		} else {
			$includePeriod = "false";
		}

		/*$thisYearStartDate = date("Y")."-01-01";*/

		$thisYearStartDate = $_REQUEST["this-month-value"];

		$finalReportArray = $finalCountArray = array();

		$taxSettingsTableData = taxSettingsTable($allConn);

		foreach ($fromDate as $fromDateKey => $fromDateValue) {
			
			$resources = $hours = $revenue = $pay = $gp = $sowData = array();
			$startDate = $fromDate[$fromDateKey];
			$endDate = $toDate[$fromDateKey];
			$thisYear = date('Y', strtotime($fromDateValue));
			$sowData = getSowData($allConn,$thisYear);
			$givenMonth = date("m/Y", strtotime($startDate));

			$delimiter = array("","[","]",'"');

			$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$startDate,$endDate);

			$mainQUERY = "SELECT
				ehd.*,
				si.c_inside_post_sales
			FROM
				vtechhrm.employees AS e
				LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
			    LEFT JOIN vtech_mappingdb.employee_history_detail AS ehd ON ehd.employee_id = e.id
			    LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			WHERE
				si.c_inside_post_sales IN ($personnelData)
			AND
				ehd.id IN (SELECT MAX(id) FROM vtech_mappingdb.employee_history_detail WHERE employee_id = ehd.employee_id AND ((created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') OR (((created_at NOT BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') AND created_at < '$startDate 00:00:00') OR ((created_at NOT BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') AND created_at > '$endDate 23:59:59'))))";

			if ($includePeriod == "true") {
				$mainQUERY .= " AND
					e.custom7 BETWEEN '$thisYearStartDate 00:00:00' AND '$endDate 23:59:59'
				AND
					ete.date_start BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
				GROUP BY e.id";
			} else {
				$mainQUERY .= " AND
					ete.date_start BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
				GROUP BY e.id";
			}

			$mainRESULT = mysqli_query($allConn, $mainQUERY);
			
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

					if ($totalHour > "0") {
						$totalPay = round(($candidateRate * $totalHour), 2);

						$totalGP = round(($grossMargin * $totalHour), 2);
						
						$totalRevenue = round(($mainROW["bill_rate"] * $totalHour), 2);

						$resources[] = $mainROW["employee_id"];
						$hours[] = $totalHour;
						$revenue[] = $totalRevenue;
						$pay[] = $totalPay;
						$gp[] = $totalGP;

						$finalReportArray[$mainROW["c_inside_post_sales"]][$givenMonth][] = array(
							"employee_id" => $mainROW["employee_id"],
							"given_start_date" => $startDate,
							"given_end_date" => $endDate,
							"total_hours" => $totalHour,
							"total_revenue" => $totalRevenue,
							"total_pay" => $totalPay,
							"total_gp" => $totalGP
						);
					}
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
								<th>Personnel</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th>Months</th>
							<?php } ?>
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
										
										$resourceArray = $hoursArray = $revenueArray = $payArray = $gpArray = $dataResources = array();

										foreach ($finalReportMonth as $finalReportMonthKey => $finalReportMonthItem) {
											$resourceArray[] = $finalReportMonthItem["employee_id"];
											$hoursArray[] = $finalReportMonthItem["total_hours"];
											$revenueArray[] = $finalReportMonthItem["total_revenue"];
											$payArray[] = $finalReportMonthItem["total_pay"];
											$gpArray[] = $finalReportMonthItem["total_gp"];

											$dataResources[$finalReportKey][] = array(
												"employee_id" => $finalReportMonthItem["employee_id"],
												"start_date" => $finalReportMonthItem["given_start_date"],
												"end_date" => $finalReportMonthItem["given_end_date"]
											);
										}

										$resourcesPerc = $hoursPerc = $revenuePerc = $payPerc = $gpPerc = "";

										foreach ($finalCountArray[$finalReportValueKey] as $finalCountArrayKey => $finalCountArrayValue) {
											$resourcesPerc = round((((count(array_unique($resourceArray))) * 100) / $finalCountArrayValue["resources"]), 2);
											$hoursPerc = round(((array_sum($hoursArray) * 100) / $finalCountArrayValue["hours"]), 2);
											$revenuePerc = round(((array_sum($revenueArray) * 100) / $finalCountArrayValue["revenue"]), 2);
											$payPerc = round(((array_sum($payArray) * 100) / $finalCountArrayValue["pay"]), 2);
											$gpPerc = round(((array_sum($gpArray) * 100) / $finalCountArrayValue["gp"]), 2);
										}
										$finalSow = round((($sowData[strtolower(trim($finalReportKey))] / 12) * $totalMonth), 2);
										
										if ($finalSow != '') { 
											array_push($gpArray, $finalSow);
										}
							?>
							<tr class="tbody-tr-style">
								<td nowrap><?php echo ucwords($finalReportKey); ?></td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td><?php echo $finalReportValueKey; ?></td>
							<?php } ?>
								<td><a class="resources-detail-popup" data-popup='<?php echo json_encode($dataResources, true); ?>'><?php echo $resourceArraySum[] = count(array_unique($resourceArray)); ?></a></td>
								<td><?php echo $resourcesPerc."%"; ?></td>
								<td><?php echo $hoursArraySum[] = array_sum($hoursArray); ?></td>
								<td><?php echo $hoursPerc."%"; ?></td>
								<td><?php echo $revenueArraySum[] = array_sum($revenueArray); ?></td>
								<td><?php echo $revenuePerc."%"; ?></td>
								<td><?php echo $payArraySum[] = array_sum($payArray); ?></td>
								<td><?php echo $payPerc."%"; ?></td>
								<td><?php echo $gpArraySum[] = array_sum($gpArray); ?></td>
								<td><?php echo $gpPerc."%"; ?></td>
							</tr>
							<?php
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

	$(document).on("click", ".resources-detail-popup", function(e){
		e.preventDefault();
		$("#divLoading").addClass("show");
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/working-resources-detail-popup-by-md-post-sales.php",
			data: {"type":$(this).data("popuptype"),"data":JSON.stringify($(this).data("popup"))},
			success: function(response) {
				$("#divLoading").removeClass("show");
				$(".view-working-resources-detail-by-md-report").modal("show");
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
