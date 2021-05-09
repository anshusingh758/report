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
	$getCategoriesQuery = mysqli_query($allConn, "SELECT
	 	al.TAB as category
	FROM
	 	vtech_primary_care_privder_v2.all_details as al
	WHERE
	   al.TAB != ''
	GROUP BY al.TAB");

	if (mysqli_num_rows($getCategoriesQuery) > 0) { 
		while ($getCategoriesRow = mysqli_fetch_array($getCategoriesQuery)) {
			$getAllCategories[] = $getCategoriesRow["category"];
		}
	}

	if (isset($_REQUEST["form-submit-button"])) {
		$category = $_POST['category-by'];
		$status = $_POST['filter-by'];

		$logChangesQuery = mysqli_query($allConn, "SELECT
			ld.reference_id,
		    ld.field_name
		FROM
			vtech_primary_care_privder_v2.log_details AS ld
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
				COUNT(DISTINCT ld.field_name) AS modification,
				cd.date_time as date_time
			FROM
				vtech_primary_care_privder_v2.all_details AS ald
				JOIN vtech_primary_care_privder_v2.contact_details AS cd ON cd.detail_id = ald.id AND cd.signature_image != ''
				LEFT JOIN vtech_primary_care_privder_v2.log_details as ld ON ld.reference_id = ald.id
			WHERE ald.TAB = '$category'
			GROUP BY ald.id";
		} else if ($status == 'Unverified') {
			$mainQuery = "SELECT
				ald.*,
				'Unverified' AS data_type,
				COUNT(DISTINCT ld.field_name) AS modification
			FROM
				vtech_primary_care_privder_v2.all_details as ald
				LEFT JOIN vtech_primary_care_privder_v2.log_details as ld ON ld.reference_id = ald.id
			WHERE
				ald.id NOT IN (SELECT
				  	cd.detail_id
				FROM
					vtech_primary_care_privder_v2.contact_details as cd)
				AND ald.TAB = '$category'
			GROUP BY ald.id";
		} else {
			$mainQuery = "SELECT
				ald.*,
				IF(cd.id != '', 'Verified', 'Unverified') AS data_type,
				COUNT(DISTINCT ld.field_name) AS modification,
				cd.date_time as date_time
			FROM
				vtech_primary_care_privder_v2.all_details as ald
				LEFT JOIN vtech_primary_care_privder_v2.contact_details AS cd ON cd.detail_id = ald.id AND cd.signature_image != ''
				LEFT JOIN vtech_primary_care_privder_v2.log_details as ld ON ld.reference_id = ald.id
				WHERE ald.TAB = '$category'
			GROUP BY ald.id";
		}
		
		$mainResult = mysqli_query($allConn, $mainQuery);

		while ($mainRow = mysqli_fetch_array($mainResult)) {
			$selectRow[] = $mainRow;
		}
	}
	$totalCount = sizeof($selectRow);
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
				<div class="col-md-12 report-title">Data Verification Status Report - V2</div>
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
					<button type="button" onclick="location.href='<?php echo "index.php"; ?>'" class="form-control smooth-button">Go to Data Verification Status Report - V1</button>
				</div>
				<div class="col-md-4">
					<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
				</div>
			</div>
			
			<form action="index_v2.php" method="post">
				<div class="row main-section-row col-md-offset-4">

					<div class="col-md-6">
						<!-- <center><h3>Coming Soon!</h3></center> -->
						<label>Category By :</label>
						<select id="category-by" class="customized-selectbox-without-all" name="category-by">
						<?php
							foreach ($getAllCategories as $categoryByListKey => $categoryByListValue) {
								$isSelected = "";
								if ($categoryByListValue == $_REQUEST["category-by"]) {
									$isSelected = " selected";
								}
								echo "<option value='".$categoryByListValue."'".$isSelected.">".$categoryByListValue."</option>";
							}
						?>
						</select>
					</div>
				</div>
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

								<th>DIRECTORY_CHAPTER</th>
								<th>TAB</th>
								<th>SPECIALITY</th>
								<th>CO_ST</th>
								<th>SPECIALTYID</th>
								<th>SPECIALTYNAME</th>
								<th>PCP_IND</th>
								<th>NPI</th>
								<th>PROVIDERID</th>
								<th>ENTITY</th>
								<th>PRINT_NAME</th>
								<th>PROVIDERGENDER</th>
								<th>GROUPNUMBER</th>
								<th>GROUPNAME</th>
								<th>GROUPTITLE</th>
								<th>ADDRESSLINE1</th>
								<th>ADDRESSLINE2</th>
								<th>CITY</th>
								<th>STATE</th>
								<th>ZIP</th>
								<th>WARD</th>
								<th>PHONE1</th>
								<th>MON</th>
								<th>TUE</th>
								<th>WED</th>
								<th>THU</th>

								<th>FRI</th>
								<th>SAT</th>
								<th>SUN</th>
								<th>LANG1</th>
								<th>LANG2</th>
								<th>LANG3</th>
								<th>LANG4</th>
								
								<th>HOSP1</th>
								<th>HOSP2</th>
								<th>HOSP3</th>
								<th>HOSP4</th>
								<th>HOSP5</th>
								<th>HOSP6</th>
								<th>AGE_LIMIT</th>
								
								<th>ACCEPTINGNEWPATIENTS</th>
								<th>BOARD_CERTIFIED_SPECIALITY</th>
								<th>HOSP_ACCRED_TYPE</th>
								<th>ACCEPTS_MEDICAID</th>
								
								<th>SPECIAL_SKILLS</th>
								<th>CULTRL_COMP_TRNG</th>
								<th>ADA_LOC</th>
								<th>ADA_RESTROOM</th>

								<th>ADAEXAM_ROOM</th>
								<th>ADAMEDICAL_EQUIPMENT</th>
								<th>ADAACCESS_BLIND</th>
								<th>ADAACCESS_COGNITIVE</th>
								
								<th>ADAACCESS_DEAF</th>
								<th>HANDICAPPED</th>
								<th>EMERGENCY_DEPT</th>
								<th>E_RX</th>

								<th>LANG_SKILL</th>
								<th>PUB_TRANSPORT_ROUTES</th>
								<th>PUBTRANSBUS</th>
								<th>PUBTRANSTRAIN</th>

								<th>PUBTRANSBUSTRAIN</th>
								<th>PUBTRANSBOAT</th>
								<th>WEBSITE</th>
								<th>HOME_DELIVERY</th>

								<th>DRIVEUP</th>
								<th>COMPOUNDING</th>

								<th>LAST MODIFIED</th>
							</tr>
						</thead>
						<tbody>
						<?php
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
										$totalVerified[] = count($mainTableDataValue["data_type"]);
										echo $percentageAccuracy[] = round(100 - ((($mainTableDataValue["modification"]) / 66) *100), 2)."%";
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

								<td <?php if (in_array("DIRECTORY_CHAPTER", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DIRECTORY_CHAPTER"];
								?>
								</td>

								<td <?php if (in_array("TAB", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["TAB"];
								?>
								</td>
								<td <?php if (in_array("SPECIALITY", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["SPECIALITY"];
								?>
								</td>
								<td <?php if (in_array("CO_ST", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["CO_ST"];
								?>
								</td>

								<td <?php if (in_array("SPECIALTYID", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["SPECIALTYID"];
								?>
								</td>

								<td <?php if (in_array("SPECIALTYNAME", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["SPECIALTYNAME"];
								?>
								</td>

								<td <?php if (in_array("PCP_IND", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PCP_IND"];
								?>
								</td>

								<td <?php if (in_array("NPI", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["NPI"];
								?>
								</td>

								<td <?php if (in_array("PROVIDERID", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PROVIDERID"];
								?>
								</td>

								<td <?php if (in_array("ENTITY", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ENTITY"];
								?>
								</td>

								<td <?php if (in_array("PRINT_NAME", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PRINT_NAME"];
								?>
								</td>

								<td <?php if (in_array("PROVIDERGENDER", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PROVIDERGENDER"];
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

								<td <?php if (in_array("GROUPTITLE", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["GROUPTITLE"];
								?>
								</td>

								<td <?php if (in_array("ADDRESSLINE1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADDRESSLINE1"];
								?>
								</td>

								<td <?php if (in_array("ADDRESSLINE2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADDRESSLINE2"];
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
								<td <?php if (in_array("WARD", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["WARD"];
								?>
								</td>
								<td <?php if (in_array("MON", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["MON"];
								?>
								</td>
								<td <?php if (in_array("TUE", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["TUE"];
								?>
								</td>
								<td <?php if (in_array("WED", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["WED"];
								?>
								</td>
								<td <?php if (in_array("THU", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["THU"];
								?>
								</td>
								<td <?php if (in_array("FRI", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["FRI"];
								?>
								</td>
								<td <?php if (in_array("SAT", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["SAT"];
								?>
								</td>
								<td <?php if (in_array("SUN", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["SUN"];
								?>
								</td>

								<td <?php if (in_array("LANG1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["LANG1"];
								?>
								</td>
								<td <?php if (in_array("LANG2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["LANG2"];
								?>
								</td>
								<td <?php if (in_array("LANG3", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["LANG3"];
								?>
								</td>
								<td <?php if (in_array("LANG4", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["LANG4"];
								?>
								</td>

								<td <?php if (in_array("HOSP1", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HOSP1"];
								?>
								</td>
								<td <?php if (in_array("HOSP2", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HOSP2"];
								?>
								</td>
								<td <?php if (in_array("HOSP3", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HOSP3"];
								?>
								</td>
								<td <?php if (in_array("HOSP4", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HOSP4"];
								?>
								</td>
								<td <?php if (in_array("HOSP5", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HOSP5"];
								?>
								</td>
								<td <?php if (in_array("HOSP6", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HOSP6"];
								?>
								</td>
								<td <?php if (in_array("AGE_LIMIT", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["AGE_LIMIT"];
								?>
								</td>
								<td <?php if (in_array("ACCEPTINGNEWPATIENTS", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ACCEPTINGNEWPATIENTS"];
								?>
								</td>

								<td <?php if (in_array("BOARD_CERTIFIED_SPECIALITY", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["BOARD_CERTIFIED_SPECIALITY"];
								?>
								</td>

								<td <?php if (in_array("HOSP_ACCRED_TYPE", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HOSP_ACCRED_TYPE"];
								?>
								</td>
								<td <?php if (in_array("ACCEPTS_MEDICAID", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ACCEPTS_MEDICAID"];
								?>
								</td>
								<td <?php if (in_array("SPECIAL_SKILLS", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["SPECIAL_SKILLS"];
								?>
								</td>
								<td <?php if (in_array("CULTRL_COMP_TRNG", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["CULTRL_COMP_TRNG"];
								?>
								</td>

								<td <?php if (in_array("ADA_LOC", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADA_LOC"];
								?>
								</td>

								<td <?php if (in_array("ADA_RESTROOM", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADA_RESTROOM"];
								?>
								</td>

								<td <?php if (in_array("ADAEXAM_ROOM", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADAEXAM_ROOM"];
								?>
								</td>

								<td <?php if (in_array("ADAMEDICAL_EQUIPMENT", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADAMEDICAL_EQUIPMENT"];
								?>
								</td>

								<td <?php if (in_array("ADAACCESS_BLIND", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADAACCESS_BLIND"];
								?>
								</td>

								<td <?php if (in_array("ADAACCESS_COGNITIVE", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADAACCESS_COGNITIVE"];
								?>
								</td>

								<td <?php if (in_array("ADAACCESS_DEAF", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["ADAACCESS_DEAF"];
								?>
								</td>
								<td <?php if (in_array("HANDICAPPED", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HANDICAPPED"];
								?>
								</td>
								<td <?php if (in_array("EMERGENCY_DEPT", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["EMERGENCY_DEPT"];
								?>
								</td>
								<td <?php if (in_array("E_RX", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["E_RX"];
								?>
								</td>
								<td <?php if (in_array("LANG_SKILL", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["LANG_SKILL"];
								?>
								</td>

								<td <?php if (in_array("PUB_TRANSPORT_ROUTES", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PUB_TRANSPORT_ROUTES"];
								?>
								</td>

								<td <?php if (in_array("PUBTRANSBUS", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PUBTRANSBUS"];
								?>
								</td>

								<td <?php if (in_array("PUBTRANSTRAIN", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PUBTRANSTRAIN"];
								?>
								</td>
								<td <?php if (in_array("PUBTRANSBUSTRAIN", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PUBTRANSBUSTRAIN"];
								?>
								</td>

								<td <?php if (in_array("PUBTRANSBOAT", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["PUBTRANSBOAT"];
								?>
								</td>
								<td <?php if (in_array("WEBSITE", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["WEBSITE"];
								?>
								</td>
								<td <?php if (in_array("HOME_DELIVERY", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["HOME_DELIVERY"];
								?>
								</td>
								<td <?php if (in_array("DRIVEUP", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["DRIVEUP"];
								?>
								</td>
								<td <?php if (in_array("COMPOUNDING", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["COMPOUNDING"];
								?>
								</td>
								<td <?php if (in_array("date_time", $logChangesItems[$mainTableDataValue["id"]])) { echo 'style="background-color: green;font-weight: bold;color: #fff;"'; } ?>>
								<?php
									echo $mainTableDataValue["date_time"];
								?>
								</td>
						<?php
						}
						?>
						</tbody>
						<tfoot>
							<tr class="tfoot-tr-style">
								<th></th>
								<th><?php
								if ($status == 'Select All') {
									echo array_sum($totalVerified).' ('. round(array_sum($totalVerified)/$totalCount*100, 2).'%)'; 
								} else {
									echo "-";
								}
								?></th>
								<th>
								<?php
								 	echo round((array_sum($percentageAccuracy) / count($percentageAccuracy)), 2)."%";
								 ?>
								 </th>
							<?php
								for ($i=0; $i < 67; $i++) { 
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
