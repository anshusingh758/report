<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "87";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Quickbook Timesheet Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
			padding: 5px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td {
			padding: 2px 1px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td:nth-child(1),
		table.dataTable tbody td:nth-child(2),
		table.dataTable tbody td:nth-child(8),
		table.dataTable tbody td:nth-child(9),
		table.dataTable tbody td:nth-child(10) {
			text-align: left;
		}
		table.dataTable thead tr:nth-child(2) th:last-child {
			border-right: 1px solid #ddd;
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
			margin-top: 10px;
			margin-bottom: 70px;
		}
		.main-section-row {
			margin-top: 15px;
		}
		.main-section-submit-row {
			margin-top: 20px;
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
			font-size: 13px;
		}
		.tfoot-tr-style {
			background-color: #ccc;
			color: #000;
			font-size: 14px;
		}
		.view-synced-timesheet-div {
			float: left;
		}
		.view-synced-timesheet-div input {
			height:13px;width:13px;cursor: pointer;
		}
		.view-synced-timesheet-div label {
			cursor: pointer;
		}
		.sub-td-span {
			font-size: 11px;
			color: #333;
			float: right;
			font-style: italic;
			color: #2266AA;
			font-weight: bold;
		}
		.fa-plus-circle {
			font-size: 18px;
			color: #2266AA;
			cursor: pointer;
		}
		.fa-minus-circle {
			font-size: 18px;
			color: red;
			cursor: pointer;
		}
        #divLoading {
        	display : none;
        }
        #divLoading.show {
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

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">Quickbook Timesheet Report</div>
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
					<button type="button" class="form-control week-button dark-button">Week</button>
				</div>
				<div class="col-md-2">
					<button type="button" class="form-control month-button smooth-button">Month</button>
				</div>
				<div class="col-md-4">
					<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
				</div>
			</div>

			<form action="index.php" method="post">
				<div class="row main-section-row multiple-week-input">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Week :</label>
						<select name="customized-week-list" class="customized-selectbox-without-all customized-week-list" autocomplete="off" required>
							<option value="">Select Option</option>
						<?php
							$timesheetWeeksList = timesheetWeeksList();
							
							foreach ($timesheetWeeksList as $timesheetWeeksListKey => $timesheetWeeksListValue) {
									if (strtotime($timesheetWeeksListValue["selected_range"]["start_date"]) >= strtotime("2021-02-21")) {
									$isSelected = "";

									if (!isset($_REQUEST['customized-month-list']) && json_encode($timesheetWeeksListValue["selected_range"]) == $_REQUEST["customized-week-list"]) {
										$isSelected = " selected";
									}

									echo "<option value='".json_encode($timesheetWeeksListValue["selected_range"])."'".$isSelected.">".$timesheetWeeksListValue['date_range']."</option>";

									unset($timesheetWeeksListKey, $timesheetWeeksListValue);
								}
							}

							unset($timesheetWeeksList);
						?>
						</select>
					</div>
				</div>
				<div class="row main-section-row multiple-month-input hidden">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Month :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							
							<input type="text" name="customized-month-list" class="form-control customized-month-list" value="<?php if (isset($_REQUEST['customized-month-list'])) { echo $_REQUEST['customized-month-list']; } ?>" placeholder="MM/YYYY" autocomplete="off" disabled>
							
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						</div>
					</div>
				</div>
				<div class="row main-section-row">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Timesheet View Type :</label>
						<select name="timesheet-view-type" class="customized-selectbox-without-all" autocomplete="off" required>
						<?php
							$timesheetViewType = array("Unsynced", "Synced");

							foreach ($timesheetViewType as $timesheetViewTypeKey => $timesheetViewTypeValue) {
								$isSelected = "";

								if ($_REQUEST["timesheet-view-type"] == $timesheetViewTypeValue) {
									$isSelected = " selected";
								}

								echo "<option value='".$timesheetViewTypeValue."'".$isSelected.">".$timesheetViewTypeValue."</option>";

								unset($timesheetViewTypeKey, $timesheetViewTypeValue);
							}

							unset($timesheetViewType);
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
		$timeEntriesData = $timesheetData = array();

		if (isset($_REQUEST["customized-month-list"])) {
			$monthsDataGiven = explode("/", $_REQUEST["customized-month-list"]);
			$monthsDataModified = $monthsDataGiven[1]."-".$monthsDataGiven[0];

			$filterId = 2;
			$startDate = date("Y-m-01", strtotime($monthsDataModified));
			$endDate = date("Y-m-t", strtotime($monthsDataModified));

			unset($monthsDataGiven, $monthsDataModified);

			echo "<script>
				$(document).ready(function(){
					$('.month-button').trigger('click');
				});
			</script>";
		} else {
			$weekData = json_decode($_REQUEST["customized-week-list"], true);

			$filterId = 1;
			$startDate = $weekData["start_date"];
			$endDate = $weekData["end_date"];

			unset($weekData);
		}

		$timesheetViewType = $_REQUEST["timesheet-view-type"];

		function getDecimalHours($time) {
	        $timeArray = explode(":", $time);
	        return ($timeArray[0] + ($timeArray[1] / 60) + ($timeArray[2] / 3600));
	    }

		function differenceBetweenInTimeAndOutTime($inTime, $outTime) {
	        $inTimeHours = $inTimeMinutes = $inTimeDiffMinutes = $outTimeHours = $outTimeMinutes = $diffTimeHours = $diffTimeMinutes = $diffTimeObjectInDecimalHours = 0;

	        $diffTimeObject = "";

	        $explodedInTime = $explodedOutTime = array();

	        $explodedInTime = explode(":", $inTime);
	        $inTimeHours = $explodedInTime[0] + 0;
	        $inTimeMinutes = $explodedInTime[1] + 0;

	        $explodedOutTime = explode(":", $outTime);
	        $outTimeHours = $explodedOutTime[0] + 0;
	        $outTimeMinutes = $explodedOutTime[1] + 0;

	        $diffTimeHours = $outTimeHours - $inTimeHours;

	        if ($inTimeHours == $outTimeHours) {
	            if ($inTimeMinutes > $outTimeMinutes) {
	                $diffTimeHours = 23;
	            }
	        } else if ($inTimeHours < $outTimeHours) {
	            if ($inTimeMinutes > $outTimeMinutes) {
	                $diffTimeHours = $diffTimeHours - 1;
	            }
	        } else if ($inTimeHours > $outTimeHours) {
	            $diffTimeHours = (24 - $inTimeHours) + $outTimeHours;

	            if ($inTimeMinutes > $outTimeMinutes) {
	                $diffTimeHours = $diffTimeHours - 1;
	            }
	        }

	        if ($inTimeMinutes == $outTimeMinutes) {
	            $diffTimeMinutes = 0;
	        } else if ($inTimeMinutes < $outTimeMinutes) {
	            $diffTimeMinutes = $outTimeMinutes - $inTimeMinutes;
	        } else if ($inTimeMinutes > $outTimeMinutes) {
	            $diffTimeMinutes = (60 - $inTimeMinutes) + $outTimeMinutes;
	        }

	        if ($diffTimeHours < 10) {
	            $diffTimeHours = "0".$diffTimeHours;
	        }

	        if ($diffTimeMinutes < 10) {
	            $diffTimeMinutes = "0".$diffTimeMinutes;
	        }

	        $diffTimeObject = $diffTimeHours.":".$diffTimeMinutes.":00";

	        $diffTimeObjectInDecimalHours = getDecimalHours($diffTimeObject);

	        return $diffTimeObjectInDecimalHours;
	    }

		$timeEntriesQuery = mysqli_query($allConn, "SELECT
			ete.employee AS employee_id,
            DATE_FORMAT(ete.date_start, '%Y-%m-%d') AS given_date,
            ete.time_start,
            ete.time_end,
            ete.details AS time_details
        FROM
            vtechhrm.employeetimeentry AS ete
        WHERE
            DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
        GROUP BY employee_id, given_date");

        if (mysqli_num_rows($timeEntriesQuery) > 0) {
			while ($timeEntriesRow = mysqli_fetch_array($timeEntriesQuery)) {
				$regularTime = differenceBetweenInTimeAndOutTime($timeEntriesRow["time_start"], $timeEntriesRow["time_end"]);

	            $explodedBreakTimeDetails = explode("BreakHours :", $timeEntriesRow["time_details"]);
	            $explodedBreakTimeList = count($explodedBreakTimeDetails) > 1 ? explode(",", $explodedBreakTimeDetails[1]) : array();
	            $explodedBreakTime = count($explodedBreakTimeList) > 0 ? trim($explodedBreakTimeList[0]) : "00:00:00";
	            $breakTime = getDecimalHours($explodedBreakTime);

	            $workedTime = round(($regularTime - $breakTime), 2);

	            $explodedOverTimeDetails = explode("OvertimeHours :", $timeEntriesRow["time_details"]);
	            $explodedOverTimeList = count($explodedOverTimeDetails) > 1 ? explode(",", $explodedOverTimeDetails[1]) : array();
	            $explodedOverTime = count($explodedOverTimeList) > 0 ? trim($explodedOverTimeList[0]) : "00:00:00";
	            $overTime = getDecimalHours($explodedOverTime);

	            $totalTime = $workedTime + $overTime;

				$timeEntriesData[$timeEntriesRow["employee_id"]][] = array(
					"given_date" => $timeEntriesRow["given_date"],
					"regular_time" => $regularTime,
                    "break_time" => $breakTime,
                    "worked_time" => $workedTime,
                    "over_time" => $overTime,
                    "total_time" => $totalTime
				);

				unset($regularTime, $explodedBreakTimeDetails, $explodedBreakTimeList, $explodedBreakTime, $breakTime, $workedTime, $explodedOverTimeDetails, $explodedOverTimeList, $explodedOverTime, $overTime, $totalTime);
			}
		}

		$searchTimesheetQuery = "SELECT
		    e.id AS hrm_employee_id,
		    CONCAT(e.first_name,' ',e.last_name) AS hrm_employee_name,
		    IF((e.employment_status = 1 OR e.employment_status = 4), 'W2', IF((e.employment_status = 2 OR e.employment_status = 5), '1099', IF((e.employment_status = 3 OR e.employment_status = 6), 'C2C', 'None'))) AS hrm_employment_status,
		    IF(CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) != '', CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)), 0) AS hrm_billrate,
		    IF(CAST(replace(e.overtime_billrate,'$','') AS DECIMAL (10,2)) != '', CAST(replace(e.overtime_billrate,'$','') AS DECIMAL (10,2)), 0) AS hrm_overtime_billrate,";

		if ($filterId == 1) {
			$searchTimesheetQuery .= " IF((SELECT IF(tal.level = 2, 'Yes', '') FROM vtech_tools.timesheet_activity_log AS tal WHERE tal.timesheetId = ete.timesheet ORDER BY tal.id DESC LIMIT 1) != '', 'Approved', '') AS hrm_employee_timesheet_status,";
		} else {
			$searchTimesheetQuery .= " (SELECT COUNT(DISTINCT ets.id) FROM vtechhrm.employeetimesheets AS ets WHERE ets.employee = e.id AND ((ets.date_end BETWEEN '$startDate' AND '$endDate') OR (ets.date_start BETWEEN '$startDate' AND '$endDate'))) AS hrm_timesheet_count,
			(SELECT COUNT(DISTINCT tal1.id) FROM vtech_tools.timesheet_activity_log AS tal1 JOIN (SELECT MAX(tal2.id) AS max_id FROM vtech_tools.timesheet_activity_log AS tal2 WHERE tal2.start_date >= '$startDate' AND tal2.end_date <= '$endDate' GROUP BY tal2.timesheetId) AS tal3 ON tal3.max_id = tal1.id WHERE tal1.candidateId = e.id AND tal1.start_date >= '$startDate' AND tal1.end_date <= '$endDate' AND tal1.level = 2) AS hrm_approved_timesheet_count,";
		}

		$searchTimesheetQuery .= " c.company_id AS hrm_client_id,
		    c.name AS hrm_client_name,
		    cf.mspChrg_pct AS client_msp_fees_percentage,
		    cf.mspChrg_dlr AS client_msp_fees_dollar,
		    qbm.mapping_id AS qb_consultant_id,
		    qbm.mapping_name AS qb_consultant_name,
		    qbm.mapping_type AS qb_consultant_type,
		    qbm2.mapping_id AS qb_service_id,
		    qbm2.mapping_name AS qb_service_name,
		    qbm3.mapping_id AS qb_customer_id,
		    qbm3.mapping_name AS qb_customer_name,
		    qbm4.mapping_id AS qb_filter_id,
		    qbm4.mapping_name AS qb_filter_name,
		    qbm5.mapping_id AS qb_sync_id,
		    qbm5.mapping_name AS qb_sync_name,
		    IF(qbm6.mapping_name != '', qbm6.mapping_name, 'Yes') AS qb_msp_status,
		    IF(qbm7.mapping_name != '', qbm7.mapping_name, 'Yes') AS qb_auto_sync,
		    (SELECT qt.id FROM quickbook.quickbook_timesheet AS qt WHERE qt.hrm_employee_id = e.id AND qt.qb_filter_id = '$filterId' AND qt.date_start = '$startDate' AND qt.date_end = '$endDate' ORDER BY qt.id DESC LIMIT 1) AS quickbook_timesheet_id
		FROM
		    vtechhrm.employees AS e
		    
		    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
		    LEFT JOIN cats.company AS c ON c.company_id = si.c_company_id
		    LEFT JOIN vtech_mappingdb.client_fees AS cf ON cf.client_id = c.company_id

		    LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id

		    LEFT JOIN quickbook.quickbook_mapping AS qbm ON qbm.reference_id = e.id AND qbm.reference_type = 'Employee' AND qbm.mapping_type IN ('Employee','Vendor')

		    LEFT JOIN quickbook.quickbook_mapping AS qbm2 ON qbm2.reference_id = e.id AND qbm2.reference_type = 'Employee' AND qbm2.mapping_type = 'Service'

		    LEFT JOIN quickbook.quickbook_mapping AS qbm3 ON qbm3.reference_id = e.id AND qbm3.reference_type = 'Employee' AND qbm3.mapping_type = 'Customer'

		    LEFT JOIN quickbook.quickbook_mapping AS qbm4 ON qbm4.reference_id = qbm3.mapping_id AND qbm4.reference_type = 'Customer' AND qbm4.mapping_type = 'Filter'

		    LEFT JOIN quickbook.quickbook_mapping AS qbm5 ON qbm5.reference_id = qbm3.mapping_id AND qbm5.reference_type = 'Customer' AND qbm5.mapping_type = 'Sync'

		    LEFT JOIN quickbook.quickbook_mapping AS qbm6 ON qbm6.reference_id = qbm3.mapping_id AND qbm6.reference_type = 'Customer' AND qbm6.mapping_type = 'MSP Status'

		    LEFT JOIN quickbook.quickbook_mapping AS qbm7 ON qbm7.reference_id = qbm3.mapping_id AND qbm7.reference_type = 'Customer' AND qbm7.mapping_type = 'Auto Sync'
		WHERE
		    qbm4.mapping_id = '$filterId'
		AND
		    DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		AND
		    ete.time_start != ete.time_end
		AND
		    (ete.time_start != '' OR ete.time_end != '')
		GROUP BY hrm_employee_id";

		if ($timesheetViewType == "Unsynced") {
			$searchTimesheetQuery .= " HAVING quickbook_timesheet_id IS NULL";
		} else {
			$searchTimesheetQuery .= " HAVING quickbook_timesheet_id != ''";
		}

		$searchTimesheetQuery .= " ORDER BY hrm_employee_name ASC";

		$searchTimesheetResult = mysqli_query($allConn, $searchTimesheetQuery);

		if (mysqli_num_rows($searchTimesheetResult) > 0) {
			while ($searchTimesheetRow = mysqli_fetch_array($searchTimesheetResult)) {
				$hrmBillRate = $searchTimesheetRow["hrm_billrate"];
				$hrmOverTimeBillRate = $searchTimesheetRow["hrm_overtime_billrate"];

				if ($searchTimesheetRow["qb_msp_status"] != "No") {
					if ($searchTimesheetRow["client_msp_fees_percentage"] > 0) {
			            $finalRate = (($hrmBillRate * $searchTimesheetRow["client_msp_fees_percentage"]) / 100);
			            $hrmBillRate = $hrmBillRate - $finalRate;

			            unset($finalRate);
			        }

			        if ($searchTimesheetRow["client_msp_fees_dollar"] > 0) {
			            $hrmBillRate = $hrmBillRate - $searchTimesheetRow["client_msp_fees_dollar"];
			        }
				}

		        $hrmBillRate = number_format((float)$hrmBillRate, 2, ".", "");

		        $totalTimesheetTime = $totalTimesheetAmount = array();

		        foreach ($timeEntriesData[$searchTimesheetRow["hrm_employee_id"]] as $timeEntriesDataKey => $timeEntriesDataValue) {
		        	$totalTimesheetTime[] = $timeEntriesDataValue["total_time"];

		        	if ($hrmOverTimeBillRate != "0.00") {
		        		$totalTimesheetAmount[] = $timeEntriesDataValue["worked_time"] * $hrmBillRate;

		        		$totalTimesheetAmount[] = $timeEntriesDataValue["over_time"] * $hrmOverTimeBillRate;
		        	} else {
		        		$totalTimesheetAmount[] = $timeEntriesDataValue["total_time"] * $hrmBillRate;
		        	}

		        	unset($timeEntriesDataKey, $timeEntriesDataValue);
		        }

				if ($filterId == 1) {
					$isLevel2Done = $searchTimesheetRow["hrm_employee_timesheet_status"] == "Approved" ? "Yes" : "No";
				} else {
					$isLevel2Done = $searchTimesheetRow["hrm_timesheet_count"] == $searchTimesheetRow["hrm_approved_timesheet_count"] ? "Yes" : "No";
				}

				$timesheetData[] = array(
					"hrm_employee_id" => $searchTimesheetRow["hrm_employee_id"],
					"hrm_employee_name" => $searchTimesheetRow["hrm_employee_name"],
					"hrm_employment_status" => $searchTimesheetRow["hrm_employment_status"],
					"hrm_billrate" => $hrmBillRate,
					"hrm_overtime_billrate" => $hrmOverTimeBillRate,
					"date_range_type" => $filterId == 1 ? "Week" : "Month",
					"total_timesheet_time" => round(array_sum($totalTimesheetTime), 2),
					"total_timesheet_amount" => round(array_sum($totalTimesheetAmount), 2),
					"is_level2_done" => $isLevel2Done,
					"hrm_client_id" => $searchTimesheetRow["hrm_client_id"],
					"hrm_client_name" => $searchTimesheetRow["hrm_client_name"],
					"client_msp_fees_percentage" => $searchTimesheetRow["client_msp_fees_percentage"],
					"client_msp_fees_dollar" => $searchTimesheetRow["client_msp_fees_dollar"],
					"qb_consultant_id" => $searchTimesheetRow["qb_consultant_id"],
					"qb_consultant_name" => $searchTimesheetRow["qb_consultant_name"],
					"qb_consultant_type" => $searchTimesheetRow["qb_consultant_type"],
					"qb_service_id" => $searchTimesheetRow["qb_service_id"],
					"qb_service_name" => $searchTimesheetRow["qb_service_name"],
					"qb_customer_id" => $searchTimesheetRow["qb_customer_id"],
					"qb_customer_name" => $searchTimesheetRow["qb_customer_name"],
					"qb_filter_id" => $searchTimesheetRow["qb_filter_id"],
					"qb_filter_name" => $searchTimesheetRow["qb_filter_name"],
					"qb_sync_id" => $searchTimesheetRow["qb_sync_id"],
					"qb_sync_name" => $searchTimesheetRow["qb_sync_name"],
					"qb_msp_status" => $searchTimesheetRow["qb_msp_status"],
					"qb_auto_sync" => $searchTimesheetRow["qb_auto_sync"],
					"quickbook_timesheet_id" => $searchTimesheetRow["quickbook_timesheet_id"],
					"selected_month" => date("F%20Y", strtotime($endDate))
				);

				unset($hrmBillRate, $hrmOverTimeBillRate, $totalTimesheetTime, $totalTimesheetAmount, $isLevel2Done);
			}
		}
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th colspan="6">HRM</th>
								<th colspan="7">Quickbooks</th>
								<!-- <th nowrap rowspan="2">Mark<br>as<br><?php /*echo $timesheetViewType == "Synced" ? "Unsynced" : "Synced";*/ ?></th> -->
							</tr>
							<tr class="thead-tr-style">
								<th>Employee</th>
								<th>Client</th>
								<th data-toggle="tooltip" data-placement="top" title="Bill Rate - MSP Fees">Bill<br>Rate</th>
								<th nowrap>OT Bill<br>Rate</th>
								<th>Total<br>Time</th>
								<!-- <th>Total<br>Amount</th> -->
								<th nowrap>Level-2<br>Done?</th>
								<th>Candidate</th>
								<th>Service</th>
								<th>Customer</th>
								<th>Filter<br>Type</th>
								<th>Sync<br>Type</th>
								<th>Deduct<br>MSP?</th>
								<th>Auto<br>Sync?</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach ($timesheetData as $timesheetDataKey => $timesheetDataValue) {
								$timeSheetLayerLink = "https://hrm.vtechsolution.com/modules/time_sheets/timeSheetLayer/index.php?client_id=&employee_id=".$timesheetDataValue["hrm_employee_id"]."&project_id=7&selected_month=".$timesheetDataValue["selected_month"];

								$dataMark = "mark_type=minus&quickbook_timesheet_id=".$timesheetDataValue["quickbook_timesheet_id"];

								if ($timesheetViewType == "Unsynced") {
									$dataMark = "mark_type=plus&hrm_employee_id=".$timesheetDataValue["hrm_employee_id"]."&hrm_employee_name=".urlencode($timesheetDataValue["hrm_employee_name"])."&hrm_employment_status=".$timesheetDataValue["hrm_employment_status"]."&hrm_billrate=".$timesheetDataValue["hrm_billrate"]."&hrm_overtime_billrate=".$timesheetDataValue["hrm_overtime_billrate"]."&qb_consultant_id=".$timesheetDataValue["qb_consultant_id"]."&qb_consultant_name=".urlencode($timesheetDataValue["qb_consultant_name"])."&qb_consultant_type=".$timesheetDataValue["qb_consultant_type"]."&qb_service_id=".$timesheetDataValue["qb_service_id"]."&qb_service_name=".urlencode($timesheetDataValue["qb_service_name"])."&qb_customer_id=".$timesheetDataValue["qb_customer_id"]."&qb_customer_name=".urlencode($timesheetDataValue["qb_customer_name"])."&qb_filter_id=".$timesheetDataValue["qb_filter_id"]."&qb_filter_name=".$timesheetDataValue["qb_filter_name"]."&qb_sync_id=".$timesheetDataValue["qb_sync_id"]."&qb_sync_name=".$timesheetDataValue["qb_sync_name"]."&date_start=".$startDate."&date_end=".$endDate."&creation_type=Manually";
								}
						?>
							<tr class="tbody-tr-style">
								<td><?php echo trim($timesheetDataValue["hrm_employee_name"]); ?><span class="sub-td-span"><?php echo $timesheetDataValue["hrm_employment_status"]; ?></span></td>
								<td><?php echo trim($timesheetDataValue["hrm_client_name"]); ?></td>
								<td nowrap><?php echo $timesheetDataValue["hrm_billrate"]; ?></td>
								<td nowrap><?php echo $timesheetDataValue["hrm_overtime_billrate"]; ?></td>
								<td nowrap><a href="<?php echo $timeSheetLayerLink; ?>" target="_blank"><?php echo $timesheetDataValue["total_timesheet_time"]; ?></a></td>
								<!-- <td nowrap><?php /*echo $timesheetDataValue["total_timesheet_amount"];*/ ?></td> -->
								<td nowrap <?php echo $timesheetDataValue["is_level2_done"] == "No" ? "style='color : red;'" : ""; ?>><?php echo $timesheetDataValue["is_level2_done"]; ?></td>
								<td <?php echo $timesheetDataValue["qb_consultant_name"] == "No" ? "style='background-color : red;'" : ""; ?>><?php echo $timesheetDataValue["qb_consultant_name"]; ?><span class="sub-td-span"><?php echo $timesheetDataValue["qb_consultant_type"]; ?></span></td>
								<td <?php echo $timesheetDataValue["qb_service_name"] == "No" ? "style='background-color : red;'" : ""; ?>><?php echo $timesheetDataValue["qb_service_name"]; ?></td>
								<td <?php echo $timesheetDataValue["qb_customer_name"] == "No" ? "style='background-color : red;'" : ""; ?>><?php echo $timesheetDataValue["qb_customer_name"]; ?></td>
								<td nowrap <?php echo $timesheetDataValue["qb_filter_name"] == "No" ? "style='background-color : red;'" : ""; ?>><?php echo $timesheetDataValue["qb_filter_name"]; ?></td>
								<td nowrap <?php echo $timesheetDataValue["qb_sync_name"] == "No" ? "style='background-color : red;'" : ""; ?>><?php echo $timesheetDataValue["qb_sync_name"]; ?></td>
								<td nowrap><?php echo $timesheetDataValue["qb_msp_status"]; ?></td>
								<td nowrap <?php echo $timesheetDataValue["qb_auto_sync"] == "No" ? "style='color : red;'" : ""; ?>><?php echo $timesheetDataValue["qb_auto_sync"]; ?></td>
								<!-- <td><i class="fa <?php /*echo $timesheetViewType == 'Synced' ? 'fa-minus-circle' : 'fa-plus-circle';*/ ?> mark-quickbook-timesheet" data-mark="<?php /*echo $dataMark;*/ ?>"></i></td> -->
							</tr>
						<?php
								unset($timeSheetLayerLink, $dataMark);
							}
						?>
						</tbody>
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

        $(".customized-month-list").datepicker({
            format: "mm/yyyy",
            startView: 1,
            minViewMode: 1,
            maxViewMode: 2,
            clearBtn: true,
            multidate: false,
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

	$(document).on("click", ".month-button", function(e){
		e.preventDefault();
		$(".customized-month-list").prop("required", true);
		$(".customized-week-list").prop("required", false);
		$(".customized-month-list").prop("disabled", false);
		$(".multiple-month-input").removeClass("hidden");
		$(".multiple-week-input").addClass("hidden");
		$(".month-button").addClass("dark-button");
		$(".month-button").removeClass("smooth-button");
		$(".week-button").addClass("smooth-button");
		$(".week-button").removeClass("dark-button");
	});

	$(document).on("click", ".week-button", function(e){
		e.preventDefault();
		$(".customized-month-list").prop("required", false);
		$(".customized-week-list").prop("required", true);
		$(".customized-month-list").prop("disabled", true);
		$(".multiple-month-input").addClass("hidden");
		$(".multiple-week-input").removeClass("hidden");
		$(".month-button").addClass("smooth-button");
		$(".month-button").removeClass("dark-button");
		$(".week-button").addClass("dark-button");
		$(".week-button").removeClass("smooth-button");
	});

	$(document).on("click", ".mark-quickbook-timesheet", function(e){
		e.preventDefault();

		let thisData = $(this);

		let postData = thisData.data("mark");

		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/mark-quickbook-timesheet.php",
			data: postData,
			beforeSend: function() {
				$("#divLoading").addClass("show");
			},
			success: function(response) {
				$("#divLoading").removeClass("show");

				if ($.trim(response) == "success") {
					thisData.closest("tr").remove();
				} else {
					alert("Oops, Something wrong.");
				}
			},
			error: function(response) {
				$("#divLoading").removeClass("show");

				alert("Oops, Something wrong.");
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
