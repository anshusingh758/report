<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "10";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>EA Detail Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
			padding: 4px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td {
			padding: 2px 1px;
			text-align: center;
			vertical-align: middle;
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
		.logout-button,
		.logout-button:focus {
			outline: none;
			color: #fff;
			background-color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
			padding: 5px 12px;
			float: right;
		}
		.setting-button,
		.setting-button:focus {
			outline: none;
			color: #fff;
			background-color: #673AB7;
			border: 1px solid #673AB7;
			border-radius: 0px;
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
			margin-top: 20px;
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
			font-size: 11px;
		}
		.tbody-tr-style {
			color: #000;
			font-size: 11px;
		}
	</style>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">EA Detail Report</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container">
			<form action="index.php" method="post">
				<div class="row">
					<div class="col-md-2 col-md-offset-3">
						<label>Date From :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							
							<input type="text" name="customized-from-date" class="form-control customized-date-picker" value="<?php if (isset($_REQUEST["customized-from-date"])) { echo $_REQUEST["customized-from-date"]; }?>" placeholder="MM/DD/YYYY" autocomplete="off">
						</div>
					</div>
					<div class="col-md-2">
						<label>Date To :</label>
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
							
							<input type="text" name="customized-to-date" class="form-control customized-date-picker" value="<?php if (isset($_REQUEST["customized-to-date"])) { echo $_REQUEST["customized-to-date"]; }?>" placeholder="MM/DD/YYYY" autocomplete="off">
						</div>
					</div>
					<div class="col-md-2">
						<label>Select Status :</label>
						<select class="customized-selectbox-without-all" name="employee-status" required>
							<!-- <option value="All" <?php if(isset($_REQUEST["employee-status"])) { if($_REQUEST["employee-status"] == "All"){ echo "selected"; }}?>>All</option> -->
							<option value="Active" <?php if(isset($_REQUEST["employee-status"])) { if($_REQUEST["employee-status"] == "Active"){ echo "selected"; }}?>>Active</option>
							<option value="Onboarding" <?php if(isset($_REQUEST["employee-status"])) { if($_REQUEST["employee-status"] == "Onboarding"){ echo "selected"; }}?>>Onboarding</option>
							<option value="Terminated" <?php if(isset($_REQUEST["employee-status"])) { if($_REQUEST["employee-status"] == "Terminated"){ echo "selected"; }}?>>Terminated</option>
                        </select>
					</div>
					<div class="col-md-3">
						<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
					</div>
				</div>
				<!-- <div class="row" style="margin-top: 15px;">
					<div class="col-md-6 col-md-offset-3">
						<div class="this-month-div">
							<input name="this-month-input" type="checkbox" id="this-month-ids" <?php if (isset($_REQUEST['this-month-input'])) { echo 'checked'; } ?>>
							<label for="this-month-ids"> Only Candidate Started This Year <?php echo "(".date("Y").")"; ?></label>
						</div>
					</div>
				</div> -->
				<div class="row" style="margin-top: 20px;">
					<div class="col-md-2 col-md-offset-3">
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back</button>
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
		$fromDate = $toDate = $employeeStatus = $employeeData = "";

		if ($_REQUEST["customized-from-date"] != "" && $_REQUEST["customized-to-date"] != "") {
			$fromDate = date("Y-m-d",strtotime($_REQUEST["customized-from-date"]));
			$toDate = date("Y-m-d",strtotime($_REQUEST["customized-to-date"]));
		}

		// echo $fromDate;
		// echo $toDate;

		$employeeStatus = $_REQUEST["employee-status"];
		// $employeeData = "'".implode("', '",$_REQUEST["employee-list"])."'";

		// if (isset($_REQUEST["this-month-input"])) {
		// 	$includePeriod = "true";
		// } else {
		// 	$includePeriod = "false";
		// }

		// $thisYearStartDate = date("Y")."-01-01";
		// $thisYearDate = strtotime($thisYearStartDate);
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>Consultant Full Name</th>
								<th>Address</th>
								<th>Client Name</th>
								<th>Client Location</th>
								<th>Employment Status</th>
								<th>Home Phone</th>
								<th>Mobile Phone</th>
								<th>Email</th>
								<th>Start Date</th>
								<th>CS Manager</th>
								<th>Recruiter Name</th>
								<th>PS Manager Name</th>
								<th>Bill Rate</th>
								<th>Pay Rate</th>
								<?php if ($employeeStatus == "Terminated") { ?>
								<th>End Date</th>
							<?php } ?>
								<th>Status</th>
								<th>EA Person</th>
							</tr>
						</thead>
						<tbody>
							<?php
								// $taxSettingsTableData = taxSettingsTable($allConn);

								// if ($fromDate != "" && $toDate != "") {
								// 	$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$fromDate,$toDate);
								// }

								$mainQUERY = "SELECT 
										c.id,
										c.name AS fullName,
										c.address,
										c.c_client_name,
										c.c_employment_type,
										c.phone2,
										c.phone,
										c.email,
										c.c_poc,
										c.c_client_location,
										date_format(from_unixtime(c.closedate),'%Y-%m-%d') AS start_date,
										CONCAT(r.first_name,' ',r.last_name) AS recruiter,
										c.c_bill_rate AS bill_rate,
										c.c_pay_rate AS pay_rate,
										date_format(from_unixtime(c.c_termination_end_date),'%Y-%m-%d') AS termination_date,
										c.c_manager AS cs_manager,
										c.c_ps_poc AS ps_manager,
										c.c_consultant_status
									FROM vtechea.x2_contacts AS c
									LEFT JOIN vtechhrm.employees AS e ON e.id = c.c_h_employee_id
									LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = c.c_h_employee_id
									LEFT JOIN cats.user AS r ON r.user_id = si.c_recruiter_id";

								if ($fromDate != "" && $toDate != "") {
										$mainQUERY .= "
										WHERE
											date_format(from_unixtime(c.closedate),'%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND";
									} else {
									$mainQUERY .= " WHERE ";
									}

								if ($employeeStatus == "Active") {
									$mainQUERY .= "  
										c.c_consultant_status = 'Active'";
								} elseif ($employeeStatus == "Onboarding") {
									$mainQUERY .= " 
									c.c_consultant_status = 'Onboarding'";
								} elseif ($employeeStatus == "Terminated") {
									$mainQUERY .= " 
									c.c_consultant_status = 'Terminated'";
								}

								// echo $mainQUERY;
								
								$mainRESULT = mysqli_query($allConn, $mainQUERY);
								
								if (mysqli_num_rows($mainRESULT) > 0) {
									while ($mainROW = mysqli_fetch_array($mainRESULT)) {
								?>
							<tr class="tbody-tr-style">
								<td><?php echo $mainROW["fullName"]; ?></td>
								<td><?php echo $mainROW["address"]; ?></td>
								<td><?php echo $mainROW["c_client_name"]; ?></td>
								<td><?php echo $mainROW["c_client_location"]; ?></td>
								<td><?php echo $mainROW["c_employment_type"]; ?></td>
								<td><?php echo $mainROW["phone"]; ?></td>
								<td><?php echo $mainROW["phone2"]; ?></td>
								<td><?php echo $mainROW["email"]; ?></td>
								<td><?php echo $mainROW["start_date"]; ?></td>
								<td><?php echo $mainROW["cs_manager"]; ?></td>
								<td><?php echo $mainROW["recruiter"]; ?></td>
								<td><?php echo $mainROW["ps_manager"]; ?></td>
								<td><?php echo $mainROW["bill_rate"]; ?></td>
								<td><?php echo $mainROW["pay_rate"]; ?></td>
							<?php if ($employeeStatus == "Terminated") { ?>
								<td><?php echo $mainROW["termination_date"]; ?></td>
							<?php } ?>
								<td><?php echo $mainROW["c_consultant_status"]; ?></td>
								<td><?php echo $mainROW["c_poc"]; ?></td>
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
		$(".loading-image").hide();
		$(".main-section").removeClass("hidden");
		$(".customized-datatable-section").removeClass("hidden");
		
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
		    "aaSorting": [[2,"asc"]],
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

	$(document).on("change", "#client-list", function(e){
		e.preventDefault();
		$.ajax({
			type: "POST",
			url: "<?php echo DIR_PATH; ?>functions/search-employee-by-client.php",
			data: $("#client-list").serialize(),
			success:function(response){
				$("#employee-list").html(response);
				$("#employee-list").multiselect("destroy");
				$("#employee-list").multiselect({
		            nonSelectedText: "Select Option",
		            numberDisplayed: 1,
		            enableFiltering: true,
		            enableCaseInsensitiveFiltering: true,
		            buttonWidth: "100%",
		            includeSelectAllOption: true,
		            maxHeight: 200
		        });
				$("#employee-list").multiselect("selectAll", false);
				$("#employee-list").multiselect("updateButtonText");
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
