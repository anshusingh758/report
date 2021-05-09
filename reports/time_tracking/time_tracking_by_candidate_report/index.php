<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
	
    if (isset($user) && isset($userMember)) {
		$reportId = "36";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Time Tracking by Candidate Report</title>

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
				<div class="col-md-12 report-title">Time Tracking by Candidate Report</div>
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

			$totalDays = round((strtotime($mainROW["hrm_placed_date"]) - strtotime($dateRangeValue["end_date"]) - strtotime($dateRangeValue["start_date"])) / (60 * 60 * 24) + 1);

			$filterValue = $dateRangeValue["filter_value"];

			$mainQUERY = mysqli_query($allConn, "SELECT
				e.id AS employee_id,
			    LOWER(CONCAT(TRIM(e.first_name),' ',TRIM(e.last_name))) AS employee_name,
			    DATE_FORMAT(e.custom7, '%m-%d-%Y') AS emp_placed_date,
			    DATE_FORMAT(e.custom7, '%Y-%m-%d') AS hrm_placed_date,
			    DATE_FORMAT(cj.date_created, '%Y-%m-%d') AS job_received_date,
			    MIN(CASE WHEN cjsh.status_to = '400' THEN DATE_FORMAT(cjsh.date, '%Y-%m-%d') END) AS submission_date,
			    MIN(CASE WHEN cjsh.status_to = '500' THEN DATE_FORMAT(cjsh.date, '%Y-%m-%d') END) AS interview_date,
			    MIN(CASE WHEN cjsh.status_to = '600' THEN DATE_FORMAT(cjsh.date, '%Y-%m-%d') END) AS offer_date,
			    MIN(CASE WHEN cjsh.status_to = '800' THEN DATE_FORMAT(cjsh.date, '%Y-%m-%d') END) AS join_date,
			    MIN(DATE_FORMAT(ets.date_start, '%Y-%m-%d')) AS timesheet_date,
			    MIN(DATE_FORMAT(ir.date_from, '%Y-%m-%d')) AS invoice_date,
			    MIN(DATE_FORMAT(ete.date_start, '%Y-%m-%d')) AS gp_date
			FROM
				vtechhrm.employees AS e

			    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
			    
			    LEFT JOIN cats.candidate_joborder AS cj ON cj.candidate_id = si.c_candidate_id AND cj.joborder_id = si.c_joborder_id
			    
			    LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.candidate_id = si.c_candidate_id AND cjsh.joborder_id = si.c_joborder_id

			    LEFT JOIN vtechhrm.employeetimesheets AS ets ON ets.employee = e.id AND ets.status = 'Approved'

			    LEFT JOIN vtech_mappingdb.invoice_report AS ir ON ir.eid = e.id

			    LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = si.h_employee_id
			WHERE
				DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY e.id");

			while ($mainROW = mysqli_fetch_array($mainQUERY)) {
				$finalReportArray[] = array(
					"employee_id" => $mainROW["employee_id"],
					"employee_name" => ucwords($mainROW["employee_name"]),
					"emp_placed_date" => $mainROW["emp_placed_date"],

			        "daterange_type" => $filterValue,

					"job_received_date" => $mainROW["job_received_date"],
					"job_received_days" => $mainROW["job_received_date"] == "" ? "0" : round((strtotime($mainROW["hrm_placed_date"]) - strtotime($mainROW["job_received_date"])) / (60 * 60 * 24)),

					"submission_date" => $mainROW["submission_date"],
					"submission_days" => $mainROW["submission_date"] == "" ? "0" : round((strtotime($mainROW["hrm_placed_date"]) - strtotime($mainROW["submission_date"])) / (60 * 60 * 24)),

					"interview_date" => $mainROW["interview_date"],
					"interview_days" => $mainROW["interview_date"] == "" ? "0" : round((strtotime($mainROW["hrm_placed_date"]) - strtotime($mainROW["interview_date"])) / (60 * 60 * 24)),

					"offer_date" => $mainROW["offer_date"],
					"offer_days" => $mainROW["offer_date"] == "" ? "0" : round((strtotime($mainROW["hrm_placed_date"]) - strtotime($mainROW["offer_date"])) / (60 * 60 * 24)),

					"join_date" => $mainROW["join_date"],
					"join_days" => $mainROW["join_date"] == "" ? "0" : round((strtotime($mainROW["hrm_placed_date"]) - strtotime($mainROW["join_date"])) / (60 * 60 * 24)),

					"timesheet_date" => $mainROW["timesheet_date"],
					"timesheet_days" => $mainROW["timesheet_date"] == "" ? "0" : round((strtotime($mainROW["hrm_placed_date"]) - strtotime($mainROW["timesheet_date"])) / (60 * 60 * 24)),

					"invoice_date" => $mainROW["invoice_date"],
					"invoice_days" => $mainROW["invoice_date"] == "" ? "0" : round((strtotime($mainROW["hrm_placed_date"]) - strtotime($mainROW["invoice_date"])) / (60 * 60 * 24)),

					"gp_date" => $mainROW["gp_date"],
					"gp_days" => $mainROW["gp_date"] == "" ? "0" : round((strtotime($mainROW["hrm_placed_date"]) - strtotime($mainROW["gp_date"])) / (60 * 60 * 24))
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
								<th rowspan="2">Candidate</th>
							<?php if ($dateRangeType == "month") { ?>
								<th rowspan="2">Months</th>
							<?php } elseif ($dateRangeType == "quarter") { ?>
								<th rowspan="2">Quarters</th>
							<?php } ?>
								<th rowspan="2">Placed Date</th>
								<th colspan="8">Total No. of Days for First</th>
							</tr>
							<tr class="thead-tr-style">
								<th>Job Received</th>
								<th>Submission</th>
								<th>Interview</th>
								<th>Offer</th>
								<th>Join</th>
								<th>Timesheet</th>
								<th>Invoice</th>
								<th>GP / Revenue</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach ($finalReportArray as $finalReportKey => $finalReportValue) {
						?>
							<tr class="tbody-tr-style">
								<td><?php echo $finalReportValue["employee_name"]; ?></td>
							<?php if ($dateRangeType != "daterange") { ?>
								<td><?php echo $finalReportValue["daterange_type"]; ?></td>
							<?php } ?>
								<td><?php echo $finalReportValue["emp_placed_date"]; ?></td>
								<td><?php echo $finalReportValue["job_received_days"]; ?></td>
								<td><?php echo $finalReportValue["submission_days"]; ?></td>
								<td><?php echo $finalReportValue["interview_days"]; ?></td>
								<td><?php echo $finalReportValue["offer_days"]; ?></td>
								<td><?php echo $finalReportValue["join_days"]; ?></td>
								<td><?php echo $finalReportValue["timesheet_days"]; ?></td>
								<td><?php echo $finalReportValue["invoice_days"]; ?></td>
								<td><?php echo $finalReportValue["gp_days"]; ?></td>
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
