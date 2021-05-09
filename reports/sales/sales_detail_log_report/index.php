<?php
    header('Content-Type: text/html; charset=ISO-8859-1');
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "65";
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
	<title>Sales Matrix Detail Report</title>

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
			font-size: 13px;
		}
		.tbody-tr-style td {
			color: #333;
			font-size: 13px;
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
				<div class="col-md-12 report-title">Sales Matrix Detail Report</div>
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
						<label>Select Type :</label>
						<select id="type-list" class="customized-selectbox-without-all" name="type-list" required>
							<option value="">Select Option</option>
							<?php
								$typeList = array(
									"Accounts" => "New Accounts",
									"Contacts" => "New Contacts",
									"firsttouch" => "First Touch",
									"followups" => "Follow Ups",
									"note" => "Comment",
									"call" => "Call",
									"emailData" => "Email",
									"event" => "Meetings / Appointments",
									"showups" => "Show Ups",
									"meaningfulData" => "Meaningful",
									"requirements" => "Accounts Got Requirements"
								);
								foreach ($typeList as $typeKey => $typeValue) {
									if ($_REQUEST["type-list"] == $typeKey) {
										$isSelected = " selected";
									} else {
										$isSelected = "";
									}
									echo "<option value='".$typeKey."'".$isSelected.">".$typeValue."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row main-section-row">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Personnel :</label>
						<select id="personnel-list" class="customized-selectbox-without-all" name="personnel-list" required>
							<option value="">Select Option</option>
							<?php
								$personnelQUERY = mysqli_query($allConn, "SELECT
									CONCAT(u.firstName,' ',u.lastName) AS personnel
								FROM
									vtechcrm.x2_users AS u
								    LEFT JOIN vtechcrm.x2_group_to_user AS gu ON gu.userId = u.id
								    LEFT JOIN vtechcrm.x2_groups AS g ON g.id = gu.groupId
								WHERE
									g.id IN ($userGroupList)
								GROUP BY personnel
								ORDER BY personnel ASC");
								if (mysqli_num_rows($personnelQUERY) > 0) {
									while ($personnelROW = mysqli_fetch_array($personnelQUERY)) {
										if ($_REQUEST["personnel-list"] == $personnelROW['personnel']) {
											$isSelected = " selected";
										} else {
											$isSelected = "";
										}
										echo "<option value='".$personnelROW['personnel']."'".$isSelected.">".$personnelROW['personnel']."</option>";
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
		
		$personnelData = $_REQUEST["personnel-list"];
		
		$typeData = $_REQUEST["type-list"];
		
		if ($typeData != "Accounts" && $typeData != "Contacts" && $typeData != "requirements") {
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>Log Date</th>
								<th>Id</th>
								<th>Name</th>
								<th>Menu</th>
							<?php if ($typeData == "firsttouch" || $typeData == "followups" || $typeData == "showups") { ?>
								<th>Log Type</th>
							<?php } ?>
								<th>Log Detail</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($fromDate AS $key => $fromDateValue) {
									$startDate = $fromDate[$key];
									$endDate = $toDate[$key];
									
									if ($typeData == "showups") {
										
										$mainQUERY = "SELECT
											CONCAT(u.firstName,' ',u.lastName) AS personnelName,
										    act.associationId,
										    act.associationType,
										    act.associationName,
										    act.type,
										    DATE_FORMAT(FROM_UNIXTIME(act.completeDate), '%m-%d-%Y') AS createDate,
										    (SELECT text FROM x2_action_text WHERE actionId = act.id) AS textValue
										FROM
										    x2_actions AS act
										    LEFT JOIN x2_users AS u ON u.username = act.completedBy
										WHERE
											CONCAT(u.firstName,' ',u.lastName) = '$personnelData'
										AND
											act.type = 'event'
										AND
											act.complete = 'Yes'
										AND
											DATE_FORMAT(FROM_UNIXTIME(act.completeDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
										GROUP BY act.id";
									
									} else {
									
										$mainQUERY = "SELECT
											MIN(actx.id) AS min_id,
											act.id,
											CONCAT(u.firstName,' ',u.lastName) AS personnelName,
										    act.associationId,
										    act.associationType,
										    act.associationName,
										    act.type,
										    DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%m-%d-%Y') AS createDate,
										    (SELECT text FROM x2_action_text WHERE actionId = act.id) AS textValue
										FROM
											x2_actions AS act
											LEFT JOIN x2_users AS u ON u.username = act.completedBy
											LEFT JOIN x2_actions AS actx ON actx.associationId = act.associationId AND actx.associationType = act.associationType AND actx.type IN ('note','call','emaildata','meaningfulData','event') AND actx.completedBy = act.completedBy
										WHERE
											CONCAT(u.firstName, ' ', u.lastName) = '$personnelData'
										AND
											act.associationType IN ('accounts','contacts','opportunities')";

										if ($typeData == "firsttouch") {
										
											$mainQUERY .= "
											AND
												act.type IN ('note','call','emaildata','meaningfulData','event')
											AND
												DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
											GROUP BY act.id
											HAVING id = min_id";
										
										} elseif ($typeData == "followups") {
										
											$mainQUERY .= "
											AND
												act.type IN ('note','call','emaildata','meaningfulData','event')
											AND
												DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
											GROUP BY act.id
											HAVING id != min_id";
										
										} else {
										
											$mainQUERY .= "
											AND
												act.type = '$typeData'
											AND
												DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
											GROUP BY act.id";
										
										}
									
									}
									
									$mainRESULT = mysqli_query($sales_connect, $mainQUERY);
									
									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {
							?>
							<tr class="tbody-tr-style">
								<td nowrap>
									<?php echo $mainROW["createDate"]; ?>
								</td>
							<?php if ($mainROW["associationType"] == "accounts") { ?>
								<td><a href="https://sales.vtechsolution.com/index.php/accounts/<?php echo $mainROW["associationId"]; ?>" target="_blank"><?php echo $mainROW["associationId"]; ?></a></td>
								<td nowrap><a href="https://sales.vtechsolution.com/index.php/accounts/<?php echo $mainROW["associationId"]; ?>" target="_blank"><?php echo $mainROW["associationName"]; ?></a></td>
							<?php } elseif ($mainROW["associationType"] == "contacts") { ?>
								<td><a href="https://sales.vtechsolution.com/index.php/contacts/id/<?php echo $mainROW["associationId"]; ?>" target="_blank"><?php echo $mainROW["associationId"]; ?></a></td>
								<td nowrap><a href="https://sales.vtechsolution.com/index.php/contacts/id/<?php echo $mainROW["associationId"]; ?>" target="_blank"><?php echo $mainROW["associationName"]; ?></a></td>
							<?php } elseif ($mainROW["associationType"] == "opportunities") { ?>
								<td><a href="https://sales.vtechsolution.com/index.php/opportunities/<?php echo $mainROW["associationId"]; ?>" target="_blank"><?php echo $mainROW["associationId"]; ?></a></td>
								<td nowrap><a href="https://sales.vtechsolution.com/index.php/opportunities/<?php echo $mainROW["associationId"]; ?>" target="_blank"><?php echo $mainROW["associationName"]; ?></a></td>
							<?php } else { ?>
								<td></td>
								<td></td>
							<?php } ?>
								<td><?php echo ucwords($mainROW["associationType"]); ?></td>
							<?php if ($typeData == "firsttouch" || $typeData == "followups" || $typeData == "showups") { ?>
								<td>
								<?php
									if ($mainROW["type"] == "note") {
										echo "Comment";
									} elseif ($mainROW["type"] == "call") {
										echo "Call";
									} elseif ($mainROW["type"] == "emailData") {
										echo "Email";
									} elseif ($mainROW["type"] == "event") {
										echo "Meeting";
									} elseif ($mainROW["type"] == "meaningfulData") {
										echo "Meaningful";
									}
								?>
								</td>
							<?php } ?>
								<td>
									<?php echo $mainROW["textValue"]; ?>
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
		} else {
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-8 col-md-offset-2">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
							<?php if ($typeData != "requirements") { ?>
								<th>Create Date</th>
							<?php } ?>
								<th>Id</th>
								<th>Name</th>
							<?php if ($typeData == "requirements") { ?>
								<th>Total Job</th>
							<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($fromDate AS $key => $fromDateValue) {
									$startDate = $fromDate[$key];
									$endDate = $toDate[$key];

									if ($typeData == "Accounts") {
										$mainQUERY = "SELECT
											act.id,
											act.name,
										    'accounts' AS associationType,
										    DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%m-%d-%Y') AS createDate
										FROM
											x2_events AS e
											LEFT JOIN x2_accounts AS act ON act.id = e.associationId
											LEFT JOIN x2_users AS u ON u.username = e.user OR u.userAlias = e.user
										WHERE
											CONCAT(u.firstName,' ',u.lastName) = '$personnelData'
										AND
											e.type = 'record_create'
										AND
											e.associationType = 'Accounts'
										AND
											DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
										GROUP BY act.id";
									} elseif ($typeData == "Contacts") {
										$mainQUERY = "SELECT
											c.id,
											c.name,
										    'contacts' AS associationType,
										    DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%m-%d-%Y') AS createDate
										FROM
											x2_events AS e
											LEFT JOIN x2_contacts AS c ON c.id = e.associationId
											LEFT JOIN x2_users AS u ON u.username = e.user OR u.userAlias = e.user
										WHERE
											CONCAT(u.firstName,' ',u.lastName) = '$personnelData'
										AND
											e.type = 'record_create'
										AND
											e.associationType = 'Contacts'
										AND
											DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
										GROUP BY c.id";
									} elseif ($typeData == "requirements") {
										$mainQUERY = "SELECT
											copp.id,
										    copp.name,
										    'requirements' AS requirements,
										    COUNT(job.joborder_id) AS total_job
										FROM
											contract.x2_opportunities AS copp
										    LEFT JOIN vtechcrm.x2_users AS u ON u.username = copp.assignedTo OR u.username = copp.c_research_by
										   	LEFT JOIN cats.contract_mapping AS cm ON cm.value_map = copp.c_solicitation_number AND cm.field_name = 'Contract No'
										    LEFT JOIN cats.joborder AS job ON job.company_id = cm.data_item_id
										WHERE
											CONCAT(u.firstName,' ',u.lastName) = '$personnelData'
										AND
											DATE_FORMAT(FROM_UNIXTIME(copp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
										GROUP BY copp.id
										HAVING total_job >= 1";
									}
									
									$mainRESULT = mysqli_query($sales_connect, $mainQUERY);

									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {

							?>
							<tr class="tbody-tr-style">
							<?php if ($typeData != "requirements") { ?>
								<td nowrap><?php echo $mainROW["createDate"]; ?></td>
							<?php } ?>

							<?php if ($mainROW["associationType"] == "accounts") { ?>
								<td><a href="https://sales.vtechsolution.com/index.php/accounts/<?php echo $mainROW["id"]; ?>" target="_blank"><?php echo $mainROW["id"]; ?></a></td>
								<td nowrap><a href="https://sales.vtechsolution.com/index.php/accounts/<?php echo $mainROW["id"]; ?>" target="_blank"><?php echo $mainROW["name"]; ?></a></td>
							<?php } elseif ($mainROW["associationType"] == "contacts") { ?>
								<td><a href="https://sales.vtechsolution.com/index.php/contacts/id/<?php echo $mainROW["id"]; ?>" target="_blank"><?php echo $mainROW["id"]; ?></a></td>
								<td nowrap><a href="https://sales.vtechsolution.com/index.php/contacts/id/<?php echo $mainROW["id"]; ?>" target="_blank"><?php echo $mainROW["name"]; ?></a></td>
							<?php } elseif ($mainROW["associationType"] == "requirements") { ?>
								<td><a href="https://contract.vtechsolution.com/index.php/opportunities/<?php echo $mainROW["id"]; ?>" target="_blank"><?php echo $mainROW["id"]; ?></a></td>
								<td nowrap><a href="https://contract.vtechsolution.com/index.php/opportunities/<?php echo $mainROW["id"]; ?>" target="_blank"><?php echo $mainROW["name"]; ?></a></td>
								<td><?php echo $mainROW["total_job"]; ?></td>
							<?php } ?>
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
            multidate: false,
            orientation: "top",
            autoclose: true
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
