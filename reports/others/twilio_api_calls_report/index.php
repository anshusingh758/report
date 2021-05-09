<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
	include('./lib/redis.php');
	
    if (isset($user) && isset($userMember)) {
		$reportId = "78";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Twilio API Calls Report</title>

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
		.view-twilio-call-detail-popup .modal-lg {
			width: calc(100% - 100px);
		}
		.view-twilio-call-detail-popup .modal-header {
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
				<div class="col-md-12 report-title">Twilio API Calls Report</div>
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
				<?php if($userMember == 'Admin') { 
					    $managerName = '' ; 	?>
					<div class="row main-section-row">
						<div class="col-md-2 col-md-offset-4">
									<label>Filter By :</label>
									<select id="filter-by" class="customized-selectbox-without-all" name="filter-by">
										<option value="">Select Option</option>
										<option value="CS Team">CS Team</option>
										<option value="PS Team">PS Team</option>
										<option value="BDG Team">BDG Team</option>
										<option value="BDC Team">BDC Team</option>
									</select>
								</div>
						<div class="col-md-2">
							<label>Select Personnel :</label>
							<select id="personnel-list" class="customized-selectbox-without-all" name="recruiter-list[]" multiple required>
							</select>
						</div>
					</div>
				<?php } else { ?>
					<div class="row main-section-row">
						<div class="col-md-4 col-md-offset-4">
							<label>Select Personnel :</label>
							<select id="personnel-list" class="customized-selectbox-with-all" name="recruiter-list[]" multiple required>
								<?php 
									$userQuery = mysqli_query($allConn,"SELECT 
														first_name,
														last_name
													FROM
														mis_reports.users 
													WHERE
														uid = '$user'");
									$rowuserQuery = mysqli_fetch_array($userQuery);
									$managerName = trim($rowuserQuery['first_name']).' '.trim($rowuserQuery['last_name']);
									$personnelList = catsUserListForTwilioCall($catsConn,$managerName);
									foreach ($personnelList as $personnelKey => $personnelValue) {
										echo "<option value='".$personnelValue['phone_work']."'>".$personnelValue['name']."</option>";
									}
								?>
							</select>
						</div>
					</div>
				<?php } ?>
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
		
		$output = $dateRangeType = $empNumber = "";
		$dateRange = $finalReportArray = $resultTwilioHistoryArray = array();

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

		if (isset($_REQUEST["recruiter-list"])) {
			$empList = $_REQUEST["recruiter-list"];
			$countEmpArray = count($empList);
			if($countEmpArray == 1) {
				$empNumber = $empList[0];
				$employeesNumber = $empList;
			} else {
				$employeesNumber = $empList;
			}
		}
		// print_r($employeesNumber); die;
		// echo $empNumber; die;
		function secondToTimeFormat($seconds) {
  			$t = round($seconds);
  			return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
		}
		$startDate = $dateRange[0]['start_date'];
		$endDate = $dateRange[0]['end_date'];
		$startDates = date('Y-m-d', strtotime("-1 day", strtotime($startDate)));
		$endDates = date('Y-m-d', strtotime("+1 day", strtotime($endDate)));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, 'https://saas.vtechsolution.com/api/v1/twilio/get-call-data?startDate='.$startDates.'&endDate='.$endDates.'&fromNumber='.json_encode($empList));
		$resultTwilioHistoryJson = curl_exec($ch);
		curl_close($ch);
		$resultTwilioHistoryArray = json_decode($resultTwilioHistoryJson,'ARRAY');
		// print_r($resultTwilioHistoryArray); die;
		$dareRanges = "start_date=".$startDate."&end_date=".$endDate;
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>Personnel Name</th>
								<th>Personnel Number</th>
								<th>TO</th>
								<th>Duration</th>
								<th>Status</th>
								<th>Date</th>
								<th>Direction</th>
								<th>Date Range</th>
							</tr>
						</thead>
						<tbody>
							<?php	
							$query = mysqli_query($allConn, "SELECT  
								last_name,
								first_name,
								phone_work 
							FROM 
								cats.user
							WHERE 
								access_level != 0
							AND 
								phone_work!= ''");

 							while ($rowData = mysqli_fetch_array($query)) {

        						$phones = preg_replace('/\D+/', '', $rowData['phone_work']);
 								$phone = '+1'.$phones;
								$data[$phone] = $rowData['first_name'].' '.$rowData['last_name'];
							}
						    
							foreach ($resultTwilioHistoryArray as $twilioHistoryKey => $twilioHistoryValue) {
								
								$name = $data[$twilioHistoryValue["from"]];

								// $seconds[$twilioHistoryValue["from"]][] = $twilioHistoryValue["duration"];

								// $twilioFromNumber[] = $twilioHistoryValue["from"];
								
								// $twilioData[$twilioHistoryValue["from"]][] = array(
								// 	"from" => $twilioHistoryValue["from"],
								// 	"to"   => $twilioHistoryValue["to"],
								// 	"duration" => $twilioHistoryValue["duration"],
								// 	"status" => $twilioHistoryValue["status"],
								// 	"date" => $twilioHistoryValue["date"] 
								// );

								// $uniqueNumber = array_unique($twilioFromNumber);
								// // print_r($uniqueNumber); die;
								// foreach ($uniqueNumber as $uniqueNumberKey => $uniqueNumberValue) {
								// 	# code...
								// if(in_array($uniqueNumberValue, $employeesNumber)) {
								// 	if($data[$uniqueNumberValue] != '') { 
								// 		$name = $data[$uniqueNumberValue]; 
								// 	} else { 
								// 		$name = $uniqueNumberValue; 
								// 	} 
							?>
							<tr class="tbody-tr-style">
								<td><?php echo $name; ?></td>
								<td><?php echo $twilioHistoryValue["from"]; ?></td>
								<td><?php echo $twilioHistoryValue["to"]; ?></td>
								<td><?php echo secondToTimeFormat($twilioHistoryValue["duration"]); ?></td>
								<td><?php echo $twilioHistoryValue["status"]; ?></td>
								<td><?php echo $twilioHistoryValue["date"]; ?></td>					
								<td><?php echo $twilioHistoryValue["direction"]; ?></td>
								<td><?php echo $startDate .' TO '. $endDate ; ?></td>
								<!-- <td><?php //echo $twilioHistoryValue["date"]; ?></td> -->
							</tr>
							<?php }  ?>
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

	
	$(document).on("click", ".twilio-call-detail-popup", function(e){
		e.preventDefault();
		$("#divLoading").addClass("show");
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/twilio-call-detail-popup.php",
			data: "number="+$(this).data("number")+"&"+$(this).data("popup")+"&listArray="+JSON.stringify($(this).data("list")),
			success: function(response) {
				$("#divLoading").removeClass("show");
				$(".view-twilio-call-detail-popup").modal("show");
				$(".twilio-call-detail-section").html("");
				$(".twilio-call-detail-section").html(response);
				$(".scrollable-datatable").DataTable();
			}
		});
	});

	$(document).on("change", "#filter-by", function(e){
		e.preventDefault();
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
		if ($("#filter-by").val() == "CS Team") {
			$.ajax({
				type: "POST",
				url: "<?php echo DIR_PATH; ?>functions/search-extra-field-personnel.php",
				data: "twilioCsTeam="+$("#filter-by").val(),
				success:function(response){
					$("#personnel-list").html(response);
					$("#personnel-list").multiselect("destroy");
					$("#personnel-list").multiselect({
			            numberDisplayed: 1,
			            enableFiltering: true,
			            enableCaseInsensitiveFiltering: true,
			            buttonWidth: "100%",
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
					$("#personnel-list").multiselect("selectAll", false);
					$("#personnel-list").multiselect("updateButtonText");
				}
			});
		} 
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
