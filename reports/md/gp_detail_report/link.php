<?php
	include_once("../../../security.php");
	include_once("../../../config.php");

	$dateRange = date("m/Y", strtotime("-2 month"));
	
	$fromDate = date("Y-m-01", strtotime('-2 month'));
	$toDate = date("Y-m-t", strtotime('-2 month'));
	
	$fromDate2 = date("Y-m-01", strtotime('-3 month'));
	$toDate2 = date("Y-m-t", strtotime('-3 month'));

	$detailLink = LOCAL_REPORT_PATH."/md/gp_detail_report/index.php?customized-multiple-month=".urlencode($dateRange)."&form-submit-button=&response_type=1";

	$key = CURL_SESSION_KEY;
	$userSession = $_SESSION["user"];
	$memberSession = $_SESSION["userMember"];

	$detailLink .= "&curl_session_key=".$key."&user=".$userSession."&userMember=".$memberSession;

	$curl = curl_init();
	
	curl_setopt_array($curl, array(
		CURLOPT_URL => $detailLink,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"postman-token: e71c1a3f-ef9e-8515-4be2-235e7b30c128"
		),
	));

	$response = curl_exec($curl);
	$error = curl_error($curl);
	curl_close($curl);

	if ($error) {
		echo "cURL Error #:".$error;
	} else {
		$detailData = $response;
	}

	$detailData2 = json_decode($detailData, true);

	$briefData = json_encode($detailData2["briefList"], true);
	$totalData = json_encode($detailData2["totalList"], true);

	$newempQUERY = mysqli_query($allConn, "SELECT
		COUNT(DISTINCT e.id) AS total_new_employees
	FROM
		vtechhrm.employees AS e
	WHERE
		e.status != 'Internal Employee'
	AND
		DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");

	$newempROW = mysqli_fetch_array($newempQUERY);

	$totalNewEmployees = $newempROW["total_new_employees"];

	$leftempQUERY = mysqli_query($allConn, "SELECT
		COUNT(DISTINCT e.id) AS total_left_employees
	FROM
		vtechhrm.employees as e
		LEFT JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
	WHERE
		ep.project != '6'
	AND
		(e.status = 'Terminated' OR e.status = 'Termination In_Vol' OR e.status = 'Termination Vol')
	AND
		DATE_FORMAT(e.termination_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");

	$leftempROW = mysqli_fetch_array($leftempQUERY);

	$totalLeftEmployees = $leftempROW["total_left_employees"];
	
	$activeEmpQUERY = mysqli_query($allConn, "SELECT
		gd.total_active
	FROM
		mis_reports.gp_data AS gd
	WHERE
		gd.from_date = '$fromDate2'
	AND
		gd.to_date = '$toDate2'
	GROUP BY gd.id");

	$activeEmpROW = mysqli_fetch_array($activeEmpQUERY);

	$totalActiveEmployees = $activeEmpROW["total_active"] + $totalNewEmployees - $totalLeftEmployees;

 	if (mysqli_query($allConn, "INSERT INTO mis_reports.gp_data(from_date,to_date,total_active,total_new,total_left,brief_data,total_data) VALUES('$fromDate','$toDate','$totalActiveEmployees','$totalNewEmployees','$totalLeftEmployees','$briefData','$totalData')")) {
		echo "success";
	} else {
		echo "error";
	}
?>