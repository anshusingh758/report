<?php
	error_reporting(0);
	include_once("../../../security.php");
	include_once("../../../config.php");

	$crudQUERY = array();

	/////// Sync Candidate ///////
	$candidateSyncQUERY = mysqli_query($vtechhrmConn, "SELECT
		e.id AS employee_id,
		CONCAT(e.first_name,' ',e.last_name) AS employee_name,
	    si.c_company_id AS company_id
	FROM
		vtechhrm.employees AS e
	    LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
	WHERE
		e.id NOT IN (SELECT cnf.emp_id FROM vtech_mappingdb.candidate_fees AS cnf)
	GROUP BY e.id");
	
	while ($candidateSyncROW =mysqli_fetch_array($candidateSyncQUERY)) {
		$employeeId = mysqli_real_escape_string($allConn, $candidateSyncROW["employee_id"]);
		$employeeName = mysqli_real_escape_string($allConn, $candidateSyncROW["employee_name"]);
		$companyId = mysqli_real_escape_string($allConn, $candidateSyncROW["company_id"]);
		
		$crudQUERY[] = "INSERT INTO vtech_mappingdb.candidate_fees(emp_id,e_name,c_clientid,added_by) values('$employeeId','$employeeName','$companyId','$user')";
	}

	//////// Sync Clients Fees Table ////////
	$clientFeesSyncQUERY = mysqli_query($allConn, "SELECT
		c.company_id,
	   	c.name AS company_name
	FROM
		cats.company AS c
	WHERE
		c.company_id NOT IN (SELECT clf.client_id FROM vtech_mappingdb.client_fees AS clf)
	GROUP BY c.company_id");
	
	while ($clientFeesSyncROW =mysqli_fetch_array($clientFeesSyncQUERY)) {
		$companyId = mysqli_real_escape_string($allConn, $clientFeesSyncROW["company_id"]);
		$companyName = mysqli_real_escape_string($allConn, $clientFeesSyncROW["company_name"]);
		
		$crudQUERY[] = "INSERT INTO vtech_mappingdb.client_fees(client_id,client_name,added_by) values('$companyId','$companyName','$user')";
	}

	//////// Sync Client Markup / Margin Table ////////
	$clientMarkupMarginSyncQUERY = mysqli_query($allConn, "SELECT
		c.company_id,
	   	c.name AS company_name,
	   	ic_margin.value AS default_margin_percentage
	FROM
		cats.company AS c
		LEFT JOIN mis_reports.incentive_criteria AS ic_margin ON ic_margin.personnel = 'Default MD Percentage' AND ic_margin.comment = 'vTech Margin'
	WHERE
		c.company_id NOT IN (SELECT cmm.client_id FROM vtech_mappingdb.client_markup_margin AS cmm)
	GROUP BY c.company_id");
	
	while ($clientSyncROW =mysqli_fetch_array($clientMarkupMarginSyncQUERY)) {
		$companyId = mysqli_real_escape_string($allConn, $clientSyncROW["company_id"]);
		$companyName = mysqli_real_escape_string($allConn, $clientSyncROW["company_name"]);
		$defaultMarginPercentage = mysqli_real_escape_string($allConn, $clientSyncROW["default_margin_percentage"]);
		
		$crudQUERY[] = "INSERT INTO vtech_mappingdb.client_markup_margin(client_id,client_name,type,value,added_by) values('$companyId','$companyName','Margin','$defaultMarginPercentage','$user')";
	}

	/////// Update Candidate Name ///////
	$candidateUpdateQUERY = mysqli_query($vtechhrmConn, "SELECT
		e.id AS employee_id,
		CONCAT(e.first_name,' ',e.last_name) AS employee_name,
	    cnf.e_name AS cnf_employee_name
	FROM
		vtechhrm.employees AS e
	    LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
	GROUP BY e.id");
	
	while ($candidateUpdateROW =mysqli_fetch_array($candidateUpdateQUERY)) {
		$employeeId = mysqli_real_escape_string($allConn, $candidateUpdateROW["employee_id"]);
		$employeeName = mysqli_real_escape_string($allConn, $candidateUpdateROW["employee_name"]);

		if ($employeeName != $candidateUpdateROW["cnf_employee_name"]) {
			$crudQUERY[] = "UPDATE vtech_mappingdb.candidate_fees SET e_name = '$employeeName' WHERE emp_id = '$employeeId'";
		}
	}

	/////// Update Client Fees / Client Markup Margin Table ///////
	$clientUpdateQUERY = mysqli_query($allConn, "SELECT
		c.company_id,
	   	c.name AS company_name,
	   	clf.client_name AS clf_company_name,
	   	cmm.client_name AS cmm_company_name,
	   	cmm.type AS cmm_company_type,
	   	cmm.value AS cmm_percentage_value,
	   	ic_margin.value AS default_margin_percentage,
	   	ic_markup.value AS default_markup_percentage
	FROM
		cats.company AS c
		LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = c.company_id
		LEFT JOIN vtech_mappingdb.client_markup_margin AS cmm ON cmm.client_id = c.company_id
		LEFT JOIN mis_reports.incentive_criteria AS ic_margin ON ic_margin.personnel = 'Default MD Percentage' AND ic_margin.comment = 'vTech Margin'
		LEFT JOIN mis_reports.incentive_criteria AS ic_markup ON ic_markup.personnel = 'Default MD Percentage' AND ic_markup.comment = 'vTech Markup'
	GROUP BY c.company_id");
	
	while ($clientUpdateROW =mysqli_fetch_array($clientUpdateQUERY)) {
		$companyId = mysqli_real_escape_string($allConn, $clientUpdateROW["company_id"]);
		$companyName = mysqli_real_escape_string($allConn, $clientUpdateROW["company_name"]);
		$defaultMarginPercentage = mysqli_real_escape_string($allConn, $clientUpdateROW["default_margin_percentage"]);
		$defaultMarkupPercentage = mysqli_real_escape_string($allConn, $clientUpdateROW["default_markup_percentage"]);
		
		if ($companyName != $clientUpdateROW["clf_company_name"]) {
			$crudQUERY[] = "UPDATE vtech_mappingdb.client_fees SET client_name = '$companyName' WHERE client_id = '$companyId'";
		}

		if ($companyName != $clientUpdateROW["cmm_company_name"]) {
			$crudQUERY[] = "UPDATE vtech_mappingdb.client_markup_margin SET client_name = '$companyName' WHERE client_id = '$companyId'";
		}

		if ($clientUpdateROW["cmm_company_type"] == "Margin" && $clientUpdateROW["cmm_percentage_value"] == "0") {
			$crudQUERY[] = "UPDATE vtech_mappingdb.client_markup_margin SET value = '$defaultMarginPercentage' WHERE client_id = '$companyId'";
		}

		if ($clientUpdateROW["cmm_company_type"] == "Markup" && $clientUpdateROW["cmm_percentage_value"] == "0") {
			$crudQUERY[] = "UPDATE vtech_mappingdb.client_markup_margin SET value = '$defaultMarkupPercentage' WHERE client_id = '$companyId'";
		}
	}

	if (count($crudQUERY) > 0) {
		$crudSTATEMENTS = implode(";", $crudQUERY);
		if (mysqli_multi_query($allConn, $crudSTATEMENTS)) {
			echo "Synchronization Completed!";
		} else {
			echo "Something Wrong!";
		}
	} else {
		echo "All records are up to date!";
	}
?>