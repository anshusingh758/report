<?php
	include("../../../security.php");

	$responseArray = array();
	$responseType = isset($_REQUEST['response_type']) && $_REQUEST['response_type'] == 1 ? 1 : 0;

    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='38';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){
			if($responseType == 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Recruiter Incentive Report</title>

	<?php
		include('../../../cdn.php');
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
			$("#multimonth2").datepicker({
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

			/*Select Recruiter START*/
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
			/*Select Recruiter END*/

			/*Select Recruiter2 START*/
			$('#recruiter_name2').multiselect({
				nonSelectedText: 'Select Recruiter',
				numberDisplayed: 1,
				enableFiltering:true,
				enableCaseInsensitiveFiltering:true,
				buttonWidth:'100%',
				includeSelectAllOption: true,
 				maxHeight: 300
			});
			/*Select Recruiter2 END*/

			/*Datatable Calling START*/
			var tableX = $('#RIRdata').DataTable({
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

		/*Select Client of Particular Manager START*/
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
		/*Select Client of Particular Manager END*/
		function goBack(){
			window.history.back();
		}
	</script>

</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Recruiter Incentive Report</div>
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
					<div class="col-md-6 col-md-offset-1">
						<label>Select Months :</label>
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
					<div class="col-md-3">
						<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-9">
						<div class="row" style="margin-top: 25px;">
							<div class="col-md-4 col-md-offset-4">
								<label>Select CS Manager :</label>
								<select id="manager_name" name="manager_name[]" onchange="mnname()" multiple required>
									<?php
										$mannm=array();
										$sqluser="SELECT
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
											$mannm[]=$userlist['manager_name'];
											echo "<option value='".$userlist['manager_name']."'>".ucwords($userlist['manager_name'])."</option>";
										}
									?>
								</select>
							</div>
							<div class="col-md-4">
								<label>Select Recruiter :</label>
								<select id="recruiter_name" name="recruiter_name[]" multiple required>
									<?php
										foreach($mannm AS $mannm2){
											$sqluserX="SELECT
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
							<div class="col-md-4 col-md-offset-4" style="margin-top: 35px;">
								<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
							</div>
							<div class="col-md-4" style="margin-top: 35px;">
								<button type="submit" class="form-control" name="RIRsubmit1" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<img src="<?php echo IMAGE_PATH; ?>/notice.png" class="pull-left">
					</div>
				</div>
			</form>
		</div>
	</section>

<!--POST multimonth Section-->
<?php
	}
	function decimalHours($time){
		$tms = explode(":", $time);
		return ($tms[0] + ($tms[1]/60) + ($tms[2]/3600));
	}
	//POST Overview Section START
	if(isset($_REQUEST['RIRsubmit1'])){
		$monthsdata=$_REQUEST['multimonth'];
		$recruiterdata=$_REQUEST['recruiter_name'];

		if($responseType == 0){
?>
	<section id="RIRdatatable" class="hidden">
		<div class="container-fluid">
			<form id="CSincentive" onsubmit="return true">
				<div class="row" style="margin-bottom: 50px;">
					<div class="col-md-12">
						<table id="RIRdata" class="table table-striped table-bordered">
							<thead>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th class='no-sort' style="text-align: center;vertical-align: middle;" rowspan="2"><input style="height:20px;width:20px;cursor: pointer;outline: none;" type='checkbox' name='select_all' id='select_all'></th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Recruiter</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Recruiter Manager</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Total</th>
									<th style="text-align: center;vertical-align: middle;" colspan="2">Fix Incentive</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Incentive</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Amount</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th style="text-align: center;vertical-align: middle;">Candidate</th>
									<th style="text-align: center;vertical-align: middle;">Join (In Quater)</th>
									<th style="text-align: center;vertical-align: middle;">Incentive Amount</th>
									<th style="text-align: center;vertical-align: middle;">Per Hire</th>
									<th style="text-align: center;vertical-align: middle;">New Account Crack</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;">Method</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;">Amount</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Comment</th>
								</tr>
							</thead>
							<tbody>
								<?php
									}
									$monthsdata=str_replace("-", "/", $monthsdata);
									$mdata=explode("/",$monthsdata);
									$month=$mdata[0];
									$year=$mdata[1];

									$dt = $year."-".$month;
									$dtX = $month."-".$year;
									$dtX2 = $month."/".$year;

									$sdateX=date('Y-m-01',strtotime($dt));
									$tdateX=date('Y-m-t',strtotime($dt));

									//Quater Based Dates START
									//$thisMonth = date("m",strtotime('first day of this month'));
									$thisMonth=$mdata[0];
									if($thisMonth=='01' || $thisMonth=='02' || $thisMonth=='03'){
										$firstday=date("Y-01-01");
										$lastday=date("Y-03-31");
									}elseif($thisMonth=='04' || $thisMonth=='05' || $thisMonth=='06'){
										$firstday=date("Y-04-01");
										$lastday=date("Y-06-30");
									}elseif($thisMonth=='07' || $thisMonth=='08' || $thisMonth=='09'){
										$firstday=date("Y-07-01");
										$lastday=date("Y-09-30");
									}elseif($thisMonth=='10' || $thisMonth=='11' || $thisMonth=='12'){
										$firstday=date("Y-10-01");
										$lastday=date("Y-12-31");
									}
									//Quater Based Dates END

									$start_dateX=date('Y/m/01',strtotime($dt));
									$end_dateX=date('Y/m/t',strtotime($dt));
									$start_dateX2=strtotime($start_dateX);
									$end_dateX2=strtotime($end_dateX);

									///////Finding basic Criteria(LIMIT, #of join, min margin) START//////
									$limitQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Limit (in Month)'");
									$limitROW=mysqli_fetch_array($limitQRY);
									$limitVAL=$limitROW['value'];

									$minMarginQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Minimum Margin'");
									$minMarginROW=mysqli_fetch_array($minMarginQRY);
									$minMarginVAL=$minMarginROW['value'];

									$joinQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Join this quarter'");
									$joinROW=mysqli_fetch_array($joinQRY);
									$joinVAL=$joinROW['value'];
									///////Finding basic Criteria(LIMIT, #of join, min margin) END////

									$iii=0;
									foreach($recruiterdata AS $rdata){
										$main_query="SELECT
													u.user_id AS rid,
													concat(u.first_name,' ',u.last_name) AS recnm,
													u.notes AS mannm,
													emp.id AS eid,
													emp.status,
													concat(emp.first_name,' ',emp.last_name) AS ename,
												    date_format(emp.custom7, '%m-%d-%Y') AS joindate,
													date_format(emp.custom7, '%Y-%m-%d') AS joindateX,
													date_format(emp.custom7, '%m') AS joinmonth,
												    date_format(emp.termination_date, '%m-%d-%Y') AS termi_date,
													date_format(emp.termination_date, '%Y-%m-%d') AS termi_dateX,
													comp.company_id AS cid,
													comp.name AS cname,
													emp.custom1 AS benefit,
													emp.custom2 AS benefitlist,
													CAST(replace(emp.custom3,'$','') AS DECIMAL (10,2)) AS billrate,
													CAST(replace(emp.custom4,'$','') AS DECIMAL (10,2)) AS payrate,
													es.id AS es_id,
													es.name AS employment_type
												FROM
													employees AS emp
													JOIN vtechhrm.employmentstatus AS es ON es.id=emp.employment_status
													JOIN vtech_mappingdb.system_integration AS mp ON mp.h_employee_id=emp.id
													JOIN cats.user AS u ON u.user_id=mp.c_recruiter_id
													JOIN cats.company AS comp ON comp.company_id=mp.c_company_id
												WHERE
													u.user_id IN ({$rdata})
												AND
													date_format(emp.custom7, '%Y-%m-%d')<='$tdateX'
												AND
													year(emp.custom7)>'2017'
												AND
													TIMESTAMPDIFF(MONTH, date_format(emp.custom7, '%Y-%m-%d'), '$tdateX')<='$limitVAL'";
										$main_result=mysqli_query($vtechhrmConn,$main_query);

										$enameX=$cnameX=$joindateX2=$eligibilityX=$g_margin2X=$tothrX=$termi_dateX=$perHireExtraX=$newAccCrackAmtX=$statusX2=array();
										$recnmX=$mannmX=$ridXX2="";

										if(mysqli_num_rows($main_result)>0){
											while($main_row=mysqli_fetch_array($main_result)){
												$ridXX=$main_row['rid'];
												$ridXX2=$main_row['rid'];
												$recnmX=$main_row['recnm'];
												$mannmX=$main_row['mannm'];
												$clid1=$main_row['cid'];
												$emp_eid=$main_row['eid'];
												$billrate=$main_row['billrate'];
												$payrate=$main_row['payrate'];
												$est_id=$main_row['es_id'];
												$statusX=$main_row['status'];
												$emptype=$main_row['employment_type'];
												$benefit=$main_row['benefit'];

												$bfli=$main_row['benefitlist'];
												$delimiter=array("","[","]",'"');
												$replace=str_replace($delimiter, $delimiter[0], $bfli);
												$explode=explode(" ",$replace);
												$benefitlist=$replace;

												$newAccCrackAmt='0';
												$nacaQRY=mysqli_query($vtechMappingdbConn, "SELECT id FROM system_integration WHERE h_employee_id='$emp_eid' AND c_company_id='$clid1'");
												$nacaROW=mysqli_fetch_array($nacaQRY);

												$nacaQRY2=mysqli_query($vtechMappingdbConn, "SELECT MIN(id) AS minid FROM system_integration WHERE c_company_id='$clid1'");
												$nacaROW2=mysqli_fetch_array($nacaQRY2);
												if(($nacaROW2['minid']==$nacaROW['id']) && ($main_row['joinmonth']==$month)){
													$pheQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Fix Incentive For New Account Crack'");
													$pheROW=mysqli_fetch_array($pheQRY);
													$newAccCrackAmt=$pheROW['value'];
												}else{
													$newAccCrackAmt='0';
												}

												if($newAccCrackAmt<='0'){
													$perHireExtra='0';
													if($main_row['joinmonth']==$month){
														$pheQRY2=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Fix Incentive Per Hire'");
														$pheROW2=mysqli_fetch_array($pheQRY2);
														$perHireExtra=$pheROW2['value'];
													}else{
														$perHireExtra="0";
													}
												}else{
													$perHireExtra="0";
												}


												$tax_r=0;
												$mspfee2=0;
												$prime_chrg21=0;
												$rate_can3=0;
												$g_margin=0;
												$g_margin2=0;

												$tax=array();
												//////////////////////////    TAX    //////////////////////////

												$mydata1=explode(",",$benefitlist);
												foreach($mydata1 as $value22){
													$sel_query="SELECT charge_pct,benefits FROM tax_settings WHERE empst_id=$est_id AND benefits LIKE '%$value22%'";
													$sel12=mysqli_query($vtechMappingdbConn,$sel_query);
													$sell4=mysqli_fetch_assoc($sel12);

													$per_wbf=$sell4['charge_pct'];
													$prime_tax=($payrate * ($per_wbf/100));

													/////  for w2,1099 candidates without benefit tax='11-2'  /////
													if($est_id!=3 || $est_id!=6){
														if($benefit=="Without Benefits"|| $benefit=="Not Applicable"){
															if($est_id==1 || $est_id==4){
																$per_nbf='11';
															}
															if($est_id==5 || $est_id==2){
																 $per_nbf='2';
															}
															if($est_id==3 || $est_id==6){
																 $per_nbf='0';
															}
															$tax[]=($per_nbf/ 100) * $payrate;
														}
														if($benefit=="With Benefits"){
															$tax[]=$payrate*($per_wbf / 100);
														}
														$tax11=array_sum($tax);
														$tax_r=round($tax11,2);
													}else{
														$tax[]=0;
														$tax_r=array_sum($tax);
													}
												}

												if(($tax_r>0)&&($replace!='')&&($replace!=' ')&& ($est_id==1 || $est_id==4)){
													$tax_r1=($tax_r+($payrate*0.11));
													$tax_r=round($tax_r1,2);
												}
												if(($tax_r>0)&&($replace!='')&&($replace!=' ')&& ($est_id==5 || $est_id==2)){
													$tax_r2=($tax_r+($payrate*0.02));
													$tax_r=round($tax_r2,2);
												}

												/*echo $tax_r;
												echo "----------";*/

												//////////////////////////    MSP Fees    //////////////////////////

												$s_msp="SELECT mspChrg_pct,primechrg_pct,primeChrg_dlr,mspChrg_dlr FROM client_fees WHERE client_id=$clid1";
												$s_msp2=mysqli_query($vtechMappingdbConn,$s_msp);

												$row212=mysqli_fetch_assoc($s_msp2);
												$msp_charge=$row212['mspChrg_pct'];
												$prime_chrg=$row212['primechrg_pct'];
												$pcli_dlr=$row212['primeChrg_dlr'];
												$clmsp_dlr=$row212['mspChrg_dlr'];

												//////    msp    ///////////
												$mspfee=($msp_charge/100)*$billrate;
												$mspfee1=round($mspfee,2);
												$mspfee2=$mspfee1+$clmsp_dlr;

												/*echo $mspfee2;
												echo "----------";*/

												//////////////////////////    prime vendor fees    //////////////////////////

												$can_sql="SELECT c_primeCharge_pct,c_primeCharge_dlr,c_anyCharge_dlr FROM candidate_fees WHERE emp_id='$emp_eid'";
												$can_sql2=mysqli_query($vtechMappingdbConn,$can_sql);
												$row213=mysqli_fetch_assoc($can_sql2);

												$can_prime=$row213['c_primeCharge_pct'];
												$canprime_dlr=$row213['c_primeCharge_dlr'];
												$can_other=$row213['c_anyCharge_dlr'];

												$prime_chrg1=($prime_chrg / 100) * $billrate;
												$prime_chrg_can=($can_prime / 100) * $billrate;
												$prime_chrg2=$prime_chrg1+$prime_chrg_can+$canprime_dlr+$pcli_dlr;

												$prime_chrg21=round($prime_chrg2,2);

												/*echo $prime_chrg21;
												echo "----------";*/

												//////////////////////////    Rate for Candidate    //////////////////////////

												$rate_can2=$payrate+$tax_r+$mspfee2+$prime_chrg21;
												$rate_can3=round($rate_can2,2);

												/*echo $rate_can3;
												echo "---------"; */

												////////////////////////// Gross Margin //////////////////////////

												$g_margin=$billrate-$rate_can3;
												$g_margin2=round($g_margin,2);

												///////////////// Total Hours //////////////////////

												$tothr = 0;
												for($i=$start_dateX2;$i<=$end_dateX2;$i+=86400){
													$date_cur=date('Y-m-d', $i);
													$sql_q="SELECT time_start,time_end from employeetimeentry WHERE employee='$emp_eid' and date_format(date_start,'%Y-%m-%d')='$date_cur'";
													$exeqry_q = mysqli_query($vtechhrmConn,$sql_q);
													while($row_q = mysqli_fetch_array($exeqry_q)){
														$decimalHours = decimalHours($row_q['time_start']);
														$decimalHours2 = decimalHours($row_q['time_end']);
														$_hours=$decimalHours2 - $decimalHours ;
														$tothr += $_hours;
													}
												}


												//////////////Eligibility///////////////////
												$cur_date = strtotime(date("Y-m-d"));
												$third_mon_date = strtotime($main_row['joindateX'].' 3 month');
												$termi_date_chk = strtotime($main_row['termi_dateX']);
												if($main_row['status']=='Active'){
													if($cur_date>$third_mon_date){
														$eligibility="Yes";
													}else{
														$eligibility="No";
													}
												}else{
													if($termi_date_chk>$third_mon_date){
														$eligibility="Yes";
													}else{
														$eligibility="No";
													}
												}

												$enameX[]=$main_row['ename'];
												$cnameX[]=$main_row['cname'];
												$joindateX2[]=$main_row['joindate'];
												$termi_dateX[]=$main_row['termi_date'];
												$eligibilityX[]=$eligibility;
												$g_margin2X[]=$g_margin2;
												$tothrX[]=$tothr;
												$perHireExtraX[]=$perHireExtra;
												$newAccCrackAmtX[]=$newAccCrackAmt;
												$statusX2[]=$statusX;
											}
										}
										$main_query.=" GROUP BY recnm";
										$main_resultX=mysqli_query($vtechhrmConn, $main_query);
										$main_rowX=mysqli_fetch_array($main_resultX);
										$ridXXX=$main_rowX['rid'];
										///////////////////Compare JOin & Extension///////////////////////
										$totplace=$totext=array();

										$COMPAREquery1="SELECT
															cjsh.candidate_id AS totplace
														FROM
															candidate_joborder_status_history AS cjsh
															JOIN candidate_joborder AS cj ON cj.candidate_id=cjsh.candidate_id AND cj.joborder_id=cjsh.joborder_id
														WHERE
															cj.added_by IN ({$ridXXX})
														AND
															cjsh.status_to='800'
														AND
															date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$firstday' AND '$lastday'";
										$COMPAREresult1=mysqli_query($catsConn,$COMPAREquery1);
										while($COMPARErow1=mysqli_fetch_array($COMPAREresult1)){
											$totplace[]=$COMPARErow1['totplace'];
										}
										$totjoinX=sizeof($totplace);
										$COMPAREquery2="SELECT
															cjsh.candidate_id AS totext
														FROM
															candidate_joborder_status_history AS cjsh
															JOIN candidate_joborder AS cj ON cj.candidate_id=cjsh.candidate_id AND cj.joborder_id=cjsh.joborder_id
														WHERE
															cj.added_by IN ({$ridXXX})
														AND
															cjsh.status_from='800' AND cjsh.status_to='620'
														AND
															date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$firstday' AND '$lastday'";
										$COMPAREresult2=mysqli_query($catsConn,$COMPAREquery2);
										while($COMPARErow2=mysqli_fetch_array($COMPAREresult2)){
											$totext[]=$COMPARErow2['totext'];
										}
										$NEWtotjoinX=$NEWtotjoinXX='0';
										$NEWtotjoinX=sizeof(array_unique(array_diff($totplace,$totext)));
										if($NEWtotjoinX=='0'){
											$NEWtotjoinXX=$totjoinX;
										}else{
											$NEWtotjoinXX=$NEWtotjoinX;
										}

										array_multisort($tothrX, SORT_DESC, SORT_NATURAL, $enameX, $cnameX, $joindateX2, $termi_dateX, $eligibilityX, $g_margin2X, $perHireExtraX, $statusX2, $newAccCrackAmtX);
										$cmargin='0';
										$incAMTX=$incAMTX2=$enameX3=$cnameX2=$perHireExtraX2=$newAccCrackAmtX2=array();
										foreach($enameX AS $key => $enameX2){
											if(($tothrX[$key]=='0' && $statusX2[$key]=='Active') || $tothrX[$key]>'0'){
												if($eligibilityX[$key]=='Yes' && $g_margin2X[$key]>$minMarginVAL){
													$cmargin+=$g_margin2X[$key];
												}
												//////////////////Incentive Percentage//////////////////////////
												$percX='0';
												$query2="SELECT * FROM incentive_criteria WHERE personnel='Recruiter' AND comment=''";
												$result2=mysqli_query($misReportsConn,$query2);
												while($rowX2=mysqli_fetch_array($result2)){
													if($cmargin>=$rowX2['min_margin'] && $cmargin<$rowX2['max_margin']){
														$percX=$rowX2['value'];
													}
													if($cmargin>=$rowX2['min_margin'] && $rowX2['max_margin']=='0'){
														$percX=$rowX2['value'];
													}
												}
												$incAMT='0';
												$incAMT=round($g_margin2X[$key]*$percX/100*60*$tothrX[$key],2);
												if($eligibilityX[$key]=='Yes' && $g_margin2X[$key]>$minMarginVAL){
													$enameX3[]=$enameX[$key];
													$cnameX2[]=$cnameX[$key];
													$incAMTX[]=$incAMT+$perHireExtraX[$key]+$newAccCrackAmtX[$key];
													$incAMTX2[]=$incAMT;
													$perHireExtraX2[]=$perHireExtraX[$key];
													$newAccCrackAmtX2[]=$newAccCrackAmtX[$key];
												}
											}
										}
										if(sizeof(array_unique($enameX3))!="0"){
											$selectQRY="SELECT * FROM incentive_data WHERE person_id='$ridXX2' AND period='$dtX' AND type = 'Recruiter'";
											$selectRES=mysqli_query($misReportsConn, $selectQRY);
											if(mysqli_num_rows($selectRES)>0){
												while($selectROW=mysqli_fetch_array($selectRES)){
													if($responseType == 0){
								?>
								<tr style="background-color: #c3dcf4;">
									<td style="text-align: center;vertical-align: middle;"><i class="fa fa-lock" style="font-size: 18px;color: #2266AA;"></i></td>
									<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($selectROW['person_name']); ?></td>
									<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($selectROW['manager_name']); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['total_candidate']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['total_join']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['incentive_amount']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['rec_per_hire']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['rec_new_acc_crack']; ?></td>
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
								<tr>
									<td style="text-align: center;vertical-align: middle;"><input style="height:18px;width:18px;cursor:pointer;outline: none;" type="checkbox" class="checkboxes" name="checked_id[<?php echo $iii; ?>]" id="checked_id" value="<?php echo $ridXX2; ?>"></td>

									<input type="hidden" name="person_id[<?php echo $iii; ?>]" value="<?php echo $ridXX2; ?>">
									<input type="hidden" name="person_name[<?php echo $iii; ?>]" value="<?php echo ucwords($recnmX); ?>">
									<input type="hidden" name="manager_name[<?php echo $iii; ?>]" value="<?php echo ucwords($mannmX); ?>">
									<input type="hidden" name="type_data[<?php echo $iii; ?>]" value="<?php echo "Recruiter"; ?>">
									<input type="hidden" name="period[<?php echo $iii; ?>]" value="<?php echo $dtX; ?>">
									<input type="hidden" name="total_candidate[<?php echo $iii; ?>]" value="<?php echo sizeof(array_unique($enameX3)); ?>">
									<input type="hidden" name="total_join[<?php echo $iii; ?>]" value="<?php echo $NEWtotjoinXX; ?>">
								<?php if($NEWtotjoinXX>=$joinVAL){?>
									<input type="hidden" id="mainamount<?php echo $iii; ?>" value="<?php echo array_sum($incAMTX); ?>">
								<?php }else{ ?>
									<input type="hidden" id="mainamount<?php echo $iii; ?>" value="<?php echo "0"; ?>">
								<?php } ?>
									<input type="hidden" name="mainamount[<?php echo $iii; ?>]" value="<?php echo array_sum($incAMTX2); ?>">
									<input type="hidden" name="rec_per_hire[<?php echo $iii; ?>]" value="<?php echo array_sum($perHireExtraX2); ?>">
									<input type="hidden" name="rec_new_acc_crack[<?php echo $iii; ?>]" value="<?php echo array_sum($newAccCrackAmtX2); ?>">
									<input type="hidden" name="final_incentive[<?php echo $iii; ?>]" value="<?php echo array_sum($incAMTX); ?>">
									<input type="hidden" name="detail_link[<?php echo $iii; ?>]" value="<?php echo LOCAL_REPORT_PATH; ?>/incentive/recruiter_incentive_report/index.php?multimonth=<?php echo urlencode($dtX); ?>&recruiter_name2=<?php echo urlencode($ridXX2); ?>&RIRsubmit2=&response_type=1">

									<td style="text-align: left;vertical-align: middle;"><a href="?multimonth=<?php echo $dtX; ?>&recruiter_name2=<?php echo $ridXX2; ?>&RIRsubmit2=" style="cursor: pointer;"><?php echo ucwords($recnmX); ?></a></td>
									<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mannmX); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo sizeof(array_unique($enameX3)); ?></td>
								<?php if($NEWtotjoinXX>=$joinVAL){?>
									<td style="text-align: center;vertical-align: middle;"><?php echo $NEWtotjoinXX; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($incAMTX2); ?></td>
								<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $NEWtotjoinXX; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo array_sum($incAMTX2); ?></td>
								<?php } ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($perHireExtraX2); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($newAccCrackAmtX2); ?></td>
								<?php if($NEWtotjoinXX>=$joinVAL){?>
									<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo array_sum($incAMTX); ?></td>
								<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;"><?php echo array_sum($incAMTX); ?></td>
								<?php } ?>
									<td style="text-align: center;vertical-align: middle;">
										<select id="adjustment_method<?php echo $iii; ?>" name="adjustment_method[<?php echo $iii; ?>]" onchange="adjustMETHOD<?php echo $iii; ?>(this.value)" style="padding: 5px;cursor: pointer;" required>
											<option value="plus">ADD (+)</option>
											<option value="minus">SUB (-)</option>
										</select>
									</td>
									<td style="text-align: center;vertical-align: middle;">
										<input type="text" id="adjustment_amount<?php echo $iii; ?>" name="adjustment_amount[<?php echo $iii; ?>]" onchange="adjustAMT<?php echo $iii; ?>(this.value)" maxlength="10" onkeypress='return event.charCode >= 46 && event.charCode <= 57' placeholder="0" style="width: 70%;padding: 2px 5px;">
									</td>
									<td style="text-align: center;vertical-align: middle;"><textarea name="adjustment_comment[<?php echo $iii; ?>]" rows="1" autocomplete="off" style="width: 90%;padding: 3px 7px;"></textarea></td>
								<?php if($NEWtotjoinXX>=$joinVAL){?>
									<td style="text-align: center;vertical-align: middle;"><input type="text" id="final_amount<?php echo $iii; ?>" name="final_amount[<?php echo $iii; ?>]" style="background-color: #fff;color: #000;width: 70%;padding: 2px 5px;border: none;text-align: center;" value="<?php echo array_sum($incAMTX);; ?>" readonly></td>
								<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;"><input type="text" id="final_amount<?php echo $iii; ?>" name="final_amount[<?php echo $iii; ?>]" style="background-color: #fff;color: #000;width: 70%;padding: 2px 5px;border: none;text-align: center;" value="<?php echo "0"; ?>" readonly></td>
								<?php } ?>
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
										}
										$iii++;
									}
									if($responseType == 0){
								?>
							</tbody>
							<tfoot class="overviewtfoot">
								<th colspan="13" style="background-color: #bbb;text-align: right;vertical-align: middle;"><button type="submit" class="btn btn-primary" style="border-radius: 0px;background-color: #2266AA;"><i class="fa fa-lock"></i> Lock the Amount</button></th>
							</tfoot>
						</table>
					</div>
				</div>
			</form>
		</div>
	</section>

	<script>
		/*Select All checkboxes START*/
		$('#select_all').on('click',function(){
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
		});
		/*Select All checkboxes END*/

		/*CS Incentive Form Submission START*/
		$('#CSincentive').submit(function(e){
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
					data: $('#CSincentive').serialize(),
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
		/*CS Incentive Form Submission END*/
	</script>

<?php
		}
	}
	//POST Overview Section END

	//POST Detail Section START
	if(isset($_REQUEST['RIRsubmit2'])){
		$monthsdata=$_REQUEST['multimonth'];
		$recruiterdata=$_REQUEST['recruiter_name2'];
		if($responseType == 0){
?>
	<section id="RIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row">
				<?php
					//Inside PS Personnel Name
					$rdataQ="SELECT concat(first_name,' ',last_name) AS rname FROM user WHERE user_id='$recruiterdata'";
					$rdataR=mysqli_query($catsConn,$rdataQ);
					$rdataD=mysqli_fetch_array($rdataR);
				?>
				<div class="col-md-4 col-md-offset-4" style="background-color: #ccc;color: #000;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">Recruiter : <span style="font-size: 16px;color: #333;"><?php echo ucwords($rdataD['rname']); ?></span><span style="font-size: 15px;color: #449D44;"><?php echo " (".$monthsdata.")"; ?></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="RIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Client</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Joining Date</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Status</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Termination Date</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">3 Months<br>Completed</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Margin</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Cumulative Margin</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Percentage (%)</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Total Hours</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2" data-toggle="tooltip" data-placement="auto" title="Margin *Percentage / (100*60)*Total Hours">Incentive Amount</th>
								<th style="text-align: center;vertical-align: middle;" colspan="2">Fix Incentive</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Amount</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;">Per Hire</th>
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">New Account Crack</th>
							</tr>
						</thead>
						<tbody>
							<?php
								}
								$mdata=explode("-",$monthsdata);
								$month=$mdata[0];
								$year=$mdata[1];

								$dt = $year."-".$month;

								$sdateX=date('Y-m-01',strtotime($dt));
								$tdateX=date('Y-m-t',strtotime($dt));

								//Quater Based Dates START
								//$thisMonth = date("m",strtotime('first day of this month'));
								$thisMonth=$mdata[0];
								if($thisMonth=='01' || $thisMonth=='02' || $thisMonth=='03'){
									$firstday=date("Y-01-01");
									$lastday=date("Y-03-31");
								}elseif($thisMonth=='04' || $thisMonth=='05' || $thisMonth=='06'){
									$firstday=date("Y-04-01");
									$lastday=date("Y-06-30");
								}elseif($thisMonth=='07' || $thisMonth=='08' || $thisMonth=='09'){
									$firstday=date("Y-07-01");
									$lastday=date("Y-09-30");
								}elseif($thisMonth=='10' || $thisMonth=='11' || $thisMonth=='12'){
									$firstday=date("Y-10-01");
									$lastday=date("Y-12-31");
								}
								//Quater Based Dates END

								$start_dateX=date('Y/m/01',strtotime($dt));
								$end_dateX=date('Y/m/t',strtotime($dt));
								$start_dateX2=strtotime($start_dateX);
								$end_dateX2=strtotime($end_dateX);

								///////Finding basic Criteria(LIMIT, #of join, min margin) START//////
								$limitQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Limit (in Month)'");
								$limitROW=mysqli_fetch_array($limitQRY);
								$limitVAL=$limitROW['value'];

								$minMarginQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Minimum Margin'");
								$minMarginROW=mysqli_fetch_array($minMarginQRY);
								$minMarginVAL=$minMarginROW['value'];

								$joinQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Join this quarter'");
								$joinROW=mysqli_fetch_array($joinQRY);
								$joinVAL=$joinROW['value'];
								///////Finding basic Criteria(LIMIT, #of join, min margin) END////

								$main_query="SELECT
											u.user_id AS rid,
										    concat(u.first_name,' ',u.last_name) AS recnm,
										    u.notes AS mannm,
											emp.id AS eid,
											emp.status,
										    concat(emp.first_name,' ',emp.last_name) AS ename,
										    date_format(emp.custom7, '%m-%d-%Y') AS joindate,
											date_format(emp.custom7, '%Y-%m-%d') AS joindateX,
											date_format(emp.custom7, '%m') AS joinmonth,
										    date_format(emp.termination_date, '%m-%d-%Y') AS termi_date,
											date_format(emp.termination_date, '%Y-%m-%d') AS termi_dateX,
										    comp.company_id AS cid,
											comp.name AS cname,
											emp.custom1 AS benefit,
											emp.custom2 AS benefitlist,
											CAST(replace(emp.custom3,'$','') AS DECIMAL (10,2)) AS billrate,
											CAST(replace(emp.custom4,'$','') AS DECIMAL (10,2)) AS payrate,
											es.id AS es_id,
											es.name AS employment_type
										FROM
											employees AS emp
										    JOIN vtechhrm.employmentstatus AS es ON es.id=emp.employment_status
										    JOIN vtech_mappingdb.system_integration AS mp ON mp.h_employee_id=emp.id
										    JOIN cats.user AS u ON u.user_id=mp.c_recruiter_id
										    JOIN cats.company AS comp ON comp.company_id=mp.c_company_id
										WHERE
											u.user_id IN ({$recruiterdata})
										AND
											date_format(emp.custom7, '%Y-%m-%d')<='$tdateX'
										AND
											year(emp.custom7)>'2017'
										AND
											TIMESTAMPDIFF(MONTH, date_format(emp.custom7, '%Y-%m-%d'), '$tdateX')<='12'";
								$main_result=mysqli_query($vtechhrmConn,$main_query);
								if(mysqli_num_rows($main_result)>0){
									while($main_row=mysqli_fetch_array($main_result)){
										$ridXX=$main_row['rid'];
										$clid1=$main_row['cid'];
										$emp_eid=$main_row['eid'];
										$billrate=$main_row['billrate'];
										$payrate=$main_row['payrate'];
										$est_id=$main_row['es_id'];
										$statusX=$main_row['status'];
										$emptype=$main_row['employment_type'];
										$benefit=$main_row['benefit'];

										$bfli=$main_row['benefitlist'];
										$delimiter=array("","[","]",'"');
										$replace=str_replace($delimiter, $delimiter[0], $bfli);
										$explode=explode(" ",$replace);
										$benefitlist=$replace;

										$newAccCrackAmt='0';
										$nacaQRY=mysqli_query($vtechMappingdbConn, "SELECT id FROM system_integration WHERE h_employee_id='$emp_eid' AND c_company_id='$clid1'");
										$nacaROW=mysqli_fetch_array($nacaQRY);

										$nacaQRY2=mysqli_query($vtechMappingdbConn, "SELECT MIN(id) AS minid FROM system_integration WHERE c_company_id='$clid1'");
										$nacaROW2=mysqli_fetch_array($nacaQRY2);
										if(($nacaROW2['minid']==$nacaROW['id']) && ($main_row['joinmonth']==$month)){
											$pheQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Fix Incentive For New Account Crack'");
											$pheROW=mysqli_fetch_array($pheQRY);
											$newAccCrackAmt=$pheROW['value'];
										}else{
											$newAccCrackAmt='0';
										}
										if($newAccCrackAmt<='0'){
											$perHireExtra='0';
											if($main_row['joinmonth']==$month){
												$pheQRY2=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Fix Incentive Per Hire'");
												$pheROW2=mysqli_fetch_array($pheQRY2);
												$perHireExtra=$pheROW2['value'];
											}else{
												$perHireExtra="0";
											}
										}else{
											$perHireExtra="0";
										}

										$tax_r=0;
										$mspfee2=0;
										$prime_chrg21=0;
										$rate_can3=0;
										$g_margin=0;
										$g_margin2=0;

										$tax=array();
										//////////////////////////    TAX    //////////////////////////

										$mydata1=explode(",",$benefitlist);
										foreach($mydata1 as $value22){
											$sel_query="SELECT charge_pct,benefits FROM tax_settings WHERE empst_id=$est_id AND benefits LIKE '%$value22%'";
											$sel12=mysqli_query($vtechMappingdbConn,$sel_query);
											$sell4=mysqli_fetch_assoc($sel12);

										    $per_wbf=$sell4['charge_pct'];
											$prime_tax=($payrate * ($per_wbf/100));

											/////  for w2,1099 candidates without benefit tax='11-2'  /////
											if($est_id!=3 || $est_id!=6){
												if($benefit=="Without Benefits"|| $benefit=="Not Applicable"){
													if($est_id==1 || $est_id==4){
														$per_nbf='11';
													}
													if($est_id==5 || $est_id==2){
														 $per_nbf='2';
													}
													if($est_id==3 || $est_id==6){
														 $per_nbf='0';
													}
													$tax[]=($per_nbf/ 100) * $payrate;
												}
												if($benefit=="With Benefits"){
													$tax[]=$payrate*($per_wbf / 100);
												}
												$tax11=array_sum($tax);
												$tax_r=round($tax11,2);
											}else{
												$tax[]=0;
												$tax_r=array_sum($tax);
											}
										}

										if(($tax_r>0)&&($replace!='')&&($replace!=' ')&& ($est_id==1 || $est_id==4)){
											$tax_r1=($tax_r+($payrate*0.11));
											$tax_r=round($tax_r1,2);
										}
										if(($tax_r>0)&&($replace!='')&&($replace!=' ')&& ($est_id==5 || $est_id==2)){
											$tax_r2=($tax_r+($payrate*0.02));
											$tax_r=round($tax_r2,2);
										}

										/*echo $tax_r;
										echo "----------";*/

										//////////////////////////    MSP Fees    //////////////////////////

										$s_msp="SELECT mspChrg_pct,primechrg_pct,primeChrg_dlr,mspChrg_dlr FROM client_fees WHERE client_id=$clid1";
										$s_msp2=mysqli_query($vtechMappingdbConn,$s_msp);

										$row212=mysqli_fetch_assoc($s_msp2);
										$msp_charge=$row212['mspChrg_pct'];
										$prime_chrg=$row212['primechrg_pct'];
										$pcli_dlr=$row212['primeChrg_dlr'];
										$clmsp_dlr=$row212['mspChrg_dlr'];

										//////    msp    ///////////
										$mspfee=($msp_charge/100)*$billrate;
										$mspfee1=round($mspfee,2);
										$mspfee2=$mspfee1+$clmsp_dlr;

										/*echo $mspfee2;
										echo "----------";*/

										//////////////////////////    prime vendor fees    //////////////////////////

										$can_sql="SELECT c_primeCharge_pct,c_primeCharge_dlr,c_anyCharge_dlr FROM candidate_fees WHERE emp_id='$emp_eid'";
										$can_sql2=mysqli_query($vtechMappingdbConn,$can_sql);
										$row213=mysqli_fetch_assoc($can_sql2);

										$can_prime=$row213['c_primeCharge_pct'];
										$canprime_dlr=$row213['c_primeCharge_dlr'];
										$can_other=$row213['c_anyCharge_dlr'];

										$prime_chrg1=($prime_chrg / 100) * $billrate;
										$prime_chrg_can=($can_prime / 100) * $billrate;
										$prime_chrg2=$prime_chrg1+$prime_chrg_can+$canprime_dlr+$pcli_dlr;

										$prime_chrg21=round($prime_chrg2,2);

										/*echo $prime_chrg21;
										echo "----------";*/

										//////////////////////////    Rate for Candidate    //////////////////////////

										$rate_can2=$payrate+$tax_r+$mspfee2+$prime_chrg21;
										$rate_can3=round($rate_can2,2);

										/*echo $rate_can3;
										echo "---------"; */

										////////////////////////// Gross Margin //////////////////////////

										$g_margin=$billrate-$rate_can3;
										$g_margin2=round($g_margin,2);

										///////////////// Total Hours //////////////////////

										$tothr = 0;
										for($i=$start_dateX2;$i<=$end_dateX2;$i+=86400){
											$date_cur=date('Y-m-d', $i);
											$sql_q="SELECT time_start,time_end from employeetimeentry WHERE employee='$emp_eid' and date_format(date_start,'%Y-%m-%d')='$date_cur'";
											$exeqry_q = mysqli_query($vtechhrmConn,$sql_q);
											while($row_q = mysqli_fetch_array($exeqry_q)){
												$decimalHours = decimalHours($row_q['time_start']);
												$decimalHours2 = decimalHours($row_q['time_end']);
												$_hours=$decimalHours2 - $decimalHours ;
												$tothr += $_hours;
											}
										}

										//////////////Eligibility///////////////////
										$cur_date = strtotime(date("Y-m-d"));
										$third_mon_date = strtotime($main_row['joindateX'].' 3 month');
										$termi_date_chk = strtotime($main_row['termi_dateX']);
										if($main_row['status']=='Active'){
											if($cur_date>$third_mon_date){
												$eligibility="Yes";
											}else{
												$eligibility="No";
											}
										}else{
											if($termi_date_chk>$third_mon_date){
												$eligibility="Yes";
											}else{
												$eligibility="No";
											}

										}


										$enameX[]=$main_row['ename'];
										$cnameX[]=$main_row['cname'];
										$joindateX2[]=$main_row['joindate'];
										$termi_dateX[]=$main_row['termi_date'];
										$eligibilityX[]=$eligibility;
										$g_margin2X[]=$g_margin2;
										$tothrX[]=$tothr;
										$perHireExtraX[]=$perHireExtra;
										$newAccCrackAmtX[]=$newAccCrackAmt;
										$statusX2[]=$statusX;
									}
								}
								$main_query.=" GROUP BY recnm";
								$main_resultX=mysqli_query($vtechhrmConn, $main_query);
								$main_rowX=mysqli_fetch_array($main_resultX);
								$ridXXX=$main_rowX['rid'];
								///////////////////Compare JOin & Extension///////////////////////
								$totplace=$totext=array();

								$COMPAREquery1="SELECT
													cjsh.candidate_id AS totplace
												FROM
													candidate_joborder_status_history AS cjsh
												    JOIN candidate_joborder AS cj ON cj.candidate_id=cjsh.candidate_id AND cj.joborder_id=cjsh.joborder_id
												WHERE
													cj.added_by IN ({$ridXXX})
												AND
													cjsh.status_to='800'
												AND
													date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$firstday' AND '$lastday'";
								$COMPAREresult1=mysqli_query($catsConn,$COMPAREquery1);
								while($COMPARErow1=mysqli_fetch_array($COMPAREresult1)){
									$totplace[]=$COMPARErow1['totplace'];
								}
								$totjoinX=sizeof($totplace);
								$COMPAREquery2="SELECT
													cjsh.candidate_id AS totext
												FROM
													candidate_joborder_status_history AS cjsh
												    JOIN candidate_joborder AS cj ON cj.candidate_id=cjsh.candidate_id AND cj.joborder_id=cjsh.joborder_id
												WHERE
													cj.added_by IN ({$ridXXX})
												AND
													cjsh.status_from='800' AND cjsh.status_to='620'
												AND
													date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$firstday' AND '$lastday'";
								$COMPAREresult2=mysqli_query($catsConn,$COMPAREquery2);
								while($COMPARErow2=mysqli_fetch_array($COMPAREresult2)){
									$totext[]=$COMPARErow2['totext'];
								}
								$NEWtotjoinX=$NEWtotjoinXX='0';
								$NEWtotjoinX=sizeof(array_unique(array_diff($totplace,$totext)));
								if($NEWtotjoinX=='0'){
									$NEWtotjoinXX=$totjoinX;
								}else{
									$NEWtotjoinXX=$NEWtotjoinX;
								}

								array_multisort($tothrX, SORT_DESC, SORT_NATURAL, $enameX, $cnameX, $joindateX2, $termi_dateX, $eligibilityX, $g_margin2X, $perHireExtraX, $statusX2, $newAccCrackAmtX);
								$cmargin='0';
								$incAMTX=$enameX3=$cnameX2=array();
								foreach($enameX AS $key => $enameX2){
									if(($tothrX[$key]=='0' && $statusX2[$key]=='Active') || $tothrX[$key]>'0'){
										if($responseType == 0){
							?>
							<tr>
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($enameX[$key]); ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $cnameX[$key]; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $joindateX2[$key]; ?></td>
							<?php if($statusX2[$key]=="Active"){?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $statusX2[$key]; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $statusX2[$key]; ?></td>
							<?php } ?>
							<?php if($statusX2[$key]!="Active"){?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $termi_dateX[$key]; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;">---</td>
							<?php } ?>
							<?php if($eligibilityX[$key]=='Yes'){ ?>
								<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $eligibilityX[$key]; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $eligibilityX[$key]; ?></td>
							<?php } ?>
							<?php if($g_margin2X[$key]<=$minMarginVAL){?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $g_margin2X[$key]; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $g_margin2X[$key]; ?></td>
							<?php } ?>
							<?php
								if($eligibilityX[$key]=='Yes' && $g_margin2X[$key]>$minMarginVAL){
									$cmargin+=$g_margin2X[$key];
									//////////////////Incentive Percentage//////////////////////////
									$percX='0';
									$query2="SELECT * FROM incentive_criteria WHERE personnel='Recruiter' AND comment=''";
									$result2=mysqli_query($misReportsConn,$query2);
									while($rowX2=mysqli_fetch_array($result2)){
										if($cmargin>=$rowX2['min_margin'] && $cmargin<$rowX2['max_margin']){
											$percX=$rowX2['value'];
										}
										if($cmargin>=$rowX2['min_margin'] && $rowX2['max_margin']=='0'){
											$percX=$rowX2['value'];
										}
									}
							?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $cmargin; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $percX; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;">0</td>
								<td style="text-align: center;vertical-align: middle;">0</td>
							<?php } ?>
							<?php
							?>
								<td style="text-align: center;vertical-align: middle;"><?php echo round($tothrX[$key],2); ?></td>
							<?php
								$incAMT='0';
								if($eligibilityX[$key]=='Yes' && $g_margin2X[$key]>$minMarginVAL){
									$incAMT=round($g_margin2X[$key]*$percX/100*60*$tothrX[$key],2);
							?>
								<td style="text-align: center;vertical-align: middle;cursor: pointer;font-weight: bold;" data-toggle="tooltip" data-placement="auto" title="<?php echo $g_margin2X[$key]."*(".$percX."/100)*60*".$tothrX[$key]; ?>"><?php echo $incAMT; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;cursor: pointer;font-weight: bold;" data-toggle="tooltip" data-placement="auto" title="<?php echo $g_margin2X[$key]."*(0/100)*60*".$tothrX[$key]; ?>"><?php echo $incAMT; ?></td>
							<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $perHireExtraX[$key]; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $newAccCrackAmtX[$key]; ?></td>
							<?php
								if($eligibilityX[$key]=='Yes' && $g_margin2X[$key]>$minMarginVAL){
									$enameX3[]=$enameX[$key];
									$cnameX2[]=$cnameX[$key];
									$incAMTX[]=$incAMT+$perHireExtraX[$key]+$newAccCrackAmtX[$key];
							?>
								<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $incAMT+$perHireExtraX[$key]+$newAccCrackAmtX[$key]; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;"><?php echo $incAMT+$perHireExtraX[$key]+$newAccCrackAmtX[$key]; ?></td>
							<?php } ?>
							</tr>
							<?php
										}else{
											if($eligibilityX[$key]=='Yes' && $g_margin2X[$key]>$minMarginVAL){
												$cmargin+=$g_margin2X[$key];
												//////////////////Incentive Percentage//////////////////////////
												$percX='0';
												$query2="SELECT * FROM incentive_criteria WHERE personnel='Recruiter' AND comment=''";
												$result2=mysqli_query($misReportsConn,$query2);
												while($rowX2=mysqli_fetch_array($result2)){
													if($cmargin>=$rowX2['min_margin'] && $cmargin<$rowX2['max_margin']){
														$percX=$rowX2['value'];
													}
													if($cmargin>=$rowX2['min_margin'] && $rowX2['max_margin']=='0'){
														$percX=$rowX2['value'];
													}
												}
												$cmarginX=$cmargin;
												$percXX=$percX;
											}else{
												$cmarginX='0';
												$percXX='0';
											}
											$incAMT='0';

											if($eligibilityX[$key]=='Yes' && $g_margin2X[$key]>$minMarginVAL){
												$incAMT=round($g_margin2X[$key]*$percX/100*60*$tothrX[$key],2);
												$incAMTX2=$incAMT+$perHireExtraX[$key]+$newAccCrackAmtX[$key];
											} else {
												$incAMT = 0;
												$incAMTX2=$perHireExtraX[$key]+$newAccCrackAmtX[$key];
											}

											if($statusX2[$key]=='Active'){
												$termi_date2 = "---";
											}else{
												$termi_date2 = $termi_dateX[$key];
											}
											$responseArray[] = array('candidate' => ucwords($enameX[$key]),
												'client' => $cnameX[$key],
												'join_date' => $joindateX2[$key],
												'status' => $statusX2[$key],
												'termi_date' => $termi_date2,
												'eligibility' => $eligibilityX[$key],
												'margin' => $g_margin2X[$key],
												'cum_margin' => $cmarginX,
												'percentage' => $percXX,
												'total_hour' => round($tothrX[$key],2),
												'inc_amount' => $incAMT,
												'per_hire_amount' => $perHireExtraX[$key],
												'new_acc_amount' => $newAccCrackAmtX[$key],
												'final_amount' => $incAMTX2
											);
										}
									}
								}
								if($responseType == 0){
							?>
						</tbody>
						<tfoot>
							<tr style="background-color: #ccc;color: #000;font-size: 15px;">
								<th style="text-align: center;vertical-align: middle;"><?php echo sizeof(array_unique($enameX3)); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo sizeof(array_unique($cnameX2)); ?></th>
								<th style="text-align: center;vertical-align: middle;" colspan="11">Total No. of Join <span style="color: #2266AA;">(In this Quater)</span> : <?php echo $NEWtotjoinXX; ?></th>
							<?php if($NEWtotjoinXX>=$joinVAL){?>
								<th style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo array_sum($incAMTX); ?></th>
							<?php }else{ ?>
								<th style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;"><?php echo array_sum($incAMTX); ?></th>
							<?php } ?>
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
