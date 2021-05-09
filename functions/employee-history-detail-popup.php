<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");

	if ($_POST) {
		$employeeId = $_POST["employeeId"];
		$employeeName = $_POST["employeeName"];
		
		if ($_POST["startDate"] && $_POST["endDate"]) {
			$startDate = $_POST["startDate"];
			$endDate = $_POST["endDate"];
		}

		$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
						<thead>
							<tr class='thead-tr-style'>
								<th colspan='11'>".$employeeName."</th>
							</tr>
							<tr class='thead-tr-style'>
								<th>Date</th>
								<th>Status</th>
								<th>Client</th>
								<th>Client Manager</th>
								<th>Bill Rate</th>
								<th>Pay Rate</th>
								<th>Tax</th>
								<th>MSP Fees</th>
								<th>Vendor Fees</th>
								<th>Candidate Rate</th>
								<th data-toggle='tooltip' data-placement='top' title='Bill Rate - Candidate Rate'>Margin</th>
							</tr>
						</thead>
						<tbody>";

		$marginQUERY = "SELECT
			hel.employee_status,
			hel.company_id,
			hel.company_name,
			hel.company_manager_id,
			hel.company_manager_name,
			hel.bill_rate,
			hel.pay_rate,
			hel.tax_rate,
			hel.msp_fees,
			hel.prime_charges,
			hel.candidate_rate,
			hel.margin,
			hel.created_at
		FROM
			vtech_mappingdb.hrm_employee_log AS hel
		WHERE
			hel.employee_id = '$employeeId'";

		if ($_POST["startDate"] && $_POST["endDate"]) {
			$marginQUERY .= " AND
				DATE_FORMAT(hel.created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY hel.id";
		} else {
			$marginQUERY .= " GROUP BY hel.id";
		}

		$marginRESULT = mysqli_query($allConn, $marginQUERY);
		
		$employeeStatus = $companyName = $companyManager = $billRate = $payRate = $taxRate = $mspFees = $primeCharges = $candidateRate = $margin = "";

		if (mysqli_num_rows($marginRESULT) > 0) {
			while ($marginROW = mysqli_fetch_array($marginRESULT)) {
				
				if (isset($changeData) && $changeData["employee_status"] != $marginROW["employee_status"]) {
					$employeeStatus = "<b style='color: green;'>".$marginROW["employee_status"]."</b>";
				} else {
					$employeeStatus = $marginROW["employee_status"];
				}

				if (isset($changeData) && $changeData["company_id"] != $marginROW["company_id"]) {
					$companyName = "<b style='color: green;'>".$marginROW["company_name"]."</b>";
				} else {
					$companyName = $marginROW["company_name"];
				}

				if (isset($changeData) && $changeData["company_manager_id"] != $marginROW["company_manager_id"]) {
					$companyManager = "<b style='color: green;'>".$marginROW["company_manager_name"]."</b>";
				} else {
					$companyManager = $marginROW["company_manager_name"];
				}

				if (isset($changeData) && $changeData["bill_rate"] != $marginROW["bill_rate"]) {
					$billRate = "<b style='color: green;'>".$marginROW["bill_rate"]."</b>";
				} else {
					$billRate = $marginROW["bill_rate"];
				}

				if (isset($changeData) && $changeData["pay_rate"] != $marginROW["pay_rate"]) {
					$payRate = "<b style='color: green;'>".$marginROW["pay_rate"]."</b>";
				} else {
					$payRate = $marginROW["pay_rate"];
				}

				if (isset($changeData) && $changeData["tax_rate"] != $marginROW["tax_rate"]) {
					$taxRate = "<b style='color: green;'>".$marginROW["tax_rate"]."</b>";
				} else {
					$taxRate = $marginROW["tax_rate"];
				}

				if (isset($changeData) && $changeData["msp_fees"] != $marginROW["msp_fees"]) {
					$mspFees = "<b style='color: green;'>".$marginROW["msp_fees"]."</b>";
				} else {
					$mspFees = $marginROW["msp_fees"];
				}

				if (isset($changeData) && $changeData["prime_charges"] != $marginROW["prime_charges"]) {
					$primeCharges = "<b style='color: green;'>".$marginROW["prime_charges"]."</b>";
				} else {
					$primeCharges = $marginROW["prime_charges"];
				}

				if (isset($changeData) && $changeData["candidate_rate"] != $marginROW["candidate_rate"]) {
					$candidateRate = "<b style='color: green;'>".$marginROW["candidate_rate"]."</b>";
				} else {
					$candidateRate = $marginROW["candidate_rate"];
				}

				if (isset($changeData) && $changeData["margin"] != $marginROW["margin"]) {
					$margin = "<b style='color: green;'>".$marginROW["margin"]."</b>";
				} else {
					$margin = $marginROW["margin"];
				}

				$output .= "<tr class='tbody-tr-style'>
					<td nowrap>".date('m-d-Y', strtotime($marginROW["created_at"]))."</td>
					<td>".$employeeStatus."</td>
					<td>".$companyName."</td>
					<td>".$companyManager."</td>
					<td>".$billRate."</td>
					<td>".$payRate."</td>
					<td>".$taxRate."</td>
					<td>".$mspFees."</td>
					<td>".$primeCharges."</td>
					<td>".$candidateRate."</td>
					<td>".$margin."</td>
				</tr>";

				$changeData = array();
				$changeData = array("employee_status" => $marginROW["employee_status"],
					"company_id" => $marginROW["company_id"],
					"company_manager_id" => $marginROW["company_manager_id"],
					"bill_rate" => $marginROW["bill_rate"],
					"pay_rate" => $marginROW["pay_rate"],
					"tax_rate" => $marginROW["tax_rate"],
					"msp_fees" => $marginROW["msp_fees"],
					"prime_charges" => $marginROW["prime_charges"],
					"candidate_rate" => $marginROW["candidate_rate"],
					"margin" => $marginROW["margin"]
				);
			}
		}

		$output .= "</tbody>
					</table>";
	
		$output .= "<br>
			<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
						<thead>
							<tr class='thead-tr-style'>
								<th colspan='8'>Personnel History by Client</th>
							</tr>
							<tr class='thead-tr-style'>
								<th>Date</th>
								<th>Client</th>
								<th>Inside Sales1</th>
								<th>Inside Sales2</th>
								<th>Research By</th>
								<th>Inside Post Sales</th>
								<th>Onsite Sales</th>
								<th>Onsite Post Sales</th>
							</tr>
						</thead>
						<tbody>";

		$personnelQUERY = "SELECT
			hel.company_id,
		    hel.company_name,
			MAX(CASE WHEN cpl.personnel_type = 'Inside Sales Person1' THEN personnel END) AS inside_sales1,
			MAX(CASE WHEN cpl.personnel_type = 'Inside Sales Person2' THEN personnel END) AS inside_sales2,
			MAX(CASE WHEN cpl.personnel_type = 'Research By' THEN personnel END) AS research_by,
			MAX(CASE WHEN cpl.personnel_type = 'Inside Post Sales' THEN personnel END) AS inside_post_sales,
			MAX(CASE WHEN cpl.personnel_type = 'OnSite Sales Person' THEN personnel END) AS onsite_sales,
			MAX(CASE WHEN cpl.personnel_type = 'OnSite Post Sales' THEN personnel END) AS onsite_post_sales,
		    cpl.created_at
		FROM
			vtech_mappingdb.hrm_employee_log AS hel
		    JOIN vtech_mappingdb.cats_personnel_log AS cpl ON cpl.company_id = hel.company_id
		WHERE
			hel.employee_id = '$employeeId'";

		if ($_POST["startDate"] && $_POST["endDate"]) {
			$personnelQUERY .= " AND
				DATE_FORMAT(cpl.created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY DATE_FORMAT(cpl.created_at, '%Y-%m-%d')";
		} else {
			$personnelQUERY .= " GROUP BY DATE_FORMAT(cpl.created_at, '%Y-%m-%d')";
		}

		$personnelRESULT = mysqli_query($allConn, $personnelQUERY);
		
		if (mysqli_num_rows($personnelRESULT) > 0) {
			while ($personnelROW = mysqli_fetch_array($personnelRESULT)) {
				
				$output .= "<tr class='tbody-tr-style'>
					<td nowrap>".date('m-d-Y', strtotime($personnelROW["created_at"]))."</td>
					<td>".$personnelROW["company_name"]."</td>
					<td>".$personnelROW["inside_sales1"]."</td>
					<td>".$personnelROW["inside_sales2"]."</td>
					<td>".$personnelROW["research_by"]."</td>
					<td>".$personnelROW["inside_post_sales"]."</td>
					<td>".$personnelROW["onsite_sales"]."</td>
					<td>".$personnelROW["onsite_post_sales"]."</td>
				</tr>";

			}
		}

		$output .= "</tbody>
					</table>";
	
		echo $output;
	}
?>