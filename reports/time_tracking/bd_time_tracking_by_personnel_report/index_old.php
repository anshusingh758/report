<?php
	error_reporting(0);
	header("Content-Type: text/html; charset=ISO-8859-1");
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "56";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
			$sessionROW = mysqli_fetch_array($sessionQUERY);
?>
<!DOCTYPE html>
<html>
<head>
	<title>BD Time Tracking By Personnel Report</title>

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
				<div class="col-md-12 report-title">BD Time Tracking By Personnel Report</div>
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

			<form action="index_old.php" method="post">
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
		
		$fromDate = $toDate = $bdgTeam = $bdcTeam = $usbdTeam = $allTeam = array();
		
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
		$bdcTeam = salesGroupPersonnelList($sales_connect,"2");
		$usbdTeam = salesGroupPersonnelList($sales_connect,"3");
		$allTeam = array_merge($bdgTeam, $bdcTeam, $usbdTeam);

		if ($sessionROW["user_type"] == "Inside Sales (Gov.)") {
			$personnelData = "'".implode("', '",$bdgTeam)."'";
		} elseif ($sessionROW["user_type"] == "Inside Sales (Com.)") {
			$personnelData = "'".implode("', '",$bdcTeam)."'";
		} elseif ($sessionROW["user_type"] == "Onsite Sales" || $sessionROW["user_type"] == "Onsite Post Sales") {
			$personnelData = "'".implode("', '",$usbdTeam)."'";
		} else {
			$personnelData = "'".implode("', '",$allTeam)."'";
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
							<?php if (isset($_REQUEST['customized-multiple-month'])) { ?>
								<th rowspan="2">Months</th>
							<?php } ?>
								<th colspan="12">Date of First</th>
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
								foreach ($fromDate as $fromDateKey => $fromDateValue) {
									$startDate = $fromDate[$fromDateKey];
									$endDate = $toDate[$fromDateKey];

									$givenMonth = date("m/Y", strtotime($startDate));

									$mainQUERY = "SELECT
										a.personnelName,
										a.status,
										DATE_FORMAT(FROM_UNIXTIME(b.pipelineDateMain), '%m-%d-%Y') AS pipelineDateMain,
										DATE_FORMAT(FROM_UNIXTIME(c.PipelineDateLog), '%m-%d-%Y') AS PipelineDateLog,
										DATE_FORMAT(FROM_UNIXTIME(d.workingDateMain), '%m-%d-%Y') AS workingDateMain,
										DATE_FORMAT(FROM_UNIXTIME(e.workingDateLog), '%m-%d-%Y') AS workingDateLog,
										DATE_FORMAT(FROM_UNIXTIME(f.submittedDateMain), '%m-%d-%Y') AS submittedDateMain,
										DATE_FORMAT(FROM_UNIXTIME(g.submittedDateLog), '%m-%d-%Y') AS submittedDateLog,
										DATE_FORMAT(FROM_UNIXTIME(h.secondStageDateMain), '%m-%d-%Y') AS secondStageDateMain,
										DATE_FORMAT(FROM_UNIXTIME(i.secondStageDateLog), '%m-%d-%Y') AS secondStageDateLog,
										DATE_FORMAT(FROM_UNIXTIME(j.wonDateMain), '%m-%d-%Y') AS wonDateMain,
										DATE_FORMAT(FROM_UNIXTIME(k.wonDateLog), '%m-%d-%Y') AS wonDateLog,
										DATE_FORMAT(FROM_UNIXTIME(l.lostDateMain), '%m-%d-%Y') AS lostDateMain,
										DATE_FORMAT(FROM_UNIXTIME(m.lostDateLog), '%m-%d-%Y') AS lostDateLog,
										DATE_FORMAT(FROM_UNIXTIME(callData.callDate), '%m-%d-%Y') AS callDate,
										DATE_FORMAT(FROM_UNIXTIME(emailData.emailDate), '%m-%d-%Y') AS emailDate,
										DATE_FORMAT(FROM_UNIXTIME(noteData.noteDate), '%m-%d-%Y') AS noteDate,
										DATE_FORMAT(FROM_UNIXTIME(eventData.eventDate), '%m-%d-%Y') AS eventDate,
										DATE_FORMAT(FROM_UNIXTIME(meaningfulData.meaningfulDate), '%m-%d-%Y') AS meaningfulDate,
										DATE_FORMAT(FROM_UNIXTIME(showup.showUpDate), '%m-%d-%Y') AS showUpDate
									FROM
									(SELECT
									 	concat(u.firstName,' ',u.lastName) AS personnelName,
									 	u.status
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										concat(u.firstName,' ',u.lastName) IN ($personnelData)
									GROUP BY personnelName) AS a
									LEFT OUTER JOIN
									(SELECT
									    concat(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS pipelineDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Pipeline'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS b ON a.personnelName = b.personnelName
									LEFT OUTER JOIN
									(SELECT
										concat(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(chg.timestamp) AS pipelineDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON opp.id = chg.itemId
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Pipeline'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS c ON a.personnelName = c.personnelName
									LEFT OUTER JOIN
									(SELECT
									    concat(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS workingDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Working'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS d ON a.personnelName = d.personnelName
									LEFT OUTER JOIN
									(SELECT
										concat(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(chg.timestamp) AS workingDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON opp.id = chg.itemId
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Working'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS e ON a.personnelName = e.personnelName
									LEFT OUTER JOIN
									(SELECT
									    concat(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS submittedDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Submitted'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS f ON a.personnelName = f.personnelName
									LEFT OUTER JOIN
									(SELECT
										concat(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(chg.timestamp) AS submittedDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON opp.id = chg.itemId
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Submitted'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS g ON a.personnelName = g.personnelName
									LEFT OUTER JOIN
									(SELECT
									    concat(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS secondStageDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Second Stage'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS h ON a.personnelName = h.personnelName
									LEFT OUTER JOIN
									(SELECT
										concat(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(chg.timestamp) AS secondStageDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON opp.id = chg.itemId
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Second Stage'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS i ON a.personnelName = i.personnelName
									LEFT OUTER JOIN
									(SELECT
									    concat(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS wonDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Won'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS j ON a.personnelName = j.personnelName
									LEFT OUTER JOIN
									(SELECT
										concat(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(chg.timestamp) AS wonDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON opp.id = chg.itemId
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Won'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS k ON a.personnelName = k.personnelName
									LEFT OUTER JOIN
									(SELECT
									    concat(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS lostDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Lost'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS l ON a.personnelName = l.personnelName
									LEFT OUTER JOIN
									(SELECT
										concat(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(chg.timestamp) AS lostDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON opp.id = chg.itemId
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										concat(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Lost'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS m ON a.personnelName = m.personnelName
									LEFT OUTER JOIN
									(SELECT
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(act.createDate) AS callDate
									FROM
									    x2_actions AS act
										LEFT JOIN x2_users AS u ON u.username = act.completedBy
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN($personnelData)
									AND
									    act.associationType IN('accounts','contacts','opportunities')
									AND
									    act.type = 'call'
									AND
										DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS callData ON a.personnelName = callData.personnelName
									LEFT OUTER JOIN
									(SELECT
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(act.createDate) AS emailDate
									FROM
									    x2_actions AS act
										LEFT JOIN x2_users AS u ON u.username = act.completedBy
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN($personnelData)
									AND
									    act.associationType IN('accounts','contacts','opportunities')
									AND
									    act.type = 'emailData'
									AND
										DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS emailData ON a.personnelName = emailData.personnelName
									LEFT OUTER JOIN
									(SELECT
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(act.createDate) AS noteDate
									FROM
									    x2_actions AS act
										LEFT JOIN x2_users AS u ON u.username = act.completedBy
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN($personnelData)
									AND
									    act.associationType IN('accounts','contacts','opportunities')
									AND
									    act.type = 'note'
									AND
										DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS noteData ON a.personnelName = noteData.personnelName
									LEFT OUTER JOIN
									(SELECT
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(act.createDate) AS eventDate
									FROM
									    x2_actions AS act
										LEFT JOIN x2_users AS u ON u.username = act.completedBy
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN($personnelData)
									AND
									    act.associationType IN('accounts','contacts','opportunities')
									AND
									    act.type = 'event'
									AND
										DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS eventData ON a.personnelName = eventData.personnelName
									LEFT OUTER JOIN
									(SELECT
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(act.createDate) AS meaningfulDate
									FROM
									    x2_actions AS act
										LEFT JOIN x2_users AS u ON u.username = act.completedBy
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN($personnelData)
									AND
									    act.associationType IN('accounts','contacts','opportunities')
									AND
									    act.type = 'meaningfulData'
									AND
										DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS meaningfulData ON a.personnelName = meaningfulData.personnelName
									LEFT OUTER JOIN
									(SELECT
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MIN(e.timestamp) AS showUpDate
									FROM
										x2_actions AS act
									    LEFT JOIN x2_events AS e ON e.associationId = act.id
									    LEFT JOIN x2_users AS u ON u.username = e.user
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										act.type = 'event'
									AND
										act.complete = 'Yes'
									AND
										e.type = 'action_complete'
									AND
										DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY personnelName) AS showup ON a.personnelName = showup.personnelName
									WHERE
										(pipelineDateMain != '' OR PipelineDateLog != '' OR workingDateMain != '' OR workingDateLog != '' OR submittedDateMain != '' OR submittedDateLog != '' OR secondStageDateMain != '' OR secondStageDateLog != '' OR wonDateMain != '' OR wonDateLog != '' OR lostDateMain != '' OR lostDateLog != '' OR callDate != '' OR emailDate != '' OR noteDate != '' OR eventDate != '' OR meaningfulDate != '' OR showUpDate != '' OR status = '1')";

									$mainRESULT = mysqli_query($sales_connect, $mainQUERY);
									
									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {
							?>
							<tr class="tbody-tr-style">
								<td>
									<?php echo ucwords($mainROW['personnelName']); ?>
								</td>
							<?php if(isset($_REQUEST['customized-multiple-month'])){ ?>
								<td><?php echo $givenMonth; ?></td>
							<?php } ?>
								<td><?php echo $mainROW['callDate']; ?></td>
								<td><?php echo $mainROW['emailDate']; ?></td>
								<td><?php echo $mainROW['noteDate']; ?></td>
								<td><?php echo $mainROW['eventDate']; ?></td>
								<td><?php echo $mainROW['showUpDate']; ?></td>
								<td><?php echo $mainROW['meaningfulDate']; ?></td>
								<td>
								<?php
									if ($mainROW['pipelineDateMain'] == '' && $mainROW['PipelineDateLog'] != '') {
										echo $mainROW['PipelineDateLog'];
									} elseif ($mainROW['PipelineDateLog'] == '' && $mainROW['pipelineDateMain'] != '') {
										echo $mainROW['pipelineDateMain'];
									} elseif ($mainROW['pipelineDateMain'] != '' && $mainROW['PipelineDateLog'] != '') {
										echo $mainROW['PipelineDateLog'];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW['workingDateMain'] == '' && $mainROW['workingDateLog'] != '') {
										echo $mainROW['workingDateLog'];
									} elseif ($mainROW['workingDateLog'] == '' && $mainROW['workingDateMain'] != '') {
										echo $mainROW['workingDateMain'];
									} elseif ($mainROW['workingDateMain'] != '' && $mainROW['workingDateLog'] != '') {
										echo $mainROW['workingDateLog'];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW['submittedDateMain'] == '' && $mainROW['submittedDateLog'] != '') {
										echo $mainROW['submittedDateLog'];
									} elseif ($mainROW['submittedDateLog'] == '' && $mainROW['submittedDateMain'] != '') {
										echo $mainROW['submittedDateMain'];
									} elseif ($mainROW['submittedDateMain'] != '' && $mainROW['submittedDateLog'] != '') {
										echo $mainROW['submittedDateLog'];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW['secondStageDateMain'] == '' && $mainROW['secondStageDateLog'] != '') {
										echo $mainROW['secondStageDateLog'];
									} elseif ($mainROW['secondStageDateLog'] == '' && $mainROW['secondStageDateMain'] != '') {
										echo $mainROW['secondStageDateMain'];
									} elseif ($mainROW['secondStageDateMain'] != '' && $mainROW['secondStageDateLog'] != '') {
										echo $mainROW['secondStageDateLog'];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW['wonDateMain'] == '' && $mainROW['wonDateLog'] != '') {
										echo $mainROW['wonDateLog'];
									} elseif ($mainROW['wonDateLog'] == '' && $mainROW['wonDateMain'] != '') {
										echo $mainROW['wonDateMain'];
									} elseif ($mainROW['wonDateMain'] != '' && $mainROW['wonDateLog'] != '') {
										echo $mainROW['wonDateLog'];
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW['lostDateMain'] == '' && $mainROW['lostDateLog'] != '') {
										echo $mainROW['lostDateLog'];
									} elseif ($mainROW['lostDateLog'] == '' && $mainROW['lostDateMain'] != '') {
										echo $mainROW['lostDateMain'];
									} elseif ($mainROW['lostDateMain'] != '' && $mainROW['lostDateLog'] != '') {
										echo $mainROW['lostDateLog'];
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
