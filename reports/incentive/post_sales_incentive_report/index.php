<?php
	include("../../../security.php");

	$responseArray = array();
	$responseType = isset($_REQUEST['response_type']) && $_REQUEST['response_type'] == 1 ? 1 : 0;

    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='4';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){
			if($responseType == 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Post Sales Incentive Report</title>

	<?php
		include('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th{
			padding: 5px 2px;
		}
		table.dataTable tbody td,
		table.dataTable tfoot th{
			padding: 2px;
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
			$('#PSIRdatatable').removeClass("hidden");

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

			/*Select Post Sales Personnel START*/
			$('#ps_name').multiselect({
				nonSelectedText: 'Select PS Personnel',
				numberDisplayed: 1,
				enableFiltering:true,
				enableCaseInsensitiveFiltering:true,
				buttonWidth:'100%',
				includeSelectAllOption: true,
 				maxHeight: 300
			});
			$("#ps_name").multiselect('selectAll', false);
	        $("#ps_name").multiselect('updateButtonText');

			$('#ps_name2').multiselect({
				nonSelectedText: 'Select PS Personnel',
				numberDisplayed: 1,
				enableFiltering:true,
				enableCaseInsensitiveFiltering:true,
				buttonWidth:'100%',
				includeSelectAllOption: true,
 				maxHeight: 300
			});
			/*Select Post Sales Personnel END*/

			/*Datatable Calling START*/
			var tableX = $('#PSIRdata').DataTable({
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
		        	$('div.dataTables_filter input').css("width","180")
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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Post Sales Incentive Report</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section id="MainSection" class="hidden" style="margin-top: 20px;margin-bottom: 100px;">
		<div class="container">
			<!--POST Overview Section-->
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
								<label>Select PS Personnel :</label>
								<select id="ps_name" name="ps_name[]" multiple required>
									<?php
										$sqluser2="SELECT value as bdmname FROM extra_field WHERE field_name='Inside Post Sales' AND value != '' AND value != ' ' AND value IS NOT NULL GROUP BY bdmname ORDER BY bdmname ASC";
										$resultuser2=mysqli_query($catsConn,$sqluser2);
										while($userlist2=mysqli_fetch_array($resultuser2)){
											echo "<option value='".$userlist2['bdmname']."'>".ucwords($userlist2['bdmname'])."</option>";
										}
									?>
								</select>
							</div>
							<div class="col-md-3 col-md-offset-6" style="margin-top: 35px;">
								<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
							</div>
							<div class="col-md-3" style="margin-top: 35px;">
								<button type="submit" class="form-control" name="PSIRsubmit1" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
							</div>
						</div>
					</div>
					<div class="col-md-3 col-md-offset-1">
						<img src="<?php echo IMAGE_PATH; ?>/noticex.png" style="bottom: 0px;" class="bottom-right">
					</div>
				</div>
			</form>
		</div>
	</section>

<!--POST Overview Section-->
<?php
			}
	if(isset($_REQUEST['PSIRsubmit1'])){
		$monthsdata=$_REQUEST['multimonth'];
		$psdata=$_REQUEST['ps_name'];
		if($responseType == 0){
?>
	<section id="PSIRdatatable" class="hidden">
		<div class="container-fluid">
			<form id="PSincentive" onsubmit="return true">
				<div class="row" style="margin-bottom: 50px;">
					<div class="col-md-12">
						<table id="PSIRdata" class="table table-striped table-bordered">
							<thead>
								<tr style="background-color: #bbb;color: #000;font-size: 12px;">
									<th class='no-sort' style="text-align: center;vertical-align: middle;" rowspan="3"><input style="height:20px;width:20px;cursor: pointer;outline: none;" type='checkbox' name='select_all' id='select_all'></th>
									<th style="text-align: center;vertical-align: middle;" rowspan="3">PS Personnel</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Total</th>
									<th style="text-align: center;vertical-align: middle;" colspan="4">Fix Incentive</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="3">Final Incentive</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="3">Final Amount</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 12px;">
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Margin</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Incentive Amount</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Joborder Type</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">New Client</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Method</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Amount</th>
									<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;" rowspan="2">Comment</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 12px;">
									<th style="text-align: center;vertical-align: middle;">Exclusive</th>
									<th style="text-align: center;vertical-align: middle;">Direct</th>
									<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Pass Through</th>
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

									$start_dateX=date('m/01/Y',strtotime($dt));
									$end_dateX=date('m/t/Y',strtotime($dt));
									$start_dateX2=strtotime($start_dateX);
									$end_dateX2=strtotime($end_dateX);

									$iii='0';
									foreach($psdata AS $psdataX){
										$psidQRY=mysqli_query($catsConn, "SELECT user_id FROM user WHERE concat(first_name,' ',last_name)='$psdataX'");
										$psidROW=mysqli_fetch_array($psidQRY);
										$psidX=$psidROW['user_id'];
										$main_query="SELECT
													ef.value AS ipsnm,
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
													es.name AS employment_type,
													mp.c_joborder_id AS jid
												FROM
													employees AS emp
												    JOIN vtechhrm.employmentstatus AS es ON es.id=emp.employment_status
												    JOIN vtech_mappingdb.system_integration AS mp ON mp.h_employee_id=emp.id
												    JOIN cats.company AS comp ON comp.company_id=mp.c_company_id
												    JOIN cats.extra_field AS ef ON ef.data_item_id=comp.company_id
												WHERE
													ef.field_name='Inside Post Sales'
												AND
													ef.value = '$psdataX'
												AND
													date_format(emp.custom7, '%Y-%m-%d') BETWEEN '$sdateX' AND '$tdateX'";
										$main_result=mysqli_query($vtechhrmConn,$main_query);

										$enameX=$cnameX=$joindateX=$termi_dateX=$statusX=$eligibility=$g_margin3=$newAccCrackAmt=$exReqAmt=$directAmt=$ptAmt=array();

										if(mysqli_num_rows($main_result)>0){
											while($main_row=mysqli_fetch_array($main_result)){
												$enameX[]=$main_row['ename'];
												$cnameX[]=$main_row['cname'];
												$joindateX[]=$main_row['joindate'];
												$termi_dateX[]=$main_row['termi_date'];
												$statusX[]=$main_row['status'];

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

												$clid1=$main_row['cid'];
												$emp_eid=$main_row['eid'];

												$nacaQRY=mysqli_query($vtechMappingdbConn, "SELECT id FROM system_integration WHERE h_employee_id='$emp_eid' AND c_company_id='$clid1'");
												$nacaROW=mysqli_fetch_array($nacaQRY);

												$nacaQRY2=mysqli_query($vtechMappingdbConn, "SELECT MIN(id) AS minid FROM system_integration WHERE c_company_id='$clid1'");
												$nacaROW2=mysqli_fetch_array($nacaQRY2);
												if(($nacaROW2['minid']==$nacaROW['id']) && ($main_row['joinmonth']==$month)){
													$pheQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Post Sales' AND comment='New Clients'");
													$pheROW=mysqli_fetch_array($pheQRY);
													$newAccCrackAmt[]=$pheROW['value'];
												}else{
													$newAccCrackAmt[]='0';
												}

												$jidX=$main_row['jid'];
												$jobtypeQRY=mysqli_query($catsConn,"SELECT ic.comment AS type,ic.value AS fixamt FROM extra_field AS ef JOIN mis_reports.incentive_criteria AS ic ON ic.comment=ef.value WHERE field_name='Joborder Type' AND data_item_id='$jidX'");
												if($jobtypeROW=mysqli_fetch_array($jobtypeQRY)){
													if($jobtypeROW['type']=='Exclusive Req Hire'){
														$exReqAmt[]=$jobtypeROW['fixamt'];
													}elseif($jobtypeROW['type']=='Direct Req'){
														$directAmt[]=$jobtypeROW['fixamt'];
													}elseif($jobtypeROW['type']=='Pass Through'){
														$ptAmt[]=$jobtypeROW['fixamt'];
													}else{
														$exReqAmt[]='0';
														$directAmt[]='0';
														$ptAmt[]='0';
													}
												}else{
													$exReqAmt[]='0';
													$directAmt[]='0';
													$ptAmt[]='0';
												}


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
												$g_margin3[]=$g_margin2;
											}
										}
										$totCAN=$totGP=$exReqAmtX=$directAmtX=$ptAmtX=$newAccCrackAmtX='0';
										$totCOMP=array();
										foreach($enameX AS $key => $enameX2){
											if($g_margin3[$key] <= '4' || $eligibility[$key]=="No"){
											}else{
												$totCAN++;
												$totCOMP[]=$cnameX[$key];
												$totGP+=$g_margin3[$key];
												$exReqAmtX+=$exReqAmt[$key];
												$directAmtX+=$directAmt[$key];
												$ptAmtX+=$ptAmt[$key];
												$newAccCrackAmtX+=$newAccCrackAmt[$key];
											}
										}
										/////////Incentive Amount//////////////////
										$iamountQ="SELECT * FROM incentive_criteria WHERE personnel='Post Sales' AND comment=''";
										$iamountR=mysqli_query($misReportsConn,$iamountQ);
										$iamountV='0';
										while($iamountD=mysqli_fetch_array($iamountR)){
											if($totGP>=$iamountD['min_margin'] && $totGP<$iamountD['max_margin']){
												$iamountV=$iamountD['value'];
											}elseif($totGP>=$iamountD['min_margin'] && $iamountD['max_margin']=='0'){
												$iamountV=$iamountD['value'];
											}
										}
										$selectQRY="SELECT * FROM incentive_data WHERE person_name='$psdataX' AND period='$dtX' AND type = 'Post Sales'";
										$selectRES=mysqli_query($misReportsConn, $selectQRY);
										if(mysqli_num_rows($selectRES)>0){
											while($selectROW=mysqli_fetch_array($selectRES)){
												if($responseType == 0){
								?>
								<tr style="background-color: #c3dcf4;">
									<td style="text-align: center;vertical-align: middle;"><i class="fa fa-lock" style="font-size: 18px;color: #2266AA;"></i></td>
									<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($selectROW['person_name']); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['total_candidate']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['total_margin']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['incentive_amount']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['ps_exclusive_req']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['ps_direct_req']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['ps_pass_through_req']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['ps_new_client']; ?></td>
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
									<td style="text-align: center;vertical-align: middle;"><input style="height:18px;width:18px;cursor:pointer;outline: none;" type="checkbox" class="checkboxes" name="checked_id[<?php echo $iii; ?>]" id="checked_id" value="<?php echo $iii; ?>"></td>

									<input type="hidden" id="mainamount<?php echo $iii; ?>" name="final_incentive[<?php echo $iii; ?>]" value="<?php echo $iamountV+$exReqAmtX+$directAmtX+$ptAmtX+$newAccCrackAmtX; ?>">

									<input type="hidden" name="person_id[<?php echo $iii; ?>]" value="<?php echo $psidX; ?>">
									<input type="hidden" name="person_name[<?php echo $iii; ?>]" value="<?php echo $psdataX; ?>">
									<input type="hidden" name="type_data[<?php echo $iii; ?>]" value="<?php echo "Post Sales"; ?>">
									<input type="hidden" name="period[<?php echo $iii; ?>]" value="<?php echo $dtX; ?>">
									<input type="hidden" name="total_candidate[<?php echo $iii; ?>]" value="<?php echo $totCAN; ?>">
									<input type="hidden" name="total_margin[<?php echo $iii; ?>]" value="<?php echo $totGP; ?>">
									<input type="hidden" name="incentive_amount[<?php echo $iii; ?>]" value="<?php echo $iamountV; ?>">
									<input type="hidden" name="ps_exclusive_req[<?php echo $iii; ?>]" value="<?php echo $exReqAmtX; ?>">
									<input type="hidden" name="ps_direct_req[<?php echo $iii; ?>]" value="<?php echo $directAmtX; ?>">
									<input type="hidden" name="ps_pass_through_req[<?php echo $iii; ?>]" value="<?php echo $ptAmtX; ?>">
									<input type="hidden" name="ps_new_client[<?php echo $iii; ?>]" value="<?php echo $newAccCrackAmtX; ?>">
									<input type="hidden" name="detail_link[<?php echo $iii; ?>]" value="<?php echo LOCAL_REPORT_PATH; ?>/incentive/post_sales_incentive_report/index.php?multimonth=<?php echo urlencode($dtX); ?>&ps_name=<?php echo urlencode($psdataX); ?>&PSIRsubmit2=&response_type=1">

									<td style="text-align: left;vertical-align: middle;"><a href="?multimonth=<?php echo $dtX; ?>&ps_name=<?php echo $psdataX; ?>&PSIRsubmit2="><?php echo $psdataX; ?></a></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $totCAN; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $totGP; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $iamountV; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $exReqAmtX; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $directAmtX; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $ptAmtX; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $newAccCrackAmtX; ?></td>
									<td style="text-align: center;vertical-align: middle;font-weight: bold;"><?php echo $iamountV+$exReqAmtX+$directAmtX+$ptAmtX+$newAccCrackAmtX; ?></td>
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
									<td style="text-align: center;vertical-align: middle;font-weight: bold;"><input type="text" id="final_amount<?php echo $iii; ?>" name="final_amount[<?php echo $iii; ?>]" style="background-color: #fff;color: #000;width: 70%;padding: 2px 5px;border: none;text-align: center;" value="<?php echo $iamountV+$exReqAmtX+$directAmtX+$ptAmtX+$newAccCrackAmtX; ?>" readonly></td>
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
								<th colspan="14" style="background-color: #bbb;text-align: right;vertical-align: middle;"><button type="submit" class="btn btn-primary" style="border-radius: 0px;background-color: #2266AA;"><i class="fa fa-lock"></i> Lock the Amount</button></th>
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

		/*PS Incentive Form Submission START*/
		$('#PSincentive').submit(function(e){
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
					data: $('#PSincentive').serialize(),
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

	if(isset($_REQUEST['PSIRsubmit2'])){
		$monthsdata=$_REQUEST['multimonth'];
		$psdata=$_REQUEST['ps_name'];
		if($responseType == 0){
?>
	<section id="PSIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-4 col-md-offset-4" style="background-color: #ccc;color: #000;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">PS Personnel : <span style="font-size: 16px;color: #333;"><?php echo $psdata; ?></span><span style="font-size: 15px;color: #449D44;"><?php echo " (".$monthsdata.")"; ?></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-10 col-md-offset-1">
					<table id="PSIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Candidate</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Client</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Joining Date</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Status</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Termination Date</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">3 Months Completed</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Margin</th>
								<th style="text-align: center;vertical-align: middle;" colspan="4">Fix Incentive</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;" colspan="3">Joborder Type</th>
								<th style="text-align: center;vertical-align: middle; border-right: 1px solid #ddd;" rowspan="2">New Client</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">Exclusive</th>
								<th style="text-align: center;vertical-align: middle;">Direct</th>
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Pass Through</th>
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

								$start_dateX=date('m/01/Y',strtotime($dt));
								$end_dateX=date('m/t/Y',strtotime($dt));
								$start_dateX2=strtotime($start_dateX);
								$end_dateX2=strtotime($end_dateX);

								$main_query="SELECT
											ef.value AS ipsnm,
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
											es.name AS employment_type,
											mp.c_joborder_id AS jid
										FROM
											employees AS emp
										    JOIN vtechhrm.employmentstatus AS es ON es.id=emp.employment_status
										    JOIN vtech_mappingdb.system_integration AS mp ON mp.h_employee_id=emp.id
										    JOIN cats.company AS comp ON comp.company_id=mp.c_company_id
										    JOIN cats.extra_field AS ef ON ef.data_item_id=comp.company_id
										WHERE
											ef.field_name='Inside Post Sales'
										AND
											ef.value = '$psdata'
										AND
											date_format(emp.custom7, '%Y-%m-%d') BETWEEN '$sdateX' AND '$tdateX'";
								$main_result=mysqli_query($vtechhrmConn,$main_query);

								$enameX=$cnameX=$joindateX=$termi_dateX=$statusX=$eligibility=$g_margin3=$newAccCrackAmt=$exReqAmt=$directAmt=$ptAmt=array();

								if(mysqli_num_rows($main_result)>0){
									while($main_row=mysqli_fetch_array($main_result)){
										$enameX[]=$main_row['ename'];
										$cnameX[]=$main_row['cname'];
										$joindateX[]=$main_row['joindate'];
										$termi_dateX[]=$main_row['termi_date'];
										$statusX[]=$main_row['status'];

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

										$clid1=$main_row['cid'];
										$emp_eid=$main_row['eid'];

										$nacaQRY=mysqli_query($vtechMappingdbConn, "SELECT id FROM system_integration WHERE h_employee_id='$emp_eid' AND c_company_id='$clid1'");
										$nacaROW=mysqli_fetch_array($nacaQRY);

										$nacaQRY2=mysqli_query($vtechMappingdbConn, "SELECT MIN(id) AS minid FROM system_integration WHERE c_company_id='$clid1'");
										$nacaROW2=mysqli_fetch_array($nacaQRY2);
										if(($nacaROW2['minid']==$nacaROW['id']) && ($main_row['joinmonth']==$month)){
											$pheQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Post Sales' AND comment='New Clients'");
											$pheROW=mysqli_fetch_array($pheQRY);
											$newAccCrackAmt[]=$pheROW['value'];
										}else{
											$newAccCrackAmt[]='0';
										}

										$jidX=$main_row['jid'];
										$jobtypeQRY=mysqli_query($catsConn,"SELECT ic.comment AS type,ic.value AS fixamt FROM extra_field AS ef JOIN mis_reports.incentive_criteria AS ic ON ic.comment=ef.value WHERE field_name='Joborder Type' AND data_item_id='$jidX'");
										if($jobtypeROW=mysqli_fetch_array($jobtypeQRY)){
											if($jobtypeROW['type']=='Exclusive Req Hire'){
												$exReqAmt[]=$jobtypeROW['fixamt'];
											}elseif($jobtypeROW['type']=='Direct Req'){
												$directAmt[]=$jobtypeROW['fixamt'];
											}elseif($jobtypeROW['type']=='Pass Through'){
												$ptAmt[]=$jobtypeROW['fixamt'];
											}else{
												$exReqAmt[]='0';
												$directAmt[]='0';
												$ptAmt[]='0';
											}
										}else{
											$exReqAmt[]='0';
											$directAmt[]='0';
											$ptAmt[]='0';
										}


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
										$g_margin3[]=$g_margin2;
									}
								}
								$totCAN=$totGP=$exReqAmtX=$directAmtX=$ptAmtX=$newAccCrackAmtX='0';
								$totCOMP=array();
								foreach($enameX AS $key => $enameX2){
									if($responseType == 0){
							?>
								<tr>
									<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($enameX[$key]); ?></td>
									<td style="text-align: left;vertical-align: middle;"><?php echo $cnameX[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $joindateX[$key]; ?></td>
							<?php if($statusX[$key]=='Active'){ ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo $statusX[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;">---</td>
							<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $statusX[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $termi_dateX[$key]; ?></td>
							<?php } ?>
							<?php if($eligibility[$key]=="Yes"){ ?>
									<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $eligibility[$key]; ?></td>
							<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $eligibility[$key]; ?></td>
							<?php } ?>
							<?php if($g_margin3[$key] <= '4' || $eligibility[$key]=="No"){ ?>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $g_margin3[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $exReqAmt[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $directAmt[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $ptAmt[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $newAccCrackAmt[$key]; ?></td>
							<?php }else{
								$totCAN++;
								$totCOMP[]=$cnameX[$key];
								$totGP+=$g_margin3[$key];
								$exReqAmtX+=$exReqAmt[$key];
								$directAmtX+=$directAmt[$key];
								$ptAmtX+=$ptAmt[$key];
								$newAccCrackAmtX+=$newAccCrackAmt[$key];
							 ?>
									<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $g_margin3[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $exReqAmt[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $directAmt[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $ptAmt[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $newAccCrackAmt[$key]; ?></td>
							<?php } ?>
								</tr>
							<?php
									}else{
										if($statusX[$key]=='Active'){
											$termi_date2 = "---";
										}else{
											$termi_date2 = $termi_date[$key];
										}
										$responseArray[] = array('candidate' => ucwords($enameX[$key]),
											'client' => $cnameX[$key],
											'join_date' => $joindateX[$key],
											'status' => $statusX[$key],
											'termi_date' => $termi_date2,
											'eligibility' => $eligibility[$key],
											'margin' => $g_margin3[$key],
											'exclusive' => $exReqAmt[$key],
											'direct' => $directAmt[$key],
											'pass_through' => $ptAmt[$key],
											'new_client' => $newAccCrackAmt[$key]);
									}
								}
								if($responseType == 0){
							?>
						</tbody>
						<tfoot>
							<?php
								/////////Incentive Amount//////////////////
								$iamountQ="SELECT * FROM incentive_criteria WHERE personnel='Post Sales' AND comment=''";
								$iamountR=mysqli_query($misReportsConn,$iamountQ);
								$iamountV='0';
								while($iamountD=mysqli_fetch_array($iamountR)){
									if($totGP>=$iamountD['min_margin'] && $totGP<$iamountD['max_margin']){
										$iamountV=$iamountD['value'];
									}elseif($totGP>=$iamountD['min_margin'] && $iamountD['max_margin']=='0'){
										$iamountV=$iamountD['value'];
									}
								}
							?>
							<tr style="background-color: #bbb;color: #000;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $totCAN; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo sizeof(array_unique($totCOMP)); ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2" colspan="4"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $totGP; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $exReqAmtX; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $directAmtX; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $ptAmtX; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $newAccCrackAmtX; ?></th>
							</tr>
							<tr style="background-color: #bbb;color: #000;">
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;"><?php echo $iamountV; ?></th>
							</tr>
							<tr style="background-color: #bbb;color: #2266AA;font-size: 16px;">
								<th style="text-align: center;vertical-align: middle;" colspan="11">Final Incentive Amount : <span style="color: #000;"><?php echo $iamountV+$exReqAmtX+$directAmtX+$ptAmtX+$newAccCrackAmtX; ?></span></th>
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
