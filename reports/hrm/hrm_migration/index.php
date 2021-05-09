<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "68";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>HRM Migration Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
			padding: 3px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td {
			padding: 2px 1px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td a {
			cursor: pointer;
			font-weight: bold;
		}
		.dark-button,
		.dark-button:focus {
			outline: none;
			color: #fff;
			background-color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
		}
		.smooth-button,
		.smooth-button:focus {
			outline: none;
			background-color: #fff;
			color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
			font-weight: bold;
		}
		.logout-button {
			outline: none;
			color: #fff;
			background-color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
			padding: 5px 12px;
			float: right;
		}
		.report-title {
			color: #000;
			font-size: 27px;
			background-color: #aaa;
			padding: 10px;
			text-align: center;
		}
		.loading-image-style {
			display: block;
			margin: 0 auto;
			width: 250px;
		}
		.main-section {
			margin-top: 20px;
			margin-bottom: 50px;
		}
		.report-bottom-style {
			margin-bottom: 30px;
		}
		.thead-tr-style th {
			background-color: #ccc;
			color: #000;
			font-size: 12px;
		}
		.tbody-tr-style td {
			color: #333;
			font-size: 12px;
		}
	</style>
</head>
<body>

	<?php include_once("../../../popups.php"); ?>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">HRM Migration Report</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back to Home</button>
				</div>
				<div class="col-md-8">
				</div>
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
				</div>
			</div>
		</div>
	</section>

	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th colspan="5">HRM</th>
								<th colspan="13">CATS</th>
							</tr>
							<tr class="thead-tr-style">
								<th>EID</th>
								<th>Employee</th>
								<th>Status</th>
								<th>Join Date</th>
								<th>Termination Date</th>
								<th>Candidate</th>
								<th>Job Order</th>
								<th>Company</th>
								<th>Company Manager</th>
								<th>Recruiter</th>
								<th>Recruiter Manager</th>
								<th>EA Person</th>
								<th>Inside Sales1</th>
								<th>Inside Sales2</th>
								<th>Research By</th>
								<th>Inside Post Sales</th>
								<th>Onsite Sales</th>
								<th>Onsite Post Sales</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$mainQUERY = mysqli_query($allConn, "SELECT
									si.*,
									CONCAT(e.first_name,' ',e.last_name) AS employee_name,
									e.status AS employee_status,
									DATE_FORMAT(e.custom7, '%Y-%m-%d') AS join_date,
									DATE_FORMAT(e.termination_date, '%Y-%m-%d') AS termination_date,
									c.candidate_id,
									CONCAT(c.first_name,' ',c.last_name) AS candidate_name,
									j.joborder_id,
									j.title AS joborder_title,
									co.company_id,
									co.name AS company_name,
									IF(CONCAT(u_co_man.first_name,' ',u_co_man.last_name) != '', CONCAT(u_co_man.first_name,' ',u_co_man.last_name), '---') AS company_manager_name,
									IF(CONCAT(u_rec.first_name,' ',u_rec.last_name) != '', CONCAT(u_rec.first_name,' ',u_rec.last_name), '---') AS recruiter_name,
									IF(si.c_recruiter_manager_name != '', si.c_recruiter_manager_name, '---') AS c_recruiter_manager_name,
									IF(mer.mapping_value != '', mer.mapping_value, '---') AS ea_person,
									IF(si.c_inside_sales1 != '', si.c_inside_sales1, '---') AS c_inside_sales1,
									IF(si.c_inside_sales2 != '', si.c_inside_sales2, '---') AS c_inside_sales2,
									IF(si.c_research_by != '', si.c_research_by, '---') AS c_research_by,
									IF(si.c_inside_post_sales != '', si.c_inside_post_sales, '---') AS c_inside_post_sales,
									IF(si.c_onsite_sales != '', si.c_onsite_sales, '---') AS c_onsite_sales,
									IF(si.c_onsite_post_sales != '', si.c_onsite_post_sales, '---') AS c_onsite_post_sales
								FROM
									vtech_mappingdb.system_integration AS si
									LEFT JOIN vtechhrm.employees AS e ON e.id = si.h_employee_id
									LEFT JOIN cats.candidate AS c ON c.candidate_id = si.c_candidate_id
									LEFT JOIN cats.joborder AS j ON j.joborder_id = si.c_joborder_id
									LEFT JOIN cats.company AS co ON co.company_id = si.c_company_id
									LEFT JOIN cats.user AS u_co_man ON u_co_man.user_id = si.c_company_manager_id
									LEFT JOIN cats.user AS u_rec ON u_rec.user_id = si.c_recruiter_id
									LEFT JOIN vtech_mappingdb.manage_ea_roles AS mer ON mer.reference_id = si.h_employee_id AND mer.reference_type = 'Employee'
								GROUP BY si.id");

								if (mysqli_fetch_array($mainQUERY) > 0) {
									while ($mainROW = mysqli_fetch_array($mainQUERY)) {
							?>
							<tr class="tbody-tr-style">
								<td><?php echo $mainROW["h_employee_id"]; ?></td>
								<td><?php echo ucwords($mainROW["employee_name"]); ?></td>
								<td><?php echo $mainROW["employee_status"]; ?></td>
								<td nowrap><?php echo $mainROW["join_date"]; ?></td>
								<td nowrap><?php if ($mainROW["employee_status"] == "Terminated" || $mainROW["employee_status"] == "Termination Vol" || $mainROW["employee_status"] == "Termination In_Vol") { echo $mainROW["termination_date"]; } else { echo "---"; } ?></td>
								<td><a href="https://ats.vtechsolution.com/index.php?m=candidates&a=show&candidateID=<?php echo $mainROW["candidate_id"]; ?>" target="_blank"><?php echo ucwords($mainROW["candidate_name"]); ?></a></td>
								<td><a href="https://ats.vtechsolution.com/index.php?m=joborders&a=show&jobOrderID=<?php echo $mainROW["joborder_id"]; ?>" target="_blank"><?php echo $mainROW["joborder_title"]; ?></a></td>
								<td><a href="https://ats.vtechsolution.com/index.php?m=companies&a=show&companyID=<?php echo $mainROW["company_id"]; ?>" target="_blank"><?php echo $mainROW["company_name"]; ?></a></td>
								<td><?php echo ucwords($mainROW["company_manager_name"]); ?></td>
								<td><?php echo ucwords($mainROW["recruiter_name"]); ?></td>
								<td><?php echo ucwords($mainROW["c_recruiter_manager_name"]); ?></td>
								<td><?php echo ucwords($mainROW["ea_person"]); ?></td>
								<td><?php echo ucwords($mainROW["c_inside_sales1"]); ?></td>
								<td><?php echo ucwords($mainROW["c_inside_sales2"]); ?></td>
								<td><?php echo ucwords($mainROW["c_research_by"]); ?></td>
								<td><?php echo ucwords($mainROW["c_inside_post_sales"]); ?></td>
								<td><?php echo ucwords($mainROW["c_onsite_sales"]); ?></td>
								<td><?php echo ucwords($mainROW["c_onsite_post_sales"]); ?></td>
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

<script>
	$(document).ready(function(){
		$(".loading-image").hide();
		$(".main-section").removeClass("hidden");
		$(".customized-datatable-section").removeClass("hidden");

		var customizedDataTable = $(".customized-datatable").DataTable({
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		    dom: "Bfrtip",
		    "aaSorting": [[1,"asc"]],
	        buttons:[
	            "excel","pageLength"
	        ],
	        initComplete: function(){
	        	$("div.dataTables_filter input").css("width","250")
			}
		});
		customizedDataTable.button(0).nodes().css("background", "#2266AA");
		customizedDataTable.button(0).nodes().css("border", "#2266AA");
		customizedDataTable.button(0).nodes().css("color", "#fff");
		customizedDataTable.button(0).nodes().html("Download Report");
		customizedDataTable.button(1).nodes().css("background", "#449D44");
		customizedDataTable.button(1).nodes().css("border", "#449D44");
		customizedDataTable.button(1).nodes().css("color", "#fff");
	});
</script>
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
