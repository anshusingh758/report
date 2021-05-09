<?php
	include("../../../security.php");
	include("../../../functions/reporting-service.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='47';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage CATS Roles</title>
	
	<?php
		include('../../../cdn.php');
	?>

	<!--Custom CSS-->
	<style>
		table.dataTable thead th{
			padding: 7px;
		}
		table.dataTable tbody td,
		table.dataTable tfoot th{
			padding: 3px;
		}
		.titletagbar{
			font-size: 30px;background-color: #aaa;color: #111;padding: 10px;
		}
		.topHeight{
			margin-top: 20px;
			margin-bottom: 10px;
		}
		.topHeight2{
			margin-bottom: 50px;
		}
		.theadbar{
			background-color: #2266AA;color: #fff;font-size: 15px;
		}
		.tbodybar{
			font-size: 14px;color: #000;
		}
		.selectAll{
			height:20px;width:20px;cursor: pointer;outline: none;
		}
		.checkboxes{
			height:18px;width:18px;cursor:pointer;outline: none;
		}
		.btnx,
		.btnx:focus{
			background-color: #2266AA;
			color: #fff;
			outline: none;
			border-color: #2266AA;
			border-radius: 0px;
		}
		.editicon{
			cursor: pointer;
			font-size: 17px;
		}
		.fontpad{
			padding-left: 0px;
		}
		.red {
			background-color: red;
		}
	</style>

	<!--customizedDatatableWithScroll-->
	<script>
		$(document).ready(function(){
			$("#LoadingImage").hide();
			$('#MainSection').removeClass("hidden");

	        //customizedDepartmentBox
	        $('.customizedDepartmentBox').multiselect({
	            nonSelectedText: 'Select Department',
	            numberDisplayed: 1,
	            enableFiltering:true,
	            enableCaseInsensitiveFiltering:true,
	            buttonWidth:'100%',
	            includeSelectAllOption: true,
	            maxHeight: 200
	        });

	        //customizedDesignationBox
	        $('.customizedDesignationBox').multiselect({
	            nonSelectedText: 'Select Designation',
	            numberDisplayed: 1,
	            enableFiltering:true,
	            enableCaseInsensitiveFiltering:true,
	            buttonWidth:'100%',
	            includeSelectAllOption: true,
	            maxHeight: 200
	        });

	        //customizedManagerBox
	        $('.customizedManagerBox').multiselect({
	            nonSelectedText: 'Select Manager',
	            numberDisplayed: 1,
	            enableFiltering:true,
	            enableCaseInsensitiveFiltering:true,
	            buttonWidth:'100%',
	            includeSelectAllOption: true,
	            maxHeight: 200
	        });

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

			var customizedDatatableWithScroll = $('.customizedDatatableWithScroll').DataTable({
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
			customizedDatatableWithScroll.button(0).remove();
		});
	</script>

</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 text-center titletagbar">Manage CATS Roles</div>
				<div class="col-md-12" id="LoadingImage" style="margin-top: 10px;">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" style="display: block;margin: 0 auto;width: 250px;">
				</div>
			</div>
		</div>
	</section>

	<section id="MainSection" class="topHeight hidden">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control" style="background-color: #2266AA;border-radius: 0px;border: #2266AA;color: #fff;outline: none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to home</button>
				</div>
				<div class="col-md-7"></div>
				<div class="col-md-3">
					<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
				</div>
			</div>
			<div class="row" style="margin-top: 30px;">
				<div class="col-md-3 col-md-offset-1">
					<label>Change Department :</label>
					<select class="customizedDepartmentBox" onchange="saveCatsRoles(this.value,'department')">
						<?php
							print_r(catsRolesDepartmentList($vtechMappingdbConn));
						?>
					</select>
				</div>
				<div class="col-md-4">
					<label>Change Designation :</label>
					<select class="customizedDesignationBox" onchange="saveCatsRoles(this.value,'designation')">
						<?php
							print_r(catsRolesDesignationList($allConn));
						?>
					</select>
				</div>
				<div class="col-md-3">
					<label>Change Manager :</label>
					<select class="customizedManagerBox" onchange="saveCatsRoles(this.value,'manager')">
						<?php
							print_r(catsRolesManagerList($catsConn));
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-10 col-md-offset-1" style="margin-top: 20px;">
					<table class="table table-striped table-bordered customizedDatatableWithScroll">
						<thead>
							<tr class="theadbar">
								<th class='no-sort' style="text-align: center;vertical-align: middle;"><!-- <input style="height:20px;width:20px;cursor: pointer;outline: none;" type='checkbox' name='select_all' id='select_all'> --></th>
								<th style="text-align: center;vertical-align: middle;">Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Department</th>
								<th style="text-align: center;vertical-align: middle;">Designation</th>
								<th style="text-align: center;vertical-align: middle;">Manager</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$mainQRY=mysqli_query($catsConn, "SELECT
									u.user_id,
									concat(u.first_name,' ',u.last_name) AS empname,
									u.notes AS manager,
								    (SELECT mcr.department FROM vtech_mappingdb.manage_cats_roles AS mcr WHERE mcr.user_id = u.user_id) AS department,
								    (SELECT mcr.designation FROM vtech_mappingdb.manage_cats_roles AS mcr WHERE mcr.user_id = u.user_id) AS designation,
								    u.access_level
								FROM
									user AS u
								ORDER BY empname ASC");
								while($mainROW = mysqli_fetch_array($mainQRY)){
							?>
							<tr class="tbodybar <?php echo $mainROW['access_level'] == 0 ? ' red' : '' ?>">
								<td style="text-align: center;vertical-align: middle;"><input type="checkbox" class="checkboxes" name="checked_id[<?php echo $mainROW['user_id']; ?>]" id="checked_id" value="<?php echo $mainROW['user_id']; ?>"></td>
								<td style="vertical-align: middle;"><?php echo ucwords($mainROW['empname']); ?></td>
								<td style="vertical-align: middle;">
									<div id="departmentTd<?php echo $mainROW['user_id']; ?>">
										<?php echo ucwords($mainROW['department']); ?>
									</div>
								</td>
								<td style="vertical-align: middle;">
									<div id="designationTd<?php echo $mainROW['user_id']; ?>">
										<?php echo ucwords($mainROW['designation']); ?>
									</div>
								</td>
								<td style="vertical-align: middle;">
									<div id="managerTd<?php echo $mainROW['user_id']; ?>">
										<?php echo ucwords($mainROW['manager']); ?>
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
		</div>
	</section>

<script>
	function saveCatsRoles(idValue,type){
		if ($('.checkboxes:checked').length == 0) {
			$.ajax({
				url: '<?php echo REPORT_PATH; ?>/others/manage_cats_roles/reload-department.php',
				success: function(output) {
					$('.customizedDepartmentBox').html(output);
        			$('.customizedDepartmentBox').multiselect("destroy");
					$('.customizedDepartmentBox').multiselect({
			            nonSelectedText: 'Select Department',
			            numberDisplayed: 1,
			            enableFiltering:true,
			            enableCaseInsensitiveFiltering:true,
			            buttonWidth:'100%',
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
				}
			});
			$.ajax({
				url: '<?php echo REPORT_PATH; ?>/others/manage_cats_roles/reload-designation.php',
				success: function(output) {
					$('.customizedDesignationBox').html(output);
        			$('.customizedDesignationBox').multiselect("destroy");
					$('.customizedDesignationBox').multiselect({
			            nonSelectedText: 'Select Designation',
			            numberDisplayed: 1,
			            enableFiltering:true,
			            enableCaseInsensitiveFiltering:true,
			            buttonWidth:'100%',
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
				}
			});
			$.ajax({
				url: '<?php echo REPORT_PATH; ?>/others/manage_cats_roles/reload-manager.php',
				success: function(output) {
					$('.customizedManagerBox').html(output);
        			$('.customizedManagerBox').multiselect("destroy");
					$('.customizedManagerBox').multiselect({
			            nonSelectedText: 'Select Manager',
			            numberDisplayed: 1,
			            enableFiltering:true,
			            enableCaseInsensitiveFiltering:true,
			            buttonWidth:'100%',
			            includeSelectAllOption: true,
			            maxHeight: 200
			        });
				}
			});
			alert("Please select atleast one personnel!");
		} else {
			var checkedBoxValue = $('.checkboxes:checked').serialize();

			$.ajax({
				url: '<?php echo REPORT_PATH; ?>/others/manage_cats_roles/save-cats-roles.php',
				type: 'POST',
				data: "idValue="+idValue+"&type="+type+"&"+checkedBoxValue,
				success: function(response) {
					if (response = 'success') {
						if (type == 'department') {
							$.each($('.checkboxes:checked'), function() {
								$("#departmentTd"+this.value).html("");
								$("#departmentTd"+this.value).html(idValue);
							});
							$.ajax({
								url: '<?php echo REPORT_PATH; ?>/others/manage_cats_roles/reload-department.php',
								success: function(output) {
									$('.customizedDepartmentBox').html(output);
	                    			$('.customizedDepartmentBox').multiselect("destroy");
									$('.customizedDepartmentBox').multiselect({
							            nonSelectedText: 'Select Department',
							            numberDisplayed: 1,
							            enableFiltering:true,
							            enableCaseInsensitiveFiltering:true,
							            buttonWidth:'100%',
							            includeSelectAllOption: true,
							            maxHeight: 200
							        });
								}
							});
		            		alert('Department Successfully Updated!');
						}
						if(type == 'designation') {
							$.each($('.checkboxes:checked'), function() {
								$("#designationTd"+this.value).html("");
								$("#designationTd"+this.value).html(idValue);
							});
							$.ajax({
								url: '<?php echo REPORT_PATH; ?>/others/manage_cats_roles/reload-designation.php',
								success: function(output) {
									$('.customizedDesignationBox').html(output);
	                    			$('.customizedDesignationBox').multiselect("destroy");
									$('.customizedDesignationBox').multiselect({
							            nonSelectedText: 'Select Designation',
							            numberDisplayed: 1,
							            enableFiltering:true,
							            enableCaseInsensitiveFiltering:true,
							            buttonWidth:'100%',
							            includeSelectAllOption: true,
							            maxHeight: 200
							        });
								}
							});
		            		alert('Designation Successfully Updated!');
						}
						if(type == 'manager') {
							$.each($('.checkboxes:checked'), function() {
								$("#managerTd"+this.value).html("");
								$("#managerTd"+this.value).html(idValue);
							});
							$.ajax({
								url: '<?php echo REPORT_PATH; ?>/others/manage_cats_roles/reload-manager.php',
								success: function(output) {
									$('.customizedManagerBox').html(output);
	                    			$('.customizedManagerBox').multiselect("destroy");
									$('.customizedManagerBox').multiselect({
							            nonSelectedText: 'Select Manager',
							            numberDisplayed: 1,
							            enableFiltering:true,
							            enableCaseInsensitiveFiltering:true,
							            buttonWidth:'100%',
							            includeSelectAllOption: true,
							            maxHeight: 200
							        });
								}
							});
		            		alert('Manager Successfully Updated!');
						}
						$('#select_all').prop("checked", false);
						$('.checkboxes').prop("checked", false);
					} else {
	            		alert('Something went wrong!');
					}
	            }
			});
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
