<?php
	header('Content-Type: text/html; charset=ISO-8859-1');
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "6";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>MD Report Settings</title>

	<?php
		include_once("../../../cdn.php");
	?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot td{
			padding: 5px 1px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td{
			padding: 2px 1px;
			text-align: center;
			vertical-align: middle;
		}
		table.taxTable tbody td:nth-child(1),
		table.taxTable tbody td:nth-child(2),
		table.candidateTable tbody td:nth-child(1),
		table.clientTable tbody td:nth-child(1) {
			text-align: left;
		}
		.darkButton,
		.darkButton:focus{
			background-color: #2266AA;
			color: #fff;
			border: 1px solid #2266AA;
			border-radius: 0px;
			padding: 5px 10px;
		}
		.smoothButton,
		.smoothButton:focus{
			background-color: #fff;
			color: #2266AA;
			border: 1px solid #2266AA;
			border-radius: 0px;
			padding: 5px 10px;
			font-weight: bold;
		}
		.titleStyle {
			text-align: center;
			font-size: 30px;
			background-color: #aaa;
			color: #111;
			padding: 10px;
		}
		.loadingImg {
			display: block;
			margin: 0 auto;
			width: 250px;
		}
		.sectionPadding {
			margin-top: 30px;
			margin-bottom: 20px;
		}
		.sectionTitle {
			text-align: center;
			font-size: 20px;
			color: #2266AA;
			font-weight: bold;
			background-color: #ccc;
			padding: 3px 0px 3px 0px;
		}
		.syncButton {
			background-color: #673AB7;
			border-radius: 0px;
			border: 1px solid #673AB7;
			color: #fff;
			padding: 5px 10px;
		}
		.tableMargin {
			margin-top: 30px;
		}
		.thead-tr-style {
			background-color: #ccc;
			color: #000;
			font-size: 14px;
		}
		.tbody-tr-style {
			font-size: 14px;
		}
		.editModalClass {
			cursor: pointer;
		}
		.adminFeesPercentageError,
		.markupPercentageError,
		.marginPercentageError,
		.save-default-charges-error {
	        font-size: 12px;
	        color: red;
	        font-style: italic;
	        font-weight: bold;
		}
	</style>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 titleStyle">MD Report Settings</div>
			</div>
			<div class="row">
				<div class="col-md-12 loadingImage">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loadingImg">
				</div>
			</div>
			<div class="row navPanel hidden" style="margin-top: 20px;">
				<div class="col-md-2">
					<button onclick="location.href='<?php echo REPORT_PATH; ?>/md/md_report/index.php'" class="smoothButton pull-left"><i class="fa fa-arrow-left"></i> Back to MD Report</button>
				</div>
				<div class="col-md-2">
					<button class="darkButton form-control taxButton">Tax Rate</button>
				</div>
				<div class="col-md-2">
					<button class="smoothButton form-control candidateButton">Candidate Fees</button>
				</div>
				<div class="col-md-2">
					<button class="smoothButton form-control clientButton">Client Fees</button>
				</div>
				<div class="col-md-2">
					<button class="smoothButton form-control clientMarginMarkupButton">Client Markup / Margin</button>
				</div>
				<div class="col-md-2">
					<button type="button" class="syncButton pull-right"><i class="fa fa-refresh fa-spin fa-1x"></i> Sync All Tables</button>
				</div>
			</div>
		</div>
	</section>
	
	<section class="outputSection hidden">
		<div class="taxSection sectionPadding hidden">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-md-offset-4 sectionTitle">Tax Settings</div>
				</div>
				<div class="row tableMargin">
					<div class="col-md-8 col-md-offset-2">
						<table class="table table-striped table-bordered customizedDataTable taxTable">
							<thead>
								<tr class="thead-tr-style">
									<th>Employment Type</th>
									<th>Benefits</th>
									<th>Tax %</th>
									<th class="no-sort">Edit</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$taxQUERY = mysqli_query($vtechMappingdbConn, "SELECT * FROM tax_settings ORDER BY empst_id ASC");
									$dataCutomObject = "";
									while ($taxROW = mysqli_fetch_array($taxQUERY)) {
										$dataCutomObject = "dataType=tax&dataId=".$taxROW['id']."&empType=".$taxROW['emp_type']."&benefit=".$taxROW['benefits']."&chargePct=".$taxROW['charge_pct'];
								?>
								<tr class="tbody-tr-style">
									<td class="taxtOne"><?php echo $taxROW['emp_type']; ?></td>
									<td class="taxTwo"><?php echo $taxROW['benefits']; ?></td>
									<td class="taxThree<?php echo $taxROW['id']; ?>"><?php echo $taxROW['charge_pct']; ?></td>
									<td><a class="editModalClass" data-custom="<?php echo $dataCutomObject; ?>"><i class="fa fa-pencil-square-o"></i></a></td>
								</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="candidateSection sectionPadding hidden">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-md-offset-4 sectionTitle">Candidate Fees</div>
				</div>
				<div class="row tableMargin">
					<div class="col-md-8 col-md-offset-2">
						<table class="table table-striped table-bordered customizedDataTable candidateTable">
							<thead>
								<tr class="thead-tr-style">
									<th>Candidate</th>
									<th>Prime Fees %</th>
									<th>Prime Fees $</th>
									<th>Other Fees $</th>
									<th class="no-sort">Edit</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$candidateQUERY = mysqli_query($vtechMappingdbConn, "SELECT * FROM candidate_fees");
									$dataCutomObject = "";
									while ($candidateROW = mysqli_fetch_array($candidateQUERY)) {
										$dataCutomObject = "dataType=candidate&dataId=".$candidateROW['id']."&candidateId=".$candidateROW['emp_id']."&candidateName=".$candidateROW['e_name']."&primeChargePct=".$candidateROW['c_primeCharge_pct']."&primeChargeDlr=".$candidateROW['c_primeCharge_dlr']."&anyChargeDlr=".$candidateROW['c_anyCharge_dlr'];
								?>
								<tr class="tbody-tr-style">
									<td class="candidateOne"><?php echo $candidateROW['e_name']; ?></td>
									<td class="candidateTwo<?php echo $candidateROW['id']; ?>"><?php echo $candidateROW['c_primeCharge_pct']; ?></td>
									<td class="candidateThree<?php echo $candidateROW['id']; ?>"><?php echo $candidateROW['c_primeCharge_dlr']; ?></td>
									<td class="candidateFour<?php echo $candidateROW['id']; ?>"><?php echo $candidateROW['c_anyCharge_dlr']; ?></td>
									<td><a class="editModalClass" data-custom="<?php echo $dataCutomObject; ?>"><i class="fa fa-pencil-square-o"></i></a></td>
								</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="clientSection sectionPadding hidden">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-md-offset-4 sectionTitle">Client Fees</div>
				</div>
				<div class="row tableMargin">
					<div class="col-md-10 col-md-offset-1">
						<table class="table table-striped table-bordered customizedDataTable clientTable">
							<thead>
								<tr class="thead-tr-style">
									<th>Client</th>
									<th>MSP Fees %</th>
									<th>MSP Fees $</th>
									<th>Prime Fees %</th>
									<th>Prime Fees $</th>
									<th class="no-sort">Edit</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$clientQUERY = mysqli_query($vtechMappingdbConn, "SELECT * FROM client_fees");
									$dataCutomObject = "";
									while ($clientROW = mysqli_fetch_array($clientQUERY)) {
										$dataCutomObject = "dataType=client&dataId=".$clientROW['id']."&clientId=".$clientROW['client_id']."&clientName=".$clientROW['client_name']."&mspChrgPct=".$clientROW['mspChrg_pct']."&mspChrgDlr=".$clientROW['mspChrg_dlr']."&primeChrgPct=".$clientROW['primeChrg_pct']."&primeChrgDlr=".$clientROW['primeChrg_dlr'];
								?>
								<tr class="tbody-tr-style">
									<td class="clientOne"><?php echo $clientROW['client_name']; ?></td>
									<td class="clientTwo<?php echo $clientROW['id']; ?>"><?php echo $clientROW['mspChrg_pct']; ?></td>
									<td class="clientThree<?php echo $clientROW['id']; ?>"><?php echo $clientROW['mspChrg_dlr']; ?></td>
									<td class="clientFour<?php echo $clientROW['id']; ?>"><?php echo $clientROW['primeChrg_pct']; ?></td>
									<td class="clientFive<?php echo $clientROW['id']; ?>"><?php echo $clientROW['primeChrg_dlr']; ?></td>
									<td><a class="editModalClass" data-custom="<?php echo $dataCutomObject; ?>"><i class="fa fa-pencil-square-o"></i></a></td>
								</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="clientMarginMarkupSection sectionPadding hidden">
			<div class="container">
				<div class="row">
					<div class="col-md-4 col-md-offset-4 sectionTitle">Client Markup / Margin</div>
				</div>
				<div class="row tableMargin">
					<div class="col-md-8 col-md-offset-1">
						<table class="table table-striped table-bordered customizedDataTable clientTable">
							<thead>
								<tr class="thead-tr-style">
									<th>Client</th>
									<th>Type</th>
									<th>Value %</th>
									<th class="no-sort">Edit</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$clientMarkupMarginQUERY = mysqli_query($allConn, "SELECT
											cmm.*
										FROM
											vtech_mappingdb.client_markup_margin AS cmm
										GROUP BY cmm.id");
									$dataCutomObject = "";
									while ($clientMarkupMarginROW = mysqli_fetch_array($clientMarkupMarginQUERY)) {
										$dataCutomObject = "dataType=clientMarkupMargin&dataId=".$clientMarkupMarginROW['id']."&clientId=".$clientMarkupMarginROW['client_id']."&clientName=".$clientMarkupMarginROW['client_name']."&clientType=".$clientMarkupMarginROW['type']."&percentageValue=".$clientMarkupMarginROW['value'];
								?>
								<tr class="tbody-tr-style">
									<td nowrap class="clientMarkupMarginOne"><?php echo $clientMarkupMarginROW['client_name']; ?></td>
									<td class="clientMarkupMarginTwo<?php echo $clientMarkupMarginROW['id']; ?>"><?php echo $clientMarkupMarginROW['type']; ?></td>
									<td class="clientMarkupMarginThree<?php echo $clientMarkupMarginROW['id']; ?>"><?php echo $clientMarkupMarginROW['value']; ?></td>
									<td><a class="editModalClass" data-custom="<?php echo $dataCutomObject; ?>"><i class="fa fa-pencil-square-o"></i></a></td>
								</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
					<?php
						$defaultChargesQUERY = mysqli_query($allConn, "SELECT
							MAX(CASE WHEN ic.comment = 'Admin Fees' THEN ic.value END) AS admin_fees_percentage,
							MAX(CASE WHEN ic.comment = 'vTech Markup' THEN ic.value END) AS vtech_markup_percentage,
							MAX(CASE WHEN ic.comment = 'vTech Margin' THEN ic.value END) AS vtech_margin_percentage
						FROM
							mis_reports.incentive_criteria AS ic
						WHERE
							ic.personnel = 'Default MD Percentage'");
						if (mysqli_num_rows($defaultChargesQUERY) > 0) {
							while ($defaultChargesROW = mysqli_fetch_array($defaultChargesQUERY)) {
					?>
					<div class="col-md-2 col-md-offset-1" style="border: 1px solid #aaa;">
						<form class="defaultChargesForm">
							<div class="row">
								<div class="col-md-12" style="font-size: 20px;text-align: center;font-weight: bold;background-color: #2266AA;color: #fff;padding: 5px 10px;">Default</div>
							</div>
							<div class="row" style="margin-top: 10px;">
								<div class="col-md-12">
									<label>Admin Fees % :</label>
									<input type="text" name="adminFeesPercentage" class="adminFeesPercentage readOnlyInput form-control" value="<?php echo $defaultChargesROW['admin_fees_percentage']; ?>" readOnly required>
									<span class="adminFeesPercentageError hidden">Enter 0 to 100 Only!</span>
								</div>
							</div>
							<div class="row" style="margin-top: 5px;">
								<div class="col-md-12">
									<label>Markup % :</label>
									<input type="text" name="markupPercentage" class="markupPercentage readOnlyInput form-control" value="<?php echo $defaultChargesROW['vtech_markup_percentage']; ?>" readOnly required>
									<span class="markupPercentageError hidden">Enter 0 to 100 Only!</span>
								</div>
							</div>
							<div class="row" style="margin-top: 5px;">
								<div class="col-md-12">
									<label>Margin % :</label>
									<input type="text" name="marginPercentage" class="marginPercentage readOnlyInput form-control" value="<?php echo $defaultChargesROW['vtech_margin_percentage']; ?>" readOnly required>
									<span class="marginPercentageError hidden">Enter 0 to 100 Only!</span>
								</div>
							</div>
							<div class="row" style="margin-top: 15px;margin-bottom: 10px;">
								<div class="col-md-12">
									<input type="hidden" name="errorValue" id="errorValue" value="false">
									<button type="button" class="darkButton edit-default-charges pull-right"><i class="fa fa-pencil-square-o"></i> Edit</button>
									<button type="submit" class="smoothButton save-default-charges pull-right hidden"><i class="fa fa-floppy-o"></i> Save</button>
									<span class="save-default-charges-error hidden"><br><br>Oops, Something Wrong!</span>
								</div>
							</div>
						</form>
					</div>
					<?php
							}
						}
					?>
				</div>
			</div>
		</div>
	</section>

	<div id="editModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content" id="modalViewSection">
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function(){
			
			$(".loadingImage").hide();
			$('.navPanel').removeClass("hidden");
			$('.outputSection').removeClass("hidden");
			$('.taxSection').removeClass("hidden");

			var customizedDataTable = $('.customizedDataTable').DataTable({
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			    dom: 'Bfrtip',
			    "columnDefs":[{
					"targets" : 'no-sort',
					"orderable": false,
			    }],
			    "aaSorting": [[0,'asc']],
		        buttons:[
		            'pageLength'
		        ],
		        initComplete: function(){
		        	$('div.dataTables_filter input').css("width","250")
				}
			});

			$('.candidateSection').addClass("hidden");
			$('.clientSection').addClass("hidden");
			$('.clientMarginMarkupSection').addClass("hidden");

			$('.taxButton').click(function(e){
				e.preventDefault();
				$('.taxButton').addClass("darkButton");
				$('.taxButton').removeClass("smoothButton");
				$('.candidateButton').addClass("smoothButton");
				$('.candidateButton').removeClass("darkButton");
				$('.clientButton').addClass("smoothButton");
				$('.clientButton').removeClass("darkButton");
				$('.clientMarginMarkupButton').addClass("smoothButton");
				$('.clientMarginMarkupButton').removeClass("darkButton");
				$('.taxSection').removeClass("hidden");
				$('.candidateSection').addClass("hidden");
				$('.clientSection').addClass("hidden");
				$('.clientMarginMarkupSection').addClass("hidden");
			});

			$('.candidateButton').click(function(e){
				e.preventDefault();
				$('.taxButton').addClass("smoothButton");
				$('.taxButton').removeClass("darkButton");
				$('.candidateButton').addClass("darkButton");
				$('.candidateButton').removeClass("smoothButton");
				$('.clientButton').addClass("smoothButton");
				$('.clientButton').removeClass("darkButton");
				$('.clientMarginMarkupButton').addClass("smoothButton");
				$('.clientMarginMarkupButton').removeClass("darkButton");
				$('.taxSection').addClass("hidden");
				$('.candidateSection').removeClass("hidden");
				$('.clientSection').addClass("hidden");
				$('.clientMarginMarkupSection').addClass("hidden");
			});

			$('.clientButton').click(function(e){
				e.preventDefault();
				$('.taxButton').addClass("smoothButton");
				$('.taxButton').removeClass("darkButton");
				$('.candidateButton').addClass("smoothButton");
				$('.candidateButton').removeClass("darkButton");
				$('.clientButton').addClass("darkButton");
				$('.clientButton').removeClass("smoothButton");
				$('.clientMarginMarkupButton').addClass("smoothButton");
				$('.clientMarginMarkupButton').removeClass("darkButton");
				$('.taxSection').addClass("hidden");
				$('.candidateSection').addClass("hidden");
				$('.clientSection').removeClass("hidden");
				$('.clientMarginMarkupSection').addClass("hidden");
			});

			$('.clientMarginMarkupButton').click(function(e){
				e.preventDefault();
				$('.taxButton').addClass("smoothButton");
				$('.taxButton').removeClass("darkButton");
				$('.candidateButton').addClass("smoothButton");
				$('.candidateButton').removeClass("darkButton");
				$('.clientButton').addClass("smoothButton");
				$('.clientButton').removeClass("darkButton");
				$('.clientMarginMarkupButton').addClass("darkButton");
				$('.clientMarginMarkupButton').removeClass("smoothButton");
				$('.taxSection').addClass("hidden");
				$('.candidateSection').addClass("hidden");
				$('.clientSection').addClass("hidden");
				$('.clientMarginMarkupSection').removeClass("hidden");
			});

			$(".edit-default-charges").click(function(e){
				$(".edit-default-charges").addClass("hidden");
				$(".save-default-charges").removeClass("hidden");
				$(".readOnlyInput").prop("readOnly", false);
			});
/*
			$(".save-default-charges").click(function(e){
				$(".save-default-charges").addClass("hidden");
				$(".edit-default-charges").removeClass("hidden");
				$(".readOnlyInput").prop("readOnly", true);
				$(".adminFeesPercentageError").addClass("hidden");
				$(".markupPercentageError").addClass("hidden");
				$(".marginPercentageError").addClass("hidden");
			});
*/
		    // Default Charges Changes Must be Float type //
		    $(document).on("keypress", ".adminFeesPercentage, .markupPercentage, .marginPercentage", function(e) {
		        if ($(this).val().length > 5) {
		            return false;
		        }
		        if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
		            return false;
		        }
		    });

			// Bill Rate & Markup Rate Changes Function
			$(document).on("keyup", ".adminFeesPercentage, .markupPercentage, .marginPercentage", function(e) {
				var adminFeesPercentage = $(".adminFeesPercentage").val();
				var markupPercentage = $(".markupPercentage").val();
				var marginPercentage = $(".marginPercentage").val();

				var float = /^\s*(\+|-)?((\d+(\.\d+)?)|(\.\d+))\s*$/;

				if(float.test(adminFeesPercentage) == false) {
					$(".adminFeesPercentageError").removeClass("hidden");
                } else if (adminFeesPercentage < 0 || adminFeesPercentage > 100) {
					$(".adminFeesPercentageError").removeClass("hidden");
                } else {
					$(".adminFeesPercentageError").addClass("hidden");
                }

				if(float.test(markupPercentage) == false) {
					$(".markupPercentageError").removeClass("hidden");
                } else if (markupPercentage < 0 || markupPercentage > 100) {
					$(".markupPercentageError").removeClass("hidden");
                } else {
					$(".markupPercentageError").addClass("hidden");
                }

				if(float.test(marginPercentage) == false) {
					$(".marginPercentageError").removeClass("hidden");
                } else if (marginPercentage < 0 || marginPercentage > 100) {
					$(".marginPercentageError").removeClass("hidden");
                } else {
					$(".marginPercentageError").addClass("hidden");
                }

				if(float.test($(this).val()) == false) {
					$("#errorValue").val("true");
                } else if ($(this).val() < 0 || $(this).val() > 100) {
					$("#errorValue").val("true");
                } else {
					$("#errorValue").val("false");
                }

			});

			$(document).on("submit", ".defaultChargesForm", function(e){
				e.preventDefault();
				$.ajax({
					url: 'saveDefaultCharges.php',
					type: 'POST',
					data: $(".defaultChargesForm").serialize(),
					success: function(response) {
						if ($.trim(response) == "success") {
							$(".save-default-charges").addClass("hidden");
							$(".edit-default-charges").removeClass("hidden");
							$(".readOnlyInput").prop("readOnly", true);
							$(".adminFeesPercentageError").addClass("hidden");
							$(".markupPercentageError").addClass("hidden");
							$(".marginPercentageError").addClass("hidden");
							$(".save-default-charges-error").addClass("hidden");
						}
						if ($.trim(response) == "error") {
							$(".save-default-charges-error").removeClass("hidden");
						}
					}
				});
			});

		});

		$(document).on('click', '.editModalClass', function(e) {
			e.preventDefault();
			var modalData = $(this).data('custom');
			$.ajax({
				url: 'editmodal.php',
				type: 'POST',
				data: modalData,
				success: function(response) {
					$('#modalViewSection').html(response);
					$('#editModal').modal("show");
				}
			});
		});

		$(document).on('keypress', '.checkNumber', function(e) {
			if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
				return false;
			}
		});

		$(document).on('click', '.syncButton', function(e) {
			e.preventDefault();
			$(".loadingImage").show();
			$('.navPanel').addClass("hidden");
			$('.outputSection').addClass("hidden");
			$.ajax({
				url: 'sync.php',
				type: 'post',
				success: function(response) {
					alert($.trim(response));
					location.reload();
				}
			});
		});

		$(document).on('submit', '#editModalForm', function(e) {
			e.preventDefault();
			$.ajax({
				url: 'syncmodal.php',
				type: 'post',
				data: $('#editModalForm').serialize(),
    			dataType: "json",
				success: function(response) {
					if (response['data'] == 'Error') {
						alert("Something Wrong!");
					} else {
						if (response['type'] == 'tax') {
							$('.taxThree'+response['id']).html(response['charge_pct']);
						}
						if (response['type'] == 'candidate') {
							$('.candidateTwo'+response['id']).html(response['c_primeCharge_pct']);
							$('.candidateThree'+response['id']).html(response['c_primeCharge_dlr']);
							$('.candidateFour'+response['id']).html(response['c_anyCharge_dlr']);
						}
						if (response['type'] == 'client') {
							$('.clientTwo'+response['id']).html(response['mspChrg_pct']);
							$('.clientThree'+response['id']).html(response['mspChrg_dlr']);
							$('.clientFour'+response['id']).html(response['primeChrg_pct']);
							$('.clientFive'+response['id']).html(response['primeChrg_dlr']);
						}
						if (response['type'] == 'clientMarkupMargin') {
							$('.clientMarkupMarginTwo'+response['id']).html(response['clientType']);
							$('.clientMarkupMarginThree'+response['id']).html(response['percentageValue']);
						}
						$('#editModal').modal("hide");
					}
				}
			});
		});
	</script>

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
