<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
	
    if (isset($user) && isset($userMember)) {
		$reportId = "56";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
			$protocol = "http:";

	        if ($_SERVER["HTTPS"] == 'on') {
	            $protocol = "https:";
	        }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Time Tracking By Personnel Report</title>

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
				<div class="col-md-12 report-title">Time Tracking By Personnel Report</div>
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

			<form class="form-submit-action" action="index.php" method="post">
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
				<div class="row main-section-row">
					<div class="col-md-4 col-md-offset-4">
						<label>Filter By :</label>
						<select class="<?php if (isset($_REQUEST["filter-by"])) { echo "customized-selectbox-without-all"; } else { echo "customized-selectbox-with-all"; } ?>" name="filter-by" multiple required>
							<?php
								$filterBy = array(
									"1" => array(
											"title" => "CS Team",
											"class" => "cs_team",
											"url" => $protocol.REPORT_PATH."/time_tracking/bd_time_tracking_by_personnel_report/cs_team.php"
										),
									"2" => array(
											"title" => "BDC Team",
											"class" => "bdc_team",
											"url" => $protocol.REPORT_PATH."/time_tracking/bd_time_tracking_by_personnel_report/bdc_team.php"
										),
									"3" => array(
											"title" => "BDG Team",
											"class" => "bdg_team",
											"url" => $protocol.REPORT_PATH."/time_tracking/bd_time_tracking_by_personnel_report/bdg_team.php"
										),
									"4" => array(
											"title" => "USBD Team",
											"class" => "usbd_team",
											"url" => $protocol.REPORT_PATH."/time_tracking/bd_time_tracking_by_personnel_report/usbd_team.php"
										),
									"5" => array(
											"title" => "Inside Sales Team",
											"class" => "inside_sales_team",
											"url" => $protocol.REPORT_PATH."/time_tracking/bd_time_tracking_by_personnel_report/inside_sales_team.php"
										),
									"6" => array(
											"title" => "Inside Post Sales Team",
											"class" => "inside_post_sales_team",
											"url" => $protocol.REPORT_PATH."/time_tracking/bd_time_tracking_by_personnel_report/inside_post_sales_team.php"
										),
									"7" => array(
											"title" => "Onsite Sales Team",
											"class" => "onsite_sales_team",
											"url" => $protocol.REPORT_PATH."/time_tracking/bd_time_tracking_by_personnel_report/onsite_sales_team.php"
										),
									"8" => array(
											"title" => "Onsite Post Sales Team",
											"class" => "onsite_post_sales_team",
											"url" => $protocol.REPORT_PATH."/time_tracking/bd_time_tracking_by_personnel_report/onsite_post_sales_team.php"
										),
									"9" => array(
											"title" => "Sourcing Team",
											"class" => "sourcing_team",
											"url" => $protocol.REPORT_PATH."/time_tracking/bd_time_tracking_by_personnel_report/sourcing_team.php"
										)
								);
								foreach ($filterBy AS $filterByKey => $filterByValue) {
									$isSelected = "";
									if (in_array($filterByKey, $_REQUEST["filter-by"])) {
										$isSelected = " selected";
									}
									echo "<option value='".$filterByKey."'".$isSelected.">".$filterByValue["title"]."</option>";
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

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="panel-group filter-report" id="accordion">
					</div>
				</div>
			</div>
		</div>
	</section>

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
	});

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
