<?php
	include_once("../../../security.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include_once('../../../config.php');

    	$childUser = $_SESSION['userMember'];
		$reportID = '33';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
		$sessionResult = mysqli_query($misReportsConn, $sessionQuery);
		if(mysqli_num_rows($sessionResult) > 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Placement Report</title>

	<?php
		include_once('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th,
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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Placement Report</div>
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
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">Candidate</th>
								<th style="text-align: center;vertical-align: middle;">Status</th>
								<th style="text-align: center;vertical-align: middle;">Placement Date</th>
								<th style="text-align: center;vertical-align: middle;">Company</th>
								<th style="text-align: center;vertical-align: middle;">Company Manager</th>
								<th style="text-align: center;vertical-align: middle;">Joborder ID</th>
								<th style="text-align: center;vertical-align: middle;">Job Title</th>
								<th style="text-align: center;vertical-align: middle;">Recruiter</th>
								<th style="text-align: center;vertical-align: middle;">CS Manager</th>
								<th style="text-align: center;vertical-align: middle;">Inside Sales Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Inside PS Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Onsite Sales Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Onsite PS Personnel</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($fromDate AS $key => $fromDate2){
									$fromDateX = $fromDate[$key];
									$toDateX = $toDate[$key];

									$mainQUERY = "SELECT
										cjsh.candidate_id AS can_id,
										concat(can.first_name,' ',can.last_name) AS can_name,
									    cjsh.status_to AS can_status,
										DATE_FORMAT(cjsh.date, '%m-%d-%Y') AS placement_date,
										comp.company_id AS comp_id,
										comp.name AS comp_name,
									    (SELECT concat(first_name,' ',last_name) AS cmname FROM user WHERE user_id = comp.owner) AS client_manager,
										job.joborder_id AS job_id,
									    job.title AS job_title,
									    (SELECT concat(first_name,' ',last_name) AS rname FROM user WHERE user_id = cj.added_by) AS recruiter,
									    (SELECT notes FROM user WHERE user_id = cj.added_by) AS cs_manager,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Sales Person1' AND data_item_id = comp.company_id) AS inside_sales1,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Sales Person2' AND data_item_id = comp.company_id) AS inside_sales2,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Post Sales' AND data_item_id = comp.company_id) AS inside_post_sales,
									    (SELECT value FROM extra_field WHERE field_name = 'OnSite Sales Person' AND data_item_id = comp.company_id) AS onsite_sales,
									    (SELECT value FROM extra_field WHERE field_name = 'OnSite Post Sales' AND data_item_id = comp.company_id) AS onsite_post_sales
									FROM
										candidate_joborder_status_history AS cjsh
										JOIN candidate AS can ON can.candidate_id = cjsh.candidate_id
										JOIN candidate_joborder AS cj ON cj.candidate_id = cjsh.candidate_id AND cj.joborder_id = cjsh.joborder_id
										JOIN joborder AS job ON job.joborder_id = cjsh.joborder_id
										JOIN company AS comp ON comp.company_id = job.company_id
									WHERE
										(cjsh.status_to = '800' OR cjsh.status_to = '620') 
									AND
										date_format(cjsh.date, '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX'
									GROUP BY cjsh.candidate_id, job.joborder_id";
									$mainRESULT = mysqli_query($catsConn, $mainQUERY);
									if(mysqli_num_rows($mainRESULT) > 0){
										while($mainROW = mysqli_fetch_array($mainRESULT)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=<?php echo $mainROW['can_id']; ?>" target="_blank"><?php echo ucwords($mainROW['can_name']); ?></a></td>
							<?php if($mainROW['can_status'] == '800'){ ?>
								<td style="text-align: center;vertical-align: middle;">Placement</td>
							<?php }else{ ?>
								<td style="text-align: center;vertical-align: middle;color: #fc2828;">Extension</td>
							<?php } ?>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['placement_date']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID=<?php echo $mainROW['comp_id']; ?>" target="_blank"><?php echo $mainROW['comp_name']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['client_manager']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=<?php echo $mainROW['job_id']; ?>" target="_blank"><?php echo $mainROW['job_id']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=<?php echo $mainROW['job_id']; ?>" target="_blank"><?php echo $mainROW['job_title']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['recruiter']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['cs_manager']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['inside_sales1']); if($mainROW['inside_sales2'] != ''){ echo ", ".ucwords($mainROW['inside_sales2']); } ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['inside_post_sales']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['onsite_sales']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['onsite_post_sales']); ?></td>	
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
