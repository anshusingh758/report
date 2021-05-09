<?php
	include("../../../security.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='43';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Locked Incentive Report</title>

	<?php
		include('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th{
			padding: 2px;
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
			$('#LIRdatatable').removeClass("hidden");

			/*multimonth Script START*/
			$("#multimonth").datepicker({
				format: "mm/yyyy",
			    startView: 1,
			    minViewMode: 1,
			    maxViewMode: 2,
			    clearBtn: true,
			    multidate: true,
				orientation: "top",
				autoclose: false
			});
			/*multimonth Script END*/
			
			/*viewby START*/
			$('#viewby').multiselect({
				nonSelectedText: 'Select Filter',
				numberDisplayed: 1,
				enableFiltering:true,
				enableCaseInsensitiveFiltering:true,
				buttonWidth:'100%',
				includeSelectAllOption: true,
 				maxHeight: 300
			});
			/*viewby END*/

			/*Datatable Calling START*/
			var tableX = $('#LIRdata').DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				"columnDefs":[{
					"targets" : 'no-sort',
					"orderable": false,
			    }],
			    dom: 'Bfrtip',
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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Locked Incentive Report</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section id="MainSection" class="hidden" style="margin-top: 20px;margin-bottom: 100px;">
		<div class="container">
			<form action="index.php" method="get">
				<div class="row">
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
				<div class="row" style="margin-top: 20px;">
					<div class="col-md-4 col-md-offset-4">
						<label>Filter By:</label>
						<select id="viewby" name="viewby" class="form-control" required>
							<option value="">Select Filter</option>
							<?php
								$viewQRY=mysqli_query($misReportsConn, "SELECT personnel FROM incentive_criteria GROUP BY personnel ORDER BY id ASC");
								while($viewROW=mysqli_fetch_array($viewQRY)){
									if($_REQUEST['viewby']==$viewROW['personnel']){
										$isSelected = ' selected';
									}else{
										$isSelected = '';
									}
									if($viewROW['personnel']=='Sales'){
										$rowdata='BDC';
									}elseif($viewROW['personnel']=='Proposal'){
										$rowdata='BDG';
									}else{
										$rowdata=$viewROW['personnel'];
									}
									echo "<option value='".$viewROW['personnel']."'".$isSelected.">".$rowdata."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class="row" style="margin-top: 35px;">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
					</div>
					<div class="col-md-2">
						<button type="submit" class="form-control" name="LIRsubmit" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	if(isset($_REQUEST['LIRsubmit'])){
		$monthsdata=$_REQUEST['multimonth'];
		$months_data=array_unique(explode(",", $monthsdata));
		$viewbydata=$_REQUEST['viewby'];

		if($viewbydata=="Recruiter"){
?>
	<section id="LIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="LIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Recruiter</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Recruiter Manager</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Months</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Total</th>
								<th style="text-align: center;vertical-align: middle;" colspan="2">Fix Incentive</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Incentive</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Amount</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Created By</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Date Created</th>
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
								foreach($months_data as $mdata2){
									$monthsdata2=str_replace("-", "/", $mdata2);
									$d1=explode("/",$monthsdata2);
									$month=$d1[0];
									$month_arr[]=$d1[0];
									$year=$d1[1];
									
									$dtX = $month."-".$year;

									$main_query="SELECT *,DATE_FORMAT(date_created, '%m-%d-%Y (%h:%i %p)') AS created_date FROM incentive_data WHERE type='$viewbydata' AND period='$dtX'";
									$main_result=mysqli_query($misReportsConn,$main_query);
									if(mysqli_num_rows($main_result)>0){
										while($main_row=mysqli_fetch_array($main_result)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><a href="?viewId=<?php echo $main_row['id']; ?>&viewby=<?php echo $main_row['type']; ?>&multimonth=<?php echo $main_row['period']; ?>&viewSubmit=" style="cursor: pointer;"><?php echo $main_row['person_name']; ?></a></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $main_row['manager_name']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['period']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['total_candidate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['total_join']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['incentive_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['rec_per_hire']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['rec_new_acc_crack']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_incentive']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php if($main_row['adjustment_method']=='plus'){ ?>
										+
									<?php }else{ ?>
										-
									<?php } ?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_comment']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php
										$uidX=$main_row['addedby'];
										$unameQRY=mysqli_query($misReportsConn, "SELECT uname FROM users WHERE uid='$uidX'");
										$unameROW=mysqli_fetch_array($unameQRY);
										echo $unameROW['uname'];
									?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['created_date']; ?></td>
							</tr>
							<?php
										}
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
		if($viewbydata=="CS Manager"){
?>
	<section id="LIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="LIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Client Manager</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Months</th>
								<th style="text-align: center;vertical-align: middle;" colspan="4">Total</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Amount</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Created By</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Date Created</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th class="no-sort" style="text-align: center;vertical-align: middle;">Recruiter</th>
								<th class="no-sort" style="text-align: center;vertical-align: middle;">Candidate</th>
								<th class="no-sort" style="text-align: center;vertical-align: middle;">Join (This Month)</th>
								<th class="no-sort" style="text-align: center;vertical-align: middle;">Incentive Amount</th>
								<th class="no-sort" style="text-align: center;vertical-align: middle;">Method</th>
								<th class="no-sort" style="text-align: center;vertical-align: middle;">Amount</th>
								<th class="no-sort" style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Comment</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($months_data as $mdata2){
									$monthsdata2=str_replace("-", "/", $mdata2);
									$d1=explode("/",$monthsdata2);
									$month=$d1[0];
									$month_arr[]=$d1[0];
									$year=$d1[1];

									$dtX = $month."-".$year;

									$main_query="SELECT *,DATE_FORMAT(date_created, '%m-%d-%Y (%h:%i %p)') AS created_date FROM incentive_data WHERE type='$viewbydata' AND period='$dtX'";
									$main_result=mysqli_query($misReportsConn,$main_query);
									if(mysqli_num_rows($main_result)>0){
										while($main_row=mysqli_fetch_array($main_result)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><a href="?viewId=<?php echo $main_row['id']; ?>&viewby=<?php echo $main_row['type']; ?>&multimonth=<?php echo $main_row['period']; ?>&viewSubmit=" style="cursor: pointer;"><?php echo $main_row['person_name']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['period']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['total_recruiter']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['total_candidate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['total_join']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_incentive']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php if($main_row['adjustment_method']=='plus'){ ?>
										+
									<?php }else{ ?>
										-
									<?php } ?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_comment']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php
										$uidX=$main_row['addedby'];
										$unameQRY=mysqli_query($misReportsConn, "SELECT uname FROM users WHERE uid='$uidX'");
										$unameROW=mysqli_fetch_array($unameQRY);
										echo $unameROW['uname'];
									?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['created_date']; ?></td>
							</tr>
							<?php
										}
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
		if($viewbydata=="Post Sales"){
?>

	<section id="LIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="LIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 11px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="3">PS Personnel</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Months</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Total</th>
								<th style="text-align: center;vertical-align: middle;" colspan="4">Fix Incentive</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Final Incentive</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Final Amount</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Created By</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Date Created</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 11px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Margin</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Incentive Amount</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Joborder Type</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">New Client</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Method</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Amount</th>
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;" rowspan="2">Comment</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 11px;">
								<th style="text-align: center;vertical-align: middle;">Exclusive</th>
								<th style="text-align: center;vertical-align: middle;">Direct</th>
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Pass Through</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($months_data as $mdata2){
									$monthsdata2=str_replace("-", "/", $mdata2);
									$d1=explode("/",$monthsdata2);
									$month=$d1[0];
									$month_arr[]=$d1[0];
									$year=$d1[1];

									$dtX = $month."-".$year;

									$main_query="SELECT *,DATE_FORMAT(date_created, '%m-%d-%Y (%h:%i %p)') AS created_date FROM incentive_data WHERE type='$viewbydata' AND period='$dtX'";
									$main_result=mysqli_query($misReportsConn,$main_query);
									if(mysqli_num_rows($main_result)>0){
										while($main_row=mysqli_fetch_array($main_result)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><a href="?viewId=<?php echo $main_row['id']; ?>&viewby=<?php echo $main_row['type']; ?>&multimonth=<?php echo $main_row['period']; ?>&viewSubmit=" style="cursor: pointer;"><?php echo $main_row['person_name']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['period']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['total_candidate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['total_margin']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['incentive_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['ps_exclusive_req']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['ps_direct_req']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['ps_pass_through_req']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['ps_new_client']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_incentive']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php if($main_row['adjustment_method']=='plus'){ ?>
										+
									<?php }else{ ?>
										-
									<?php } ?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_comment']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php
										$uidX=$main_row['addedby'];
										$unameQRY=mysqli_query($misReportsConn, "SELECT uname FROM users WHERE uid='$uidX'");
										$unameROW=mysqli_fetch_array($unameQRY);
										echo $unameROW['uname'];
									?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['created_date']; ?></td>
							</tr>
							<?php
										}
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
		if($viewbydata=="Sales"){
?>

	<section id="LIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="LIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="3">BDC Personnel</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Months</th>
								<th style="text-align: center;vertical-align: middle;" colspan="7">Total</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Final Amount</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Created By</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Date Created</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Incentive Amount</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Total Contract</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Fix Incentive (Contract Signing Bonus)</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Incentive</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Method</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Amount</th>
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;" rowspan="2">Comment</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;">MSP</th>
								<th style="text-align: center;vertical-align: middle;">Direct</th>
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Product Sale</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($months_data as $mdata2){
									$monthsdata2=str_replace("-", "/", $mdata2);
									$d1=explode("/",$monthsdata2);
									$month=$d1[0];
									$month_arr[]=$d1[0];
									$year=$d1[1];

									$dtX = $month."-".$year;

									$main_query="SELECT *,DATE_FORMAT(date_created, '%m-%d-%Y (%h:%i %p)') AS created_date FROM incentive_data WHERE type='$viewbydata' AND period='$dtX'";
									$main_result=mysqli_query($misReportsConn,$main_query);
									if(mysqli_num_rows($main_result)>0){
										while($main_row=mysqli_fetch_array($main_result)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><a href="?viewId=<?php echo $main_row['id']; ?>&viewby=<?php echo $main_row['type']; ?>&multimonth=<?php echo $main_row['period']; ?>&viewSubmit=" style="cursor: pointer;"><?php echo $main_row['person_name']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['period']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['total_candidate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['incentive_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['bd_total_contract']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['bd_msp']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['bd_direct']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['bd_product_sale']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_incentive']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php if($main_row['adjustment_method']=='plus'){ ?>
										+
									<?php }else{ ?>
										-
									<?php } ?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_comment']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php
										$uidX=$main_row['addedby'];
										$unameQRY=mysqli_query($misReportsConn, "SELECT uname FROM users WHERE uid='$uidX'");
										$unameROW=mysqli_fetch_array($unameQRY);
										echo $unameROW['uname'];
									?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['created_date']; ?></td>
							</tr>
							<?php
										}
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
		if($viewbydata=="Proposal"){
?>

	<section id="LIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="LIRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="3">BDG Personnel</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Months</th>
								<th style="text-align: center;vertical-align: middle;" colspan="8">Total</th>
								<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Final Amount</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Created By</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="3">Date Created</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Incentive Amount</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Total Contract</th>
								<th style="text-align: center;vertical-align: middle;" colspan="4">Fix Incentive (Contract Signing Bonus)</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Final Incentive</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Method</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Amount</th>
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;" rowspan="2">Comment</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;">SOW</th>
								<th style="text-align: center;vertical-align: middle;">MSP</th>
								<th style="text-align: center;vertical-align: middle;">Direct</th>
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;">Product Sale</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($months_data as $mdata2){
									$monthsdata2=str_replace("-", "/", $mdata2);
									$d1=explode("/",$monthsdata2);
									$month=$d1[0];
									$month_arr[]=$d1[0];
									$year=$d1[1];

									$dtX = $month."-".$year;

									$main_query="SELECT *,DATE_FORMAT(date_created, '%m-%d-%Y (%h:%i %p)') AS created_date FROM incentive_data WHERE type='$viewbydata' AND period='$dtX'";
									$main_result=mysqli_query($misReportsConn,$main_query);
									if(mysqli_num_rows($main_result)>0){
										while($main_row=mysqli_fetch_array($main_result)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><a href="?viewId=<?php echo $main_row['id']; ?>&viewby=<?php echo $main_row['type']; ?>&multimonth=<?php echo $main_row['period']; ?>&viewSubmit=" style="cursor: pointer;"><?php echo $main_row['person_name']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['period']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['total_candidate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['incentive_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['bd_total_contract']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['bd_sow']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['bd_msp']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['bd_direct']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['bd_product_sale']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_incentive']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php if($main_row['adjustment_method']=='plus'){ ?>
										+
									<?php }else{ ?>
										-
									<?php } ?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['adjustment_comment']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['final_amount']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<?php
										$uidX=$main_row['addedby'];
										$unameQRY=mysqli_query($misReportsConn, "SELECT uname FROM users WHERE uid='$uidX'");
										$unameROW=mysqli_fetch_array($unameQRY);
										echo $unameROW['uname'];
									?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $main_row['created_date']; ?></td>
							</tr>
							<?php
										}
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
	}
?>


<?php
	if(isset($_REQUEST['viewSubmit'])){
		$viewId=$_REQUEST['viewId'];
		$viewby=$_REQUEST['viewby'];

		if($viewby=="Recruiter"){
?>
	<section id="LIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row">
				<?php
					//Inside PS Personnel Name
					$rdataQ="SELECT * FROM incentive_data WHERE id='$viewId'";
					$rdataR=mysqli_query($misReportsConn,$rdataQ);
					$rdataD=mysqli_fetch_array($rdataR);
				?>
				<div class="col-md-4 col-md-offset-4" style="background-color: #ccc;color: #000;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">Recruiter : <span style="font-size: 16px;color: #333;"><?php echo ucwords($rdataD['person_name']); ?></span><span style="font-size: 15px;color: #449D44;"><?php echo " (".$rdataD['period'].")"; ?></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="LIRdata" class="table table-striped table-bordered">
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
							$minMarginQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Minimum Margin'");
							$minMarginROW=mysqli_fetch_array($minMarginQRY);
							$minMarginVAL=$minMarginROW['value'];

							$joinQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Recruiter' AND comment='Join this quarter'");
							$joinROW=mysqli_fetch_array($joinQRY);
							$joinVAL=$joinROW['value'];

							$dataObject = json_decode($rdataD['detail_data'],true);
							foreach($dataObject AS $key => $dataObject2){
						?>
						<tr style="font-size: 12px;">
							<td style="text-align: left;vertical-align: middle;"><?php echo $dataObject[$key]["candidate"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["client"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["join_date"]; ?></td>
						<?php if($dataObject[$key]["status"]=="Active"){?>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["status"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["termi_date"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["status"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["termi_date"]; ?></td>
						<?php } ?>
						<?php if($dataObject[$key]["eligibility"]=='Yes'){ ?>
							<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $dataObject[$key]["eligibility"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["eligibility"]; ?></td>
						<?php } ?>
						<?php if($dataObject[$key]["margin"]<=$minMarginVAL){?>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["margin"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["margin"]; ?></td>
						<?php } ?>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["cum_margin"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["percentage"]."%"; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["total_hour"]; ?></td>
							<td style="text-align: center;vertical-align: middle;font-weight: bold;"><?php echo $dataObject[$key]["inc_amount"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["per_hire_amount"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["new_acc_amount"]; ?></td>
						<?php
							if($dataObject[$key]["eligibility"]=='Yes' && $dataObject[$key]["margin"]>$minMarginVAL){
						?>
							<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $dataObject[$key]["final_amount"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;"><?php echo $dataObject[$key]["final_amount"]; ?></td>
						<?php } ?>
						</tr>
						<?php
							}
						?>
						</tbody>
						<tfoot>
							<tr style="background-color: #ccc;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;"><?php echo $rdataD['total_candidate']; ?></th>
								<th style="text-align: center;vertical-align: middle;" colspan="12">Total No. of Join <span style="color: #2266AA;">(In this Quater)</span> : <?php echo $rdataD['total_join']; ?></th>
							<?php if($rdataD['total_join']>=$joinVAL){?>
								<th style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $rdataD['final_incentive']; ?></th>
							<?php }else{ ?>
								<th style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;"><?php echo $rdataD['final_incentive']; ?></th>
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
		if($viewby=="CS Manager"){
?>
	<section id="LIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row">
				<?php
					//Inside PS Personnel Name
					$rdataQ="SELECT * FROM incentive_data WHERE id='$viewId'";
					$rdataR=mysqli_query($misReportsConn,$rdataQ);
					$rdataD=mysqli_fetch_array($rdataR);
				?>
				<div class="col-md-4 col-md-offset-4" style="background-color: #ccc;color: #000;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">Recruiter : <span style="font-size: 16px;color: #333;"><?php echo ucwords($rdataD['person_name']); ?></span><span style="font-size: 15px;color: #449D44;"><?php echo " (".$rdataD['period'].")"; ?></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="LIRdata" class="table table-striped table-bordered">
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
								<th style="text-align: center;vertical-align: middle;" data-toggle="tooltip" data-placement="auto" title="Margin *Percentage / (100*60)*Total Hours">Incentive Amount</th>
							</tr>
						</thead>
						<tbody>
						<?php
							$minMarginQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Minimum Margin'");
							$minMarginROW=mysqli_fetch_array($minMarginQRY);
							$minMarginVAL=$minMarginROW['value'];

							$joinQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='CS Manager' AND comment='Join this month'");
							$joinROW=mysqli_fetch_array($joinQRY);
							$joinVAL=$joinROW['value'];

							$dataObject = json_decode($rdataD['detail_data'],true);
							foreach($dataObject AS $key => $dataObject2){
						?>
						<tr style="font-size: 12px;">
							<td style="text-align: left;vertical-align: middle;"><?php echo $dataObject[$key]["recruiter"]; ?></td>
							<td style="text-align: left;vertical-align: middle;"><?php echo $dataObject[$key]["recruiter_manager"]; ?></td>
							<td style="text-align: left;vertical-align: middle;"><?php echo $dataObject[$key]["candidate"]; ?></td>
							<td style="text-align: left;vertical-align: middle;"><?php echo $dataObject[$key]["client"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["join_date"]; ?></td>
						<?php if($dataObject[$key]["status"]=="Active"){?>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["status"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["termi_date"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["status"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["termi_date"]; ?></td>
						<?php } ?>
						<?php if($dataObject[$key]["eligibility"]=='Yes'){ ?>
							<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $dataObject[$key]["eligibility"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["eligibility"]; ?></td>
						<?php } ?>
						<?php if($dataObject[$key]["margin"]<=$minMarginVAL){?>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["margin"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["margin"]; ?></td>
						<?php } ?>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["cum_margin"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["percentage"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["total_hour"]; ?></td>
						<?php
							if($dataObject[$key]["eligibility"]=='Yes' && $dataObject[$key]["margin"]>$minMarginVAL){
						?>
							<td style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $dataObject[$key]["inc_amount"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;"><?php echo $dataObject[$key]["inc_amount"]; ?></td>
						<?php } ?>
						</tr>
						<?php
							}
						?>
						</tbody>
						<tfoot>
							<tr style="background-color: #ccc;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;"><?php echo $rdataD['total_recruiter']; ?></th>
								<th style="text-align: center;vertical-align: middle;"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $rdataD['total_candidate']; ?></th>
								<th style="text-align: center;vertical-align: middle;" colspan="9">Total No. of Join : <?php echo $rdataD['total_join']; ?></th>
							<?php if($rdataD['total_join']>=$joinVAL){?>
								<th style="text-align: center;vertical-align: middle;background-color: #449D44;color: #fff;"><?php echo $rdataD['final_incentive']; ?></th>
							<?php }else{ ?>
								<th style="text-align: center;vertical-align: middle;background-color: #fc2828;color: #fff;"><?php echo $rdataD['final_incentive']; ?></th>
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
		if($viewby=="Post Sales"){
?>
	<section id="LIRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row">
				<?php
					//Inside PS Personnel Name
					$rdataQ="SELECT * FROM incentive_data WHERE id='$viewId'";
					$rdataR=mysqli_query($misReportsConn,$rdataQ);
					$rdataD=mysqli_fetch_array($rdataR);
				?>
				<div class="col-md-4 col-md-offset-4" style="background-color: #ccc;color: #000;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">Recruiter : <span style="font-size: 16px;color: #333;"><?php echo ucwords($rdataD['person_name']); ?></span><span style="font-size: 15px;color: #449D44;"><?php echo " (".$rdataD['period'].")"; ?></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-10 col-md-offset-1">
					<table id="LIRdata" class="table table-striped table-bordered">
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
							$minMarginQRY=mysqli_query($misReportsConn, "SELECT value FROM incentive_criteria WHERE personnel='Post Sales' AND comment='Minimum Margin'");
							$minMarginROW=mysqli_fetch_array($minMarginQRY);
							$minMarginVAL=$minMarginROW['value'];

							$dataObject = json_decode($rdataD['detail_data'],true);
							foreach($dataObject AS $key => $dataObject2){
						?>
						<tr style="font-size: 13px;">
							<td style="text-align: left;vertical-align: middle;"><?php echo $dataObject[$key]["candidate"]; ?></td>
							<td style="text-align: left;vertical-align: middle;"><?php echo $dataObject[$key]["client"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["join_date"]; ?></td>
						<?php if($dataObject[$key]["status"]=="Active"){?>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["status"]; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $dataObject[$key]["termi_date"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["status"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["termi_date"]; ?></td>
						<?php } ?>
						<?php if($dataObject[$key]["eligibility"]=='Yes'){ ?>
							<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $dataObject[$key]["eligibility"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["eligibility"]; ?></td>
						<?php } ?>
						<?php
							if($dataObject[$key]["eligibility"]=='Yes' && $dataObject[$key]["margin"]>$minMarginVAL){
						?>
							<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $dataObject[$key]["margin"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $dataObject[$key]["exclusive"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $dataObject[$key]["direct"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $dataObject[$key]["pass_through"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #449D44;"><?php echo $dataObject[$key]["new_client"]; ?></td>
						<?php }else{ ?>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["margin"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["exclusive"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["direct"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["pass_through"]; ?></td>
							<td style="text-align: center;vertical-align: middle;color: #fc2828;"><?php echo $dataObject[$key]["new_client"]; ?></td>
						<?php } ?>
						</tr>
						<?php
							}
						?>
						</tbody>
						<tfoot>
							<tr style="background-color: #bbb;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $rdataD['total_candidate']; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2" colspan="5"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo $rdataD['total_margin']; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $rdataD['ps_exclusive_req']; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $rdataD['ps_direct_req']; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $rdataD['ps_pass_through_req']; ?></th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2"><?php echo $rdataD['ps_new_client']; ?></th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;"><?php echo $rdataD['incentive_amount']; ?></th>
							</tr>
							<tr style="background-color: #bbb;color: #2266AA;font-size: 16px;">
								<th style="text-align: center;vertical-align: middle;" colspan="11">Final Incentive Amount : <span style="color: #000;"><?php echo $rdataD['final_incentive']; ?></span></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</section>
<?php
		}
		if($viewby=="Sales"){
?>
<?php
		}
		if($viewby=="Proposal"){
?>
<?php
		}
	}
?>



</body>
</html>
<?php
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