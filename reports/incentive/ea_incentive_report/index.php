<?php
	include("../../../security.php");
	include("../../../functions/reporting-service.php");
	$responseArray = array();
	$responseType = isset($_REQUEST['response_type']) && $_REQUEST['response_type'] == 1 ? 1 : 0;

    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='60';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){
			if ($responseType == 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>EA Incentive Report</title>

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
			$(".LoadingImage").hide();
			$('.MainSection').removeClass("hidden");
			$('.customizedDataTableSection').removeClass("hidden");

	        //customizedMultiMonth
	        $(".customizedMultiMonth").datepicker({
	            format: "mm/yyyy",
	            startView: 1,
	            minViewMode: 1,
	            maxViewMode: 2,
	            clearBtn: true,
	            multidate: false,
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
	        $(".customizedSelectBoxWithAll").multiselect('selectAll', false);
	        $(".customizedSelectBoxWithAll").multiselect('updateButtonText');

	        //Datatable Calling START
	        var customizedDataTableWithOutPaging = $('.customizedDataTableWithOutPaging').DataTable({
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
	        customizedDataTableWithOutPaging.button(0).nodes().css('background', '#2266AA');
	        customizedDataTableWithOutPaging.button(0).nodes().css('border', '#2266AA');
	        customizedDataTableWithOutPaging.button(0).nodes().css('color', '#fff');
	        customizedDataTableWithOutPaging.button(0).nodes().html('Download Report');
	        customizedDataTableWithOutPaging.button(1).nodes().css('background', '#449D44');
	        customizedDataTableWithOutPaging.button(1).nodes().css('border', '#449D44');
	        customizedDataTableWithOutPaging.button(1).nodes().css('color', '#fff');

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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">EA Incentive Report</div>
				<div class="LoadingImage col-md-12" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section class="MainSection hidden" style="margin-top: 20px;margin-bottom: 100px;">
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
							<input class="form-control customizedMultiMonth" name="multimonth" placeholder="MM/YYYY" type="text" value="<?php if(isset($_REQUEST['multimonth'])){echo $_REQUEST['multimonth'];}?>" autocomplete="off" required>
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
					<div class="col-md-4 col-md-offset-4">
						<label>Select EA Personnel :</label>
						<select class="customizedSelectBoxWithAll" name="eapersonnel[]" multiple required>
							<?php
								$eaPersonnelList = eaPersonnelList($catsConn);
								print_r($eaPersonnelList);
							?>
						</select>
					</div>
				</div>
				<div class="row" style="margin-top: 35px;">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
					</div>
					<div class="col-md-2">
						<button type="submit" class="form-control" name="overviewreport" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	}
	//POST Overview Section START
	if (isset($_REQUEST['overviewreport'])) {
		$monthsData = $_REQUEST['multimonth'];
		$eaPersonnelData = $_REQUEST['eapersonnel'];
		if ($responseType == 0) {
?>
	<section class="customizedDataTableSection hidden">
		<div class="container-fluid">
			<form id="EAincentive" onsubmit="return true">
				<div class="row" style="margin-bottom: 50px;">
					<div class="col-md-12">
						<table class="table table-striped table-bordered customizedDataTableWithOutPaging">
							<thead>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th class='no-sort' style="text-align: center;vertical-align: middle;" rowspan="3"><input style="height:20px;width:20px;cursor: pointer;outline: none;" type='checkbox' name='select_all' id='select_all'></th>
									<th style="text-align: center;vertical-align: middle;" rowspan="3">EA Personnel</th>
									<th style="text-align: center;vertical-align: middle;" colspan="6">After Completing</th>
									<!--<th style="text-align: center;vertical-align: middle;" colspan="6">Extra Amount For</th>-->
									<th style="text-align: center;vertical-align: middle;" rowspan="3">Final Incentive</th>
									<th style="text-align: center;vertical-align: middle;" colspan="3">Adjustment</th>
									<th style="text-align: center;vertical-align: middle;" rowspan="3">Final Amount</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th style="text-align: center;vertical-align: middle;" colspan="2">3 Months</th>
									<th style="text-align: center;vertical-align: middle;" colspan="2">6 Months</th>
									<th style="text-align: center;vertical-align: middle;" colspan="2">12 Months</th>
									<!--<th style="text-align: center;vertical-align: middle;" colspan="2">New Hire</th>
									<th style="text-align: center;vertical-align: middle;" colspan="2">Redeployment</th>
									<th style="text-align: center;vertical-align: middle;" colspan="2">Retention</th>-->
									<th class="no-sort" style="text-align: center;vertical-align: middle;" rowspan="2">Method</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;" rowspan="2">Amount</th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;" rowspan="2">Comment</th>
								</tr>
								<tr style="background-color: #bbb;color: #000;font-size: 13px;">
									<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-user"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-inr"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-user"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-inr"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-user"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;"><i class="fa fa-inr"></i></th>
									<!--<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-user"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-inr"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-user"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-inr"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;"><i class="fa fa-user"></i></th>
									<th class="no-sort" style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;"><i class="fa fa-inr"></i></th>-->
								</tr>
							</thead>
							<tbody>
							<?php
								}
								$iii = 1;
								foreach ($eaPersonnelData as $key => $eaPersonnelValue) {
									$monthsData=str_replace("-", "/", $monthsData);
									$mdata=explode("/",$monthsData);
									
									$dateObj = DateTime::createFromFormat('!m', $mdata[0]);
									
									$lastDateOfSelectedMonth = date('Y-m-d', strtotime('last day of '.$dateObj->format('F').' '.$mdata[1]));
									
									$lastThirdMonth = date("m-Y",strtotime("-3 Months", (strtotime($lastDateOfSelectedMonth))));
									$lastSixthmonth = date("m-Y",strtotime("-6 Months", (strtotime($lastDateOfSelectedMonth))));
									$lastTwelfthMonth = date("m-Y",strtotime("-12 Months", (strtotime($lastDateOfSelectedMonth))));

									$mainQUERY = mysqli_query($vtechhrmConn, "SELECT
									    e.id AS employeeId,
									    concat(e.first_name,' ',e.last_name) AS employeeName,
									    DATE_FORMAT(e.custom7, '%m-%d-%Y') AS joiningDate,
									    (CASE
									        WHEN date_format(e.custom7, '%m-%Y') = '$lastThirdMonth' THEN '3'
									        WHEN date_format(e.custom7, '%m-%Y') = '$lastSixthmonth' THEN '6'
									        WHEN date_format(e.custom7, '%m-%Y') = '$lastTwelfthMonth' THEN '12'
									    END) AS completedMonth,
									    comp.company_id AS companyId,
									    comp.name AS companyName,
									    (SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'EA Team' AND ic.comment = '3 Months') AS threeMonthRate,
									    (SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'EA Team' AND ic.comment = '6 Months') AS sixMonthRate,
									    (SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'EA Team' AND ic.comment = '12 Months') AS twelveMonthRate
									FROM
									    employees AS e
									    JOIN vtech_mappingdb.system_integration AS mp ON e.id = mp.h_employee_id
									    JOIN cats.company AS comp ON comp.company_id = mp.c_company_id
									    JOIN vtech_mappingdb.manage_ea_roles AS mer ON mer.reference_id = e.id
									WHERE
									    e.status = 'Active'
									AND
										mer.reference_type = 'Employee'
									AND
										mer.mapping_value = '$eaPersonnelValue'
									AND
										date_format(e.custom7, '%Y-%m-%d') > (SELECT date_format(he.confirmation_date, '%Y-%m-%d') AS confirmationDate FROM hrm_india.employees AS he WHERE concat(he.first_name,' ',he.last_name) = '$eaPersonnelValue')
									AND
										date_format(e.custom7, '%m-%Y') IN ('$lastThirdMonth','$lastSixthmonth','$lastTwelfthMonth')
									GROUP BY employeeName
									ORDER BY e.custom7 ASC, employeeName ASC");

									$threeMonthIncentive = $sixMonthIncentive = $twelveMonthIncentive = array();
									
									if (mysqli_num_rows($mainQUERY) > 0) {
										while ($mainROW = mysqli_fetch_array($mainQUERY)) {
											if ($mainROW['completedMonth'] == '3') {
												$threeMonthIncentive[] = $mainROW['threeMonthRate'];
											} elseif ($mainROW['completedMonth'] == '6') {
												$sixMonthIncentive[] = $mainROW['sixMonthRate'];
											} elseif ($mainROW['completedMonth'] == '12') {
												$twelveMonthIncentive[] = $mainROW['twelveMonthRate'];
											}

										}
									}
									$selectQUERY = mysqli_query($misReportsConn, "SELECT * FROM ea_incentive_data WHERE person_name = '$eaPersonnelValue' AND period='$monthsData'");
									if(mysqli_num_rows($selectQUERY) > 0) {
										$selectROW = mysqli_fetch_array($selectQUERY);
										if($responseType == 0) {
							?>
							<tr style="background-color: #c3dcf4;">
								<td style="text-align: center;vertical-align: middle;"><i class="fa fa-lock" style="font-size: 18px;color: #2266AA;"></i></td>
								<td style="text-align: left;vertical-align: middle;"><?php echo $selectROW['person_name']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['three_month_candidate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['three_month_price']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['six_month_candidate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['six_month_price']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['twelve_month_candidate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $selectROW['twelve_month_price']; ?></td>
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
									} else {
										if($responseType == 0){
							?>
							<tr style="font-size: 14px;">
								<input type="hidden" name="person_name[<?php echo $iii; ?>]" value="<?php echo ucwords($eaPersonnelValue); ?>">
								<input type="hidden" name="period[<?php echo $iii; ?>]" value="<?php echo $monthsData; ?>">
								<input type="hidden" name="three_month_candidate[<?php echo $iii; ?>]" value="<?php echo count($threeMonthIncentive); ?>">
								<input type="hidden" name="three_month_price[<?php echo $iii; ?>]" value="<?php echo array_sum($threeMonthIncentive); ?>">
								<input type="hidden" name="six_month_candidate[<?php echo $iii; ?>]" value="<?php echo count($sixMonthIncentive); ?>">
								<input type="hidden" name="six_month_price[<?php echo $iii; ?>]" value="<?php echo array_sum($sixMonthIncentive); ?>">
								<input type="hidden" name="twelve_month_candidate[<?php echo $iii; ?>]" value="<?php echo count($twelveMonthIncentive); ?>">
								<input type="hidden" name="twelve_month_price[<?php echo $iii; ?>]" value="<?php echo array_sum($twelveMonthIncentive); ?>">
								<input type="hidden" name="final_incentive[<?php echo $iii; ?>]" id="mainamount<?php echo $iii; ?>" value="<?php echo $totalIncetive = array_sum($threeMonthIncentive) + array_sum($sixMonthIncentive) + array_sum($twelveMonthIncentive);; ?>">

								<input type="hidden" name="detail_link[<?php echo $iii; ?>]" value="<?php echo LOCAL_REPORT_PATH; ?>/incentive/ea_incentive_report/index.php?multimonth=<?php echo urlencode($monthsData); ?>&eapersonnel=<?php echo urlencode($eaPersonnelValue); ?>&detailreport=&response_type=1">

								<td style="text-align: center;vertical-align: middle;"><input type="checkbox" class="checkboxes" name="checked_id[<?php echo $iii; ?>]"style="height:18px;width:18px;cursor: pointer;outline: none;" value="<?php echo $iii; ?>"></td>
								<td style="vertical-align: middle;"><a href="?multimonth=<?php echo $monthsData; ?>&eapersonnel=<?php echo $eaPersonnelValue; ?>&detailreport=" style="cursor: pointer;"><?php echo ucwords($eaPersonnelValue); ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo count($threeMonthIncentive); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($threeMonthIncentive); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo count($sixMonthIncentive); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($sixMonthIncentive); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo count($twelveMonthIncentive); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo array_sum($twelveMonthIncentive); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $totalIncetive; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<select id="adjustment_method<?php echo $iii; ?>" name="adjustment_method[<?php echo $iii; ?>]" style="padding: 5px;cursor: pointer;" onchange="adjustMETHOD<?php echo $iii; ?>(this.value)" required>
										<option value="plus">ADD (+)</option>
										<option value="minus">SUB (-)</option>
									</select>
								</td>
								<td style="text-align: center;vertical-align: middle;">
									<input type="text" id="adjustment_amount<?php echo $iii; ?>" name="adjustment_amount[<?php echo $iii; ?>]" onchange="adjustAMOUNT<?php echo $iii; ?>(this.value)" maxlength="10" onkeypress='return event.charCode >= 46 && event.charCode <= 57' placeholder="0" style="width: 70%;padding: 2px 5px;">
								</td>
								<td style="text-align: center;vertical-align: middle;"><textarea name="adjustment_comment[<?php echo $iii; ?>]" rows="1" autocomplete="off"></textarea></td>
								<td style="text-align: center;vertical-align: middle;"><input type="text" id="final_amount<?php echo $iii; ?>" name="final_amount[<?php echo $iii; ?>]" style="background-color: #fff;color: #000;width: 70%;padding: 2px 5px;border: none;text-align: center;" value="<?php echo $totalIncetive; ?>" readonly></td>
							</tr>
							<script>
								/*Adjustment Calculation START*/
								function adjustAMOUNT<?php echo $iii; ?>(adjustValue) {
									var method = $('#adjustment_method<?php echo $iii; ?>').val();
									if ($('#mainamount<?php echo $iii; ?>').val() != '') {
										var mainAmount = parseInt($('#mainamount<?php echo $iii; ?>').val());
									} else {
										var mainAmount = $('#mainamount<?php echo $iii; ?>').val();
									}
									if (adjustValue != '') {
										var adjustAmount = parseInt(adjustValue);
									} else {
										var adjustAmount = adjustValue;
									}
									if (method == 'plus') {
										$('#final_amount<?php echo $iii; ?>').val(mainAmount + adjustAmount);
									} else {
										$('#final_amount<?php echo $iii; ?>').val(mainAmount - adjustAmount);
									}
								}
								function adjustMETHOD<?php echo $iii; ?>(method) {
									var adjustAmount = parseInt($('#adjustment_amount<?php echo $iii; ?>').val());
									var mainAmount = parseInt($('#mainamount<?php echo $iii; ?>').val());
									var method = method;
									if (method == 'plus') {
										$('#final_amount<?php echo $iii; ?>').val(mainAmount + adjustAmount);
									} else {
										$('#final_amount<?php echo $iii; ?>').val(mainAmount - adjustAmount);
									}
								}
								/*Adjustment Calculation END*/
							</script>
							<?php
										}
									}
											$iii++;
								}

								if ($responseType == 0) {
							?>
							</tbody>
							<tfoot>
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

		/*EAincentive Form Submission START*/
		$('#EAincentive').submit(function(e){
			if($('.checkboxes:checked').length == 0){
				alert("Please select atleast one checkbox!");
				return false;
			}
			if($('.checkboxes:checked').length > 0){
				e.preventDefault();
				$(".LoadingImage").show();
				$.ajax({
					url: 'lockamount.php',
					type: 'POST',
					data: $('#EAincentive').serialize(),
					success: function(response){
						$(".LoadingImage").hide();
						location.reload();
	            		alert('Incentive Successfully Locked!');
						//console.log(response);
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
	if (isset($_REQUEST['detailreport'])) {
		$monthsData = $_REQUEST['multimonth'];
		$eaPersonnelData = $_REQUEST['eapersonnel'];
		if ($responseType == 0) {
?>
	<section class="customizedDataTableSection hidden">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-4 col-md-offset-4" style="background-color: #ccc;color: #000;text-align: center;font-size: 17px;padding: 5px;margin-bottom: 20px;font-weight: bold;color: #2266AA;">EA Personnel : <span style="font-size: 16px;color: #333;"><?php echo ucwords($eaPersonnelData); ?></span><span style="font-size: 15px;color: #449D44;"><?php echo " (".$monthsData.")"; ?></span>
				</div>
			</div>
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-10 col-md-offset-1">
					<table class="table table-striped table-bordered customizedDataTableWithOutPaging">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;padding: 10px;">Candidate</th>
								<th style="text-align: center;vertical-align: middle;padding: 10px;">Client</th>
								<th style="text-align: center;vertical-align: middle;padding: 10px;">Joining Date</th>
								<th style="text-align: center;vertical-align: middle;padding: 10px;">Months Completed</th>
								<th style="text-align: center;vertical-align: middle;padding: 10px;">Incentive Amount</th>
							</tr>
						</thead>
						<tbody>
						<?php
							}
							$monthsData=str_replace("-", "/", $monthsData);
							$mdata=explode("/",$monthsData);
							
							$dateObj = DateTime::createFromFormat('!m', $mdata[0]);
							
							$lastDateOfSelectedMonth = date('Y-m-d', strtotime('last day of '.$dateObj->format('F').' '.$mdata[1]));
							
							$lastThirdMonth = date("m-Y",strtotime("-3 Months", (strtotime($lastDateOfSelectedMonth))));
							$lastSixthmonth = date("m-Y",strtotime("-6 Months", (strtotime($lastDateOfSelectedMonth))));
							$lastTwelfthMonth = date("m-Y",strtotime("-12 Months", (strtotime($lastDateOfSelectedMonth))));

							$mainQUERY = mysqli_query($vtechhrmConn, "SELECT
							    e.id AS employeeId,
							    concat(e.first_name,' ',e.last_name) AS employeeName,
							    DATE_FORMAT(e.custom7, '%m-%d-%Y') AS joiningDate,
							    (CASE
							        WHEN date_format(e.custom7, '%m-%Y') = '$lastThirdMonth' THEN '3'
							        WHEN date_format(e.custom7, '%m-%Y') = '$lastSixthmonth' THEN '6'
							        WHEN date_format(e.custom7, '%m-%Y') = '$lastTwelfthMonth' THEN '12'
							    END) AS completedMonth,
							    comp.company_id AS companyId,
							    comp.name AS companyName,
							    (SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'EA Team' AND ic.comment = '3 Months') AS threeMonthRate,
							    (SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'EA Team' AND ic.comment = '6 Months') AS sixMonthRate,
							    (SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'EA Team' AND ic.comment = '12 Months') AS twelveMonthRate
							FROM
							    employees AS e
							    JOIN vtech_mappingdb.system_integration AS mp ON e.id = mp.h_employee_id
							    JOIN cats.company AS comp ON comp.company_id = mp.c_company_id
							    JOIN vtech_mappingdb.manage_ea_roles AS mer ON mer.reference_id = e.id
							WHERE
							    e.status = 'Active'
							AND
								mer.reference_type = 'Employee'
							AND
								mer.mapping_value = '$eaPersonnelData'
							AND
								date_format(e.custom7, '%Y-%m-%d') > (SELECT date_format(he.confirmation_date, '%Y-%m-%d') AS confirmationDate FROM hrm_india.employees AS he WHERE concat(he.first_name,' ',he.last_name) = '$eaPersonnelData')
							AND
								date_format(e.custom7, '%m-%Y') IN ('$lastThirdMonth','$lastSixthmonth','$lastTwelfthMonth')
							GROUP BY employeeName
							ORDER BY e.custom7 ASC, employeeName ASC");
							$incentiveAmount = "0";
							$incentiveAmountArray = array();
							if (mysqli_num_rows($mainQUERY) > 0) {
								while ($mainROW = mysqli_fetch_array($mainQUERY)) {
									if ($mainROW['completedMonth'] == '3') {
										$incentiveAmount = $mainROW['threeMonthRate'];
									} elseif ($mainROW['completedMonth'] == '6') {
										$incentiveAmount = $mainROW['sixMonthRate'];
									} elseif ($mainROW['completedMonth'] == '12') {
										$incentiveAmount = $mainROW['twelveMonthRate'];
									}

									if ($responseType == 0) {
						?>
						<tr style="font-size: 14px;">
							<td style="vertical-align: middle;"><?php echo ucwords($mainROW['employeeName']); ?></td>
							<td style="vertical-align: middle;"><?php echo $mainROW['companyName']; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['joiningDate']; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['completedMonth']; ?></td>
							<td style="text-align: center;vertical-align: middle;"><?php echo $incentiveAmountArray[] = $incentiveAmount; ?></td>
						</tr>
						<?php
									} else {
										$responseArray[] = array('candidate' => ucwords($mainROW['employeeName']),
											'client' => ucwords($mainROW['companyName']),
											'joining_date' => ucwords($mainROW['joiningDate']),
											'months_completed' => $mainROW['completedMonth'],
											'incentive_amount' => $incentiveAmount);
									}
								}
							}
							if ($responseType == 0) {
						?>
						</tbody>
						<tfoot>
							<tr style="background-color: #bbb;color: #000;font-size: 15px;">
								<th style="text-align: center;vertical-align: middle;" colspan="4"></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo number_format(array_sum($incentiveAmountArray)); ?></th>
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
