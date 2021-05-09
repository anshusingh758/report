<?php
	include_once("../../../security.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include_once('../../../config.php');

    	$childUser = $_SESSION['userMember'];
		$reportID = '34';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
		$sessionResult = mysqli_query($misReportsConn, $sessionQuery);
		if(mysqli_num_rows($sessionResult) > 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Current Client Summary</title>

	<?php
		include_once('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th,
		table.dataTable tbody td{
			padding: 3px 1px;
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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Current Client Summary</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
			<div class="row" style="margin-top: 30px;">
				<div class="col-md-2 pull-left">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control" style="background-color: #2266AA;border-radius: 0px;border: #2266AA;color: #fff;outline: none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to home</button>
				</div>
				<?php
					if($_REQUEST['data']=='All'){
				?>
					<div class="col-md-2 col-md-offset-3">
						<a class="form-control btn" href="?data=<?php echo 'Active'; ?>" style="background-color: #449D44;border-radius: 0px;border: #449D44;color: #fff;outline: none;text-align: center;">View Active Client</a>
					</div>
				<?php }else{ ?>
					<div class="col-md-2 col-md-offset-3">
						<a class="form-control btn" href="?data=<?php echo 'All'; ?>" style="background-color: #449D44;border-radius: 0px;border: #449D44;color: #fff;outline: none;text-align: center;">View All Client</a>
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
							<tr style="background-color: #ccc;color: #000;font-size: 12px;">
								<th style="text-align: center;vertical-align: middle;">Client</th>
								<th style="text-align: center;vertical-align: middle;">Status</th>
								<th style="text-align: center;vertical-align: middle;">Client Manager</th>
								<th style="text-align: center;vertical-align: middle;">Industry Type</th>
								<th style="text-align: center;vertical-align: middle;">Inside Sales Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Inside PS Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Onsite Sales Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Onsite PS Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Address</th>
								<th style="text-align: center;vertical-align: middle;">City</th>
								<th style="text-align: center;vertical-align: middle;">State</th>
								<th style="text-align: center;vertical-align: middle;">ZIP Code</th>
								<th style="text-align: center;vertical-align: middle;">Phone1</th>
								<th style="text-align: center;vertical-align: middle;">Phone2</th>
								<th style="text-align: center;vertical-align: middle;">Total Job-Order</th>
								<th style="text-align: center;vertical-align: middle;">Total Active Job-Order</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($_REQUEST['data'] == 'Active'){
									$mainQUERY = "SELECT
										comp.company_id AS cid,
									    comp.name AS cname,
									    ef.value AS client_status,
									    (SELECT concat(first_name,' ',last_name) AS mannm FROM user WHERE user_id = comp.owner) AS client_manager,
									    (SELECT value FROM extra_field WHERE field_name = 'Industry Type' AND data_item_id = comp.company_id) AS industry_type,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Sales Person1' AND data_item_id = comp.company_id) AS inside_sales1,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Sales Person2' AND data_item_id = comp.company_id) AS inside_sales2,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Post Sales' AND data_item_id = comp.company_id) AS inside_post_sales,
									    (SELECT value FROM extra_field WHERE field_name = 'Onsite Sales Person' AND data_item_id = comp.company_id) AS onsite_sales,
									    (SELECT value FROM extra_field WHERE field_name = 'Onsite Post Sales' AND data_item_id = comp.company_id) AS onsite_post_sales,
									    comp.address,
									    comp.city,
									    comp.state,
									    comp.zip,
									    comp.phone1,
									    comp.phone2,
									    (SELECT COUNT(*) AS totjob FROM joborder WHERE company_id= comp.company_id) AS totjob,
									    (SELECT COUNT(CASE WHEN status='Active' THEN 1 END) AS totactive FROM joborder WHERE company_id = comp.company_id) AS totactive
									FROM
										company AS comp
										LEFT JOIN extra_field AS ef ON ef.data_item_id = comp.company_id AND ef.field_name = 'Status'
									WHERE
										ef.field_name = 'Status'
									AND
										ef.value='Active'
									GROUP BY comp.company_id";
								}elseif($_REQUEST['data'] == 'All'){
									$mainQUERY = "SELECT
										comp.company_id AS cid,
									    comp.name AS cname,
									    ef.value AS client_status,
									    (SELECT concat(first_name,' ',last_name) AS mannm FROM user WHERE user_id = comp.owner) AS client_manager,
									    (SELECT value FROM extra_field WHERE field_name = 'Industry Type' AND data_item_id = comp.company_id) AS industry_type,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Sales Person1' AND data_item_id = comp.company_id) AS inside_sales1,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Sales Person2' AND data_item_id = comp.company_id) AS inside_sales2,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Post Sales' AND data_item_id = comp.company_id) AS inside_post_sales,
									    (SELECT value FROM extra_field WHERE field_name = 'Onsite Sales Person' AND data_item_id = comp.company_id) AS onsite_sales,
									    (SELECT value FROM extra_field WHERE field_name = 'Onsite Post Sales' AND data_item_id = comp.company_id) AS onsite_post_sales,
									    comp.address,
									    comp.city,
									    comp.state,
									    comp.zip,
									    comp.phone1,
									    comp.phone2,
									    (SELECT COUNT(*) AS totjob FROM joborder WHERE company_id= comp.company_id) AS totjob,
									    (SELECT COUNT(CASE WHEN status='Active' THEN 1 END) AS totactive FROM joborder WHERE company_id = comp.company_id) AS totactive
									FROM
										company AS comp
										LEFT JOIN extra_field AS ef ON ef.data_item_id = comp.company_id AND ef.field_name = 'Status'
									GROUP BY comp.company_id";
								}else{
									$mainQUERY = "SELECT
										comp.company_id AS cid,
									    comp.name AS cname,
									    ef.value AS client_status,
									    (SELECT concat(first_name,' ',last_name) AS mannm FROM user WHERE user_id = comp.owner) AS client_manager,
									    (SELECT value FROM extra_field WHERE field_name = 'Industry Type' AND data_item_id = comp.company_id) AS industry_type,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Sales Person1' AND data_item_id = comp.company_id) AS inside_sales1,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Sales Person2' AND data_item_id = comp.company_id) AS inside_sales2,
									    (SELECT value FROM extra_field WHERE field_name = 'Inside Post Sales' AND data_item_id = comp.company_id) AS inside_post_sales,
									    (SELECT value FROM extra_field WHERE field_name = 'Onsite Sales Person' AND data_item_id = comp.company_id) AS onsite_sales,
									    (SELECT value FROM extra_field WHERE field_name = 'Onsite Post Sales' AND data_item_id = comp.company_id) AS onsite_post_sales,
									    comp.address,
									    comp.city,
									    comp.state,
									    comp.zip,
									    comp.phone1,
									    comp.phone2,
									    (SELECT COUNT(*) AS totjob FROM joborder WHERE company_id= comp.company_id) AS totjob,
									    (SELECT COUNT(CASE WHEN status='Active' THEN 1 END) AS totactive FROM joborder WHERE company_id = comp.company_id) AS totactive
									FROM
										company AS comp
										LEFT JOIN extra_field AS ef ON ef.data_item_id = comp.company_id AND ef.field_name = 'Status'
									WHERE
										ef.field_name = 'Status'
									AND
										ef.value='Active'
									GROUP BY comp.company_id";
								}
								$mainRESULT = mysqli_query($catsConn, $mainQUERY);
								if(mysqli_num_rows($mainRESULT) > 0){
									while($mainROW = mysqli_fetch_array($mainRESULT)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><a href="https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID=<?php echo $mainROW['cid']; ?>" target="_blank"><?php echo $mainROW['cname']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['client_status']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['client_manager']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['industry_type']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['inside_sales1']); if($mainROW['inside_sales2'] != ''){ echo ", ".$mainROW['inside_sales2']; } ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['inside_post_sales']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['onsite_sales']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo ucwords($mainROW['onsite_post_sales']); ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['address']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['city']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['state']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['zip']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['phone1']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['phone2']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['totjob']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['totactive']; ?></td>
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
