<?php
	include("../../../security.php");
	include("../../../functions/reporting-service.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='59';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage EA Roles</title>
	
	<?php
		include('../../../cdn.php');
	?>

	<!--Custom CSS-->
	<style>
		table.dataTable thead th{
			padding: 5px;
		}
		table.dataTable tbody td,
		table.dataTable tfoot th{
			padding: 3px;
		}
		.btnx,
		.btnx:focus{
			background-color: #2266AA;
			color: #fff;
			outline: none;
			border-color: #2266AA;
			border-radius: 0px;
		}
		.viewButton {
			background-color: #2266AA;
			border-radius: 0px;
			border: 1px solid #2266AA;
			color: #fff;
			font-weight: bold;
		}
		.hiddenButton {
			background-color: #fff;
			border-radius: 0px;
			border: 1px solid #2266AA;
			color: #2266AA;
			font-weight: bold;
		}
	</style>

	<script>
		$(document).ready(function(){
			$(".LoadingImage").hide();
			$('.MainSection').removeClass("hidden");

			/*Select All checkboxes START*/
			$('#select_all').on('click',function(){
		        if(this.checked){
		            $('.checkboxes').each(function(){
		                this.checked = true;
		            });
		        }else{
		             $('.checkboxes').each(function(){
		                this.checked = false;
		            });
		        }
		    });

			$('.checkboxes').on('click',function(){
				if($('.checkboxes:checked').length == $('.checkboxes').length){
					$('#select_all').prop('checked',true);
				}else{
					$('#select_all').prop('checked',false);
				}
			});
			/*Select All checkboxes END*/
	 
	        //customizedPersonnelSelectBox
	        $('.customizedPersonnelSelectBox').multiselect({
	            nonSelectedText: 'Select EA Personnel',
	            numberDisplayed: 1,
	            enableFiltering:true,
	            enableCaseInsensitiveFiltering:true,
	            buttonWidth:'100%',
	            includeSelectAllOption: true,
	            maxHeight: 200
	        });

	        //Datatable Calling START
	        var customizedDataTableWithPaging = $('#customizedDataTableWithPaging').DataTable({
			    dom: 'Bfrtip',
			    "columnDefs":[{
		            "targets" : 'no-sort',
		            "orderable": false,
		        }],
			    "aaSorting": [[1,'asc']],
		        buttons:[
		            'pageLength'
		        ],
		        "scrollY": "300px",
		        "scrollCollapse": true,
		        "paging": false,
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","200")
				}
			});
			customizedDataTableWithPaging.button(0).remove();
		});
	</script>

