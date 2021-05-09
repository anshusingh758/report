<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "64";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
			$sessionROW = mysqli_fetch_array($sessionQUERY);
			if ($sessionROW["user_type"] == "Inside Sales (Gov.)") {
				$userGroupList = "1,3";
			} elseif ($sessionROW["user_type"] == "Inside Sales (Gov.)") {
				$userGroupList = "2,3";
			} else {
				$userGroupList = "1,2,3";
			}

			if ($user == "58") {
				$userGroupList = "4,5";
			}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Sales Opportunities Status Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th,
		table.dataTable tbody td {
			padding: 3px 0px;
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
		.inner-td {
			font-size: 10px;
			text-align: right;
			font-style: italic;
			color: #2266AA;
			font-weight: bold;
		}
		.inner-td-span {
			color: green;
			font-style: normal;
		}
	</style>
</head>
<body>

	<?php include_once("../../../popups.php"); ?>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">Sales Opportunities Status Report</div>
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
								$personnelQUERY = mysqli_query($allConn, "SELECT
									CONCAT(u.firstName,' ',u.lastName) AS personnel,
									IF(u.status = 1, 'Active', 'Terminated') AS personnel_status
								FROM
									vtechcrm.x2_users AS u
								    LEFT JOIN vtechcrm.x2_group_to_user AS gu ON u.id = gu.userId
								    LEFT JOIN vtechcrm.x2_groups AS g ON gu.groupId = g.id
								WHERE
									g.id IN ($userGroupList)
								GROUP BY personnel
								ORDER BY personnel ASC");
								if (mysqli_num_rows($personnelQUERY) > 0) {
									while ($personnelROW = mysqli_fetch_array($personnelQUERY)) {
										if (in_array($personnelROW['personnel'], $_REQUEST["personnel-list"])) {
											$isSelected = " selected";
										} else {
											$isSelected = "";
										}
										echo "<option value='".$personnelROW['personnel']."'".$isSelected.">".$personnelROW['personnel']." - ".$personnelROW['personnel_status']."</option>";
									}
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
								<th colspan="3">Opportunity</th>
								<th rowspan="2">Expected<br>Close<br>Date</th>
								<th rowspan="2">Lead<br>Source</th>
								<th rowspan="2">Contract<br>Type</th>
								<th rowspan="2">Client<br>Type</th>
								<th colspan="11">Date of Opportunity in</th>
								<th rowspan="2">Last<br>Updated</th>
							</tr>
							<tr class="thead-tr-style">
								<th>Id</th>
								<th>Name</th>
								<th>Type</th>
								<th>Research</th>
								<th>Pipeline</th>
								<th>Working</th>
								<th>Not<br>Moving<br>Forward</th>
								<th>Late<br>Delivery</th>
								<th>Submitted</th>
								<th>Cancelled</th>
								<th>Hold</th>
								<th>Second<br>Stage</th>
								<th>Won</th>
								<th>Lost</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($fromDate AS $key => $fromDateValue) {
									$startDate = $fromDate[$key];
									$endDate = $toDate[$key];

									$givenMonth = date("m/Y", strtotime($startDate));

									$mainQUERY = "SELECT
										a.oppId,
										a.oppName,
										a.oppType,
										DATE_FORMAT(FROM_UNIXTIME(a.expectedCloseDate), '%m-%d-%Y') AS expectedCloseDate,
										a.leadSource,
										a.oppContractType,
										a.oppClientType,
										DATE_FORMAT(FROM_UNIXTIME(a.lastUpdated), '%m-%d-%Y') AS oppLastUpdated,
										a.personnelName,
										(SELECT CONCAT(firstName,' ',lastName) AS uname FROM x2_users WHERE username = a.assignedTo) AS assignedName,
										(SELECT CONCAT(firstName,' ',lastName) AS uname FROM x2_users WHERE username = a.c_research_by) AS researchName,
										a.username,
										a.assignedTo,
										a.c_research_by,
										a.salesStage,
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
										DATE_FORMAT(FROM_UNIXTIME(n.notMovingForwardDateMain), '%m-%d-%Y') AS notMovingForwardDateMain,
										DATE_FORMAT(FROM_UNIXTIME(o.notMovingForwardDateLog), '%m-%d-%Y') AS notMovingForwardDateLog,
										DATE_FORMAT(FROM_UNIXTIME(p.inResearchDateMain), '%m-%d-%Y') AS inResearchDateMain,
										DATE_FORMAT(FROM_UNIXTIME(q.inResearchDateLog), '%m-%d-%Y') AS inResearchDateLog,
										DATE_FORMAT(FROM_UNIXTIME(r.lateDeliveryDateMain), '%m-%d-%Y') AS lateDeliveryDateMain,
										DATE_FORMAT(FROM_UNIXTIME(s.lateDeliveryDateLog), '%m-%d-%Y') AS lateDeliveryDateLog,
										DATE_FORMAT(FROM_UNIXTIME(t.cancelledDateMain), '%m-%d-%Y') AS cancelledDateMain,
										DATE_FORMAT(FROM_UNIXTIME(u.cancelledDateLog), '%m-%d-%Y') AS cancelledDateLog,
										DATE_FORMAT(FROM_UNIXTIME(v.holdDateMain), '%m-%d-%Y') AS holdDateMain,
										DATE_FORMAT(FROM_UNIXTIME(w.holdDateLog), '%m-%d-%Y') AS holdDateLog
									FROM
									(SELECT
									 	opp.id AS oppId,
									 	opp.name AS oppName,
									 	opp.c_type AS oppType,
									 	opp.expectedCloseDate,
									 	opp.leadSource,
									 	opp.c_contract_type AS oppContractType,
									 	opp.c_ClientType AS oppClientType,
									 	opp.lastUpdated,
									 	u.username,
									 	CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									 	opp.assignedTo,
									 	opp.c_research_by,
									 	opp.salesStage
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									GROUP BY oppId) AS a
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS pipelineDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Pipeline'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS b ON b.oppId = a.oppId AND b.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS pipelineDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
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
									GROUP BY oppId) AS c ON c.oppId = a.oppId AND c.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS workingDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Working'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS d ON d.oppId = a.oppId AND d.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS workingDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
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
									GROUP BY oppId) AS e ON e.oppId = a.oppId AND e.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS submittedDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Submitted'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS f ON f.oppId = a.oppId AND f.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS submittedDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
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
									GROUP BY oppId) AS g ON g.oppId = a.oppId AND g.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS secondStageDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Second Stage'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS h ON h.oppId = a.oppId AND h.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS secondStageDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
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
									GROUP BY oppId) AS i ON i.oppId = a.oppId AND i.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS wonDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Won'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS j ON j.oppId = a.oppId AND j.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS wonDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
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
									GROUP BY oppId) AS k ON k.oppId = a.oppId AND k.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS lostDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Lost'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS l ON l.oppId = a.oppId AND l.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS lostDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Lost'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS m ON m.oppId = a.oppId AND m.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS notMovingForwardDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Not Moving Fwd'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS n ON n.oppId = a.oppId AND n.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS notMovingForwardDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Not Moving Fwd'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS o ON o.oppId = a.oppId AND o.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS inResearchDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'In Research'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS p ON p.oppId = a.oppId AND p.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS inResearchDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'In Research'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS q ON q.oppId = a.oppId AND q.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS lateDeliveryDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Late Delivery'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS r ON r.oppId = a.oppId AND r.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS lateDeliveryDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Late Delivery'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS s ON s.oppId = a.oppId AND s.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS cancelledDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Cancelled'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS t ON t.oppId = a.oppId AND t.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS cancelledDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Cancelled'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS u ON u.oppId = a.oppId AND u.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									    opp.id AS oppId,
									    CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    opp.createDate AS holdDateMain
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
									    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
									 	opp.salesStage = 'Hold'
									AND
									    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS v ON v.oppId = a.oppId AND v.personnelName = a.personnelName
									LEFT JOIN
									(SELECT
									 	opp.id AS oppId,
										CONCAT(u.firstName,' ',u.lastName) AS personnelName,
									    MAX(chg.timestamp) AS holdDateLog
									FROM
									    x2_opportunities AS opp
									    LEFT JOIN x2_changelog AS chg ON chg.itemId = opp.id
									    LEFT JOIN x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
									WHERE
										CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
									AND
										chg.type = 'Opportunity'
									AND
									    chg.fieldName = 'salesStage'
									AND
									 	chg.newValue = 'Hold'
									AND
										DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY oppId) AS w ON w.oppId = a.oppId AND w.personnelName = a.personnelName
									WHERE
										(pipelineDateMain != '' OR PipelineDateLog != '' OR workingDateMain != '' OR workingDateLog != '' OR submittedDateMain != '' OR submittedDateLog != '' OR secondStageDateMain != '' OR secondStageDateLog != '' OR wonDateMain != '' OR wonDateLog != '' OR lostDateMain != '' OR lostDateLog != '' OR notMovingForwardDateMain != '' OR notMovingForwardDateLog != '' OR inResearchDateMain != '' OR inResearchDateLog != '' OR lateDeliveryDateMain != '' OR lateDeliveryDateLog != '' OR cancelledDateMain != '' OR cancelledDateLog != '' OR holdDateMain != '' OR holdDateLog != '')";
									$mainRESULT = mysqli_query($sales_connect, $mainQUERY);
									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {
							?>
							<tr class="tbody-tr-style">
								<td>
									<?php echo ucwords($mainROW["personnelName"]); ?>
									<div class="inner-td">
										<?php
											if ($mainROW["assignedTo"] == $mainROW["username"]) {
												echo " - Assigned To";
											} elseif ($mainROW["c_research_by"] == $mainROW["username"]) {
												echo " - Research By";
											}
										?>
									</div>
								</td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td nowrap><?php echo $givenMonth; ?></td>
							<?php } ?>
								<td nowrap><a href="https://sales.vtechsolution.com/index.php/opportunities/<?php echo $mainROW['oppId']; ?>" target="_blank"><?php echo $mainROW["oppId"]; ?></a></td>
								<td>
									<a href="https://sales.vtechsolution.com/index.php/opportunities/<?php echo $mainROW['oppId']; ?>" target="_blank"><?php echo $mainROW["oppName"]; ?></a>
									<div class="inner-td">
										<?php
											if ($mainROW["assignedTo"] == $mainROW["username"]) {
												if ($mainROW["c_research_by"] != "Anyone" && $mainROW["c_research_by"] != "") {
													echo " - Research By: <br><span class='inner-td-span'>".$mainROW["researchName"]."</span>";
												}
											} elseif ($mainROW["c_research_by"] == $mainROW["username"]) {
												if ($mainROW["assignedTo"] != "Anyone" && $mainROW["assignedTo"] != "") {
													echo " - Assigned To: <br><span class='inner-td-span'>".$mainROW["assignedName"]."</span>";
												}
											}
										?>
									</div>
								</td>
								<td nowrap><?php echo $mainROW["oppType"]; ?></td>
								<td nowrap><?php echo $mainROW["expectedCloseDate"]; ?></td>
								<td nowrap><?php echo $mainROW["leadSource"]; ?></td>
								<td nowrap><?php echo $mainROW["oppContractType"]; ?></td>
								<td nowrap><?php echo $mainROW["oppClientType"]; ?></td>
								<?php if ($mainROW["salesStage"] == "In Research") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["inResearchDateMain"] == "" && $mainROW["inResearchDateLog"] != "") {
										echo $mainROW["inResearchDateLog"];
									} elseif ($mainROW["inResearchDateLog"] == "" && $mainROW["inResearchDateMain"] != "") {
										echo $mainROW["inResearchDateMain"];
									} elseif ($mainROW["inResearchDateMain"] != "" && $mainROW["inResearchDateLog"] != "") {
										echo $mainROW["inResearchDateLog"];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == "Pipeline") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["pipelineDateMain"] == "" && $mainROW["PipelineDateLog"] != "") {
										echo $mainROW["PipelineDateLog"];
									} elseif ($mainROW["PipelineDateLog"] == "" && $mainROW["pipelineDateMain"] != "") {
										echo $mainROW["pipelineDateMain"];
									} elseif ($mainROW["pipelineDateMain"] != "" && $mainROW["PipelineDateLog"] != "") {
										echo $mainROW["PipelineDateLog"];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == "Working") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["workingDateMain"] == "" && $mainROW["workingDateLog"] != "") {
										echo $mainROW["workingDateLog"];
									} elseif ($mainROW["workingDateLog"] == "" && $mainROW["workingDateMain"] != "") {
										echo $mainROW["workingDateMain"];
									} elseif ($mainROW["workingDateMain"] != "" && $mainROW["workingDateLog"] != "") {
										echo $mainROW['workingDateLog'];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == "Not Moving Fwd") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["notMovingForwardDateMain"] == "" && $mainROW["notMovingForwardDateLog"] != "") {
										echo $mainROW["notMovingForwardDateLog"];
									} elseif ($mainROW["notMovingForwardDateLog"] == "" && $mainROW["notMovingForwardDateMain"] != "") {
										echo $mainROW["notMovingForwardDateMain"];
									} elseif ($mainROW["notMovingForwardDateMain"] != "" && $mainROW["notMovingForwardDateLog"] != "") {
										echo $mainROW["notMovingForwardDateLog"];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == "Late Delivery") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["lateDeliveryDateMain"] == "" && $mainROW["lateDeliveryDateLog"] != "") {
										echo $mainROW["lateDeliveryDateLog"];
									} elseif ($mainROW["lateDeliveryDateLog"] == "" && $mainROW["lateDeliveryDateMain"] != "") {
										echo $mainROW["lateDeliveryDateMain"];
									} elseif ($mainROW["lateDeliveryDateMain"] != "" && $mainROW["lateDeliveryDateLog"] != "") {
										echo $mainROW["lateDeliveryDateLog"];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == "Submitted") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["submittedDateMain"] == "" && $mainROW["submittedDateLog"] != "") {
										echo $mainROW["submittedDateLog"];
									} elseif ($mainROW["submittedDateLog"] == "" && $mainROW["submittedDateMain"] != "") {
										echo $mainROW["submittedDateMain"];
									} elseif ($mainROW["submittedDateMain"] != "" && $mainROW['submittedDateLog'] != "") {
										echo $mainROW["submittedDateLog"];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == "Cancelled") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["cancelledDateMain"] == "" && $mainROW["cancelledDateLog"] != "") {
										echo $mainROW["cancelledDateLog"];
									} elseif ($mainROW["cancelledDateLog"] == "" && $mainROW["cancelledDateMain"] != "") {
										echo $mainROW["cancelledDateMain"];
									} elseif ($mainROW["cancelledDateMain"] != "" && $mainROW["cancelledDateLog"] != "") {
										echo $mainROW["cancelledDateLog"];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == "Hold") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["holdDateMain"] == "" && $mainROW["holdDateLog"] != "") {
										echo $mainROW["holdDateLog"];
									} elseif ($mainROW["holdDateLog"] == "" && $mainROW["holdDateMain"] != "") {
										echo $mainROW["holdDateMain"];
									} elseif ($mainROW["holdDateMain"] != "" && $mainROW["holdDateLog"] != "") {
										echo $mainROW["holdDateLog"];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == 'Second Stage') { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["secondStageDateMain"] == "" && $mainROW["secondStageDateLog"] != "") {
										echo $mainROW["secondStageDateLog"];
									} elseif ($mainROW["secondStageDateLog"] == "" && $mainROW["secondStageDateMain"] != "") {
										echo $mainROW["secondStageDateMain"];
									} elseif ($mainROW["secondStageDateMain"] != "" && $mainROW["secondStageDateLog"] != "") {
										echo $mainROW["secondStageDateLog"];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == "Won") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["wonDateMain"] == "" && $mainROW["wonDateLog"] != "") {
										echo $mainROW["wonDateLog"];
									} elseif ($mainROW["wonDateLog"] == "" && $mainROW["wonDateMain"] != "") {
										echo $mainROW["wonDateMain"];
									} elseif ($mainROW["wonDateMain"] != "" && $mainROW["wonDateLog"] != "") {
										echo $mainROW["wonDateLog"];
									}
								?>
								</td>
								<?php if ($mainROW["salesStage"] == "Lost") { ?>
									<td nowrap style="color: #449D44;font-weight: bold;">
								<?php } else { ?>
									<td nowrap>
								<?php } ?>
								<?php
									if ($mainROW["lostDateMain"] == "" && $mainROW["lostDateLog"] != "") {
										echo $mainROW["lostDateLog"];
									} elseif ($mainROW["lostDateLog"] == "" && $mainROW["lostDateMain"] != "") {
										echo $mainROW["lostDateMain"];
									} elseif ($mainROW["lostDateMain"] != "" && $mainROW["lostDateLog"] != "") {
										echo $mainROW["lostDateLog"];
									}
								?>
								</td>
								<td nowrap><?php echo $mainROW["oppLastUpdated"]; ?></td>
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
