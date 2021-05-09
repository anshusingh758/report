<?php
	include("../../../security.php");
	$responseArray = array();
	$responseType = isset($_REQUEST['response_type']) && $_REQUEST['response_type'] == 1 ? 1 : 0;

    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='39';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){

if ($responseType == 0) {

?>
<!DOCTYPE html>
<html>
<head>
	<title>Client CSM Incentive Report</title>

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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Client CSM Incentive Report</div>
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
					<div class="col-md-4">
						<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-8">
						<div class="row" style="margin-top: 25px;">
							<div class="col-md-6 col-md-offset-6">
								<label>Select Client Manager :</label>
								<select class="form-control" id="manager_name" name="manager_name[]" style="border: 1px solid #aaa;border-radius: 0px;" multiple required>
									<?php
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
											if($_REQUEST['manager_name2']==$userlist['manager_name']){
												$isSelected = ' selected';
											}else{
												$isSelected = '';
											}
											echo "<option value='".$userlist['manager_name']."'".$isSelected.">".$userlist['manager_name']."</option>";
										}
									?>
								</select>
							</div>
							<div class="col-md-3 col-md-offset-6" style="margin-top: 30px;">
								<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
							</div>
							<div class="col-md-3" style="margin-top: 30px;">
								<button type="submit" class="form-control" name="CSMIRsubmit" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<img src="<?php echo IMAGE_PATH; ?>/notice.png" class="pull-right">
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
}
	//Decimal Hours COUNT
	function decimalHours($time){
		$tms = explode(":", $time);
		return ($tms[0] + ($tms[1]/60) + ($tms[2]/3600));
	}
	//POST Overview Section
	if(isset($_REQUEST['CSMIRsubmit'])){
		$monthsdata=$_REQUEST['multimonth'];
		$managerdata=$_REQUEST['manager_name'];

		if($responseType == 0) {
?>
	<section id="CSMIRdatatable" class="hidden">
		<div class="container-fluid">
			<form id="CSMincentive" onsubmit="return true">
				<div class="row" style="margin-bottom: 50px;">
					<div class="col-md-10 col-md-offset-1">
						<table id="CSMIRdata" class="table table-striped table-bordered">
							<thead>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th class='no-sort' style="text-align: center;vertical-align: middle;" rowspan="2"><input style="height:20px;width:20px;cursor: pointer;outline: none;" type='checkbox' name='select_all' id='select_all'></th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Client Manager</th>
									<th style="text-align: center;vertical-align: middle;" colspan="4">Total</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Amount</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th style="text-align: center;vertical-align: middle;">Recruiter</th>
									<th style="text-align: center;vertical-align: middle;">Candidate</th>
									<th style="text-align: center;vertical-align: middle;">Join (This Month)</th>
									<th style="text-align: center;vertical-align: middle;">Incentive Amount</th>
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

									$third_month = strtotime($sdateX.' -3 month');
									$last_month = strtotime($sdateX.' -1 month');

									$first_date = date('Y-m-01', $third_month);
									$last_date = date('Y-m-t', $last_month);

									$start_dateX=date('Y/m/01',strtotime($dt));
									$end_dateX=date('Y/m/t',strtotime($dt));
									$start_dateX2=strtotime($start_dateX);
									$end_dateX2=strtotime($end_dateX);

									///////Finding basic Criteria(LIMIT, #of join, min margin) START//////
									$limitQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Limit (in Month)'");
									$limitROW=mysqli_fetch_array($limitQRY);
									$limitVAL=$limitROW['value'];

									$minMarginQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Minimum Margin'");
									$minMarginROW=mysqli_fetch_array($minMarginQRY);
									$minMarginVAL=$minMarginROW['value'];

									$joinQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Join this month'");
									$joinROW=mysqli_fetch_array($joinQRY);
									$joinVAL=$joinROW['value'];
									///////Finding basic Criteria(LIMIT, #of join, min margin) END//////

									$iii=0;
									foreach($managerdata AS $mandata){
										$queryX="SELECT
													user_id AS user_id
												FROM
													user
												WHERE
													notes = '$mandata'
												GROUP BY user_id
												ORDER BY user_id ASC";
										$resultX=mysqli_query($catsConn,$queryX);
										$user_idX=array();
										$finaljoinX=array();
										while($rowX=mysqli_fetch_array($resultX)){
											$user_idX[]=$rowX['user_id'];

											$recid=$rowX['user_id'];

											///////////////////Compare JOin & Extension///////////////////////
											$totplace=$totext=array();

											$COMPAREquery1="SELECT
																cjsh.candidate_id AS totplace
															FROM
																candidate_joborder_status_history AS cjsh
															    JOIN candidate_joborder AS cj ON cj.candidate_id=cjsh.candidate_id AND cj.joborder_id=cjsh.joborder_id
															WHERE
																cj.added_by IN ({$recid})
															AND
																cjsh.status_to='800'
															AND
																date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$sdateX' AND '$tdateX'";
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
																cj.added_by IN ({$recid})
															AND
																cjsh.status_from='800' AND cjsh.status_to='620'
															AND
																date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$sdateX' AND '$tdateX'";
											$COMPAREresult2=mysqli_query($catsConn,$COMPAREquery2);
											while($COMPARErow2=mysqli_fetch_array($COMPAREresult2)){
												$totext[]=$COMPARErow2['totext'];
											}
											$finaljoin='0';
											$finaljoin=sizeof(array_unique(array_diff($totplace,$totext)));
											if($finaljoin=='0'){
												$finaljoinX[]=$totjoinX;
											}else{
												$finaljoinX[]=$finaljoin;
											}

										}
										$ridX=implode(",", $user_idX);

										$queryXX="SELECT user_id AS mid FROM user WHERE concat(first_name,' ',last_name)='$mandata'";
										$resultXX=mysqli_query($catsConn, $queryXX);
										$rowXX=mysqli_fetch_array($resultXX);
										$midX='';
										$midX=$rowXX['mid'];

										$main_query="SELECT
														u.user_id AS rid,
														concat(u.first_name,' ',u.last_name) AS recnm,
														u.notes AS mannm,
														emp.id AS eid,
														emp.status,
														concat(emp.first_name,' ',emp.last_name) AS ename,
														date_format(emp.custom7, '%m-%d-%Y') AS joindate,
														date_format(emp.custom7, '%Y-%m-%d') AS joindateX,
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
														comp.owner='$midX'
													AND
														date_format(emp.custom7, '%Y-%m-%d')<='$tdateX'
													AND
														year(emp.custom7)>'2017'
													AND
														TIMESTAMPDIFF(MONTH, date_format(emp.custom7, '%Y-%m-%d'), '$tdateX')<='$limitVAL'";
										$main_result=mysqli_query($vtechhrmConn,$main_query);

										$recnm=$ename=$cname=$mannmX=$statusX=$joindate=$termi_date=$tothrX=$eligibility=$g_margin3=array();

										if(mysqli_num_rows($main_result)>0){
											while($main_row=mysqli_fetch_array($main_result)){
												$recnm[]=$main_row['recnm'];
												$ename[]=$main_row['ename'];
												$cname[]=$main_row['cname'];
												$statusX[]=$main_row['status'];
												$joindate[]=$main_row['joindate'];
												$termi_date[]=$main_row['termi_date'];

												$ridXX=$main_row['rid'];
												$clid1=$main_row['cid'];
												$emp_eid=$main_row['eid'];
												$billrate=$main_row['billrate'];
												$payrate=$main_row['payrate'];
												$est_id=$main_row['es_id'];
												$emptype=$main_row['employment_type'];
												$benefit=$main_row['benefit'];

												$bfli=$main_row['benefitlist'];
												$delimiter=array("","[","]",'"');
												$replace=str_replace($delimiter, $delimiter[0], $bfli);
												$explode=explode(" ",$replace);
												$benefitlist=$replace;

												$tax_r=$mspfee2=$prime_chrg21=$rate_can3=$g_margin=$g_margin2=0;

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
												$g_margin3[]=$g_margin2;

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
												$tothrX[]=$tothr;

												//////////////Eligibility///////////////////
												$cur_date = strtotime(date("Y-m-d"));
												$third_mon_date = strtotime($main_row['joindateX'].' 3 month');
												$termi_date_chk = strtotime($main_row['termi_dateX']);
												if($main_row['status']=='Active'){
													if($cur_date>$third_mon_date){
														$eligibility[]="Yes";
													}else{
														$eligibility[]="No";
													}
												}else{
													if($termi_date_chk>$third_mon_date){
														$eligibility[]="Yes";
													}else{
														$eligibility[]="No";
													}
												}
											}
										}
										$g_marginX=round(array_sum($g_margin3),2);

										$incAMTX=$cmargin='0';
										$enameX=$cnameX=$recnmXX=array();

										array_multisort($tothrX, SORT_DESC, SORT_NATURAL, $recnm, $ename, $cname, $mannmX, $statusX, $joindate, $termi_date, $eligibility, $g_margin3);

										foreach($recnm AS $key => $recnmX){
											if(($tothrX[$key]=='0' && $statusX[$key]=='Active') || $tothrX[$key]>'0'){
												if($eligibility[$key]=='Yes' && $g_margin3[$key]>$minMarginVAL){
													$cmargin+=$g_margin3[$key];
												}
												//////////////////Incentive Percentage//////////////////////////
												$percX='0';
												$query2="SELECT * FROM incentive_criteria WHERE personnel='CS Manager' AND comment=''";
												$result2=mysqli_query($misReportsConn,$query2);
												while($rowX2=mysqli_fetch_array($result2)){
													if($cmargin>=$rowX2['min_margin'] && $cmargin<$rowX2['max_margin']){
														$percX=$rowX2['value'];
													}
													if($cmargin>=$rowX2['min_margin'] && $rowX2['max_margin']=='0'){
														$percX=$rowX2['value'];
													}
												}

												$incAMT=round($g_margin3[$key]*$percX/100*60*$tothrX[$key],2);
												if($g_margin3[$key] <= $minMarginVAL || $eligibility[$key]=='No'){
												}else{
													$recnmXX[]=$recnm[$key];
													$enameX[]=$ename[$key];
													$cnameX[]=$cname[$key];
													$incAMTX+=$incAMT;
												}
											}
										}
										$selectQRY="SELECT * FROM incentive_data WHERE person_id='$midX' AND period='$dtX' AND type = 'CS Manager'";
										$selectRES=mysqli_query($misReportsConn, $selectQRY);
										if(mysqli_num_rows($selectRES)>0){
											while($selectROW=mysqli_fetch_array($selectRES)){
												if($responseType == 0) {
								?>
								<tr style="background-color: #c3dcf4;">
									<td style="text-align: center;vertical-align: middle;"><i class="fa fa-lock" style="font-size: 18px;color: #2266AA;"></i></td>
									<td style="text-align: left;vertical-align: middle;"><?php echo $selectROW['person_name']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['total_recruiter']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['total_candidate']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['total_join']; ?></td>
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
									<td style="text-align: center;vertical-align: middle;"><input style="height:18px;width:18px;cursor:pointer;outline: none;" type="checkbox" class="checkboxes" name="checked_id[<?php echo $iii; ?>]" id="checked_id" value="<?php echo $midX; ?>"></td>
									<input type="hidden" name="person_id[<?php echo $iii; ?>]" value="<?php echo $midX; ?>">
									<input type="hidden" name="person_name[<?php echo $iii; ?>]" value="<?php echo $mandata; ?>">
									<input type="hidden" name="typedata[<?php echo $iii; ?>]" value="<?php echo "CS Manager"; ?>">
									<input type="hidden" name="total_recruiter[<?php echo $iii; ?>]" value="<?php echo sizeof(array_unique($recnmXX)); ?>">
									<input type="hidden" name="total_candidate[<?php echo $iii; ?>]" value="<?php echo sizeof(array_unique($enameX)); ?>">
									<input type="hidden" name="total_join[<?php echo $iii; ?>]" value="<?php echo array_sum($finaljoinX); ?>">
								<?php if(array_sum($finaljoinX)<$joinVAL){ ?>
									<input type="hidden" id="mainamount<?php echo $iii; ?>" value="<?php echo "0"; ?>">
								<?php }else{ ?>
									<input type="hidden" id="mainamount<?php echo $iii; ?>" value="<?php echo $incAMTX; ?>">
								<?php } ?>
									<input type="hidden" name="final_incentive[<?php echo $iii; ?>]" value="<?php echo $incAMTX; ?>">
									<input type="hidden" name="period[<?php echo $iii; ?>]" value="<?php echo $dtX; ?>">

									<input type="hidden" name="detail_link[<?php echo $iii; ?>]" value="<?php echo LOCAL_REPORT_PATH; ?>/incentive/client_csm_incentive_report/index.php?multimonth=<?php echo urlencode($dtX); ?>&manager_name2=<?php echo urlencode($mandata); ?>&CSMIRsubmit2=&response_type=1">


									<td style="text-align: left;vertical-align: middle;"><a href="?multimonth=<?php echo $dtX; ?>&manager_name2=<?php echo $mandata; ?>&CSMIRsubmit2=" style="cursor: pointer;"><?php echo $mandata; ?></a></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo sizeof(array_unique($recnmXX)); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo sizeof(array_unique($enameX)); ?></td>
								<?php if(array_sum($finaljoinX)<$joinVAL){ ?>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo array_sum($finaljoinX);?></td>
								<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($finaljoinX);?></td>
								<?php } ?>
								<?php if(array_sum($finaljoinX)<$joinVAL){ ?>
									<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;"><?php echo $incAMTX; ?></td>
								<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $incAMTX; ?></td>
								<?php } ?>
									<td style="text-align: center;vertical-align: middle;">
										<select id="adjustment_method<?php echo $iii; ?>" name="adjustment_method[<?php echo $iii; ?>]" style="padding: 5px;cursor: pointer;" onchange="adjustMETHOD<?php echo $iii; ?>(this.value)" required>
											<option value="plus">ADD (+)</option>
											<option value="minus">SUB (-)</option>
										</select>
									</td>
									<td style="text-align: center;vertical-align: middle;">
										<input type="text" id="adjustment_amount<?php echo $iii; ?>" name="adjustment_amount[<?php echo $iii; ?>]" onchange="adjustAMT<?php echo $iii; ?>(this.value)" maxlength="10" onkeypress='return event.charCode >= 46 && event.charCode <= 57' placeholder="0" style="width: 70%;padding: 2px 5px;">
									</td>
									<td style="text-align: center;vertical-align: middle;"><textarea name="adjustment_comment[<?php echo $iii; ?>]" rows="1" autocomplete="off"></textarea></td>
								<?php if(array_sum($finaljoinX)<$joinVAL){ ?>
									<td style="text-align: center;vertical-align: middle;"><input type="text" id="final_amount<?php echo $iii; ?>" name="final_amount[<?php echo $iii; ?>]" style="background-color: #fff;color: #000;width: 70%;padding: 2px 5px;border: none;text-align: center;" value="<?php echo "0"; ?>" readonly></td>
								<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;"><input type="text" id="final_amount<?php echo $iii; ?>" name="final_amount[<?php echo $iii; ?>]" style="background-color: #fff;color: #000;width: 70%;padding: 2px 5px;border: none;text-align: center;" value="<?php echo $incAMTX; ?>" readonly></td>
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
											$iii++;
										}
										}
									}

									if ($responseType == 0) {
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
	if(isset($_REQUEST['CSMIRsubmit2'])){
		$monthsdata=$_REQUEST['multimonth'];
		$managerdata=$_REQUEST['manager_name2'];
		if ($responseType == 0) {
?>
	<section id="CSMIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-4 col-md-offset-4" style="background-color: #ccc;color: #000;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">Client CS Manager : <span style="font-size: 16px;color: #333;"><?php echo $managerdata; ?></span><span style="font-size: 15px;color: #449D44;"><?php echo " (".$monthsdata.")"; ?></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="CSMIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;">Recruiter</th>
								<th style="text-align: center;vertical-align: middle;">Recruiter Manager</th>
								<th style="text-align: center;vertical-align: middle;">Candidate</th>
								<th style="text-align: center;vertical-align: middle;">Client</th>
								<th style="text-align: center;vertical-align: middle;">Joining Date</th>
								<th style="text-align: center;vertical-align: middle;">Status</th>
								<th style="text-align: center;vertical-align: middle;">Termination Date</th>
								<th style="text-align: center;vertical-align: middle;">3 Months<br>Completed</th>
								<th style="text-align: center;vertical-align: middle;">Margin</th>
								<th style="text-align: center;vertical-align: middle;">Cumulative Margin</th>
								<th style="text-align: center;vertical-align: middle;">Percentage (%)</th>
								<th style="text-align: center;vertical-align: middle;">Total Hours</th>
								<th style="text-align: center;vertical-align: middle;" data-toggle="tooltip" data-placement="auto" title="Margin * (Percentage/ 100)*60*Total Hours">Incentive Amount</th>
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

								$third_month = strtotime($sdateX.' -3 month');
								$last_month = strtotime($sdateX.' -1 month');

								$first_date = date('Y-m-01', $third_month);
								$last_date = date('Y-m-t', $last_month);

								$start_dateX=date('Y/m/01',strtotime($dt));
								$end_dateX=date('Y/m/t',strtotime($dt));
								$start_dateX2=strtotime($start_dateX);
								$end_dateX2=strtotime($end_dateX);

								///////Finding basic Criteria(LIMIT, #of join, min margin) START//////
								$limitQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Limit (in Month)'");
								$limitROW=mysqli_fetch_array($limitQRY);
								$limitVAL=$limitROW['value'];

								$minMarginQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Minimum Margin'");
								$minMarginROW=mysqli_fetch_array($minMarginQRY);
								$minMarginVAL=$minMarginROW['value'];

								$joinQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Join this month'");
								$joinROW=mysqli_fetch_array($joinQRY);
								$joinVAL=$joinROW['value'];
								///////Finding basic Criteria(LIMIT, #of join, min margin) END//////

								$queryX="SELECT
											user_id AS user_id
										FROM
											user
										WHERE
											notes = '$managerdata'
										GROUP BY user_id
										ORDER BY user_id ASC";
								$resultX=mysqli_query($catsConn,$queryX);
								$user_idX=array();
								$finaljoinX=array();
								while($rowX=mysqli_fetch_array($resultX)){
									$user_idX[]=$rowX['user_id'];

									$recid=$rowX['user_id'];

									///////////////////Compare JOin & Extension///////////////////////
									$totplace=$totext=array();

									$COMPAREquery1="SELECT
														cjsh.candidate_id AS totplace
													FROM
														candidate_joborder_status_history AS cjsh
													    JOIN candidate_joborder AS cj ON cj.candidate_id=cjsh.candidate_id AND cj.joborder_id=cjsh.joborder_id
													WHERE
														cj.added_by IN ({$recid})
													AND
														cjsh.status_to='800'
													AND
														date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$sdateX' AND '$tdateX'";
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
														cj.added_by IN ({$recid})
													AND
														cjsh.status_from='800' AND cjsh.status_to='620'
													AND
														date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$sdateX' AND '$tdateX'";
									$COMPAREresult2=mysqli_query($catsConn,$COMPAREquery2);
									while($COMPARErow2=mysqli_fetch_array($COMPAREresult2)){
										$totext[]=$COMPARErow2['totext'];
									}
									$finaljoin='0';
									$finaljoin=sizeof(array_unique(array_diff($totplace,$totext)));
									if($finaljoin=='0'){
										$finaljoinX[]=$totjoinX;
									}else{
										$finaljoinX[]=$finaljoin;
									}

								}
								$ridX=implode(",", $user_idX);

								$queryXX="SELECT user_id AS mid FROM user WHERE concat(first_name,' ',last_name)='$managerdata'";
								$resultXX=mysqli_query($catsConn, $queryXX);
								$rowXX=mysqli_fetch_array($resultXX);
								$midX=$rowXX['mid'];

								$main_query="SELECT
											u.user_id AS rid,
										    concat(u.first_name,' ',u.last_name) AS recnm,
										    u.notes AS mannm,
											emp.id AS eid,
											emp.status,
										    concat(emp.first_name,' ',emp.last_name) AS ename,
										    date_format(emp.custom7, '%m-%d-%Y') AS joindate,
											date_format(emp.custom7, '%Y-%m-%d') AS joindateX,
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
											comp.owner='$midX'
										AND
											date_format(emp.custom7, '%Y-%m-%d')<='$tdateX'
										AND
											year(emp.custom7)>'2017'
										AND
											TIMESTAMPDIFF(MONTH, date_format(emp.custom7, '%Y-%m-%d'), '$tdateX')<='$limitVAL'";
								$main_result=mysqli_query($vtechhrmConn,$main_query);

								$recnm=$ename=$cname=$mannmX=$statusX=$joindate=$termi_date=$tothrX=$eligibility=$g_margin3=array();

								if(mysqli_num_rows($main_result)>0){
									while($main_row=mysqli_fetch_array($main_result)){
										$recnm[]=$main_row['recnm'];
										$ename[]=$main_row['ename'];
										$cname[]=$main_row['cname'];
										$statusX[]=$main_row['status'];
										$joindate[]=$main_row['joindate'];
										$termi_date[]=$main_row['termi_date'];

										$ridXX=$main_row['rid'];
										$mannmQRY=mysqli_query($catsConn,"SELECT notes FROM user WHERE user_id='$ridXX'");
										$mannmROW=mysqli_fetch_array($mannmQRY);
										$mannmX[]=$mannmROW['notes'];

										$clid1=$main_row['cid'];
										$emp_eid=$main_row['eid'];
										$billrate=$main_row['billrate'];
										$payrate=$main_row['payrate'];
										$est_id=$main_row['es_id'];
										$emptype=$main_row['employment_type'];
										$benefit=$main_row['benefit'];

										$bfli=$main_row['benefitlist'];
										$delimiter=array("","[","]",'"');
										$replace=str_replace($delimiter, $delimiter[0], $bfli);
										$explode=explode(" ",$replace);
										$benefitlist=$replace;

										$tax_r=$mspfee2=$prime_chrg21=$rate_can3=$g_margin=$g_margin2=0;

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
										$g_margin3[]=$g_margin2;

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
										$tothrX[]=$tothr;

										//////////////Eligibility///////////////////
										$cur_date = strtotime(date("Y-m-d"));
										$third_mon_date = strtotime($main_row['joindateX'].' 3 month');
										$termi_date_chk = strtotime($main_row['termi_dateX']);
										if($main_row['status']=='Active'){
											if($cur_date>$third_mon_date){
												$eligibility[]="Yes";
											}else{
												$eligibility[]="No";
											}
										}else{
											if($termi_date_chk>$third_mon_date){
												$eligibility[]="Yes";
											}else{
												$eligibility[]="No";
											}
										}
									}
								}
								$g_marginX=round(array_sum($g_margin3),2);

								$incAMTX=$cmargin='0';
								$enameX=$cnameX=$recnmXX=array();
								array_multisort($tothrX, SORT_DESC, SORT_NATURAL, $recnm, $ename, $cname, $mannmX, $statusX, $joindate, $termi_date, $eligibility, $g_margin3);
								foreach($recnm AS $key => $recnmX){
									if(($tothrX[$key]=='0' && $statusX[$key]=='Active') || $tothrX[$key]>'0'){

										if ($responseType == 0) {
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><?php echo $recnm[$key]; ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $mannmX[$key]; ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $ename[$key]; ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $cname[$key]; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $joindate[$key]; ?></td>
							<?php if($statusX[$key]=="Active"){?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $statusX[$key]; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $statusX[$key]; ?></td>
							<?php } ?>
							<?php if($statusX[$key]!="Active"){?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $termi_date[$key]; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;">---</td>
							<?php } ?>
							<?php if($eligibility[$key]=="Yes"){ ?>
								<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $eligibility[$key]; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $eligibility[$key]; ?></td>
							<?php } ?>
							<?php if($g_margin3[$key] <= $minMarginVAL){
							?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $g_margin3[$key]; ?></td>
							<?php }else{
							?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $g_margin3[$key]; ?></td>
							<?php } ?>
							<?php
								if($eligibility[$key]=='Yes' && $g_margin3[$key]>$minMarginVAL){
									$cmargin+=$g_margin3[$key];
							?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $cmargin; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;">0</td>
							<?php } ?>
							<?php
								//////////////////Incentive Percentage//////////////////////////
								$percX='0';
								$query2="SELECT * FROM incentive_criteria WHERE personnel='CS Manager' AND comment=''";
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
							<?php
								$incAMT='0';
								if($eligibility[$key]=='Yes' && $g_margin3[$key]>$minMarginVAL){
									$incAMT=round($g_margin3[$key]*$percX/100*60*$tothrX[$key],2);
							?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $percX."%"; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;">0%</td>
							<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo round($tothrX[$key],2);?></td>
							<?php
								if($g_margin3[$key] <= $minMarginVAL || $eligibility[$key]=='No'){
							?>
								<td style="text-align: center;vertical-align: middle;font-size: 15px;background-color: #fc2828;color: #fff;cursor: pointer;"><?php echo $incAMT; ?></td>
							<?php
								}else{
									$recnmXX[]=$recnm[$key];
									$enameX[]=$ename[$key];
									$cnameX[]=$cname[$key];
									$incAMTX+=$incAMT;
							?>
								<td style="text-align: center;vertical-align: middle;font-size: 15px;background-color: #449D44;color: #fff;cursor: pointer;" data-toggle="tooltip" data-placement="auto" title="<?php echo $g_margin3[$key]."*(".$percX."/100)*60*".$tothrX[$key]; ?>"><?php echo $incAMT; ?></td>
							<?php } ?>
							</tr>
							<?php
						} else {
							if($eligibility[$key]=='Yes' && $g_margin3[$key]>$minMarginVAL){
								$cmargin+=$g_margin3[$key];
								//////////////////Incentive Percentage//////////////////////////
								$percX='0';
								$query2="SELECT * FROM incentive_criteria WHERE personnel='CS Manager' AND comment=''";
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
							if($eligibility[$key]=='Yes' && $g_margin3[$key]>$minMarginVAL){
								$incAMT=round($g_margin3[$key]*$percX/100*60*$tothrX[$key],2);
							}
							if($statusX[$key]=='Active'){
								$termi_date2 = "---";
							}else{
								$termi_date2 = $termi_date[$key];
							}
							$responseArray[] = array('recruiter' => ucwords($recnm[$key]),
								'recruiter_manager' => ucwords($mannmX[$key]),
								'candidate' => ucwords($ename[$key]),
								'client' => $cname[$key],
								'join_date' => $joindate[$key],
								'status' => $statusX[$key],
								'termi_date' => $termi_date2,
								'eligibility' => $eligibility[$key],
								'margin' => $g_margin3[$key],
								'cum_margin' => $cmarginX,
								'percentage' => $percXX,
								'total_hour' => round($tothrX[$key],2),
								'inc_amount' => $incAMT);
						}
									}
								}
								if ($responseType == 0) {
							?>
						</tbody>
						<tfoot>
							<tr>
								<td style="text-align: center;vertical-align: middle;font-size: 15px;font-weight: bold;"><?php echo sizeof(array_unique($recnmXX)); ?></td>
								<td style="text-align: center;vertical-align: middle;"></td>
								<td style="text-align: center;vertical-align: middle;font-size: 15px;font-weight: bold;"><?php echo sizeof(array_unique($enameX)); ?></td>
								<td style="text-align: center;vertical-align: middle;font-size: 15px;font-weight: bold;"><?php echo sizeof(array_unique($cnameX)); ?></td>
								<td style="text-align: center;vertical-align: middle;font-size: 16px;font-weight: bold;" colspan="8">Total No. of Join : <?php echo array_sum($finaljoinX); ?></td>
							<?php if(array_sum($finaljoinX)<$joinVAL){ ?>
								<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;font-size: 15px;"><?php echo $incAMTX; ?></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;font-size: 15px;"><?php echo $incAMTX; ?></td>
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
	//POST Detail Section END
	if ($responseType == 0) {
?>

</body>
</html>
<?php
} else {
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