</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center" style="font-size: 30px;background-color: #aaa;color: #111;padding: 10px;">Manage EA Roles</div>
				<div class="LoadingImage col-md-12" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section class="MainSection hidden" style="margin-top: 20px;margin-bottom: 10px;">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control" style="background-color: #2266AA;border-radius: 0px;border: #2266AA;color: #fff;outline: none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Report</button>
				</div>
				<div class="col-md-3 col-md-offset-3">
				<?php if (!isset($_REQUEST['type']) || $_REQUEST['type'] == 'hrmemployees' || $_REQUEST['type'] != 'catsclients' || $_REQUEST['type'] == '') { ?>
					<a href="?type=catsclients" class="btn form-control hiddenButton" style="outline: none;text-decoration: none;color: #2266AA;"><i class="fa fa-eye" aria-hidden="true"></i> View CATS Clients</a>
				<?php } elseif ($_REQUEST['type'] == 'catsclients') { ?>
					<a href="?type=hrmemployees" class="btn form-control hiddenButton" style="outline: none;text-decoration: none;color: #2266AA;"><i class="fa fa-eye" aria-hidden="true"></i> View HRM Employees</a>
				<?php } ?>
				</div>
				<div class="col-md-2 col-md-offset-2">
					<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
				</div>
			</div>
			<?php if (!isset($_REQUEST['type']) || $_REQUEST['type'] == 'hrmemployees' || $_REQUEST['type'] != 'catsclients' ||  $_REQUEST['type'] == '') { ?>
			<div class="row" style="margin-top: 40px;">
				<div class="col-md-3 col-md-offset-1">
					<select class="customizedPersonnelSelectBox eaPersonnelNames" name="eaPersonnelGroup[]">
						<option value="">Select EA Personnel</option>
						<?php
							print_r(eaPersonnelList($catsConn));
						?>
					</select>
				</div>
				<div class="col-md-1">
					<button class="btn btn-success" onclick="saveEARoles('employee')">Change</button>
				</div>
			</div>
			<div class="row">
				<div class="col-md-10 col-md-offset-1">
					<table id="customizedDataTableWithPaging" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th class='no-sort' style="text-align: center;vertical-align: middle;" rowspan="2"><input style="height:20px;width:20px;cursor: pointer;outline: none;" type='checkbox' name='select_all' id='select_all'></th>
								<th style="text-align: center;vertical-align: middle;" colspan="2">HRM</th>
								<th style="text-align: center;vertical-align: middle;" colspan="2">CATS</th>
							</tr>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">Employee</th>
								<th style="text-align: center;vertical-align: middle;">EA Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Company</th>
								<th style="text-align: center;vertical-align: middle;">EA Personnel</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$mainQRY = mysqli_query($vtechhrmConn, "SELECT
								    e.id AS empId,
								    concat(e.first_name,' ',e.last_name) AS empName,
								    job.name AS jobTitle,
								    comp.company_id AS cid,
								    comp.name AS cname,
								    (SELECT mer.mapping_value FROM vtech_mappingdb.manage_ea_roles AS mer WHERE mer.reference_type = 'Employee' AND mer.reference_id = e.id ORDER BY mer.id DESC LIMIT 1) AS employeeEAPersonnel,
								    (SELECT mer.mapping_value FROM vtech_mappingdb.manage_ea_roles AS mer WHERE mer.reference_type = 'Company' AND mer.reference_id = comp.company_id ORDER BY mer.id DESC LIMIT 1) AS clientEAPersonnel
								FROM
								    employees AS e
								    JOIN employmentstatus AS es ON e.employment_status = es.id
								    JOIN jobtitles AS job ON e.job_title = job.id
								    JOIN vtech_mappingdb.system_integration AS mp ON e.id = mp.h_employee_id
								    JOIN cats.company AS comp ON comp.company_id = mp.c_company_id
								WHERE
								    e.status = 'Active'
								AND
									comp.company_id != '2'
								GROUP BY empName");
								while ($mainROW = mysqli_fetch_array($mainQRY)) {
							?>
							<tr style="font-size: 14px;">
								<td style="text-align: center;vertical-align: middle;"><input type="checkbox" class="checkboxes" name="checked_id[<?php echo $mainROW['empId']; ?>]"style="height:18px;width:18px;cursor: pointer;outline: none;" value="<?php echo $mainROW['empId']; ?>"></td>
								<td style="vertical-align: middle;"><?php echo ucwords($mainROW['empName']); ?></td>
								<td style="vertical-align: middle;">
									<div id="mapTd<?php echo $mainROW['empId']; ?>">
										<?php echo ucwords($mainROW['employeeEAPersonnel']); ?>
									</div>
								</td>
								<td style="vertical-align: middle;"><?php echo ucwords($mainROW['cname']); ?></td>
								<td style="vertical-align: middle;"><?php echo ucwords($mainROW['clientEAPersonnel']); ?></td>
							</tr>
							<?php
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<?php } elseif ($_REQUEST['type'] == 'catsclients') { ?>
			<div class="row" style="margin-top: 40px;">
				<div class="col-md-3 col-md-offset-2">
					<select class="customizedPersonnelSelectBox eaPersonnelNames" name="eaPersonnelGroup[]" multiple>
						<?php
							print_r(eaPersonnelList($catsConn));
						?>
					</select>
				</div>
				<div class="col-md-1">
					<button class="btn btn-success" onclick="saveEARoles('company')">Change</button>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<table id="customizedDataTableWithPaging" class="table table-striped table-bordered">
						<thead>
							<tr style="background-color: #bbb;color: #000;font-size: 13px;">
								<th class='no-sort' style="text-align: center;vertical-align: middle;"><input style="height:20px;width:20px;cursor: pointer;outline: none;" type='checkbox' name='select_all' id='select_all'></th>
								<th style="text-align: center;vertical-align: middle;">CATS Company</th>
								<th style="text-align: center;vertical-align: middle;">EA Personnel</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$clientQUERY = mysqli_query($catsConn, "SELECT
								    comp.company_id,
								    comp.name,
								    (SELECT mer.mapping_value FROM vtech_mappingdb.manage_ea_roles AS mer WHERE mer.reference_type = 'Company' AND mer.reference_id = comp.company_id ORDER BY mer.id DESC LIMIT 1) AS eaPersonnel
								FROM
									company AS comp
								ORDER BY comp.name ASC");
								while ($clientROW = mysqli_fetch_array($clientQUERY)) {
							?>
							<tr style="font-size: 14px;">
								<td style="text-align: center;vertical-align: middle;"><input type="checkbox" class="checkboxes" name="checked_id[<?php echo $clientROW['company_id']; ?>]"style="height:18px;width:18px;cursor: pointer;outline: none;" value="<?php echo $clientROW['company_id']; ?>"></td>
								<td style="vertical-align: middle;"><?php echo ucwords($clientROW['name']); ?></td>
								<td style="vertical-align: middle;">
									<div id="mapTd<?php echo $clientROW['company_id']; ?>">
										<?php echo ucwords($clientROW['eaPersonnel']); ?>
									</div>
								</td>
							</tr>
							<?php
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<?php } ?>
		</div>
	</section>

	<script>
		function saveEARoles(mapType) {
			if ($('.checkboxes:checked').length == 0) {
				alert("Please select atleast one "+mapType+"!");
			} else {
				if (mapType == 'employee') {
					var eaListGroup = $('.eaPersonnelNames').val();
				} else {
					var eaListGroup = $('.eaPersonnelNames').serialize();
				}
				if (eaListGroup == '') {
					alert('Please select EA Personnel!');
				} else {
					var mapList = $('.checkboxes:checked').serialize();
					var eaList = $('.eaPersonnelNames').serialize();
					$.ajax({
						url: '<?php echo REPORT_PATH; ?>/others/manage_ea_roles/save-ea-roles.php',
						type: 'POST',
						data: "mapType="+mapType+"&"+mapList+"&"+eaList,
						success: function(response) {
							console.log(response);
							if (response != '') {
								$.each($('.checkboxes:checked'), function() {
									$("#mapTd"+this.value).html("");
									$("#mapTd"+this.value).html(response);
								});
								$('#select_all').prop("checked", false);
								$('.checkboxes').prop("checked", false);
								$.ajax({
									url: '<?php echo REPORT_PATH; ?>/others/manage_ea_roles/reload-selectbox.php',
									type: 'POST',
									data: "mapType="+mapType,
									success: function(output) {
										$('.customizedPersonnelSelectBox').html(output);
		                    			$('.customizedPersonnelSelectBox').multiselect("destroy");
										$('.customizedPersonnelSelectBox').multiselect({
								            nonSelectedText: 'Select EA Personnel',
								            numberDisplayed: 1,
								            enableFiltering:true,
								            enableCaseInsensitiveFiltering:true,
								            buttonWidth:'100%',
								            includeSelectAllOption: true,
								            maxHeight: 200
								        });
									}
								});
			            		alert('EA Personnel Successfully Updated!');
							} else {
			            		alert('Something went wrong!');
							};
			            }
					});
				}
			}
		}
	</script>

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
