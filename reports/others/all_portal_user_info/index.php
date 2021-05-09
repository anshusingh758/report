<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "85";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>All Portal User Info</title>

	<?php
		include_once('../../../cdn.php');
	?>

	<style>
		table#customized-datatable thead th,
		table#customized-datatable tbody td{
			padding: 5px 0px;
			text-align: center;
			vertical-align: middle;
			font-size: 12px;
		}
		table#customized-datatable thead tr {
			background-color: #ccc;
			color: #000;
		}

		.report-title {
			font-size: 30px;
			background-color: #aaa;
			color: #111;
			padding: 10px;
		}
		#LoadingImage {
			margin-top: 10px;
		}
		#LoadingImage img {
			display: block;
			margin: 0 auto;
			width: 250px;
		}
		#navigation-buttons-row {
			margin-top: 30px;
		}
		#back-to-home-button {
			background-color: #2266AA;
			border-radius: 0px;
			border: #2266AA;
			color: #fff;
			outline: none;
		}
		#logout-link,
		#logout-link:focus{
			background-color: #2266AA;
			color: #fff;
			outline: none;
			border-color: #2266AA;
			border-radius: 0px;
		}
		.selected-portal-link {
			background-color: #2266AA;
			border-radius: 0px;
			border: #2266AA;
			color: #fff;outline: none;
			text-align: center;
			text-decoration: none;
		}
		.not-selected-portal-link {
			background-color: #449D44;
			border-radius: 0px;
			border: #449D44;
			color: #fff;outline: none;
			text-align: center;
			text-decoration: none;
		}
	</style>

	<script>
		$(document).ready(function(){
			$("#LoadingImage").hide();
			$("#navigation-buttons-row, #main-section").removeClass("hidden");

			var customizedDatatable = $("#customized-datatable").DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			    dom: "Bfrtip",
		        buttons:[
		            "pageLength"
		        ],
		        initComplete: function(){
		        	$("div.dataTables_filter input").css("width","250")
				}
			});
			customizedDatatable.button(0).nodes().css("background", "#449D44");
			customizedDatatable.button(0).nodes().css("border", "#449D44");
			customizedDatatable.button(0).nodes().css("color", "#fff");
		});
	</script>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center report-title">All Portal User Info</div>
				<div class="col-md-12" id="LoadingImage">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif">
				</div>
			</div>
			<div id="navigation-buttons-row" class="row hidden">
				<div class="col-md-2 pull-left">
					<button id="back-to-home-button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control"><i class="fa fa-arrow-left"></i> Back to home</button>
				</div>
				<div class="col-md-1 col-md-offset-2">
					<a class="btn form-control <?php echo isset($_REQUEST['portal']) ? ($_REQUEST['portal'] == 'hrmin' ? 'selected-portal-link' : 'not-selected-portal-link') : 'selected-portal-link' ;?>" style="color: #fff;" href="?portal=hrmin">HRM IN</a>
				</div>
				<div class="col-md-1">
					<a class="btn form-control <?php echo $_REQUEST['portal'] == 'sales' ? 'selected-portal-link' : 'not-selected-portal-link'; ?>" style="color: #fff;" href="?portal=sales">Sales</a>
				</div>
				<div class="col-md-1">
					<a class="btn form-control <?php echo $_REQUEST['portal'] == 'ea' ? 'selected-portal-link' : 'not-selected-portal-link'; ?>" style="color: #fff;" href="?portal=ea">EA</a>
				</div>
				<div class="col-md-1">
					<a class="btn form-control <?php echo $_REQUEST['portal'] == 'cats' ? 'selected-portal-link' : 'not-selected-portal-link'; ?>" style="color: #fff;" href="?portal=cats">CATS</a>
				</div>
				<div class="col-md-2 pull-right">
					<a href="../../../logout.php" id="logout-link" class="btn pull-right"><i class="fa fa-fw fa-power-off"></i> Logout</a>
				</div>
			</div>
		</div>
	</section>

	<section id="main-section" class="hidden">
		<div class="container-fluid">
			<div class="row" style="margin-top: 50px;margin-bottom: 50px;">
				<div class="col-md-12">
					<table id="customized-datatable" class="table table-striped table-bordered">
						<thead>
						<?php if (!isset($_REQUEST["portal"]) || $_REQUEST["portal"] == "hrmin" || $_REQUEST["portal"] == "") { ?>
							<tr>
								<th>User ID</th>
								<th>User Full Name</th>
								<th>Email Address</th>
								<th>Extension Number</th>
								<th>Office Number</th>
								<th>Office Number Source</th>
								<th>Is Active?</th>
							</tr>
						<?php } elseif ($_REQUEST["portal"] == "sales") { ?>
							<tr>
								<th>User ID</th>
								<th>User Full Name</th>
								<th>Email Address</th>
								<th>Is Active?</th>
							</tr>
						<?php } elseif ($_REQUEST["portal"] == "ea") { ?>
							<tr>
								<th>User ID</th>
								<th>User Full Name</th>
								<th>Email Address</th>
								<th>Is Active?</th>
							</tr>
						<?php } elseif ($_REQUEST["portal"] == "cats") { ?>
							<tr>
								<th>User ID</th>
								<th>User Full Name</th>
								<th>Email Address</th>
								<th>Is Active?</th>
							</tr>
						<?php } ?>
						</thead>
						<tbody>
						<?php
							if (!isset($_REQUEST["portal"]) || $_REQUEST["portal"] == "hrmin" || $_REQUEST["portal"] == "") {
								$selectQuery = mysqli_query($allConn, "SELECT
									mu.id AS user_id,
									mu.userfullname AS user_full_name,
									mu.emailaddress AS email_address,
									me.extension_number,
									me.office_number,
									me.office_number_source,
									IF(mu.isactive = 1, 'Yes', 'No') AS is_active
								FROM
									vtechhrm_in.main_users AS mu
									LEFT JOIN vtechhrm_in.main_employees AS me ON me.user_id = mu.id
								GROUP BY mu.id");

								if (mysqli_num_rows($selectQuery) > 0) {
									while ($selectRow = mysqli_fetch_array($selectQuery)) {
						?>
							<tr style="<?php echo $selectRow['is_active'] != 'Yes' ? 'color : red;' : ''; ?>">
								<td><?php echo $selectRow["user_id"]; ?></td>
								<td><?php echo $selectRow["user_full_name"]; ?></td>
								<td><?php echo $selectRow["email_address"]; ?></td>
								<td><?php echo $selectRow["extension_number"]; ?></td>
								<td><?php echo $selectRow["office_number"]; ?></td>
								<td><?php echo $selectRow["office_number_source"]; ?></td>
								<td><?php echo $selectRow["is_active"]; ?></td>
							</tr>
						<?php
									}
								}
						?>
						<?php
							} elseif ($_REQUEST["portal"] == "sales") {
								$selectQuery = mysqli_query($allConn, "SELECT
									xu.id AS user_id,
									CONCAT(xu.firstName,' ',xu.lastName) AS user_full_name,
									xu.emailAddress AS email_address,
									IF(xu.status = 1, 'Yes', 'No') AS is_active
								FROM
									vtechcrm.x2_users AS xu
								GROUP BY xu.id");

								if (mysqli_num_rows($selectQuery) > 0) {
									while ($selectRow = mysqli_fetch_array($selectQuery)) {
						?>
							<tr style="<?php echo $selectRow['is_active'] != 'Yes' ? 'color : red;' : ''; ?>">
								<td><?php echo $selectRow["user_id"]; ?></td>
								<td><?php echo $selectRow["user_full_name"]; ?></td>
								<td><?php echo $selectRow["email_address"]; ?></td>
								<td><?php echo $selectRow["is_active"]; ?></td>
							</tr>
						<?php
									}
								}	
						?>
						<?php
							} elseif ($_REQUEST["portal"] == "ea") {
								$selectQuery = mysqli_query($allConn, "SELECT
									xu.id AS user_id,
									CONCAT(xu.firstName,' ',xu.lastName) AS user_full_name,
									xu.emailAddress AS email_address,
									IF(xu.status = 1, 'Yes', 'No') AS is_active
								FROM
									vtechea.x2_users AS xu
								GROUP BY xu.id");

								if (mysqli_num_rows($selectQuery) > 0) {
									while ($selectRow = mysqli_fetch_array($selectQuery)) {
						?>
							<tr style="<?php echo $selectRow['is_active'] != 'Yes' ? 'color : red;' : ''; ?>">
								<td><?php echo $selectRow["user_id"]; ?></td>
								<td><?php echo $selectRow["user_full_name"]; ?></td>
								<td><?php echo $selectRow["email_address"]; ?></td>
								<td><?php echo $selectRow["is_active"]; ?></td>
							</tr>
						<?php
									}
								}
						?>
						<?php
							} elseif ($_REQUEST["portal"] == "cats") {
								$selectQuery = mysqli_query($allConn, "SELECT
									u.user_id,
									CONCAT(u.first_name,' ',u.last_name) AS user_full_name,
									u.email AS email_address,
									IF(u.access_level != 0, 'Yes', 'No') AS is_active
								FROM
									cats.user AS u
								GROUP BY u.user_id");

								if (mysqli_num_rows($selectQuery) > 0) {
									while ($selectRow = mysqli_fetch_array($selectQuery)) {
						?>
							<tr style="<?php echo $selectRow['is_active'] != 'Yes' ? 'color : red;' : ''; ?>">
								<td><?php echo $selectRow["user_id"]; ?></td>
								<td><?php echo $selectRow["user_full_name"]; ?></td>
								<td><?php echo $selectRow["email_address"]; ?></td>
								<td><?php echo $selectRow["is_active"]; ?></td>
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
