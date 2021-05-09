<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "24";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Current Status - Active Requirements</title>

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
			padding: 5px 12px;
			float: left;
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
			margin-top: 50px;
			margin-bottom: 20px;
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
				<div class="col-md-12 report-title">Current Status - Active Requirements</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button"><i class="fa fa-fw fa-arrow-left"></i> Back to home</button>
				</div>
				<div class="col-md-6">
				</div>
				<div class="col-md-4">
					<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
				</div>
			</div>

			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th rowspan="2">Date<br>Received</th>
								<th rowspan="2">Company Job Id</th>
								<th rowspan="2">Job Id</th>
								<th rowspan="2">Job Order</th>
								<th rowspan="2">Client</th>
								<th rowspan="2">Department</th>
								<th rowspan="2">Due Date</th>
								<th rowspan="2">Location</th>
								<th rowspan="2">Recruiter</th>
								<th rowspan="2">Client Manager</th>
								<th rowspan="2">Inside PS Personnel</th>
								<th colspan="7">Total</th>
							</tr>
							<tr class="thead-tr-style">
								<th>Openings</th>
								<th>Remaining<br>Openings</th>
								<th>Submission</th>
								<th>Interview</th>
								<th>Offer</th>
								<th>Placed</th>
								<th>Delivery Failed</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$totalOpenings = $totalRemainingOpenings = $totalSubmission = $totalInterview = $totalOffer = $totalPlaced = $totalDeliveryFailed = array();
								
								$mainQUERY = "SELECT
								    date_format(job.date_created,'%m-%d-%Y') AS date_created,
									job.joborder_id,
									job.client_job_id,
									job.city,
									job.state,
								    job.title AS job_title,
								    (SELECT name FROM company_department WHERE company_department_id = job.company_department_id) AS department,
								    (SELECT value FROM extra_field WHERE field_name='Due Date' AND data_item_id = job.joborder_id) AS due_date,
								    comp.company_id AS client_id,
								    comp.name AS client_name,
								    (SELECT concat(first_name,' ',last_name) AS mannm FROM user WHERE user_id = comp.owner) AS client_manager,
								    (SELECT concat(first_name,' ',last_name) AS rname FROM user WHERE user_id = job.recruiter) AS recruiter,
								    (SELECT value FROM extra_field WHERE field_name='Inside Post Sales' AND  data_item_id = comp.company_id) AS inside_post_sales,
								    job.openings,
								    job.openings_available,
								    (SELECT COUNT(cjsh.candidate_id) FROM candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to='400') AS total_submission,
									(SELECT COUNT(cjsh.candidate_id) FROM candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to='500') AS total_interview,
									(SELECT COUNT(cjsh.candidate_id) FROM candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to='560') AS total_interviewd,
									(SELECT COUNT(cjsh.candidate_id) FROM candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to='600') AS total_offer,
									(SELECT COUNT(cjsh.candidate_id) FROM candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to='800') AS total_placed,
									(SELECT COUNT(cjsh.candidate_id) FROM candidate_joborder_status_history AS cjsh WHERE cjsh.joborder_id = job.joborder_id AND cjsh.status_to='900') AS total_delivery_Failed
								FROM
									company AS comp
									JOIN joborder AS job ON comp.company_id =job.company_id
								WHERE
									job.status = 'Active'
								GROUP BY job.joborder_id";
								$mainRESULT = mysqli_query($catsConn, $mainQUERY);
								if (mysqli_num_rows($mainRESULT) > 0) {
									while ($mainROW = mysqli_fetch_array($mainRESULT)) {
							?>
							<tr class="tbody-tr-style">
								<td><?php echo $mainROW["date_created"]; ?></td>
								<td><?php echo $mainROW["client_job_id"]; ?></td>
								<td><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=<?php echo $mainROW['joborder_id']; ?>" target="_blank"><?php echo $mainROW["joborder_id"]; ?></a></td>
								<td><?php echo $mainROW["job_title"]; ?></td>
								<td style="text-align: center;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID=<?php echo $mainROW['client_id']; ?>" target="_blank"><?php echo $mainROW['client_name']; ?></a></td>
								<td><?php echo $mainROW["department"]; ?></td>
								<td><?php echo $mainROW["due_date"]; ?></td>
								<td><?php echo $mainROW["city"]; if ($mainROW["state"] != "") { echo ", ".$mainROW["state"]; } ?></td>
								<td><?php echo $mainROW["recruiter"]; ?></td>
								<td><?php echo $mainROW["client_manager"]; ?></td>
								<td><?php echo $mainROW["inside_post_sales"]; ?></td>
								<td><?php echo $totalOpenings[] = $mainROW["openings"]; ?></td>
								<td><?php echo $totalRemainingOpenings[] = $mainROW["openings_available"]; ?></td>
								<td><?php echo $totalSubmission[] = $mainROW["total_submission"]; ?></td>
								<td><?php echo $totalInterview[] = $mainROW["total_interview"]; ?></td>
								<td><?php echo $totalOffer[] = $mainROW["total_offer"]; ?></td>
								<td><?php echo $totalPlaced[] = $mainROW["total_placed"]; ?></td>
								<td><?php echo $totalDeliveryFailed[] = $mainROW["total_delivery_Failed"]; ?></td>
							</tr>
							<?php
									}
								}
							?>
						</tbody>
						<tfoot>
							<tr class="tfoot-tr-style">
								<th colspan="11"></th>
								<th><?php echo array_sum($totalOpenings); ?></th>
								<th><?php echo array_sum($totalRemainingOpenings); ?></th>
								<th><?php echo array_sum($totalSubmission); ?></th>
								<th><?php echo array_sum($totalInterview); ?></th>
								<th><?php echo array_sum($totalOffer); ?></th>
								<th><?php echo array_sum($totalPlaced); ?></th>
								<th><?php echo array_sum($totalDeliveryFailed); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</section>

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

	$(document).on("change", "#filter-by", function(e){
		e.preventDefault();
		if ($("#filter-by").val() == "Select All") {
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/search-client-by-personnel.php",
				data: "item="+$("#filter-by").val(),
				success:function(response){
					$("#client-list").html(response);
					$("#client-list").multiselect("destroy");
					$("#client-list").multiselect({
			            nonSelectedText: "Select Option",
			            numberDisplayed: 1,
			            enableFiltering: true,
			            enableCaseInsensitiveFiltering: true,
			            buttonWidth: "100%",
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
					$("#client-list").multiselect("selectAll", false);
					$("#client-list").multiselect("updateButtonText");

					$("#personnel-list").html("");
					$("#personnel-list").multiselect("destroy");
					$("#personnel-list").multiselect({
			            nonSelectedText: "Select Option",
			            numberDisplayed: 1,
			            enableFiltering: true,
			            enableCaseInsensitiveFiltering: true,
			            buttonWidth: "100%",
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
				}
			});
		} else {
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/search-extra-field-personnel.php",
				data: "fieldName="+$("#filter-by").val(),
				success:function(response){
					$("#personnel-list").html(response);
					$("#personnel-list").multiselect("destroy");
					$("#personnel-list").multiselect({
			            nonSelectedText: "Select Option",
			            numberDisplayed: 1,
			            enableFiltering: true,
			            enableCaseInsensitiveFiltering: true,
			            buttonWidth: "100%",
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
				}
			});
		}
	});

	$(document).on("change", "#personnel-list", function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/search-client-by-personnel.php",
			data: "personnel="+$("#personnel-list").val()+"&type="+$("#filter-by").val(),
			success:function(response){
				$("#client-list").html(response);
				$("#client-list").multiselect("destroy");
				$("#client-list").multiselect({
		            nonSelectedText: "Select Option",
		            numberDisplayed: 1,
		            enableFiltering: true,
		            enableCaseInsensitiveFiltering: true,
		            buttonWidth: "100%",
		            includeSelectAllOption: true,
		            maxHeight: 200
		        });
				$("#client-list").multiselect("selectAll", false);
				$("#client-list").multiselect("updateButtonText");
			}
		});
	});

	$(document).on("click", ".margin-detail-popup", function(e){
		e.preventDefault();
		var titleType = $(this).data("titletype");
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/margin-detail-view-popup.php",
			data: "titleType="+titleType+"&"+$(this).data("popup"),
			success: function(response) {
				if (titleType == "Actual") {
					$(".modal-header").css("background-color", "#2266AA");
				}
				$(".modal-title-type").html(titleType);
				$(".view-margin-detail").modal("show");
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
