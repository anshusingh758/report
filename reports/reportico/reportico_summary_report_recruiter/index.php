<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "28";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
			$sessionROW = mysqli_fetch_array($sessionQUERY);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Summary Report Recruiter</title>

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
		table.scrollable-datatable thead tr:nth-child(1) {
			padding: 4px 0px;
			text-align: center;
			vertical-align: middle;
			font-size: 15px;
			color: #2266AA;
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
		.this-month-div {
			float: left;
		}
		.this-month-div input {
			height:13px;width:13px;cursor: pointer;
		}
		.this-month-div label {
			cursor: pointer;
		}
	</style>
</head>
<body>

	<?php include_once("../../../popups.php"); ?>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">Summary Report Recruiter</div>
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
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th rowspan="4">Recruiter</th>
								<th rowspan="4">CS Manager</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th rowspan="4">Months</th>
							<?php } ?>
								<th colspan="11">Total</th>
							</tr>
							<tr class="thead-tr-style">
								<th colspan="4">Joborder</th>
								<th rowspan="3">Submitted</th>
								<th rowspan="3">Interview</th>
								<th rowspan="3">Interview Decline</th>
								<th rowspan="3">Offer</th>
								<th rowspan="3">Placed</th>
								<th rowspan="3">Extension</th>
								<th rowspan="3">Delivery Falied</th>
							</tr>
							<tr class="thead-tr-style">
								<th colspan="2">Assigned</th>
								<th rowspan="2">Unanswered</th>
								<th rowspan="2" style="border-right: 1px solid #ddd;">On Hold - Internal</th>
							</tr>
							<tr class="thead-tr-style">
								<th>Total</th>
								<th style="border-right: 1px solid #ddd;">Active</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($fromDate as $fromDateKey => $fromDateValue) {
									$startDate = $fromDate[$fromDateKey];
									$endDate = $toDate[$fromDateKey];

									$givenMonth = date("m/Y", strtotime($startDate));

									$mainQUERY = "SELECT
										a1.recruiter_id,
										a1.recruiter_name,
										a1.recruiter_manager_name,
										a2.total_submission,
										a2.total_interview,
										a2.total_interview_declined,
										a2.total_offer,
										a2.total_delivery_failed,
										a3.total_placed,
										a4.total_extension,
										b.total_job,
										b.total_active_job,
										b.total_onhold_internal,
										c.total_answered_job
									FROM
									(SELECT
										u.user_id AS recruiter_id,
										CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
									    u.notes AS recruiter_manager_name
									FROM
									 	cats.user AS u
									GROUP BY recruiter_id) AS a1
									LEFT JOIN
									(SELECT
										u.user_id AS recruiter_id,
									 	COUNT(DISTINCT j.joborder_id) AS total_job,
									 	COUNT(DISTINCT CASE WHEN j.status = 'Active' THEN j.joborder_id END) AS total_active_job,
									 	COUNT(DISTINCT CASE WHEN j.status = 'On Hold - Internal' THEN j.joborder_id END) AS total_onhold_internal
									FROM
									 	cats.user AS u
									 	LEFT JOIN cats.joborder AS j ON j.recruiter = u.user_id
									WHERE
									 	DATE_FORMAT(j.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY recruiter_id) AS b ON b.recruiter_id = a1.recruiter_id
									LEFT JOIN
									(SELECT
										u.user_id AS recruiter_id,
									 	COUNT(DISTINCT j.joborder_id) AS total_answered_job
									FROM
									 	cats.user AS u
									 	LEFT JOIN cats.joborder AS j ON j.recruiter = u.user_id
									 	LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = j.joborder_id
									WHERE
										cjsh.status_to = '400'
									AND
										DATE_FORMAT(j.date_created, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY recruiter_id) AS c ON c.recruiter_id = a1.recruiter_id
									LEFT JOIN
									(SELECT
										u.user_id AS recruiter_id,
									    COUNT(CASE WHEN cjsh.status_to = '400' THEN 1 END) AS total_submission,
									    COUNT(CASE WHEN cjsh.status_to = '500' THEN 1 END) AS total_interview,
									    COUNT(CASE WHEN cjsh.status_to = '560' THEN 1 END) AS total_interview_declined,
									    COUNT(CASE WHEN cjsh.status_to = '600' THEN 1 END) AS total_offer,
									    COUNT(CASE WHEN cjsh.status_to = '900' THEN 1 END) AS total_delivery_failed
									FROM
									 	cats.user AS u
									 	LEFT JOIN cats.candidate_joborder AS cj ON cj.added_by = u.user_id
									 	LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									WHERE
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY recruiter_id) AS a2 ON a2.recruiter_id = a1.recruiter_id
									LEFT JOIN
									(SELECT
										u.user_id AS recruiter_id,
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
										cjsh.candidate_id NOT IN (SELECT
									    cjsh.candidate_id
									FROM
									 	cats.user AS u
									 	LEFT JOIN cats.candidate_joborder AS cj ON cj.added_by = u.user_id
									 	LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									WHERE
										cjsh.status_to = '620'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
									GROUP BY recruiter_id) AS a3 ON a3.recruiter_id = a1.recruiter_id
									LEFT JOIN
									(SELECT
										u.user_id AS recruiter_id,
									    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_extension
									FROM
									 	cats.user AS u
									 	LEFT JOIN cats.candidate_joborder AS cj ON cj.added_by = u.user_id
									 	LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									WHERE
										cjsh.status_to = '620'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									AND
										cjsh.candidate_id NOT IN (SELECT
									    cjsh.candidate_id
									FROM
									 	cats.user AS u
									 	LEFT JOIN cats.candidate_joborder AS cj ON cj.added_by = u.user_id
									 	LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									WHERE
										cjsh.status_to = '800'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
									GROUP BY recruiter_id) AS a4 ON a4.recruiter_id = a1.recruiter_id
									WHERE
										(b.total_job != '' OR b.total_active_job != '' OR c.total_answered_job != '' OR a2.total_submission != '' OR a2.total_interview != '' OR a2.total_interview_declined != '' OR a2.total_offer != '' OR a2.total_delivery_failed != '' OR a3.total_placed != '' OR a4.total_extension != '')";

									if ($sessionROW["user_type"] == "CS Manager") {
										$findOwnerQUERY = mysqli_query($allConn, "SELECT
											u.user_id,
											CONCAT(u.first_name,' ',u.last_name) AS user_full_name
										FROM
											cats.user AS u
										WHERE
											u.user_name = '".$sessionROW['uname']."'");

										$findOwnerROW = mysqli_fetch_array($findOwnerQUERY);
										
										$mainQUERY .= "
										AND
											a1.recruiter_manager_name = '".$findOwnerROW['user_full_name']."'
										GROUP BY recruiter_id
										ORDER BY recruiter_name ASC";
									} else {
										$mainQUERY .= " GROUP BY recruiter_id
										ORDER BY recruiter_name ASC";
									}

									$mainRESULT = mysqli_query($allConn, $mainQUERY);

									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {
							?>
							<tr class="tbody-tr-style">
								<td>
								<?php
									echo ucwords($mainROW["recruiter_name"]);
								?>
								</td>
								<td>
								<?php
									echo ucwords($mainROW["recruiter_manager_name"]);
								?>
								</td>
							<?php
								if (isset($_REQUEST["customized-multiple-month"])) {
							?>
								<td>
								<?php
									echo $givenMonth;
								?>
								</td>
							<?php
								}
							?>
								<td>
								<?php
									if ($mainROW["total_job"] != "") {
										echo $totalJob[] = $mainROW["total_job"];
									} else {
										echo $totalJob[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_active_job"] != "") {
										echo $totalActiveJob[] = $mainROW["total_active_job"];
									} else {
										echo $totalActiveJob[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									echo $totalUnansweredJob[] = ($mainROW["total_job"] - $mainROW["total_answered_job"]);
								?>
								</td>
								<td>
								<?php
									echo $totalOnholdInternal[] = $mainROW["total_onhold_internal"];
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_submission"] != "") {
										echo $totalSubmission[] = $mainROW["total_submission"];
									} else {
										echo $totalSubmission[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_interview"] != "") {
										echo $totalInterview[] = $mainROW["total_interview"];
									} else {
										echo $totalInterview[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_interview_declined"] != "") {
										echo $totalInterviewDeclined[] = $mainROW["total_interview_declined"];
									} else {
										echo $totalInterviewDeclined[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_offer"] != "") {
										echo $totalOffer[] = $mainROW["total_offer"];
									} else {
										echo $totalOffer[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_placed"] != "") {
										echo $totalPlaced[] = $mainROW["total_placed"];
									} else {
										echo $totalPlaced[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_extension"] != "") {
										echo $totalExtension[] = $mainROW["total_extension"];
									} else {
										echo $totalExtension[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["total_delivery_failed"] != "") {
										echo $totalDeliveryFailed[] = $mainROW["total_delivery_failed"];
									} else {
										echo $totalDeliveryFailed[] = "0";
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
							<?php
								if (isset($_REQUEST["customized-multiple-month"])) {
							?>
								<th colspan="3"></th>
							<?php
								} else {
							?>
								<th colspan="2"></th>
							<?php
								}
							?>
							<th>
							<?php
								echo array_sum($totalJob);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalActiveJob);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalUnansweredJob);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalOnholdInternal);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalSubmission);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalInterview);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalInterviewDeclined);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalOffer);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalPlaced);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalExtension);
							?>
							</th>
							<th>
							<?php
								echo array_sum($totalDeliveryFailed);
							?>
							</th>
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
