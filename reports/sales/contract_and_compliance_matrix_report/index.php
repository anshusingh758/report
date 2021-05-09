<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
	
    if (isset($user) && isset($userMember)) {
		$reportId = "70";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>C&C Matrix Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
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
		table.dataTable tbody td:nth-child(1) {
			text-align: left;
		}
		table.dataTable tfoot td:nth-child(1) {
			text-align: left;
		}
		table.customized-datatable-2 tbody td:nth-child(1) {
			text-align: center;
		}
		table.dataTable thead tr:nth-child(2) th:last-child {
			border-right: 1px solid #ddd;
		}
		table.dataTable tbody td a {
			cursor: pointer;
			font-weight: bold;
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
				<div class="col-md-12 report-title">C&C Matrix Report</div>
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
		$personnelData = "'".implode("', '",$_REQUEST['personnel-list'])."'";

		$complianceTeamDataArray = array();

		foreach ($fromDate as $fromDateKey => $fromDateValue) {
			$startDate = $fromDate[$fromDateKey];
			$endDate = $toDate[$fromDateKey];

			$givenMonth = date("m/Y", strtotime($startDate));

			$newContractsInprocessQUERY = mysqli_query($allConn, "SELECT
				'$givenMonth' AS row_name,
				
				COUNT(DISTINCT s_opp.id) AS new_contract_in_process
			FROM
				vtechcrm.x2_opportunities AS s_opp
			WHERE
				s_opp.salesStage = 'Won'
			AND
				s_opp.id NOT IN (SELECT
				    c_opp.c_sale_opportunity_id
			    FROM
			    	contract.x2_opportunities AS c_opp
			    WHERE
			    	c_opp.c_sale_opportunity_id IS NOT NULL)
			AND
				DATE_FORMAT(FROM_UNIXTIME(s_opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY row_name");

			$newContractsInprocessROW = mysqli_fetch_array($newContractsInprocessQUERY);

			$newContractsInprocessItem = $newContractsInprocessROW["new_contract_in_process"];

			$mainQUERY = "SELECT
				a.assigned_to,
				b.new_contracts_completed,
				c.total_external_inprocess,
				c.total_external_completed,
				c.total_internal_inprocess,
				c.total_internal_completed,
				d.total_awards_inprocess,
				d.total_awards_completed
			FROM
			(SELECT
				concat(c_u.firstName,' ',c_u.lastName) AS assigned_to
			FROM
				contract.x2_users AS c_u
			GROUP BY c_u.username
			ORDER BY c_u.username ASC) AS a
			LEFT JOIN
			(SELECT
				concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    COUNT(DISTINCT c_o.id) AS new_contracts_completed
			FROM
				contract.x2_opportunities AS c_o
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_o.assignedTo
			WHERE
				DATE_FORMAT(FROM_UNIXTIME(c_o.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY c_o.assignedTo
			ORDER BY assigned_to ASC) AS b ON b.assigned_to = a.assigned_to
			LEFT JOIN
			(SELECT
				all_main.assigned_to,
			    
			    SUM(IF(((ext_proc_main.ext_inprocess_main IS NULL) AND (ext_proc_log.ext_inprocess_log IS NOT NULL)), 1, IF(((ext_proc_main.ext_inprocess_main IS NOT NULL) AND (ext_proc_log.ext_inprocess_log IS NULL)), 1, IF(((ext_proc_main.ext_inprocess_main IS NOT NULL) AND (ext_proc_log.ext_inprocess_log IS NOT NULL)), 1, 0)))) AS total_external_inprocess,
			    
			    SUM(IF(((ext_comp_main.ext_completed_main IS NULL) AND (ext_comp_log.ext_completed_log IS NOT NULL)), 1, IF(((ext_comp_main.ext_completed_main IS NOT NULL) AND (ext_comp_log.ext_completed_log IS NULL)), 1, IF(((ext_comp_main.ext_completed_main IS NOT NULL) AND (ext_comp_log.ext_completed_log IS NOT NULL)), 1, 0)))) AS total_external_completed,
			    
			    SUM(IF(((int_proc_main.int_inprocess_main IS NULL) AND (int_proc_log.int_inprocess_log IS NOT NULL)), 1, IF(((int_proc_main.int_inprocess_main IS NOT NULL) AND (int_proc_log.int_inprocess_log IS NULL)), 1, IF(((int_proc_main.int_inprocess_main IS NOT NULL) AND (int_proc_log.int_inprocess_log IS NOT NULL)), 1, 0)))) AS total_internal_inprocess,
			    
			    SUM(IF(((int_comp_main.int_completed_main IS NULL) AND (int_comp_log.int_completed_log IS NOT NULL)), 1, IF(((int_comp_main.int_completed_main IS NOT NULL) AND (int_comp_log.int_completed_log IS NULL)), 1, IF(((int_comp_main.int_completed_main IS NOT NULL) AND (int_comp_log.int_completed_log IS NOT NULL)), 1, 0)))) AS total_internal_completed
			FROM
			(SELECT
				c_a.id AS audit_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to
			FROM
				contract.x2_audits AS c_a
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
			GROUP BY audit_id) AS all_main
			LEFT JOIN
			(SELECT
			    c_a.id AS audit_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    c_a.id AS ext_inprocess_main
			FROM
			    contract.x2_audits AS c_a
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
			WHERE
			    c_a.c_audit_type = 'External/Client'
			AND
			    c_a.c_audit_status = 'In process'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(c_a.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY audit_id) AS ext_proc_main ON ext_proc_main.audit_id = all_main.audit_id AND ext_proc_main.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_a.id AS audit_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    MAX(chg.id) AS ext_inprocess_log
			FROM
			    contract.x2_audits AS c_a
			    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_a.id
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
			WHERE
			    c_a.c_audit_type = 'External/Client'
			AND
			    chg.type = 'Audits'
			AND
			    chg.fieldName = 'c_audit_status'
			AND
			    chg.newValue = 'In process'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY audit_id) AS ext_proc_log ON ext_proc_log.audit_id = all_main.audit_id AND ext_proc_log.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_a.id AS audit_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    c_a.id AS ext_completed_main
			FROM
			    contract.x2_audits AS c_a
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
			WHERE
			    c_a.c_audit_type = 'External/Client'
			AND
			    c_a.c_audit_status = 'Completed'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(c_a.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY audit_id) AS ext_comp_main ON ext_comp_main.audit_id = all_main.audit_id AND ext_comp_main.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_a.id AS audit_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    MAX(chg.id) AS ext_completed_log
			FROM
			    contract.x2_audits AS c_a
			    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_a.id
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
			WHERE
			    c_a.c_audit_type = 'External/Client'
			AND
			    chg.type = 'Audits'
			AND
			    chg.fieldName = 'c_audit_status'
			AND
			    chg.newValue = 'Completed'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY audit_id) AS ext_comp_log ON ext_comp_log.audit_id = all_main.audit_id AND ext_comp_log.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_a.id AS audit_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    c_a.id AS int_inprocess_main
			FROM
			    contract.x2_audits AS c_a
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
			WHERE
			    c_a.c_audit_type = 'Internal'
			AND
			    c_a.c_audit_status = 'In process'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(c_a.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY audit_id) AS int_proc_main ON int_proc_main.audit_id = all_main.audit_id AND int_proc_main.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_a.id AS audit_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    MAX(chg.id) AS int_inprocess_log
			FROM
			    contract.x2_audits AS c_a
			    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_a.id
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
			WHERE
			    c_a.c_audit_type = 'Internal'
			AND
			    chg.type = 'Audits'
			AND
			    chg.fieldName = 'c_audit_status'
			AND
			    chg.newValue = 'In process'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY audit_id) AS int_proc_log ON int_proc_log.audit_id = all_main.audit_id AND int_proc_log.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_a.id AS audit_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    c_a.id AS int_completed_main
			FROM
			    contract.x2_audits AS c_a
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
			WHERE
			    c_a.c_audit_type = 'Internal'
			AND
			    c_a.c_audit_status = 'Completed'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(c_a.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY audit_id) AS int_comp_main ON int_comp_main.audit_id = all_main.audit_id AND int_comp_main.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_a.id AS audit_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    MAX(chg.id) AS int_completed_log
			FROM
			    contract.x2_audits AS c_a
			    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_a.id
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
			WHERE
			    c_a.c_audit_type = 'Internal'
			AND
			    chg.type = 'Audits'
			AND
			    chg.fieldName = 'c_audit_status'
			AND
			    chg.newValue = 'Completed'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY audit_id) AS int_comp_log ON int_comp_log.audit_id = all_main.audit_id AND int_comp_log.assigned_to = all_main.assigned_to
			WHERE
			    (ext_proc_main.ext_inprocess_main != '' OR ext_proc_log.ext_inprocess_log != '' OR ext_comp_main.ext_completed_main != '' OR ext_comp_log.ext_completed_log != '' OR int_proc_main.int_inprocess_main != '' OR int_proc_log.int_inprocess_log != '' OR int_comp_main.int_completed_main != '' OR int_comp_log.int_completed_log != '')
			GROUP BY assigned_to
			ORDER BY assigned_to ASC) AS c ON c.assigned_to = a.assigned_to
			LEFT JOIN
			(SELECT
				all_main.assigned_to,
			    
			    SUM(IF(((award_proc_main.award_inprocess_main IS NULL) AND (award_proc_log.award_inprocess_log IS NOT NULL)), 1, IF(((award_proc_main.award_inprocess_main IS NOT NULL) AND (award_proc_log.award_inprocess_log IS NULL)), 1, IF(((award_proc_main.award_inprocess_main IS NOT NULL) AND (award_proc_log.award_inprocess_log IS NOT NULL)), 1, 0)))) AS total_awards_inprocess,
			    
			    SUM(IF(((award_comp_main.award_completed_main IS NULL) AND (award_comp_log.award_completed_log IS NOT NULL)), 1, IF(((award_comp_main.award_completed_main IS NOT NULL) AND (award_comp_log.award_completed_log IS NULL)), 1, IF(((award_comp_main.award_completed_main IS NOT NULL) AND (award_comp_log.award_completed_log IS NOT NULL)), 1, 0)))) AS total_awards_completed
			FROM
			(SELECT
				c_ca.id AS award_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to
			FROM
				contract.x2_certification_award AS c_ca
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
			GROUP BY award_id) AS all_main
			LEFT JOIN
			(SELECT
			    c_ca.id AS award_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    c_ca.id AS award_inprocess_main
			FROM
			    contract.x2_certification_award AS c_ca
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
			WHERE
			    c_ca.c_award_status = 'In process'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(c_ca.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY award_id) AS award_proc_main ON award_proc_main.award_id = all_main.award_id AND award_proc_main.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_ca.id AS award_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    MAX(chg.id) AS award_inprocess_log
			FROM
			    contract.x2_certification_award AS c_ca
			    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_ca.id
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
			WHERE
			    chg.type = 'Certification_award'
			AND
			    chg.fieldName = 'c_award_status'
			AND
			    chg.newValue = 'In process'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY award_id) AS award_proc_log ON award_proc_log.award_id = all_main.award_id AND award_proc_log.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_ca.id AS award_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    c_ca.id AS award_completed_main
			FROM
			    contract.x2_certification_award AS c_ca
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
			WHERE
			    c_ca.c_award_status = 'Awarded'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(c_ca.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY award_id) AS award_comp_main ON award_comp_main.award_id = all_main.award_id AND award_comp_main.assigned_to = all_main.assigned_to
			LEFT JOIN
			(SELECT
			    c_ca.id AS award_id,
			    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
			    MAX(chg.id) AS award_completed_log
			FROM
			    contract.x2_certification_award AS c_ca
			    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_ca.id
			    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
			WHERE
			    chg.type = 'Certification_award'
			AND
			    chg.fieldName = 'c_award_status'
			AND
			    chg.newValue = 'Awarded'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY award_id) AS award_comp_log ON award_comp_log.award_id = all_main.award_id AND award_comp_log.assigned_to = all_main.assigned_to
			WHERE
			    (award_proc_main.award_inprocess_main != '' OR award_proc_log.award_inprocess_log != '' OR award_comp_main.award_completed_main != '' OR award_comp_log.award_completed_log != '')
			GROUP BY assigned_to
			ORDER BY assigned_to ASC) AS d ON d.assigned_to = a.assigned_to
			WHERE
				(b.new_contracts_completed != '' OR c.total_external_inprocess != '' OR c.total_external_completed != '' OR c.total_internal_inprocess != '' OR c.total_internal_completed != '' OR d.total_awards_inprocess != '' OR d.total_awards_completed != '')
			GROUP BY assigned_to
			ORDER BY assigned_to ASC";

			$mainRESULT = mysqli_query($allConn, $mainQUERY);

			if (mysqli_num_rows($mainRESULT) > 0) {
				while ($mainROW = mysqli_fetch_array($mainRESULT)) {

					$complianceTeamDataArray[] = array(
						"assigned_to" => ucwords(strtolower($mainROW["assigned_to"])),
						"given_month" => $givenMonth,
						"new_contracts_inprocess" => $mainROW["new_contracts_inprocess"] == "" ? "0" : $mainROW["new_contracts_inprocess"],
						"new_contracts_completed" => $mainROW["new_contracts_completed"] == "" ? "0" : $mainROW["new_contracts_completed"],
						"total_awards_inprocess" => $mainROW["total_awards_inprocess"] == "" ? "0" : $mainROW["total_awards_inprocess"],
						"total_awards_completed" => $mainROW["total_awards_completed"] == "" ? "0" : $mainROW["total_awards_completed"],
						"total_external_inprocess" => $mainROW["total_external_inprocess"] == "" ? "0" : $mainROW["total_external_inprocess"],
						"total_external_completed" => $mainROW["total_external_completed"] == "" ? "0" : $mainROW["total_external_completed"],
						"total_internal_inprocess" => $mainROW["total_internal_inprocess"] == "" ? "0" : $mainROW["total_internal_inprocess"],
						"total_internal_completed" => $mainROW["total_internal_completed"] == "" ? "0" : $mainROW["total_internal_completed"]
					);

				}
			}
	
			$newContractsInprocessArray[] = array(
				"assigned_to" => "Compliance Team",
				"given_month" => $givenMonth,
				"new_contracts_inprocess" => $newContractsInprocessItem == "" ? "0" : $newContractsInprocessItem,
				"new_contracts_completed" => "---",
				"total_awards_inprocess" => "---",
				"total_awards_completed" => "---",
				"total_external_inprocess" => "---",
				"total_external_completed" => "---",
				"total_internal_inprocess" => "---",
				"total_internal_completed" => "---"
			);

		}

?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-10 col-md-offset-1">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th rowspan="2">Assigned To</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th rowspan="2">Months</th>
							<?php } ?>
								<th colspan="2">New Contracts</th>
								<th colspan="2">Certification / Award</th>
								<th colspan="2">Client Audits</th>
								<th colspan="2">Internal Audits</th>
							</tr>
							<tr class="thead-tr-style">
								<th>In Process</th>
								<th>Completed</th>
								<th>In Process</th>
								<th>Completed</th>
								<th>In Process</th>
								<th>Completed</th>
								<th>In Process</th>
								<th>Completed</th>
							</tr>
						</thead>
						<tbody>
						<?php
							$newContractsInprocess = $newContractsCompleted = $totalExternalInprocess = $totalExternalCompleted = $totalInternalInprocess = $totalInternalCompleted = $totalAwardsInprocess = $totalAwardsCompleted = array();

							foreach ($complianceTeamDataArray AS $complianceTeamDataArrayKey => $complianceTeamDataArrayValue) {
						?>
							<tr class="tbody-tr-style">
								<td>
									<?php
										echo $complianceTeamDataArrayValue["assigned_to"];
									?>
								</td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td>
								<?php
									echo $complianceTeamDataArrayValue["given_month"];
								?>
								</td>
							<?php } ?>
								<td>
								<?php
									if ($complianceTeamDataArrayValue["new_contracts_inprocess"] == "0") {
										echo  "---";
									} else {
										echo $newContractsInprocess[] = $complianceTeamDataArrayValue["new_contracts_inprocess"];
									}
								?>
								</td>
								<td>
								<?php
									echo $newContractsCompleted[] = $complianceTeamDataArrayValue["new_contracts_completed"];
								?>
								</td>
								<td>
								<?php
									echo $totalAwardsInprocess[] = $complianceTeamDataArrayValue["total_awards_inprocess"];
								?>
								</td>
								<td>
								<?php
									echo $totalAwardsCompleted[] = $complianceTeamDataArrayValue["total_awards_completed"];
								?>
								</td>
								<td>
								<?php
									echo $totalExternalInprocess[] = $complianceTeamDataArrayValue["total_external_inprocess"];
								?>
								</td>
								<td>
								<?php
									echo $totalExternalCompleted[] = $complianceTeamDataArrayValue["total_external_completed"];
								?>
								</td>
								<td>
								<?php
									echo $totalInternalInprocess[] = $complianceTeamDataArrayValue["total_internal_inprocess"];
								?>
								</td>
								<td>
								<?php
									echo $totalInternalCompleted[] = $complianceTeamDataArrayValue["total_internal_completed"];
								?>
								</td>
							</tr>
						<?php
							}
						?>
						</tbody>
						<tfoot>
						<?php
							foreach ($newContractsInprocessArray AS $newContractsInprocessArrayKey => $newContractsInprocessArrayValue) {
						?>
							<tr class="tbody-tr-style">
								<td>
									<?php
										echo $newContractsInprocessArrayValue["assigned_to"];
									?>
								</td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td>
								<?php
									echo $newContractsInprocessArrayValue["given_month"];
								?>
								</td>
							<?php } ?>
								<td>
								<?php
									echo $newContractsInprocess[] = $newContractsInprocessArrayValue["new_contracts_inprocess"];
								?>
								</td>
								<td>
								<?php
									echo $newContractsInprocessArrayValue["new_contracts_completed"];
								?>
								</td>
								<td>
								<?php
									echo $newContractsInprocessArrayValue["total_awards_inprocess"];
								?>
								</td>
								<td>
								<?php
									echo $newContractsInprocessArrayValue["total_awards_completed"];
								?>
								</td>
								<td>
								<?php
									echo $newContractsInprocessArrayValue["total_external_inprocess"];
								?>
								</td>
								<td>
								<?php
									echo $newContractsInprocessArrayValue["total_external_completed"];
								?>
								</td>
								<td>
								<?php
									echo $newContractsInprocessArrayValue["total_internal_inprocess"];
								?>
								</td>
								<td>
								<?php
									echo $newContractsInprocessArrayValue["total_internal_completed"];
								?>
								</td>
							</tr>
						<?php
							}
						?>
							<tr class="tfoot-tr-style">
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th colspan="2">Total</th>
							<?php } else { ?>
								<th>Total</th>
							<?php } ?>
								<th><?php echo array_sum($newContractsInprocess); ?></th>
								<th><?php echo array_sum($newContractsCompleted); ?></th>
								<th><?php echo array_sum($totalAwardsInprocess); ?></th>
								<th><?php echo array_sum($totalAwardsCompleted); ?></th>
								<th><?php echo array_sum($totalExternalInprocess); ?></th>
								<th><?php echo array_sum($totalExternalCompleted); ?></th>
								<th><?php echo array_sum($totalInternalInprocess); ?></th>
								<th><?php echo array_sum($totalInternalCompleted); ?></th>
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
	        	$("div.dataTables_filter input").css("width","200")
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
