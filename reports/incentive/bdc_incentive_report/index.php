<?php
	include("../../../security.php");

	$responseArray = array();
	$responseType = isset($_REQUEST['response_type']) && $_REQUEST['response_type'] == 1 ? 1 : 0;

    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='44';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){
			if($responseType == 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>BDC Incentive Report</title>
	
	<?php
		include('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th{
			padding: 5px 3px;
		}
		table.dataTable tbody td{
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
			
			/*Hide & show sections START*/
			$("#LoadingImage").hide();
			$('#MainSection').removeClass("hidden");
			$('#PIRdatatable').removeClass("hidden");
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


			/*Select Personnel Name START*/
			$('#personnel_name').multiselect({
				nonSelectedText: 'Select Personnel',
				numberDisplayed: 1,
				enableFiltering:true,
				enableCaseInsensitiveFiltering:true,
				buttonWidth:'100%',
				includeSelectAllOption: true,
					maxHeight: 300
			});
			$("#personnel_name").multiselect('selectAll', false);
	        $("#personnel_name").multiselect('updateButtonText');
			/*Select Personnel Name END*/


			/*Datatable Calling START*/
			var tableX = $('#PIRdata').DataTable({
				"paging": false,
				"aaSorting": [],
			    dom: 'Bfrtip',
			    "columnDefs":[{
					"targets" : 'no-sort',
					"orderable": false,
			    }],
		        buttons:[
		            'excel'
		        ],
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","200")
				}
			});
			tableX.button(0).nodes().css('background', '#2266AA');
			tableX.button(0).nodes().css('border', '#2266AA');
			tableX.button(0).nodes().css('color', '#fff');
			tableX.button(0).nodes().html('Download Report');
			/*Datatable Calling END*/

			/*Datatable Calling START*/
			var tableX = $('#PIRdata2').DataTable({
				"paging": false,
				"aaSorting": [],
			    dom: 'Bfrtip',
			    "columnDefs":[{
					"targets" : 'no-sort',
					"orderable": false,
			    }],
		        buttons:[
		            'excel'
		        ],
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","200")
				}
			});
			tableX.button(0).nodes().css('background', '#2266AA');
			tableX.button(0).nodes().css('border', '#2266AA');
			tableX.button(0).nodes().css('color', '#fff');
			tableX.button(0).nodes().html('Download Report');
			/*Datatable Calling END*/


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
			            }
					});
					return true;
				}
			});
			/*CSM Incentive Form Submission END*/
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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">BDC Incentive Report</div>
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
								<label>Select Personnel :</label>
								<select class="form-control" id="personnel_name" name="personnel_name[]" style="border: 1px solid #aaa;border-radius: 0px;" multiple>
									<?php
										$pnameQRY=mysqli_query($sales_connect, "SELECT
											concat(u.firstName,' ',u.lastName) AS pname
										FROM
											x2_users AS u
										    JOIN vtech_mappingdb.manage_sales_roles AS msr ON msr.user_id = u.id
										WHERE
											msr.department = 'Inside Sales'
										AND
											u.status != '0'
										AND
											msr.manager_name = 'Haresh Vataliya'");
										while($pnameROW=mysqli_fetch_array($pnameQRY)){
											echo "<option value='".$pnameROW['pname']."'>".ucwords($pnameROW['pname'])."</option>";
										}
								?>
								</select>
							</div>
							<div class="col-md-3 col-md-offset-6" style="margin-top: 30px;">
								<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
							</div>
							<div class="col-md-3" style="margin-top: 30px;">
								<button type="submit" class="form-control" name="PIRsubmit" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<img src="<?php echo IMAGE_PATH; ?>/noticex2.png" style="bottom: 0px;" class="bottom-right">
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	}
	//POST Overview Section
	if(isset($_REQUEST['PIRsubmit'])){
		$monthsdata=$_REQUEST['multimonth'];
		$personneldata=$_REQUEST['personnel_name'];
		if($responseType == 0){
?>
	<section id="PIRdatatable" class="hidden">
		<div class="container-fluid">
			<form id="BDGincentive" onsubmit="return true">
				<div class="row" style="margin-bottom: 50px;">
					<div class="col-md-12">
						<table id="PIRdata" class="table table-striped table-bordered">
							<thead>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th class='no-sort' style="text-align: center;vertical-align: middle;" rowspan="3"><input style="height:20px;width:20px;cursor: pointer;outline: none;" type='checkbox' name='select_all' id='select_all'></th>
									<th style="text-align: center;vertical-align: middle;" rowspan="3">BDC Personnel</th>
									<th style="text-align: center;vertical-align: middle;" colspan="7">Total</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="3">Final Amount</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Incentive Amount</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Total Contract</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Fix Incentive (Contract Signing Bonus)</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Incentive</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Method</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="2">Amount</th>
									<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;" rowspan="2">Comment</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th style="text-align: center;vertical-align: middle;">MSP</th>
									<th style="text-align: center;vertical-align: middle;">Direct</th>
									<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Product Sale</th>
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
									$sdateX2=strtotime($sdateX);
									$tdateX2=strtotime($tdateX);

									$iii='0';
									foreach($personneldata AS $personneldataX){
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
											date_format(comp.date_created, '%Y-%m-%d') AS client_date,
											emp.custom1 AS benefit,
											emp.custom2 AS benefitlist,
											CAST(replace(emp.custom3,'$','') AS DECIMAL (10,2)) AS billrate,
											CAST(replace(emp.custom4,'$','') AS DECIMAL (10,2)) AS payrate,
											es.id AS es_id,
											es.name AS employment_type,
											mp.c_joborder_id AS jid
										FROM
											employees AS emp
										    JOIN employmentstatus AS es ON es.id=emp.employment_status
										    JOIN vtech_mappingdb.system_integration AS mp ON mp.h_employee_id=emp.id
										    JOIN cats.company AS comp ON comp.company_id=mp.c_company_id
										    JOIN cats.extra_field AS ef ON ef.data_item_id=comp.company_id
										WHERE
											ef.value = '$personneldataX'
										AND
											(ef.field_name='Inside Sales Person1' OR ef.field_name='Inside Sales Person2')
										AND
											date_format(emp.custom7, '%Y-%m-%d') BETWEEN '$sdateX' AND '$tdateX'";
										$main_result=mysqli_query($vtechhrmConn, $main_query);
										$sameClient=$sameAmount="";
										$enameX=$cnameX=$joindateX=$termi_dateX=$statusX=$eligibility=$g_margin3=$client_elig=$newAccCrack=$shareAMT=$shareNAME=array();

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
												$knowEligibility="";
												if($main_row['status']=='Active'){
													if($cur_date>$third_mon_date){
														$eligibility[]="Yes";
														$knowEligibility="Yes";
													}else{
														$eligibility[]="No";
														$knowEligibility="No";
													}
												}else{
													if($termi_date_chk>$third_mon_date){
														$eligibility[]="Yes";
														$knowEligibility="Yes";
													}else{
														$eligibility[]="No";
														$knowEligibility="No";
													}
												}


												///////////////////Client Eligibility//////////////////
												$two_year_date=strtotime($main_row['client_date'].' 24 month');
												if($cur_date<$two_year_date){
													$client_elig[]="Yes";
												}else{
													$client_elig[]="No";
												}


												//////////////////////New Account Crack/////////////////////
												$clid1=$main_row['cid'];
												$emp_eid=$main_row['eid'];

												$nacaQRY=mysqli_query($vtechMappingdbConn, "SELECT id FROM system_integration WHERE h_employee_id='$emp_eid' AND c_company_id='$clid1'");
												$nacaROW=mysqli_fetch_array($nacaQRY);

												$nacaQRY2=mysqli_query($vtechMappingdbConn, "SELECT
													si.id AS minid,
												    date_format(e.custom7, '%m') AS mon,
												    date_format(e.custom7, '%Y') AS year
												FROM
													system_integration AS si
												    JOIN vtechhrm.employees AS e ON si.h_employee_id=e.id
												WHERE
													si.c_company_id='$clid1'");
												$newAccCrackVariable="";
												while($nacaROW2=mysqli_fetch_array($nacaQRY2)){
													if ($sameClient == $clid1 && $sameAmount != '') {
														break;
													}
													if (($nacaROW2['mon']<$month) && ($main_row['year']<$year)) {
														break;
													}else{
														if(($nacaROW2['minid']==$nacaROW['id']) && ($main_row['joinmonth']==$month) && ($knowEligibility=='Yes')){
															$pheQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Proposal' AND comment='First Hire'");
															$pheROW=mysqli_fetch_array($pheQRY);
															$newAccCrackVariable=$pheROW['value'];
															$sameClient = $clid1;
															$sameAmount = $pheROW['value'];
															break;
														}
													}
												}

												if($newAccCrackVariable != ''){
													$newAccCrack[]=$newAccCrackVariable;
												}else{
													$newAccCrack[]='0';
												}


												/////////////////Share//////////////////
												$shareQRY=mysqli_query($catsConn, "SELECT value FROM extra_field WHERE data_item_id='$clid1' AND field_name='Inside Sales Person2' AND value!=''");
												if(mysqli_num_rows($shareQRY)>0){
													$shareROW=mysqli_fetch_array($shareQRY);
													if($shareROW['value']==$main_row['ipsnm']){
														$shareQRY2=mysqli_query($catsConn, "SELECT value FROM extra_field WHERE data_item_id='$clid1' AND field_name='Inside Sales Person1'");
														$shareROW2=mysqli_fetch_array($shareQRY2);
														$shareNAME[]=$main_row['ipsnm'].'-'.$shareROW2['value'];
													}else{
														$shareNAME[]=$main_row['ipsnm'].'-'.$shareROW['value'];
													}
													$shareAMT[]='2';
												}else{
													$shareNAME[]='NULL';
													$shareAMT[]='1';
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
										$totCAN='0';
										$totCOMP=$totINC=array();
										foreach($enameX AS $key => $enameX2){
											if($eligibility[$key]=="Yes" && $client_elig[$key]=="Yes"){
												$totCAN++;
												$totCOMP[]=$cnameX[$key];
											}
											
											if($newAccCrack[$key]=='0'){
												/////////Incentive Amount//////////////////
												$iamountQ="SELECT * FROM incentive_criteria WHERE personnel='Sales' AND comment=''";
												$iamountR=mysqli_query($misReportsConn,$iamountQ);
												$iamountV='0';
												while($iamountD=mysqli_fetch_array($iamountR)){
													if($g_margin3[$key]<$iamountD['min_margin']){
														$iamountV=$iamountD['value'];
													}elseif($g_margin3[$key]>=$iamountD['max_margin']){
														$iamountV=$iamountD['value'];
													}
												}
											}else{
												$iamountV='0';
												$iamountV=$newAccCrack[$key];
											}

											if($eligibility[$key]=="Yes" && $client_elig[$key]=="Yes"){
												$totINC[]=$iamountV/$shareAMT[$key];
											}
										}

										$contractQRY=mysqli_query($allConn, "SELECT
											cxopp.name,
										    cxopp.createDate,
										    cxopp.c_contract_type,
										    cxopp.c_solicitation_number,
										    cxopp.accountName
										FROM
											contract.x2_opportunities AS cxopp
											JOIN vtechcrm.x2_opportunities AS sxopp ON cxopp.name=sxopp.name
										    JOIN vtechcrm.x2_users AS sxuser ON sxopp.assignedTo=sxuser.username
										WHERE
											concat(sxuser.firstName,' ',sxuser.lastName)='$personneldataX'
										AND
											cxopp.createDate BETWEEN '$sdateX2' AND '$tdateX2'
										GROUP BY cxopp.id");
										$contract_name=$sign_date=$contract_type=$mspAMT=$directAMT=$productsaleAMT=array();
										while($contractROW=mysqli_fetch_array($contractQRY)){
											$contract_name[]=$contractROW['name'];
											$sign_date[]=date('m-d-Y', $contractROW['createDate']);
											$contract_type[]=$contractROW['c_contract_type'];
											
											$contract_type_value=$contractROW['c_contract_type'];
											$contractTypeQRY=mysqli_query($misReportsConn,"SELECT value FROM incentive_criteria WHERE personnel='sales' AND comment='$contract_type_value'");
											if(mysqli_num_rows($contractTypeQRY)>0){
												$contractTypeROW=mysqli_fetch_array($contractTypeQRY);
												if($contractROW['c_contract_type']=='MSP Staffing' OR $contractROW['c_contract_type']=='MSP Staffing + SOW'){
													$mspAMT[]=$contractTypeROW['value'];
													$directAMT[]='0';
													$productsaleAMT[]='0';
												}elseif($contractROW['c_contract_type']=='Direct Staffing' OR $contractROW['c_contract_type']=='Direct Staffing + SOW'){
													$mspAMT[]='0';
													$directAMT[]=$contractTypeROW['value'];
													$productsaleAMT[]='0';
												}elseif($contractROW['c_contract_type']=='Product Sale'){
													$mspAMT[]='0';
													$directAMT[]='0';
													$productsaleAMT[]=$contractTypeROW['value'];
												}else{
													$mspAMT[]='0';
													$directAMT[]='0';
													$productsaleAMT[]='0';
												}
											}else{
												$mspAMT[]='0';
												$directAMT[]='0';
												$productsaleAMT[]='0';
											}
										}
										$selectQRY="SELECT * FROM incentive_data WHERE person_name='$personneldataX' AND period='$dtX' AND type = 'Sales'";
										$selectRES=mysqli_query($misReportsConn, $selectQRY);
										if(mysqli_num_rows($selectRES)>0){
											while($selectROW=mysqli_fetch_array($selectRES)){
												if($responseType == 0){
								?>
								<tr style="background-color: #c3dcf4;">
									<td style="text-align: center;vertical-align: middle;"><i class="fa fa-lock" style="font-size: 18px;color: #2266AA;"></i></td>
									<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($selectROW['person_name']); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['total_candidate']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['incentive_amount']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['bd_total_contract']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['bd_msp']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['bd_direct']; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['bd_product_sale']; ?></td>
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
									
									<input type="hidden" id="mainamount<?php echo $iii; ?>" name="mainamount[<?php echo $iii; ?>]" value="<?php echo array_sum($totINC)+array_sum($mspAMT)+array_sum($directAMT)+array_sum($productsaleAMT); ?>">

									<input type="hidden" name="person_name[<?php echo $iii; ?>]" value="<?php echo ucwords($personneldataX); ?>">
									<input type="hidden" name="type_data[<?php echo $iii; ?>]" value="<?php echo "Sales"; ?>">
									<input type="hidden" name="period[<?php echo $iii; ?>]" value="<?php echo $dtX; ?>">
									<input type="hidden" name="total_candidate[<?php echo $iii; ?>]" value="<?php echo $totCAN; ?>">
									<input type="hidden" name="incentive_amount[<?php echo $iii; ?>]" value="<?php echo array_sum($totINC); ?>">
									<input type="hidden" name="bd_total_contract[<?php echo $iii; ?>]" value="<?php echo sizeof($contract_name); ?>">
									<input type="hidden" name="bd_msp[<?php echo $iii; ?>]" value="<?php echo array_sum($mspAMT); ?>">
									<input type="hidden" name="bd_direct[<?php echo $iii; ?>]" value="<?php echo array_sum($directAMT); ?>">
									<input type="hidden" name="bd_product_sale[<?php echo $iii; ?>]" value="<?php echo array_sum($productsaleAMT); ?>">
									<input type="hidden" name="final_incentive[<?php echo $iii; ?>]" value="<?php echo array_sum($totINC)+array_sum($mspAMT)+array_sum($directAMT)+array_sum($productsaleAMT); ?>">
									<input type="hidden" name="detail_link[<?php echo $iii; ?>]" value="<?php echo LOCAL_REPORT_PATH; ?>/incentive/bdc_incentive_report/index.php?multimonth=<?php echo urlencode($dtX); ?>&personnel_name=<?php echo urlencode($personneldataX); ?>&PIRsubmit2=&response_type=1">

									<td style="text-align: left;vertical-align: middle;"><a href="?multimonth=<?php echo $dtX; ?>&personnel_name=<?php echo $personneldataX; ?>&PIRsubmit2="><?php echo ucwords($personneldataX); ?></a></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $totCAN; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($totINC); ?></td>
									<td style="text-align: center;vertical-align: middle;font-weight: bold;"><?php echo sizeof($contract_name); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($mspAMT); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($directAMT); ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($productsaleAMT); ?></td>
									<td style="text-align: center;vertical-align: middle;font-weight: bold;"><?php echo array_sum($totINC)+array_sum($mspAMT)+array_sum($directAMT)+array_sum($productsaleAMT); ?></td>
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
									<td style="text-align: center;vertical-align: middle;font-weight: bold;"><input type="text" id="final_amount<?php echo $iii; ?>" name="final_amount[<?php echo $iii; ?>]" style="background-color: #fff;color: #000;width: 70%;padding: 2px 5px;border: none;text-align: center;" value="<?php echo array_sum($totINC)+array_sum($mspAMT)+array_sum($directAMT)+array_sum($productsaleAMT); ?>" readonly></td>
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

		/*PS Incentive Form Submission START*/
		$('#BDGincentive').submit(function(e){
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
					data: $('#BDGincentive').serialize(),
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
	if(isset($_REQUEST['PIRsubmit2'])){
		$monthsdata=$_REQUEST['multimonth'];
		$personneldata=$_REQUEST['personnel_name'];
		if($responseType == 0){
?>
	<section id="PIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-4 col-md-offset-4" style="background-color: #ccc;color: #000;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">Personnel Name : <span style="font-size: 16px;color: #333;"><?php echo ucwords($personneldata); ?></span><span style="font-size: 15px;color: #449D44;"><?php echo " (".$monthsdata.")"; ?></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 30px;">
				<div class="col-md-12">
					<table id="PIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">Candidate</th>
								<th style="text-align: center;vertical-align: middle;">Client</th>
								<th style="text-align: center;vertical-align: middle;">Joining Date</th>
								<th style="text-align: center;vertical-align: middle;">Status</th>
								<th style="text-align: center;vertical-align: middle;">Termination Date</th>
								<th style="text-align: center;vertical-align: middle;">3 Months Completed</th>
								<th style="text-align: center;vertical-align: middle;">Margin</th>
								<th style="text-align: center;vertical-align: middle;">Incentive Amount</th>
								<th style="text-align: center;vertical-align: middle;" data-toggle="tooltip" data-placement="top" title="Total Inside Sales Person">Share</th>
								<th style="text-align: center;vertical-align: middle;">Final Incentive</th>
							</tr>
						</thead>
						<tbody>
							<?php
								}
								$mdata=explode("-",$monthsdata);
								$month=$mdata[0];
								$year=$mdata[1];

								$dt = $year."-".$month;
								$dtX = $month."-".$year;
								$dtX2 = $month."/".$year;

								$sdateX=date('Y-m-01',strtotime($dt));
								$tdateX=date('Y-m-t',strtotime($dt));
								$sdateX2=strtotime($sdateX);
								$tdateX2=strtotime($tdateX);

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
									date_format(comp.date_created, '%Y-%m-%d') AS client_date,
									emp.custom1 AS benefit,
									emp.custom2 AS benefitlist,
									CAST(replace(emp.custom3,'$','') AS DECIMAL (10,2)) AS billrate,
									CAST(replace(emp.custom4,'$','') AS DECIMAL (10,2)) AS payrate,
									es.id AS es_id,
									es.name AS employment_type,
									mp.c_joborder_id AS jid
								FROM
									employees AS emp
								    JOIN employmentstatus AS es ON es.id=emp.employment_status
								    JOIN vtech_mappingdb.system_integration AS mp ON mp.h_employee_id=emp.id
								    JOIN cats.company AS comp ON comp.company_id=mp.c_company_id
								    JOIN cats.extra_field AS ef ON ef.data_item_id=comp.company_id
								WHERE
									ef.value = '$personneldata'
								AND
									(ef.field_name='Inside Sales Person1' OR ef.field_name='Inside Sales Person2')
								AND
									date_format(emp.custom7, '%Y-%m-%d') BETWEEN '$sdateX' AND '$tdateX'";
								$main_result=mysqli_query($vtechhrmConn, $main_query);
								$sameClient=$sameAmount="";
								$enameX=$cnameX=$joindateX=$termi_dateX=$statusX=$eligibility=$g_margin3=$client_elig=$newAccCrack=$shareAMT=$shareNAME=array();

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
										$knowEligibility="";
										if($main_row['status']=='Active'){
											if($cur_date>$third_mon_date){
												$eligibility[]="Yes";
												$knowEligibility="Yes";
											}else{
												$eligibility[]="No";
												$knowEligibility="No";
											}
										}else{
											if($termi_date_chk>$third_mon_date){
												$eligibility[]="Yes";
												$knowEligibility="Yes";
											}else{
												$eligibility[]="No";
												$knowEligibility="No";
											}
										}


										///////////////////Client Eligibility//////////////////
										$two_year_date=strtotime($main_row['client_date'].' 24 month');
										if($cur_date<$two_year_date){
											$client_elig[]="Yes";
										}else{
											$client_elig[]="No";
										}


										//////////////////////New Account Crack/////////////////////
										$clid1=$main_row['cid'];
										$emp_eid=$main_row['eid'];

										$nacaQRY=mysqli_query($vtechMappingdbConn, "SELECT id FROM system_integration WHERE h_employee_id='$emp_eid' AND c_company_id='$clid1'");
										$nacaROW=mysqli_fetch_array($nacaQRY);

										$nacaQRY2=mysqli_query($vtechMappingdbConn, "SELECT
											si.id AS minid,
										    date_format(e.custom7, '%m') AS mon,
										    date_format(e.custom7, '%Y') AS year
										FROM
											system_integration AS si
										    JOIN vtechhrm.employees AS e ON si.h_employee_id=e.id
										WHERE
											si.c_company_id='$clid1'");
										$newAccCrackVariable="";
										while($nacaROW2=mysqli_fetch_array($nacaQRY2)){
											if ($sameClient == $clid1 && $sameAmount != '') {
												break;
											}
											if (($nacaROW2['mon']<$month) && ($main_row['year']<$year)) {
												break;
											}else{
												if(($nacaROW2['minid']==$nacaROW['id']) && ($main_row['joinmonth']==$month) && ($knowEligibility=='Yes')){
													$pheQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Proposal' AND comment='First Hire'");
													$pheROW=mysqli_fetch_array($pheQRY);
													$newAccCrackVariable=$pheROW['value'];
													$sameClient = $clid1;
													$sameAmount = $pheROW['value'];
													break;
												}
											}
										}

										if($newAccCrackVariable != ''){
											$newAccCrack[]=$newAccCrackVariable;
										}else{
											$newAccCrack[]='0';
										}


										/////////////////Share//////////////////
										$shareQRY=mysqli_query($catsConn, "SELECT value FROM extra_field WHERE data_item_id='$clid1' AND field_name='Inside Sales Person2' AND value!=''");
										if(mysqli_num_rows($shareQRY)>0){
											$shareROW=mysqli_fetch_array($shareQRY);
											if($shareROW['value']==$main_row['ipsnm']){
												$shareQRY2=mysqli_query($catsConn, "SELECT value FROM extra_field WHERE data_item_id='$clid1' AND field_name='Inside Sales Person1'");
												$shareROW2=mysqli_fetch_array($shareQRY2);
												$shareNAME[]=$main_row['ipsnm'].'-'.$shareROW2['value'];
											}else{
												$shareNAME[]=$main_row['ipsnm'].'-'.$shareROW['value'];
											}
											$shareAMT[]='2';
										}else{
											$shareNAME[]='NULL';
											$shareAMT[]='1';
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
								$totCAN='0';
								$totCOMP=$totINC=array();
								foreach($enameX AS $key => $enameX2){
									if($responseType == 0){
							?>
								<tr>
									<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($enameX[$key]); if($newAccCrack[$key]!='0'){?> <i class="fa fa-star" style="color: #9b8200;"></i><?php } ?></td>
									<td style="text-align: left;vertical-align: middle;"><?php echo $cnameX[$key]; if($client_elig[$key]=='Yes'){ ?> <i class="fa fa-circle" style="color: #449D44;"></i><?php }else{ ?> <i class="fa fa-circle" style="color: #fc2828;"></i><?php } ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $joindateX[$key]; ?></td>
							<?php if($statusX[$key]=='Active'){ ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo $statusX[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;">---</td>
							<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $statusX[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $termi_dateX[$key]; ?></td>
							<?php } ?>
							<?php if($eligibility[$key]=="Yes" && $client_elig[$key]=="Yes"){
								$totCAN++;
								$totCOMP[]=$cnameX[$key];
								?>
									<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $eligibility[$key]; ?></td>
							<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $eligibility[$key]; ?></td>
							<?php } ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo $g_margin3[$key]; ?></td>
							<?php
								if($newAccCrack[$key]=='0'){
									/////////Incentive Amount//////////////////
									$iamountQ="SELECT * FROM incentive_criteria WHERE personnel='Sales' AND comment=''";
									$iamountR=mysqli_query($misReportsConn,$iamountQ);
									$iamountV='0';
									while($iamountD=mysqli_fetch_array($iamountR)){
										if($g_margin3[$key]<$iamountD['min_margin']){
											$iamountV=$iamountD['value'];
										}elseif($g_margin3[$key]>=$iamountD['max_margin']){
											$iamountV=$iamountD['value'];
										}
									}
								}else{
									$iamountV='0';
									$iamountV=$newAccCrack[$key];
								}
							?>
							<?php if($eligibility[$key]=="Yes" && $client_elig[$key]=="Yes"){
									$totINC[]=$iamountV/$shareAMT[$key];
							?>
									<td style="text-align: center;vertical-align: middle;font-weight: bold;"><?php echo $iamountV; ?></td>
							<?php if($shareAMT[$key]=='2'){ ?>
									<td style="text-align: center;vertical-align: middle;font-weight: bold;color: #2266AA;cursor: pointer;" data-toggle="tooltip" data-placement="top" title="<?php echo $shareNAME[$key]; ?>"><?php echo $shareAMT[$key]; ?></td>
							<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;"><?php echo $shareAMT[$key]; ?></td>
							<?php } ?>
									<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $iamountV/$shareAMT[$key]; ?></td>
							<?php }else{ ?>
									<td style="text-align: center;vertical-align: middle;font-weight: bold;"><?php echo $iamountV; ?></td>
									<td style="text-align: center;vertical-align: middle;"><?php echo $shareAMT[$key]; ?></td>
									<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;"><?php echo $iamountV/$shareAMT[$key]; ?></td>
							<?php } ?>
								</tr>
							<?php
									}else{
										if($newAccCrack[$key]=='0'){
											/////////Incentive Amount//////////////////
											$iamountQ="SELECT * FROM incentive_criteria WHERE personnel='Sales' AND comment=''";
											$iamountR=mysqli_query($misReportsConn,$iamountQ);
											$iamountV='0';
											while($iamountD=mysqli_fetch_array($iamountR)){
												if($g_margin3[$key]<$iamountD['min_margin']){
													$iamountV=$iamountD['value'];
												}elseif($g_margin3[$key]>=$iamountD['max_margin']){
													$iamountV=$iamountD['value'];
												}
											}
										}else{
											$iamountV='0';
											$iamountV=$newAccCrack[$key];
										}
										$responseArray['candidateList'][] = array('candidate' => ucwords($enameX[$key]),
											'client' => $cnameX[$key],
											'join_date' => $joindateX[$key],
											'status' => $statusX[$key],
											'termi_date' => $termi_dateX[$key],
											'eligibility' => $eligibility[$key],
											'client_eligibility' => $client_elig[$key],
											'margin' => $g_margin3[$key],
											'incentive_amount' => $iamountV,
											'share' => $shareAMT[$key],
											'share_name' => $shareNAME[$key],
											'final_incentive' => $iamountV/$shareAMT[$key]
										);
									}
								}
								if($responseType == 0){
							?>
						</tbody>
						<tfoot>
							<tr style="background-color: #bbb;color: #000;">
								<th style="text-align: center;vertical-align: middle;"><?php echo $totCAN; ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo sizeof(array_unique($totCOMP)); ?></th>
								<th style="text-align: center;vertical-align: middle;" colspan="7"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $totINCX=array_sum($totINC); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>

			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-8 col-md-offset-2">
					<table id="PIRdata2" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Opportunities</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Account</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Contract No.</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Type</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Sign Date</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Signing Amount</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">MSP</th>
								<th style="text-align: center;vertical-align: middle;">Direct</th>
								<th style="text-align: center;vertical-align: middle;">Product Sale</th>
							</tr>
						</thead>
						<tbody>
							<?php
								}
								$contractQRY=mysqli_query($allConn, "SELECT
									cxopp.name,
								    cxopp.createDate,
								    cxopp.c_contract_type,
								    cxopp.c_solicitation_number,
									cxopp.accountName
								FROM
									contract.x2_opportunities AS cxopp
									JOIN vtechcrm.x2_opportunities AS sxopp ON cxopp.name=sxopp.name
								    JOIN vtechcrm.x2_users AS sxuser ON sxopp.assignedTo=sxuser.username
								WHERE
									concat(sxuser.firstName,' ',sxuser.lastName)='$personneldata'
								AND
									cxopp.createDate BETWEEN '$sdateX2' AND '$tdateX2'
								GROUP BY cxopp.id");
								$contract_name=$accountName=$contractNo=$sign_date=$contract_type=$mspAMT=$directAMT=$productsaleAMT=array();
								while($contractROW=mysqli_fetch_array($contractQRY)){
									$contract_name[]=$contractROW['name'];
									$accountName[]=$contractROW['accountName'];
									$contractNo[]=$contractROW['c_solicitation_number'];
									$sign_date[]=date('m-d-Y', $contractROW['createDate']);
									$contract_type[]=$contractROW['c_contract_type'];
									
									$contract_type_value=$contractROW['c_contract_type'];
									$contractTypeQRY=mysqli_query($misReportsConn,"SELECT value FROM incentive_criteria WHERE personnel='sales' AND comment='$contract_type_value'");
									if(mysqli_num_rows($contractTypeQRY)>0){
										$contractTypeROW=mysqli_fetch_array($contractTypeQRY);
										if($contractROW['c_contract_type']=='MSP Staffing' OR $contractROW['c_contract_type']=='MSP Staffing + SOW'){
											$mspAMT[]=$contractTypeROW['value'];
											$directAMT[]='0';
											$productsaleAMT[]='0';
										}elseif($contractROW['c_contract_type']=='Direct Staffing' OR $contractROW['c_contract_type']=='Direct Staffing + SOW'){
											$mspAMT[]='0';
											$directAMT[]=$contractTypeROW['value'];
											$productsaleAMT[]='0';
										}elseif($contractROW['c_contract_type']=='Product Sale'){
											$mspAMT[]='0';
											$directAMT[]='0';
											$productsaleAMT[]=$contractTypeROW['value'];
										}else{
											$mspAMT[]='0';
											$directAMT[]='0';
											$productsaleAMT[]='0';
										}
									}else{
										$mspAMT[]='0';
										$directAMT[]='0';
										$productsaleAMT[]='0';
									}
								}
								foreach($contract_name AS $key => $contract_nameX){
									if($responseType == 0){
							?>
							<tr>
								<td style="text-align: left;vertical-align: middle;"><?php echo $contract_name[$key]; ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $accountName[$key]; ?></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $contractNo[$key]; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $contract_type[$key]; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $sign_date[$key]; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mspAMT[$key]; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $directAMT[$key]; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $productsaleAMT[$key]; ?></td>
							</tr>
							<?php
									}else{
										$responseArray['opportunityList'][] = array('opportunity' => $contract_name[$key],
											'contract_type' => $contract_type[$key],
											'sign_date' => $sign_date[$key],
											'msp_ammount' => $mspAMT[$key],
											'direct_ammount' => $directAMT[$key],
											'product_sale_ammount' => $productsaleAMT[$key]
										);
									}
								}
								if($responseType == 0){
							?>
						</tbody>
						<tfoot>
							<tr style="background-color: #bbb;color: #000;">
								<th style="text-align: center;vertical-align: middle;"><?php echo sizeof($contract_name); ?></th>
								<th style="text-align: center;vertical-align: middle;" colspan="4"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($mspAMT); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($directAMT); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($productsaleAMT); ?></th>
							</tr>
							<tr style="background-color: #bbb;color: #000;">
								<th style="text-align: center;vertical-align: middle;" colspan="5">Total</th>
								<th style="text-align: center;vertical-align: middle;" colspan="4"><?php echo $totBONUS=array_sum($mspAMT)+array_sum($directAMT)+array_sum($productsaleAMT); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>

			<div class="row" style="margin-bottom: 70px;">
				<div class="col-md-6 col-md-offset-3" style="text-align: center;">
					<span style="font-size: 18px;font-weight: bold;color: #2266AA;background-color: #ccc;padding: 7px;">Final Incentive : <span style="color: #333;"><?php echo $totINCX; ?> + <?php echo $totBONUS; ?> = <?php echo $finalINC=$totINCX+$totBONUS; ?></span></span>
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
