<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "67";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>ATS Analysis</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
			padding: 4px 0px;
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
		table.dataTable tbody td:nth-child(2) {
			text-align: left;
		}
		table.dataTable thead tr:nth-child(2) th:last-child {
			border-right: 1px solid #ddd;
		}
		table.scrollable-datatable thead tr:nth-child(1) {
			padding: 5px 0px;
			text-align: center;
			vertical-align: middle;
			font-size: 15px;
			color: #2266AA;
		}
		.scrollable-datatable .thead-tr-style,
		.scrollable-datatable .tfoot-tr-style {
			background-color: #ccc;
			color: #000;
			font-size: 13px;
		}
		.scrollable-datatable .tbody-tr-style {
			color: #333;
			font-size: 13px;
		}
		.modal-header {
			color: #fff;
			font-size: 20px;
			font-weight: bold;
			background-color: #2266AA;
			padding: 10px;
			text-align: center;
		}
		.modal-close-button {
			color: #fff;
		}
		.dark-button,
		.dark-button:focus {
			outline: none;
			color: #fff;
			background-color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
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
			margin-bottom: 80px;
		}
		.report-style {
			margin-top: 10px;
			margin-bottom: 0px;
		}
		.thead-tr-style th {
			background-color: #ccc;
			color: #000;
			font-size: 14px;
		}
		.tbody-tr-style td {
			color: #333;
			font-size: 14px;
		}
		.tfoot-tr-style th {
			background-color: #ccc;
			color: #000;
			font-size: 15px;
		}
	</style>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">ATS Analysis</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

<?php
	$atsAnalysis = array();

	$thirdMonthStartDate = date("Y-m-d", strtotime("-3 months"));
	$thirdMonthEndDate = date("Y-m-d");

	$atsAnalysisQUERY = mysqli_query($allConn, "SELECT
		COUNT(DISTINCT c.candidate_id) AS total_candidates,
	    COUNT(DISTINCT CASE WHEN ((DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$thirdMonthStartDate' AND '$thirdMonthEndDate') AND (cjsh.status_to = '200')) THEN c.candidate_id END) AS total_candidates_contacted_within_last_3_months,
	    COUNT(DISTINCT CASE WHEN ((DATE_FORMAT(c.date_created, '%Y-%m-%d') BETWEEN '$thirdMonthStartDate' AND '$thirdMonthEndDate') AND (cjsh.status_to != '200')) THEN c.candidate_id END) AS total_candidates_not_contacted_over_3_months,
	    COUNT(DISTINCT CASE WHEN c.email1 != '' THEN c.candidate_id END) AS total_primary_emails,
	    COUNT(DISTINCT CASE WHEN c.email2 != '' THEN c.candidate_id END) AS total_secondary_emails,
	    COUNT(DISTINCT CASE WHEN c.phone_home != '' THEN c.phone_home END) AS total_primary_contact_number,
	    COUNT(DISTINCT CASE WHEN c.phone_cell != '' THEN c.phone_cell END) AS total_secondary_contact_number,
	    (SELECT COUNT(DISTINCT ef.data_item_id) FROM cats.extra_field AS ef WHERE ef.field_name = 'DNC Status' AND ef.value = 'Yes') AS total_candidates_in_DNC
	FROM
		cats.candidate AS c
	    LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.candidate_id = c.candidate_id");

	while ($atsAnalysisROW = mysqli_fetch_array($atsAnalysisQUERY)) {
		
		$atsAnalysis["ats"]["total_candidates"] = $atsAnalysisROW["total_candidates"];
		$atsAnalysis["ats"]["total_monthly_registered_candidates_for_last_2_years"] = "modal";
		$atsAnalysis["ats"]["total_candidates_contacted_within_last_3_months"] = $atsAnalysisROW["total_candidates_contacted_within_last_3_months"];
		$atsAnalysis["ats"]["total_candidates_not_contacted_over_3_months"] = $atsAnalysisROW["total_candidates_not_contacted_over_3_months"];
		$atsAnalysis["ats"]["total_primary_emails"] = $atsAnalysisROW["total_primary_emails"];
		$atsAnalysis["ats"]["total_secondary_emails"] = $atsAnalysisROW["total_secondary_emails"];
		$atsAnalysis["ats"]["total_primary_contact_number"] = $atsAnalysisROW["total_primary_contact_number"];
		$atsAnalysis["ats"]["total_secondary_contact_number"] = $atsAnalysisROW["total_secondary_contact_number"];
		$atsAnalysis["ats"]["total_monthly_submissions_for_last_2_years"] = "modal";
		$atsAnalysis["ats"]["total_monthly_interviews_for_last_2_years"] = "modal";
		$atsAnalysis["ats"]["total_monthly_offers_for_last_2_years"] = "modal";
		$atsAnalysis["ats"]["total_monthly_placements_for_last_2_years"] = "modal";
		$atsAnalysis["ats"]["average_submissions_per_recruiter_per_week_for_last_2_years"] = "modal";
		$atsAnalysis["ats"]["average_placements_per_recruiter_per_week_for_last_2_years"] = "modal";
		$atsAnalysis["ats"]["total_candidates_in_DNC"] = $atsAnalysisROW["total_candidates_in_DNC"];

	}
?>

	<section class="main-section hidden">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back to Home</button>
				</div>
				<div class="col-md-8"></div>
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
				</div>
			</div>
			<div class="row report-style">
				<div class="col-md-8 col-md-offset-2">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>No.</th>
								<th>Title</th>
								<th>Answer</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$count = 1;
								foreach ($atsAnalysis["ats"] as $atsAnalysisKey => $atsAnalysisValue) {
							?>
							<tr class="tbody-tr-style">
								<td><?php echo $count; ?></td>
								<td><?php echo ucwords(implode(" ", explode("_", $atsAnalysisKey))); ?></td>
								<td>
								<?php
									if ($atsAnalysisValue == "modal") {
								?>
									<i class="fa fa-eye" style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="View"></i>
								<?php
									} else {
										echo $atsAnalysisValue;
									}
								?>
								</td>
							</tr>
							<?php
									$count++;
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

		var customizedDataTable = $(".customized-datatable").DataTable({
		    dom: "Bfrtip",
		    "paging": false,
		    "aaSorting": [[0,"asc"]],
	        buttons:[
	            "excel"
	        ],
	        initComplete: function(){
	        	$("div.dataTables_filter input").css("width","250")
			}
		});
		customizedDataTable.button(0).nodes().css("background", "#2266AA");
		customizedDataTable.button(0).nodes().css("border", "#2266AA");
		customizedDataTable.button(0).nodes().css("color", "#fff");
		customizedDataTable.button(0).nodes().html("Download Report");
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
