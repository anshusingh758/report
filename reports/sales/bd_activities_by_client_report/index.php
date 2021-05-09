<?php
	error_reporting(0);
	header("Content-Type: text/html; charset=ISO-8859-1");
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "52";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>BD Activities By Client Report</title>

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
			text-align: right;
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
				<div class="col-md-12 report-title">BD Activities By Client Report</div>
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
		
		$fromDate = $toDate = $bdgTeam = array();
		
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
		$bdgTeam = salesGroupPersonnelList($sales_connect,"1");
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th rowspan="2">Client</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th rowspan="2">Months</th>
							<?php } ?>
								<th rowspan="2">Assigned To</th>
								<th colspan="6">Total No. of Logs in</th>
								<th colspan="6">Total Opportunities in</th>
							</tr>
							<tr class="thead-tr-style">
								<th>Call</th>
								<th>Email</th>
								<th>Comment</th>
								<th>Meetings</th>
								<th>ShowUps</th>
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
								$totalCall = $totalEmail = $totalComment = $totalMeeting = $totalShowUps = $totalMeaningful = $totalPipeline = $totalWorking = $totalSubmitted = $totalSecondStage = $totalWon = $totalLost = array();

								foreach ($fromDate as $fromDateKey => $fromDateValue) {
									$startDate = strtotime($fromDate[$fromDateKey]);
									$endDate = strtotime($toDate[$fromDateKey]);

									$givenMonth = date("m/Y", $startDate);

									$mainQUERY = "SELECT
										a.account_id,
										a.account_name,
										a.personnel_name,
										a.status,
									    log.total_pipeline_log,
									    log.total_working_log,
									    log.total_submitted_log,
									    log.total_second_stage_log,
									    log.total_won_log,
									    log.total_won_log_divide,
									    log.total_won_log_same_name,
									    log.total_lost_log,
									    log_merge.total_pipeline_merge,
									    log_merge.total_working_merge,
									    log_merge.total_submitted_merge,
									    log_merge.total_second_stage_merge,
									    log_merge.total_won_merge,
									    log_merge.total_won_merge_divide,
									    log_merge.total_won_merge_same_name,
									    log_merge.total_lost_merge,
									    main.total_pipeline_main,
									    main.total_working_main,
									    main.total_submitted_main,
									    main.total_second_stage_main,
									    main.total_won_main,
									    main.total_won_main_divide,
									    main.total_won_main_same_name,
									    main.total_lost_main,
										lost_won_data.lost_won_numbers,
										lost_won_data.lost_won_numbers_divide,
										lost_won_data.lost_won_numbers_same_name,
									    e.total_call,
										e.total_email,
										e.total_comment,
										e.total_meeting,
										e.total_meaningful,
										i.total_show_ups
									FROM
									(SELECT
										act.id AS account_id,
										act.name AS account_name,
										concat(u.firstName,' ',u.lastName) AS personnel_name,
										u.status
									FROM
										x2_accounts AS act
										JOIN x2_users AS u ON u.username = act.assignedTo
									GROUP BY account_id) AS a
									LEFT OUTER JOIN
									(SELECT
									    opp.id AS oppId,
										act.id AS account_id,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Pipeline' THEN opp.id END) AS total_pipeline_log,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Working' THEN opp.id END) AS total_working_log,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Submitted' THEN opp.id END) AS total_submitted_log,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Second Stage' THEN opp.id END) AS total_second_stage_log,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Won' THEN opp.id END) AS total_won_log,
									    COUNT(DISTINCT CASE WHEN opp.assignedTo != 'admin' AND opp.assignedTo != '' AND opp.assignedTo != 'Anyone' AND opp.c_research_by != 'admin' AND opp.c_research_by != '' AND opp.c_research_by != 'Anyone' AND chg.newValue = 'Won' THEN opp.id END) AS total_won_log_divide,
									    COUNT(DISTINCT CASE WHEN opp.assignedTo = u.username AND opp.c_research_by = u.username AND chg.newValue = 'Won' THEN opp.id END) AS total_won_log_same_name,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Lost' THEN opp.id END) AS total_lost_log
									FROM
										x2_opportunities AS opp
									    JOIN x2_changelog AS chg ON opp.id = chg.itemId
									    JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									    JOIN x2_relationships AS rel ON opp.id = rel.firstId
									    JOIN x2_accounts AS act ON rel.secondId = act.id
									WHERE
									    rel.firstType = 'Opportunity'
									AND
									    rel.secondType = 'Accounts'
									AND
									    chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									    chg.newValue IN ('Pipeline','Working','Submitted','Second Stage','Won','Lost')
									AND
									    chg.timestamp BETWEEN '$startDate' AND '$endDate'
									AND
									chg.itemId NOT IN (SELECT
									        oppx.id
									    FROM
									        x2_opportunities AS oppx
									    WHERE
									        oppx.salesStage IN ('Pipeline','Working','Submitted','Second Stage','Won','Lost')
									    AND
									        oppx.id = chg.itemId
									    AND
									        oppx.createDate BETWEEN '$startDate' AND '$endDate')
									GROUP BY account_id) AS log ON a.account_id = log.account_id
									LEFT OUTER JOIN
									(SELECT
									    opp.id AS oppId,
										act.id AS account_id,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Pipeline' THEN opp.id END) AS total_pipeline_merge,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Working' THEN opp.id END) AS total_working_merge,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Submitted' THEN opp.id END) AS total_submitted_merge,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Second Stage' THEN opp.id END) AS total_second_stage_merge,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Won' THEN opp.id END) AS total_won_merge,
									    COUNT(DISTINCT CASE WHEN opp.assignedTo != 'admin' AND opp.assignedTo != '' AND opp.assignedTo != 'Anyone' AND opp.c_research_by != 'admin' AND opp.c_research_by != '' AND opp.c_research_by != 'Anyone' AND chg.newValue = 'Won' THEN opp.id END) AS total_won_merge_divide,
									    COUNT(DISTINCT CASE WHEN opp.assignedTo = u.username AND opp.c_research_by = u.username AND chg.newValue = 'Won' THEN opp.id END) AS total_won_merge_same_name,
									    COUNT(DISTINCT CASE WHEN chg.newValue = 'Lost' THEN opp.id END) AS total_lost_merge
									FROM
										x2_opportunities AS opp
									    JOIN x2_changelog AS chg ON opp.id = chg.itemId
									    JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									    JOIN x2_relationships AS rel ON opp.id = rel.firstId
									    JOIN x2_accounts AS act ON rel.secondId = act.id
									WHERE
									    rel.firstType = 'Opportunity'
									AND
									    rel.secondType = 'Accounts'
									AND
									    chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									    chg.newValue IN ('Pipeline','Working','Submitted','Second Stage','Won','Lost')
									AND
									    chg.timestamp BETWEEN '$startDate' AND '$endDate'
									AND
									chg.itemId IN (SELECT
									        oppx.id
									    FROM
									        x2_opportunities AS oppx
									    WHERE
									        oppx.salesStage IN ('Pipeline','Working','Submitted','Second Stage','Won','Lost')
									    AND
									        oppx.id = chg.itemId
									    AND
									        oppx.createDate BETWEEN '$startDate' AND '$endDate')
									GROUP BY account_id) AS log_merge ON a.account_id = log_merge.account_id
									LEFT OUTER JOIN
									(SELECT
									    opp.id AS oppId,
										act.id AS account_id,
									    COUNT(DISTINCT CASE WHEN opp.salesStage = 'Pipeline' THEN opp.id END) AS total_pipeline_main,
									    COUNT(DISTINCT CASE WHEN opp.salesStage = 'Working' THEN opp.id END) AS total_working_main,
									    COUNT(DISTINCT CASE WHEN opp.salesStage = 'Submitted' THEN opp.id END) AS total_submitted_main,
									    COUNT(DISTINCT CASE WHEN opp.salesStage = 'Second Stage' THEN opp.id END) AS total_second_stage_main,
									    COUNT(DISTINCT CASE WHEN opp.salesStage = 'Won' THEN opp.id END) AS total_won_main,
									    COUNT(DISTINCT CASE WHEN opp.assignedTo != 'admin' AND opp.assignedTo != '' AND opp.assignedTo != 'Anyone' AND opp.c_research_by != 'admin' AND opp.c_research_by != '' AND opp.c_research_by != 'Anyone' AND opp.salesStage = 'Won' THEN opp.id END) AS total_won_main_divide,
									    COUNT(DISTINCT CASE WHEN opp.assignedTo = u.username AND opp.c_research_by = u.username AND opp.salesStage = 'Won' THEN opp.id END) AS total_won_main_same_name,
									    COUNT(DISTINCT CASE WHEN opp.salesStage = 'Lost' THEN opp.id END) AS total_lost_main
									FROM
										x2_opportunities AS opp
									    JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									    JOIN x2_relationships AS rel ON opp.id = rel.firstId
									    JOIN x2_accounts AS act ON rel.secondId = act.id
									WHERE
									    rel.firstType = 'Opportunity'
									AND
									    rel.secondType = 'Accounts'
									AND
									    opp.salesStage IN ('Pipeline','Working','Submitted','Second Stage','Won','Lost')
									AND
									    opp.createDate BETWEEN '$startDate' AND '$endDate'
									AND
									opp.id NOT IN (SELECT
									        chg.itemId
									    FROM
									        x2_changelog AS chg
									    WHERE
									        chg.type = 'Opportunity'
									    AND
									        chg.fieldName = 'salesStage'
									    AND
									        chg.newValue IN ('Pipeline','Working','Submitted','Second Stage','Won','Lost')
									    AND
									        chg.itemId = opp.id
									    AND
									        chg.timestamp BETWEEN '$startDate' AND '$endDate')
									GROUP BY account_id) AS main ON a.account_id = main.account_id
									LEFT OUTER JOIN
									(SELECT
										act.id AS account_id,
									    COUNT(DISTINCT opp.id) AS lost_won_numbers,
									    COUNT(DISTINCT CASE WHEN opp.assignedTo != 'admin' AND opp.assignedTo != '' AND opp.assignedTo != 'Anyone' AND opp.c_research_by != 'admin' AND opp.c_research_by != '' AND opp.c_research_by != 'Anyone' THEN opp.id END) AS lost_won_numbers_divide,
									    COUNT(DISTINCT CASE WHEN opp.assignedTo = u.username AND opp.c_research_by = u.username THEN opp.id END) AS lost_won_numbers_same_name
									FROM
										x2_opportunities AS opp
									    JOIN x2_changelog AS chg ON opp.id = chg.itemId
									    JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									    JOIN x2_relationships AS rel ON opp.id = rel.firstId
									    JOIN x2_accounts AS act ON rel.secondId = act.id
									WHERE
									    rel.firstType = 'Opportunity'
									AND
									    rel.secondType = 'Accounts'
									AND
									    chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									    chg.newValue = 'Won'
									AND
									    opp.salesStage != 'Won'
									AND
									    chg.timestamp BETWEEN '$startDate' AND '$endDate'
									GROUP BY account_id) AS lost_won_data ON a.account_id = lost_won_data.account_id
									LEFT OUTER JOIN
									(SELECT
									    acts.id AS account_id,
									    COUNT(CASE WHEN act.type = 'call' THEN act.id END) AS total_call,
									    COUNT(CASE WHEN act.type = 'emaildata' THEN act.id END) AS total_email,
									    COUNT(CASE WHEN act.type = 'note' THEN act.id END) AS total_comment,
									    COUNT(CASE WHEN act.type = 'event' THEN act.id END) AS total_meeting,
									    COUNT(CASE WHEN act.type = 'meaningfulData' THEN act.id END) AS total_meaningful
									FROM
									    x2_actions AS act
									    JOIN x2_users AS u ON u.username = act.completedBy
									    JOIN x2_accounts AS acts ON act.associationName = acts.name
									WHERE
										act.associationType = 'accounts'
									AND
										act.type IN ('call','emaildata','note','event','meaningfulData')
									AND
										act.createDate BETWEEN '$startDate' AND '$endDate'
									GROUP BY account_id) AS e ON a.account_id = e.account_id
									LEFT OUTER JOIN
									(SELECT
									    acts.id AS account_id,
									    COUNT(e.id) AS total_show_ups
									FROM
										x2_actions AS act
									    JOIN x2_events AS e ON e.associationId = act.id
									    JOIN x2_users AS u ON u.username = e.user
									    JOIN x2_accounts AS acts ON act.associationName = acts.name
									WHERE
										act.type = 'event'
									AND
										act.complete = 'Yes'
									AND
										e.type = 'action_complete'
									AND
										e.timestamp BETWEEN '$startDate' AND '$endDate'
									GROUP BY account_id) AS i ON a.account_id = i.account_id
									WHERE
										(log.total_pipeline_log != '' OR log.total_working_log != '' OR log.total_submitted_log != '' OR log.total_second_stage_log != '' OR log.total_won_log != '' OR log.total_won_log_same_name != '' OR log.total_won_log_divide != '' OR log.total_lost_log != '' OR log_merge.total_pipeline_merge != '' OR log_merge.total_working_merge != '' OR log_merge.total_submitted_merge != '' OR log_merge.total_second_stage_merge != '' OR log_merge.total_won_merge != '' OR log_merge.total_won_merge_same_name != '' OR log_merge.total_won_merge_divide != '' OR log_merge.total_lost_merge != '' OR main.total_pipeline_main != '' OR main.total_working_main != '' OR main.total_submitted_main != '' OR main.total_second_stage_main != '' OR main.total_won_main != '' OR main.total_won_main_same_name != '' OR main.total_won_main_divide != '' OR main.total_lost_main != '' OR lost_won_data.lost_won_numbers != '' OR lost_won_data.lost_won_numbers_divide != '' OR lost_won_data.lost_won_numbers_same_name != '' OR e.total_call != '' OR e.total_email != '' OR e.total_comment != '' OR e.total_meeting != '' OR e.total_meaningful != '' OR i.total_show_ups != '')";

									$mainRESULT = mysqli_query($sales_connect, $mainQUERY);
									
									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {

											$totalPipelineData = $totalWorkingData = $totalSubmittedData = $totalSecondStageData = $totalWonData = $totalLostData = 0;

											$totalPipelineData = ($mainROW["total_pipeline_log"] + $mainROW["total_pipeline_merge"] + $mainROW["total_pipeline_main"]);

											$totalWorkingData = ($mainROW["total_working_log"] + $mainROW["total_working_merge"] + $mainROW["total_working_main"]);

											$totalSubmittedData = ($mainROW["total_submitted_log"] + $mainROW["total_submitted_merge"] + $mainROW["total_submitted_main"]);

											$totalSecondStageData = ($mainROW["total_second_stage_log"] + $mainROW["total_second_stage_merge"] + $mainROW["total_second_stage_main"]);

											$totalWonData = ($mainROW["total_won_log"] + $mainROW["total_won_merge"] + $mainROW["total_won_main"]) - $mainROW["lost_won_numbers"];
											
											$totalLostData = ($mainROW["total_lost_log"] + $mainROW["total_lost_merge"] + $mainROW["total_lost_main"]);
							?>
							<tr class="tbody-tr-style">
								<td>
									<a href="https://sales.vtechsolution.com/index.php/accounts/<?php echo $mainROW["account_id"]; ?>" target="_blank"><?php echo $mainROW["account_name"]; ?></a>
								</td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td>
									<?php echo $givenMonth; ?>
								</td>
							<?php } ?>
								<td>
									<?php echo ucwords($mainROW["personnel_name"]); ?>
								</td>
								<td>
								<?php
									if ($mainROW["total_call"] == "") {
										echo $totalCall[] = "0";
									} else {
										echo $totalCall[] = $mainROW["total_call"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_email"] == "") {
										echo $totalEmail[] = "0";
									} else {
										echo $totalEmail[] = $mainROW["total_email"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_comment"] == "") {
										echo $totalComment[] = "0";
									} else {
										echo $totalComment[] = $mainROW["total_comment"];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_meeting"] == "") {
										echo $totalMeeting[] = "0";
									} else {
										echo $totalMeeting[] = $mainROW["total_meeting"];
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
									if ($mainROW["total_meaningful"] == "") {
										echo $totalMeaningful[] = "0";
									} else {
										echo $totalMeaningful[] = $mainROW["total_meaningful"];
									}
								?>
								</td>
								<td>
								<?php
									echo $totalPipeline[] = $totalPipelineData;
								?>
								</td>
								<td>
								<?php
									echo $totalWorking[] = $totalWorkingData;
								?>
								</td>
								<td>
								<?php
									echo $totalSubmitted[] = $totalSubmittedData;
								?>
								</td>
								<td>
								<?php
									echo $totalSecondStage[] = $totalSecondStageData;
								?>
								</td>
								<td>
								<?php
									echo $totalWon[] = $totalWonData;
								?>
								</td>
								<td>
								<?php
									echo $totalLost[] = $totalLostData;
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
								<th colspan="3">Total</th>
							<?php } else { ?>
								<th colspan="2">Total</th>
							<?php } ?>
								<th><?php echo array_sum($totalCall); ?></th>
								<th><?php echo array_sum($totalEmail); ?></th>
								<th><?php echo array_sum($totalComment); ?></th>
								<th><?php echo array_sum($totalMeeting); ?></th>
								<th><?php echo array_sum($totalShowUps); ?></th>
								<th><?php echo array_sum($totalMeaningful); ?></th>
								<th><?php echo array_sum($totalPipeline); ?></th>
								<th><?php echo array_sum($totalWorking); ?></th>
								<th><?php echo array_sum($totalSubmitted); ?></th>
								<th><?php echo array_sum($totalSecondStage); ?></th>
								<th><?php echo array_sum($totalWon); ?></th>
								<th><?php echo array_sum($totalLost); ?></th>
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
