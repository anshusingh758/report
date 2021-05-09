<?php
	include_once("../../../security.php");
	header("Content-Type: text/html; charset=ISO-8859-1");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include_once('../../../config.php');
		
    	$childUser = $_SESSION['userMember'];
		$reportID = '82';
		$sessionQuery = "SELECT * FROM mapping JOIN users ON users.uid = mapping.uid JOIN reports ON reports.rid = mapping.rid WHERE mapping.uid = '$user' AND mapping.rid = '$reportID' AND users.ustatus = '1' AND reports.rstatus = '1'";
		$sessionResult = mysqli_query($misReportsConn, $sessionQuery);
		$sessionROW = mysqli_fetch_array($sessionResult);
		if(mysqli_num_rows($sessionResult) > 0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Sales Contact Info Report</title>

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
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Sales Contact Info Report</div>
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
			<form id="mulmonth" action="index.php" method="post">
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
			<form id="daterange" class="hidden" action="index.php" method="post">
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
								<th style="text-align: center;vertical-align: middle;">Name</th>
								<th style="text-align: center;vertical-align: middle;">Title</th>
								<th style="text-align: center;vertical-align: middle;">Company</th>
								<th style="text-align: center;vertical-align: middle;">Phone</th>
								<th style="text-align: center;vertical-align: middle;">Phone2</th>
								<th style="text-align: center;vertical-align: middle;">Email</th>
								<th style="text-align: center;vertical-align: middle;">Website</th>
								<th style="text-align: center;vertical-align: middle;">Address</th>
								<th style="text-align: center;vertical-align: middle;">Address2</th>
								<th style="text-align: center;vertical-align: middle;">City</th>
								<th style="text-align: center;vertical-align: middle;">State</th>
								<th style="text-align: center;vertical-align: middle;">Country</th>
								<th style="text-align: center;vertical-align: middle;">Zipcode</th>
								<th style="text-align: center;vertical-align: middle;">LinkedIn</th>
								<th style="text-align: center;vertical-align: middle;">Lead Source</th>
								<th style="text-align: center;vertical-align: middle;">Lead Date</th>
								<th style="text-align: center;vertical-align: middle;">Lead Type</th>
								<th style="text-align: center;vertical-align: middle;">Do Not Call</th>
								<th style="text-align: center;vertical-align: middle;">Do Not Email</th>
								<th style="text-align: center;vertical-align: middle;">Created Date</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach($fromDate AS $key => $fromDate2){
									$fromDateX = $fromDate[$key];
									$toDateX = $toDate[$key];

									$mainQUERY = "SELECT
										xc.id,
										xc.name,
										xc.title,
										xc.company,
										xc.phone,
										xc.phone2,
										xc.email,
										xc.website,
										xc.address,
										xc.address2,
										xc.city,
										xc.state,
										xc.zipcode,
										xc.country,
										xc.linkedin,
										xc.leadSource,
										DATE_FORMAT(FROM_UNIXTIME(xc.leadDate), '%m-%d-%Y') AS leadDate,
										xc.leadtype,
										IF(xc.doNotCall = 1, 'Yes', 'No') AS doNotCall,
										IF(xc.doNotEmail = 1, 'Yes', 'No') AS doNotEmail,
										DATE_FORMAT(FROM_UNIXTIME(xc.createDate), '%m-%d-%Y') AS createdDate
									FROM
										vtechcrm.x2_contacts AS xc
									WHERE
										DATE_FORMAT(FROM_UNIXTIME(xc.createDate), '%Y-%m-%d') BETWEEN '$fromDateX' AND '$toDateX'
									GROUP BY xc.id";

									$mainRESULT = mysqli_query($catsConn, $mainQUERY);
									if(mysqli_num_rows($mainRESULT) > 0){
										while($mainROW = mysqli_fetch_array($mainRESULT)){
							?>
							<tr style="font-size: 13px;">
								<td style="text-align: left;vertical-align: middle;"><a href="https://sales.vtechsolution.com/index.php/contacts/id/<?php echo $mainROW['id']; ?>" target="_blank"><?php echo $mainROW['name']; ?></a></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['title']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['company']; ?></td>
								<td nowrap style="text-align: center;vertical-align: middle;"><?php echo $mainROW['phone']; ?></td>
								<td nowrap style="text-align: center;vertical-align: middle;"><?php echo $mainROW['phone2']; ?></td>
								<td nowrap style="text-align: center;vertical-align: middle;"><?php echo $mainROW['email']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['website']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['address']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['address2']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['city']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['state']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['country']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['zipcode']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['linkedin']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['leadSource']; ?></td>
								<td nowrap style="text-align: center;vertical-align: middle;"><?php echo $mainROW['leadDate']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['leadtype']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['doNotCall']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['doNotEmail']; ?></td>
								<td style="text-align: center;vertical-align: middle;"><?php echo $mainROW['createdDate']; ?></td>
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
