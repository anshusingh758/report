<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
	
    if (isset($user) && isset($userMember)) {
		$reportId = "57";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>BD Time Tracking By Client Report</title>

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
		table.dataTable thead tr:nth-child(2) th:last-child,
		table.dataTable thead tr:nth-child(3) th:last-child {
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
		.report-headline {
			background-color: #ccc;
			font-size: 15px;
			font-weight: bold;
			color: #333;
			text-align: center;
			padding: 3px 0px;
		}
		.panel-heading {
			cursor: pointer;
		}
		.panel-heading.report-heading {
			background-color: #b3d4f5;
			padding: 5px;
		}
		.panel-heading.report-heading .anchor-title {
			text-decoration: none;
			outline: none;
			color: #333;
			font-weight: bold;
			font-size: 18px;
		}
		.filter-individual-item{
			border: 3px solid #b3d4f5;
		}
		.filter-report .collapsable-row {
			position: relative;
			overflow: auto;
		}
		.filter-report .collapsable-row .result {
			overflow: auto;
		}
		.loading-logo-div {
			padding: 30px 0px 40px 0px;
			text-align: center;
		}
		.loading-logo-div span {
			font-size: 15px;
			color: #000;
		}
		#loading-logo {
			-webkit-animation: rotation 1s infinite linear;
		}
		@-webkit-keyframes rotation {
			from {
				-webkit-transform: rotate(0deg);
			}
			to {
				-webkit-transform: rotate(359deg);
			}
		}
		.error-message {
			text-align: center;
			margin: 30px auto;
			color: #333;
			font-size: 16px;
			font-weight: bold;
		}
		.view-average-margin-detail-popup .modal-lg {
			width: calc(100% - 100px);
		}
		.view-average-margin-detail-popup .modal-header {
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
				<div class="col-md-12 report-title">BD Time Tracking By Client Report</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					<div class="row">
						<div class="col-md-4">
							<button type="button" class="form-control months-button dark-button">Months</button>
						</div>
						<div class="col-md-4">
							<button type="button" class="form-control quarter-button smooth-button">Quarters</button>
						</div>
						<div class="col-md-4">
							<button type="button" class="form-control date-range-button smooth-button p-0">Date Range</button>
						</div>
					</div>
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
				<div class="row main-section-row multiple-quarter-input hidden">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Quarters:</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>

							<input type="text" name="customized-multiple-quarter" class="form-control customized-multiple-quarter" value="<?php if (isset($_REQUEST['customized-multiple-quarter'])) {echo $_REQUEST['customized-multiple-quarter'];}?>" placeholder="Quarters/YYYY" autocomplete="off" disabled>

							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						</div>
					</div>
				</div>
				<div class="row main-section-row date-range-input hidden">
					<div class="col-md-2 col-md-offset-4">
						<label>Date From :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							
							<input type="text" name="customized-start-date" class="form-control customized-date-picker customized-start-date" value="<?php if (isset($_REQUEST['customized-start-date'])) { echo $_REQUEST['customized-start-date']; }?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
						</div>
					</div>
					<div class="col-md-2">
						<label>Date To :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							
							<input type="text" name="customized-end-date" class="form-control customized-date-picker customized-end-date" value="<?php if (isset($_REQUEST['customized-end-date'])) { echo $_REQUEST['customized-end-date']; }?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
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
		
		$output = $dateRangeType = "";
		$dateRange = $finalReportArray = array();

		if (isset($_REQUEST["customized-multiple-month"])) {
			$dateRange = monthDateRange(array_unique(explode(",", $_REQUEST["customized-multiple-month"])));
			echo "<script>
				$(document).ready(function(){
					$('.months-button').trigger('click');
				});
			</script>";
		} elseif (isset($_REQUEST["customized-multiple-quarter"])) {
			$dateRange = quarterDateRange(array_unique(explode(",", $_REQUEST["customized-multiple-quarter"])));
			echo "<script>
				$(document).ready(function(){
					$('.quarter-button').trigger('click');
				});
			</script>";
		} elseif (isset($_REQUEST["customized-start-date"]) && isset($_REQUEST["customized-end-date"])) {
			$dateRange = normalDateRange($_REQUEST["customized-start-date"], $_REQUEST["customized-end-date"]);
			echo "<script>
				$(document).ready(function(){
					$('.date-range-button').trigger('click');
				});
			</script>";
		}

		$dateRangeType = $dateRange["filter_type"];

		array_shift($dateRange);

		foreach ($dateRange as $dateRangeKey => $dateRangeValue) {
			$startDate = $dateRangeValue["start_date"];
			$endDate = $dateRangeValue["end_date"];

			$totalDays = round((strtotime($dateRangeValue["end_date"]) - strtotime($dateRangeValue["start_date"])) / (60 * 60 * 24) + 1);

			$filterValue = $dateRangeValue["filter_value"];

			$mainQUERY = mysqli_query($allConn, "SELECT
			    a.account_id,
			    a.account_name,
			    a.account_create_date,
			    a.personnel_name,
			    a.personnel_status,
			    b.first_call_date,
			    c.first_email_date,
			    d.first_comment_date,
			    e.first_meeting_date,
			    f.first_meaningful_date,
			    g.first_showup_date,

			    IF((j.first_log_pipeline_date IS NOT NULL OR j.first_log_pipeline_date != ''), j.first_log_pipeline_date, IF((i.first_pipeline_date IS NOT NULL OR i.first_pipeline_date != ''), i.first_pipeline_date, '')) AS first_pipeline_date,

			    IF((l.first_log_working_date IS NOT NULL OR l.first_log_working_date != ''), l.first_log_working_date, IF((k.first_working_date IS NOT NULL OR k.first_working_date != ''), k.first_working_date, '')) AS first_working_date,

			    IF((n.first_log_submitted_date IS NOT NULL OR n.first_log_submitted_date != ''), n.first_log_submitted_date, IF((m.first_submitted_date IS NOT NULL OR m.first_submitted_date != ''), m.first_submitted_date, '')) AS first_submitted_date,

			    IF((p.first_log_second_stage_date IS NOT NULL OR p.first_log_second_stage_date != ''), p.first_log_second_stage_date, IF((o.first_second_stage_date IS NOT NULL OR o.first_second_stage_date != ''), o.first_second_stage_date, '')) AS first_second_stage_date,

			    IF((r.first_log_won_date IS NOT NULL OR r.first_log_won_date != ''), r.first_log_won_date, IF((q.first_won_date IS NOT NULL OR q.first_won_date != ''), q.first_won_date, '')) AS first_won_date,

			    IF((t.first_log_lost_date IS NOT NULL OR t.first_log_lost_date != ''), t.first_log_lost_date, IF((s.first_lost_date IS NOT NULL OR s.first_lost_date != ''), s.first_lost_date, '')) AS first_lost_date
			FROM
			(SELECT
			    acts.id AS account_id,
			    acts.name AS account_name,
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') AS account_create_date,
			    LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) AS personnel_name,
			    u.status AS personnel_status
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_users AS u ON u.username = acts.assignedTo
			WHERE
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS a
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d')) AS first_call_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_actions AS act ON act.associationName = acts.name
			WHERE
			    act.associationType = 'accounts'
			AND
			    act.type = 'call'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS b ON b.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d')) AS first_email_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_actions AS act ON act.associationName = acts.name
			WHERE
			    act.associationType = 'accounts'
			AND
			    act.type = 'emaildata'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS c ON c.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d')) AS first_comment_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_actions AS act ON act.associationName = acts.name
			WHERE
			    act.associationType = 'accounts'
			AND
			    act.type = 'note'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS d ON d.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d')) AS first_meeting_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_actions AS act ON act.associationName = acts.name
			WHERE
			    act.associationType = 'accounts'
			AND
			    act.type = 'event'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS e ON e.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d')) AS first_meaningful_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_actions AS act ON act.associationName = acts.name
			WHERE
			    act.associationType = 'accounts'
			AND
			    act.type = 'meaningfulData'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS f ON f.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%Y-%m-%d')) AS first_showup_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_actions AS act ON act.associationName = acts.name
			    JOIN vtechcrm.x2_events AS e ON e.associationId = act.id
			WHERE
			    act.type = 'event'
			AND
			    act.complete = 'Yes'
			AND
			    e.type = 'action_complete'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS g ON g.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') AS first_pipeline_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			WHERE
			    opp.salesStage = 'Pipeline'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS i ON i.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d')) AS first_log_pipeline_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			    JOIN vtechcrm.x2_changelog AS chg ON opp.id = chg.itemId
			WHERE
			    chg.type = 'Opportunity'
			AND
			    chg.fieldName = 'salesStage'
			AND
			    chg.newValue = 'Pipeline'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS j ON j.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') AS first_working_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			WHERE
			    opp.salesStage = 'Working'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS k ON k.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d')) AS first_log_working_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			    JOIN vtechcrm.x2_changelog AS chg ON opp.id = chg.itemId
			WHERE
			    chg.type = 'Opportunity'
			AND
			    chg.fieldName = 'salesStage'
			AND
			    chg.newValue = 'Working'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS l ON l.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') AS first_submitted_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			WHERE
			    opp.salesStage = 'Submitted'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS m ON m.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d')) AS first_log_submitted_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			    JOIN vtechcrm.x2_changelog AS chg ON opp.id = chg.itemId
			WHERE
			    chg.type = 'Opportunity'
			AND
			    chg.fieldName = 'salesStage'
			AND
			    chg.newValue = 'Submitted'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS n ON n.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') AS first_second_stage_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			WHERE
			    opp.salesStage = 'Second Stage'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS o ON o.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d')) AS first_log_second_stage_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			    JOIN vtechcrm.x2_changelog AS chg ON opp.id = chg.itemId
			WHERE
			    chg.type = 'Opportunity'
			AND
			    chg.fieldName = 'salesStage'
			AND
			    chg.newValue = 'Second Stage'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS p ON p.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') AS first_won_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			WHERE
			    opp.salesStage = 'Won'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS q ON q.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d')) AS first_log_won_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			    JOIN vtechcrm.x2_changelog AS chg ON opp.id = chg.itemId
			WHERE
			    chg.type = 'Opportunity'
			AND
			    chg.fieldName = 'salesStage'
			AND
			    chg.newValue = 'Won'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS r ON r.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') AS first_lost_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			WHERE
			    opp.salesStage = 'Lost'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS s ON s.account_id = a.account_id
			LEFT JOIN
			(SELECT
			    acts.id AS account_id,
			    MIN(DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d')) AS first_log_lost_date
			FROM
			    vtechcrm.x2_accounts AS acts
			    JOIN vtechcrm.x2_relationships AS rel ON rel.secondId = acts.id
			    JOIN vtechcrm.x2_opportunities AS opp ON opp.id = rel.firstId
			    JOIN vtechcrm.x2_changelog AS chg ON opp.id = chg.itemId
			WHERE
			    chg.type = 'Opportunity'
			AND
			    chg.fieldName = 'salesStage'
			AND
			    chg.newValue = 'Lost'
			AND
			    DATE_FORMAT(FROM_UNIXTIME(acts.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY account_id) AS t ON t.account_id = a.account_id");

			while ($mainROW = mysqli_fetch_array($mainQUERY)) {
				$finalReportArray[] = array(
					"account_id" => $mainROW["account_id"],
					"account_name" => $mainROW["account_name"],
					"account_create_date" => $mainROW["account_create_date"],
					"personnel_name" => ucwords($mainROW["personnel_name"]),
					"personnel_status" => $mainROW["personnel_status"],

			        "daterange_type" => $filterValue,

					"first_call_date" => $mainROW["first_call_date"],
					"first_call_days" => $mainROW["first_call_date"] == "" ? "0" : round((strtotime($mainROW["first_call_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_email_date" => $mainROW["first_email_date"],
					"first_email_days" => $mainROW["first_email_date"] == "" ? "0" : round((strtotime($mainROW["first_email_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_comment_date" => $mainROW["first_comment_date"],
					"first_comment_days" => $mainROW["first_comment_date"] == "" ? "0" : round((strtotime($mainROW["first_comment_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_meeting_date" => $mainROW["first_meeting_date"],
					"first_meeting_days" => $mainROW["first_meeting_date"] == "" ? "0" : round((strtotime($mainROW["first_meeting_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_meaningful_date" => $mainROW["first_meaningful_date"],
					"first_meaningful_days" => $mainROW["first_meaningful_date"] == "" ? "0" : round((strtotime($mainROW["first_meaningful_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_showup_date" => $mainROW["first_showup_date"],
					"first_showup_days" => $mainROW["first_showup_date"] == "" ? "0" : round((strtotime($mainROW["first_showup_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_pipeline_date" => $mainROW["first_pipeline_date"],
					"first_pipeline_days" => $mainROW["first_pipeline_date"] == "" ? "0" : round((strtotime($mainROW["first_pipeline_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_working_date" => $mainROW["first_working_date"],
					"first_working_days" => $mainROW["first_working_date"] == "" ? "0" : round((strtotime($mainROW["first_working_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_submitted_date" => $mainROW["first_submitted_date"],
					"first_submitted_days" => $mainROW["first_submitted_date"] == "" ? "0" : round((strtotime($mainROW["first_submitted_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_second_stage_date" => $mainROW["first_second_stage_date"],
					"first_second_stage_days" => $mainROW["first_second_stage_date"] == "" ? "0" : round((strtotime($mainROW["first_second_stage_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_won_date" => $mainROW["first_won_date"],
					"first_won_days" => $mainROW["first_won_date"] == "" ? "0" : round((strtotime($mainROW["first_won_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24)),

					"first_lost_date" => $mainROW["first_lost_date"],
					"first_lost_days" => $mainROW["first_lost_date"] == "" ? "0" : round((strtotime($mainROW["first_lost_date"]) - strtotime($mainROW["account_create_date"])) / (60 * 60 * 24))
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
								<th rowspan="2">Client</th>
								<th rowspan="2">Create Date</th>
								<th rowspan="2">Assigned To</th>
								<th colspan="12">Total No. of Days for First</th>
							</tr>
							<tr class="thead-tr-style">
								<th>Call</th>
								<th>Email</th>
								<th>Comment</th>
								<th>Meeting</th>
								<th>ShowUp</th>
								<th>Meaningful</th>
								<th>Pipeline</th>
								<th>Working</th>
								<th>Submitted</th>
								<th>Second Stage</th>
								<th>Won</th>
								<th>Loss</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach ($finalReportArray as $finalReportKey => $finalReportValue) {
						?>
							<tr class="tbody-tr-style">
								<td><?php echo $finalReportValue["account_name"]; ?></td>
								<td><?php echo $finalReportValue["account_create_date"]; ?></td>
								<td><?php echo $finalReportValue["personnel_name"]; ?></td>
								<td><?php echo $finalReportValue["first_call_days"]; ?></td>
								<td><?php echo $finalReportValue["first_email_days"]; ?></td>
								<td><?php echo $finalReportValue["first_comment_days"]; ?></td>
								<td><?php echo $finalReportValue["first_meeting_days"]; ?></td>
								<td><?php echo $finalReportValue["first_showup_days"]; ?></td>
								<td><?php echo $finalReportValue["first_meaningful_days"]; ?></td>
								<td><?php echo $finalReportValue["first_pipeline_days"]; ?></td>
								<td><?php echo $finalReportValue["first_working_days"]; ?></td>
								<td><?php echo $finalReportValue["first_submitted_days"]; ?></td>
								<td><?php echo $finalReportValue["first_second_stage_days"]; ?></td>
								<td><?php echo $finalReportValue["first_won_days"]; ?></td>
								<td><?php echo $finalReportValue["first_lost_days"]; ?></td>
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

		$.fn.datepicker.dates["qtrs"] = {
			days: ["Sunday", "Moonday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
			daysShort: ["Sun", "Moon", "Tue", "Wed", "Thu", "Fri", "Sat"],
			daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
			months: ["Q1", "Q2", "Q3", "Q4", "", "", "", "", "", "", "", ""],
			monthsShort: ["Q1", "Q2", "Q3", "Q4", "", "", "", "", "", "", "", ""],
			today: "Today",
			clear: "Clear",
			format: "mm/dd/yyyy",
			titleFormat: "MM yyyy",
			/* Leverages same syntax as 'format' */
			weekStart: 0,
		};

		$(".customized-multiple-quarter").datepicker({
			format: "MM/yyyy",
			minViewMode: 1,
			language: "qtrs",
			forceParse: false,
			clearBtn: true,
			multidate: true,
            orientation: "top",
			autoclose: false,
		}).on("show", function(event) {
			$(".month").each(function(index, element) {
				if (index > 3) $(element).hide();
			});
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
/*
	$(document).on("submit", ".form-submit-action", function(e){
		e.preventDefault();
		let givenFilterList = <?php echo json_encode($filterBy); ?>;
		let selectedFilterList = $(this).find("select[name='filter-by']").val();

		var selectedFilterId = filterTitle = filterURL = "";

		$(".filter-report").html("");
		
		$(selectedFilterList).each(function(){
			selectedFilterId = this;
				
			$.each(givenFilterList, function (index, value) {
				if (selectedFilterId == index) {
				    filterTitle = value.title;
				    filterURL = value.url;
				    filterClass = value.class;
				    return false;
				}
			});

			$(".filter-report").append("<div class='panel panel-default filter-individual-item'><div class='panel-heading report-heading' data-toggle='collapse' href='#"+filterClass+"'><a class='anchor-title'>"+filterTitle+" <i class='fa fa-caret-down'></i></a></div><div id='"+filterClass+"' class='panel-body collapsable-row panel-collapse collapse in'><div class='result' data-url='"+filterURL+"'></div><div class='error-message hidden'>Something Wrong!</div><div class='loading-logo-div'><img src='<?php echo IMAGE_PATH; ?>/logo.png' id='loading-logo'><br><span>Loading...</span></div></div></div>");
		});

		let data = null;
		let multipleMonth = $(".customized-multiple-month").val();
		let multipleQuarter = $(".customized-multiple-quarter").val();
		let startDate = $(".customized-start-date").val();
		let endDate = $(".customized-end-date").val();

		if ($(".multiple-month-input").hasClass("hidden") === true) {
			multipleMonth = "";
		}

		if ($(".multiple-quarter-input").hasClass("hidden") === true) {
			multipleQuarter = "";
		}

		if ($(".date-range-input").hasClass("hidden") === true) {
			startDate = "";
			endDate = "";
		}

		data = {
			multipleMonth: multipleMonth,
			multipleQuarter: multipleQuarter,
			startDate: startDate,
			endDate: endDate
		}

		$(".filter-individual-item").each(function(){
			let that = this;
			let resultContainer = $(that).find(".result");
			let url = resultContainer.data("url");

			if (typeof(url) !== 'undefined' && url !== "") {
				$.ajax({
					url: url,
					type: "POST",
					data: data,
					success: function(response){
						//console.log(response);
						resultContainer.html(response);
						resultContainer.siblings(".loading-logo-div").addClass("hidden");
						resultContainer.siblings(".error-message").addClass("hidden");
					},
					error: function(error){
						resultContainer.html("");
						resultContainer.siblings(".loading-logo-div").addClass("hidden");
						resultContainer.siblings(".error-message").removeClass("hidden");
					}
				});
			} else {
					resultContainer.html("");
					resultContainer.siblings(".loading-logo-div").addClass("hidden");
					resultContainer.siblings(".error-message").removeClass("hidden");
			}
		});

	});
*/
	$(document).on("click", ".months-button", function(e){
		e.preventDefault();
		$(".customized-multiple-month").prop("required", true);
		$(".customized-date-picker, .customized-multiple-quarter").prop("required", false);
		$(".customized-multiple-month").prop("disabled", false);
		$(".customized-date-picker, .customized-multiple-quarter").prop("disabled", true);
		$(".date-range-input, .multiple-quarter-input").addClass("hidden");
		$(".multiple-month-input").removeClass("hidden");
		$(".months-button").addClass("dark-button");
		$(".months-button").removeClass("smooth-button");
		$(".date-range-button, .quarter-button").addClass("smooth-button");
		$(".date-range-button, .quarter-button").removeClass("dark-button");
	});

	$(document).on("click", ".quarter-button", function(e){
		e.preventDefault();
		$(".customized-multiple-quarter").prop("required", true);
		$(".customized-date-picker, .customized-multiple-month").prop("required", false);
		$(".customized-multiple-quarter").prop("disabled", false);
		$(".customized-date-picker, .customized-multiple-month").prop("disabled", true);
		$(".date-range-input, .multiple-month-input").addClass("hidden");
		$(".multiple-quarter-input").removeClass("hidden");
		$(".quarter-button").addClass("dark-button");
		$(".quarter-button").removeClass("smooth-button");
		$(".date-range-button, .months-button").addClass("smooth-button");
		$(".date-range-button, .months-button").removeClass("dark-button");
	});

	$(document).on("click", ".date-range-button", function(e){
		e.preventDefault();
		$(".customized-date-picker").prop("required", true);
		$(".customized-multiple-month, .customized-multiple-quarter").prop("required", false);
		$(".customized-date-picker").prop("disabled", false);
		$(".customized-multiple-month, .customized-multiple-quarter").prop("disabled", true);
		$(".date-range-input").removeClass("hidden");
		$(".multiple-month-input, .multiple-quarter-input").addClass("hidden");
		$(".date-range-button").addClass("dark-button");
		$(".date-range-button").removeClass("smooth-button");
		$(".months-button, .quarter-button").addClass("smooth-button");
		$(".months-button, .quarter-button").removeClass("dark-button");
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
