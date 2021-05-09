<?php
	include_once("../../../security.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include_once('../../../config.php');

    	$childUser = $_SESSION['userMember'];
		$reportID = '25';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
		$sessionResult = mysqli_query($misReportsConn, $sessionQuery);
		if(mysqli_num_rows($sessionResult) > 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Recruiter Assign Jobs Report</title>

	<?php
		include_once('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th,
		table.dataTable tbody td{
			padding: 5px;
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


			var tableX = $('#CPRdata').DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			    dom: 'Bfrtip',
		        buttons:[
		            'excel','pageLength'
		        ],
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","150")
				}
			});
			tableX.button(0).nodes().css('background', '#2266AA');
			tableX.button(0).nodes().css('border', '#2266AA');
			tableX.button(0).nodes().css('color', '#fff');
			tableX.button(0).nodes().html('Download Report');
			tableX.button(1).nodes().css('background', '#449D44');
			tableX.button(1).nodes().css('border', '#449D44');
			tableX.button(1).nodes().css('color', '#fff');
		});
	</script>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Recruiter Assign Jobs Report</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section id="MainSection" class="hidden" style="margin-top: 20px;margin-bottom: 50px;">
		<div class="container">
			<div class="row">
				<div class="col-md-2 pull-left">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control" style="background-color: #2266AA;border-radius: 0px;border: #2266AA;color: #fff;outline: none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to home</button>
				</div>
				<div class="col-md-2 pull-right">
					<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
				</div>
			</div>
		</div>
	</section>

	<section id="CPRdatatable" class="hidden">
		<div class="container">
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-6 col-md-offset-3">
					<table id="CPRdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #ccc;color: #000;">
								<th style="text-align: center;vertical-align: middle;">User Name</th>
								<th style="text-align: center;vertical-align: middle;">No Of Job</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$mainQUERY = "SELECT
									u.user_id AS recruiterId,
								    concat(u.first_name,' ',u.last_name) AS recruiterName,
								    count(job.joborder_id) AS totalJob
								FROM
									user AS u
								    JOIN joborder AS job ON job.recruiter = u.user_id
								WHERE
									job.status = 'Active'
								AND
									u.access_level != '0'
								GROUP BY recruiterId";

								$mainRESULT = mysqli_query($catsConn, $mainQUERY);
								if(mysqli_num_rows($mainRESULT) > 0){
									while($mainROW = mysqli_fetch_array($mainRESULT)){
							?>
							<tr>
								<td style="text-align: left;vertical-align: middle;"><?php echo $mainROW['recruiterName']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['totalJob']; ?></td>
							</tr>
							<?php
									}
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
<!--SELECT
    rname AS user_name,
    COUNT(*) AS NoOfJobs
FROM
    (SELECT
        CAST(CONCAT(CONCAT(u.first_name, ' '),u.last_name) AS CHAR(100)) AS Rname
    FROM
        joborder jo,
        USER u
    WHERE
        jo.recruiter = u.user_id AND jo.status = 'active'
    UNION ALL
	SELECT
	    CAST(ef.value AS CHAR(100)) AS Rname
	FROM
	    joborder jo,
	    extra_field AS ef
	WHERE
	    jo.status = 'active' AND ef.field_name = 'Recruiter 2' AND jo.joborder_id = ef.data_item_id
	UNION ALL
	SELECT
	    CAST(ef.value AS CHAR(100)) AS Rname
	FROM
	    joborder jo,
	    extra_field AS ef
	WHERE
	    jo.status = 'active' AND ef.field_name = 'Recruiter 3' AND jo.joborder_id = ef.data_item_id
	UNION ALL
	SELECT
	    CAST(ef.value AS CHAR(100)) AS Rname
	FROM
	    joborder jo,
	    extra_field AS ef
	WHERE
	    jo.status = 'active' AND ef.field_name = 'Recruiter 4' AND jo.joborder_id = ef.data_item_id) AS tmp
GROUP BY rname -->
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
