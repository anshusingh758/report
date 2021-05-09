<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "35";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Current Active Candidates</title>

	<?php
		include_once('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th,
		table.dataTable tbody td{
			padding: 5px 0px;
		}
		.btnx,
		.btnx:focus{
			background-color: #2266AA;
			color: #fff;
			outline: none;
			border-color: #2266AA;
			border-radius: 0px;
		}
	</style>

	<script>
		$(document).ready(function(){
			$("#LoadingImage").hide();
			$('#MainSection').removeClass("hidden");


			var tableX = $('#CACLdata').DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
		});
	</script>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Current Active Candidates</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
			<div class="row" style="margin-top: 30px;">
				<div class="col-md-2 pull-left">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control" style="background-color: #2266AA;border-radius: 0px;border: #2266AA;color: #fff;outline: none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to home</button>
				</div>
				<?php
					if ($_REQUEST['data']=='Terminated') {
				?>
					<div class="col-md-2 col-md-offset-3">
						<a class="form-control btn" href="?data=<?php echo 'Active'; ?>" style="background-color: #449D44;border-radius: 0px;border: #449D44;color: #fff;outline: none;text-align: center;">View Active</a>
					</div>
				<?php } else { ?>
					<div class="col-md-2 col-md-offset-3">
						<a class="form-control btn" href="?data=<?php echo 'Terminated'; ?>" style="background-color: #449D44;border-radius: 0px;border: #449D44;color: #fff;outline: none;text-align: center;">View Terminated</a>
					</div>
				<?php } ?>
				<div class="col-md-2 pull-right">
					<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
				</div>
			</div>
		</div>
	</section>

	<section id="MainSection" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-top: 50px;margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="CACLdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #ccc;color: #000;font-size: 11px;">
								<th style="text-align: center;vertical-align: middle;">Name</th>
								<th style="text-align: center;vertical-align: middle;">Job Title</th>
								<th style="text-align: center;vertical-align: middle;">Client</th>
								<th style="text-align: center;vertical-align: middle;">Client Manager</th>
								<th style="text-align: center;vertical-align: middle;">Recruiter</th>
								<th style="text-align: center;vertical-align: middle;">Recruiter Manager</th>
								<th style="text-align: center;vertical-align: middle;">Inside Sales Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Inside PS Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Onsite Sales Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Onsite PS Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Join Date</th>
								<th style="text-align: center;vertical-align: middle;">Termination Date</th>
								<th style="text-align: center;vertical-align: middle;">Employment Type</th>
								<th style="text-align: center;vertical-align: middle;">Benefit</th>
								<th style="text-align: center;vertical-align: middle;">Benefit List</th>
								<th style="text-align: center;vertical-align: middle;">Bill Rate</th>
								<th style="text-align: center;vertical-align: middle;">Pay Rate</th>
								<th style="text-align: center;vertical-align: middle;">OT Bill Rate</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$mainQUERY = "SELECT
								    CONCAT(e.first_name,' ',e.last_name) AS ename,
								    e.status,
								    (SELECT jt.name FROM jobtitles AS jt JOIN employees AS emp ON emp.job_title = jt.id WHERE emp.id = e.id) AS job_title,
								    DATE_FORMAT(e.custom7,'%m-%d-%Y') AS join_date,
								    DATE_FORMAT(e.termination_date,'%m-%d-%Y') AS termi_date,
									e.custom1 AS benefit,
									e.custom2 AS benefitlist,
									CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS billrate,
									CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS payrate,
								    e.city,
								    e.work_email,
								    e.overtime_billrate,
									es.name AS employment_type,
								    comp.name AS cname,
								    CONCAT(cmn.first_name,' ',cmn.last_name) AS client_manager_name,
								    CONCAT(rec.first_name,' ',rec.last_name) AS recruiter_name,
								    rec.notes AS recruiter_manager_name,
								    isp1.value AS inside_sales1,
								    isp2.value AS inside_sales2,
								    ips.value AS inside_post_sales,
								    os.value AS onsite_sales,
								    ops.value AS onsite_post_sales
								FROM
									vtechhrm.employees AS e
									LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
								    LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
								    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
								    LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
								    LEFT JOIN cats.user AS cmn ON cmn.user_id = comp.owner
								    LEFT JOIN cats.user AS rec ON rec.user_id = si.c_recruiter_id
								    LEFT JOIN cats.extra_field AS isp1 ON isp1.data_item_id = comp.company_id AND isp1.field_name = 'Inside Sales Person1'
								    LEFT JOIN cats.extra_field AS isp2 ON isp2.data_item_id = comp.company_id AND isp2.field_name = 'Inside Sales Person2'
								    LEFT JOIN cats.extra_field AS ips ON ips.data_item_id = comp.company_id AND ips.field_name = 'Inside Post Sales'
								    LEFT JOIN cats.extra_field AS os ON os.data_item_id = comp.company_id AND os.field_name = 'OnSite Sales Person'
								    LEFT JOIN cats.extra_field AS ops ON ops.data_item_id = comp.company_id AND ops.field_name = 'OnSite Post Sales'
								WHERE
									comp.company_id != '2'";
								
								if ($_REQUEST['data'] == 'Active') {
									$mainQUERY .= " AND
										e.status = 'Active'
									GROUP BY e.id";
								} elseif ($_REQUEST['data'] == 'Terminated') {
									$mainQUERY .= " AND
									(e.status='Terminated' OR e.status='Termination In_Vol' OR e.status='Termination Vol')
									GROUP BY e.id";
								} else {
									$mainQUERY .= " AND
										e.status = 'Active'
									GROUP BY e.id";
								}

								$mainRESULT = mysqli_query($vtechhrmConn, $mainQUERY);
								while ($mainROW = mysqli_fetch_array($mainRESULT)) {
									$delimiter = array("","[","]",'"');
									$replace = str_replace($delimiter, $delimiter[0], $mainROW['benefitlist']);
									$benefitlist = $replace;
							?>
							<tr style="font-size: 12px;">
								<td style="text-align: left;vertical-align: middle;"><?php echo ucwords($mainROW['ename']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['job_title']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['cname']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['client_manager_name']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['recruiter_name']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['recruiter_manager_name']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['inside_sales1']); if($mainROW['inside_sales2'] != ''){ echo ", ".ucwords($mainROW['inside_sales2']); } ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['inside_post_sales']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['onsite_sales']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['onsite_post_sales']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['join_date']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
								<?php
									if ($mainROW['status'] == 'Active') {
										echo "---";
									} else {
										echo $mainROW['termi_date'];
									}
								?>
								</td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['employment_type']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['benefit']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $benefitlist; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['billrate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['payrate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['overtime_billrate']; ?></td>
							</tr>
							<?php
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>

</body>
</html>
<?php
		} else {
			if ($userMember == "Admin") {
				header("Location:../../../admin.php");
			} elseif ($userMember == "User") {
				header("Location:../../../user.php");
			} else {
				header("Location:../../../index.php");
			}
		}
    } else {
        header("Location:../../../index.php");
    }
?>
