<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../security.php");
	include_once("../../../functions/reporting-service.php");
    if (isset($user) && isset($userMember)) {
		$reportId = "46";
		$sessionQUERY = findSessionItem($misReportsConn,$user,$reportId);
		if (mysqli_num_rows($sessionQUERY) > 0) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>Monthly GP Report</title>

	<?php include_once("../../../cdn.php"); ?>

	<style>
		table.dataTable thead th,
		table.dataTable tfoot th {
			padding: 5px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable tbody td {
			padding: 3px 0px;
			text-align: center;
			vertical-align: middle;
		}
		table.dataTable thead tr:nth-child(2) th:last-child {
			border-right: 1px solid #ddd;
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
		.report-bottom-style {
			margin-top: 50px;
			margin-bottom: 20px;
		}
		.thead-tr-style th {
			background-color: #ccc;
			color: #000;
			font-size: 13px;
		}
		.tbody-tr-style td {
			color: #333;
			font-size: 13px;
		}
		.tfoot-tr-style th {
			background-color: #ccc;
			color: #000;
			font-size: 14px;
		}
	</style>
</head>
<body>

<?php
	$finalArray = $finalNewEmployee = $finalLeftEmployee = $finalGP = $finalRevenue = array();

	$lastMonth = date("m", strtotime("last day of last month"));

	for ($i=1; $i <= $lastMonth; $i++) {

		$totalNewEmployee = $totalLeftEmployee = $totalMarginArray = $totalGPArray = $totalRevenueArray = array();

		$loopMonth = sprintf("%02d", $i);
		
		$lastYear = date("Y", strtotime("last year"));
		
		$thisYear = date("Y");
		
		if ($lastMonth == "12") {
			$dateModified = $lastYear."-".$loopMonth;
		} else {
			$dateModified = $thisYear."-".$loopMonth;
		}

		$fromDate = date("Y-m-01", strtotime($dateModified));
		$toDate = date("Y-m-t", strtotime($dateModified));
		
		if ($loopMonth == $lastMonth) {
			//find current data

			if ($loopMonth == "01") {
				$firstMonthQUERY = mysqli_query($allConn, "SELECT
					gd.total_active
				FROM
					mis_reports.gp_data AS gd
				WHERE
					gd.from_date = '$lastYear-12-01'
				AND
					gd.to_date = '$lastYear-12-31'");
				
				$firstMonthROW = mysqli_fetch_array($firstMonthQUERY);

				$activeEmployee = $firstMonthROW["total_active"];
			}

			$newempQUERY = mysqli_query($allConn, "SELECT
				COUNT(DISTINCT e.id) AS total_new
			FROM
				vtechhrm.employees AS e
			WHERE
				e.status != 'Internal Employee'
			AND
				e.custom7 BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'");

			$newempROW = mysqli_fetch_array($newempQUERY);

			$leftempQUERY = mysqli_query($allConn, "SELECT
				COUNT(DISTINCT e.id) AS total_left
			FROM
				vtechhrm.employees as e
				LEFT JOIN vtechhrm.employeeprojects AS ep ON e.id = ep.employee
			WHERE
				ep.project != '6'
			AND
				(e.status = 'Terminated' OR e.status = 'Termination In_Vol' OR e.status = 'Termination Vol')
			AND
				e.termination_date BETWEEN '$fromDate 00:00:00' AND '$toDate 23:59:59'");

			$leftempROW = mysqli_fetch_array($leftempQUERY);
			
			$newEmployee = $newempROW["total_new"];
			$leftEmployee = $leftempROW["total_left"];

			$totalNewEmployee[] = $newEmployee;
			$totalLeftEmployee[] = $leftEmployee;

			$openingBalance = $activeEmployee;
			$closingBalance = $activeEmployee + $newEmployee - $leftEmployee;

			$taxSettingsTableData = taxSettingsTable($allConn);
			$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$fromDate,$toDate);

			$mainQUERY = "SELECT
				e.id AS employee_id,
				e.status AS employee_status,
				e.custom1 AS benefit,
				e.custom2 AS benefit_list,
				CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
				CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
				es.id AS employment_id,
				clf.mspChrg_pct AS client_msp_charge_percentage,
				clf.primechrg_pct AS client_prime_charge_percentage,
				clf.primeChrg_dlr AS client_prime_charge_dollar,
				clf.mspChrg_dlr AS client_msp_charge_dollar,
				cnf.c_primeCharge_pct AS employee_prime_charge_percentage,
				cnf.c_primeCharge_dlr AS employee_prime_charge_dollar,
				cnf.c_anyCharge_dlr AS employee_any_charge_dollar
			FROM
				vtechhrm.employees AS e
				LEFT JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
				LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
				LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
			    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
				LEFT JOIN cats.company AS comp ON si.c_company_id = comp.company_id
				LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
				LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
			WHERE
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
			GROUP BY employee_id";

			$mainRESULT = mysqli_query($allConn, $mainQUERY);
			if (mysqli_num_rows($mainRESULT) > 0) {
				while ($mainROW = mysqli_fetch_array($mainRESULT)) {

					$delimiter = array("","[","]",'"');
					
					$benefitList = str_replace($delimiter, $delimiter[0], $mainROW["benefit_list"]);

					//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$mainROW["benefit"],$benefitList,$mainROW["employment_id"],$mainROW["pay_rate"]), 2);

					$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$mainROW["benefit"],$benefitList,$mainROW["employment_id"],$mainROW["pay_rate"]), 2);

					$mspFees = round((($mainROW["client_msp_charge_percentage"] / 100) * $mainROW["bill_rate"]) + $mainROW["client_msp_charge_dollar"], 2);

					$primeCharges = round(((($mainROW["client_prime_charge_percentage"] / 100) * $mainROW["bill_rate"]) + (($mainROW["employee_prime_charge_percentage"] / 100) * $mainROW["bill_rate"]) + $mainROW["employee_prime_charge_dollar"] + $mainROW["employee_any_charge_dollar"] + $mainROW["client_prime_charge_dollar"]), 2);

					$candidateRate = round(($mainROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

					$grossMargin = round(($mainROW["bill_rate"] - $candidateRate), 2);

					//$totalHour = round(employeeWorkingHours($vtechhrmConn,$fromDate,$toDate,$mainROW["employee_id"]), 2);
					
					$totalHour = round(array_sum($employeeTimeEntryTableData[$mainROW["employee_id"]]), 2);

					$totalGP = round(($grossMargin * $totalHour), 2);
					
					$totalRevenue = round(($mainROW["bill_rate"] * $totalHour), 2);

					if ($totalHour > "0") {
						$totalMarginArray[] = $grossMargin;
						$totalGPArray[] = $totalGP;
						$totalRevenueArray[] = $totalRevenue;
					}
				}
			}

		} else {
			//find log data

			$findLockDataQUERY = mysqli_query($allConn, "SELECT
				gd.id,
				gd.total_new,
				gd.total_left,
				gd.total_active,
				gd.total_data
			FROM
				mis_reports.gp_data AS gd
			WHERE
				gd.from_date = '$fromDate'
			AND
				gd.to_date = '$toDate'
			GROUP BY gd.id");

			$findLockDataROW = mysqli_fetch_array($findLockDataQUERY);

			$activeEmployee = $findLockDataROW["total_active"];
			$newEmployee = $findLockDataROW["total_new"];
			$leftEmployee = $findLockDataROW["total_left"];
			
			$totalNewEmployee[] = $newEmployee;
			$totalLeftEmployee[] = $leftEmployee;
			
			$openingBalance = $activeEmployee + $leftEmployee - $newEmployee;
			$closingBalance = $activeEmployee;

			$dataObject = json_decode($findLockDataROW["total_data"], true);

			$totalMarginArray[] = $dataObject[0]["total_gp_per_hour"];
			$totalGPArray[] = $dataObject[0]["final_gp"];
			$totalRevenueArray[] = $dataObject[0]["final_revenue"];
		}

		$attrition = round(($leftEmployee / (($openingBalance + $newEmployee) / 2)) * 100, 2)."%";

		$finalArray[] = array(
			"sr_no" => $loopMonth,
			"month" => date("F", mktime(0, 0, 0, $loopMonth, 10)),
			"opening_balance" => $openingBalance,
			"new_employee" => $newEmployee,
			"left_employee" => $leftEmployee,
			"closing_balance" => $closingBalance,
			"total_margin" => array_sum($totalMarginArray),
			"total_gp" => number_format(array_sum($totalGPArray), 2),
			"total_revenue" => number_format(array_sum($totalRevenueArray), 2),
			"attrition" => $attrition
		);

		$finalNewEmployee[] = array_sum($totalNewEmployee);
		
		$finalLeftEmployee[] = array_sum($totalLeftEmployee);
		
		$finalGP[] = array_sum($totalGPArray);
		
		$finalRevenue[] = array_sum($totalRevenueArray);
	}
?>

	<section>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 report-title">MD - Monthly GP Report - <?php if (date("m", strtotime("last day of last month")) == "12") { echo date("Y", strtotime("last year")); } else { echo date("Y"); } ?></div>
				<div class="col-md-12 loading-image">
					<img src="<?php echo IMAGE_PATH; ?>/plw.gif" class="loading-image-style">
				</div>
			</div>
		</div>
	</section>

	<section class="main-section hidden">
		<div class="container">
			<div class="row">
				<div class="col-md-2">
					<button type="button" onclick="location.href='<?php echo BACK_TO_HOME; ?>'" class="dark-button form-control"><i class="fa fa-arrow-left"></i> Back to Home</button>
				</div>
				<div class="col-md-7"></div>
				<div class="col-md-3">
					<button type="button" onclick="location.href='<?php echo DIR_PATH; ?>logout.php'" class="logout-button"><i class="fa fa-fw fa-power-off"></i> Logout</button>
				</div>
			</div>

			<div class="row report-bottom-style">
				<div class="col-md-12">
					<table class="table table-striped table-bordered customized-datatable">
						<thead>
							<tr class="thead-tr-style">
								<th rowspan="2">Sr.<br>No.</th>
								<th rowspan="2">Month</th>
								<th rowspan="2">Opening<br>Balance</th>
								<th colspan="2">Employees</th>
								<th rowspan="2">Closing<br>Balance</th>
								<th rowspan="2">GP / Hour</th>
								<th rowspan="2">Total<br>GP</th>
								<th rowspan="2">Total<br>Revenue</th>
								<th rowspan="2"  data-toggle="tooltip" data-placement="top" title="(Left Employees / ((Opening Balance + New Employees) / 2) * 100)">Attrition %</th>
							</tr>
							<tr class="thead-tr-style">
								<th>New</th>
								<th>Left</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($finalArray as $finalArrayKey => $finalArrayValue) {
							?>
							<tr class="tbody-tr-style">
								<td><?php echo $finalArrayValue["sr_no"]; ?></td>
								<td><?php echo $finalArrayValue["month"]; ?></td>
								<td><?php echo $finalArrayValue["opening_balance"]; ?></td>
								<td><?php echo $finalArrayValue["new_employee"]; ?></td>
								<td><?php echo $finalArrayValue["left_employee"]; ?></td>
								<td><?php echo $finalArrayValue["closing_balance"]; ?></td>
								<td><?php echo $finalArrayValue["total_margin"]; ?></td>
								<td><?php echo $finalArrayValue["total_gp"]; ?></td>
								<td><?php echo $finalArrayValue["total_revenue"]; ?></td>
								<td><?php echo $finalArrayValue["attrition"]; ?></td>
							</tr>
							<?php
								}
							?>
						</tbody>
						<tfoot>
							<tr class="tfoot-tr-style">
								<th colspan="3"></th>
								<th><?php echo array_sum($finalNewEmployee); ?></th>
								<th><?php echo array_sum($finalLeftEmployee); ?></th>
								<th colspan="2"></th>
								<th><?php echo number_format(array_sum($finalGP), 2); ?></th>
								<th><?php echo number_format(array_sum($finalRevenue), 2); ?></th>
								<th></th>
							</tr>
						</tfoot>
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
			"paging": false,
			"searching":false
		});
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
