<?php
	include_once("../../../security.php");
	
	$responseArray = array();
	$responseType = isset($_REQUEST['response_type']) && $_REQUEST['response_type'] == 1 ? 1 : 0;

    if(isset($_SESSION['user'])){
		error_reporting(0);
		include_once('../../../config.php');

    	$childUser = $_SESSION['userMember'];
		$reportID = '50';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
		$sessionResult = mysqli_query($misReportsConn, $sessionQuery);
		if(mysqli_num_rows($sessionResult) > 0){
			if($responseType == 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Old CSM Incentive Report</title>

	<?php
		include_once('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th{
			padding: 3px 2px;
		}
		table.dataTable tbody td{
			padding: 3px 2px;
		}
		table.dataTable tfoot th{
			padding: 3px 10px;
		}
		.btnx,
		.btnx:focus{
			background-color: #2266AA;
			color: #fff;
			outline: none;
			border-color: #2266AA;
			border-radius: 0px;
		}
		.btny,
		.btny:focus{
			background-color: #fff;
			color: #2266AA;
			outline: none;
			border-color: #2266AA;
			border-radius: 0px;
			font-weight: bold;
		}
	</style>

	<script>
		$(document).ready(function(){

			/*Hide & show sections START*/
			$("#LoadingImage").hide();
			$('#MainSection').removeClass("hidden");
			$('#CSMIRdatatable').removeClass("hidden");
			/*Hide & show sections END*/

			/*multimonth Script START*/
			$("#multimonth").datepicker({
				format: "mm/yyyy",
			    startView: 1,
			    minViewMode: 1,
			    maxViewMode: 2,
			    clearBtn: true,
			    multidate: false,
				orientation: "top",
				autoclose: true
			});
			/*multimonth Script END*/

			/*Select CS Manager START*/
			$('#manager_name').multiselect({
				nonSelectedText: 'Select CS Manager',
				numberDisplayed: 1,
				enableFiltering:true,
				enableCaseInsensitiveFiltering:true,
				buttonWidth:'100%',
				includeSelectAllOption: true,
					maxHeight: 300
			});
			$("#manager_name").multiselect('selectAll', false);
	        $("#manager_name").multiselect('updateButtonText');
			/*Select CS Manager END*/

			/*Datatable Calling START*/
			var tableX = $('#CSMIRdataX').DataTable({
				"paging": false,
			    dom: 'Bfrtip',
				"aaSorting": [[1,'asc']],
			    "columnDefs":[{
					"targets" : 'no-sort',
					"orderable": false,
			    }],
		        buttons:[
		            'excel'
		        ],
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","250")
				}
			});
			tableX.button(0).nodes().css('background', '#2266AA');
			tableX.button(0).nodes().css('border', '#2266AA');
			tableX.button(0).nodes().css('color', '#fff');
			tableX.button(0).nodes().html('Download Report');
			tableX.button(1).nodes().css('background', '#449D44');
			tableX.button(1).nodes().css('border', '#449D44');
			tableX.button(1).nodes().css('color', '#fff');
			/*Datatable Calling END*/

			/*Datatable Calling START*/
			var tableX = $('#CSMIRdata').DataTable({
				"paging": false,
			    dom: 'Bfrtip',
				"aaSorting": [],
			    "columnDefs":[{
					"targets" : 'no-sort',
					"orderable": false,
			    }],
		        buttons:[
		            'excel'
		        ],
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","250")
				}
			});
			tableX.button(0).nodes().css('background', '#2266AA');
			tableX.button(0).nodes().css('border', '#2266AA');
			tableX.button(0).nodes().css('color', '#fff');
			tableX.button(0).nodes().html('Download Report');
			tableX.button(1).nodes().css('background', '#449D44');
			tableX.button(1).nodes().css('border', '#449D44');
			tableX.button(1).nodes().css('color', '#fff');
			/*Datatable Calling END*/
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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Old CSM Incentive Report</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section id="MainSection" class="hidden" style="margin-top: 20px;margin-bottom: 100px;">
		<div class="container">
			<form action="index.php" method="get">
				<div class="row" style="margin-top: 5px;">
					<div class="col-md-2">
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="btny form-control"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Home</button>
					</div>
					<div class="col-md-4 col-md-offset-2">
						<label>Select Month :</label>
						<div class="input-group">
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control" id="multimonth" name="multimonth" placeholder="MM/YYYY" type="text" value="<?php if(isset($_REQUEST['multimonth'])){echo $_REQUEST['multimonth'];}?>" autocomplete="off" required>
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-8">
						<div class="row" style="margin-top: 25px;">
							<div class="col-md-6 col-md-offset-6">
								<label>Select CS Manager :</label>
								<select class="form-control" id="manager_name" name="manager_id[]" style="border: 1px solid #aaa;border-radius: 0px;" multiple required>
									<?php
										$sqluser="SELECT
											user.user_id AS manager_id,
											extra_field.value AS manager_name
										FROM
											extra_field
											LEFT JOIN user ON CONCAT(user.first_name,' ',user.last_name)=extra_field.value
										WHERE
											extra_field.field_name='Manager - Client Service'
										AND
											user.access_level!='0'
										AND
											extra_field.value!='Sahil Khan'
										AND
											extra_field.value!='Ashutosh Upadhyay'
										GROUP BY manager_name";
										$resultuser=mysqli_query($catsConn,$sqluser);
										while($userlist=mysqli_fetch_array($resultuser)){
											echo "<option value='".$userlist['manager_id']."'>".$userlist['manager_name']."</option>";
										}
									?>
								</select>
							</div>
							<div class="col-md-3 col-md-offset-6" style="margin-top: 30px;">
								<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
							</div>
							<div class="col-md-3" style="margin-top: 30px;">
								<button type="submit" class="form-control" name="OCSMsubmit" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
							</div>
						</div>
					</div>
					<div class="col-md-4">
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	}
	//POST Overview Section
	if(isset($_REQUEST['OCSMsubmit'])){
		$monthsdata = $_REQUEST['multimonth'];
		$managerdata = $_REQUEST['manager_id'];
		if($responseType == 0){
?>
	<section id="CSMIRdatatable" class="hidden">
		<div class="container-fluid">
			<form id="CSMincentive" onsubmit="return true">
				<div class="row" style="margin-bottom: 50px;">
					<div class="col-md-10 col-md-offset-1">
						<table id="CSMIRdataX" class="table table-striped table-bordered">
							<thead>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th class='no-sort' style="text-align: center;vertical-align: middle;" rowspan="2"><i class=" fa fa-check-square-o" style="font-size: 20px;"></i></th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">CS Manager</th>
									<th style="text-align: center;vertical-align: middle;" colspan="2">Target Placement</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Additional Incentive</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Incentive</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Amount</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th class="no-sort" style="text-align: center;vertical-align: middle;">Total No.</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;">Incentive</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;">Method</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;">Amount</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Comment</th>
								</tr>
							</thead>
							<tbody>
							<?php
								}
								$monthsdata = str_replace("-", "/", $monthsdata);
	
								$dateGiven = explode("/", $monthsdata);

								$dateModified = $dateGiven[1]."-".$dateGiven[0];

								$dtX = $dateGiven[0]."-".$dateGiven[1];
								
								$fromDate = date('Y-m-01', strtotime($dateModified));
								$toDate = date('Y-m-t', strtotime($dateModified));

								$minMarginQRY = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Minimum Margin'");
								$minMarginROW = mysqli_fetch_array($minMarginQRY);
								$minMarginVAL = $minMarginROW['value'];

								$iii = "0";
								foreach($managerdata AS $managerdataX){

									/////// Target Placement Count Till $fromDate START ///////
									$eligibilityX = array();
									$selectedLastMonth = $dateGiven[0] - 1;

									$lastMonthLastDateX = "last day of ".date("M", mktime(0, 0, 0, $selectedLastMonth, 10));
									$firstMonthFirstDate = date('Y-m-d', strtotime('first day of january'));
									if ($selectedLastMonth != '0'){
										$lastMonthLastDate = date('Y-m-d', strtotime($lastMonthLastDateX));
									
										$tpQRY = mysqli_query($catsConn, "SELECT
										    emp.id AS eid,
										    concat(emp.first_name,' ',emp.last_name) AS ename,
											date_format(emp.custom7, '%Y-%m-%d') AS joindateX,
											emp.status AS hrm_status,
											date_format(emp.termination_date, '%Y-%m-%d') AS termi_dateX
										FROM
											candidate_joborder_status_history AS cjsh
											JOIN candidate AS can ON can.candidate_id = cjsh.candidate_id
											JOIN candidate_joborder AS cj ON cj.candidate_id = cjsh.candidate_id AND cj.joborder_id = cjsh.joborder_id
											JOIN joborder AS job ON job.joborder_id = cjsh.joborder_id
											JOIN company AS comp ON comp.company_id = job.company_id
										    JOIN vtech_mappingdb.system_integration AS mp ON mp.c_candidate_id = cjsh.candidate_id
										    JOIN vtechhrm.employees AS emp ON emp.id = mp.h_employee_id
										WHERE
											cjsh.status_to = '800'
										AND
											comp.owner = '$managerdataX'
										AND
										    date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$firstMonthFirstDate' AND '$lastMonthLastDate'
										GROUP BY cj.candidate_id");
										if (mysqli_num_rows($tpQRY) > 0) {
											while ($tpROW = mysqli_fetch_array($tpQRY)) {
												$cur_dateX = strtotime(date("Y-m-d"));
												$third_mon_dateX = strtotime($tpROW['joindateX'].' 3 month');
												$termi_date_chkX = strtotime($tpROW['termi_dateX']);
												if($tpROW['hrm_status'] == 'Active'){
													if($cur_dateX > $third_mon_dateX){
														$eligibilityX[] = "Yes";
													}
												}else{
													if($termi_date_chkX > $third_mon_dateX){
														$eligibilityX[] = "Yes";
													}
												}
											}
										}
										$tpVAL =  sizeof($eligibilityX) + 1;
									}else{
										$tpVAL = 1;
									}
									/////// Target Placement Count Till $fromDate END ///////

									$recruiterdata = array();
									$managerQUERY = mysqli_query($catsConn, "SELECT concat(first_name,' ',last_name) AS man_name FROM user WHERE user_id = '$managerdataX'");
									$managerROW = mysqli_fetch_array($managerQUERY);
									$managerNM = $managerROW['man_name'];

									$recruiterQUERY = mysqli_query($catsConn, "SELECT user_id FROM user WHERE notes = '$managerNM'");
									while($recruiterROW = mysqli_fetch_array($recruiterQUERY)){
										$recruiterdata[] = $recruiterROW['user_id'];
									}
									$recruiterdataX = implode(",", $recruiterdata);

									$mainQUERY = "SELECT
										cjsh.candidate_id AS canid,
										concat(can.first_name,' ',can.last_name) AS canname,
										comp.company_id AS cid,
										comp.name AS cname,
										concat(u.first_name,' ',u.last_name) AS client_manager,
									    (SELECT concat(first_name,' ',last_name) AS rname FROM user WHERE user_id = cj.added_by) AS recruiter,
										(SELECT notes FROM user WHERE user_id = cj.added_by) AS recruiter_manager,
									    cjsh.status_to AS cats_status,
									    (SELECT mc.designation FROM user JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = user.user_id WHERE user.user_id = cj.added_by) AS designation,
									    emp.id AS eid,
										emp.status AS hrm_status,
									    concat(emp.first_name,' ',emp.last_name) AS ename,
									    date_format(emp.custom7, '%m-%d-%Y') AS joindate,
										date_format(emp.custom7, '%Y-%m-%d') AS joindateX,
									    date_format(emp.termination_date, '%m-%d-%Y') AS termi_date,
										date_format(emp.termination_date, '%Y-%m-%d') AS termi_dateX,
										emp.custom1 AS benefit,
										emp.custom2 AS benefitlist,
										CAST(replace(emp.custom3,'$','') AS DECIMAL (10,2)) AS billrate,
										CAST(replace(emp.custom4,'$','') AS DECIMAL (10,2)) AS payrate,
										es.id AS es_id,
										es.name AS employment_type
									FROM
										candidate_joborder_status_history AS cjsh
										JOIN candidate AS can ON can.candidate_id = cjsh.candidate_id
										JOIN candidate_joborder AS cj ON cj.candidate_id = cjsh.candidate_id AND cj.joborder_id = cjsh.joborder_id
										JOIN joborder AS job ON job.joborder_id = cjsh.joborder_id
										JOIN company AS comp ON comp.company_id = job.company_id
										JOIN user AS u ON u.user_id = comp.owner
									    JOIN vtech_mappingdb.system_integration AS mp ON mp.c_candidate_id = cjsh.candidate_id
									    JOIN vtechhrm.employees AS emp ON emp.id = mp.h_employee_id
									    JOIN vtechhrm.employmentstatus AS es ON es.id = emp.employment_status
									WHERE
										(cjsh.status_to = '800' OR cjsh.status_to = '620')
									AND
										(comp.owner = '$managerdataX' OR cj.added_by IN ({$recruiterdataX}))
									AND
									    date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
									GROUP BY cj.candidate_id";
									$mainRESULT = mysqli_query($catsConn, $mainQUERY);

									$tax_r=$mspfee=$prime_charge=$rate_can=$g_margin=$tragetPlacement=$iamountX=$targetIncentiveX=$finalIncentive=0;
									$iamount=$targetIncentive=array();

									if(mysqli_num_rows($mainRESULT) > 0){
										while($mainROW = mysqli_fetch_array($mainRESULT)){
											$emp_eid = $mainROW['eid'];
											$clid = $mainROW['cid'];
											$billrate = $mainROW['billrate'];
											$payrate = $mainROW['payrate'];
											$est_id = $mainROW['es_id'];
											$benefit = $mainROW['benefit'];

											$delimiter = array("","[","]",'"');
											$replace = str_replace($delimiter, $delimiter[0], $mainROW['benefitlist']);
											$benefitlist = $replace;

											$tax = array();

											/////// TAX START ///////

											$benefitLists = explode(",",$benefitlist);
											foreach($benefitLists AS $value22){
												$taxQUERY = "SELECT charge_pct,benefits FROM tax_settings WHERE empst_id=$est_id AND benefits LIKE '%$value22%'";
												$taxRESULT = mysqli_query($vtechMappingdbConn,$taxQUERY);
												$taxROW = mysqli_fetch_assoc($taxRESULT);
																		
												$prime_tax = ($payrate * ($taxROW['charge_pct'] / 100));

												/////// For w2,1099 Without Benefit Tax='11-2' START ///////

												if($est_id != 3 || $est_id != 6){
													if($benefit == "Without Benefits" || $benefit == "Not Applicable"){
														if($est_id == 1 || $est_id == 4){
															$noBenifitPercentage = '11';
														}
														if($est_id == 5 || $est_id == 2){
															 $noBenifitPercentage='2';
														}
														if($est_id == 3 || $est_id == 6){
															 $noBenifitPercentage = '0';
														}
														$tax[] = ($noBenifitPercentage / 100) * $payrate;
													}
													if($benefit == "With Benefits"){
														$tax[] = $payrate * ($taxROW['charge_pct'] / 100);
													}
													$tax_r = round(array_sum($tax), 2);
												}else{
													$tax[] = 0;
													$tax_r = array_sum($tax);
												}

												/////// For w2,1099 Without Benefit Tax='11-2' END ///////
											}

											if(($tax_r > 0) && ($replace != '') && ($replace != ' ') && ($est_id ==1 || $est_id == 4)){
												$tax_r = round(($tax_r + ($payrate * 0.11)), 2);
											}
											if(($tax_r > 0) && ($replace != '') && ($replace != ' ') && ($est_id ==5 || $est_id == 2)){
												$tax_r = round(($tax_r + ($payrate * 0.02)), 2);
											}

											/////// TAX END ///////

											/////// MSP Fees START ///////

											$mspQUERY = "SELECT mspChrg_pct,primechrg_pct,primeChrg_dlr,mspChrg_dlr FROM client_fees WHERE client_id=$clid";
											$mspRESULT = mysqli_query($vtechMappingdbConn, $mspQUERY);
											$mspROW = mysqli_fetch_assoc($mspRESULT);

											$mspfee = round((($mspROW['mspChrg_pct'] / 100) * $billrate), 2) + $mspROW['mspChrg_dlr'];

											/////// MSP Fees END ///////

											/////// Prime Vendor Fees START ///////

											$vendorQUERY = "SELECT c_primeCharge_pct,c_primeCharge_dlr,c_anyCharge_dlr FROM candidate_fees WHERE emp_id='$emp_eid'";
											$vendorResult = mysqli_query($vtechMappingdbConn,$vendorQUERY);
											$vendorRow = mysqli_fetch_assoc($vendorResult);

											$prime_charge = round(((($mspROW['primechrg_pct'] / 100) * $billrate) + (($vendorRow['c_primeCharge_pct'] / 100) * $billrate) + $vendorRow['c_primeCharge_dlr'] + $mspROW['primeChrg_dlr']), 2);

											/////// Prime Vendor Fees END ///////

											/////// Rate For Candidate START ///////

											$rate_can = round(($payrate + $tax_r + $mspfee + $prime_charge), 2);

											/////// Rate For Candidate END ///////

											/////// Margin START ///////

											$g_margin = round(($billrate-$rate_can), 2);

											/////// Margin END ///////

											/////// Eligibility ///////

											$cur_date = strtotime(date("Y-m-d"));
											$third_mon_date = strtotime($mainROW['joindateX'].' 3 month');
											$termi_date_chk = strtotime($mainROW['termi_dateX']);
											if($mainROW['hrm_status'] == 'Active'){
												if($cur_date > $third_mon_date){
													$eligibility = "Yes";
												}else{
													$eligibility = "No";
												}
											}else{
												if($termi_date_chk>$third_mon_date){
													$eligibility = "Yes";
												}else{
													$eligibility = "No";
												}
											}

											if($mainROW['cats_status'] == '800'){
												$commentX = 'Placement';
											}else{
												$commentX = 'Extension';
											}
											
											if($mainROW['client_manager'] == $managerNM && $eligibility == 'Yes' && $commentX == 'Placement'){
												$tragetPlacement++;

												$targetIncQUERY = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Placement' AND '$tpVAL' BETWEEN min_margin AND max_margin");
												$targetIncROW = mysqli_fetch_array($targetIncQUERY);
												$targetIncentive[] = $targetIncROW['value'];
												$tpVAL++;

												if($g_margin >= $minMarginVAL){
													$addIncentive = '0';
													$addIncentiveQUERY = "SELECT * FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Additional Incentive'";
													$addIncentiveRESULT = mysqli_query($misReportsConn, $addIncentiveQUERY);
													while($addIncentiveROW = mysqli_fetch_array($addIncentiveRESULT)){
														if($g_margin > $addIncentiveROW['min_margin'] && $g_margin <= $addIncentiveROW['max_margin']){
															$addIncentive = $addIncentiveROW['value'];
														}
														if($g_margin > $addIncentiveROW['min_margin'] && $addIncentiveROW['max_margin'] == '0'){
															$addIncentive = $addIncentiveROW['value'];
														}
													}
													$iamount[] = $addIncentive;
												}elseif($g_margin < $minMarginVAL){
													$iamountQUERY2 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Old MIN Margin Incentive'");
													$iamountROW2 = mysqli_fetch_array($iamountQUERY2);
													$iamount[] = $iamountROW2['value'];
												}
											}elseif($eligibility == 'Yes' && $commentX == 'Placement'){
												$iamountQUERY3 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Others Account'");
												$iamountROW3 = mysqli_fetch_array($iamountQUERY3);
												$iamount[] = $iamountROW3['value'];
											}
										}
									}

									$targetIncentiveX = array_sum($targetIncentive);
									
									$iamountX = array_sum($iamount);
									
									$finalIncentive = ($iamountX + $targetIncentiveX);
									$selectQRY="SELECT * FROM incentive_data WHERE person_id = '$managerdataX' AND period = '$dtX' AND type = 'Old CSM'";
									$selectRES=mysqli_query($misReportsConn, $selectQRY);
									if(mysqli_num_rows($selectRES)>0){
										while($selectROW=mysqli_fetch_array($selectRES)){
											if($responseType == 0){
							?>
							<tr style="background-color: #c3dcf4;font-size: 13px;">
								<td style="text-align: center;vertical-align: middle;"><i class="fa fa-lock" style="font-size: 18px;color: #2266AA;"></i></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($selectROW['person_name']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['total_candidate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['incentive_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['additional_incentive']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['final_incentive']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php if($selectROW['adjustment_method']=='plus'){ ?>
										+
									<?php }else{ ?>
										-
									<?php } ?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['adjustment_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['adjustment_comment']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['final_amount']; ?></td>
							</tr>
							<?php
											}
										}
									}else{
										if($responseType == 0){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: center;vertical-align: middle;"><input style="height:18px;width:18px;cursor:pointer;outline: none;" type="checkbox" class="checkboxes" name="checked_id[<?php echo $iii; ?>]" id="checked_id" value="<?php echo $iii; ?>"></td>

								<input type="hidden" id="mainamount<?php echo $iii; ?>" value="<?php echo $finalIncentive; ?>">

								<input type="hidden" name="person_id[<?php echo $iii; ?>]" value="<?php echo $managerdataX; ?>">
								<input type="hidden" name="person_name[<?php echo $iii; ?>]" value="<?php echo ucwords($managerNM); ?>">
								<input type="hidden" name="type_data[<?php echo $iii; ?>]" value="<?php echo 'Old CSM'; ?>">
								<input type="hidden" name="period[<?php echo $iii; ?>]" value="<?php echo $dtX; ?>">
								<input type="hidden" name="total_candidate[<?php echo $iii; ?>]" value="<?php echo $tragetPlacement; ?>">
								<input type="hidden" name="incentive_amount[<?php echo $iii; ?>]" value="<?php echo $targetIncentive; ?>">
								<input type="hidden" name="additional_incentive[<?php echo $iii; ?>]" value="<?php echo $iamountX; ?>">
								<input type="hidden" name="final_incentive[<?php echo $iii; ?>]" value="<?php echo $finalIncentive; ?>">
								<input type="hidden" name="detail_link[<?php echo $iii; ?>]" value="<?php echo LOCAL_REPORT_PATH; ?>/incentive/old_csm_incentive_report/index.php?multimonth=<?php echo urlencode($dtX); ?>&manager_id=<?php echo urlencode($managerdataX); ?>&OCSMsubmitX=&response_type=1">

								<td style="text-align: left;vertical-align: middle;"><a href="?multimonth=<?php echo $dtX; ?>&manager_id=<?php echo $managerdataX; ?>&OCSMsubmitX="><?php echo ucwords($managerNM); ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $tragetPlacement; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php if($targetIncentive == ''){ echo "0"; }else{ echo $targetIncentiveX; } ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $iamountX; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $finalIncentive; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<select id="adjustment_method<?php echo $iii; ?>" name="adjustment_method[<?php echo $iii; ?>]" onchange="adjustMETHOD<?php echo $iii; ?>(this.value)" style="padding: 5px;cursor: pointer;" required>
										<option value="plus">ADD (+)</option>
										<option value="minus">SUB (-)</option>
									</select>
								</td>
								<td style="text-align: center;vertical-align: middle;">
									<input type="text" id="adjustment_amount<?php echo $iii; ?>" name="adjustment_amount[<?php echo $iii; ?>]" onchange="adjustAMT<?php echo $iii; ?>(this.value)" maxlength="10" onkeypress='return event.charCode >= 46 && event.charCode <= 57' placeholder="0" style="width: 70%;padding: 2px 5px;">
								</td>
								<td style="text-align: center;vertical-align: middle;">
									<textarea name="adjustment_comment[<?php echo $iii; ?>]" rows="1" autocomplete="off" style="width: 90%;padding: 3px 7px;"></textarea>
								</td>
								<td style="text-align: center;vertical-align: middle;font-weight: bold;">
									<input type="text" id="final_amount<?php echo $iii; ?>" name="final_amount[<?php echo $iii; ?>]" style="background-color: #fff;color: #000;width: 70%;padding: 2px 5px;border: none;text-align: center;" value="<?php echo $finalIncentive; ?>" readonly>
								</td>
							</tr>
							<script>
								/*Adjustment Calculation START*/
								function adjustAMT<?php echo $iii; ?>(adjstvalue){
									var val1=$('#adjustment_method<?php echo $iii; ?>').val();
									var val2=$('#mainamount<?php echo $iii; ?>').val();
									var val3=adjstvalue;
									$.ajax({
										url:'add_sub.php',
										type:'POST',
										data: {method: val1, mainamount: val2, adjstamount: val3},
										success:function(opt){
											$('#final_amount<?php echo $iii; ?>').val(opt);
										}
									});
								}
								function adjustMETHOD<?php echo $iii; ?>(adjstvalue){
									var val1=$('#adjustment_amount<?php echo $iii; ?>').val();
									var val2=$('#mainamount<?php echo $iii; ?>').val();
									var val3=adjstvalue;
									$.ajax({
										url:'add_sub_method.php',
										type:'POST',
										data: {method: val3, mainamount: val2, adjstamount: val1},
										success:function(opt){
											$('#final_amount<?php echo $iii; ?>').val(opt);
										}
									});
								}
								/*Adjustment Calculation END*/
							</script>
							<?php
										}
									}
									$iii++;
								}
								if($responseType == 0){
							?>
							</tbody>
							<tfoot class="overviewtfoot">
								<th colspan="10" style="background-color: #bbb;text-align: right;vertical-align: middle;"><button type="submit" class="btn btn-primary" style="border-radius: 0px;background-color: #2266AA;"><i class="fa fa-lock"></i> Lock the Amount</button></th>
							</tfoot>
						</table>
					</div>
				</div>
			</form>
		</div>
	</section>
	<script>
		/*CSM Incentive Form Submission START*/
		$('#CSMincentive').submit(function(e){
			if($('.checkboxes:checked').length == 0){
				alert("Please select atleast one checkbox!");
				return false;
			}
			if($('.checkboxes:checked').length > 0){
				e.preventDefault();
				$("#LoadingImage").show();
				$.ajax({
					url: 'lockamount.php',
					type: 'POST',
					data: $('#CSMincentive').serialize(),
					success: function(opt){
						$("#LoadingImage").hide();
						location.reload();
	            		alert('Incentive Successfully Locked!');
						//console.log(opt);
		            }
				});
				return true;
			}
		});
		/*CSM Incentive Form Submission END*/
	</script>
<?php
		}
	}
	//POST Overview Section END

	//POST Detail Section START
	if(isset($_REQUEST['OCSMsubmitX'])){
		$monthsdata = $_REQUEST['multimonth'];
		$managerdata = $_REQUEST['manager_id'];
		if($responseType == 0){
?>
	<section id="CSMIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row">
				<?php
					}
					$managerQUERY = mysqli_query($catsConn, "SELECT concat(first_name,' ',last_name) AS man_name FROM user WHERE user_id = '$managerdata'");
					$managerROW = mysqli_fetch_array($managerQUERY);
					$managerNM = $managerROW['man_name'];
					if($responseType == 0){
				?>
				<div class="col-md-4 col-md-offset-4" style="background-color: #ccc;color: #000;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">CS Manager : <span style="font-size: 16px;color: #333;"><?php echo $managerNM; ?></span></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="CSMIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">Recruiter</th>
								<th style="text-align: center;vertical-align: middle;">Recruiter Manager</th>
								<th style="text-align: center;vertical-align: middle;">Candidate</th>
								<th style="text-align: center;vertical-align: middle;">Client</th>
								<th style="text-align: center;vertical-align: middle;">Client Manager</th>
								<th style="text-align: center;vertical-align: middle;">CATS Date</th>
								<th style="text-align: center;vertical-align: middle;">CATS Status</th>
								<th style="text-align: center;vertical-align: middle;">HRM Date</th>
								<th style="text-align: center;vertical-align: middle;">HRM Status</th>
								<th style="text-align: center;vertical-align: middle;">Termination Date</th>
								<th style="text-align: center;vertical-align: middle;">3 Months<br>Completed</th>
								<th style="text-align: center;vertical-align: middle;">Margin</th>
								<th style="text-align: center;vertical-align: middle;">Target<br>Placement<br>Incentive</th>
								<th style="text-align: center;vertical-align: middle;">Additional<br>Incentive<br>Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
								}
								$monthsdata = str_replace("-", "/", $monthsdata);
	
								$dateGiven = explode("/", $monthsdata);
								$dateModified = $dateGiven[1]."-".$dateGiven[0];

								$fromDate = date('Y-m-01', strtotime($dateModified));
								$toDate = date('Y-m-t', strtotime($dateModified));

								/////// Target Placement Count Till $fromDate START ///////
								
								$selectedLastMonth = $dateGiven[0] - 1;

								$lastMonthLastDateX = "last day of ".date("M", mktime(0, 0, 0, $selectedLastMonth, 10));
								$firstMonthFirstDate = date('Y-m-d', strtotime('first day of january'));
								if ($selectedLastMonth != '0'){
									$lastMonthLastDate = date('Y-m-d', strtotime($lastMonthLastDateX));
								
									$tpQRY = mysqli_query($catsConn, "SELECT
									    emp.id AS eid,
									    concat(emp.first_name,' ',emp.last_name) AS ename,
										date_format(emp.custom7, '%Y-%m-%d') AS joindateX,
										emp.status AS hrm_status,
										date_format(emp.termination_date, '%Y-%m-%d') AS termi_dateX
									FROM
										candidate_joborder_status_history AS cjsh
										JOIN candidate AS can ON can.candidate_id = cjsh.candidate_id
										JOIN candidate_joborder AS cj ON cj.candidate_id = cjsh.candidate_id AND cj.joborder_id = cjsh.joborder_id
										JOIN joborder AS job ON job.joborder_id = cjsh.joborder_id
										JOIN company AS comp ON comp.company_id = job.company_id
									    JOIN vtech_mappingdb.system_integration AS mp ON mp.c_candidate_id = cjsh.candidate_id
									    JOIN vtechhrm.employees AS emp ON emp.id = mp.h_employee_id
									WHERE
										cjsh.status_to = '800'
									AND
										comp.owner = '$managerdata'
									AND
									    date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$firstMonthFirstDate' AND '$lastMonthLastDate'
									GROUP BY cj.candidate_id");
									if (mysqli_num_rows($tpQRY) > 0) {
										while ($tpROW = mysqli_fetch_array($tpQRY)) {
											$cur_dateX = strtotime(date("Y-m-d"));
											$third_mon_dateX = strtotime($tpROW['joindateX'].' 3 month');
											$termi_date_chkX = strtotime($tpROW['termi_dateX']);
											if($tpROW['hrm_status'] == 'Active'){
												if($cur_dateX > $third_mon_dateX){
													$eligibilityX[] = "Yes";
												}
											}else{
												if($termi_date_chkX > $third_mon_dateX){
													$eligibilityX[] = "Yes";
												}
											}
										}
									}
									$tpVALX =  sizeof($eligibilityX);
									$tpVAL =  sizeof($eligibilityX) + 1;
								}else{
									$lastMonthLastDate = "Now";
									$tpVALX = 0;
									$tpVAL = 1;
								}
								/////// Target Placement Count Till $fromDate END ///////

								$minMarginQRY = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Minimum Margin'");
								$minMarginROW = mysqli_fetch_array($minMarginQRY);
								$minMarginVAL = $minMarginROW['value'];

								$recruiterQUERY = mysqli_query($catsConn, "SELECT user_id FROM user WHERE notes = '$managerNM'");
								while($recruiterROW = mysqli_fetch_array($recruiterQUERY)){
									$recruiterdata[] = $recruiterROW['user_id'];
								}
								$recruiterdataX = implode(",", $recruiterdata);

								$mainQUERY = "SELECT
									cjsh.candidate_id AS canid,
									concat(can.first_name,' ',can.last_name) AS canname,
									comp.company_id AS cid,
									comp.name AS cname,
									concat(u.first_name,' ',u.last_name) AS client_manager,
								    (SELECT concat(first_name,' ',last_name) AS rname FROM user WHERE user_id = cj.added_by) AS recruiter,
									(SELECT notes FROM user WHERE user_id = cj.added_by) AS recruiter_manager,
								    date_format(cjsh.date, '%m-%d-%Y') AS cats_date,
								    cjsh.status_to AS cats_status,
								    (SELECT mc.designation FROM user JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = user.user_id WHERE user.user_id = cj.added_by) AS designation,
								    emp.id AS eid,
									emp.status AS hrm_status,
								    concat(emp.first_name,' ',emp.last_name) AS ename,
								    date_format(emp.custom7, '%m-%d-%Y') AS joindate,
									date_format(emp.custom7, '%Y-%m-%d') AS joindateX,
								    date_format(emp.termination_date, '%m-%d-%Y') AS termi_date,
									date_format(emp.termination_date, '%Y-%m-%d') AS termi_dateX,
									emp.custom1 AS benefit,
									emp.custom2 AS benefitlist,
									CAST(replace(emp.custom3,'$','') AS DECIMAL (10,2)) AS billrate,
									CAST(replace(emp.custom4,'$','') AS DECIMAL (10,2)) AS payrate,
									es.id AS es_id,
									es.name AS employment_type
								FROM
									candidate_joborder_status_history AS cjsh
									JOIN candidate AS can ON can.candidate_id = cjsh.candidate_id
									JOIN candidate_joborder AS cj ON cj.candidate_id = cjsh.candidate_id AND cj.joborder_id = cjsh.joborder_id
									JOIN joborder AS job ON job.joborder_id = cjsh.joborder_id
									JOIN company AS comp ON comp.company_id = job.company_id
									JOIN user AS u ON u.user_id = comp.owner
								    JOIN vtech_mappingdb.system_integration AS mp ON mp.c_candidate_id = cjsh.candidate_id
								    JOIN vtechhrm.employees AS emp ON emp.id = mp.h_employee_id
								    JOIN vtechhrm.employmentstatus AS es ON es.id = emp.employment_status
								WHERE
									(cjsh.status_to = '800' OR cjsh.status_to = '620')
								AND
									(comp.owner = '$managerdata' OR cj.added_by IN ({$recruiterdataX}))
								AND
								    date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
								GROUP BY cj.candidate_id";
								$mainRESULT = mysqli_query($catsConn, $mainQUERY);

								$tax_r=$mspfee=$prime_charge=$rate_can=$g_margin=$tragetPlacement=$iamountX=$targetIncentiveX=$finalIncentive=0;
								$iamount=$targetIncentive=array();

								if(mysqli_num_rows($mainRESULT) > 0){
									while($mainROW = mysqli_fetch_array($mainRESULT)){
										$emp_eid = $mainROW['eid'];
										$clid = $mainROW['cid'];
										$billrate = $mainROW['billrate'];
										$payrate = $mainROW['payrate'];
										$est_id = $mainROW['es_id'];
										$benefit = $mainROW['benefit'];

										$delimiter = array("","[","]",'"');
										$replace = str_replace($delimiter, $delimiter[0], $mainROW['benefitlist']);
										$benefitlist = $replace;

										$tax = array();

										/////// TAX START ///////

										$benefitLists = explode(",",$benefitlist);
										foreach($benefitLists AS $value22){
											$taxQUERY = "SELECT charge_pct,benefits FROM tax_settings WHERE empst_id=$est_id AND benefits LIKE '%$value22%'";
											$taxRESULT = mysqli_query($vtechMappingdbConn,$taxQUERY);
											$taxROW = mysqli_fetch_assoc($taxRESULT);
																	
											$prime_tax = ($payrate * ($taxROW['charge_pct'] / 100));

											/////// For w2,1099 Without Benefit Tax='11-2' START ///////

											if($est_id != 3 || $est_id != 6){
												if($benefit == "Without Benefits" || $benefit == "Not Applicable"){
													if($est_id == 1 || $est_id == 4){
														$noBenifitPercentage = '11';
													}
													if($est_id == 5 || $est_id == 2){
														 $noBenifitPercentage='2';
													}
													if($est_id == 3 || $est_id == 6){
														 $noBenifitPercentage = '0';
													}
													$tax[] = ($noBenifitPercentage / 100) * $payrate;
												}
												if($benefit == "With Benefits"){
													$tax[] = $payrate * ($taxROW['charge_pct'] / 100);
												}
												$tax_r = round(array_sum($tax), 2);
											}else{
												$tax[] = 0;
												$tax_r = array_sum($tax);
											}

											/////// For w2,1099 Without Benefit Tax='11-2' END ///////
										}

										if(($tax_r > 0) && ($replace != '') && ($replace != ' ') && ($est_id ==1 || $est_id == 4)){
											$tax_r = round(($tax_r + ($payrate * 0.11)), 2);
										}
										if(($tax_r > 0) && ($replace != '') && ($replace != ' ') && ($est_id ==5 || $est_id == 2)){
											$tax_r = round(($tax_r + ($payrate * 0.02)), 2);
										}

										/////// TAX END ///////

										/////// MSP Fees START ///////

										$mspQUERY = "SELECT mspChrg_pct,primechrg_pct,primeChrg_dlr,mspChrg_dlr FROM client_fees WHERE client_id=$clid";
										$mspRESULT = mysqli_query($vtechMappingdbConn, $mspQUERY);
										$mspROW = mysqli_fetch_assoc($mspRESULT);

										$mspfee = round((($mspROW['mspChrg_pct'] / 100) * $billrate), 2) + $mspROW['mspChrg_dlr'];

										/////// MSP Fees END ///////

										/////// Prime Vendor Fees START ///////

										$vendorQUERY = "SELECT c_primeCharge_pct,c_primeCharge_dlr,c_anyCharge_dlr FROM candidate_fees WHERE emp_id='$emp_eid'";
										$vendorResult = mysqli_query($vtechMappingdbConn,$vendorQUERY);
										$vendorRow = mysqli_fetch_assoc($vendorResult);

										$prime_charge = round(((($mspROW['primechrg_pct'] / 100) * $billrate) + (($vendorRow['c_primeCharge_pct'] / 100) * $billrate) + $vendorRow['c_primeCharge_dlr'] + $mspROW['primeChrg_dlr']), 2);

										/////// Prime Vendor Fees END ///////

										/////// Rate For Candidate START ///////

										$rate_can = round(($payrate + $tax_r + $mspfee + $prime_charge), 2);

										/////// Rate For Candidate END ///////

										/////// Margin START ///////

										$g_margin = round(($billrate-$rate_can), 2);

										/////// Margin END ///////

										/////// Eligibility ///////

										$cur_date = strtotime(date("Y-m-d"));
										$third_mon_date = strtotime($mainROW['joindateX'].' 3 month');
										$termi_date_chk = strtotime($mainROW['termi_dateX']);
										if($mainROW['hrm_status'] == 'Active'){
											if($cur_date > $third_mon_date){
												$eligibility = "Yes";
											}else{
												$eligibility = "No";
											}
										}else{
											if($termi_date_chk>$third_mon_date){
												$eligibility = "Yes";
											}else{
												$eligibility = "No";
											}
										}

										if($mainROW['cats_status'] == '800'){
											$commentX = 'Placement';
										}else{
											$commentX = 'Extension';
										}
										if($responseType == 0){
							?>
							<tr style="font-size: 13px;color: #000;">
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW['recruiter']); ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW['recruiter_manager']); ?></td>
							<?php if($mainROW['client_manager'] == $managerNM && $eligibility == 'Yes' && $commentX == 'Placement'){ ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW['ename']); ?> <span class="badge" style="background-color: #449D44;"><?php echo $tpVAL; ?></span></td>
							<?php }else{ ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW['ename']); ?></td>
							<?php } ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo $mainROW['cname']; ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW['client_manager']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['cats_date']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $commentX; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['joindate']; ?></td>
							<?php if($mainROW['hrm_status'] == 'Active'){ ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['hrm_status']; ?></td>
								<td style="text-align: center;vertical-align: middle;">---</td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $mainROW['hrm_status']; ?></td>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $mainROW['termi_date']; ?></td>
							<?php } ?>
							<?php if($eligibility == 'Yes'){ ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $eligibility; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $eligibility; ?></td>
							<?php } ?>
							<?php if($g_margin >= $minMarginVAL){ ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $g_margin; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $g_margin; ?></td>
							<?php } ?>
							<?php if($mainROW['client_manager'] == $managerNM && $eligibility == 'Yes' && $commentX == 'Placement'){ ?>
								<td style="text-align: center;vertical-align: middle;">
									<?php
										$targetIncQUERY = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Placement' AND '$tpVAL' BETWEEN min_margin AND max_margin");
										$targetIncROW = mysqli_fetch_array($targetIncQUERY);
										echo $targetIncentive[] = $targetIncROW['value'];
										$tpVAL++;
									?>
								</td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;">0</td>
							<?php } ?>
							<?php
								if($mainROW['client_manager'] == $managerNM && $eligibility == 'Yes' && $commentX == 'Placement'){
									$tragetPlacement++;
									if($g_margin >= $minMarginVAL){
										$addIncentive = '0';
										$addIncentiveQUERY = "SELECT * FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Additional Incentive'";
										$addIncentiveRESULT = mysqli_query($misReportsConn, $addIncentiveQUERY);
										while($addIncentiveROW = mysqli_fetch_array($addIncentiveRESULT)){
											if($g_margin > $addIncentiveROW['min_margin'] && $g_margin <= $addIncentiveROW['max_margin']){
												$addIncentive = $addIncentiveROW['value'];
											}
											if($g_margin > $addIncentiveROW['min_margin'] && $addIncentiveROW['max_margin'] == '0'){
												$addIncentive = $addIncentiveROW['value'];
											}
										}
										$iamount[] = $addIncentive;
							?>
								<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $addIncentive; ?></td>
							<?php
									}elseif($g_margin < $minMarginVAL){
									$iamountQUERY2 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Old MIN Margin Incentive'");
									$iamountROW2 = mysqli_fetch_array($iamountQUERY2);
									$iamount[] = $iamountROW2['value'];
							?>
								<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $iamountROW2['value']; ?></td>
							<?php
									}else{
							?>
								<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;">0</td>
							<?php
									}
								}elseif($eligibility == 'Yes' && $commentX == 'Placement'){
									$iamountQUERY3 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Others Account'");
									$iamountROW3 = mysqli_fetch_array($iamountQUERY3);
									$iamount[] = $iamountROW3['value'];
							?>
								<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $iamountROW3['value']; ?></td>
							<?php
								}else{
							?>
								<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;">0</td>
							<?php
								}
							?>
							</tr>
							<?php
										}else{
											if($mainROW['hrm_status'] == 'Active'){
												$hrm_termi_date = "---";
											}else{
												$hrm_termi_date = $mainROW['termi_date'];
											}

											$additional_incentiveX = '0';
											if($mainROW['client_manager'] == $managerNM && $eligibility == 'Yes' && $commentX == 'Placement'){
												if($g_margin >= $minMarginVAL){
													$addIncentive = '0';
													$addIncentiveQUERY = "SELECT * FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Additional Incentive'";
													$addIncentiveRESULT = mysqli_query($misReportsConn, $addIncentiveQUERY);
													while($addIncentiveROW = mysqli_fetch_array($addIncentiveRESULT)){
														if($g_margin > $addIncentiveROW['min_margin'] && $g_margin <= $addIncentiveROW['max_margin']){
															$addIncentive = $addIncentiveROW['value'];
														}
														if($g_margin > $addIncentiveROW['min_margin'] && $addIncentiveROW['max_margin'] == '0'){
															$addIncentive = $addIncentiveROW['value'];
														}
													}
													$additional_incentiveX = $addIncentive;
												}elseif($g_margin < $minMarginVAL){
													$iamountQUERY2 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Old MIN Margin Incentive'");
													$iamountROW2 = mysqli_fetch_array($iamountQUERY2);
													$additional_incentiveX = $iamountROW2['value'];
												}else{
													$additional_incentiveX = '0';
												}
											}elseif($eligibility == 'Yes' && $commentX == 'Placement'){
												$iamountQUERY3 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'CS Manager' AND comment = 'Others Account'");
												$iamountROW3 = mysqli_fetch_array($iamountQUERY3);
												$additional_incentiveX = $iamountROW3['value'];
											}else{
												$additional_incentiveX = '0';
											}

											$responseArray[] = array('recruiter' => ucwords($mainROW['recruiter']),
												'recruiter_manager' => ucwords($mainROW['recruiter_manager']),
												'candidate' => ucwords($mainROW['ename']),
												'client' => $mainROW['cname'],
												'client_manager' => ucwords($mainROW['client_manager']),
												'cats_placement_date' => $mainROW['cats_date'],
												'cats_status' => $commentX,
												'join_date' => $mainROW['joindate'],
												'hrm_status' => $mainROW['hrm_status'],
												'termi_date' => $hrm_termi_date,
												'eligibility' => $eligibility,
												'margin' => $g_margin,
												'additional_incentive' => $additional_incentiveX
											);
										}
									}
								}
								if($responseType == 0){
							?>
						</tbody>
						<tfoot>
							<tr style="background-color: #bbb;color: #000;font-size: 14px;">
								<th style="text-align: left;vertical-align: middle;" colspan="12">
									<span style="color: #2266AA;">Total No. of Target Placement (Till <?php echo $lastMonthLastDate; ?>) : </span> <?php echo $tpVALX; ?> <br>
									<span style="color: #2266AA;">Total No. of Target Placement (This Month) : </span> <?php echo $tragetPlacement; ?>
								</th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $targetIncentiveX = array_sum($targetIncentive); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $iamountX = array_sum($iamount); ?></th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 15px;">
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;" colspan="14">
								<span style="color: #2266AA;">Final Incentive : </span>
								<?php echo $targetIncentiveX." + ".$iamountX." = ".($targetIncentiveX + $iamountX); ?> 
								</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</section>
<?php
		}
	}
	//POST Detail Section END
	if($responseType == 0){
?>

</body>
</html>
<?php
			}else{
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
