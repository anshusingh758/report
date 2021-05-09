<?php
	include_once('../../../config.php');
	$array = array();
	$mainQuery = "SELECT
		DISTINCT e.id as id,
		cbs.inprocess_on,
		cbs.completed_on,
		tov.OVID AS ovid,
		tov.value,
		(CASE WHEN cbs.CCTID = cst.CCTID AND cbs.OVID != 4 AND cbs.OVID != 6 AND cbs.client_id = cst.client_id THEN cst.description END) AS pending,
		(CASE WHEN cbs.CCTID = cst.CCTID AND cbs.OVID = 4 AND cbs.OVID != 6 AND cbs.client_id = cst.client_id THEN cst.description END) AS submit,
		(CASE WHEN cbs.CCTID = cst.CCTID AND cbs.OVID != 6 AND cbs.client_id = cst.client_id THEN cst.description END) AS required,
		(CASE WHEN cbs.CCTID = cst.CCTID  AND cbs.client_id = cst.client_id THEN cst.description END) AS status
	 FROM
	 	vtech_candidate_onboarding.compliance_client_template as cst
	 	LEFT JOIN vtech_candidate_onboarding.candidate_bg_status as cbs on cst.client_id = cbs.client_id
	 	LEFT JOIN vtech_candidate_onboarding.t_option_value as tov ON tov.OVID = cbs.OVID
	 	LEFT JOIN vtechhrm.employees as e on cbs.can_id = e.id
	WHERE
		e.status = 'OnBoarding'";

  	$dailyRESULT = mysqli_query($allConn, $mainQuery);
    
    while($mainROW = mysqli_fetch_array($dailyRESULT)){
    	$array[$mainROW['id']][] = $mainROW;
  	}
?>