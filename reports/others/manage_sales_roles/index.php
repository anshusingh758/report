<?php
	include("../../../security.php");
	include("../../../functions/reporting-service.php");
    if(isset($_SESSION['user'])){
		error_reporting(0);
		include('../../../config.php');

    	$childUser=$_SESSION['userMember'];
		$reportID='42';
		$sessionQuery="SELECT * FROM mapping JOIN users ON users.uid=mapping.uid JOIN reports ON reports.rid=mapping.rid WHERE mapping.uid='$user' AND mapping.rid='$reportID' AND users.ustatus='1' AND reports.rstatus='1'";
		$sessionResult=mysqli_query($misReportsConn,$sessionQuery);
		if(mysqli_num_rows($sessionResult)>0){
?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage Sales Roles</title>
	
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
	</style>

	<!--customizedDatatableWithScroll-->
	<script>
		$(document).ready(function(){
			$("#LoadingImage").hide();
			$('#MainSection').removeClass("hidden");

	        //customizedSelectBoxWOAll
	        $('.customizedSelectBoxWOAll').multiselect({
	            nonSelectedText: 'Select Option',
	            numberDisplayed: 1,
	            enableFiltering:true,
	            enableCaseInsensitiveFiltering:true,
	            buttonWidth:'100%',
	            includeSelectAllOption: true,
	            maxHeight: 200
	        });

			$('#editManager').multiselect({
				nonSelectedText: 'Select Manager',
				numberDisplayed: 1,
				enableFiltering:true,
				enableCaseInsensitiveFiltering:true,
				buttonWidth:'100%',
				includeSelectAllOption: true,
 				maxHeight: 150
			});

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
				<div class="col-md-12 text-center titletagbar">Manage Sales Roles</div>
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
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="form-control" style="background-color: #2266AA;border-radius: 0px;border: 1px solid #2266AA;color: #fff;outline: none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to home</button>
				</div>
				<div class="col-md-3 col-md-offset-3">
					<button type="button" class="form-control" data-toggle="modal" data-target="#changeDepartmentManager" style="background-color: #fff;border-radius: 0px;border: 1px solid #2266AA;color: #2266AA;outline: none;font-weight: bold;"><i class="fa fa-edit" aria-hidden="true"></i> Change Department Manager</button>
				</div>
				<div class="col-md-1 col-md-offset-3">
					<a href="../../../logout.php" class="btn btnx pull-right" style="color: #fff;"><i class="fa fa-fw fa-power-off"></i> Logout</a>
				</div>
			</div>
			<div class="row" style="margin-top: 30px;">
				<div class="col-md-4 col-md-offset-2">
					<label>Change Department :</label>
					<select class="customizedDepartmentBox" onchange="saveSalesRoles(this.value,'department')">
						<?php
							print_r(salesRolesDepartmentList($sales_connect));
						?>
					</select>
				</div>
				<div class="col-md-4">
					<label>Change Manager :</label>
					<select class="customizedManagerBox" onchange="saveSalesRoles(this.value,'manager')">
						<?php
							print_r(salesRolesManagerList($sales_connect));
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 col-md-offset-2" style="margin-top: 20px;">
					<table class="table table-striped table-bordered customizedDatatableWithScroll">
						<thead>
							<tr class="theadbar">
								<th class='no-sort' style="text-align: center;vertical-align: middle;"><input style="height:20px;width:20px;cursor: pointer;outline: none;" type='checkbox' name='select_all' id='select_all'></th>
								<th style="text-align: center;vertical-align: middle;">Personnel</th>
								<th style="text-align: center;vertical-align: middle;">Department</th>
								<th style="text-align: center;vertical-align: middle;">Manager</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$mainQRY = mysqli_query($sales_connect, "SELECT
									u.id,
								    CONCAT(u.firstName, ' ', u.lastName) AS personnel,
									(SELECT msr.department FROM vtech_mappingdb.manage_sales_roles AS msr WHERE msr.user_id = u.id) AS department,
									(SELECT msr.manager_name FROM vtech_mappingdb.manage_sales_roles AS msr WHERE msr.user_id = u.id) AS manager
								FROM
								    x2_users AS u
								ORDER BY personnel ASC");
								while($mainROW = mysqli_fetch_array($mainQRY)){
							?>
							<tr class="tbodybar">
								<td style="text-align: center;vertical-align: middle;"><input type="checkbox" class="checkboxes" name="checked_id[<?php echo $mainROW['id']; ?>]" id="checked_id" value="<?php echo $mainROW['id']; ?>"></td>
								<td style="vertical-align: middle;"><?php echo ucwords($mainROW['personnel']); ?></td>
								<td style="vertical-align: middle;">
									<div id="departmentTd<?php echo $mainROW['id']; ?>">
										<?php echo ucwords($mainROW['department']); ?>
									</div>
								</td>
								<td style="vertical-align: middle;">
									<div id="managerTd<?php echo $mainROW['id']; ?>">
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

	<div id="changeDepartmentManager" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<form id="changeDepartmentManagerForm">
					<div class="modal-header" style="background-color: #2266AA;color: #fff;">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title text-center">Change Department Manager</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-12">
								<label>Select Department :</label>
								<select class="customizedSelectBoxWOAll" name="selectDepartment" onchange="selectManager(this.value)" required>
									<option value="">Select Option</option>
									<?php
										$deptQUERY = mysqli_query($sales_connect, "SELECT
										    id AS dept_id,
										    name AS dept_name
										FROM
										    x2_roles
										WHERE
										    id IN(5, 6, 7, 8)
										ORDER BY dept_name ASC");
										while($deptROW = mysqli_fetch_array($deptQUERY)){
											echo "<option value='".$deptROW['dept_name']."'>".$deptROW['dept_name']."</option>";
										}
									?>
								</select>
							</div>
						</div>
						<div class="row" style="margin-top: 10px;">
							<div class="col-sm-12">
								<label>Edit Manager :</label>
								<select id="editManager" name="editManager[]" multiple required>
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer" style="margin-top: 40px;padding: 10px 5px;">
						<button type="submit" class="btn" style="background-color: #2266AA;color: #fff;border: 1px solid #2266AA;"><i class="fa fa-edit"></i> Change</button>
					</div>
				</form>
			</div>
		</div>
	</div>

<script>

	function selectManager(dname){
		$.ajax({
			type:'POST',
			url:'<?php echo REPORT_PATH; ?>/others/manage_sales_roles/manage-manager.php',
			data:'dname='+dname,
			success:function(output){
				$('#editManager').html(output);
				$('#editManager').multiselect("destroy");
				$('#editManager').multiselect({
					nonSelectedText: 'Select Manager',
					numberDisplayed: 1,
					enableFiltering:true,
					enableCaseInsensitiveFiltering:true,
					buttonWidth:'100%',
					includeSelectAllOption: true,
	 				maxHeight: 150
				});
			}
		});
	}
	
	$('#changeDepartmentManagerForm').submit(function(e) {
			e.preventDefault();
			$.ajax({
				url: '<?php echo REPORT_PATH; ?>/others/manage_sales_roles/save-department-manager.php',
				type: 'POST',
				data: $('#changeDepartmentManagerForm').serialize(),
				success: function(response){
					if (response = 'success') {
						location.reload();
		        		alert('Manager Successfully Changed!');
		        	} else {
		        		alert('Something went wrong!');
		        	}
	            }
			});
	});

	function saveSalesRoles(idValue,type){
		if ($('.checkboxes:checked').length == 0) {
			$.ajax({
				url: '<?php echo REPORT_PATH; ?>/others/manage_sales_roles/reload-department.php',
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
				url: '<?php echo REPORT_PATH; ?>/others/manage_sales_roles/reload-manager.php',
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
				url: '<?php echo REPORT_PATH; ?>/others/manage_sales_roles/save-sales-roles.php',
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
								url: '<?php echo REPORT_PATH; ?>/others/manage_sales_roles/reload-department.php',
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
						if(type == 'manager') {
							$.each($('.checkboxes:checked'), function() {
								$("#managerTd"+this.value).html("");
								$("#managerTd"+this.value).html(idValue);
							});
							$.ajax({
								url: '<?php echo REPORT_PATH; ?>/others/manage_sales_roles/reload-manager.php',
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
