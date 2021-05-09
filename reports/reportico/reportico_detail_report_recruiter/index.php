<?php
	include_once("../../../security.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include_once('../../../config.php');

    	$childUser = $_SESSION['userMember'];
		$reportID = '28';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
		$sessionResult = mysqli_query($misReportsConn, $sessionQuery);
		$sessionROW = mysqli_fetch_array($sessionResult);
		if(mysqli_num_rows($sessionResult) > 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Detail Report Recruiter</title>

	<?php
		include_once('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot td{
			padding: 5px;
		}
		table.dataTable tbody td{
			padding: 1px;
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
				"lengthMenu": [[10, 20, 30, -1], [10, 20, 30, "All"]],
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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Detail Report Recruiter</div>
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
							<tr style="background-color: #ccc;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Recruiter</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Joborder Id</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Company Job Id</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Job Title</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Date Created</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Client</th>
								<th style="text-align: center;vertical-align: middle;" rowspan="2">Candidate</th>
								<th style="text-align: center;vertical-align: middle;" colspan="7">Date</th>
							</tr>
							<tr style="background-color: #ccc;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">Submission</th>
								<th style="text-align: center;vertical-align: middle;">Interview</th>
								<th style="text-align: center;vertical-align: middle;">Interview Decline</th>
								<th style="text-align: center;vertical-align: middle;">Offer</th>
								<th style="text-align: center;vertical-align: middle;">Place</th>
								<th style="text-align: center;vertical-align: middle;">Extension</th>
								<th style="text-align: center;vertical-align: middle;">Delivery Failed</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($fromDate AS $key => $fromDate2){
									$fromDateX = $fromDate[$key];
									$toDateX = $toDate[$key];

									$mainQUERY="SELECT
										a1.recruiter_id,
										a1.recruiter_name,
										a1.recruiter_manager_name,
										a1.joborder_id,
										a1.client_job_id,
										a1.job_title,
										a1.job_date_created,
										a1.company_id,
										a1.company_name,
										a1.company_owner,
										a1.candidate_id,
										a1.candidate_name,
										a1.candidate_joborder_status,
										a2.submission_date,
										a3.interview_date,
										a4.interview_decline_date,
										a5.offer_date,
										a6.join_date,
										a7.extension_date,
										a8.delivery_failed_date
									FROM
									(SELECT
										u.user_id AS recruiter_id,
										CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
										u.notes AS recruiter_manager_name,
										job.joborder_id,
										job.client_job_id,
										job.title AS job_title,
										DATE_FORMAT(job.date_created, '%m-%d-%Y') AS job_date_created,
										comp.company_id,
										comp.name AS company_name,
										comp.owner AS company_owner,
										can.candidate_id,
										CONCAT(can.first_name,' ',can.last_name) AS candidate_name,
										cjsh.status_to AS candidate_joborder_status
									FROM
										cats.user AS u
										LEFT JOIN cats.candidate_joborder AS cj ON cj.added_by = u.user_id
										LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
										LEFT JOIN cats.candidate AS can ON can.candidate_id = cjsh.candidate_id
									    LEFT JOIN cats.joborder AS job ON job.joborder_id = cjsh.joborder_id
									    LEFT JOIN cats.company AS comp ON comp.company_id = job.company_id
									WHERE
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX') AS a1
									LEFT JOIN
									(SELECT
										cjsh.joborder_id,
										cjsh.candidate_id,
										DATE_FORMAT(cjsh.date,'%m-%d-%Y') AS submission_date
									FROM
										cats.candidate_joborder_status_history AS cjsh
									WHERE
										cjsh.status_to = '400'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX') AS a2 ON a2.joborder_id = a1.joborder_id AND a2.candidate_id = a1.candidate_id
									LEFT JOIN
									(SELECT
										cjsh.joborder_id,
										cjsh.candidate_id,
										DATE_FORMAT(cjsh.date,'%m-%d-%Y') AS interview_date
									FROM
										cats.candidate_joborder_status_history AS cjsh
									WHERE
										cjsh.status_to = '500'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX') AS a3 ON a3.joborder_id = a1.joborder_id AND a3.candidate_id = a1.candidate_id
									LEFT JOIN
									(SELECT
										cjsh.joborder_id,
										cjsh.candidate_id,
										DATE_FORMAT(cjsh.date,'%m-%d-%Y') AS interview_decline_date
									FROM
										cats.candidate_joborder_status_history AS cjsh
									WHERE
										cjsh.status_to = '560'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX') AS a4 ON a4.joborder_id = a1.joborder_id AND a4.candidate_id = a1.candidate_id
									LEFT JOIN
									(SELECT
										cjsh.joborder_id,
										cjsh.candidate_id,
										DATE_FORMAT(cjsh.date,'%m-%d-%Y') AS offer_date
									FROM
										cats.candidate_joborder_status_history AS cjsh
									WHERE
										cjsh.status_to = '600'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX') AS a5 ON a5.joborder_id = a1.joborder_id AND a5.candidate_id = a1.candidate_id
									LEFT JOIN
									(SELECT
										cjsh.joborder_id,
										cjsh.candidate_id,
										DATE_FORMAT(cjsh.date,'%m-%d-%Y') AS join_date
									FROM
										cats.candidate_joborder_status_history AS cjsh
									WHERE
										cjsh.status_to = '800'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX') AS a6 ON a6.joborder_id = a1.joborder_id AND a6.candidate_id = a1.candidate_id
									LEFT JOIN
									(SELECT
										cjsh.joborder_id,
										cjsh.candidate_id,
										DATE_FORMAT(cjsh.date,'%m-%d-%Y') AS extension_date
									FROM
										cats.candidate_joborder_status_history AS cjsh
									WHERE
										cjsh.status_to = '620'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX') AS a7 ON a7.joborder_id = a1.joborder_id AND a7.candidate_id = a1.candidate_id
									LEFT JOIN
									(SELECT
										cjsh.joborder_id,
										cjsh.candidate_id,
										DATE_FORMAT(cjsh.date,'%m-%d-%Y') AS delivery_failed_date
									FROM
										cats.candidate_joborder_status_history AS cjsh
									WHERE
										cjsh.status_to = '900'
									AND
										DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX') AS a8 ON a8.joborder_id = a1.joborder_id AND a8.candidate_id = a1.candidate_id
									WHERE
										a1.candidate_joborder_status IN ('400', '500', '560', '600', '800', '620', '900')";
									
									if ($sessionROW['user_type'] == 'CS Manager') {
										$findOwnerQUERY = mysqli_query($catsConn, "SELECT
											user_id,
											CONCAT(first_name,' ',last_name) AS user_full_name
										FROM
											user
										WHERE
											user_name = '".$sessionROW['uname']."'");
										$findOwnerROW = mysqli_fetch_array($findOwnerQUERY);
										$mainQUERY .= " AND
											a1.recruiter_manager_name = '".$findOwnerROW['user_full_name']."'
										GROUP BY a1.joborder_id, a1.candidate_id";
									} else {
										$mainQUERY .= " GROUP BY a1.joborder_id, a1.candidate_id";
									}

									$mainRESULT = mysqli_query($catsConn, $mainQUERY);
									if(mysqli_num_rows($mainRESULT) > 0){
										while($mainROW = mysqli_fetch_array($mainRESULT)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW['recruiter_name']);?></td>
								<td style="text-align: center;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=<?php echo $mainROW['joborder_id']; ?>" target="_blank"><?php echo $mainROW['joborder_id']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;word-break: break-word;width: 30px;"><?php echo $mainROW['client_job_id']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['job_title']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['job_date_created']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID=<?php echo $mainROW['company_id']; ?>" target="_blank"><?php echo $mainROW['company_name']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=<?php echo $mainROW['candidate_id']; ?>" target="_blank"><?php echo $mainROW['candidate_name']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['submission_date']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['interview_date']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['interview_decline_date']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['offer_date']; ?></td>
							<?php if($mainROW['extension_date']==''){ ?>
								<td style="text-align: center;vertical-align: middle;color: #449D44;"><b><?php echo $mainROW['join_date']; ?></b></td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;"><b><?php echo $mainROW['join_date']; ?></b></td>
							<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['extension_date']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['delivery_failed_date']; ?></td>
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
