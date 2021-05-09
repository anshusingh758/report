<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "6";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>MD Report</title>

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
				<div class="col-md-12 report-title">MD Report</div>
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
							<option value="All" <?php if(isset($_REQUEST["employee-status"])) { if($_REQUEST["employee-status"] == "All"){ echo "selected"; }}?>>All</option>
							<option value="Active" <?php if(isset($_REQUEST["employee-status"])) { if($_REQUEST["employee-status"] == "Active"){ echo "selected"; }}?>>Active</option>
							<option value="Terminated" <?php if(isset($_REQUEST["employee-status"])) { if($_REQUEST["employee-status"] == "Terminated"){ echo "selected"; }}?>>Terminated</option>
                        </select>
					</div>
					<div class="col-md-3">
						<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
					</div>
				</div>
				<div class="row main-section-row">
					<div class="col-md-3 col-md-offset-3">
						<label>Select Clients :</label>
						<select id="client-list" class="customized-selectbox-with-all" name="client-list[]" multiple required>
							<?php
								$clientList = catsClientList($catsConn);
								foreach ($clientList as $clientKey => $clientValue) {
									echo "<option value='".$clientValue["id"]."'>".$clientValue["name"]."</option>";
								}
							?>
						</select>
					</div>
					<div class="col-md-3">
						<label>Select Employees :</label>
						<select id="employee-list" class="customized-selectbox-with-all" name="employee-list[]" multiple required>
							<?php
								$employeeList = hrmEmployeeList($vtechhrmConn,"All");
								foreach ($employeeList as $employeeKey => $employeeValue) {
									echo "<option value='".$employeeValue["id"]."'>".$employeeValue["name"]."</option>";
								}
							?>
                        </select>
					</div>
				</div>
				<div class="row" style="margin-top: 15px;">
					<div class="col-md-6 col-md-offset-3">
						<div class="this-month-div">
							<input name="this-month-input" type="checkbox" id="this-month-ids" <?php if (isset($_REQUEST['this-month-input'])) { echo 'checked'; } ?>>
							<label for="this-month-ids"> Only Candidate Started This Year <?php echo "(".date("Y").")"; ?></label>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 20px;">
					<div class="col-md-2 col-md-offset-3">
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back</button>
					</div>
					<div class="col-md-2">
						<button type="submit" name="form-submit-button" class="form-control form-submit-button"><i class="fa fa-search"></i> Search</button>
					</div>
					<div class="col-md-2">
						<button type="button" onclick="location.href='<?php echo REPORT_PATH;?>/md/md_report/settings.php'" class="setting-button form-control"><i class="fa fa-cog fa-spin fa-1x"></i> Settings</button>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	if (isset($_REQUEST["form-submit-button"])) {
		$fromDate = $toDate = $employeeStatus = $employeeData = "";

		if ($_REQUEST["customized-from-date"] != "" && $_REQUEST["customized-to-date"] != "") {
			$fromDate = date("Y-m-d", strtotime($_REQUEST["customized-from-date"]));
			$toDate = date("Y-m-d", strtotime($_REQUEST["customized-to-date"]));
		}
		
		$employeeStatus = $_REQUEST["employee-status"];
		$employeeData = "'".implode("', '",$_REQUEST["employee-list"])."'";

		if (isset($_REQUEST["this-month-input"])) {
			$includePeriod = "true";
		} else {
			$includePeriod = "false";
		}

		$thisYearStartDate = date("Y")."-01-01";
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>Id</th>
								<th>EMPID</th>
								<th>Employee</th>
								<th>Joining Date</th>
							<?php if ($employeeStatus == "Terminated") { ?>
								<th>Termination Date</th>
							<?php } ?>
								<th>Work City</th>
								<th>Work State</th>
								<th>Client</th>
								<th>Client Manager</th>
								<th>Recruiter</th>
								<th>Recruiter Manager</th>
								<th>Inside Sales Personnel 1</th>
								<th>Inside Sales Personnel 2</th>
								<th>Research By</th>
								<th>Inside PS Personnel</th>
								<th>Onsite Sales Personnel</th>
								<th>Onsite PS Personnel</th>
								<th>Employment Type</th>
								<th>Benefit</th>
								<th>Benefit List</th>
								<th>Bill Rate</th>
								<th>Pay Rate</th>
								<th>Tax</th>
								<th>Fee (MSP Fee)</th>
								<th>Prime Vendor Fee</th>
								<th>Rate for Candidate</th>
								<th>Bill Rate for Client</th>
								<th>Margin</th>
							<?php if ($fromDate != "" && $toDate != "") { ?>
								<th>Total Hour</th>
								<th>Total GP</th>
								<th>Total Revenue</th>
							<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
								$taxSettingsTableData = taxSettingsTable($allConn);

								if ($fromDate != "" && $toDate != "") {
									$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$fromDate,$toDate);
								}

								$mainQUERY = "SELECT
									e.id AS employee_id,
									e.employee_id AS emp_id,
									CONCAT(e.first_name,' ',e.last_name) AS employee_name,
									e.status AS employee_status,
									DATE_FORMAT(e.custom7, '%m-%d-%Y') AS join_date,
									DATE_FORMAT(e.termination_date, '%m-%d-%Y') AS termination_date,
								    comp.city,
								    comp.state,
									e.custom1 AS benefit,
									e.custom2 AS benefit_list,
									CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
									CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
									es.id AS employment_id,
									es.name AS employment_type,
									comp.company_id,
									comp.name AS company_name,
									clf.mspChrg_pct AS client_msp_charge_percentage,
									clf.primechrg_pct AS client_prime_charge_percentage,
									clf.primeChrg_dlr AS client_prime_charge_dollar,
									clf.mspChrg_dlr AS client_msp_charge_dollar,
									cnf.c_primeCharge_pct AS employee_prime_charge_percentage,
									cnf.c_primeCharge_dlr AS employee_prime_charge_dollar,
									cnf.c_anyCharge_dlr AS employee_any_charge_dollar,
								    CONCAT(cm.first_name,' ',cm.last_name) AS client_manager,
								    CONCAT(r.first_name,' ',r.last_name) AS recruiter,
								    r.notes AS recruiter_manager,
								    is1.value AS inside_sales1,
								    is2.value AS inside_sales2,
								    rb.value AS research_by,
								    ips.value AS inside_post_sales,
								    os.value AS onsite_sales,
								    ops.value AS onsite_post_sales
								FROM
									vtechhrm.employees AS e
									LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
								    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
									LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
									LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
									LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
									LEFT JOIN cats.user AS cm ON cm.user_id = comp.owner
									LEFT JOIN cats.user AS r ON r.user_id = si.c_recruiter_id
									LEFT JOIN cats.extra_field AS is1 ON is1.data_item_id = comp.company_id AND is1.field_name = 'Inside Sales Person1'
									LEFT JOIN cats.extra_field AS is2 ON is2.data_item_id = comp.company_id AND is2.field_name = 'Inside Sales Person2'
									LEFT JOIN cats.extra_field AS rb ON rb.data_item_id = comp.company_id AND rb.field_name = 'Research By'
									LEFT JOIN cats.extra_field AS ips ON ips.data_item_id = comp.company_id AND ips.field_name = 'Inside Post Sales'
									LEFT JOIN cats.extra_field AS os ON os.data_item_id = comp.company_id AND os.field_name = 'Onsite Sales Person'
									LEFT JOIN cats.extra_field AS ops ON ops.data_item_id = comp.company_id AND ops.field_name = 'Onsite Post Sales'";

								if ($fromDate != "" && $toDate != "") {
									if ($includePeriod == "true") {
										$mainQUERY .= " LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
										WHERE
											e.id IN ($employeeData)
										AND
											DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$thisYearStartDate' AND '$toDate'
										AND
											DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
										AND";
									} else {
										$mainQUERY .= " LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
										WHERE
											e.id IN ($employeeData)
										AND
											DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
										AND";
									}
								}

								if ($fromDate == "" && $toDate == "") {
									$mainQUERY .= " WHERE ";
								}

								if ($employeeStatus == "All") {
									$mainQUERY .= " e.id IN ($employeeData)
									GROUP BY employee_id";
								} elseif ($employeeStatus == "Active") {
									$mainQUERY .= " e.id IN ($employeeData)
									AND
										e.status = 'Active'
									GROUP BY employee_id";
								} elseif ($employeeStatus == "Terminated") {
									$mainQUERY .= " e.id IN ($employeeData)
									AND
										e.status IN ('Terminated','Termination Vol','Termination In_Vol')
									GROUP BY employee_id";
								}
								
								$mainRESULT = mysqli_query($allConn, $mainQUERY);
								
								if (mysqli_num_rows($mainRESULT) > 0) {
									while ($mainROW = mysqli_fetch_array($mainRESULT)) {
										$taxRate = $mspFees = $primeCharges = $candidateRate = $grossMargin = $totalHour = $totalGP = $totalRevenue = 0;
										
										$delimiter = array("","[","]",'"');
										
										$benefitList = str_replace($delimiter, $delimiter[0], $mainROW["benefit_list"]);

										//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$mainROW["benefit"],$benefitList,$mainROW["employment_id"],$mainROW["pay_rate"]), 2);

										$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$mainROW["benefit"],$benefitList,$mainROW["employment_id"],$mainROW["pay_rate"]), 2);

										$mspFees = round((($mainROW["client_msp_charge_percentage"] / 100) * $mainROW["bill_rate"]) + $mainROW["client_msp_charge_dollar"], 2);

										$primeCharges = round(((($mainROW["client_prime_charge_percentage"] / 100) * $mainROW["bill_rate"]) + (($mainROW["employee_prime_charge_percentage"] / 100) * $mainROW["bill_rate"]) + $mainROW["employee_prime_charge_dollar"] + $mainROW["employee_any_charge_dollar"] + $mainROW["client_prime_charge_dollar"]), 2);

										$candidateRate = round(($mainROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

										$grossMargin = round(($mainROW["bill_rate"] - $candidateRate), 2);

										if ($fromDate != "" && $toDate != "") {
											//$totalHour = round(employeeWorkingHours($vtechhrmConn,$fromDate,$toDate,$mainROW["employee_id"]), 2);
											
											$totalHour = round(array_sum($employeeTimeEntryTableData[$mainROW["employee_id"]]), 2);

											$totalGP = round(($grossMargin * $totalHour), 2);
											
											$totalRevenue = round(($mainROW["bill_rate"] * $totalHour), 2);
										}

							?>
							<tr class="tbody-tr-style">
								<td><?php echo $mainROW["employee_id"]; ?></td>
								<td><?php echo $mainROW["emp_id"]; ?></td>
								<td><?php echo ucwords($mainROW["employee_name"]); ?></td>
								<td><?php echo $mainROW["join_date"]; ?></td>
							<?php if ($employeeStatus == "Terminated") { ?>
								<td><?php echo $mainROW["termination_date"]; ?></td>
							<?php } ?>
								<td><?php echo $mainROW["city"]; ?></td>
								<td><?php echo $mainROW["state"]; ?></td>
								<td><?php echo $mainROW["company_name"]; ?></td>
								<td><?php echo $mainROW["client_manager"]; ?></td>
								<td><?php echo $mainROW["recruiter"]; ?></td>
								<td><?php echo $mainROW["recruiter_manager"]; ?></td>
								<td><?php echo $mainROW["inside_sales1"]; ?></td>
								<td><?php echo $mainROW["inside_sales2"]; ?></td>
								<td><?php echo $mainROW["research_by"]; ?></td>
								<td><?php echo $mainROW["inside_post_sales"]; ?></td>
								<td><?php echo $mainROW["onsite_sales"]; ?></td>
								<td><?php echo $mainROW["onsite_post_sales"]; ?></td>
								<td><?php echo $mainROW["employment_type"]; ?></td>
								<td><?php echo $mainROW["benefit"]; ?></td>
								<td><?php echo $benefitList; ?></td>
								<td><?php echo $mainROW["bill_rate"]; ?></td>
								<td><?php echo $mainROW["pay_rate"]; ?></td>
								<td><?php echo $taxRate; ?></td>
								<td><?php echo $mspFees; ?></td>
								<td><?php echo $primeCharges; ?></td>
								<td><?php echo $candidateRate; ?></td>
								<td><?php echo $mainROW["bill_rate"]; ?></td>
								<td><?php echo $grossMargin; ?></td>
							<?php if ($fromDate != "" && $toDate != "") { ?>
								<td><?php echo $totalHour; ?></td>
								<td><?php echo $totalGP; ?></td>
								<td><?php echo $totalRevenue; ?></td>
							<?php } ?>
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
