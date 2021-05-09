<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
	
    if (isset($user) && isset($userMember)) {
		$reportId = "79";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>AI Matching Detail Report</title>

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
				<div class="col-md-12 report-title">AI Matching Detail Report</div>
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
	function monthRange($dateRange) {
		foreach($dateRange as $dateRangeKey => $dateRangeValue) {
			$dateRangeValue = explode('/', $dateRangeValue);
			$output[$dateRangeKey] = $dateRangeValue[1].$dateRangeValue[0];
		}

		return implode(",", $output);
	}

	function quarterRange($dateRange) {
		foreach($dateRange as $dateRangeKey => $dateRangeValue) {
			$dateRangeValue = explode('/', $dateRangeValue);

			if ($dateRangeValue[0] == "Q1") {
				$output[$dateRangeKey] = "'".$dateRangeValue[1]."01', '".$dateRangeValue[1]."02', '".$dateRangeValue[1]."03'";
			} elseif ($dateRangeValue[0] == "Q2") {
				$output[$dateRangeKey] = "'".$dateRangeValue[1]."04', '".$dateRangeValue[1]."05', '".$dateRangeValue[1]."06'";
			} elseif ($dateRangeValue[0] == "Q3") {
				$output[$dateRangeKey] = "'".$dateRangeValue[1]."07', '".$dateRangeValue[1]."08', '".$dateRangeValue[1]."09'";
			} elseif ($dateRangeValue[0] == "Q4") {
				$output[$dateRangeKey] = "'".$dateRangeValue[1]."10', '".$dateRangeValue[1]."11', '".$dateRangeValue[1]."12'";
			}
		}

		return implode(",", $output);
	}

	function fullDateRange($startDate, $endDate) {
		return array(
			0 => date("Y-m-d", strtotime($startDate)),
			1 => date("Y-m-d", strtotime($endDate))
		);
	}

	if (isset($_REQUEST["form-submit-button"])) {
		if (isset($_REQUEST["customized-multiple-month"])) {
			$monthRange = monthRange(array_unique(explode(",", $_REQUEST["customized-multiple-month"])));

			echo "<script>
				$(document).ready(function(){
					$('.months-button').trigger('click');
				});
			</script>";
		} elseif (isset($_REQUEST["customized-multiple-quarter"])) {
			$quarterRange = quarterRange(array_unique(explode(",", $_REQUEST["customized-multiple-quarter"])));

			echo "<script>
				$(document).ready(function(){
					$('.quarter-button').trigger('click');
				});
			</script>";
		} elseif (isset($_REQUEST["customized-start-date"]) && isset($_REQUEST["customized-end-date"])) {
			$dateRange = fullDateRange($_REQUEST["customized-start-date"], $_REQUEST["customized-end-date"]);

			echo "<script>
				$(document).ready(function(){
					$('.date-range-button').trigger('click');
				});
			</script>";
		}

		$searchQuery = "SELECT
			c.candidate_id,
		    CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
		    CONCAT(c.first_name,' ',c.last_name) AS candidate_name,
		    j.joborder_id,
		    j.title AS job_title,
		    comp.company_id,
		    comp.name AS company_name,
		    IF(cjsh.status_to = '400', 'Submission', IF(cjsh.status_to = '500', 'Interview', IF(cjsh.status_to = '560', 'Interview Declined', IF(cjsh.status_to = '600', 'Offer', IF(cjsh.status_to = '800', 'Placed', IF(cjsh.status_to = '620', 'Extension', IF(cjsh.status_to = '900', 'Delivery Failed', 'Other'))))))) AS status_data";

		if (isset($_REQUEST["customized-multiple-month"])) {
			
			$searchQuery .= ", EXTRACT(YEAR_MONTH FROM cjsh.date) as date_data,
			DATE_FORMAT(cjsh.date, '%m/%Y') as date_name_data";
		
		} elseif (isset($_REQUEST["customized-multiple-quarter"])) {
			
			$searchQuery .= ", CONCAT(YEAR(cjsh.date), 'Q', QUARTER(cjsh.date)) as date_data,
			CONCAT('Q',QUARTER(cjsh.date),'/',YEAR(cjsh.date)) as date_name_data";
		
		}

		$searchQuery .= " FROM
			sovren.ai_matching_candidate_log AS ascl
			LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.candidate_id = ascl.cats_candidate_id
		    LEFT JOIN cats.candidate AS c ON c.candidate_id = cjsh.candidate_id
		    LEFT JOIN cats.joborder AS j ON j.joborder_id = cjsh.joborder_id
		    LEFT JOIN cats.company AS comp ON comp.company_id = j.company_id
		    LEFT JOIN cats.candidate_joborder AS cj ON cj.joborder_id = j.joborder_id AND cj.candidate_id = c.candidate_id
		    LEFT JOIN cats.user AS u ON u.user_id = cj.added_by";

		if (isset($_REQUEST["customized-multiple-month"])) {

			$searchQuery .= " WHERE
				cjsh.status_to IN (400,500,560,600,800,620,900)
			AND
				EXTRACT(YEAR_MONTH FROM ascl.date) IN ($monthRange)
			AND
				EXTRACT(YEAR_MONTH FROM cjsh.date) = EXTRACT(YEAR_MONTH FROM ascl.date)
			GROUP BY cjsh.candidate_joborder_status_history_id, status_data, date_data";

		} elseif (isset($_REQUEST["customized-multiple-quarter"])) {

			$searchQuery .= " WHERE
				cjsh.status_to IN (400,500,560,600,800,620,900)
			AND
				EXTRACT(YEAR_MONTH FROM ascl.date) IN ($quarterRange)
			AND
				EXTRACT(YEAR_MONTH FROM cjsh.date) = EXTRACT(YEAR_MONTH FROM ascl.date)
			GROUP BY cjsh.candidate_joborder_status_history_id, status_data, date_data";

		} elseif (isset($_REQUEST["customized-start-date"]) && isset($_REQUEST["customized-end-date"])) {

			$searchQuery .= " WHERE
				cjsh.status_to IN (400,500,560,600,800,620,900)
			AND
				DATE_FORMAT(ascl.date, '%Y-%m-%d') BETWEEN '$dateRange[0]' AND '$dateRange[1]'
			AND
				EXTRACT(YEAR_MONTH FROM cjsh.date) = EXTRACT(YEAR_MONTH FROM ascl.date)
			GROUP BY cjsh.candidate_joborder_status_history_id, status_data";

		}
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-8 col-md-offset-2">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>Recruiter</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th>Months</th>
							<?php } elseif (isset($_REQUEST["customized-multiple-quarter"])) { ?>
								<th>Quarters</th>
							<?php } ?>
								<th>Candidate</th>
								<th>Job Title</th>
								<th>Company</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
						<?php
							$searchResult = mysqli_query($allConn, $searchQuery);
							if (mysqli_num_rows($searchResult) > 0) {
								while ($searchRow = mysqli_fetch_array($searchResult)) {
						?>
							<tr class="tbody-tr-style">
								<td><?php echo ucwords(trim(strtolower($searchRow["recruiter_name"]))); ?></td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td><?php echo $searchRow["date_name_data"]; ?></td>
							<?php } elseif (isset($_REQUEST["customized-multiple-quarter"])) { ?>
								<td><?php echo $searchRow["date_name_data"]; ?></td>
							<?php } ?>
								<td><a href="https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=<?php echo $searchRow["candidate_id"]; ?>" target="_blank"><?php echo ucwords(trim(strtolower($searchRow["candidate_name"]))); ?></a></td>
								<td><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=<?php echo $searchRow["joborder_id"]; ?>" target="_blank"><?php echo $searchRow["job_title"]; ?></td>
								<td><a href="https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID=<?php echo $searchRow["company_id"]; ?>" target="_blank"><?php echo $searchRow["company_name"]; ?></td>
								<td><?php echo $searchRow["status_data"]; ?></td>
							</tr>
						<?php
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
