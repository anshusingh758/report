<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
	
    if (isset($user) && isset($userMember)) {
		$reportId = "75";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>ROI Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th {
			border: 1px solid #aaa;
		}
		table.dataTable thead th,
		table.dataTable tfoot th,
		table.dataTable tfoot td {
			padding: 3px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td {
			padding: 2px 1px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable thead tr:nth-child(2) th:last-child,
		table.dataTable thead tr:nth-child(3) th:last-child {
			border-right: 1px solid #ddd;
		}
		table.dataTable tbody tr td:first-child {
			text-align: left;
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
			margin-bottom: 100px;
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
		.p-0 {
			padding: 0;
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
		.hyper-link-text {
			font-weight: bold;
			cursor: pointer;
		}
		.modal-header {
			color: #fff;
			font-size: 20px;
			font-weight: bold;
			background-color: #2266AA;
			padding: 10px;
			text-align: center;
		}
		.view-reward-popup .modal-lg {
			width: calc(100% - 100px);
		}
		.view-reward-popup .modal-header {
			background-color: #2266AA;
			color: #fff;
			font-weight: bold;
			text-align: center;
		}
		table.scrollable-datatable thead tr:nth-child(1) {
			padding: 4px 0px;
			text-align: center;
			vertical-align: middle;
			font-size: 15px;
			color: #2266AA;
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
						<li>This Report is useful for viewing ROI detail of all department for selected daterange.
							<ul>
								<li>Here, You can view individual teams personnel's ROI related information.</li>
							</ul>
						</li>
						<li>
							<span style="color: green;">Expected ROI Formula</span>
							<ul>
								<li>(((Salary * Department Rate) / Total Days) * Worked Days)</li>
							</ul>
						</li>
						<li>
							<span style="color: red;">Personal name column with red box</span>
						 	<ul>
						 		<li>It defines that personal is terminated / not active.</li>
						 	</ul>
						 </li>
						<li>
							<span style="color: red;">Achieved ROI Stage column with NA</span>
						 	<ul>
						 		<li>It defines that salary field for that candidate is empty or their email address is not mapped properly with HRM / other portal.</li>
						 	</ul>
						 </li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">ROI Report</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container">
			<form class="form-submit-action" action="index.php" method="post">
				<div class="row">
					<div class="col-md-2">
						<button type="button" class="notes-popup-button" data-toggle="modal" data-target=".notes-popup"><i class="fa fa-fw fa-commenting-o"></i> Guidelines</button>
					</div>
					<div class="col-md-4 col-md-offset-2">
						<label>Select Year :</label>
						<select class="customized-selectbox-without-all" name="year-list" required>
							<?php
								for ($yearCounter = date("Y"); $yearCounter >= 2019; $yearCounter--) {
									$isSelected = "";
									if (isset($_REQUEST["year-list"])) {
										if ($yearCounter == $_REQUEST["year-list"]) {
											$isSelected = " selected";
										}
									} else {
										if ($yearCounter == date("Y")) {
											$isSelected = " selected";
										}
									}

									echo "<option value='".$yearCounter."'".$isSelected.">".$yearCounter."</option>";
								}
							?>
						</select>
					</div>
					<div class="col-md-4">
						<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
					</div>
				</div>
				<div class="row main-section-row">
					<div class="col-md-4 col-md-offset-4">
						<div class="this-month-div">
							<label for="this-month-id"> Search Report by : </label>
							<select class="search-report-by-input" name="search-report-by-input" style="cursor: pointer;" required>
							<?php
								$searchReportByOption = array("Year", "Month", "Quarter");
								
								foreach ($searchReportByOption as $searchReportByOptionKey => $searchReportByOptionValue) {
									$isSelected = "";
									if ($_REQUEST['search-report-by-input'] == $searchReportByOptionValue) {
										$isSelected = " selected";
									}
									
									echo "<option value='".$searchReportByOptionValue."'".$isSelected.">".$searchReportByOptionValue."</option>";
								}
							?>
							</select>
						</div>
					</div>
				</div>
				<div class="row main-section-row month-list-row hidden">
					<div class="col-md-4 col-md-offset-4">
						<select class="customized-selectbox-without-all month-list" name="month-list[]" multiple>
							<?php
								$monthList = array(
									"01" => "January",
									"02" => "February",
									"03" => "March",
									"04" => "April",
									"05" => "May",
									"06" => "June",
									"07" => "July",
									"08" => "August",
									"09" => "September",
									"10" => "October",
									"11" => "November",
									"12" => "December"
								);
								foreach ($monthList as $monthListKey => $monthListValue) {
									$isSelected = "";
									if ($_REQUEST['search-report-by-input'] == "Month" && (in_array($monthListKey, $_REQUEST["month-list"]))) {
										$isSelected = " selected";
									}
									echo "<option value='".$monthListKey."'".$isSelected.">".$monthListValue."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row main-section-row quarter-list-row hidden">
					<div class="col-md-4 col-md-offset-4">
						<select class="customized-selectbox-without-all quarter-list" name="quarter-list[]" multiple>
							<?php
								$quarterList = array(
									"Q1" => "Q1",
									"Q2" => "Q2",
									"Q3" => "Q3",
									"Q4" => "Q4"
								);
								foreach ($quarterList as $quarterListKey => $quarterListValue) {
									$isSelected = "";
									if ($_REQUEST['search-report-by-input'] == "Quarter" && (in_array($quarterListKey, $_REQUEST["quarter-list"]))) {
										$isSelected = " selected";
									}
									echo "<option value='".$quarterListKey."'".$isSelected.">".$quarterListValue."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row main-section-row">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Department :</label>
						<select class="customized-selectbox-without-all department-list" name="department-list" required>
							<option value="">Select Option</option>
							<?php
								$departmentList = hrmIndiaDepartmentRateinfo($allConn);

								foreach ($departmentList as $departmentListKey => $departmentListValue) {
									$isSelected = "";
									if ($_REQUEST["department-list"] == json_encode($departmentListValue["department_detail"])) {
										$isSelected = " selected";
									}
									echo "<option value='".json_encode($departmentListValue["department_detail"])."'".$isSelected.">".$departmentListValue["department_name"]."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row" style="margin-top: 7px;">
					<div class="col-md-4 col-md-offset-4">
						<div class="this-month-div">
							<input name="this-month-input" type="checkbox" id="this-month-id" <?php if (isset($_REQUEST['this-month-input'])) { echo 'checked'; } ?>>
							<label for="this-month-id"> Only Count Candidate Started This Year</label>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 10px;">
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
	function getDecryptedValue($value) {
        $key = 'password to (en/de)crypt';
     
        $data = base64_decode($value);
        
        $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

        $decryptedValue = rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128,
                hash('sha256', $key, true),
                substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
                MCRYPT_MODE_CBC,
                $iv
            ),
            "\0"
        );

        return $decryptedValue;
    }

    function clean($string) {
	   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

	   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}

	if (isset($_REQUEST["form-submit-button"])) {
		$dateRange = $departmentList = $rewardPoint = $rewardPointBrief = $managerExpectedGPTarget = $managerExpectedPlacementTarget = $managerAchievedGpTarget = $managerAchievedPlacementTarget = $directorExpectedGPTarget = $directorAchievedGpTarget = $directorExpectedPlacementTarget = $managerAchievedPlacementTarget = $finalArray = $finalData = $finalDataItem = array();

		echo "<script>
			$(document).ready(function(){
				$('.search-report-by-input').trigger('change');
			});
		</script>";

		$yearList = $_POST["year-list"];
		$yearStartDate = $yearList."-01-01";
		$yearEndDate = $yearList."-12-31";

		$dateRangeType = $_POST["search-report-by-input"];

		if ($dateRangeType == "Year") {
			$dateRange[] = array(
				"start_date" => $yearList."-01-01",
				"end_date" => $yearList."-12-31",
				"date_range_value" => "Year",
				"given_days" => 365
			);
		} elseif ($dateRangeType == "Month") {
			foreach ($_POST["month-list"] as $monthListKey => $monthListValue) {
				$dateRange[] = array(
					"start_date" => date("Y-m-01", strtotime($yearList."-".$monthListValue)),
					"end_date" => date("Y-m-t", strtotime($yearList."-".$monthListValue)),
					"date_range_value" => $monthListValue."-".$yearList,
					"given_days" => 30
				);
			}
		} else {
			foreach ($_POST["quarter-list"] as $quarterListKey => $quarterListValue) {
				if ($quarterListValue == "Q1") {
					$dateRange[] = array(
						"start_date" => $yearList."-01-01",
						"end_date" => $yearList."-03-31",
						"date_range_value" => "Q1-".$yearList,
						"given_days" => 90
					);
				} elseif ($quarterListValue == "Q2") {
					$dateRange[] = array(
						"start_date" => $yearList."-04-01",
						"end_date" => $yearList."-06-30",
						"date_range_value" => "Q2-".$yearList,
						"given_days" => 90
					);
				} elseif ($quarterListValue == "Q3") {
					$dateRange[] = array(
						"start_date" => $yearList."-07-01",
						"end_date" => $yearList."-09-30",
						"date_range_value" => "Q3-".$yearList,
						"given_days" => 90
					);
				} else {
					$dateRange[] = array(
						"start_date" => $yearList."-10-01",
						"end_date" => $yearList."-12-31",
						"date_range_value" => "Q4-".$yearList,
						"given_days" => 90
					);
				}
			}
		}

		$departmentList = json_decode($_POST["department-list"], true);
		$departmentIdList = $departmentList["department_id"];
		$departmentName = $departmentList["department_name"];
		$departmentRate = $departmentList["department_rate"];

		if (isset($_REQUEST["this-month-input"])) {
			$includePeriod = "true";
		} else {
			$includePeriod = "false";
		}

		$taxSettingsTableData = taxSettingsTable($allConn);

		$delimiter = array("","[","]",'"');

		$managerListQuery = mysqli_query($allConn, "SELECT
			efs.extra_field_options
		FROM
			cats.extra_field_settings AS efs
		WHERE
			efs.field_name = 'Manager - Client Service'");

		$managerListRow = mysqli_fetch_array($managerListQuery);

		$managerListNameGroup = explode(",", str_replace("+", " ", $managerListRow['extra_field_options']));
		unset($managerListNameGroup[0]);

		$csDirectorNameQuery = mysqli_query($allConn, "SELECT
			CONCAT(u.first_name,' ',u.last_name) AS cs_director_name
		FROM 
			cats.user AS u
		    JOIN vtech_mappingdb.manage_cats_roles AS mcr ON mcr.user_id = u.user_id
		WHERE
			mcr.designation = 'Associate Director - Client Service'
		AND
			u.access_level != 0
		GROUP BY u.user_id");

		if (mysqli_num_rows($csDirectorNameQuery) > 0) {
			while ($csDirectorNameRow = mysqli_fetch_array($csDirectorNameQuery)) {
				$csDirectorName[] = $csDirectorNameRow["cs_director_name"];
			}
		}

        foreach ($dateRange as $dateRangeKey => $dateRangeValue) {
        	$startDate = $dateRangeValue["start_date"];
			$endDate = $dateRangeValue["end_date"];
			
			$mainData = array();

	        $employeeTimeEntryTableData = employeeTimeEntryTable($allConn, $startDate, $endDate);

			// Reward Point Process
			include("reward_point_process.php");

			$mainQUERY = mysqli_query($allConn, "SELECT
				mu.id AS personnel_id,
				mu.userfullname AS personnel_name,
			    mu.isactive AS personnel_status,
			    u.notes AS manager_name,
			    mu.emailaddress,
			    me.date_of_joining,
			    me.date_of_confirmation,
			    mep.gp_target AS expected_gp_target,
			    mep.placement_target AS expected_placement_target,
			    mep.salary_type AS personnel_salary_type,
			    mep.salary AS personnel_salary,
			    mep.sow_product_cost
			FROM
				vtechhrm_in.main_users AS mu
			    LEFT JOIN vtechhrm_in.main_employees AS me ON me.user_id = mu.id
				LEFT JOIN vtechhrm_in.main_empperformance AS mep ON mep.user_id = mu.id AND mep.isactive = 1 AND mep.year = '$yearList'
				LEFT JOIN cats.user AS u ON CONCAT(u.first_name,' ',u.last_name) = mu.userfullname
			WHERE
				me.department_id IN ($departmentIdList)
			AND
				me.date_of_joining <= '$endDate'
			GROUP BY mu.id
			ORDER BY mu.userfullname");

			if (mysqli_num_rows($mainQUERY) > 0) {
				while ($mainROW = mysqli_fetch_array($mainQUERY)) {
					$dateOfConfirmation = $mainROW["date_of_confirmation"];

					if ($mainROW["date_of_confirmation"] == "0001-01-01" || $mainROW["date_of_confirmation"] == "0000-00-00" || $mainROW["date_of_confirmation"] == "") {
						$dateOfConfirmation = date("Y-m-d", strtotime("+ 3 months", strtotime($mainROW["date_of_joining"])));
					}

					$givenDays = $dateRangeValue["given_days"];

					if (strtotime($dateOfConfirmation) >= strtotime($startDate) && strtotime($dateOfConfirmation) <= strtotime($endDate)) {
						$givenDaysDiff = date_diff(date_create($endDate), date_create($dateOfConfirmation));
						$givenDays = $givenDaysDiff->format("%a");
					} elseif (strtotime($dateOfConfirmation) > strtotime($endDate)) {
						$givenDays = 0;
					}

					$mainData[] = array(
						"personnel_id" => $mainROW["personnel_id"],
						"personnel_name" => ucwords(strtolower(trim($mainROW["personnel_name"]))),
						"personnel_status" => $mainROW["personnel_status"],
						"manager_name" => $mainROW["manager_name"],
						"emailaddress" => $mainROW["emailaddress"],
						"date_of_joining" => $mainROW["date_of_joining"],
						"date_of_confirmation" => $dateOfConfirmation,
						"given_date_of_confirmation" => $dateOfConfirmation,
						"expected_gp_target" => $mainROW["expected_gp_target"],
						"expected_placement_target" => $mainROW["expected_placement_target"],
						"personnel_salary" => $mainROW["personnel_salary"],
						"sow_product_cost" => $mainROW["sow_product_cost"],
						"given_days" => $givenDays
					);
				}
			}

			foreach ($mainData as $mainDataKey => $mainDataValue) {
				$totalGrossProfit = array();
				
				$expectedGPTarget = $achievedGpTarget = $achievedGpTargetPercentage = $expectedPlacementTarget = $achievedPlacementTarget = $achievedPlacementTargetPercentage = $personnelSalary = $expectedROI = $achievedROIPercentage = 0;

				$achievedROIStage = "NA";
				$achievedROIStageColor = "transparent";
				$achievedROIStageFontColor = "#000";

				$personnelName = $mainDataValue["personnel_name"];
				$emailaddress = $mainDataValue["emailaddress"];

				$expectedGPTarget = round((($mainDataValue["expected_gp_target"] / 365) * $mainDataValue["given_days"]), 2);
				$expectedPlacementTarget = round((($mainDataValue["expected_placement_target"] / 365) * $mainDataValue["given_days"]), 2);

				if ($departmentName == "CS Team") {
					if (!in_array($mainDataValue["personnel_name"], $managerListNameGroup) && !in_array($mainDataValue["personnel_name"], $csDirectorName)) {
						if ($expectedGPTarget == 0 || $expectedGPTarget == "") {
							$expectedGPTarget = ((10000 / 30) * $mainDataValue["given_days"]);
						}

						if ($expectedPlacementTarget == 0 || $expectedPlacementTarget == "") {
							$expectedPlacementTarget = ((1 / 30) * $mainDataValue["given_days"]);
						}
					}

					$managerExpectedGPTarget[$mainDataValue["manager_name"]][$dateRangeValue["date_range_value"]][] = round($expectedGPTarget, 2);
					$managerExpectedPlacementTarget[$mainDataValue["manager_name"]][$dateRangeValue["date_range_value"]][] = round($expectedPlacementTarget, 2);

					// CS Team Process
					include("cs_team_process.php");
				} elseif ($departmentName == "BDC Team" || $departmentName == "BDG Team") {
					// Sales Team Process
					include("sales_team_process.php");
				} elseif ($departmentName == "PS Team") {
					// PS Team Process
					include("ps_team_process.php");
				} elseif ($departmentName == "USBD Team") {
					// PS Team Process
					include("usbd_team_process.php");
				}

				// ROI Process
				include("roi_process.php");

				$finalArray[] = array(
					"personnel_id" => $mainDataValue["personnel_id"],
					"personnel_name" => $mainDataValue["personnel_name"],
					"personnel_status" => $mainDataValue["personnel_status"],
					"manager_name" => $mainDataValue["manager_name"],
					"date_range_type_value" => $dateRangeValue["date_range_value"],
					"date_of_joining" => $mainDataValue["date_of_joining"],
					"date_of_confirmation" => $mainDataValue["date_of_confirmation"],
					"reward_point" => array_sum($rewardPoint[$mainDataValue["personnel_id"]]),
					"expected_gp_target" => round($expectedGPTarget, 2),
					"achieved_gp_target" => $achievedGpTarget,
					"achieved_gp_target_percentage" => $achievedGpTargetPercentage,
					"expected_placement_target" => round($expectedPlacementTarget, 2),
					"achieved_placement_target" => $achievedPlacementTarget,
					"achieved_placement_target_percentage" => $achievedPlacementTargetPercentage,
					"personnel_salary" => $personnelSalary,
					"sow_product_cost" => $mainDataValue["sow_product_cost"],
					"given_days" => $mainDataValue["given_days"],
					"expected_roi" => $expectedROI,
					"achieved_roi" => $achievedGpTarget,
					"achieved_roi_percentage" => $achievedROIPercentage,
					"achieved_roi_stage" => $achievedROIStage,
					"achieved_roi_stage_color" => $achievedROIStageColor,
					"achieved_roi_stage_font_color" => $achievedROIStageFontColor
				);
			}
		}

		foreach ($finalArray as $finalArrayKey => $finalArrayValue) {
			if ($departmentName == "CS Team" && in_array($finalArrayValue["personnel_name"], $managerListNameGroup)) {
				$managerExpectedGPTarget2 = array_sum($managerExpectedGPTarget[$finalArrayValue["personnel_name"]][$finalArrayValue["date_range_type_value"]]);
				$managerAchievedGpTarget2 = array_sum($managerAchievedGpTarget[$finalArrayValue["personnel_name"]][$finalArrayValue["date_range_type_value"]]);

				if ($finalArrayValue["expected_gp_target"] <= $managerExpectedGPTarget2 && $finalArrayValue["expected_gp_target"] != 0 && $finalArrayValue["expected_gp_target"] != "") {
					$managerExpectedGPTarget2 = $finalArrayValue["expected_gp_target"];
				}

				if ($finalArrayValue["achieved_gp_target"] <= $managerAchievedGpTarget2 && $finalArrayValue["achieved_gp_target"] != 0 && $finalArrayValue["achieved_gp_target"] != "") {
					$managerAchievedGpTarget2 = $finalArrayValue["achieved_gp_target"];
				}

				$managerExpectedPlacementTarget2 = array_sum($managerExpectedPlacementTarget[$finalArrayValue["personnel_name"]][$finalArrayValue["date_range_type_value"]]);
				$managerAchievedPlacementTarget2 = array_sum($managerAchievedPlacementTarget[$finalArrayValue["personnel_name"]][$finalArrayValue["date_range_type_value"]]);

				if ($finalArrayValue["expected_placement_target"] > $managerExpectedPlacementTarget2) {
					$managerExpectedPlacementTarget2 = $finalArrayValue["expected_placement_target"];
				}

				if ($finalArrayValue["achieved_placement_target"] > $managerAchievedPlacementTarget2) {
					$managerAchievedPlacementTarget2 = $finalArrayValue["achieved_placement_target"];
				}

				if (in_array($finalArrayValue["personnel_name"], $csDirectorName)) {
					$managerExpectedGPTarget2 = $finalArrayValue["expected_gp_target"];
					$managerAchievedGpTarget2 = $finalArrayValue["achieved_gp_target"];
					$managerExpectedPlacementTarget2 = $finalArrayValue["expected_placement_target"];
					$managerAchievedPlacementTarget2 = $finalArrayValue["achieved_placement_target"];
				}

				$managerAchievedGpTargetPercentage = round((($managerAchievedGpTarget2 * 100) / $managerExpectedGPTarget2));
				
				$managerAchievedPlacementTargetPercentage = round((($managerAchievedPlacementTarget2 * 100) / $managerExpectedPlacementTarget2));

				if (in_array($finalArrayValue["manager_name"], $csDirectorName)) {
					$directorExpectedGPTarget[$finalArrayValue["manager_name"]][$finalArrayValue["date_range_type_value"]][] = round($managerExpectedGPTarget2, 2);
					
					$directorAchievedGpTarget[$finalArrayValue["manager_name"]][$finalArrayValue["date_range_type_value"]][] = round($managerAchievedGpTarget2, 2);

					$directorExpectedPlacementTarget[$finalArrayValue["manager_name"]][$finalArrayValue["date_range_type_value"]][] = round($managerExpectedPlacementTarget2, 2);

					$directorAchievedPlacementTarget[$finalArrayValue["manager_name"]][$finalArrayValue["date_range_type_value"]][] = round($managerAchievedPlacementTarget2, 2);
				}

				// Manager ROI Process
				include("manager_roi_process.php");

				$finalData[] = array(
					"personnel_id" => $finalArrayValue["personnel_id"],
					"personnel_name" => $finalArrayValue["personnel_name"],
					"personnel_status" => $finalArrayValue["personnel_status"],
					"manager_name" => $finalArrayValue["manager_name"],
					"date_range_type_value" => $finalArrayValue["date_range_type_value"],
					"date_of_joining" => $finalArrayValue["date_of_joining"],
					"date_of_confirmation" => $finalArrayValue["date_of_confirmation"],
					"reward_point" => $finalArrayValue["reward_point"],
					"expected_gp_target" => $managerExpectedGPTarget2,
					"achieved_gp_target" => $managerAchievedGpTarget2,
					"achieved_gp_target_percentage" => $managerAchievedGpTargetPercentage,
					"expected_placement_target" => $managerExpectedPlacementTarget2,
					"achieved_placement_target" => $managerAchievedPlacementTarget2,
					"achieved_placement_target_percentage" => $managerAchievedPlacementTargetPercentage,
					"personnel_salary" => $finalArrayValue["personnel_salary"],
					"sow_product_cost" => $finalArrayValue["sow_product_cost"],
					"given_days" => $finalArrayValue["given_days"],
					"expected_roi" => $expectedROI,
					"achieved_roi" => $managerAchievedGpTarget2,
					"achieved_roi_percentage" => $achievedROIPercentage,
					"achieved_roi_stage" => $achievedROIStage,
					"achieved_roi_stage_color" => $achievedROIStageColor,
					"achieved_roi_stage_font_color" => $achievedROIStageFontColor
				);
			} else {
				if ($departmentName == "CS Team" && in_array($finalArrayValue["manager_name"], $csDirectorName)) {
					$directorExpectedGPTarget[$finalArrayValue["manager_name"]][$finalArrayValue["date_range_type_value"]][] = round($finalArrayValue["expected_gp_target"], 2);
					
					$directorAchievedGpTarget[$finalArrayValue["manager_name"]][$finalArrayValue["date_range_type_value"]][] = round($finalArrayValue["achieved_gp_target"], 2);

					$directorExpectedPlacementTarget[$finalArrayValue["manager_name"]][$finalArrayValue["date_range_type_value"]][] = round($finalArrayValue["expected_placement_target"], 2);

					$directorAchievedPlacementTarget[$finalArrayValue["manager_name"]][$finalArrayValue["date_range_type_value"]][] = round($finalArrayValue["achieved_placement_target"], 2);
				}

				$finalData[] = array(
					"personnel_id" => $finalArrayValue["personnel_id"],
					"personnel_name" => $finalArrayValue["personnel_name"],
					"personnel_status" => $finalArrayValue["personnel_status"],
					"manager_name" => $finalArrayValue["manager_name"],
					"date_range_type_value" => $finalArrayValue["date_range_type_value"],
					"date_of_joining" => $finalArrayValue["date_of_joining"],
					"date_of_confirmation" => $finalArrayValue["date_of_confirmation"],
					"reward_point" => $finalArrayValue["reward_point"],
					"expected_gp_target" => $finalArrayValue["expected_gp_target"],
					"achieved_gp_target" => $finalArrayValue["achieved_gp_target"],
					"achieved_gp_target_percentage" => $finalArrayValue["achieved_gp_target_percentage"],
					"expected_placement_target" => $finalArrayValue["expected_placement_target"],
					"achieved_placement_target" => $finalArrayValue["achieved_placement_target"],
					"achieved_placement_target_percentage" => $finalArrayValue["achieved_placement_target_percentage"],
					"personnel_salary" => $finalArrayValue["personnel_salary"],
					"sow_product_cost" => $finalArrayValue["sow_product_cost"],
					"given_days" => $finalArrayValue["given_days"],
					"expected_roi" => $finalArrayValue["expected_roi"],
					"achieved_roi" => $finalArrayValue["achieved_roi"],
					"achieved_roi_percentage" => $finalArrayValue["achieved_roi_percentage"],
					"achieved_roi_stage" => $finalArrayValue["achieved_roi_stage"],
					"achieved_roi_stage_color" => $finalArrayValue["achieved_roi_stage_color"],
					"achieved_roi_stage_font_color" => $finalArrayValue["achieved_roi_stage_font_color"]
				);
			}
		}

		foreach ($finalData as $finalDataKey => $finalDataValue) {
			if ($departmentName == "CS Team" && in_array($finalDataValue["personnel_name"], $csDirectorName)) {
				$directorExpectedGPTarget2 = array_sum($directorExpectedGPTarget[$finalDataValue["personnel_name"]][$finalDataValue["date_range_type_value"]]);
				$directorAchievedGpTarget2 = array_sum($directorAchievedGpTarget[$finalDataValue["personnel_name"]][$finalDataValue["date_range_type_value"]]);

				if ($finalDataValue["expected_gp_target"] <= $directorExpectedGPTarget2 && $finalDataValue["expected_gp_target"] != 0 && $finalDataValue["expected_gp_target"] != "") {
					$directorExpectedGPTarget2 = $finalDataValue["expected_gp_target"];
				}

				if ($finalDataValue["achieved_gp_target"] <= $directorAchievedGpTarget2 && $finalDataValue["achieved_gp_target"] != 0 && $finalDataValue["achieved_gp_target"] != "") {
					$directorAchievedGpTarget2 = $finalDataValue["achieved_gp_target"];
				}

				$directorExpectedPlacementTarget2 = array_sum($directorExpectedPlacementTarget[$finalDataValue["personnel_name"]][$finalDataValue["date_range_type_value"]]);
				$directorAchievedPlacementTarget2 = array_sum($directorAchievedPlacementTarget[$finalDataValue["personnel_name"]][$finalDataValue["date_range_type_value"]]);

				if ($finalDataValue["expected_placement_target"] > $directorExpectedPlacementTarget2) {
					$directorExpectedPlacementTarget2 = $finalDataValue["expected_placement_target"];
				}

				if ($finalDataValue["achieved_placement_target"] > $directorAchievedPlacementTarget2) {
					$directorAchievedPlacementTarget2 = $finalDataValue["achieved_placement_target"];
				}

				$directorAchievedGpTargetPercentage = round((($directorAchievedGpTarget2 * 100) / $directorExpectedGPTarget2));
				
				$directorAchievedPlacementTargetPercentage = round((($directorAchievedPlacementTarget2 * 100) / $directorExpectedPlacementTarget2));

				// director ROI Process
				include("director_roi_process.php");

				$finalDataItem[] = array(
					"personnel_id" => $finalDataValue["personnel_id"],
					"personnel_name" => $finalDataValue["personnel_name"],
					"personnel_status" => $finalDataValue["personnel_status"],
					"manager_name" => $finalDataValue["manager_name"],
					"date_range_type_value" => $finalDataValue["date_range_type_value"],
					"date_of_joining" => $finalDataValue["date_of_joining"],
					"date_of_confirmation" => $finalDataValue["date_of_confirmation"],
					"reward_point" => $finalDataValue["reward_point"],
					"expected_gp_target" => $directorExpectedGPTarget2,
					"achieved_gp_target" => $directorAchievedGpTarget2,
					"achieved_gp_target_percentage" => $directorAchievedGpTargetPercentage,
					"expected_placement_target" => $directorExpectedPlacementTarget2,
					"achieved_placement_target" => $directorAchievedPlacementTarget2,
					"achieved_placement_target_percentage" => $directorAchievedPlacementTargetPercentage,
					"personnel_salary" => $finalDataValue["personnel_salary"],
					"sow_product_cost" => $finalDataValue["sow_product_cost"],
					"given_days" => $finalDataValue["given_days"],
					"expected_roi" => $expectedROI,
					"achieved_roi" => $directorAchievedGpTarget2,
					"achieved_roi_percentage" => $achievedROIPercentage,
					"achieved_roi_stage" => $achievedROIStage,
					"achieved_roi_stage_color" => $achievedROIStageColor,
					"achieved_roi_stage_font_color" => $achievedROIStageFontColor
				);
			} else {
				$finalDataItem[] = array(
					"personnel_id" => $finalDataValue["personnel_id"],
					"personnel_name" => $finalDataValue["personnel_name"],
					"personnel_status" => $finalDataValue["personnel_status"],
					"manager_name" => $finalDataValue["manager_name"],
					"date_range_type_value" => $finalDataValue["date_range_type_value"],
					"date_of_joining" => $finalDataValue["date_of_joining"],
					"date_of_confirmation" => $finalDataValue["date_of_confirmation"],
					"reward_point" => $finalDataValue["reward_point"],
					"expected_gp_target" => $finalDataValue["expected_gp_target"],
					"achieved_gp_target" => $finalDataValue["achieved_gp_target"],
					"achieved_gp_target_percentage" => $finalDataValue["achieved_gp_target_percentage"],
					"expected_placement_target" => $finalDataValue["expected_placement_target"],
					"achieved_placement_target" => $finalDataValue["achieved_placement_target"],
					"achieved_placement_target_percentage" => $finalDataValue["achieved_placement_target_percentage"],
					"personnel_salary" => $finalDataValue["personnel_salary"],
					"sow_product_cost" => $finalDataValue["sow_product_cost"],
					"given_days" => $finalDataValue["given_days"],
					"expected_roi" => $finalDataValue["expected_roi"],
					"achieved_roi" => $finalDataValue["achieved_roi"],
					"achieved_roi_percentage" => $finalDataValue["achieved_roi_percentage"],
					"achieved_roi_stage" => $finalDataValue["achieved_roi_stage"],
					"achieved_roi_stage_color" => $finalDataValue["achieved_roi_stage_color"],
					"achieved_roi_stage_font_color" => $finalDataValue["achieved_roi_stage_font_color"]
				);
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
								<th rowspan="2">Personnel</th>
							<?php if ($departmentName == "CS Team") { ?>
								<th rowspan="2">Manager</th>
							<?php } ?>
							<?php if ($dateRangeType == "Month" && count($_POST["month-list"]) > 1) { ?>
								<th rowspan="2">Months</th>
							<?php } elseif ($dateRangeType == "Quarter" && count($_POST["quarter-list"]) > 1) { ?>
								<th rowspan="2">Quarters</th>
							<?php } ?>
								<th colspan="2">Date of</th>
								<th rowspan="2">Reward Points</th>
								<th colspan="3">GP Target</th>
							<?php if ($departmentName == "CS Team") { ?>
								<th colspan="3">Placement Target</th>
							<?php } ?>
								<th rowspan="2">Salary</th>
								<th colspan="4">Achieved ROI</th>
							</tr>
							<tr class="thead-tr-style">
								<th>Joining</th>
								<th>Confirmation</th>
								<th>Expected</th>
								<th>Achieved</th>
								<th>Percentage(%)</th>
							<?php if ($departmentName == "CS Team") { ?>
								<th>Expected</th>
								<th>Achieved</th>
								<th>Percentage(%)</th>
							<?php } ?>
								<th data-toggle="tooltip" data-placement="top" title="(((Salary * Department Rate) / Total Days) * Worked Days)">Expected</th>
								<th>Achieved</th>
								<th>Percentage(%)</th>
								<th>Stage</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach ($finalDataItem as $finalDataItemKey => $finalDataItemValue) {
						?>
							<tr class="tbody-tr-style">
								<td <?php echo $finalDataItemValue["personnel_status"] != 1 ? "style='color: #fff;background-color: red;'" : ""; ?>><?php echo $finalDataItemValue["personnel_name"]; ?></td>
							<?php if ($departmentName == "CS Team") { ?>
								<td><?php echo $finalDataItemValue["manager_name"]; ?></td>
							<?php } ?>
							<?php if (($dateRangeType == "Month" && count($_POST["month-list"]) > 1) || ($dateRangeType == "Quarter" && count($_POST["quarter-list"]) > 1)) { ?>
								<td><?php echo $finalDataItemValue["date_range_type_value"]; ?></td>
							<?php } ?>
								<td><?php echo $finalDataItemValue["date_of_joining"] != "" ? date("m-d-Y", strtotime($finalDataItemValue["date_of_joining"])) : ""; ?></td>
								<td><?php echo $finalDataItemValue["date_of_confirmation"] != "" ? date("m-d-Y", strtotime($finalDataItemValue["date_of_confirmation"])) : ""; ?></td>
								<td><?php echo $finalDataItemValue["reward_point"] != "" ? $finalDataItemValue["reward_point"] : 0; ?></td>
								<td><?php echo $finalDataItemValue["expected_gp_target"] != "" ? $finalDataItemValue["expected_gp_target"] : 0; ?></td>
								<td><?php echo $finalDataItemValue["achieved_gp_target"] != "" ? $finalDataItemValue["achieved_gp_target"] : 0; ?></td>
								<td><?php echo $finalDataItemValue["achieved_gp_target_percentage"] != "" ? $finalDataItemValue["achieved_gp_target_percentage"] : 0; ?></td>
							<?php if ($departmentName == "CS Team") { ?>
								<td><?php echo $finalDataItemValue["expected_placement_target"] != "" ? $finalDataItemValue["expected_placement_target"] : 0; ?></td>
								<td><?php echo $finalDataItemValue["achieved_placement_target"] != "" ? $finalDataItemValue["achieved_placement_target"] : 0; ?></td>
								<td><?php echo $finalDataItemValue["achieved_placement_target_percentage"] != "" ? $finalDataItemValue["achieved_placement_target_percentage"] : 0; ?></td>
							<?php } ?>
								<td><?php echo $finalDataItemValue["personnel_salary"] != "" ? $finalDataItemValue["personnel_salary"] : ""; ?></td>
								<td><?php echo $finalDataItemValue["expected_roi"] != "" ? $finalDataItemValue["expected_roi"] : 0; ?></td>
								<td><?php echo $finalDataItemValue["achieved_roi"] != "" ? $finalDataItemValue["achieved_roi"] : 0; ?></td>
								<td><?php echo $finalDataItemValue["achieved_roi_percentage"] != "" ? $finalDataItemValue["achieved_roi_percentage"] : 0; ?></td>
								<td style="color: <?php echo $finalDataItemValue["achieved_roi_stage_font_color"]; ?>;background-color: <?php echo $finalDataItemValue["achieved_roi_stage_color"]; ?>;"><?php echo $finalDataItemValue["achieved_roi_stage"]; ?></td>
							</tr>
						<?php
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
		$(".loading-image").addClass("hidden");
		$(".main-section, .customized-datatable-section").removeClass("hidden");

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

	$(document).on("change", ".search-report-by-input", function(e){
		e.preventDefault();

		if ($(this).val() == "Year") {
			$(".month-list-row, .quarter-list-row").addClass("hidden");
			$(".month-list, .quarter-list").prop("required", false);
		} else if ($(this).val() == "Month") {
			$(".month-list-row").removeClass("hidden");
			$(".quarter-list-row").addClass("hidden");
			$(".month-list").prop("required", true);
			$(".quarter-list").prop("required", false);
		} else if ($(this).val() == "Quarter") {
			$(".month-list-row").addClass("hidden");
			$(".quarter-list-row").removeClass("hidden");
			$(".month-list").prop("required", false);
			$(".quarter-list").prop("required", true);

		}
	});

	$(document).on("click", ".reward-popup", function(e){
		e.preventDefault();
		console.log($(this).data("popup"));
		$(".view-reward-popup").modal("show");
		$(".reward-table-section").html("");
		$(".reward-table-section").html($(this).data("popup"));
		$(".scrollable-datatable").DataTable();
		/*$("#divLoading").addClass("show");
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/joborder-detail-popup.php",
			data: $(this).data("popup"),
			success: function(response) {
				$("#divLoading").removeClass("show");
				$(".view-reward-popup").modal("show");
				$(".reward-table-section").html("");
				$(".reward-table-section").html(response);
				$(".scrollable-datatable").DataTable();
			}
		});*/
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
