<?php
	error_reporting(0);
	header("Content-Type: text/html; charset=ISO-8859-1");
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "74";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<?php
	if (isset($_REQUEST["form-submit-button"])) {
		$status = $_POST['filter-by'];
		
		$logChangesQuery = mysqli_query($allConn, "SELECT
			ld.reference_id,
		    ld.field_name
		FROM
			vtech_primary_care_privder_v1.log_details AS ld
		GROUP BY ld.field_name,ld.reference_id");

		if (mysqli_num_rows($logChangesQuery) > 0) {
			while ($logChangesRow = mysqli_fetch_array($logChangesQuery)) {
				$logChangesItems[$logChangesRow["reference_id"]][] = $logChangesRow["field_name"];
			}
		}

		if ($status == 'Verified') {
			$mainQuery = "SELECT
				ald.*,
				IF(cd.id != '', 'Verified', 'Unverified') AS data_type,
				COUNT(DISTINCT ld.field_name) AS modification
			FROM
				vtech_primary_care_privder_v1.all_details AS ald
				JOIN vtech_primary_care_privder_v1.contact_details AS cd ON cd.detail_id = ald.id AND cd.signature_image != ''
				LEFT JOIN vtech_primary_care_privder_v1.log_details as ld ON ld.reference_id = ald.id
			GROUP BY ald.id";
		} else if ($status == 'Unverified') {
			$mainQuery = "SELECT
				ald.*,
				'Unverified' AS data_type,
				COUNT(DISTINCT ld.field_name) AS modification
			FROM
				vtech_primary_care_privder_v1.all_details as ald
				LEFT JOIN vtech_primary_care_privder_v1.log_details as ld ON ld.reference_id = ald.id
			WHERE
				ald.id NOT IN (SELECT
				  	cd.detail_id
				FROM
					vtech_primary_care_privder_v1.contact_details as cd)
			GROUP BY ald.id";
		} else {
			$mainQuery = "SELECT
				ald.*,
				IF(cd.id != '', 'Verified', 'Unverified') AS data_type,
				COUNT(DISTINCT ld.field_name) AS modification
			FROM
				vtech_primary_care_privder_v1.all_details as ald
				LEFT JOIN vtech_primary_care_privder_v1.contact_details AS cd ON cd.detail_id = ald.id AND cd.signature_image != ''
				LEFT JOIN vtech_primary_care_privder_v1.log_details as ld ON ld.reference_id = ald.id
			GROUP BY ald.id";
		}
		
		$mainResult = mysqli_query($allConn, $mainQuery);

		while ($mainRow = mysqli_fetch_array($mainResult)) {
			$selectRow[] = $mainRow;
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Data Verification Status Report - V1</title>

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
			margin-bottom: 80px;
		}
		.main-section-row {
			margin-top: 15px;
		}
		.main-section-submit-row {
			margin-top: 30px;
		}
		.input-group-addon {
			background-color: #2266AA;
			border-color: #2266AA;
			color: #fff;
		}
		.form-submit-button {
			background-color: #449D44;
			border-radius: 0px;
			border: #449D44;
			outline: none;
			color: #fff;
		}
		.report-bottom-style {
			margin-bottom: 50px;
		}
		.thead-tr-style {
			background-color: #ccc;
			color: #000;
			font-size: 12px;
		}
		.tbody-tr-style {
			color: #333;
			font-size: 13px;
		}
		.tfoot-tr-style {
			background-color: #ccc;
			color: #000;
			font-size: 14px;
		}
	</style>
</head>
<body>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">Data Verification Status Report - V1</div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					<button type="button" onclick="location.href='<?php echo "index_v2.php"; ?>'" class="form-control smooth-button">Go to Data Verification Status Report - V2</button>
				</div>
				<div class="col-md-4">
					<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
				</div>
			</div>
			
			<form action="index.php" method="post">
				<div class="row main-section-row col-md-offset-4">
					<div class="col-md-6">
						<label>Filter By :</label>
						<?php
							$filterByList = array(
								"Select All" => "All",
								"Verified" => "Verified",
								"Unverified" => "Unverified"
							);
						?>
						<select id="filter-by" class="customized-selectbox-without-all" name="filter-by">
						<?php
							foreach ($filterByList as $filterByListKey => $filterByListValue) {
								$isSelected = "";
								if ($filterByListKey == $_REQUEST["filter-by"]) {
									$isSelected = " selected";
								}
								echo "<option value='".$filterByListKey."'".$isSelected.">".$filterByListValue."</option>";
							}
						?>
						</select>
					</div>
				</div>
				<div class="row main-section-submit-row">
					<div class="col-md-2 col-md-offset-4">
						<button type="button" onclick="location.href='<?php echo "index.php";; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back to Home</button>
					</div>
					<div class="col-md-2">
						<button type="submit" name="form-submit-button" class="form-control form-submit-button"><i class="fa fa-search"></i> Search</button>
					</div>
				</div>
			</form>
		</div>
	</section>

<?php
	if (isset($_REQUEST["form-submit-button"])) {
?>
	<section class="customized-datatable-section hidden">
		<div class="container-fluid">
			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th>No.</th>
								<th>STATUS</th>
								<th>ACCURACY</th>
								<th>TOTAL CHANGES</th>
								<th>GROUPNUMBER</th>
								<th>GROUPNAME</th>
								<th>FULLNAME</th>
								<th>NPI</th>
								<th>TAX_ID</th>
								<th>TITLE</th>
								<th>ADDRESSLINE1</th>
								<th>CITY</th>
								<th>STATE</th>
								<th>ZIP</th>
								<th>WARD</th>
								<th>PROVIDERADDRESSID</th>
								<th>PROVIDERID</th>
								<th>PROVIDERADDRESSTYPE</th>
								<th>PHONE1</th>
								<th>PHONE2</th>
								<th>PRIMARYCAREPROVIDER</th>
								<th>ACCEPTINGNEWPATIENTS</th>
								<th>AGEMIN</th>
								<th>AGEMAX</th>
								<th>PATIENTGENDER</th>
								<th>PROVIDERGENDER</th>
								<th>SPTY_1</th>

								<th>SPTY_2</th>
								<th>CULTRL_COMP_TRNG</th>
								<th>ADA1_VALUE</th>
								<th>LANGUAGES</th>
								<th>BOARD_CERTS</th>
								<th>HOSP_ASSOC</th>
								
								<th>DAY1START1</th>
								<th>DAY1END1</th>
								<th>DAY1START2</th>
								<th>DAY1END2</th>
								
								<th>DAY2START1</th>
								<th>DAY2END1</th>
								<th>DAY2START2</th>
								<th>DAY2END2</th>
								
								<th>DAY3START1</th>
								<th>DAY3END1</th>
								<th>DAY3START2</th>
								<th>DAY3END2</th>

								<th>DAY4START1</th>
								<th>DAY4END1</th>
								<th>DAY4START2</th>
								<th>DAY4END2</th>
								
								<th>DAY5START1</th>
								<th>DAY5END1</th>
								<th>DAY5START2</th>
								<th>DAY5END2</th>

								<th>DAY6START1</th>
								<th>DAY6END1</th>
								<th>DAY6START2</th>
								<th>DAY6END2</th>

								<th>DAY7START1</th>
								<th>DAY7END1</th>
								<th>DAY7START2</th>
								<th>DAY7END2</th>

								<th>last Modified</th>
							</tr>
						</thead>
						<tbody>
						<?php
							$serial_no = 1;

							foreach (array_filter($selectRow) as $mainTableDataKey => $mainTableDataValue) {
						?>
							<tr class="tbody-tr-style">
								<td>
								<?php
								 	echo $mainTableDataValue["id"];
								?>
								</td>
								<td <?php echo $mainTableDataValue["data_type"] == 'Verified' ? 'style="background-color: green;font-weight: bold;color: #fff;"' : '' ?>>
								<?php
									echo $mainTableDataValue["data_type"];
								?>
								</td>
								<td>
								<?php
									if ($mainTableDataValue["data_type"] == "Verified") {
										echo $percentageAccuracy[] = round(100 - ((($mainTableDataValue["modification"]) / 57) *100), 2)."%";
									} else {
										echo "---";
									}
								?>
								</td>
								<td>
								<?php
									echo $mainTableDataValue["modification"];
								?>
								</td>
								<td <?php if (in_array("GROUPNUMBER", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["GROUPNUMBER"];
								?>
								</td>
								<td <?php if (in_array("GROUPNAME", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["GROUPNAME"];
								?>
								</td>
								<td <?php if (in_array("FULLNAME", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["FULLNAME"];
								?>
								</td>
								<td <?php if (in_array("NPI", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["NPI"];
								?>
								</td>
								<td <?php if (in_array("TAX_ID", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["TAX_ID"];
								?>
								</td>
								<td <?php if (in_array("TITLE", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["TITLE"];
								?>
								</td>
								<td <?php if (in_array("ADDRESSLINE1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADDRESSLINE1"];
								?>
								</td>
								<td <?php if (in_array("CITY", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["CITY"];
								?>
								</td>
								<td <?php if (in_array("STATE", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["STATE"];
								?>
								</td>
								<td <?php if (in_array("ZIP", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ZIP"];
								?>
								</td>
								<td <?php if (in_array("WARD", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["WARD"];
								?>
								</td>
								<td <?php if (in_array("PROVIDERADDRESSID", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PROVIDERADDRESSID"];
								?>
								</td>
								<td <?php if (in_array("PROVIDERID", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PROVIDERID"];
								?>
								</td>
								<td <?php if (in_array("PROVIDERADDRESSTYPE", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PROVIDERADDRESSTYPE"];
								?>
								</td>
								<td <?php if (in_array("PHONE1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PHONE1"];
								?>
								</td>
								<td <?php if (in_array("PHONE2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PHONE2"];
								?>
								</td>
								<td <?php if (in_array("PRIMARYCAREPROVIDER", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PRIMARYCAREPROVIDER"];
								?>
								</td>
								<td <?php if (in_array("ACCEPTINGNEWPATIENTS", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ACCEPTINGNEWPATIENTS"];
								?>
								</td>
								<td <?php if (in_array("AGEMIN", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["AGEMIN"];
								?>
								</td>
								<td <?php if (in_array("AGEMAX", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["AGEMAX"];
								?>
								</td>
								<td <?php if (in_array("PATIENTGENDER", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PATIENTGENDER"];
								?>
								</td>
								<td <?php if (in_array("PROVIDERGENDER", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PROVIDERGENDER"];
								?>
								</td>
								<td <?php if (in_array("SPTY_1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["SPTY_1"];
								?>
								</td>
								<td <?php if (in_array("SPTY_2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["SPTY_2"];
								?>
								</td>
								<td <?php if (in_array("CULTRL_COMP_TRNG", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["CULTRL_COMP_TRNG"];
								?>
								</td>
								<td <?php if (in_array("ADA1_VALUE", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADA1_VALUE"];
								?>
								</td>
								<td <?php if (in_array("LANGUAGES", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["LANGUAGES"];
								?>
								</td>
								<td <?php if (in_array("BOARD_CERTS", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["BOARD_CERTS"];
								?>
								</td>
								<td <?php if (in_array("HOSP_ASSOC", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HOSP_ASSOC"];
								?>
								</td>

								<!--DAY1-->
								<td <?php if (in_array("DAY1START1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY1START1"];
								?>
								</td>
								<td <?php if (in_array("DAY1END1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY1END1"];
								?>
								</td>
								<td <?php if (in_array("DAY1START2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY1START2"];
								?>
								</td>
								<td <?php if (in_array("DAY1END2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY1END2"];
								?>
								</td>

								<!--DAY2-->
								<td <?php if (in_array("DAY2START1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY2START1"];
								?>
								</td>
								<td <?php if (in_array("DAY2END1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY2END1"];
								?>
								</td>
								<td <?php if (in_array("DAY2START2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY2START2"];
								?>
								</td>
								<td <?php if (in_array("DAY2END2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY2END2"];
								?>
								</td>

								<!--DAY3-->
								<td <?php if (in_array("DAY3START1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY3START1"];
								?>
								</td>
								<td <?php if (in_array("DAY3END1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY3END1"];
								?>
								</td>
								<td <?php if (in_array("DAY3START2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY3START2"];
								?>
								</td>
								<td <?php if (in_array("DAY3END2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY3END2"];
								?>
								</td>

								<!--DAY4-->
								<td <?php if (in_array("DAY4START1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY4START1"];
								?>
								</td>
								<td <?php if (in_array("DAY4END1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY4END1"];
								?>
								</td>
								<td <?php if (in_array("DAY4START2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY4START2"];
								?>
								</td>
								<td <?php if (in_array("DAY4END2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY4END2"];
								?>
								</td>

								<!--DAY5-->
								<td <?php if (in_array("DAY5START1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY5START1"];
								?>
								</td>
								<td <?php if (in_array("DAY5END1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY5END1"];
								?>
								</td>
								<td <?php if (in_array("DAY5START2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY5START2"];
								?>
								</td>
								<td <?php if (in_array("DAY5END2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY5END2"];
								?>
								</td>

								<!--DAY6-->
								<td <?php if (in_array("DAY6START1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY6START1"];
								?>
								</td>
								<td <?php if (in_array("DAY6END1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY6END1"];
								?>
								</td>
								<td <?php if (in_array("DAY6START2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY6START2"];
								?>
								</td>
								<td <?php if (in_array("DAY6END2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY6END2"];
								?>
								</td>

								<!--DAY7-->
								<td <?php if (in_array("DAY7START1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY7START1"];
								?>
								</td>
								<td <?php if (in_array("DAY7END1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY7END1"];
								?>
								</td>
								<td <?php if (in_array("DAY7START2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY7START2"];
								?>
								</td>
								<td <?php if (in_array("DAY7END2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DAY7END2"];
								?>
								</td>

								<td <?php if (in_array("date_time", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["date_time"];
								?>
								</td>

							</tr>
							<?php
							}
							?>
						</tbody>
						<tfoot>
							<tr class="tfoot-tr-style">
								<th></th>
								<th></th>
								<th>
								<?php
								 	echo round((array_sum($percentageAccuracy) / count($percentageAccuracy)), 2)."%";
								 ?>
								 </th>
							<?php
								for ($i=0; $i < 58; $i++) { 
							?>
								<th></th>
							<?php
								}
							?>
								<th></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</section>
<?php
	}
?>

</body>

<script>
	$(document).ready(function(){
		$(".loading-image").hide();
		$(".main-section").removeClass("hidden");
		$(".customized-datatable-section").removeClass("hidden");

		$(".customized-selectbox-without-all").multiselect({
	        nonSelectedText: "Select Option",
	        numberDisplayed: 1,
	        enableFiltering: true,
	        enableCaseInsensitiveFiltering: true,
	        buttonWidth: "100%",
	        includeSelectAllOption: true,
	        maxHeight: 200
	    });

		var customizedDataTable = $(".customized-datatable").DataTable({
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		    dom: "Bfrtip",
		    "aaSorting": [[0,"asc"]],
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
