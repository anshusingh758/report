<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "61";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>BDG Matrix Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
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
		table.scrollable-datatable tbody td:nth-child(2) {
			text-align: left;
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
			font-size: 11px;
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
		.wonDivision {
			font-size: 10px;
			color: #333;
			position: absolute;
			bottom: 0;
			right: 0;
			color: #2266AA;
			font-weight: bold;
		}
	</style>
</head>
<body>

	<?php include_once("../../../popups.php"); ?>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">BDG Matrix Report</div>
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
						<label>Select Personnel :</label>
						<select id="personnel-list" class="<?php if (isset($_REQUEST["personnel-list"])) { echo "customized-selectbox-without-all"; } else { echo "customized-selectbox-with-all"; } ?>" name="personnel-list[]" multiple required>
							<?php
								$isSelected = "";
								$salesGroupPersonnel = salesGroupPersonnelList($sales_connect,"1");
								foreach ($salesGroupPersonnel as $salesGroupPersonnelKey => $salesGroupPersonnelValue) {
									if (in_array($salesGroupPersonnelValue, $_REQUEST["personnel-list"])) {
										$isSelected = " selected";
									} else {
										$isSelected = "";
									}
									echo "<option value='".$salesGroupPersonnelValue."'".$isSelected.">".$salesGroupPersonnelValue."</option>";
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
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th rowspan="2">Personnel</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th rowspan="2">Months</th>
							<?php } ?>
								<th colspan="7">Total No. of</th>
								<th colspan="5">Total Opportunities in</th>
								<th rowspan="2">Accounts<br>Got<br>Requirements</th>
								<th rowspan="2">Total<br>GP<br>Revenue</th>
							</tr>
							<tr class="thead-tr-style">
								<th>New Accounts</th>
								<th>New Contacts</th>
								<th>First Touch</th>
								<th>Meaningful Conversions</th>
								<th>Appointments / Meetings</th>
								<th>ShowUps</th>
								<th>FollowUps</th>
								<th>Pipeline</th>
								<th>Working</th>
								<th>Submitted</th>
								<th>Second Stage</th>
								<th>Won</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$totalAccounts = $totalContacts = $totalFirstTouch = $totalMeaningfulData = $totalMeetings = $totalShowUps = $totalFollowUps = $totalPipeline = $totalWorking = $totalSubmitted = $totalSecondStage = $totalWon = $totalRequirements = $totalGrossProfit = $sowData = array();

								$taxSettingsTableData = taxSettingsTable($allConn);

								foreach ($fromDate as $fromDateKey => $fromDateValue) {
									$startDate = $fromDate[$fromDateKey];
									$endDate = $toDate[$fromDateKey];

									$givenMonth = date("m/Y", strtotime($startDate));
									$thisYear = date('Y', strtotime($fromDateValue));
									$sowData = getSowData($allConn,$thisYear);
									$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$startDate,$endDate);

									$mainQUERY = "SELECT
										a.personnel_name,
									    a.personnel_status,
									    b.total_accounts,
									    b.total_contacts,
									    c.total_first_touch,
									    d.total_follow_ups,
									    e.total_show_ups,
									    f.total_requirements,
									    g.total_call,
									    g.total_email,
									    g.total_comment,
									    g.total_meeting,
									    g.total_meaningful,
									    h.total_pipeline,
										h.total_working,
										h.total_submitted,
										h.total_second_stage,
										h.total_won,
										h.total_shared_won,
										i.resources_working,
										j.client_checklist_question_first_touch,
										j.interview_question_follow_ups,
										j.premeeting_question_follow_ups
									FROM
									(SELECT
										CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
										u.status AS personnel_status
									FROM
										vtechcrm.x2_users AS u
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									GROUP BY personnel_name) AS a
									LEFT JOIN
									(SELECT
										CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
										COUNT(DISTINCT CASE WHEN (e.associationType = 'Accounts') THEN act.id END) AS total_accounts,
										COUNT(DISTINCT CASE WHEN (e.associationType = 'Contacts') THEN c.id END) AS total_contacts
									FROM
										vtechcrm.x2_events AS e
										LEFT JOIN vtechcrm.x2_accounts AS act ON act.id = e.associationId
										LEFT JOIN x2_contacts AS c ON c.id = e.associationId
										LEFT JOIN vtechcrm.x2_users AS u ON e.user = u.username
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	e.type = 'record_create'
									AND
										DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnel_name) AS b ON b.personnel_name = a.personnel_name
									LEFT JOIN
									(SELECT
										ft.personnel_name,
										COUNT(DISTINCT ft.id) AS total_first_touch
									FROM
									(SELECT
										MIN(actx.id) AS min_id,
										act.id,
										CONCAT(u.firstName, ' ', u.lastName) AS personnel_name
									FROM
										vtechcrm.x2_actions AS act
										LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
										LEFT JOIN vtechcrm.x2_actions AS actx ON actx.associationId = act.associationId AND actx.associationType = act.associationType AND actx.type IN ('note','call','emaildata','meaningfulData','event') AND actx.completedBy = act.completedBy
									WHERE
										CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
									AND
										act.associationType IN ('accounts','contacts','opportunities')
									AND
										act.type IN ('note','call','emaildata','meaningfulData','event')
									AND
										DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY act.id
									HAVING min_id = id) AS ft
									GROUP BY personnel_name) AS c ON c.personnel_name = a.personnel_name
									LEFT JOIN
									(SELECT
										fu.personnel_name,
										COUNT(DISTINCT fu.id) AS total_follow_ups
									FROM
									(SELECT
										MIN(actx.id) AS min_id,
										act.id,
										CONCAT(u.firstName, ' ', u.lastName) AS personnel_name
									FROM
										vtechcrm.x2_actions AS act
										LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
										LEFT JOIN vtechcrm.x2_actions AS actx ON actx.associationId = act.associationId AND actx.associationType = act.associationType AND actx.type IN ('note','call','emaildata','meaningfulData','event') AND actx.completedBy = act.completedBy
									WHERE
										CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
									AND
										act.associationType IN ('accounts','contacts','opportunities')
									AND
										act.type IN ('note','call','emaildata','meaningfulData','event')
									AND
										DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY act.id
									HAVING min_id != id) AS fu
									GROUP BY personnel_name) AS d ON d.personnel_name = a.personnel_name
									LEFT JOIN
									(SELECT
										CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    COUNT(DISTINCT act.id) AS total_show_ups
									FROM
									    vtechcrm.x2_actions AS act
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										act.type = 'event'
									AND
										act.complete = 'Yes'
									AND
										DATE_FORMAT(FROM_UNIXTIME(act.completeDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnel_name) AS e ON e.personnel_name = a.personnel_name
									LEFT JOIN
									(SELECT
										tr.personnel_name,
									    COUNT(DISTINCT tr.id) AS total_requirements
									FROM
									(SELECT
										copp.id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    COUNT(job.joborder_id) AS total_job
									FROM
										contract.x2_opportunities AS copp
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = copp.assignedTo OR u.username = copp.c_research_by
									   	LEFT JOIN cats.contract_mapping AS cm ON cm.value_map = copp.c_solicitation_number AND cm.field_name = 'Contract No'
									    LEFT JOIN cats.joborder AS job ON job.company_id = cm.data_item_id
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										DATE_FORMAT(FROM_UNIXTIME(copp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY copp.id
									HAVING total_job >= 1) AS tr
									GROUP BY tr.personnel_name) AS f ON f.personnel_name = a.personnel_name
									LEFT JOIN
									(SELECT
										CONCAT(u.firstName, ' ', u.lastName) AS personnel_name,
									    COUNT(DISTINCT CASE WHEN act.type = 'call' THEN act.id END) AS total_call,
									    COUNT(DISTINCT CASE WHEN act.type = 'emaildata' THEN act.id END) AS total_email,
									    COUNT(DISTINCT CASE WHEN act.type = 'note' THEN act.id END) AS total_comment,
									    COUNT(DISTINCT CASE WHEN act.type = 'event' THEN act.id END) AS total_meeting,
									    COUNT(DISTINCT CASE WHEN act.type = 'meaningfulData' THEN act.id END) AS total_meaningful
									FROM
										vtechcrm.x2_actions AS act
										LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
									WHERE
										CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
									AND
										act.associationType IN ('accounts','contacts','opportunities')
									AND
										act.type IN ('note','call','emaildata','meaningfulData','event')
									AND
										DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnel_name) AS g ON g.personnel_name = a.personnel_name
									LEFT JOIN
									(SELECT
									    all_main.personnel_name,
									    
									    SUM(IF(((pip_main.pipeline_main IS NULL) AND (pip_log.pipeline_log IS NOT NULL)), 1, IF(((pip_main.pipeline_main IS NOT NULL) AND (pip_log.pipeline_log IS NULL)), 1, IF(((pip_main.pipeline_main IS NOT NULL) AND (pip_log.pipeline_log IS NOT NULL)), 1, 0)))) AS total_pipeline,
									    
									    SUM(IF(((wor_main.working_main IS NULL) AND (wor_log.working_log IS NOT NULL)), 1, IF(((wor_main.working_main IS NOT NULL) AND (wor_log.working_log IS NULL)), 1, IF(((wor_main.working_main IS NOT NULL) AND (wor_log.working_log IS NOT NULL)), 1, 0)))) AS total_working,
									    
									    SUM(IF(((sub_main.submitted_main IS NULL) AND (sub_log.submitted_log IS NOT NULL)), 1, IF(((sub_main.submitted_main IS NOT NULL) AND (sub_log.submitted_log IS NULL)), 1, IF(((sub_main.submitted_main IS NOT NULL) AND (sub_log.submitted_log IS NOT NULL)), 1, 0)))) AS total_submitted,
									    
									    SUM(IF(((sec_main.second_stage_main IS NULL) AND (sec_log.second_stage_log IS NOT NULL)), 1, IF(((sec_main.second_stage_main IS NOT NULL) AND (sec_log.second_stage_log IS NULL)), 1, IF(((sec_main.second_stage_main IS NOT NULL) AND (sec_log.second_stage_log IS NOT NULL)), 1, 0)))) AS total_second_stage,
									    
									    SUM(IF(((won_mainn.won_main IS NULL) AND (won_logg.won_log IS NOT NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NOT NULL)), 1, 0)))) AS total_won,

									    SUM(IF(all_main.opp_share = 1, IF(((won_mainn.won_main IS NULL) AND (won_logg.won_log IS NOT NULL)), 0.5, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NULL)), 0.5, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NOT NULL)), 0.5, 0))), IF(((won_mainn.won_main IS NULL) AND (won_logg.won_log IS NOT NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NOT NULL)), 1, 0))))) AS total_shared_won
									FROM
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    COUNT(DISTINCT CASE WHEN opp.assignedTo != 'admin' AND opp.assignedTo != '' AND opp.assignedTo != 'Anyone' AND opp.c_research_by != 'admin' AND opp.c_research_by != '' AND opp.c_research_by != 'Anyone' AND opp.assignedTo != opp.c_research_by THEN opp.id END) AS opp_share
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									GROUP BY opportunity_id,personnel_name) AS all_main
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    opp.id AS pipeline_main
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    opp.salesStage = 'Pipeline'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS pip_main ON pip_main.opportunity_id = all_main.opportunity_id AND pip_main.personnel_name = all_main.personnel_name
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    opp.id AS working_main
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    opp.salesStage = 'Working'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS wor_main ON wor_main.opportunity_id = all_main.opportunity_id AND wor_main.personnel_name = all_main.personnel_name
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    opp.id AS submitted_main
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    opp.salesStage = 'Submitted'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS sub_main ON sub_main.opportunity_id = all_main.opportunity_id AND sub_main.personnel_name = all_main.personnel_name
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    opp.id AS second_stage_main
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    opp.salesStage = 'Second Stage'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS sec_main ON sec_main.opportunity_id = all_main.opportunity_id AND sec_main.personnel_name = all_main.personnel_name
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    opp.id AS won_main
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    opp.salesStage = 'Won'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS won_mainn ON won_mainn.opportunity_id = all_main.opportunity_id AND won_mainn.personnel_name = all_main.personnel_name
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    MAX(chg.id) AS pipeline_log
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									    chg.newValue = 'Pipeline'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS pip_log ON pip_log.opportunity_id = all_main.opportunity_id AND pip_log.personnel_name = all_main.personnel_name
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    MAX(chg.id) AS working_log
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									    chg.newValue = 'Working'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS wor_log ON wor_log.opportunity_id = all_main.opportunity_id AND wor_log.personnel_name = all_main.personnel_name
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    MAX(chg.id) AS submitted_log
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									    chg.newValue = 'Submitted'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS sub_log ON sub_log.opportunity_id = all_main.opportunity_id AND sub_log.personnel_name = all_main.personnel_name
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    MAX(chg.id) AS second_stage_log
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									    chg.newValue = 'Second Stage'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS sec_log ON sec_log.opportunity_id = all_main.opportunity_id AND sec_log.personnel_name = all_main.personnel_name
									LEFT JOIN
									(SELECT
									    opp.id AS opportunity_id,
									    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
									    MAX(chg.id) AS won_log
									FROM
									    vtechcrm.x2_opportunities AS opp
									    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									    chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									    chg.newValue = 'Won'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY opportunity_id,personnel_name) AS won_logg ON won_logg.opportunity_id = all_main.opportunity_id AND won_logg.personnel_name = all_main.personnel_name
									WHERE
									    (pip_main.pipeline_main != '' OR wor_main.working_main != '' OR sub_main.submitted_main != '' OR sec_main.second_stage_main != '' OR won_mainn.won_main != '' OR pip_log.pipeline_log != '' OR wor_log.working_log != '' OR sub_log.submitted_log != '' OR sec_log.second_stage_log != '' OR won_logg.won_log != '')
									GROUP BY personnel_name) AS h ON h.personnel_name = a.personnel_name
									LEFT JOIN
									(SELECT
										ef.value_map AS personnel_name,
										COUNT(DISTINCT e.id) AS resources_working
									FROM
										vtechhrm.employees AS e
										JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
										JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
										JOIN vtechhrm.employeetimeentry AS ete ON e.id = ete.employee
										JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
										JOIN cats.company AS comp ON comp.company_id = si.c_company_id
										JOIN cats.contract_mapping AS ef ON ef.data_item_id = comp.company_id
									WHERE
										ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By')
									AND
										ef.value_map IN ($personnelData)
									AND
										ep.project != '6'
									AND
										DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnel_name) AS i ON i.personnel_name = a.personnel_name
									LEFT JOIN
									(SELECT
										aa.personnel_name,
									    COUNT(DISTINCT mcca.min_client_checklist_answer) AS client_checklist_question_first_touch,
									    COUNT(DISTINCT mia.min_interview_answer) AS interview_question_follow_ups,
									    COUNT(DISTINCT mpa.min_premeeting_answer) AS premeeting_question_follow_ups
									FROM
									(SELECT
										CONCAT(u.firstName, ' ', u.lastName) AS personnel_name
									FROM
										vtechcrm.x2_users AS u
									WHERE
										CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
									GROUP BY personnel_name) AS aa
									LEFT JOIN
									(SELECT
									    CONCAT(u.firstName, ' ', u.lastName) AS personnel_name,
									    MIN(vqf.id) AS min_client_checklist_answer
									FROM
										vtech_tools.vtech_question_bank AS vqb
									    JOIN vtech_tools.vtech_question_feedback AS vqf ON vqf.question_id = vqb.id
									    JOIN vtechcrm.x2_users AS u ON u.id = vqf.user_id
									WHERE
										CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
									AND
										vqb.type = 'sales_client_qualification_checklist'
									AND
										DATE_FORMAT(vqf.created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY vqf.candidate_id) AS mcca ON mcca.personnel_name = aa.personnel_name
									LEFT JOIN
									(SELECT
									    CONCAT(u.firstName, ' ', u.lastName) AS personnel_name,
									    MIN(vqf.id) AS min_interview_answer
									FROM
										vtech_tools.vtech_question_bank AS vqb
									    JOIN vtech_tools.vtech_question_feedback AS vqf ON vqf.question_id = vqb.id
									    JOIN vtechcrm.x2_users AS u ON u.id = vqf.user_id
									WHERE
										CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
									AND
										vqb.type = 'sales_interview_questions'
									AND
										DATE_FORMAT(vqf.created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY vqf.candidate_id) AS mia ON mia.personnel_name = aa.personnel_name
									LEFT JOIN
									(SELECT
									    CONCAT(u.firstName, ' ', u.lastName) AS personnel_name,
									    MIN(vqf.id) AS min_premeeting_answer
									FROM
										vtech_tools.vtech_question_bank AS vqb
									    JOIN vtech_tools.vtech_question_feedback AS vqf ON vqf.question_id = vqb.id
									    JOIN vtechcrm.x2_users AS u ON u.id = vqf.user_id
									WHERE
										CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
									AND
										vqb.type = 'sales_premeeting_questions'
									AND
										DATE_FORMAT(vqf.created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY vqf.candidate_id) AS mpa ON mpa.personnel_name = aa.personnel_name
									GROUP BY personnel_name) AS j ON j.personnel_name = a.personnel_name
									WHERE
										(b.total_accounts != '' OR b.total_contacts != '' OR c.total_first_touch != '' OR d.total_follow_ups != '' OR e.total_show_ups != '' OR f.total_requirements != '' OR g.total_call != '' OR g.total_email != '' OR g.total_comment != '' OR g.total_meeting != '' OR g.total_meaningful != '' OR h.total_pipeline != '' OR h.total_working != '' OR h.total_submitted != '' OR h.total_second_stage != '' OR h.total_won != '' OR i.resources_working != '' OR a.personnel_status != '0')
									GROUP BY personnel_name";

									$mainRESULT = mysqli_query($sales_connect, $mainQUERY);
									
									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {
											
											$personnelNameValue = strtolower($mainROW["personnel_name"]);
											
											$taxRate = $mspFees = $primeCharges = $candidateRate = $grossMargin = $totalHour = 0;
											
											$totalGP = array();

											$delimiter = array("","[","]",'"');

											$currentDate = date("Ym");

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
												CAST((PERIOD_DIFF($currentDate,date_format(comp.date_created, '%Y%m')) / 12) AS DECIMAL(10,1)) AS cats_company_age,
												date_format(comp.date_created, '%Y-%m-%d') AS cats_company_create_date,
												(SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'Matrix' AND ic.comment = 'Client Age') AS given_company_age,
												(SELECT COUNT(*) AS share_amount FROM cats.extra_field AS ef WHERE ef.data_item_id = comp.company_id and ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By') AND ef.value != '') AS share_amount,
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
											    LEFT JOIN vtech_mappingdb.system_integration AS si ON e.id = si.h_employee_id
												LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
											    LEFT JOIN cats.extra_field AS ef ON ef.data_item_id = comp.company_id
												LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
												LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
											WHERE
												LOWER(ef.value) = '$personnelNameValue'
											AND
											    (ef.field_name = 'Inside Sales Person1' OR ef.field_name = 'Inside Sales Person2' OR ef.field_name = 'Research By')
											AND
												ep.project != '6'
											AND
												date_format(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
											GROUP BY employee_id";

											$subRESULT = mysqli_query($vtechhrmConn, $subQUERY);
											
											if (mysqli_num_rows($subRESULT) > 0) {
												while ($subROW = mysqli_fetch_array($subRESULT)) {
																
													$benefitList = str_replace($delimiter, $delimiter[0], $subROW["benefit_list"]);

													//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$subROW["benefit"],$benefitList,$subROW["employment_id"],$subROW["pay_rate"]), 2);

													$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$subROW["benefit"],$benefitList,$subROW["employment_id"],$subROW["pay_rate"]), 2);

													$mspFees = round((($subROW["client_msp_charge_percentage"] / 100) * $subROW["bill_rate"]) + $subROW["client_msp_charge_dollar"], 2);

													$primeCharges = round(((($subROW["client_prime_charge_percentage"] / 100) * $subROW["bill_rate"]) + (($subROW["employee_prime_charge_percentage"] / 100) * $subROW["bill_rate"]) + $subROW["employee_prime_charge_dollar"] + $subROW["employee_any_charge_dollar"] + $subROW["client_prime_charge_dollar"]), 2);

													$candidateRate = round(($subROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

													$grossMargin = round(($subROW["bill_rate"] - $candidateRate), 2);

													//$totalHour = round(employeeWorkingHours($vtechhrmConn,$startDate,$endDate,$subROW["employee_id"]), 2);

													$totalHour = round(array_sum($employeeTimeEntryTableData[$subROW["employee_id"]]), 2);

													$totalGP[] = round(($grossMargin * $totalHour) / $subROW["share_amount"], 2);
												}
											}

											$dataPopUp = "type=inside&person=".$personnelNameValue."&startDate=".$startDate."&endDate=".$endDate;
							?>
							<tr class="tbody-tr-style">
								<td nowrap>
								<?php
									echo ucwords($mainROW["personnel_name"]);
								?>
								</td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td>
								<?php
									echo $givenMonth;
								?>
								</td>
							<?php } ?>
								<td>
								<?php
									if ($mainROW["total_accounts"] == "") {
										echo $totalAccounts[] = "0";
									} else {
										echo $totalAccounts[] = $mainROW["total_accounts"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_contacts"] == "") {
										echo $totalContacts[] = "0";
									} else {
										echo $totalContacts[] = $mainROW["total_contacts"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_first_touch"] == "") {
										echo $totalFirstTouch[] = $mainROW["client_checklist_question_first_touch"];
									} else {
										echo $totalFirstTouch[] = $mainROW["total_first_touch"] + $mainROW["client_checklist_question_first_touch"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_meaningful"] == "") {
										echo $totalMeaningfulData[] = "0";
									} else {
										echo $totalMeaningfulData[] = $mainROW["total_meaningful"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_meeting"] == "") {	
										echo $totalMeetings[] = "0";
									} else {
										echo $totalMeetings[] = $mainROW["total_meeting"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_show_ups"] == "") {
										echo $totalShowUps[] = "0";
									} else {
										echo $totalShowUps[] = $mainROW["total_show_ups"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_follow_ups"] == "") {
										echo $totalFollowUps[] = $mainROW["interview_question_follow_ups"] + $mainROW["premeeting_question_follow_ups"];
									} else {
										echo $totalFollowUps[] = $mainROW["total_follow_ups"] + $mainROW["interview_question_follow_ups"] + $mainROW["premeeting_question_follow_ups"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_pipeline"] == "") {
										echo $totalPipeline[] = "0";
									} else {
										echo $totalPipeline[] = $mainROW["total_pipeline"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_working"] == "") {
										echo $totalWorking[] = "0";
									} else {
										echo $totalWorking[] = $mainROW["total_working"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_submitted"] == "") {
										echo $totalSubmitted[] = "0";
									} else {
										echo $totalSubmitted[] = $mainROW["total_submitted"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_second_stage"] == "") {
										echo $totalSecondStage[] = "0";
									} else {
										echo $totalSecondStage[] = $mainROW["total_second_stage"];
									}
								?>
								</td>
								<td style="position: relative;">
								<?php
									if ($mainROW["total_shared_won"] == "") {
										echo $totalWon[] = "0";
									} else {
										echo $totalWon[] = $mainROW["total_shared_won"] + 0;
									}
								?>
								<span class="wonDivision">
								<?php
									if ($mainROW["total_won"] == "") {
										echo "0";
									} else {
										echo $mainROW["total_won"];
									}
								?>
								</span>
								</td>
								<td>
								<?php
									if ($mainROW["total_requirements"] == "") {
										echo $totalRequirements[] = "0";
									} else {
										echo $totalRequirements[] = $mainROW["total_requirements"];
									}
								?>
								</td>
								<td>
								<?php
									if ($user == "1" || $user == "3") {

										$finalSow = round((($sowData[strtolower(trim($mainROW["personnel_name"]))] / 12) * $totalMonth), 2);
										
										if ($finalSow != '') { 
											array_push($totalGP, $finalSow);
										}
								?>
									<a class="margin-detail-popup" data-popup="<?php echo $dataPopUp; ?>"><?php echo $totalGrossProfit[] = round(array_sum($totalGP), 2); ?></a>
								<?php
									} else {
										echo $totalGrossProfit[] = round(array_sum($totalGP), 2);
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
								<th colspan="2">Total</th>
							<?php } else { ?>
								<th>Total</th>
							<?php } ?>
								<th><?php echo array_sum($totalAccounts); ?></th>
								<th><?php echo array_sum($totalContacts); ?></th>
								<th><?php echo array_sum($totalFirstTouch); ?></th>
								<th><?php echo array_sum($totalMeaningfulData); ?></th>
								<th><?php echo array_sum($totalMeetings); ?></th>
								<th><?php echo array_sum($totalShowUps); ?></th>
								<th><?php echo array_sum($totalFollowUps); ?></th>
								<th><?php echo array_sum($totalPipeline); ?></th>
								<th><?php echo array_sum($totalWorking); ?></th>
								<th><?php echo array_sum($totalSubmitted); ?></th>
								<th><?php echo array_sum($totalSecondStage); ?></th>
								<th><?php echo array_sum($totalWon); ?></th>
								<th><?php echo array_sum($totalRequirements); ?></th>
								<th><?php echo array_sum($totalGrossProfit); ?></th>
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
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/sales-matrix-margin-detail-popup.php",
			data: $(this).data("popup"),
			success: function(response) {
				$(".view-sales-matrix-margin-detail").modal("show");
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
