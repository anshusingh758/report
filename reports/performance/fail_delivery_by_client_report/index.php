<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "5";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Fail Delivery by Client Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
			padding: 5px 0px;
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
			font-size: 13px;
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
	</style>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">Fail Delivery by Client Report</div>
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
					<div class="col-md-2 col-md-offset-4">
						<label>Filter By :</label>
						<select id="filter-by" class="customized-selectbox-without-all" name="filter-by">
							<option value="Select All">All Clients</option>
							<option value="Manager - Client Service">CS Manager</option>
							<option value="Inside Sales">Inside Sales</option>
							<option value="Inside Post Sales">Inside Post Sales</option>
							<option value="OnSite Sales Person">OnSite Sales</option>
							<option value="OnSite Post Sales">OnSite Post Sales</option>
							<!--<option value="Select All" <?php if (isset($_REQUEST["filter-by"])) { if ($_REQUEST["filter-by"] == "Select All") { echo "selected"; } }?>>All Clients</option>
							<option value="Manager - Client Service" <?php if (isset($_REQUEST["filter-by"])) { if ($_REQUEST["filter-by"] == "Manager - Client Service") { echo "selected"; } }?>>CS Manager</option>
							<option value="Inside Sales" <?php if (isset($_REQUEST["filter-by"])) { if ($_REQUEST["filter-by"] == "Inside Sales") { echo "selected"; } }?>>Inside Sales</option>
							<option value="Inside Post Sales" <?php if (isset($_REQUEST["filter-by"])) { if ($_REQUEST["filter-by"] == "Inside Post Sales") { echo "selected"; } }?>>Inside Post Sales</option>
							<option value="OnSite Sales Person" <?php if (isset($_REQUEST["filter-by"])) { if ($_REQUEST["filter-by"] == "OnSite Sales Person") { echo "selected"; } }?>>OnSite Sales</option>
							<option value="OnSite Post Sales" <?php if (isset($_REQUEST["filter-by"])) { if ($_REQUEST["filter-by"] == "OnSite Post Sales") { echo "selected"; } }?>>OnSite Post Sales</option>-->
						</select>
					</div>
					<div class="col-md-2">
						<label>Select Personnel :</label>
						<select id="personnel-list" class="customized-selectbox-with-all" name="personnel-list">
						</select>
					</div>
				</div>
				<div class="row main-section-row">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Client :</label>
						<select id="client-list" class="customized-selectbox-with-all" name="client-list[]" multiple required>
							<?php
								$clientList = catsClientList($catsConn);
								foreach ($clientList as $clientKey => $clientValue) {
									echo "<option value='".$clientValue['id']."'>".$clientValue['name']."</option>";
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
		$clientData = "'".implode("', '",$_REQUEST['client-list'])."'";
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-8 col-md-offset-2">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>Client</th>
								<th>Client Manager</th>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th>Months</th>
							<?php } ?>
								<th>Total Fail Delivery</th>
								<th data-toggle="tooltip" data-placement="top" title="Based on Joborder Bill Rate">Total Loss (Per-Hour)</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$totalFailedDelivery = $totalLostMargin = array();

								foreach ($fromDate as $fromDateKey => $fromDateValue) {
									$startDate = $fromDate[$fromDateKey];
									$endDate = $toDate[$fromDateKey];

									$givenMonth = date("m/Y", strtotime($startDate));

									$mainQUERY = "SELECT
										a.company_id,
										a.company_name,
										(SELECT CONCAT(u.first_name,' ',u.last_name) AS manager FROM user AS u WHERE u.user_id = a.owner) AS company_manager,
										b.total_failed_delivery,
										c.lost_margin
									FROM
									(SELECT
										comp.company_id,
										comp.name AS company_name,
										comp.owner
									FROM
										company AS comp
									WHERE
										comp.company_id IN ($clientData)
									GROUP BY comp.company_id) AS a
									LEFT OUTER JOIN
									(SELECT
										comp.company_id,
									    COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_failed_delivery
									FROM
										candidate_joborder_status_history AS cjsh
									    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									    JOIN joborder AS job ON cj.joborder_id = job.joborder_id
										JOIN company AS comp ON job.company_id = comp.company_id
									WHERE
										comp.company_id IN ($clientData)
									AND
										cjsh.status_to = '900'
									AND
										date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY comp.company_id) AS b ON b.company_id = a.company_id
									LEFT OUTER JOIN
									(SELECT
										comp.company_id,
									    SUM(CAST(REPLACE(job.salary,'$','') AS DECIMAL (10,2))) AS lost_margin
									FROM
										candidate_joborder_status_history AS cjsh
									    JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
									    JOIN joborder AS job ON cj.joborder_id = job.joborder_id
										JOIN company AS comp ON job.company_id = comp.company_id
									WHERE
										comp.company_id IN ($clientData)
									AND
										cjsh.status_to = '900'
									AND
										date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY comp.company_id) AS c ON c.company_id = a.company_id
									WHERE
										(b.total_failed_delivery != '')";
									$mainRESULT = mysqli_query($catsConn, $mainQUERY);
									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {
							?>
							<tr class="tbody-tr-style">
								<td><?php echo $mainROW["company_name"]; ?></td>
								<td><?php echo $mainROW["company_manager"]; ?></td>
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<td><?php echo $givenMonth; ?></td>
							<?php } ?>
								<td>
								<?php
									if ($mainROW["total_failed_delivery"] != "") {
										echo $totalFailedDelivery[] = $mainROW["total_failed_delivery"];
									} else {
										echo $totalFailedDelivery[] = "0";
									}
								?>
								</td>
								<td>
								<?php
									if ($mainROW["lost_margin"] != "") {
										echo $totalLostMargin[] = $mainROW["lost_margin"];
									} else {
										echo $totalLostMargin[] = "0";
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
							<?php if (isset($_REQUEST["customized-multiple-month"])) { ?>
								<th colspan="3"></th>
							<?php } else { ?>
								<th colspan="2"></th>
							<?php } ?>
								<th><?php echo array_sum($totalFailedDelivery); ?></th>
								<th><?php echo array_sum($totalLostMargin); ?></th>
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
