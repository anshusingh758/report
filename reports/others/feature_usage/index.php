<?php
error_reporting(0);
include_once "../../../config.php";
include_once "../../../security.php";
include_once "../../../functions/reporting-service.php";
include_once "../../../popups.php";

if (isset($user) && isset($userMember)) {
    $reportId = "67";
    $sessionQUERY = findSessionItem($misReportsConn, $user, $reportId);
    if (mysqli_num_rows($sessionQUERY) > 0) {
        $protocal = "http:";

        if ($_SERVER["HTTPS"] == 'on') {
            $protocal = "https:";
        }

        $featureList = array(
            'ai_matching' => array(
                "title" => "ATS | AI Matching",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/ai_matching.php',
            ),
            'advance_search' => array(
                "title" => "ATS | Advance Search",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/advance_search.php',
            ),
            'candidate_screening' => array(
                "title" => "ATS | Candidate Screening",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/candidate_screening.php',
            ),
            'sales_screening' => array(
                "title" => "Sales | Contacts Screening",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/sales_screening.php',
            ),
            /*'key_string_usage' => array(
                "title" => "ATS | Keystring Usage",
                "url" => "",
            ),*/
            'auto_import_jobs' => array(
                "title" => "ATS | Auto Import Jobs",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/auto_import_jobs.php',
            ),
            'candidate_harvesting' => array(
                "title" => "ATS | Candidate Harvesting",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/candidate_harvesting.php',
            ),
            /*'job_boards' => array(
                "title" => "ATS | Job Boards",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/job_boards.php',
            ),*/
            /*'two_way_sms_communication' => array(
                "title" => "ATS | Two Way SMS Communication",
                "url" => "",
            ),*/
            'access_controls' => array(
                "title" => "ATS | Access Control",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/access_controls.php',
			),
			'vtech_eagle_eyes' => array(
                "title" => "ATS | vTech's Eagle Eye",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/vtech_eagle_eyes.php',
			),
			'refer_a_friend' => array(
                "title" => "ATS | Refer-A-Friend",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/refer_a_friend.php',
			),
			'get_rate_usage' => array(
                "title" => "ATS | Get Rate",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/get_rate_usage.php',
			),
			'sales_it_services' => array(
                "title" => "Sales | IT services",
                "url" => $protocal . REPORT_PATH . '/others/feature_usage/sales_it_services.php',
            ),
        );
?>

<!DOCTYPE html>
<html>
<head>
	<title>Feature Usage</title>

	<?php include_once("../../../cdn.php"); ?>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
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
			font-size: 11px;
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
		.wonDivision {
			font-size: 10px;
			color: #333;
			text-align: right;
			color: #2266AA;
			font-weight: bold;
		}
	.loader .ring {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%,-50%);
		width: 150px;
		height: 150px;
		background: transparent;
		border: 3px solid #3c3c3c;
		border-radius: 50%;
		text-align: center;
		line-height: 150px;
		font-family: sans-serif;
		font-size: 20px;
		color: #fff000;
		letter-spacing:4px;
		text-transform: uppercase;
		text-shadow:0 0 10px #fff000;
		box-shadow: 0 0 20px rgba(0,0,0,.5);
		z-index: 1
}
.loader .ring:before {
	content: '';
	position: absolute;
	top: -3px;
	left: -3px;
	width: 100%;
	height: 100%;
	border: 3px solid transparent;
	border-top: 3px solid #fff000;
	border-right: 3px solid #fff000;
	border-radius: 50%;
	animation: animateCircle 2s linear infinite;
}
.loader .ring span {
	display: block;
	position: absolute;
	top: calc(50% - 2px);
	left: 50%;
	width: 50%;
	height: 4px;
	background: transparent;
	transform-origin:left;
	animation: animate 2s linear infinite;
}
.loader .ring span:before {
	content:'';
	position: absolute;
	width: 16px;
	height: 16px;
	border-radius: 50%;
	background-color: #fff000;
	top: -6px;
	right: -8px;
	box-shadow: 0 0 20px #fff000;
}

 @keyframes animateCircle
{
	0%
	{
		transform: rotate(0deg);
	}
	100%
	{
		transform: rotate(360deg);
	}
}
@keyframes animate
{
	0%
	{
		transform: rotate(45deg);
	}
	100%
	{
		transform: rotate(405deg);
	}
}
	.feature-usage .panel-body {
		height: 265px;
		position: relative;
		overflow: auto;
	}
	.feature-usage .panel-body .result {
		overflow: auto;
	}
	.modal-backdrop {
		position: absolute;
	    opacity: 0.4;
		background-color: #b1b9b5bd;
	}
	.error-template {
		text-align: center;
	}
	.table-bordered {
		font-size: 12px;
		margin: 0;
	}
	.table-bordered thead th, .table-bordered td {
		line-height: 11px;
	}
	.table-bordered th, .table-bordered td {
		text-transform: capitalize;
		padding: 5px !important;
	}

	.table-bordered td {
		vertical-align: middle !important;
	}

	.table td.red {
		background-color: #ff0000;
		color: #fff;
	}
	.p-0 {
		padding: 0;
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
    .view-advance-search-detail-popup .modal-header,
    .view-ai-matching-detail-popup .modal-header {
    	background-color: #2266AA;
    	color: #fff;
    	text-align: center;
    }
	table.scrollable-datatable thead th,
	table.scrollable-datatable tfoot th,
	table.advance-search-datatable thead th,
	table.advance-search-datatable tfoot th {
		background-color: #ccc;
		color: #000;
		text-align: center;
		vertical-align: middle;
		font-size: 14px;
	}
	table.scrollable-datatable tbody td,
	table.advance-search-datatable tbody td {
		font-size: 13px;
		text-align: center;
		vertical-align: middle;
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
	</style>
</head>
<body>
	<div id="divLoading"></div>
	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">Feature Usage</div>
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

							<input type="text" name="customized-multiple-month" class="form-control customized-multiple-month active-input-field" value="<?php if (isset($_REQUEST['customized-multiple-month'])) {echo $_REQUEST['customized-multiple-month'];}?>" placeholder="MM/YYYY" autocomplete="off" required>

							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						</div>
					</div>
				</div>
				<div class="row main-section-row multiple-quarter-input hidden">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Quarters:</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>

							<input type="text" name="customized-multiple-quarter" class="form-control customized-multiple-quarter" value="<?php if (isset($_REQUEST['customized-multiple-quarter'])) {echo $_REQUEST['customized-multiple-quarter'];}?>" placeholder="Quarters/YYYY" autocomplete="off" required>

							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
						</div>
					</div>
				</div>
				<div class="row main-section-row date-range-input hidden">
					<div class="col-md-2 col-md-offset-4">
						<label>Date From :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>

							<input type="text" name="customized-from-date" class="form-control customized-date-picker customized-from-date" value="<?php if (isset($_REQUEST['customized-from-date'])) {echo $_REQUEST['customized-from-date'];}?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
						</div>
					</div>
					<div class="col-md-2">
						<label>Date To :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>

							<input type="text" name="customized-to-date" class="form-control customized-date-picker customized-to-date" value="<?php if (isset($_REQUEST['customized-to-date'])) {echo $_REQUEST['customized-to-date'];}?>" placeholder="MM/DD/YYYY" autocomplete="off" disabled>
						</div>
					</div>
				</div>
				<div class="row main-section-submit-row">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back to Home</button>
					</div>
					<div class="col-md-2">
						<button type="button" name="form-submit-button" class="form-control form-submit-button"><i class="fa fa-search"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
	</section>
	<div class="container-fluid feature-usage">
	  <div class="row">
			<?php
				foreach ($featureList as $key => $feature) {
            ?>
					<div class="col-lg-4">
						<div class="panel panel-primary">
	            <div class="panel-heading">
	              <h3 class="panel-title"><?php echo $feature['title']; ?></h3>
	            </div>
	            <div class="panel-body feature-container">
								<div class="result" data-url="<?php echo $feature['url']; ?>">
									<div class="error-template">
		                <h2>No Data</h2>
										<div class="error-details">Please select Months or Quarters or Date Range</div>
		              </div>
								</div>
								<div class="loader hidden">
									<div class="modal-backdrop fade in"></div>
		              <div class="ring">Loading<span></span></div>
								</div>
								<div class="error-template hidden">
	                <h1>Oops!</h1>
	                <h2>Data Not Found</h2>
	                <div class="error-details">Fun Fact: Don't drink while driving - you might spill the beer</div>
	            	</div>
							</div>
	          </div>
					</div>
					<?php
}
        ?>
		</div>
	</div>
	<script>
		$(document).ready(function(){
			$(".loading-image").hide();
			$(".main-section").removeClass("hidden");
			$(".customized-datatable-section").removeClass("hidden");
			$.fn.datepicker.dates['qtrs'] = {
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

					$(".customized-multiple-quarter").datepicker({
							format: "MM/yyyy",
							minViewMode: 1,
							language: "qtrs",
							forceParse: false,
							clearBtn: true,
							multidate: true,
							autoclose: false,
	        }).on("show", function(event) {
						$(".month").each(function(index, element) {
					    if (index > 3) $(element).hide();
					  });
					});





		});

		// Remove error on focus of input element
		$(document).on("focus",".active-input-field",function(){
				if ($(this).parent('.input-group').hasClass('has-error')){
						$(this).parent('.input-group').removeClass('has-error');
				}
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
			$(".customized-date-picker, .customized-multiple-quarter").removeClass("active-input-field");
			$(".customized-multiple-month").addClass("active-input-field");
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
			$(".customized-date-picker, .customized-multiple-month").removeClass("active-input-field");
			$(".customized-multiple-quarter").addClass("active-input-field");
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
			$(".customized-multiple-month, .customized-multiple-quarter").removeClass("active-input-field");
			$(".customized-date-picker").addClass("active-input-field");
		});

		$(document).on("click", ".form-submit-button", function(e){
			let data = null;
			let multipleMonth = $('.customized-multiple-month').val();
			let fromDate = $('.customized-from-date').val();
			let toDate = $('.customized-to-date').val();
			let multipleQuarter = $('.customized-multiple-quarter').val();

			if ($('.multiple-month-input').hasClass('hidden') === true) {
				multipleMonth = '';
			}

			if ($('.multiple-quarter-input').hasClass('hidden') === true) {
				multipleQuarter = '';
			}

			if ($('.date-range-input').hasClass('hidden') === true) {
				fromDate = '';
				toDate = '';
			}

			if (multipleMonth == '' && fromDate == '' && toDate == '' && multipleQuarter == '') {
				$(".active-input-field").parent('.input-group').addClass('has-error'); // To display an error if left it blank
				return false;
			}

			data = {
				multipleMonth: multipleMonth,
				fromDate: fromDate,
				toDate: toDate,
				multipleQuarter: multipleQuarter
			}

			$('.feature-container').each(function(){
				let that = this;
				let resultContainer = $(that).find('.result');
				let url = resultContainer.data('url');
				console.log(url)

				if (typeof(url) !== 'undefined' && url !== '') {
					$(that).find('.loader').removeClass('hidden')
					$.ajax({
						url: url,
						data: data,
						success: function(result){
							resultContainer.html(result);
							$(that).find('.loader').addClass('hidden');
							$(that).find('.error-template').addClass('hidden');
						},
						error: function(error){
							resultContainer.html('');
							$(that).find('.loader').addClass('hidden');
							$(that).find('.error-template').removeClass('hidden');
						}
					});
				} else {
						resultContainer.html('');
						$(that).find('.loader').addClass('hidden');
						$(that).find('.error-template').removeClass('hidden');
				}
			})
		});

		$(document).on("click", ".ai-matching-detail-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/ai-matching-detail-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-ai-matching-detail-popup").modal("show");
					$(".ai-matching-table-section, .ai-matchig-title").html("");
					$(".ai-matching-table-section").html(response);
					$(".ai-matchig-title").html(titleName);
					$(".ai-matching-datatable").DataTable();
				}
			});
		});

		$(document).on("click", ".advance-search-detail-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/advance-search-detail-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-advance-search-detail-popup").modal("show");
					$(".advance-search-table-section, .advance-search-title").html("");
					$(".advance-search-table-section").html(response);
					$(".advance-search-title").html(titleName);
					$(".advance-search-datatable").DataTable();
				}
			});
		});

		$(document).on("click", ".candidate-screening-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/candidate-screening-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-candidate-screening-popup").modal("show");
					$(".candidate-screening-table-section, .candidate-screening-title").html("");
					$(".candidate-screening-table-section").html(response);
					$(".candidate-screening-title").html(titleName);
					$(".candidate-screening-datatable").DataTable();
				}
			});
		});

		$(document).on("click", ".sales-screening-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/sales-screening-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-sales-screening-popup").modal("show");
					$(".sales-screening-table-section, .sales-screening-title").html("");
					$(".sales-screening-table-section").html(response);
					$(".sales-screening-title").html(titleName);
					$(".sales-screening-datatable").DataTable();
				}
			});
		});

		$(document).on("click", ".auto-import-jobs-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/auto-import-jobs-detail-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-auto-import-jobs-popup").modal("show");
					$(".auto-import-table-section, .auto-import-title").html("");
					$(".auto-import-table-section").html(response);
					$(".auto-import-title").html(titleName);
					$(".auto-import-datatable").DataTable();
				}
			});
		});

		$(document).on("click", ".candidate-harvesting-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/candidate-harvesting-detail-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-candidate-harvesting-popup").modal("show");
					$(".candidate-harvesting-table-section, .candidate-harvesting-title").html("");
					$(".candidate-harvesting-table-section").html(response);
					$(".candidate-harvesting-title").html(titleName);
					$(".candidate-harvesting-datatable").DataTable();
				}
			});
		});

		$(document).on("click", ".access-controls-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/access-controls-detail-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-access-controls-popup").modal("show");
					$(".access-controls-table-section, .access-controls-title").html("");
					$(".access-controls-table-section").html(response);
					$(".access-controls-title").html(titleName);
					$(".access-controls-datatable").DataTable();
				}
			});
		});

		$(document).on("click", ".vtech-eagle-eyes-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/vtech-eagle-eyes-detail-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-vtech-eagle-eyes-popup").modal("show");
					$(".vtech-eagle-eyes-table-section, .vtech-eagle-eyes-title").html("");
					$(".vtech-eagle-eyes-table-section").html(response);
					$(".vtech-eagle-eyes-title").html(titleName);
					$(".vtech-eagle-eyes-datatable").DataTable();
				}
			});
		});

		$(document).on("click", ".get-rate-usage-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/get-rate-usage-detail-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-get-rate-usage-popup").modal("show");
					$(".get-rate-usage-table-section, .get-rate-usage-title").html("");
					$(".get-rate-usage-table-section").html(response);
					$(".get-rate-usage-title").html(titleName);
					$(".get-rate-usage-datatable").DataTable();
				}
			});
		});

		$(document).on("click", ".sales-it-services-popup", function(e){
			e.preventDefault();
			
			$("#divLoading").addClass("show");
			
			let titleName = $(this).data("titlename");
			
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/sales-it-services-detail-popup.php",
				data: "status="+$(this).data("status")+"&date="+$(this).data("popup"),
				success: function(response) {
					$("#divLoading").removeClass("show");
					$(".view-sales-it-services-popup").modal("show");
					$(".sales-it-services-table-section, .sales-it-services-title").html("");
					$(".sales-it-services-table-section").html(response);
					$(".sales-it-services-title").html(titleName);
					$(".sales-it-services-datatable").DataTable();
				}
			});
		});
		
	</script>
</body>
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
