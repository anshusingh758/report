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
			<form class="form-submit-action" action="index_old.php" method="post">
				<div class="row">
					<div class="col-md-4 col-md-offset-4">
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
								$departmentList = array(
									"CS" => "CS Team",
									"BDC" => "BDC Team",
									"BDG" => "BDG Team",
									"PS" => "PS Team"
								);
								foreach ($departmentList as $departmentListKey => $departmentListValue) {
									$isSelected = "";
									if ($_REQUEST["department-list"] == $departmentListKey) {
										$isSelected = " selected";
									}
									echo "<option value='".$departmentListKey."'".$isSelected.">".$departmentListValue."</option>";
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
		$departmentRateData = $rewardPoint = $rewardPointBrief = $mainData = array();

		echo "<script>
			$(document).ready(function(){
				$('.search-report-by-input').trigger('change');
			});
		</script>";

		$yearList = $_POST["year-list"];

		if ($_POST["search-report-by-input"] == "Year") {
			$fromDate[] = $yearList."-01-01";
			$toDate[] = $yearList."-12-31";
		} elseif ($_POST["search-report-by-input"] == "Month") {
			foreach ($_POST["month-list"] as $monthListKey => $monthListValue) {
				$fromDate[] = "";
				$toDate[] = "";
			}
		} elseif ($_POST["search-report-by-input"] == "Quarter") {
			foreach ($_POST["quarter-list"] as $quarterListKey => $quarterListValue) {
				$fromDate[] = "";
				$toDate[] = "";
			}
		}

		$startDate = $yearList."-01-01";
		$endDate = $yearList."-12-31";

		$taxSettingsTableData = taxSettingsTable($allConn);
        $employeeTimeEntryTableData = employeeTimeEntryTable($allConn, $startDate, $endDate);

        $delimiter = array("","[","]",'"');

        $departmentRateQuery = mysqli_query($allConn, "SELECT
            *
        FROM
            vtechhrm_in.main_departmentrate
        WHERE
            isactive = '1'");

        while ($departmentRateRow = mysqli_fetch_array($departmentRateQuery)) {
        	$departmentRateData[] = $departmentRateRow;
        	
        	if ($departmentRateRow["department"] == $_POST["department-list"]) {
        		$departmentIdList = implode(",", explode(",", $departmentRateRow["department_group"]));
        	}
        }

		$feedbackQuery = mysqli_query($allConn, "SELECT
			mu.id AS personnel_id,
			mu.userfullname AS personnel_name,
		    mu.isactive AS personnel_status,
		    me.date_of_joining,
            mf.user_id,
            mf.feedback_comment,
            mf.feedback_name AS feedback_given_by_name,
            mf.feedback_type,
            mf.feedback_from AS feedback_given_by_title,
            DATE_FORMAT(mf.feedback_date, '%m-%d-%Y') AS feedback_date,
            mf.authorized_by AS feedback_authorized_by_name,
            mr.feedback_point
        FROM
        	vtechhrm_in.main_users AS mu
		    LEFT JOIN vtechhrm_in.main_employees AS me ON me.user_id = mu.id
            LEFT JOIN vtechhrm_in.main_empfeedback AS mf ON mf.user_id = mu.id
            LEFT JOIN vtechhrm_in.main_rewardpoints AS mr ON mr.feedback_from = mf.feedback_from
        WHERE
        	me.department_id IN ($departmentIdList)
        AND
            year(mf.feedback_date) = '$yearList'
        AND
            mf.isactive = '1'
        ORDER BY mf.id DESC");

		if (mysqli_num_rows($feedbackQuery) > 0) {
			while ($feedbackRow = mysqli_fetch_array($feedbackQuery)) {
				if ($feedbackRow["feedback_type"] == "Positive") {
					$rewardPoint[$feedbackRow["user_id"]][] = $feedbackRow["feedback_point"];
		        } elseif ($feedbackRow["feedback_type"] == "Negative") {
		        	$rewardPoint[$feedbackRow["user_id"]][] = ($feedbackRow["feedback_point"] / 2);
		        }

		        $rewardPointBrief[$feedbackRow["user_id"]][] = array(
		        	"personnel_id" => $feedbackRow["personnel_id"],
					"personnel_name" => $feedbackRow["personnel_name"],
					"personnel_status" => $feedbackRow["personnel_status"],
					"date_of_joining" => $feedbackRow["date_of_joining"],
		        	"feedback_comment" => $feedbackRow["feedback_comment"],
					"feedback_given_by_name" => $feedbackRow["feedback_given_by_name"],
					"feedback_type" => $feedbackRow["feedback_type"],
					"feedback_given_by_title" => $feedbackRow["feedback_given_by_title"],
					"feedback_date" => $feedbackRow["feedback_date"],
					"feedback_authorized_by_name" => $feedbackRow["feedback_authorized_by_name"],
					"feedback_point" => $feedbackRow["feedback_point"]
		        );
			}
		}

		$mainQUERY = "SELECT
			mu.id AS personnel_id,
			mu.userfullname AS personnel_name,
		    mu.isactive AS personnel_status,
		    me.date_of_joining,
		    me.department_id AS personnel_department_id,
		    mep.gp_target AS given_gp_target,
		    mep.placement_target AS given_placement_target,
		    mep.sow_product_cost,
		    mep.salary AS personnel_salary,
		    COUNT(DISTINCT meg.id) AS total_subjective_goal
		FROM
			vtechhrm_in.main_users AS mu
		    LEFT JOIN vtechhrm_in.main_employees AS me ON me.user_id = mu.id
			LEFT JOIN vtechhrm_in.main_empperformance AS mep ON mep.user_id = mu.id AND mep.isactive = 1
		    LEFT JOIN vtechhrm_in.main_empgoal AS meg ON meg.user_id = mu.id AND meg.isactive = 1
		WHERE
			me.department_id IN ($departmentIdList)
		AND
			mep.year = '$yearList'
		GROUP BY mu.id
		ORDER BY mu.userfullname";

		$mainRESULT = mysqli_query($allConn, $mainQUERY);

		if (mysqli_num_rows($mainRESULT) > 0) {
			while ($mainROW = mysqli_fetch_array($mainRESULT)) {
				$personnelId = $mainROW["personnel_id"];
				$personnelName = ucwords(strtolower(trim($mainROW["personnel_name"])));

				if ($_POST["department-list"] == "CS") {

					$placementQuery = "SELECT
			            COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
			        FROM
			            cats.user AS u
			            LEFT JOIN cats.candidate_joborder AS cj ON cj.added_by = u.user_id
			            LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			        WHERE
			            cjsh.status_to = '800'
			        AND
			            DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			        AND
			            CONCAT(u.first_name,' ',u.last_name) = '$personnelName'
			        AND
			            cjsh.candidate_id NOT IN (SELECT
			            cjsh.candidate_id
			        FROM
			            cats.user AS u
			            LEFT JOIN cats.candidate_joborder AS cj ON cj.added_by = u.user_id
			            LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			        WHERE
			            cjsh.status_to = '620'
			        AND
			            CONCAT(u.first_name,' ',u.last_name) = '$personnelName'
			        AND
			            DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
			        GROUP BY u.user_id";

			        $placementResult = mysqli_query($allConn, $placementQuery);

			        $achievedPlacementTarget = $achievedPlacementTargetPercentage = "";

			        if (mysqli_num_rows($placementResult) > 0) {
			        	$placementRow = mysqli_fetch_array($placementResult);
			        	$achievedPlacementTarget = $placementRow["total_placed"];
			        	$achievedPlacementTargetPercentage = round((($achievedPlacementTarget * 100) / $mainROW["given_placement_target"]));
			        }

					$gpQuery = "SELECT
			            e.id AS employee_id,
			            e.custom1 AS benefit,
			            e.custom2 AS benefit_list,
			            CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
			            CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
			            es.id AS employment_id,
			            es.name AS employment_type,
			            comp.company_id,
			            comp.name AS company_name,
			            u.user_id AS recruiter_id,
			            CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
			            u.notes AS recruiter_manager,
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
			            CONCAT(u.first_name,' ',u.last_name) = '$personnelName'
			        AND
			            ep.project != '6'
			        AND
			            DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			        AND
			            DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			        GROUP BY employee_id";

			        $gpResult = mysqli_query($allConn, $gpQuery);

			        $totalGrossProfit = array();
			        $achievedGpTarget = $achievedGpTargetPercentage = "";

			        if (mysqli_num_rows($gpResult) > 0) {
			            while ($gpRow = mysqli_fetch_array($gpResult)) {
			                $benefitList = str_replace($delimiter, $delimiter[0], $gpRow["benefit_list"]);

			                $taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$gpRow["benefit"],$benefitList,$gpRow["employment_id"],$gpRow["pay_rate"]), 2);

			                $mspFees = round((($gpRow["client_msp_charge_percentage"] / 100) * $gpRow["bill_rate"]) + $gpRow["client_msp_charge_dollar"], 2);

			                $primeCharges = round(((($gpRow["client_prime_charge_percentage"] / 100) * $gpRow["bill_rate"]) + (($gpRow["employee_prime_charge_percentage"] / 100) * $gpRow["bill_rate"]) + $gpRow["employee_prime_charge_dollar"] + $gpRow["employee_any_charge_dollar"] + $gpRow["client_prime_charge_dollar"]), 2);

			                $candidateRate = round(($gpRow["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

			                $grossMargin = round(($gpRow["bill_rate"] - $candidateRate), 2);

			                $totalHour = round(array_sum($employeeTimeEntryTableData[$gpRow["employee_id"]]), 2);

			                $totalGrossProfit[] = round(($grossMargin * $totalHour), 2);
			            }

			            $achievedGpTarget = round(array_sum($totalGrossProfit), 2);

			            if ($mainROW["sow_product_cost"] != "" && $mainROW["sow_product_cost"] != NULL) {
					        $achievedGpTarget = $achievedGpTarget + $mainROW["sow_product_cost"];
					    }

			            $achievedGpTargetPercentage = round((($achievedGpTarget * 100) / $mainROW["given_gp_target"]));
			        }

				} elseif ($_POST["department-list"] == "BDC" || $_POST["department-list"] == "BDG") {
/*
					$placementQuery = "SELECT
			            COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
			        FROM
			            cats.user AS u
			            LEFT JOIN cats.extra_field AS ef ON ef.value = CONCAT(u.first_name,' ',u.last_name)
			            LEFT JOIN cats.company AS comp ON comp.company_id = ef.data_item_id
			            LEFT JOIN cats.joborder AS job ON job.company_id = comp.company_id
			            LEFT JOIN cats.candidate_joborder AS cj ON cj.joborder_id = job.joborder_id
			            LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			        WHERE
			            cjsh.status_to = '800'
			        AND
			            ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By')
			        AND
			            DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			        AND
			            ef.value = '$personnelName'
			        AND
			            cjsh.candidate_id NOT IN (SELECT
			            cjsh.candidate_id
			        FROM
			            cats.user AS u
			            LEFT JOIN cats.extra_field AS ef ON ef.value = CONCAT(u.first_name,' ',u.last_name)
			            LEFT JOIN cats.company AS comp ON comp.company_id = ef.data_item_id
			            LEFT JOIN cats.joborder AS job ON job.company_id = comp.company_id
			            LEFT JOIN cats.candidate_joborder AS cj ON cj.joborder_id = job.joborder_id
			            LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			        WHERE
			            cjsh.status_to = '620'
			        AND
			            ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By')
			        AND
			            ef.value = '$personnelName'
			        AND
			            DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
			        GROUP BY u.user_id";

			        $placementResult = mysqli_query($allConn, $placementQuery);

			        $achievedPlacementTarget = $achievedPlacementTargetPercentage = "";

			        if (mysqli_num_rows($placementResult) > 0) {
			        	$placementRow = mysqli_fetch_array($placementResult);
			        	$achievedPlacementTarget = $placementRow["total_placed"];
			        	$achievedPlacementTargetPercentage = round((($achievedPlacementTarget * 100) / $mainROW["given_placement_target"]));
			        }
*/
					$gpQuery = "SELECT
			            ehd.*,
			            IF((si.c_inside_sales1 != '' AND si.c_inside_sales2 = '' AND si.c_research_by = ''), si.c_inside_sales1,IF((si.c_inside_sales1 = '' AND si.c_inside_sales2 != '' AND si.c_research_by = ''), si.c_inside_sales2, IF((si.c_inside_sales1 = '' AND si.c_inside_sales2 = '' AND si.c_research_by != ''), si.c_research_by, IF((si.c_inside_sales1 != '' AND si.c_inside_sales2 != '' AND si.c_research_by = ''), CONCAT(si.c_inside_sales1,',',si.c_inside_sales2), IF((si.c_inside_sales1 = '' AND si.c_inside_sales2 != '' AND si.c_research_by != ''),  CONCAT(si.c_inside_sales2,',',si.c_research_by), IF((si.c_inside_sales1 != '' AND si.c_inside_sales2 = '' AND si.c_research_by != ''),   CONCAT(si.c_inside_sales1,',',si.c_research_by), IF((si.c_inside_sales1 != '' AND si.c_inside_sales2 != '' AND si.c_research_by != ''),    CONCAT(si.c_inside_sales1,',',si.c_inside_sales2,',',si.c_research_by), '---'))))))) AS shared_with
			        FROM
			            vtechhrm.employees AS e
			            LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
			            LEFT JOIN vtech_mappingdb.employee_history_detail AS ehd ON ehd.employee_id = e.id
			            LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			        WHERE
			            (si.c_inside_sales1 = '$personnelName' OR si.c_inside_sales2 = '$personnelName' OR si.c_research_by = '$personnelName')
			        AND
			            ehd.id IN (SELECT MAX(id) FROM vtech_mappingdb.employee_history_detail WHERE employee_id = ehd.employee_id AND ((created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') OR (((created_at NOT BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') AND created_at < '$startDate 00:00:00') OR ((created_at NOT BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') AND created_at > '$endDate 23:59:59'))))
			        AND
			            ete.date_start BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
			        GROUP BY e.id";

			        $gpResult = mysqli_query($allConn, $gpQuery);

			        $totalGrossProfit = array();
			        $achievedGpTarget = $achievedGpTargetPercentage = "";

			        if (mysqli_num_rows($gpResult) > 0) {
			            while ($gpRow = mysqli_fetch_array($gpResult)) {
			                $sharedWith = array_unique(explode(",", $gpRow["shared_with"]));

			                $totalShare = COUNT($sharedWith);

			                $empShare = round((1 / $totalShare), 2);

			                $benefitList = str_replace($delimiter, $delimiter[0], $gpRow["benefit_list"]);

			                $taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$gpRow["benefit"],$benefitList,$gpRow["employment_id"],$gpRow["pay_rate"]), 2);

			                $mspFees = round((($gpRow["client_msp_charge_percentage"] / 100) * $gpRow["bill_rate"]) + $gpRow["client_msp_charge_dollar"], 2);

			                $primeCharges = round(((($gpRow["client_prime_charge_percentage"] / 100) * $gpRow["bill_rate"]) + (($gpRow["employee_prime_charge_percentage"] / 100) * $gpRow["bill_rate"]) + $gpRow["employee_prime_charge_dollar"] + $gpRow["employee_any_charge_dollar"] + $gpRow["client_prime_charge_dollar"]), 2);

			                $candidateRate = round(($gpRow["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

			                $grossMargin = round(($gpRow["bill_rate"] - $candidateRate), 2);

			                $totalHour = round(array_sum($employeeTimeEntryTableData[$gpRow["employee_id"]]), 2);

			                $totalGrossProfit[] = round((($grossMargin * $totalHour) * $empShare), 2);
			            }

			            $achievedGpTarget = round(array_sum($totalGrossProfit), 2);

			            if ($mainROW["sow_product_cost"] != "" && $mainROW["sow_product_cost"] != NULL) {
					        $achievedGpTarget = $achievedGpTarget + $mainROW["sow_product_cost"];
					    }

			            $achievedGpTargetPercentage = round((($achievedGpTarget * 100) / $mainROW["given_gp_target"]));
			        }

				} elseif ($_POST["department-list"] == "PS") {
/*
					$placementQuery = "SELECT
			            COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
			        FROM
			            cats.user AS u
			            LEFT JOIN cats.extra_field AS ef ON ef.value = CONCAT(u.first_name,' ',u.last_name)
			            LEFT JOIN cats.company AS comp ON comp.company_id = ef.data_item_id
			            LEFT JOIN cats.joborder AS job ON job.company_id = comp.company_id
			            LEFT JOIN cats.candidate_joborder AS cj ON cj.joborder_id = job.joborder_id
			            LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			        WHERE
			            cjsh.status_to = '800'
			        AND
			            ef.field_name = 'Inside Post Sales'
			        AND
			            DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			        AND
			            ef.value = '$personnelName'
			        AND
			            cjsh.candidate_id NOT IN (SELECT
			            cjsh.candidate_id
			        FROM
			            cats.user AS u
			            LEFT JOIN cats.extra_field AS ef ON ef.value = CONCAT(u.first_name,' ',u.last_name)
			            LEFT JOIN cats.company AS comp ON comp.company_id = ef.data_item_id
			            LEFT JOIN cats.joborder AS job ON job.company_id = comp.company_id
			            LEFT JOIN cats.candidate_joborder AS cj ON cj.joborder_id = job.joborder_id
			            LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
			        WHERE
			            cjsh.status_to = '620'
			        AND
			            ef.field_name = 'Inside Post Sales'
			        AND
			            ef.value = '$personnelName'
			        AND
			            DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
			        GROUP BY u.user_id";

			        $placementResult = mysqli_query($allConn, $placementQuery);

			        $achievedPlacementTarget = $achievedPlacementTargetPercentage = "";

			        if (mysqli_num_rows($placementResult) > 0) {
			        	$placementRow = mysqli_fetch_array($placementResult);
			        	$achievedPlacementTarget = $placementRow["total_placed"];
			        	$achievedPlacementTargetPercentage = round((($achievedPlacementTarget * 100) / $mainROW["given_placement_target"]));
			        }
*/
					$gpQuery = "SELECT
			            ehd.*
			        FROM
			            vtechhrm.employees AS e
			            LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
			            LEFT JOIN vtech_mappingdb.employee_history_detail AS ehd ON ehd.employee_id = e.id
			            LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			        WHERE
			            si.c_inside_post_sales = '$personnelName'
			        AND
			            ehd.id IN (SELECT MAX(id) FROM vtech_mappingdb.employee_history_detail WHERE employee_id = ehd.employee_id AND ((created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') OR (((created_at NOT BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') AND created_at < '$startDate 00:00:00') OR ((created_at NOT BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') AND created_at > '$endDate 23:59:59'))))
			        AND
			        	e.custom7 BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
			        AND
			            ete.date_start BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
			        GROUP BY e.id";

			        $gpResult = mysqli_query($allConn, $gpQuery);

			        $totalGrossProfit = array();
			        $achievedGpTarget = $achievedGpTargetPercentage = "";

			        if (mysqli_num_rows($gpResult) > 0) {
			            while ($gpRow = mysqli_fetch_array($gpResult)) {
			                $benefitList = str_replace($delimiter, $delimiter[0], $gpRow["benefit_list"]);

			                $taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$gpRow["benefit"],$benefitList,$gpRow["employment_id"],$gpRow["pay_rate"]), 2);

			                $mspFees = round((($gpRow["client_msp_charge_percentage"] / 100) * $gpRow["bill_rate"]) + $gpRow["client_msp_charge_dollar"], 2);

			                $primeCharges = round(((($gpRow["client_prime_charge_percentage"] / 100) * $gpRow["bill_rate"]) + (($gpRow["employee_prime_charge_percentage"] / 100) * $gpRow["bill_rate"]) + $gpRow["employee_prime_charge_dollar"] + $gpRow["employee_any_charge_dollar"] + $gpRow["client_prime_charge_dollar"]), 2);

			                $candidateRate = round(($gpRow["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

			                $grossMargin = round(($gpRow["bill_rate"] - $candidateRate), 2);

			                $totalHour = round(array_sum($employeeTimeEntryTableData[$gpRow["employee_id"]]), 2);

			                $totalGrossProfit[] = round(($grossMargin * $totalHour), 2);
			            }

			            $achievedGpTarget = round(array_sum($totalGrossProfit), 2);

			            if ($mainROW["sow_product_cost"] != "" && $mainROW["sow_product_cost"] != NULL) {
					        $achievedGpTarget = $achievedGpTarget + $mainROW["sow_product_cost"];
					    }

			            $achievedGpTargetPercentage = round((($achievedGpTarget * 100) / $mainROW["given_gp_target"]));
			        }
				}

		        $departmentRate = 0;
			    foreach ($departmentRateData as $departmentratedatakey => $departmentratedatavalue) {
			    	$department_group_array = explode(",", $departmentratedatavalue["department_group"]);
			        if (in_array($mainROW["personnel_department_id"], $department_group_array)) {
			            $departmentRate = $departmentratedatavalue["rate"];
			        }
			    }

		    	$givenEmployeeSalary = $achievedROI = $achievedROIPercentage = 0;
				$achievedROIZone = "NA";
				$achievedROIZoneColor = "transparent";
				$achievedROIZoneFontColor = "#000";

			    if ($departmentRate != 0) {
			    	$givenEmployeeSalary = clean(getDecryptedValue($mainROW["personnel_salary"]));

			    	if ($givenEmployeeSalary != 0 && $givenEmployeeSalary != "") {
			    		$achievedROI = round(($givenEmployeeSalary * $departmentRate), 2);
			    		if ($yearList >= date("Y")) {
				            $currentMonth = date('m');

				            $achievedROI = round(((($givenEmployeeSalary * $departmentRate) / 12) * $currentMonth), 2);
			    		}
			            $achievedROIPercentage = round((($achievedGpTarget / $achievedROI) * 100));
			            
						$achievedROIZoneFontColor = "#fff";

			            if ($achievedROIPercentage < 30) {
			                $achievedROIZone = "Poor";
			                $achievedROIZoneColor = "red";
			            } else if ($achievedROIPercentage >= 30 && $achievedROIPercentage < 50) {
			                $achievedROIZone = "Ok";
			                $achievedROIZoneColor = "yellow";
			                $achievedROIZoneFontColor = "#000";
			            } else if ($achievedROIPercentage >= 50 && $achievedROIPercentage < 80) {
			                $achievedROIZone = "Good";
			                $achievedROIZoneColor = "green";
			            } else if ($achievedROIPercentage >= 80 && $achievedROIPercentage <= 100) {
			                $achievedROIZone = "Great";
			                $achievedROIZoneColor = "blue";
			            } else {
			                $achievedROIZone = "Excellent";
			                $achievedROIZoneColor = "#FF1493";
			            }
			        }
			    }

				$mainData[] = array(
					"personnel_id" => $mainROW["personnel_id"],
					"personnel_name" => $personnelName,
					"date_of_joining" => date("m-d-Y", strtotime($mainROW["date_of_joining"])),
					"reward_point" => array_sum($rewardPoint[$personnelId]),
					"personnel_status" => $mainROW["personnel_status"],
					"given_gp_target" => $mainROW["given_gp_target"],
					"achieved_gp_target" => $achievedGpTarget,
					"achieved_gp_target_percentage" => $achievedGpTargetPercentage,
					"given_placement_target" => $mainROW["given_placement_target"],
					"achieved_placement_target" => $achievedPlacementTarget,
					"achieved_placement_target_percentage" => $achievedPlacementTargetPercentage,
					"sow_product_cost" => $mainROW["sow_product_cost"],
					"total_subjective_goal" => $mainROW["total_subjective_goal"],
					"personnel_salary" => $givenEmployeeSalary,
					"achieved_roi" => $achievedROI,
					"achieved_roi_percentage" => $achievedROIPercentage,
					"achieved_roi_zone" => $achievedROIZone,
					"achieved_roi_zone_color" => $achievedROIZoneColor,
					"achieved_roi_zone_font_color" => $achievedROIZoneFontColor
				);
			}
		}

		foreach ($rewardPointBrief as $rewardPointBriefKey => $rewardPointBriefValue) {
			if (in_array($rewardPointBriefKey, array_column($mainData, "personnel_id"))) {

			} else {
				$mainData[] = array(
					"personnel_id" => $rewardPointBriefValue[0]["personnel_id"],
					"personnel_name" => $rewardPointBriefValue[0]["personnel_name"],
					"date_of_joining" => date("m-d-Y", strtotime($rewardPointBriefValue[0]["date_of_joining"])),
					"reward_point" => array_sum($rewardPoint[$rewardPointBriefValue[0]["personnel_id"]]),
					"personnel_status" => $rewardPointBriefValue[0]["personnel_status"],
					"given_gp_target" => "",
					"achieved_gp_target" => "",
					"achieved_gp_target_percentage" => "",
					"given_placement_target" => "",
					"achieved_placement_target" => "",
					"achieved_placement_target_percentage" => "",
					"sow_product_cost" => "",
					"total_subjective_goal" => "",
					"personnel_salary" => "",
					"achieved_roi" => "",
					"achieved_roi_percentage" => "",
					"achieved_roi_zone" => "",
					"achieved_roi_zone_color" => "",
					"achieved_roi_zone_font_color" => ""
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
								<th rowspan="3">Personnel</th>
								<th rowspan="3">Date of joining</th>
								<th rowspan="3">Reward Points</th>
								<!-- <th rowspan="3">Reward Points</th>
								<th rowspan="3">Subjective Goals</th> -->
								<th colspan="3">GP Target</th>
							<?php if ($_POST["department-list"] == "CS") { ?>
								<th colspan="3">Placement Target</th>
							<?php } ?>
								<th colspan="4">Achieved ROI</th>
							</tr>
							<tr class="thead-tr-style">
								<th rowspan="2">Assigned</th>
								<th colspan="2">Achieved</th>
							<?php if ($_POST["department-list"] == "CS") { ?>
								<th rowspan="2">Assigned</th>
								<th colspan="2">Achieved</th>
							<?php } ?>
								<th rowspan="2">Salary</th>
								<th rowspan="2">Expected GP</th>
								<th rowspan="2">Percentage (%)</th>
								<th rowspan="2">Stage</th>
							</tr>
							<tr class="thead-tr-style">
							<?php if ($_POST["department-list"] == "CS") { ?>
								<th>Total GP</th>
								<th>Percentage (%)</th>
							<?php } ?>
								<th>Total</th>
								<th style="border-right: 1px solid #aaa;">Percentage (%)</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach ($mainData as $mainDataKey => $mainDataValue) {
						?>
							<tr class="tbody-tr-style">
								<td><?php echo $mainDataValue["personnel_name"]; ?></td>
								<td><?php echo $mainDataValue["date_of_joining"]; ?></td>
								<td><!-- <a class="reward-popup hyper-link-text" data-popup='<?php echo json_encode($rewardPointBrief[$mainDataValue["personnel_id"]]); ?>'> --><?php echo $mainDataValue["reward_point"] != "" ? $mainDataValue["reward_point"] : 0; ?><!-- </a> --></td>
								<!-- <td></td>
								<td><?php /*echo $mainDataValue["total_subjective_goal"];*/ ?></td> -->
								<td><?php echo $mainDataValue["given_gp_target"] != "" ? $mainDataValue["given_gp_target"] : 0; ?></td>
								<td><?php echo $mainDataValue["achieved_gp_target"] != "" ? $mainDataValue["achieved_gp_target"] : 0; ?></td>
								<td><?php echo $mainDataValue["achieved_gp_target_percentage"] != "" ? $mainDataValue["achieved_gp_target_percentage"] : 0; ?></td>
							<?php if ($_POST["department-list"] == "CS") { ?>
								<td><?php echo $mainDataValue["given_placement_target"] != "" ? $mainDataValue["given_placement_target"] : 0; ?></td>
								<td><?php echo $mainDataValue["achieved_placement_target"] != "" ? $mainDataValue["achieved_placement_target"] : 0; ?></td>
								<td><?php echo $mainDataValue["achieved_placement_target_percentage"] != "" ? $mainDataValue["achieved_placement_target_percentage"] : 0; ?></td>
							<?php } ?>
								<td><?php echo $mainDataValue["personnel_salary"] != "" ? $mainDataValue["personnel_salary"] : ""; ?></td>
								<td><?php echo $mainDataValue["achieved_roi"] != "" ? $mainDataValue["achieved_roi"] : 0; ?></td>
								<td><?php echo $mainDataValue["achieved_roi_percentage"] != "" ? $mainDataValue["achieved_roi_percentage"] : 0; ?></td>
								<td style="color: <?php echo $mainDataValue["achieved_roi_zone_font_color"]; ?>;background-color: <?php echo $mainDataValue["achieved_roi_zone_color"]; ?>;"><?php echo $mainDataValue["achieved_roi_zone"]; ?></td>
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
