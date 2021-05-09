<?php
	include_once("../../../security.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include_once('../../../config.php');
		
    	$childUser = $_SESSION['userMember'];
		$reportID = '31';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
		$sessionResult = mysqli_query($misReportsConn, $sessionQuery);
		$sessionROW = mysqli_fetch_array($sessionResult);
		if(mysqli_num_rows($sessionResult) > 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Detail Report Client - Job Date Created</title>

	<?php
		include_once('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th,
		table.dataTable tbody td{
			padding: 5px 1px;
		}
		.btnx,
		.btnx:focus{
			background-color: #2266AA;
			color: #fff;
			font-weight: bold;
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
			$('#CPRdatatable').removeClass("hidden");


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


			var tableX = $('#CPRdata').DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			    dom: 'Bfrtip',
			    "aaSorting": [[0,'desc']],
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
	</script>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Detail Report Client - Job Date Created</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section id="MainSection" class="hidden" style="margin-top: 30px;margin-bottom: 100px;">
		<div class="container">
			<div class="row">
				<div class="col-md-2 col-md-offset-4">
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
			<form id="mulmonth" action="index.php" method="get">
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
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control" style="background-color: #2266AA;border-radius: 0px;border: #2266AA;color: #fff;outline: none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to home</button>
					</div>
					<div class="col-md-2">
						<button type="submit" class="form-control" name="CPRsubmit" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
					</div>
				</div>
			</form>

			<!--POST daterange Section-->
			<form id="daterange" class="hidden" action="index.php" method="get">
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
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control" style="background-color: #2266AA;border-radius: 0px;border: #2266AA;color: #fff;outline: none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to home</button>
					</div>
					<div class="col-md-2">
						<button type="submit" class="form-control" name="CPRsubmit" style="background-color: #449D44;border-radius: 0px;border: #449D44;outline: none;color: #fff;"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	if(isset($_REQUEST['CPRsubmit'])){
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
	<section id="CPRdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="CPRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #ccc;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Date Created</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Client</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Joborder Id</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Company Job Id</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Status</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Title</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Recruiter</th>
							<?php if ($sessionROW['user_type'] != 'CS Manager') { ?>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Client Manager</th>
							<?php } ?>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Inside PS Personnel</th>
								<th style="text-align: center;vertical-align: middle;" colspan="7">Total</th>
							</tr>
							<tr style="background-color: #ccc;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;">Openings</th>
								<th style="text-align: center;vertical-align: middle;">Submission</th>
								<th style="text-align: center;vertical-align: middle;">Interview</th>
								<th style="text-align: center;vertical-align: middle;">Interview Decline</th>
								<th style="text-align: center;vertical-align: middle;">Offer</th>
								<th style="text-align: center;vertical-align: middle;">Join</th>
								<th style="text-align: center;vertical-align: middle;">Delivery Failed</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$openings=$totsub=$totiv=$totivd=$totoff=$totjoin=$totdfail=array();
								foreach($fromDate AS $key => $fromDate2){
									$fromDateX = $fromDate[$key];
									$toDateX = $toDate[$key];

									$mainQUERY = "SELECT
										a.cid,
									    a.cname,
									    a.owner,
									    a.joborder_id,
									    a.client_job_id,
									    a.status,
									    a.title,
									    IF((a.openings = '' OR a.openings IS NULL), 0, a.openings) AS openings,
									    a.jobDate,
									    a.recruiter,
									    a.client_manager,
									    a.inside_post_sales,
									    IF((b.totsub = '' OR b.totsub IS NULL), 0, b.totsub) AS totsub,
									    IF((b.totiv = '' OR b.totiv IS NULL), 0, b.totiv) AS totiv,
									    IF((b.totivd = '' OR b.totivd IS NULL), 0, b.totivd) AS totivd,
									    IF((b.totoff = '' OR b.totoff IS NULL), 0, b.totoff) AS totoff,
									    IF((b.totjoin = '' OR b.totjoin IS NULL), 0, b.totjoin) AS totjoin,
									    IF((b.totdfail = '' OR b.totdfail IS NULL), 0, b.totdfail) AS totdfail
									FROM
									(SELECT
									    comp.company_id AS cid,
									    comp.name AS cname,
									    comp.owner,
									    job.joborder_id,
									    job.client_job_id,
									    job.status,
									    job.title,
									    job.openings,
									    date_format(job.date_created, '%Y-%m-%d') AS jobDate,
									    (SELECT concat(first_name,' ',last_name) AS rname FROM user WHERE user_id = job.recruiter) AS recruiter,
									    (SELECT concat(first_name,' ',last_name) AS rname FROM user WHERE user_id = comp.owner) AS client_manager,
									    (SELECT value FROM extra_field WHERE data_item_id = comp.company_id AND field_name = 'Inside Post Sales') AS inside_post_sales
									FROM
									    joborder AS job
									    JOIN company AS comp ON comp.company_id = job.company_id
									WHERE
									    date_format(job.date_created, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX'
									GROUP BY job.joborder_id) AS a
									LEFT OUTER JOIN
									(SELECT
									    job.joborder_id,
									    COUNT(CASE WHEN cjsh.status_to='400' THEN 1 END) AS totsub,
									    COUNT(CASE WHEN cjsh.status_to='500' THEN 1 END) AS totiv,
									    COUNT(CASE WHEN cjsh.status_to='560' THEN 1 END) AS totivd,
									    COUNT(CASE WHEN cjsh.status_to='600' THEN 1 END) AS totoff,
									    COUNT(CASE WHEN cjsh.status_to='800' THEN 1 END) AS totjoin,
									    COUNT(CASE WHEN cjsh.status_to='900' THEN 1 END) AS totdfail
									FROM
									    joborder AS job
									    JOIN company AS comp ON comp.company_id = job.company_id
									    JOIN candidate_joborder_status_history AS cjsh ON job.joborder_id = cjsh.joborder_id
									WHERE
									    date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX'
									GROUP BY job.joborder_id) AS b ON b.joborder_id = a.joborder_id";
									
									if ($sessionROW['user_type'] == 'CS Manager') {
										$findOwnerQUERY = mysqli_query($catsConn, "SELECT
											user_id
										FROM
											user
										WHERE
											user_name = '".$sessionROW['uname']."'");
										$findOwnerROW = mysqli_fetch_array($findOwnerQUERY);
										$mainQUERY .= " WHERE a.owner = '".$findOwnerROW['user_id']."' GROUP BY a.joborder_id";
									} else {
										$mainQUERY .= " GROUP BY a.joborder_id";
									}
									
									$mainRESULT = mysqli_query($catsConn, $mainQUERY);
									if(mysqli_num_rows($mainRESULT) > 0){
										while($mainROW = mysqli_fetch_array($mainRESULT)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['jobDate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID=<?php echo $mainROW['cid']; ?>" target="_blank"><?php echo $mainROW['cname']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=<?php echo $mainROW['joborder_id']; ?>" target="_blank"><?php echo $mainROW['joborder_id']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;word-break: break-word;width: 30px;"><?php echo $mainROW['client_job_id']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['status']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['title']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['recruiter']; ?></td>
							<?php if ($sessionROW['user_type'] != 'CS Manager') { ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['client_manager']; ?></td>
							<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['inside_post_sales']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $openings[] = $mainROW['openings']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $totsub[] = $mainROW['totsub']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $totiv[] = $mainROW['totiv']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $totivd[] = $mainROW['totivd']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $totoff[] = $mainROW['totoff']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $totjoin[] = $mainROW['totjoin']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $totdfail[] = $mainROW['totdfail']; ?></td>
							</tr>
							<?php
										}
									}
								}
							?>
						</tbody>
						<tfoot>
							<tr style="background-color: #ccc;">
							<?php if ($sessionROW['user_type'] == 'CS Manager') { ?>
								<th style="text-align: center;vertical-align: middle;" colspan="8"></th>
							<?php } else { ?>
								<th style="text-align: center;vertical-align: middle;" colspan="9"></th>
							<?php } ?>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($openings); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($totsub); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($totiv); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($totivd); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($totoff); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($totjoin); ?></th>
								<th style="text-align: center;vertical-align: middle;"><?php echo array_sum($totdfail); ?></th>
							</tr>
						</tfoot>
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
