<?php
	include("../../../security.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='41';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Incentive Criteria</title>
	
	<?php
		include('../../../cdn.php');
	?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th,
		table.dataTable tbody td{
			padding: 5px 1px;
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
		}
	</style>
		
	<script>
		$(document).ready(function(){
			$("#LoadingImage").hide();
			$('#MainSection').removeClass("hidden");
			$('#ICdatatable').removeClass("hidden");
			
			/*Datatable Calling START*/
			$('#ICdata').DataTable({
				"paging": false,
				"ordering":false,
				"searching":false
			});
			/*Datatable Calling END*/
        });
    </script>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Incentive Criteria</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section id="MainSection" class="hidden" style="margin-top: 30px;margin-bottom: 70px;">
		<div class="container">
			<form action="index.php" method="post">
				<div class="row">
					<div class="col-md-2">
						<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control" style="background-color: #2266AA;border-radius: 0px;border: #2266AA;color: #fff;outline: none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Home</button>
					</div>
					<div class="col-md-3 col-md-offset-2">
						<select class="form-control" name="personneldetail" style="border: 1px solid #aaa;border-radius: 0px;cursor: pointer;" required>
							<option value="">Select Personnel</option>
							<?php
								$query="SELECT personnel FROM incentive_criteria GROUP BY personnel ORDER BY id";
								$result=mysqli_query($misReportsConn,$query);
								while($row=mysqli_fetch_array($result)){
									if($_POST['personneldetail']==$row['personnel']){
										$isSelected = ' selected';
									}else{
										$isSelected = '';
									}
									echo "<option value='".$row['personnel']."'".$isSelected.">".$row['personnel']."</option>";
								}
							?>
						</select>
					</div>
					<div class="col-md-1">
						<button type="submit" name="searchcriteria" class="form-control" style="background-color: #673AB7;border-radius: 0px;border: #673AB7;color: #fff;outline: none;"><i class="fa fa-search"></i></button>
					</div>
					<div class="col-md-1">
						<button type="submit" name="addcriteria" class="form-control" style="background-color: #449D44;border-radius: 0px;border: #449D44;color: #fff;outline: none;"><i class="fa fa-plus"></i></button>
					</div>
					<div class="col-md-3">
						<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	//Search Criteria Section
	if(isset($_POST['searchcriteria'])){
		$pdata=$_POST['personneldetail'];
?>

	<section id="ICdatatable" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-bottom: 50px;">
				<div class="col-md-6 col-md-offset-3">
					<table id="ICdata" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #2266AA;color: #fff;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;">MIN Margin</th>
								<th style="text-align: center;vertical-align: middle;">MAX Margin</th>
								<th style="text-align: center;vertical-align: middle;">Value</th>
								<th style="text-align: center;vertical-align: middle;">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$query1="SELECT * FROM incentive_criteria WHERE personnel='$pdata'";
								$result1=mysqli_query($misReportsConn, $query1);
								while($row1=mysqli_fetch_array($result1)){
							?>
							<tr>
								<td style="text-align: center;vertical-align: middle;"><?php echo $row1['min_margin']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $row1['max_margin']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $row1['value']; ?></td>
								<td style="text-align: center;vertical-align: middle;">
									<a href="" style="font-size: 18px;text-decoration: none;outline: none;color: #449D44;"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<a href="" style="font-size: 18px;text-decoration: none;outline: none;color: #ff2277;"><i class="fa fa-trash"></i></a>
								</td>
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
<?php
	}

	//Add New Criteria Section
	if(isset($_POST['addcriteria'])){
		$pdata=$_POST['personneldetail'];
?>

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
