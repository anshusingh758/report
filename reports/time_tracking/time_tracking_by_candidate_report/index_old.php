<?php
	include_once("../../../security.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include_once('../../../config.php');

    	$childUser = $_SESSION['userMember'];
		$reportID = '36';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
		$sessionResult=mysqli_query($misReportsConn, $sessionQuery);
		if(mysqli_num_rows($sessionResult) > 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Time Tracking by Candidate Report</title>

	<?php
		include_once('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot td{
			padding: 4px;
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
			font-weight: bold;
			outline: none;
			border-color: #2266AA;
			border-radius: 0px;
		}
	</style>
	
	<script>
		$(document).ready(function(){
			$("#LoadingImage").hide();
			$('#MainSection').removeClass("hidden");
			$('#EXPdatatable').removeClass("hidden");


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


			$("#fdate").datepicker({
				todayHighlight: true,
				clearBtn: true,
				orientation: "top",
				autoclose: true
			});


			$("#tdate").datepicker({
				todayHighlight: true,
				clearBtn: true,
				orientation: "top",
				autoclose: true
			});


			var tableX = $('#EXPdata').DataTable({
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
			tableX.button(0).nodes().css('background', '#2266AA');
			tableX.button(0).nodes().css('border', '#2266AA');
			tableX.button(0).nodes().css('color', '#fff');
			tableX.button(0).nodes().html('Download Report');
			tableX.button(1).nodes().css('background', '#449D44');
			tableX.button(1).nodes().css('border', '#449D44');
			tableX.button(1).nodes().css('color', '#fff');


			$('#uniq1').click(function(e){
				e.preventDefault();
				$('#daterange').addClass("hidden");
				$('#mulmonth').removeClass("hidden");
				$('#uniq1').addClass("btnx");
				$('#uniq2').addClass("btny");
				$('#uniq1').removeClass("btny");
				$('#uniq2').removeClass("btnx");
			});

			
			$('#uniq2').click(function(e){
				e.preventDefault();
				$('#mulmonth').addClass("hidden");
				$('#daterange').removeClass("hidden");
				$('#uniq2').addClass("btnx");
				$('#uniq1').addClass("btny");
				$('#uniq2').removeClass("btny");
				$('#uniq1').removeClass("btnx");
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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Time Tracking by Candidate Report</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section id="MainSection" class="hidden" style="margin-top: 30px;margin-bottom: 100px;">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="btny form-control"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Home</button>
				</div>
				<div class="col-md-2 col-md-offset-2">
					<input type="button" class="form-control btnx" id="uniq1" value="Months">
				</div>
				<div class="col-md-2">
					<input type="button" class="form-control btny" id="uniq2" value="Date Range">
				</div>
				<div class="col-md-4">
					<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
				</div>
			</div>

			<!--POST multimonth Section-->
			<form id="mulmonth" action="index_old.php" method="get">
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4 col-md-offset-4">
						<label>Select Months:</label>
						<div class="input-group">
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control" id="multimonth" name="multimonth" placeholder="MM/YYYY" type="text" value="<?php if(isset($_REQUEST['multimonth'])){echo $_REQUEST['multimonth'];}?>"  autocomplete="off" required>
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
						</div>
					</div>
				</div>

				<div class="row" style="margin-top: 35px;">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
					</div>
					<div class="col-md-2">
						<button type="submit" class="form-control" name="EXPsubmit" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
					</div>
				</div>
			</form>

			<!--POST daterange Section-->
			<form id="daterange" class="hidden" action="index_old.php" method="get">
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-2 col-md-offset-4">
						<label>Date From :</label>
						<div class="input-group">
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control" id="fdate" name="fdate" placeholder="MM/DD/YYYY" type="text" value="<?php if(isset($_REQUEST['fdate'])) { echo $_REQUEST['fdate']; }?>"  autocomplete="off" required>
						</div>
					</div>
					<div class="col-md-2">
						<label>Date To :</label>
						<div class="input-group">
							<div class="input-group-addon" style="background-color: #2266AA;border-color: #2266AA;color: #fff;">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control" id="tdate" name="tdate" placeholder="MM/DD/YYYY" type="text" value="<?php if(isset($_REQUEST['tdate'])) { echo $_REQUEST['tdate']; }?>"  autocomplete="off" required>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 35px;">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="goBack()" class="btnx form-control"><i class="fa fa-backward"></i> Go Back</button>
					</div>
					<div class="col-md-2">
						<button type="submit" class="form-control" name="EXPsubmit" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	if(isset($_REQUEST['EXPsubmit'])){
		$fromDate=$toDate=array();
		if(isset($_REQUEST['multimonth'])){
			$monthsdata = array_unique(explode(",", $_REQUEST['multimonth']));
			foreach($monthsdata AS $monthsdata2){
				$dateGiven = explode("/", $monthsdata2);
				$dateModified = $dateGiven[1]."-".$dateGiven[0];
				
				$fromDate[] = date('Y-m-01', strtotime($dateModified));
				$toDate[] = date('Y-m-t', strtotime($dateModified));
			}
		}else{
			$fromDate[] = date('Y-m-d', strtotime($_REQUEST['fdate']));
			$toDate[] = date('Y-m-d', strtotime($_REQUEST['tdate']));
?>
			<script>
				$('#mulmonth').addClass("hidden");
				$('#daterange').removeClass("hidden");
				$('#uniq2').addClass("btnx");
				$('#uniq1').addClass("btny");
				$('#uniq2').removeClass("btny");
				$('#uniq1').removeClass("btnx");
			</script>
<?php
		}
?>
	<section id="EXPdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-10 col-md-offset-1">
					<table id="EXPdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #ccc;color: #000;font-size: 13px;">
				                <th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
				                <th style="text-align: center;vertical-align: middle;" colspan="5">ATS</th>
				                <th style="text-align: center;vertical-align: middle;" colspan="2">HRM</th>
				                <th style="text-align: center;vertical-align: middle;" colspan="1">QB</th>
				            </tr>
							<tr style="background-color: #ccc;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">First Job Received</th>
								<th style="text-align: center;vertical-align: middle;">First Submission</th>
								<th style="text-align: center;vertical-align: middle;">First Interview</th>
								<th style="text-align: center;vertical-align: middle;">First Offer</th>
								<th style="text-align: center;vertical-align: middle;">First Join</th>
								<th style="text-align: center;vertical-align: middle;">First Timesheet</th>
								<th style="text-align: center;vertical-align: middle;">First Invoice</th>
								<th style="text-align: center;vertical-align: middle;">First Payment</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($fromDate AS $key => $fromDate2){
									$fromDateX = $fromDate[$key];
									$toDateX = $toDate[$key];

									$mainQUERY = "SELECT
										can.candidate_id AS can_id,
									    concat(e.first_name,' ',e.last_name) AS ename,
									    se.c_candidate_id AS canid,
									    date_format(can.date_created, '%m-%d-%Y') AS jobreceived,
									    MIN(CASE WHEN cjsh.status_to = '400' THEN date_format(cjsh.date, '%m-%d-%Y') END) AS datesub,
									    MIN(CASE WHEN cjsh.status_to = '500' THEN date_format(cjsh.date, '%m-%d-%Y') END) AS dateiv,
									    MIN(CASE WHEN cjsh.status_to = '600' THEN date_format(cjsh.date, '%m-%d-%Y') END) AS dateoff,
									    MIN(CASE WHEN cjsh.status_to = '800' THEN date_format(cjsh.date, '%m-%d-%Y') END) AS datejoin,
									    MIN(CASE WHEN ets.status = 'Approved' THEN date_format(ets.date_start, '%m-%d-%Y') END) AS datetsstart,
									    MIN(CASE WHEN ets.status = 'Approved' THEN date_format(ets.date_end, '%m-%d-%Y') END) AS datetsend,
									    MIN(date_format(ir.date_from, '%m-%d-%Y')) AS dateivfrom,
									    MIN(date_format(ir.date_to, '%m-%d-%Y')) AS dateivto
									FROM
										employees AS e
									    JOIN employeetimesheets AS ets ON ets.employee = e.id
									    JOIN vtech_mappingdb.invoice_report AS ir ON ir.eid = e.id
									    JOIN vtech_mappingdb.system_integration AS se ON se.h_employee_id = e.id
									    JOIN cats.candidate AS can ON can.candidate_id = se.c_candidate_id
									    JOIN cats.candidate_joborder_status_history AS cjsh ON can.candidate_id = cjsh.candidate_id
									WHERE
										date_format(e.custom7,'%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX'
									GROUP BY can.candidate_id";
									$mainRESULT = mysqli_query($vtechhrmConn, $mainQUERY);
									if(mysqli_num_rows($mainRESULT) > 0){
										while($mainROW = mysqli_fetch_array($mainRESULT)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=<?php echo $mainROW['can_id']; ?>" target="_blank"><?php echo ucwords($mainROW['ename']); ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['jobreceived']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['datesub']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['dateiv']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['dateoff']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['datejoin']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['datetsstart']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['dateivfrom']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ""; ?></td>
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
?>

</body>
</html>
<?php
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
