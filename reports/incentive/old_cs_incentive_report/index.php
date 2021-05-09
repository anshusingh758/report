<?php
	include_once("../../../security.php");

	$responseArray = array();
	$responseType = isset($_REQUEST['response_type']) && $_REQUEST['response_type'] == 1 ? 1 : 0;

    if(isset($_SESSION['user'])){
		//error_reporting(0);
		include_once('../../../config.php');

    	$childUser = $_SESSION['userMember'];
		$reportID = '49';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult = mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult) > 0){
			if($responseType == 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Old CS Incentive Report</title>

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
			padding: 5px 3px;
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
			$("#LoadingImage").hide();
			$('#MainSection').removeClass("hidden");
			$('#RIRdatatable').removeClass("hidden");


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


			$('#recruiter_name').multiselect({
				nonSelectedText: 'Select Recruiter',
				numberDisplayed: 1,
				enableFiltering:true,
				enableCaseInsensitiveFiltering:true,
				buttonWidth:'100%',
				includeSelectAllOption: true,
 				maxHeight: 300
			});
			$("#recruiter_name").multiselect('selectAll', false);
	        $("#recruiter_name").multiselect('updateButtonText');


			var tableX = $('#RIRdataX').DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			    dom: 'Bfrtip',
			    "aaSorting": [[4,'desc']],
			    "columnDefs":[{
					"targets" : 'no-sort',
					"orderable": false,
			    }],
		        buttons:[
		            'excel','pageLength'
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

			var tableX = $('#RIRdata').DataTable({
				"paging": false,
			    dom: 'Bfrtip',
			    "aaSorting": [[0,'asc']],
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
			/*Datatable Calling END*/

			var tableX = $('#RIRdata2').DataTable({
				"paging": false,
			    dom: 'Bfrtip',
			    "aaSorting": [[0,'asc']],
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
			/*Datatable Calling END*/

		});


		function mnname(){
			var val=$('#manager_name').serialize();
			$.ajax({
				url:'manage_recruiter.php',
				type:'POST',
				data:val,
				success:function(output){
					$('#recruiter_name').html(output);
					$('#recruiter_name').multiselect("destroy");
					$('#recruiter_name').multiselect({
						nonSelectedText: 'Select Recruiter',
						numberDisplayed: 1,
						enableFiltering:true,
						enableCaseInsensitiveFiltering:true,
						buttonWidth:'100%',
						includeSelectAllOption: true,
		 				maxHeight: 300
					});
					$("#recruiter_name").multiselect('selectAll', false);
					$("#recruiter_name").multiselect('updateButtonText');
				}
			});
		}
		function goBack(){
			window.history.back();
		}
	</script>

</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Old CS Incentive Report</div>
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
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-2 col-md-offset-4">
						<label>Select CS Manager :</label>
						<select id="manager_name" name="manager_name[]" onchange="mnname()" multiple required>
							<?php
								$mannm = array();
								$sqluser = "SELECT
											extra_field.value AS manager_name
										FROM
											extra_field
											LEFT JOIN user ON CONCAT(user.first_name,' ',user.last_name) = extra_field.value
										WHERE
											extra_field.field_name = 'Manager - Client Service'
										AND
											user.access_level != '0'
										AND
											extra_field.value != 'Sahil Khan'
										AND
											extra_field.value != 'Ashutosh Upadhyay'
										GROUP BY manager_name";
								$resultuser = mysqli_query($catsConn, $sqluser);
								while($userlist = mysqli_fetch_array($resultuser)){
									$mannm[] = $userlist['manager_name'];
									echo "<option value='".$userlist['manager_name']."'>".ucwords($userlist['manager_name'])."</option>";
								}
							?>
						</select>
					</div>
					<div class="col-md-2">
						<label>Select Recruiter :</label>
						<select id="recruiter_name" name="recruiter_name[]" multiple required>
							<?php
								foreach($mannm AS $mannm2){
									$sqluserX = "SELECT
													user_id AS user_id,
													concat(first_name,' ',last_name) AS recnm
												FROM
													user
												WHERE
													access_level!='0'
												AND
													notes = '$mannm2'
												GROUP BY recnm
												ORDER BY recnm ASC";
									$resultuserX = mysqli_query($catsConn,$sqluserX);
									while($userlistX = mysqli_fetch_array($resultuserX)){
										echo "<option value=".$userlistX['user_id'].">".$userlistX['recnm']."</option>";
									}
								}
							?>
						</select>
					</div>
					<div class="col-md-2 col-md-offset-4" style="margin-top: 35px;">
						<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
					</div>
					<div class="col-md-2" style="margin-top: 35px;">
						<button type="submit" class="form-control" name="RIRsubmit1" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
	</section>

<!--POST multimonth Section-->
<?php
	}
	//POST Overview Section START
	if(isset($_REQUEST['RIRsubmit1'])){
		$monthsdata = $_REQUEST['multimonth'];
		$recruiterdata = $_REQUEST['recruiter_name'];
		if($responseType == 0){
?>
	<section id="RIRdatatable" class="hidden">
		<div class="container-fluid">
			<form id="OCSincentive" onsubmit="return true">
				<div class="row" style="margin-bottom: 50px;">
					<div class="col-md-10 col-md-offset-1">
						<table id="RIRdataX" class="table table-striped table-bordered">
							<thead>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th class='no-sort' style="text-align: center;vertical-align: middle;" rowspan="2"><i class=" fa fa-check-square-o" style="font-size: 20px;"></i></th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Recruiter</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Designation</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Recruiter Manager</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Incentive Amount</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Amount</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th class="no-sort" style="text-align: center;vertical-align: middle;">Method</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;">Amount</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Comment</th>
								</tr>
							</thead>
							<tbody>
								<?php
									}
									$monthsdata=str_replace("-", "/", $monthsdata);

									$dateGiven = explode("/", $monthsdata);

									$dateModified = $dateGiven[1]."-".$dateGiven[0];

									$dtX = $dateGiven[0]."-".$dateGiven[1];
									
									$fromDate = date('Y-m-01', strtotime($dateModified));
									$toDate = date('Y-m-t', strtotime($dateModified));

									$minMarginQRY = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Minimum Margin'");
									$minMarginROW = mysqli_fetch_array($minMarginQRY);
									$minMarginVAL = $minMarginROW['value'];

									$iii = '0';
									foreach($recruiterdata AS $recruiterdataX){
	
										$finalIncentive=$iamount2=$iamount3="0";
	
										$rdataQ = "SELECT concat(u.first_name,' ',u.last_name) AS rname, u.email, u.notes, (SELECT user_id FROM user WHERE concat(first_name,' ',last_name) = u.notes) AS notes_id, mc.designation FROM user AS u JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = u.user_id WHERE u.user_id='$recruiterdataX'";
										$rdataR = mysqli_query($catsConn, $rdataQ);
										$rdataD = mysqli_fetch_array($rdataR);

										$mainQUERY = "SELECT
											cjsh.candidate_id AS canid,
											concat(can.first_name,' ',can.last_name) AS canname,
											DATE_FORMAT(cjsh.date, '%m-%d-%Y') AS cats_placement_date,
											comp.company_id AS cid,
											comp.name AS cname,
											(SELECT concat(first_name,' ',last_name) AS cmname FROM user WHERE user_id=comp.owner) AS client_manager,
										    cjsh.status_to AS cats_status,
											concat(u.first_name,' ',u.last_name) AS recruiter,
										    u.notes AS recruiter_manager,
										    (SELECT mc.designation FROM user JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = user.user_id WHERE user.user_id=cj.added_by) AS designation,
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
											JOIN user AS u ON u.user_id = cj.added_by
										    JOIN vtech_mappingdb.system_integration AS mp ON mp.c_candidate_id = cjsh.candidate_id
										    JOIN vtechhrm.employees AS emp ON emp.id = mp.h_employee_id
										    JOIN vtechhrm.employmentstatus AS es ON es.id = emp.employment_status
										WHERE
											(cjsh.status_to = '800' OR cjsh.status_to = '620')
										AND
											cj.added_by = '$recruiterdataX'
										AND
											date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
										GROUP BY cjsh.candidate_id";
										$mainRESULT = mysqli_query($catsConn, $mainQUERY);

										$tax_r=$mspfee=$prime_charge=$rate_can=$g_margin=0;
										$enameX=$iamount=array();

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

												if($eligibility == 'Yes' && $g_margin >= $minMarginVAL){
													$enameX[] = $mainROW['ename'];
													$iamountQUERY = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = '$commentX'");
													$iamountROW = mysqli_fetch_array($iamountQUERY);
													$iamount[] = $iamountROW['value'];
												}elseif($eligibility == 'Yes' && $g_margin < $minMarginVAL && $commentX == 'Placement'){
													$iamountQUERY2 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Old MIN Margin Incentive'");
													$iamountROW2 = mysqli_fetch_array($iamountQUERY2);
													$iamount[] = $iamountROW2['value'];
												}else{
												}
											}
										}

										$iamount2 = array_sum($iamount);

										if($rdataD['designation'] == 'Lead Recruiter' || $rdataD['designation'] == 'Senior Executive - Client Services'){
											$recruiterID = array();
											$managernm = $rdataD['notes'];
											$managerID = $rdataD['notes_id'];
											$recruiterEmail = $rdataD['email'];
											// AND mc.designation != 'Lead Recruiter' AND mc.designation != 'Senior Executive - Client Services'
											$findTeamQUERY = "SELECT u.user_id, mc.designation FROM user AS u JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = u.user_id WHERE notes = '$managernm'";
											$findTeamRESULT = mysqli_query($catsConn, $findTeamQUERY);
											while($findTeamROW = mysqli_fetch_array($findTeamRESULT)){
												$recruiterID[] = $findTeamROW['user_id'];
											}
											$recruiterIDX = implode(",", $recruiterID);
																	
											$mainQUERY2 = "SELECT
												cjsh.candidate_id AS canid,
												concat(can.first_name,' ',can.last_name) AS canname,
												comp.company_id AS cid,
												comp.name AS cname,
												(SELECT concat(first_name,' ',last_name) AS cmname FROM user WHERE user_id=comp.owner) AS client_manager,
											    cjsh.status_to AS cats_status,
												date_format(cjsh.date, '%m-%d-%Y') AS cats_date,
												cjsh.date AS cats_dateX,
												concat(u.first_name,' ',u.last_name) AS recruiter,
											    u.notes AS recruiter_manager,
												(SELECT mc.designation FROM user JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = user.user_id WHERE user.user_id=cj.added_by) AS designation,
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
												JOIN user AS u ON u.user_id = cj.added_by
											    JOIN vtech_mappingdb.system_integration AS mp ON mp.c_candidate_id = cjsh.candidate_id
											    JOIN vtechhrm.employees AS emp ON emp.id = mp.h_employee_id
											    JOIN vtechhrm.employmentstatus AS es ON es.id = emp.employment_status
											WHERE
												(cjsh.status_to = '800' OR cjsh.status_to = '620')
											AND
												(cj.added_by = '$managerID' OR cj.added_by IN ({$recruiterIDX}))
											AND
												cj.added_by != '$recruiterdataX'
											AND
												date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
											GROUP BY cjsh.candidate_id";
											$mainRESULT2 = mysqli_query($catsConn, $mainQUERY2);

											$tax_r=$mspfee=$prime_charge=$rate_can=$g_margin=0;
											$enameX=$iamount=array();

											if(mysqli_num_rows($mainRESULT2) > 0){
												while($mainROW2 = mysqli_fetch_array($mainRESULT2)){
													$emp_eid = $mainROW2['eid'];
													$clid = $mainROW2['cid'];
													$billrate = $mainROW2['billrate'];
													$payrate = $mainROW2['payrate'];
													$est_id = $mainROW2['es_id'];
													$benefit = $mainROW2['benefit'];

													$delimiter = array("","[","]",'"');
													$replace = str_replace($delimiter, $delimiter[0], $mainROW2['benefitlist']);
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

													/////// Eligibility START ///////

													$cur_date = strtotime(date("Y-m-d"));
													$third_mon_date = strtotime($mainROW2['joindateX'].' 3 month');
													$termi_date_chk = strtotime($mainROW2['termi_dateX']);
													if($mainROW2['hrm_status'] == 'Active'){
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
																
													/////// Eligibility END ///////

													if($mainROW2['cats_status'] == '800'){
														$commentX = 'Placement';
													}else{
														$commentX = 'Extension';
													}

													/////// Recruiter Eligibility START ///////
													
													$findDateQUERY = "SELECT confirmation_date FROM employees WHERE work_email = '$recruiterEmail'";
													$findDateRESULT = mysqli_query($hrmIndiaConn, $findDateQUERY);
													$findDateROW = mysqli_fetch_array($findDateRESULT);

													$recruiter_eligibility=$recruiter_eligibility_date=$recruiter_symbol='';

													if($findDateROW['confirmation_date'] != '0000-00-00 00:00:00' && $findDateROW['confirmation_date'] != '0001-01-01 00:00:00'){
														$recruiter_eligibility = 'Yes';
														$recruiter_eligibility_date = $findDateROW['confirmation_date'];
													}else{
														$recruiter_eligibility = 'No';
														$recruiter_eligibility_date = '';
													}

													if($recruiter_eligibility == 'Yes'){
														$red = strtotime($recruiter_eligibility_date);
														$cd = strtotime($mainROW2['cats_dateX']);
														if($red <= $cd){
															$recruiter_symbol = 'Yes';
														}else{
															$recruiter_symbol = 'No';
														}
													}elseif($recruiter_eligibility == 'No'){
														$recruiter_symbol = 'No';
													}else{
														$recruiter_symbol = '';
													}

													/////// Recruiter Eligibility END ///////

													if($mainROW2['hrm_status'] == 'Active' && $eligibility == 'Yes' && $g_margin >= $minMarginVAL && $commentX == 'Placement' && ($recruiter_symbol == 'Yes' || $recruiter_symbol == '')){
														if($managernm == $mainROW2['client_manager']){
															$iamountQUERY3 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Sr/Lead Own Account'");
															$iamountROW3 = mysqli_fetch_array($iamountQUERY3);
															$iamount[] = $iamountROW3['value'];
														}else{
															$iamountQUERY3 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Sr/Lead Others Account'");
															$iamountROW3 = mysqli_fetch_array($iamountQUERY3);
															$iamount[] = $iamountROW3['value'];
														}
													}
												}
											}
											$iamount3 = array_sum($iamount);
										}
										$finalIncentive = ($iamount2 + $iamount3);
										$selectQRY="SELECT * FROM incentive_data WHERE person_id = '$recruiterdataX' AND period = '$dtX' AND type = 'Old CS'";
										$selectRES=mysqli_query($misReportsConn, $selectQRY);
										if(mysqli_num_rows($selectRES)>0){
											while($selectROW=mysqli_fetch_array($selectRES)){
												if($responseType == 0){
								?>
								<tr style="background-color: #c3dcf4;font-size: 13px;">
									<td style="text-align: center;vertical-align: middle;"><i class="fa fa-lock" style="font-size: 18px;color: #2266AA;"></i></td>
									<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($selectROW['person_name']); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($selectROW['designation']); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($selectROW['manager_name']); ?></td>
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
											$recruiterQUERY = mysqli_query($catsConn, "SELECT concat(u.first_name,' ',u.last_name) AS rname, u.notes, mc.designation FROM user AS u JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = u.user_id WHERE u.user_id='$recruiterdataX'");
											$recruiterROW = mysqli_fetch_array($recruiterQUERY);
											if($responseType == 0){
								?>
								<tr style="font-size: 13px;">
									<td style="text-align: center;vertical-align: middle;"><input style="height:18px;width:18px;cursor:pointer;outline: none;" type="checkbox" class="checkboxes" name="checked_id[<?php echo $iii; ?>]" id="checked_id" value="<?php echo $iii; ?>"></td>
									
									<input type="hidden" id="mainamount<?php echo $iii; ?>" value="<?php echo $finalIncentive; ?>">
									<input type="hidden" name="person_id[<?php echo $iii; ?>]" value="<?php echo $recruiterdataX; ?>">
									<input type="hidden" name="person_name[<?php echo $iii; ?>]" value="<?php echo ucwords($recruiterROW['rname']); ?>">
									<input type="hidden" name="manager_name[<?php echo $iii; ?>]" value="<?php echo ucwords($recruiterROW['notes']); ?>">
									<input type="hidden" name="designation[<?php echo $iii; ?>]" value="<?php echo ucwords($recruiterROW['designation']); ?>">
									<input type="hidden" name="type_data[<?php echo $iii; ?>]" value="<?php echo "Old CS"; ?>">
									<input type="hidden" name="period[<?php echo $iii; ?>]" value="<?php echo $dtX; ?>">
									<input type="hidden" name="final_incentive[<?php echo $iii; ?>]" value="<?php echo $finalIncentive; ?>">
									<input type="hidden" name="detail_link[<?php echo $iii; ?>]" value="<?php echo LOCAL_REPORT_PATH; ?>/incentive/old_cs_incentive_report/index.php?multimonth=<?php echo urlencode($dtX); ?>&recruiter_name2=<?php echo urlencode($recruiterdataX); ?>&RIRsubmit2=&response_type=1">
									

									<td style="text-align: left;vertical-align: middle;"><a href="?multimonth=<?php echo $dtX; ?>&recruiter_name2=<?php echo $recruiterdataX; ?>&RIRsubmit2="><?php echo ucwords($recruiterROW['rname']); ?></a></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($recruiterROW['designation']); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($recruiterROW['notes']); ?></td>
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
							<tfoot>
								<th colspan="9" style="background-color: #bbb;text-align: right;vertical-align: middle;"><button type="submit" class="btn btn-primary" style="border-radius: 0px;background-color: #2266AA;"><i class="fa fa-lock"></i> Lock the Amount</button></th>
							</tfoot>
						</table>
					</div>
				</div>
			</form>
		</div>
	</section>
	<script>
		/*Select All checkboxes START*/
/*		$('#select_all').on('click',function(){
			if(this.checked){
				$('.checkboxes').each(function(){
					this.checked = true;
				});
			}else{
				 $('.checkboxes').each(function(){
					this.checked = false;
				});
			}
		});

		$('.checkboxes').on('click',function(){
			if($('.checkboxes:checked').length == $('.checkboxes').length){
				$('#select_all').prop('checked',true);
			}else{
				$('#select_all').prop('checked',false);
			}
		}); */
		/*Select All checkboxes END*/

		/*PS Incentive Form Submission START*/
		$('#OCSincentive').submit(function(e){
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
					data: $('#OCSincentive').serialize(),
					success: function(opt){
						$("#LoadingImage").hide();
						location.reload();
						alert('Incentive Successfully Locked!');
					}
				});
				return true;
			}
		});
		/*CS Incentive Form Submission END*/
	</script>
<?php
		}
	}
	//POST Overview Section END

	//POST Detail Section START
	if(isset($_REQUEST['RIRsubmit2'])){
		$monthsdata = $_REQUEST['multimonth'];
		$recruiterdata = $_REQUEST['recruiter_name2'];
		if($responseType == 0){
?>
	<section id="RIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row">
				<?php
					}
					$rdataQ = "SELECT concat(u.first_name,' ',u.last_name) AS rname, u.email, u.notes, (SELECT user_id FROM user WHERE concat(first_name,' ',last_name) = u.notes) AS notes_id, mc.designation FROM user AS u JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = u.user_id WHERE u.user_id='$recruiterdata'";
					$rdataR = mysqli_query($catsConn, $rdataQ);
					$rdataD = mysqli_fetch_array($rdataR);
					if($responseType == 0){
				?>
				<div class="col-md-6 col-md-offset-3" style="background-color: #ccc;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">
					<?php echo ucwords($rdataD['rname']); ?>
					<span style="font-size: 15px;color: #333;"><?php echo "(".ucwords($rdataD['designation']).")"; ?></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-10 col-md-offset-1">
					<table id="RIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
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
								<th style="text-align: center;vertical-align: middle;">Incentive Amount</th>
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

								$minMarginQRY = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Minimum Margin'");
								$minMarginROW = mysqli_fetch_array($minMarginQRY);
								$minMarginVAL = $minMarginROW['value'];

								$mainQUERY = "SELECT
									cjsh.candidate_id AS canid,
									concat(can.first_name,' ',can.last_name) AS canname,
									DATE_FORMAT(cjsh.date, '%m-%d-%Y') AS cats_placement_date,
									comp.company_id AS cid,
									comp.name AS cname,
									(SELECT concat(first_name,' ',last_name) AS cmname FROM user WHERE user_id=comp.owner) AS client_manager,
									date_format(cjsh.date, '%m-%d-%Y') AS cats_date,
								    cjsh.status_to AS cats_status,
									concat(u.first_name,' ',u.last_name) AS recruiter,
								    u.notes AS recruiter_manager,
									(SELECT mc.designation FROM user JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = user.user_id WHERE user.user_id=cj.added_by) AS designation,
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
									JOIN user AS u ON u.user_id = cj.added_by
								    JOIN vtech_mappingdb.system_integration AS mp ON mp.c_candidate_id = cjsh.candidate_id
								    JOIN vtechhrm.employees AS emp ON emp.id = mp.h_employee_id
								    JOIN vtechhrm.employmentstatus AS es ON es.id = emp.employment_status
								WHERE
									(cjsh.status_to = '800' OR cjsh.status_to = '620')
								AND
									cj.added_by = '$recruiterdata'
								AND
									date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
								GROUP BY cjsh.candidate_id";
								$mainRESULT = mysqli_query($catsConn, $mainQUERY);

								$tax_r=$mspfee=$prime_charge=$rate_can=$g_margin=0;
								$enameX=$iamount=array();

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
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW['ename']);?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $mainROW['cname']; ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $mainROW['client_manager']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['cats_date']; ?></td>
							<?php if($commentX == 'Placement'){ ?>
								<td style="text-align: center;vertical-align: middle;font-weight: bold;"><?php echo $commentX; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $commentX; ?></td>
							<?php } ?>
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
							<?php
								if($eligibility == 'Yes' && $g_margin >= $minMarginVAL){
									$iamountQUERY = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = '$commentX'");
									$iamountROW = mysqli_fetch_array($iamountQUERY);
									$iamount[] = $iamountROW['value'];
							?>
								<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $iamountROW['value']; ?></td>
							<?php
								}elseif($eligibility == 'Yes' && $g_margin < $minMarginVAL && $commentX == 'Placement'){
									$iamountQUERY2 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Old MIN Margin Incentive'");
									$iamountROW2 = mysqli_fetch_array($iamountQUERY2);
									$iamount[] = $iamountROW2['value'];
							?>
								<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $iamountROW2['value']; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;">0</td>
							<?php } ?>
							</tr>
							<?php
										}else{
											$hrm_termi_date=$incentive_amountX="";
											if($mainROW['hrm_status'] == 'Active'){
												$hrm_termi_date = "---";
											}else{
												$hrm_termi_date = $mainROW['termi_date'];
											}
											if($eligibility == 'Yes' && $g_margin >= $minMarginVAL){
												$iamountQUERY = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = '$commentX'");
												$iamountROW = mysqli_fetch_array($iamountQUERY);
												$incentive_amountX = $iamountROW['value'];
											}elseif($eligibility == 'Yes' && $g_margin < $minMarginVAL && $commentX == 'Placement'){
												$iamountQUERY2 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Old MIN Margin Incentive'");
												$iamountROW2 = mysqli_fetch_array($iamountQUERY2);
												$incentive_amountX = $iamountROW2['value'];
											}else{
												$incentive_amountX = "0";
											}
											$responseArray['personalList'][] = array('candidate' => ucwords($mainROW['ename']),
												'client' => $mainROW['cname'],
												'client_manager' => $mainROW['client_manager'],
												'cats_placement_date' => $mainROW['cats_date'],
												'cats_status' => $commentX,
												'hrm_join_date' => $mainROW['joindate'],
												'hrm_status' => $mainROW['hrm_status'],
												'hrm_termi_date' => $hrm_termi_date,
												'eligibility' => $eligibility,
												'margin' => $g_margin,
												'incentive_amount' => $incentive_amountX
											);
										}
									}
								}
								if($responseType == 0){
							?>
						</tbody>
						<tfoot>
							<tr style="background-color: #bbb;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;" colspan="10"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $iamount2 = array_sum($iamount); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<?php
				}
				if($rdataD['designation'] == 'Lead Recruiter' || $rdataD['designation'] == 'Senior Executive - Client Services')
				{
					$recruiterID = array();
					$managernm = $rdataD['notes'];
					$managerID = $rdataD['notes_id'];
					$recruiterEmail = $rdataD['email'];
					// AND mc.designation != 'Lead Recruiter' AND mc.designation != 'Senior Executive - Client Services'
					$findTeamQUERY = "SELECT u.user_id, mc.designation FROM user AS u JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = u.user_id WHERE notes = '$managernm'";
					$findTeamRESULT = mysqli_query($catsConn, $findTeamQUERY);
					while($findTeamROW = mysqli_fetch_array($findTeamRESULT)){
						$recruiterID[] = $findTeamROW['user_id'];
					}
					$recruiterIDX = implode(",", $recruiterID);

					if($responseType == 0){
			?>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-6 col-md-offset-3" style="background-color: #ccc;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">
					<?php echo ucwords($rdataD['notes']); ?>
					<span style="font-size: 15px;color: #333;"><?php echo "(CS Manager)"; ?></span>
				</div>
				<div class="col-md-12" style="font-size: 15px;margin-bottom: 20px;">
					<i class="fa fa-check" style="color: #449D44;"> Eligible Candidate</i><br>
					<i class="fa fa-times" style="color: #fc2828;"> Not Eligible Candidate</i>
				</div>
				<div class="col-md-12">
					<table id="RIRdata2" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">Recruiter</th>
								<th style="text-align: center;vertical-align: middle;">Designation</th>
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
								<th style="text-align: center;vertical-align: middle;">Incentive Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
								}
								$mainQUERY2 = "SELECT
									cjsh.candidate_id AS canid,
									concat(can.first_name,' ',can.last_name) AS canname,
									DATE_FORMAT(cjsh.date, '%m-%d-%Y') AS cats_placement_date,
									comp.company_id AS cid,
									comp.name AS cname,
									(SELECT concat(first_name,' ',last_name) AS cmname FROM user WHERE user_id=comp.owner) AS client_manager,
								    date_format(cjsh.date, '%m-%d-%Y') AS cats_date,
								    cjsh.date AS cats_dateX,
								    cjsh.status_to AS cats_status,
									concat(u.first_name,' ',u.last_name) AS recruiter,
								    u.notes AS recruiter_manager,
									(SELECT mc.designation FROM user JOIN vtech_mappingdb.manage_cats_roles AS mc ON mc.user_id = user.user_id WHERE user.user_id=cj.added_by) AS designation,
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
									JOIN user AS u ON u.user_id = cj.added_by
								    JOIN vtech_mappingdb.system_integration AS mp ON mp.c_candidate_id = cjsh.candidate_id
								    JOIN vtechhrm.employees AS emp ON emp.id = mp.h_employee_id
								    JOIN vtechhrm.employmentstatus AS es ON es.id = emp.employment_status
								WHERE
									(cjsh.status_to = '800' OR cjsh.status_to = '620')
								AND
									(cj.added_by = '$managerID' OR cj.added_by IN ({$recruiterIDX}))
								AND
									cj.added_by != '$recruiterdata'
								AND
									date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
								GROUP BY cjsh.candidate_id";
								$mainRESULT2 = mysqli_query($catsConn, $mainQUERY2);

								$tax_r=$mspfee=$prime_charge=$rate_can=$g_margin=0;
								$enameX=$iamount=array();

								if(mysqli_num_rows($mainRESULT2) > 0){
									while($mainROW2 = mysqli_fetch_array($mainRESULT2)){
										$emp_eid = $mainROW2['eid'];
										$clid = $mainROW2['cid'];
										$billrate = $mainROW2['billrate'];
										$payrate = $mainROW2['payrate'];
										$est_id = $mainROW2['es_id'];
										$benefit = $mainROW2['benefit'];

										$delimiter = array("","[","]",'"');
										$replace = str_replace($delimiter, $delimiter[0], $mainROW2['benefitlist']);
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

										/////// Eligibility START ///////

										$cur_date = strtotime(date("Y-m-d"));
										$third_mon_date = strtotime($mainROW2['joindateX'].' 3 month');
										$termi_date_chk = strtotime($mainROW2['termi_dateX']);
										if($mainROW2['hrm_status'] == 'Active'){
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
										
										/////// Eligibility END ///////

										if($mainROW2['cats_status'] == '800'){
											$commentX = 'Placement';
										}else{
											$commentX = 'Extension';
										}

										/////// Recruiter Eligibility START ///////
															
										$findDateQUERY = "SELECT confirmation_date FROM employees WHERE work_email = '$recruiterEmail'";
										$findDateRESULT = mysqli_query($hrmIndiaConn, $findDateQUERY);
										$findDateROW = mysqli_fetch_array($findDateRESULT);

										$recruiter_eligibility=$recruiter_eligibility_date=$recruiter_symbol='';

										if($findDateROW['confirmation_date'] != '0000-00-00 00:00:00' && $findDateROW['confirmation_date'] != '0001-01-01 00:00:00'){
											$recruiter_eligibility = 'Yes';
											$recruiter_eligibility_date = $findDateROW['confirmation_date'];
										}else{
											$recruiter_eligibility = 'No';
											$recruiter_eligibility_date = '';
										}

										if($recruiter_eligibility == 'Yes'){
											$red = strtotime($recruiter_eligibility_date);
											$cd = strtotime($mainROW2['cats_dateX']);
											if($red <= $cd){
												$recruiter_symbol = 'Yes';
											}else{
												$recruiter_symbol = 'No';
											}
										}elseif($recruiter_eligibility == 'No'){
											$recruiter_symbol = 'No';
										}else{
											$recruiter_symbol = '';
										}

										/////// Recruiter Eligibility END ///////

										if($responseType == 0){
							?>
							<tr style="font-size: 13px;color: #000;">
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW2['recruiter']); ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW2['designation']); ?></td>
							<?php if($recruiter_symbol == 'Yes'){ ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW2['ename']); ?> <i class="fa fa-check" style="color: #449D44;"></i></td>
							<?php }elseif($recruiter_symbol == 'No'){ ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW2['ename']); ?> <i class="fa fa-times" style="color: #fc2828;"></i></td>
							<?php }else{ ?>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW2['ename']); ?></td>
							<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW2['cname']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW2['client_manager']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW2['cats_date']; ?></td>
							<?php if($commentX == 'Placement'){ ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $commentX; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $commentX; ?></td>
							<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW2['joindate']; ?></td>
							<?php if($mainROW2['hrm_status'] == 'Active'){ ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW2['hrm_status']; ?></td>
								<td style="text-align: center;vertical-align: middle;">---</td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $mainROW2['hrm_status']; ?></td>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $mainROW2['termi_date']; ?></td>
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
							<?php
								if($eligibility == 'Yes' && $g_margin >= $minMarginVAL && $commentX == 'Placement' && ($recruiter_symbol == 'Yes' || $recruiter_symbol == '')){
									if($managernm == $mainROW2['client_manager']){
										$iamountQUERY3 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Sr/Lead Own Account'");
										$iamountROW3 = mysqli_fetch_array($iamountQUERY3);
										$iamount[] = $iamountROW3['value'];
									}else{
										$iamountQUERY3 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Sr/Lead Others Account'");
										$iamountROW3 = mysqli_fetch_array($iamountQUERY3);
										$iamount[] = $iamountROW3['value'];
									}
							?>
								<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $iamountROW3['value']; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;">0</td>
							<?php } ?>
							</tr>
							<?php
										}else{
											$hrm_termi_date=$incentive_amountX="";
											if($mainROW2['hrm_status'] == 'Active'){
												$hrm_termi_date = "---";
											}else{
												$hrm_termi_date = $mainROW2['termi_date'];
											}
											if($eligibility == 'Yes' && $g_margin >= $minMarginVAL && $commentX == 'Placement' && ($recruiter_symbol == 'Yes' || $recruiter_symbol == '')){
													if($managernm == $mainROW2['client_manager']){
														$iamountQUERY3 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Sr/Lead Own Account'");
														$iamountROW3 = mysqli_fetch_array($iamountQUERY3);
														$incentive_amountX = $iamountROW3['value'];
													}else{
														$iamountQUERY3 = mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel = 'Recruiter' AND comment = 'Sr/Lead Others Account'");
														$iamountROW3 = mysqli_fetch_array($iamountQUERY3);
														$incentive_amountX = $iamountROW3['value'];
													}
											}else{
												$incentive_amountX = "0";
											}

											$responseArray['teamList'][] = array('recruiter' => ucwords($mainROW2['recruiter']),
												'designation' => ucwords($mainROW2['designation']),
												'candidate' => ucwords($mainROW2['ename']),
												'client' => $mainROW2['cname'],
												'client_manager' => ucwords($mainROW2['client_manager']),
												'cats_placement_date' => $mainROW2['cats_date'],
												'cats_status' => $commentX,
												'hrm_join_date' => $mainROW2['joindate'],
												'hrm_status' => $mainROW2['hrm_status'],
												'hrm_termi_date' => $hrm_termi_date,
												'eligibility' => $eligibility,
												'margin' => $g_margin,
												'incentive_amount' => $incentive_amountX
											);
										}
									}
								}
								if($responseType == 0){
							?>
						</tbody>
						<tfoot>
							<tr style="background-color: #bbb;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;" colspan="12"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $iamount3 = array_sum($iamount); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
				<div class="col-md-6 col-md-offset-3" style="background-color: #ccc;text-align: center;font-size: 17px;padding: 5px;margin-top: 20px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">Final Incentive : 
					<span style="font-size: 15px;color: #333;"><?php echo $iamount2." + ".$iamount3." = ".($iamount2 + $iamount3); ?></span>
				</div>
			</div>
			<?php
					}
				}
				if($responseType == 0){
			?>
		</div>
	</section>
<?php
		}
	}
	if($responseType == 0){
?>

</body>
</html>
<?php
			}else{
				echo json_encode($responseArray);
			}
		}else{
			if($childUser=='Admin'){
				header("Location:../../../admin.php");
			}elseif($childUser=='User'){
				header("Location:../../../user.php");
			}else{
				header("Location:../../../index.php");
			}
		}
    }else{
        header("Location:../../../index.php");
    }
?>
