<?php
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");

	$responseArray = array();
	$responseType = isset($_REQUEST['response_type']) && $_REQUEST['response_type'] == 1 ? 1 : 0;

    if(isset($_SESSION['user'])){
		error_reporting(0);
		include_once('../../../config.php');

    	$childUser = $_SESSION['userMember'];
		$reportID = '51';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
		$sessionResult=mysqli_query($misReportsConn, $sessionQuery);
		if(mysqli_num_rows($sessionResult) > 0){
			if ($responseType == 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>USBD Incentive Report</title>

	<?php
		include_once('../../../cdn.php');
	?>
	<style>
		table.dataTable thead th,
		table.dataTable tfoot td{
			padding: 5px 1px;
		}
		table.dataTable tbody td{
			padding: 2px 1px;
		}
		.darkButton,
		.darkButton:focus{
			background-color: #2266AA;
			color: #fff;
			outline: none;
			border-color: #2266AA;
			border-radius: 0px;
		}
		.smoothButton,
		.smoothButton:focus{
			background-color: #fff;
			color: #2266AA;
			font-weight: bold;
			outline: none;
			border-color: #2266AA;
			border-radius: 0px;
		}
		.textWrap {
			float: left;
			color: red;
		}
		.textWrapped {
			float: right;
			color: #2266AA;
			font-weight: bold;
		}
		.adjustmentAmount {
			width: 70%;padding: 2px 5px;
		}
		.adjustmentMethod {
			padding: 5px 10px;cursor: pointer;
		}
		.finalAmount {
			background-color: #fff;color: #000;width: 70%;padding: 2px 5px;border: none;text-align: center;
		}
		.reportTitle {
			text-align: center;background-color: #ccc;color: #2266AA;font-size: 18px;font-weight: bold;padding: 3px;
		}
		.tfootTh {
			background-color: #bbb;text-align: right;vertical-align: middle;
		}
		.lockButton {
			border-radius: 0px;background-color: #2266AA;
		}
	</style>
	
	<script>
		$(document).ready(function(){
			$(".LoadingImage").hide();
			$('.MainSection').removeClass("hidden");
			$('.customizedDataTableSection').removeClass("hidden");

	        //customizedMultipleMonth
	        $(".customizedMultipleMonth").datepicker({
	            format: "mm/yyyy",
	            startView: 1,
	            minViewMode: 1,
	            maxViewMode: 2,
	            clearBtn: true,
	            multidate: false,
	            orientation: "top",
	            autoclose: true
	        });

	        //customizedDatePicker
	        $(".customizedDatePicker").datepicker({
	            todayHighlight: true,
	            clearBtn: true,
	            orientation: "top",
	            autoclose: true
	        });

	        //customizedSelectBoxWithAll
	        $('.customizedSelectBoxWithAll').multiselect({
	            nonSelectedText: 'Select Option',
	            numberDisplayed: 1,
	            enableFiltering:true,
	            enableCaseInsensitiveFiltering:true,
	            buttonWidth:'100%',
	            includeSelectAllOption: true,
	            maxHeight: 200
	        });

			var customizedDataTable = $('#customizedDataTable').DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			    dom: 'Bfrtip',
			    "aaSorting": [[0,'asc']],
		        buttons:[
		            'excel','pageLength'
		        ],
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","250")
				}
			});
			customizedDataTable.button(0).nodes().css('background', '#2266AA');
			customizedDataTable.button(0).nodes().css('border', '#2266AA');
			customizedDataTable.button(0).nodes().css('color', '#fff');
			customizedDataTable.button(0).nodes().html('Download Report');
			customizedDataTable.button(1).nodes().css('background', '#449D44');
			customizedDataTable.button(1).nodes().css('border', '#449D44');
			customizedDataTable.button(1).nodes().css('color', '#fff');

			var customizedDataTableOpp = $('#customizedDataTableOpp').DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			    dom: 'Bfrtip',
			    "aaSorting": [[0,'asc']],
		        buttons:[
		            'excel','pageLength'
		        ],
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","250")
				}
			});
			customizedDataTableOpp.button(0).nodes().css('background', '#2266AA');
			customizedDataTableOpp.button(0).nodes().css('border', '#2266AA');
			customizedDataTableOpp.button(0).nodes().css('color', '#fff');
			customizedDataTableOpp.button(0).nodes().html('Download Report');
			customizedDataTableOpp.button(1).nodes().css('background', '#449D44');
			customizedDataTableOpp.button(1).nodes().css('border', '#449D44');
			customizedDataTableOpp.button(1).nodes().css('color', '#fff');


			var customizedDataTableTeam = $('#customizedDataTableTeam').DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			    dom: 'Bfrtip',
			    "aaSorting": [[0,'asc']],
		        buttons:[
		            'excel','pageLength'
		        ],
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","250")
				}
			});
			customizedDataTableTeam.button(0).nodes().css('background', '#2266AA');
			customizedDataTableTeam.button(0).nodes().css('border', '#2266AA');
			customizedDataTableTeam.button(0).nodes().css('color', '#fff');
			customizedDataTableTeam.button(0).nodes().html('Download Report');
			customizedDataTableTeam.button(1).nodes().css('background', '#449D44');
			customizedDataTableTeam.button(1).nodes().css('border', '#449D44');
			customizedDataTableTeam.button(1).nodes().css('color', '#fff');

			$('#monthsButton').click(function(e){
				e.preventDefault();
				$('.dateRangeForm').addClass("hidden");
				$('.multipleMonthForm').removeClass("hidden");
				$('#monthsButton').addClass("darkButton");
				$('#dateRangeButton').addClass("smoothButton");
				$('#monthsButton').removeClass("smoothButton");
				$('#dateRangeButton').removeClass("darkButton");
			});

			$('#dateRangeButton').click(function(e){
				e.preventDefault();
				$('.multipleMonthForm').addClass("hidden");
				$('.dateRangeForm').removeClass("hidden");
				$('#dateRangeButton').addClass("darkButton");
				$('#monthsButton').addClass("smoothButton");
				$('#dateRangeButton').removeClass("smoothButton");
				$('#monthsButton').removeClass("darkButton");
			});
		});
		function goBack(){
			window.history.back();
		}
	</script>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center" style="font-size: 27px;background-color: #aaa;color: #111;padding: 10px;">USBD Incentive Report</div>
				<div class="col-md-12 LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section class="MainSection hidden" style="margin-top: 30px;margin-bottom: 100px;">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="smoothButton form-control"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Home</button>
				</div>
				<div class="col-md-2 col-md-offset-2">
					<input type="button" class="form-control darkButton" id="monthsButton" value="Months">
				</div>
				<div class="col-md-2">
					<input type="button" class="form-control smoothButton" id="dateRangeButton" value="Date Range">
				</div>
				<div class="col-md-4">
					<a href="../../../logout.php" class="btn darkButton pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
				</div>
			</div>

			<!--POST customizedMultipleMonth Section-->
			<form class="multipleMonthForm" action="index.php" method="get">
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Months:</label>
						<div class="input-group">
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control customizedMultipleMonth" name="customizedMultipleMonth" placeholder="MM/YYYY" type="text" value="<?php if(isset($_REQUEST['customizedMultipleMonth'])){echo $_REQUEST['customizedMultipleMonth'];}?>"  autocomplete="off" required>
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Personnel :</label>
						<select class="customizedSelectBoxWithAll" name="salesPersonnel" required>
							<option value="">Select Option</option>
						<?php
							$groupQUERY = mysqli_query($sales_connect, "SELECT
								CONCAT(u.firstName,' ',u.lastName) AS personnel
							FROM
								x2_users AS u
							    JOIN x2_group_to_user AS gu ON u.id = gu.userId
							    JOIN x2_groups AS g ON gu.groupId = g.id
							WHERE
								g.id = '3'
							ORDER BY personnel ASC");
							while ($groupROW = mysqli_fetch_array($groupQUERY)) {
								if ($_REQUEST['salesPersonnel'] == $groupROW['personnel']) {
                                    $isSelected = ' selected';
                                }else{
                                    $isSelected = '';
                                }
								echo "<option value='".$groupROW['personnel']."'".$isSelected.">".$groupROW['personnel']."</option>";
							}
						?>
						</select>
					</div>
				</div>
				<div class="row" style="margin-top: 35px;">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="goBack()" class="darkButton form-control"><i class="fa fa-backward"></i> Go Back</button>
					</div>
					<div class="col-md-2">
						<button type="submit" class="form-control" name="formSubmitButton" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
					</div>
				</div>
			</form>

			<!--POST dateRange Section-->
			<form class="dateRangeForm hidden" action="index.php" method="get">
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-2 col-md-offset-4">
						<label>Date From :</label>
						<div class="input-group">
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control customizedDatePicker" name="customizedFromDate" placeholder="MM/DD/YYYY" type="text" value="<?php if(isset($_REQUEST['customizedFromDate'])) { echo $_REQUEST['customizedFromDate']; }?>"  autocomplete="off" required>
						</div>
					</div>
					<div class="col-md-2">
						<label>Date To :</label>
						<div class="input-group">
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control customizedDatePicker" name="customizedToDate" placeholder="MM/DD/YYYY" type="text" value="<?php if(isset($_REQUEST['customizedToDate'])) { echo $_REQUEST['customizedToDate']; }?>"  autocomplete="off" required>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Personnel :</label>
						<select class="customizedSelectBoxWithAll" name="salesPersonnel" required>
							<option value="">Select Option</option>
						<?php
							$groupQUERY = mysqli_query($sales_connect, "SELECT
								CONCAT(u.firstName,' ',u.lastName) AS personnel
							FROM
								x2_users AS u
							    JOIN x2_group_to_user AS gu ON u.id = gu.userId
							    JOIN x2_groups AS g ON gu.groupId = g.id
							WHERE
								g.id = '3'
							ORDER BY personnel ASC");
							while ($groupROW = mysqli_fetch_array($groupQUERY)) {
								if ($_REQUEST['salesPersonnel'] == $groupROW['personnel']) {
                                    $isSelected = ' selected';
                                }else{
                                    $isSelected = '';
                                }
								echo "<option value='".$groupROW['personnel']."'".$isSelected.">".$groupROW['personnel']."</option>";
							}
						?>
						</select>
					</div>
				</div>
				<div class="row" style="margin-top: 35px;">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="goBack()" class="darkButton form-control"><i class="fa fa-backward"></i> Go Back</button>
					</div>
					<div class="col-md-2">
						<button type="submit" class="form-control" name="formSubmitButton" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	}
	if(isset($_REQUEST['formSubmitButton'])){
		$salesPersonnel = $_REQUEST['salesPersonnel'];
		$fromDate = $toDate = $lockedReport = array();
		if(isset($_REQUEST['customizedMultipleMonth'])){
			$monthsData = str_replace("-", "/", $_REQUEST['customizedMultipleMonth']);
			$monthsDataArray = explode("/", $monthsData);
			$month = $monthsDataArray[0];
			$year = $monthsDataArray[1];

			$startDate = date('Y-m-01', strtotime($year."-".$month));
			$endDate = date('Y-m-t', strtotime($year."-".$month));
			
			$startDateSTRTO = strtotime($startDate);
			$endDateSTRTO = strtotime($endDate);

		}else{
			$startDate = date('Y-m-d', strtotime($_REQUEST['customizedFromDate']));
			$endDate = date('Y-m-d', strtotime($_REQUEST['customizedToDate']));

			$startDateSTRTO = strtotime($startDate);
			$endDateSTRTO = strtotime($endDate);
		
			if ($responseType == 0) {
?>
			<script>
				$('.multipleMonthForm').addClass("hidden");
				$('.dateRangeForm').removeClass("hidden");
				$('#dateRangeButton').addClass("darkButton");
				$('#monthsButton').addClass("smoothButton");
				$('#dateRangeButton').removeClass("smoothButton");
				$('#monthsButton').removeClass("darkButton");
			</script>
<?php
			}
		}
	
		$detailLink = LOCAL_REPORT_PATH."/incentive/usbd_incentive_report/index.php?customizedFromDate=".urlencode($startDate)."&customizedToDate=".urlencode($endDate)."&salesPersonnel=".urlencode($salesPersonnel)."&formSubmitButton=&response_type=1";

		$findLockedReportQUERY = mysqli_query($misReportsConn, "SELECT * FROM usbd_incentive_data WHERE person_name = '$salesPersonnel' AND start_date = '$startDate' AND end_date ='$endDate'");
		$isLocked = "false";
		if (mysqli_num_rows($findLockedReportQUERY) > 0) {
			$isLocked = "true";
			while ($findLockedReportROW = mysqli_fetch_array($findLockedReportQUERY)) {
				$lockedReport[] = $findLockedReportROW;
			}
		}
		
		$findRoleQUERY = mysqli_query($sales_connect, "SELECT
			GROUP_CONCAT(role.id) AS roleList
		FROM
			x2_users AS u
		    JOIN x2_role_to_user AS urole ON u.id = urole.userId
		    JOIN x2_roles AS role ON urole.roleId = role.id
		WHERE
			CONCAT(u.firstName,' ',u.lastName) = '$salesPersonnel'");
		
		$findRoleROW = mysqli_fetch_array($findRoleQUERY);
		
		$userRoleList = explode(",", $findRoleROW['roleList']);
		
		$personnelType = "Personnel";
		
		if (array_search("9", $userRoleList)) {
			$personnelType = "Manager";
			$managerListQUERY = mysqli_query($sales_connect, "SELECT
				CONCAT(u.firstName,' ',u.lastName) AS personnel
			FROM
				x2_users AS u
			    JOIN x2_group_to_user AS gu ON u.id = gu.userId
			    JOIN x2_groups AS g ON gu.groupId = g.id
			    JOIN x2_role_to_user AS urole ON u.id = urole.userId
			WHERE
				g.id = '3'
			AND
				urole.roleId = '9'
			GROUP BY personnel");
			while ($managerListROW = mysqli_fetch_array($managerListQUERY)) {
				$managerList[] = $managerListROW['personnel'];
			}
			$teamListQUERY = mysqli_query($sales_connect, "SELECT
				CONCAT(u.firstName,' ',u.lastName) AS personnel
			FROM
				x2_users AS u
			    JOIN x2_group_to_user AS gu ON u.id = gu.userId
			    JOIN x2_groups AS g ON gu.groupId = g.id
			WHERE
				g.id = '3'
			GROUP BY personnel");
			while ($teamListROW = mysqli_fetch_array($teamListQUERY)) {
				$teamList[] = $teamListROW['personnel'];
			}
			$uniqueTeamList = array_diff($teamList, $managerList);
		}

		if ($responseType == 0) {
?>
	<section class="customizedDataTableSection hidden">
		<div class="container-fluid">
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-md-4 col-md-offset-4 reportTitle"><i class="fa fa-tachometer"></i> Individual Incentive</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-10 col-md-offset-1">
					<table id="customizedDataTable" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #ccc;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;">Candidate</th>
								<th style="text-align: center;vertical-align: middle;">Client</th>
								<th style="text-align: center;vertical-align: middle;">Status</th>
								<th style="text-align: center;vertical-align: middle;">Join Date</th>
								<th style="text-align: center;vertical-align: middle;">Termination Date</th>
								<th style="text-align: center;vertical-align: middle;">Margin</th>
								<th style="text-align: center;vertical-align: middle;">Total GP</th>
							</tr>
						</thead>
						<tbody>
							<?php
								}
								if ($isLocked == "false") {
									$mainQUERY = "SELECT
										ef.value AS personnelName,
										e.id AS employeeId,
										CONCAT(e.first_name,' ',e.last_name) AS employeeName,
										e.status AS employeeStatus,
										DATE_FORMAT(e.custom7, '%m-%d-%Y') AS joiningDate,
										DATE_FORMAT(e.termination_date, '%m-%d-%Y') AS terminationDate,
										e.custom1 AS benefit,
										e.custom2 AS benefitList,
										CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS billRate,
										CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS payRate,
										es.id AS employmentId,
										es.name AS employmentType,
										comp.company_id AS companyId,
										comp.name AS companyName,
										TIMESTAMPDIFF(YEAR, comp.date_created, CURDATE()) AS companyAge,
										(SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'USBD' AND ic.comment = 'Client Age') AS givenCompanyAge
									FROM
										employees AS e
										JOIN employmentstatus AS es ON e.employment_status = es.id
									    JOIN vtech_mappingdb.system_integration AS si ON e.id = si.h_employee_id
										JOIN cats.company AS comp ON si.c_company_id = comp.company_id
									    JOIN cats.extra_field AS ef ON ef.data_item_id = comp.company_id
									WHERE
										ef.field_name IN ('OnSite Sales Person','OnSite Post Sales')
									AND
										ef.value = '$salesPersonnel'
									AND
										date_format(e.custom7, '%Y-%m-%d') > (SELECT date_format(he.confirmation_date, '%Y-%m-%d') AS confirmationDate FROM employees AS he WHERE concat(REPLACE(TRIM(he.first_name), 'ben', ''),' ',TRIM(he.last_name)) = '$salesPersonnel')
									AND
										date_format(e.custom7, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
									GROUP BY employeeId";
									$taxRate = $mspFees = $primeCharges = $candidateRate = $grossMargin = $totalHour = $totalGP = $individualCandidate = $individualGP = "";
									$totalEmployee = $finalGP = array();
									$mainRESULT = mysqli_query($vtechhrmConn, $mainQUERY);
									if (mysqli_num_rows($mainRESULT) > 0) {
										while ($mainROW = mysqli_fetch_array($mainRESULT)) {
											$employeeId = $mainROW['employeeId'];
											$companyId = $mainROW['companyId'];
											$billRate = $mainROW['billRate'];
											$payRate = $mainROW['payRate'];
											$employmentId = $mainROW['employmentId'];
											$benefit = $mainROW['benefit'];

											$delimiter = array("","[","]",'"');
											$benefitList = str_replace($delimiter, $delimiter[0], $mainROW['benefitList']);

											$tax = array();

											$benefitLists = explode(",", $benefitList);

											foreach ($benefitLists AS $benefitListValue) {
												$taxQUERY = mysqli_query($vtechMappingdbConn, "SELECT
													charge_pct,
													benefits
												FROM
													tax_settings
												WHERE
													empst_id = '$employmentId'
												AND
													benefits LIKE '%$benefitListValue%'");
												$taxROW = mysqli_fetch_array($taxQUERY);
												
												if ($employmentId != '3' || $employmentId != '6') {
													if ($benefit == "Without Benefits" || $benefit == "Not Applicable")
													{
														if ($employmentId == '1' || $employmentId == '4') {
															$noBenifitPercentage = '11';
														}
														if ($employmentId == '2' || $employmentId == '5') {
															 $noBenifitPercentage = '2';
														}
														if ($employmentId == '3' || $employmentId == '6') {
															 $noBenifitPercentage = '0';
														}
														$tax[] = ($noBenifitPercentage / 100) * $payRate;
													}
													if ($benefit == "With Benefits") {
														$tax[] = $payRate * ($taxROW['charge_pct'] / 100);
													}
													$taxRate = round(array_sum($tax), 2);
												} else {
													$tax[] = '0';
													$taxRate = array_sum($tax);
												}
											}

											if (($taxRate > '0') && ($benefitList != '') && ($benefitList != ' ') && ($employmentId == '1' || $employmentId == '4')) {
												$taxRate = round(($taxRate + ($payRate * 0.11)), 2);
											}
											if (($taxRate > '0') && ($benefitList != '') && ($benefitList != ' ') && ($employmentId == '2' || $employmentId == '5')) {
												$taxRate = round(($taxRate + ($payRate * 0.02)), 2);
											}

											$mspQUERY = mysqli_query($vtechMappingdbConn, "SELECT
												mspChrg_pct,
												primechrg_pct,
												primeChrg_dlr,
												mspChrg_dlr
											FROM
												client_fees
											WHERE
												client_id = '$companyId'");
											$mspROW = mysqli_fetch_array($mspQUERY);

											$mspFees = round((($mspROW['mspChrg_pct'] / 100) * $billRate), 2) + $mspROW['mspChrg_dlr'];

											$vendorQUERY = mysqli_query($vtechMappingdbConn, "SELECT
												c_primeCharge_pct,
												c_primeCharge_dlr,
												c_anyCharge_dlr
											FROM
												candidate_fees
											WHERE
												emp_id = '$employeeId'");
											$vendorROW = mysqli_fetch_array($vendorQUERY);

											$primeCharges = round(((($mspROW['primechrg_pct'] / 100) * $billRate) + (($vendorROW['c_primeCharge_pct'] / 100) * $billRate) + $vendorROW['c_primeCharge_dlr'] + $mspROW['primeChrg_dlr']), 2);

											$candidateRate = round(($payRate + $taxRate + $mspFees + $primeCharges), 2);

											$grossMargin = round(($billRate - $candidateRate), 2);

											for ($aaa = $startDateSTRTO;$aaa <= $endDateSTRTO;$aaa += 86400) {
												$currentDate = date('Y-m-d', $aaa);
												$timeEntryQUERY = mysqli_query($vtechhrmConn, "SELECT
													time_start,
													time_end
												FROM
													employeetimeentry
												WHERE
													employee = '$employeeId'
												AND
													date_format(date_start, '%Y-%m-%d') = '$currentDate'");
												while ($timeEntryROW = mysqli_fetch_array($timeEntryQUERY)) {
													$totalHour += round((decimalHours($timeEntryROW['time_end']) - decimalHours($timeEntryROW['time_start'])), 2);
												}
											}

											$totalGP = round(($grossMargin * $totalHour), 2);

											if ($mainROW['companyAge'] > $mainROW['givenCompanyAge']) {
											} else {
												$totalEmployee[] = $mainROW['employeeId'];
												$finalGP[] = $totalGP;
											}
											if ($responseType == 0) {
							?>
							<tr style="font-size: 14px;">
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords(strtolower($mainROW['employeeName'])); ?></td>
							<?php if ($mainROW['companyAge'] > $mainROW['givenCompanyAge']) { ?>
								<td style="text-align: left;vertical-align: middle;"><div class="textWrap"><?php echo $mainROW['companyName']; ?></div><div class="textWrapped"><?php echo $mainROW['companyAge']; ?></div></td>
							<?php } else { ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo $mainROW['companyName']; ?></td>
							<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['employeeStatus']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['joiningDate']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
								<?php
									if ($mainROW['employeeStatus'] != 'Active') {
										echo $mainROW['terminationDate'];
									} else {
										echo "---";
									}
								?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $grossMargin; ?></td>
							<?php if ($mainROW['companyAge'] > $mainROW['givenCompanyAge']) { ?>
								<td style="text-align: center;vertical-align: middle;color: red;"><?php echo $totalGP; ?></td>
							<?php } else { ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $totalGP; ?></td>
							<?php } ?>
							</tr>
							<?php
											} else {
												$responseArray['individual_incentive'][] = array('candidate' => ucwords(strtolower($mainROW['employeeName'])),
													'client' => $mainROW['companyName'],
													'client_age' => $mainROW['companyAge'],
													'given_client_age' => $mainROW['givenCompanyAge'],
													'status' => $mainROW['employeeStatus'],
													'join_date' => $mainROW['joiningDate'],
													'termination_date' => $mainROW['terminationDate'],
													'margin' => $grossMargin,
													'total_gp' => $totalGP
												);
											}
										}
									}
								} else {
									foreach ($lockedReport as $findLockedKey => $findLockedValue) {
										$dataObj = array();
										$dataObj = json_decode($lockedReport[0]['detail_data'], true);
										foreach ($dataObj['individual_incentive'] as $dataObjKey => $dataObjValue) {
											if ($responseType == 0) {
							?>
							<tr style="font-size: 14px;background-color: #c3dcf4;color: #000;">
								<td style="text-align: left;vertical-align: middle;"><?php echo $dataObjValue['candidate']; ?></td>
								<?php if ($dataObjValue['client_age'] > $dataObjValue['given_client_age']) { ?>
								<td style="text-align: left;vertical-align: middle;"><div class="textWrap"><?php echo $dataObjValue['client']; ?></div><div class="textWrapped"><?php echo $dataObjValue['client_age']; ?></div></td>
								<?php } else { ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo $dataObjValue['client']; ?></td>
								<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $dataObjValue['status']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $dataObjValue['join_date']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
								<?php
									if ($dataObjValue['status'] != 'Active') {
										echo $dataObjValue['termination_date'];
									} else {
										echo "---";
									}
								?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $dataObjValue['margin']; ?></td>
								<?php if ($dataObjValue['client_age'] > $dataObjValue['given_client_age']) { ?>
								<td style="text-align: center;vertical-align: middle;color: red;"><?php echo $dataObjValue['total_gp']; ?></td>
								<?php } else { ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $dataObjValue['total_gp']; ?></td>
								<?php } ?>
							</tr>
							<?php
											}
										}
									}
								}
								if ($responseType == 0) {
							?>
						</tbody>
						<tfoot>
							<?php 
								}
								if ($isLocked == "false") {
									$individualCandidate = count($totalEmployee);
									if ($responseType == 0) {
							?>
							<tr style="background-color: #ccc;font-size: 14px;color: #000;">
								<th style="text-align: center;vertical-align: middle;"><?php echo count($totalEmployee); ?></th>
								<?php
									/////////Incentive Amount//////////////////
									$finalGPValue = array_sum($finalGP);
									$iamountQUERY = mysqli_query($misReportsConn, "SELECT * FROM incentive_criteria WHERE personnel = 'USBD' AND comment = 'Individual'");
									$individualIncentive = $individualPercentage = '0';
									while ($iamountROW = mysqli_fetch_array($iamountQUERY)) {
										if ($iamountROW['min_margin'] == '0' && $finalGPValue < $iamountROW['max_margin']) {
											$individualPercentage = $iamountROW['value'];
											$individualIncentive = round(($iamountROW['value'] * $finalGPValue) / 100, 2);
										} elseif ($finalGPValue >= $iamountROW['min_margin'] && $finalGPValue < $iamountROW['max_margin']) {
											$individualPercentage = $iamountROW['value'];
											$individualIncentive = round(($iamountROW['value'] * $finalGPValue) / 100, 2);
										} elseif ($finalGPValue >= $iamountROW['min_margin'] && $iamountROW['max_margin'] == '0') {
											$individualPercentage = $iamountROW['value'];
											$individualIncentive = round(($iamountROW['value'] * $finalGPValue) / 100, 2);
										}
									}
									$individualGP = array_sum($finalGP);
								?>
								<th style="text-align: center;vertical-align: middle;" colspan="5">Incentive Amount : <?php echo number_format($individualIncentive, 2)." (".$individualPercentage."%)"; ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo number_format(array_sum($finalGP), 2); ?></th>
							</tr>
							<?php
									}
								} else {
									foreach ($lockedReport as $findLockedKey => $findLockedValue) {
										if ($responseType == 0) {
							?>
							<tr style="background-color: #ccc;font-size: 14px;color: #000;">
								<th style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['individual_candidate']; ?></th>
								<th style="text-align: center;vertical-align: middle;" colspan="5">Incentive Amount : <?php echo $lockedReport[0]['individual_incentive']." (".$lockedReport[0]['individual_incentive_perc']."%)"; ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['individual_gp']; ?></th>
							</tr>
							<?php
										}
									}
								}
								if ($responseType == 0) {
							?>
						</tfoot>
					</table>
				</div>
			</div>

			<div class="row" style="margin-bottom: 10px;">
				<div class="col-md-4 col-md-offset-4 reportTitle"><i class="fa fa-tachometer"></i> Contract Signing Bonus</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-10 col-md-offset-1">
					<table id="customizedDataTableOpp" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #ccc;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;">Opportunities</th>
								<th style="text-align: center;vertical-align: middle;">Account</th>
								<th style="text-align: center;vertical-align: middle;">Contract No.</th>
								<th style="text-align: center;vertical-align: middle;">Type</th>
								<th style="text-align: center;vertical-align: middle;">Sign Date</th>
								<th style="text-align: center;vertical-align: middle;">Signing Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
								}
								$contractQUERY = "SELECT
									copp.name,
								    copp.createDate,
								    copp.c_contract_type,
								    copp.c_solicitation_number,
									copp.accountName,
									(SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'USBD' AND ic.comment = copp.c_contract_type) AS signingAmount
								FROM
									contract.x2_opportunities AS copp
								    JOIN vtechcrm.x2_users AS suser ON copp.c_onsite_sale_person = suser.username OR copp.c_onsite_post_sales = suser.username
								WHERE
									concat(suser.firstName,' ',suser.lastName) = '$salesPersonnel'
								AND
									copp.createDate BETWEEN '$startDateSTRTO' AND '$endDateSTRTO'
								GROUP BY copp.id";
								$contractRESULT = mysqli_query($allConn, $contractQUERY);
								$totalOpportunities = $totalSigningAmount = array();
								if (mysqli_num_rows($contractRESULT) > 0) {
									while ($contractROW = mysqli_fetch_array($contractRESULT)) {
										$totalSigningAmount[] = $contractROW['signingAmount'];
										if ($responseType == 0) {
							?>
							<tr>
								<td style="text-align: left;vertical-align: middle;"><?php echo $contractROW['name']; ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $contractROW['accountName']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $contractROW['c_solicitation_number']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $contractROW['c_contract_type']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $contractROW['createDate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $contractROW['signingAmount']; ?></td>
							</tr>
							<?php
										} else {
											$responseArray['signing_bonus'][] = array('opportunity' => $contractROW['name'],
												'account' => $contractROW['accountName'],
												'contract_no' => $contractROW['c_solicitation_number'],
												'contract_type' => $contractROW['c_contract_type'],
												'sign_date' => $contractROW['createDate'],
												'signing_amount' => $contractROW['signingAmount']
											);
										}
									}
								}
								$totalBonus = array_sum($totalSigningAmount);
								if ($responseType == 0) {
							?>
						</tbody>
						<tfoot>
							<tr style="background-color: #ccc;font-size: 14px;color: #000;">
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($totalOpportunities); ?></th>
								<th style="text-align: right;vertical-align: middle;" colspan="4">Total Signing Bonus : </th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($totalSigningAmount); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>

<?php
	}
	if ($personnelType == 'Manager') {
		if ($responseType == 0) {
?>
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-md-4 col-md-offset-4 reportTitle"><i class="fa fa-tachometer"></i> Team Incentive</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-10 col-md-offset-1">
					<table id="customizedDataTableTeam" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #ccc;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;">Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Candidate</th>
								<th style="text-align: center;vertical-align: middle;">Client</th>
								<th style="text-align: center;vertical-align: middle;">Status</th>
								<th style="text-align: center;vertical-align: middle;">Join Date</th>
								<th style="text-align: center;vertical-align: middle;">Termination Date</th>
								<th style="text-align: center;vertical-align: middle;">Margin</th>
								<th style="text-align: center;vertical-align: middle;">Total GP</th>
							</tr>
						</thead>
						<tbody>
							<?php
								}
								if ($isLocked == "false") {
									foreach ($uniqueTeamList as $uniqueTeamListKey => $uniqueTeamListValue) {
										$subQUERY = "SELECT
											ef.value AS personnelName,
											e.id AS employeeId,
											CONCAT(e.first_name,' ',e.last_name) AS employeeName,
											e.status AS employeeStatus,
											DATE_FORMAT(e.custom7, '%m-%d-%Y') AS joiningDate,
											DATE_FORMAT(e.termination_date, '%m-%d-%Y') AS terminationDate,
											e.custom1 AS benefit,
											e.custom2 AS benefitList,
											CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS billRate,
											CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS payRate,
											es.id AS employmentId,
											es.name AS employmentType,
											comp.company_id AS companyId,
											comp.name AS companyName,
											TIMESTAMPDIFF(YEAR, comp.date_created, CURDATE()) AS companyAge,
											(SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'USBD' AND ic.comment = 'Client Age') AS givenCompanyAge
										FROM
											employees AS e
											JOIN employmentstatus AS es ON e.employment_status = es.id
										    JOIN vtech_mappingdb.system_integration AS si ON e.id = si.h_employee_id
											JOIN cats.company AS comp ON si.c_company_id = comp.company_id
										    JOIN cats.extra_field AS ef ON ef.data_item_id = comp.company_id
										WHERE
											ef.field_name IN ('OnSite Sales Person','OnSite Post Sales')
										AND
											ef.value = '$uniqueTeamListValue'
										AND
											date_format(e.custom7, '%Y-%m-%d') > (SELECT date_format(he.confirmation_date, '%Y-%m-%d') AS confirmationDate FROM employees AS he WHERE concat(REPLACE(TRIM(he.first_name), 'ben', ''),' ',TRIM(he.last_name)) = '$uniqueTeamListValue')
										AND
											date_format(e.custom7, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
										GROUP BY employeeId";
										$taxRate = $mspFees = $primeCharges = $candidateRate = $grossMargin = $totalHour = $totalGP = $teamCandidate = $teamGP = "";
										$totalEmployee = $finalGP = array();
										$subRESULT = mysqli_query($vtechhrmConn, $subQUERY);
										if (mysqli_num_rows($subRESULT) > 0) {
											while ($subROW = mysqli_fetch_array($subRESULT)) {
												$employeeId = $subROW['employeeId'];
												$companyId = $subROW['companyId'];
												$billRate = $subROW['billRate'];
												$payRate = $subROW['payRate'];
												$employmentId = $subROW['employmentId'];
												$benefit = $subROW['benefit'];

												$delimiter = array("","[","]",'"');
												$benefitList = str_replace($delimiter, $delimiter[0], $subROW['benefitList']);

												$tax = array();

												$benefitLists = explode(",", $benefitList);

												foreach ($benefitLists AS $benefitListValue) {
													$taxQUERY = mysqli_query($vtechMappingdbConn, "SELECT
														charge_pct,
														benefits
													FROM
														tax_settings
													WHERE
														empst_id = '$employmentId'
													AND
														benefits LIKE '%$benefitListValue%'");
													$taxROW = mysqli_fetch_array($taxQUERY);
													
													if ($employmentId != '3' || $employmentId != '6') {
														if ($benefit == "Without Benefits" || $benefit == "Not Applicable")
														{
															if ($employmentId == '1' || $employmentId == '4') {
																$noBenifitPercentage = '11';
															}
															if ($employmentId == '2' || $employmentId == '5') {
																 $noBenifitPercentage = '2';
															}
															if ($employmentId == '3' || $employmentId == '6') {
																 $noBenifitPercentage = '0';
															}
															$tax[] = ($noBenifitPercentage / 100) * $payRate;
														}
														if ($benefit == "With Benefits") {
															$tax[] = $payRate * ($taxROW['charge_pct'] / 100);
														}
														$taxRate = round(array_sum($tax), 2);
													} else {
														$tax[] = '0';
														$taxRate = array_sum($tax);
													}
												}

												if (($taxRate > '0') && ($benefitList != '') && ($benefitList != ' ') && ($employmentId == '1' || $employmentId == '4')) {
													$taxRate = round(($taxRate + ($payRate * 0.11)), 2);
												}
												if (($taxRate > '0') && ($benefitList != '') && ($benefitList != ' ') && ($employmentId == '2' || $employmentId == '5')) {
													$taxRate = round(($taxRate + ($payRate * 0.02)), 2);
												}

												$mspQUERY = mysqli_query($vtechMappingdbConn, "SELECT
													mspChrg_pct,
													primechrg_pct,
													primeChrg_dlr,
													mspChrg_dlr
												FROM
													client_fees
												WHERE
													client_id = '$companyId'");
												$mspROW = mysqli_fetch_array($mspQUERY);

												$mspFees = round((($mspROW['mspChrg_pct'] / 100) * $billRate), 2) + $mspROW['mspChrg_dlr'];

												$vendorQUERY = mysqli_query($vtechMappingdbConn, "SELECT
													c_primeCharge_pct,
													c_primeCharge_dlr,
													c_anyCharge_dlr
												FROM
													candidate_fees
												WHERE
													emp_id = '$employeeId'");
												$vendorROW = mysqli_fetch_array($vendorQUERY);

												$primeCharges = round(((($mspROW['primechrg_pct'] / 100) * $billRate) + (($vendorROW['c_primeCharge_pct'] / 100) * $billRate) + $vendorROW['c_primeCharge_dlr'] + $mspROW['primeChrg_dlr']), 2);

												$candidateRate = round(($payRate + $taxRate + $mspFees + $primeCharges), 2);

												$grossMargin = round(($billRate - $candidateRate), 2);

												for ($aaa = $startDateSTRTO;$aaa <= $endDateSTRTO;$aaa += 86400) {
													$currentDate = date('Y-m-d', $aaa);
													$timeEntryQUERY = mysqli_query($vtechhrmConn, "SELECT
														time_start,
														time_end
													FROM
														employeetimeentry
													WHERE
														employee = '$employeeId'
													AND
														date_format(date_start, '%Y-%m-%d') = '$currentDate'");
													while ($timeEntryROW = mysqli_fetch_array($timeEntryQUERY)) {
														$totalHour += round((decimalHours($timeEntryROW['time_end']) - decimalHours($timeEntryROW['time_start'])), 2);
													}
												}

												$totalGP = round(($grossMargin * $totalHour), 2);
												
												if ($subROW['companyAge'] > $subROW['givenCompanyAge']) {
												} else {
													$totalEmployee[] = $subROW['employeeId'];
													$finalGP[] = $totalGP;
												}
												if ($responseType == 0) {
							?>
							<tr style="font-size: 14px;">
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords(strtolower($subROW['personnelName'])); ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords(strtolower($subROW['employeeName'])); ?></td>
							<?php if ($subROW['companyAge'] > $subROW['givenCompanyAge']) { ?>
								<td style="text-align: left;vertical-align: middle;"><div class="textWrap"><?php echo $subROW['companyName']; ?></div><div class="textWrapped"><?php echo $subROW['companyAge']; ?></div></td>
							<?php } else { ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo $subROW['companyName']; ?></td>
							<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $subROW['employeeStatus']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $subROW['joiningDate']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
								<?php
									if ($subROW['employeeStatus'] != 'Active') {
										echo $subROW['terminationDate'];
									} else {
										echo "---";
									}
								?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $grossMargin; ?></td>
							<?php if ($subROW['companyAge'] > $subROW['givenCompanyAge']) { ?>
								<td style="text-align: center;vertical-align: middle;color: red;"><?php echo $totalGP; ?></td>
							<?php } else { ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $totalGP; ?></td>
							<?php } ?>
							</tr>
							<?php
												} else {
													$responseArray['team_incentive'][] = array('personnel' => ucwords(strtolower($subROW['personnelName'])),
														'candidate' => ucwords(strtolower($subROW['employeeName'])),
														'client' => $subROW['companyName'],
														'client_age' => $subROW['companyAge'],
														'given_client_age' => $subROW['givenCompanyAge'],
														'status' => $subROW['employeeStatus'],
														'join_date' => $subROW['joiningDate'],
														'termination_date' => $subROW['terminationDate'],
														'margin' => $grossMargin,
														'total_gp' => $totalGP
													);
												}
											}
										}
									}
								} else {
									foreach ($lockedReport as $findLockedKey => $findLockedValue) {
										$dataObj = array();
										$dataObj = json_decode($lockedReport[0]['detail_data'], true);
										foreach ($dataObj['team_incentive'] as $dataObjKey => $dataObjValue) {
											if ($responseType == 0) {
							?>
							<tr style="font-size: 14px;background-color: #c3dcf4;color: #000;">
								<td style="text-align: left;vertical-align: middle;"><?php echo $dataObjValue['personnel']; ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $dataObjValue['candidate']; ?></td>
								<?php if ($dataObjValue['client_age'] > $dataObjValue['given_client_age']) { ?>
								<td style="text-align: left;vertical-align: middle;"><div class="textWrap"><?php echo $dataObjValue['client']; ?></div><div class="textWrapped"><?php echo $dataObjValue['client_age']; ?></div></td>
								<?php } else { ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo $dataObjValue['client']; ?></td>
								<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $dataObjValue['status']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $dataObjValue['join_date']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
								<?php
									if ($dataObjValue['status'] != 'Active') {
										echo $dataObjValue['termination_date'];
									} else {
										echo "---";
									}
								?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $dataObjValue['margin']; ?></td>
								<?php if ($dataObjValue['client_age'] > $dataObjValue['given_client_age']) { ?>
								<td style="text-align: center;vertical-align: middle;color: red;"><?php echo $dataObjValue['total_gp']; ?></td>
								<?php } else { ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $dataObjValue['total_gp']; ?></td>
								<?php } ?>
							</tr>
							<?php
											}
										}
									}
								}
								if ($responseType == 0) {
							?>
						</tbody>
						<tfoot>
							<?php
								}
								if ($isLocked == "false") {
								$teamCandidate = count($totalEmployee);
								if ($responseType == 0) {
							?>
							<tr style="background-color: #ccc;font-size: 14px;color: #000;">
								<th style="text-align: center;vertical-align: middle;"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo count($totalEmployee); ?></th>
								<?php
									}
									/////////Incentive Amount//////////////////
									$finalGPValue = array_sum($finalGP);
									$iamountQUERY = mysqli_query($misReportsConn, "SELECT * FROM incentive_criteria WHERE personnel = 'USBD' AND comment = 'Individual'");
									$teamIncentive = $teamPercentage = '0';
									while ($iamountROW = mysqli_fetch_array($iamountQUERY)) {
										if ($iamountROW['min_margin'] == '0' && $finalGPValue < $iamountROW['max_margin']) {
											$teamPercentage = $iamountROW['value'];
											$teamIncentive = round(($iamountROW['value'] * $finalGPValue) / 100, 2);
										} elseif ($finalGPValue >= $iamountROW['min_margin'] && $finalGPValue < $iamountROW['max_margin']) {
											$teamPercentage = $iamountROW['value'];
											$teamIncentive = round(($iamountROW['value'] * $finalGPValue) / 100, 2);
										} elseif ($finalGPValue >= $iamountROW['min_margin'] && $iamountROW['max_margin'] == '0') {
											$teamPercentage = $iamountROW['value'];
											$teamIncentive = round(($iamountROW['value'] * $finalGPValue) / 100, 2);
										}
									}
									$teamGP = number_format(array_sum($finalGP), 2);
									if ($responseType == 0) {
								?>
								<th style="text-align: center;vertical-align: middle;" colspan="5">Incentive Amount : <?php echo number_format($teamIncentive, 2)." (".$teamPercentage."%)"; ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo number_format(array_sum($finalGP), 2); ?></th>
							</tr>
							<?php
									}
								} else {
									foreach ($lockedReport as $findLockedKey => $findLockedValue) {
										if ($responseType == 0) {
							?>
							<tr style="background-color: #ccc;font-size: 14px;color: #000;">
								<th style="text-align: center;vertical-align: middle;"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['team_candidate']; ?></th>
								<th style="text-align: center;vertical-align: middle;" colspan="5">Incentive Amount : <?php echo $lockedReport[0]['team_incentive']." (".$lockedReport[0]['team_incentive_perc']."%)"; ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['team_gp']; ?></th>
							</tr>
							<?php
										}
									}
								}
								if ($responseType == 0) {
							?>
						</tfoot>
					</table>
				</div>
			</div>
<?php
		}
	}
	if ($responseType == 0) {
?>
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-md-4 col-md-offset-4 reportTitle"><i class="fa fa-tachometer"></i> Lock<?php if ($isLocked == 'true') { echo "ed"; } ?> Incentive</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-10 col-md-offset-1">
					<form class="lockForm">
						<table class="table table-striped table-bordered">
							<thead>
								<tr style="background-color: #ccc;color: #000;font-size: 14px;">
									<th style="text-align: center;vertical-align: middle;">Personnel</th>
									<th style="text-align: center;vertical-align: middle;">Individual<br>Incentive</th>
									<th style="text-align: center;vertical-align: middle;">Signing<br>Bonus</th>
								<?php if ($personnelType == 'Manager') { ?>
									<th style="text-align: center;vertical-align: middle;">Team<br>Incentive</th>
								<?php } ?>
									<th style="text-align: center;vertical-align: middle;">Final<br>Incentive</th>
									<th style="text-align: center;vertical-align: middle;">Method</th>
									<th style="text-align: center;vertical-align: middle;">Amount</th>
									<th style="text-align: center;vertical-align: middle;">Comment</th>
									<th style="text-align: center;vertical-align: middle;">Final Amount</th>
								</tr>
							</thead>
							<tbody>
								<?php
									if ($isLocked == 'true') {
										foreach ($lockedReport as $findLockedKey => $findLockedValue) {
								?>
								<tr style="font-size: 13px;background-color: #c3dcf4;color: #000;">
									<td style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['person_name']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['individual_incentive']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['total_bonus']; ?></td>
								<?php if ($personnelType == 'Manager') { ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['team_incentive']; ?></td>
								<?php } ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['final_incentive']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo ucfirst($lockedReport[0]['adjustment_method']); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['adjustment_amount']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['adjustment_comment']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $lockedReport[0]['final_amount']; ?></td>
								</tr>
								<?php
										}
									} else {
								?>
								<tr style="font-size: 13px;">
									<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($salesPersonnel); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $individualIncentive; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $totalBonus; ?></td>
								<?php if ($personnelType == 'Manager') { ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo $teamIncentive; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $finalIncentive = $individualIncentive + $totalBonus + $teamIncentive; ?></td>
								<?php } else { ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo $finalIncentive = $individualIncentive + $totalBonus; ?></td>
								<?php } ?>
									<input type="hidden" name="personnelName" value="<?php echo ucwords($salesPersonnel); ?>">
									<input type="hidden" name="type" value="<?php echo $personnelType; ?>">
									<input type="hidden" name="startDate" value="<?php echo $startDate; ?>">
									<input type="hidden" name="endDate" value="<?php echo $endDate; ?>">
									<input type="hidden" name="individualCandidate" value="<?php echo $individualCandidate; ?>">
									<input type="hidden" name="individualGp" value="<?php echo $individualGP; ?>">
									<input type="hidden" name="individualIncentive" value="<?php echo $individualIncentive; ?>">
									<input type="hidden" name="individualIncentivePerc" value="<?php echo $individualPercentage; ?>">
									<input type="hidden" name="totalOpportunities" value="<?php echo array_sum($totalOpportunities); ?>">
									<input type="hidden" name="totalBonus" value="<?php echo $totalBonus; ?>">
									<input type="hidden" name="teamCandidate" value="<?php echo $teamCandidate; ?>">
									<input type="hidden" name="teamGp" value="<?php echo $teamGP; ?>">
									<input type="hidden" name="teamIncentive" value="<?php echo $teamIncentive; ?>">
									<input type="hidden" name="teamIncentivePerc" value="<?php echo $teamPercentage; ?>">
									<input type="hidden" name="finalIncentive" class="finalIncentive" value="<?php echo $finalIncentive; ?>">

									<input type="hidden" name="detailLink" value="<?php echo $detailLink; ?>">

									<td style="text-align: center;vertical-align: middle;">
										<select class="adjustmentMethod updateAmount" name="adjustmentMethod">
											<option value="plus">ADD (+)</option>
											<option value="minus">SUB (-)</option>
										</select>
									</td>
									<td style="text-align: center;vertical-align: middle;">
										<input type="text" class="adjustmentAmount checkNumber updateAmount" name="adjustmentAmount" maxlength="10" placeholder="0" autocomplete="off">
									</td>
									<td style="text-align: center;vertical-align: middle;">
										<textarea name="adjustmentComment" rows="1" autocomplete="off"></textarea>
									</td>
									<td style="text-align: center;vertical-align: middle;">
										<input type="text" class="finalAmount" name="finalAmount" value="<?php echo $finalIncentive; ?>" autocomplete="off" readonly>
									</td>
								</tr>
								<?php } ?>
							</tbody>
							<?php if ($isLocked == 'false') { ?>
							<tfoot>
								<?php if ($personnelType == 'Manager') { ?>
									<th class="tfootTh" colspan="9"><button type="submit" class="btn btn-primary lockButton"><i class="fa fa-lock"></i> Lock the Amount</button></th>
								<?php } else { ?>
									<th class="tfootTh" colspan="8"><button type="submit" class="btn btn-primary lockButton"><i class="fa fa-lock"></i> Lock the Amount</button></th>
								<?php } ?>
							</tfoot>
							<?php } ?>
						</table>
					</form>
				</div>
			</div>

		</div>
	</section>
<?php
		}
	}
	if ($responseType == 0) {
?>

<script>
	$(document).on('keypress', '.checkNumber', function(e) {
		if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
			return false;
		}
	});

	$(document).on('change', '.updateAmount', function(e) {
		e.preventDefault();
		
		var finalIncentive = parseFloat($('.finalIncentive').val());
		
		if ($('.adjustmentAmount').val() == "") {
			var adjustmentAmount = parseFloat(0);
		} else {
			var adjustmentAmount = parseFloat($('.adjustmentAmount').val());
		}
		
		var adjustmentMethod = $('.adjustmentMethod').val();

		if (adjustmentMethod == 'plus') {
			$('.finalAmount').val(finalIncentive + adjustmentAmount);
		} else {
			$('.finalAmount').val(finalIncentive - adjustmentAmount);
		}
	});

	$(document).on('submit', '.lockForm', function(e) {
		e.preventDefault();
		$(".LoadingImage").show();
		$.ajax({
			url: 'lockamount.php',
			type: 'post',
			data: $('.lockForm').serialize(),
			success: function(response) {
				if ($.trim(response) == 'true') {
					$(".LoadingImage").hide();
					location.reload();
	        		alert('Incentive Successfully Locked!');
				} else {
					$(".LoadingImage").hide();
	        		alert('Something Wrong!');
				}
			}
		});
	});
</script>

</body>
</html>
<?php
} else {
	echo json_encode($responseArray);
}
		}else{
			if($childUser == 'Admin'){
				header("Location:../../../admin.php");
			}elseif($childUser == 'User'){
				header("Location:../../../user.php");
			}else{
				header("Location:../../../index.php");
			}
		}
    }else{
        header("Location:../../../index.php");
    }
?>
