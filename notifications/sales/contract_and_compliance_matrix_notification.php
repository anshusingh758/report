<?php
	include_once("../../config.php");
	include_once("../../functions/reporting-service.php");
	// include_once("../../PHPMailer/PHPMailerAutoload.php");
	include_once("../../email-config.php");

	//Embed Image
	$mail->AddEmbeddedImage("../../images/company_logo.png", "companyLogo");

	// Add a sender
	$mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');

	//Report Logic
	$complianceTeamDataArray = array();
	$newContractsInprocessItem = 0;

	if ($_GET["type"] == "daily") {
		$startDate = date("Y-m-d", strtotime("last day"));
		$endDate = date("Y-m-d", strtotime("today"));

		$titleHead = "Daily";
		$titleDate = date("m-d-Y",strtotime('last day'));
		$mail->Subject = 'Daily C&C Matrix Report_'.date("m_d_Y",strtotime("last day"));
	} elseif ($_GET["type"] == "weekly") {
		$startDate = date("Y-m-d", strtotime("last Monday"));
		$endDate = date("Y-m-d", strtotime("last Sunday"));

		$titleHead = "Weekly";
		$titleDate = date("m-d-Y", strtotime("last Monday"))." --to-- ".date("m-d-Y", strtotime("last Sunday"));
		$mail->Subject = 'Weekly C&C Matrix Report_'.date("m_d",strtotime("last Sunday"));
	}

	$newContractsInprocessQUERY = mysqli_query($allConn, "SELECT
		COUNT(DISTINCT s_opp.id) AS new_contract_in_process
	FROM
		vtechcrm.x2_opportunities AS s_opp
	WHERE
		s_opp.salesStage = 'Won'
	AND
		s_opp.id NOT IN (SELECT
		    c_opp.c_sale_opportunity_id
	    FROM
	    	contract.x2_opportunities AS c_opp
	    WHERE
	    	c_opp.c_sale_opportunity_id IS NOT NULL)
	AND
		DATE_FORMAT(FROM_UNIXTIME(s_opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'");

	$newContractsInprocessROW = mysqli_fetch_array($newContractsInprocessQUERY);

	$newContractsInprocessItem = $newContractsInprocessROW["new_contract_in_process"];

	$mainQUERY = "SELECT
		a.assigned_to,
		b.new_contracts_completed,
		c.total_external_inprocess,
		c.total_external_completed,
		c.total_internal_inprocess,
		c.total_internal_completed,
		d.total_awards_inprocess,
		d.total_awards_completed
	FROM
	(SELECT
		concat(c_u.firstName,' ',c_u.lastName) AS assigned_to
	FROM
		contract.x2_users AS c_u
	GROUP BY c_u.username
	ORDER BY c_u.username ASC) AS a
	LEFT JOIN
	(SELECT
		concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    COUNT(DISTINCT c_o.id) AS new_contracts_completed
	FROM
		contract.x2_opportunities AS c_o
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_o.assignedTo
	WHERE
		DATE_FORMAT(FROM_UNIXTIME(c_o.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY c_o.assignedTo
	ORDER BY assigned_to ASC) AS b ON b.assigned_to = a.assigned_to
	LEFT JOIN
	(SELECT
		all_main.assigned_to,
	    
	    SUM(IF(((ext_proc_main.ext_inprocess_main IS NULL) AND (ext_proc_log.ext_inprocess_log IS NOT NULL)), 1, IF(((ext_proc_main.ext_inprocess_main IS NOT NULL) AND (ext_proc_log.ext_inprocess_log IS NULL)), 1, IF(((ext_proc_main.ext_inprocess_main IS NOT NULL) AND (ext_proc_log.ext_inprocess_log IS NOT NULL)), 1, 0)))) AS total_external_inprocess,
	    
	    SUM(IF(((ext_comp_main.ext_completed_main IS NULL) AND (ext_comp_log.ext_completed_log IS NOT NULL)), 1, IF(((ext_comp_main.ext_completed_main IS NOT NULL) AND (ext_comp_log.ext_completed_log IS NULL)), 1, IF(((ext_comp_main.ext_completed_main IS NOT NULL) AND (ext_comp_log.ext_completed_log IS NOT NULL)), 1, 0)))) AS total_external_completed,
	    
	    SUM(IF(((int_proc_main.int_inprocess_main IS NULL) AND (int_proc_log.int_inprocess_log IS NOT NULL)), 1, IF(((int_proc_main.int_inprocess_main IS NOT NULL) AND (int_proc_log.int_inprocess_log IS NULL)), 1, IF(((int_proc_main.int_inprocess_main IS NOT NULL) AND (int_proc_log.int_inprocess_log IS NOT NULL)), 1, 0)))) AS total_internal_inprocess,
	    
	    SUM(IF(((int_comp_main.int_completed_main IS NULL) AND (int_comp_log.int_completed_log IS NOT NULL)), 1, IF(((int_comp_main.int_completed_main IS NOT NULL) AND (int_comp_log.int_completed_log IS NULL)), 1, IF(((int_comp_main.int_completed_main IS NOT NULL) AND (int_comp_log.int_completed_log IS NOT NULL)), 1, 0)))) AS total_internal_completed
	FROM
	(SELECT
		c_a.id AS audit_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to
	FROM
		contract.x2_audits AS c_a
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
	GROUP BY audit_id) AS all_main
	LEFT JOIN
	(SELECT
	    c_a.id AS audit_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    c_a.id AS ext_inprocess_main
	FROM
	    contract.x2_audits AS c_a
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
	WHERE
	    c_a.c_audit_type = 'External/Client'
	AND
	    c_a.c_audit_status = 'In process'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(c_a.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY audit_id) AS ext_proc_main ON ext_proc_main.audit_id = all_main.audit_id AND ext_proc_main.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_a.id AS audit_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    MAX(chg.id) AS ext_inprocess_log
	FROM
	    contract.x2_audits AS c_a
	    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_a.id
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
	WHERE
	    c_a.c_audit_type = 'External/Client'
	AND
	    chg.type = 'Audits'
	AND
	    chg.fieldName = 'c_audit_status'
	AND
	    chg.newValue = 'In process'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY audit_id) AS ext_proc_log ON ext_proc_log.audit_id = all_main.audit_id AND ext_proc_log.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_a.id AS audit_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    c_a.id AS ext_completed_main
	FROM
	    contract.x2_audits AS c_a
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
	WHERE
	    c_a.c_audit_type = 'External/Client'
	AND
	    c_a.c_audit_status = 'Completed'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(c_a.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY audit_id) AS ext_comp_main ON ext_comp_main.audit_id = all_main.audit_id AND ext_comp_main.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_a.id AS audit_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    MAX(chg.id) AS ext_completed_log
	FROM
	    contract.x2_audits AS c_a
	    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_a.id
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
	WHERE
	    c_a.c_audit_type = 'External/Client'
	AND
	    chg.type = 'Audits'
	AND
	    chg.fieldName = 'c_audit_status'
	AND
	    chg.newValue = 'Completed'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY audit_id) AS ext_comp_log ON ext_comp_log.audit_id = all_main.audit_id AND ext_comp_log.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_a.id AS audit_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    c_a.id AS int_inprocess_main
	FROM
	    contract.x2_audits AS c_a
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
	WHERE
	    c_a.c_audit_type = 'Internal'
	AND
	    c_a.c_audit_status = 'In process'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(c_a.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY audit_id) AS int_proc_main ON int_proc_main.audit_id = all_main.audit_id AND int_proc_main.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_a.id AS audit_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    MAX(chg.id) AS int_inprocess_log
	FROM
	    contract.x2_audits AS c_a
	    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_a.id
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
	WHERE
	    c_a.c_audit_type = 'Internal'
	AND
	    chg.type = 'Audits'
	AND
	    chg.fieldName = 'c_audit_status'
	AND
	    chg.newValue = 'In process'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY audit_id) AS int_proc_log ON int_proc_log.audit_id = all_main.audit_id AND int_proc_log.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_a.id AS audit_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    c_a.id AS int_completed_main
	FROM
	    contract.x2_audits AS c_a
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
	WHERE
	    c_a.c_audit_type = 'Internal'
	AND
	    c_a.c_audit_status = 'Completed'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(c_a.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY audit_id) AS int_comp_main ON int_comp_main.audit_id = all_main.audit_id AND int_comp_main.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_a.id AS audit_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    MAX(chg.id) AS int_completed_log
	FROM
	    contract.x2_audits AS c_a
	    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_a.id
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_a.assignedTo
	WHERE
	    c_a.c_audit_type = 'Internal'
	AND
	    chg.type = 'Audits'
	AND
	    chg.fieldName = 'c_audit_status'
	AND
	    chg.newValue = 'Completed'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY audit_id) AS int_comp_log ON int_comp_log.audit_id = all_main.audit_id AND int_comp_log.assigned_to = all_main.assigned_to
	WHERE
	    (ext_proc_main.ext_inprocess_main != '' OR ext_proc_log.ext_inprocess_log != '' OR ext_comp_main.ext_completed_main != '' OR ext_comp_log.ext_completed_log != '' OR int_proc_main.int_inprocess_main != '' OR int_proc_log.int_inprocess_log != '' OR int_comp_main.int_completed_main != '' OR int_comp_log.int_completed_log != '')
	GROUP BY assigned_to
	ORDER BY assigned_to ASC) AS c ON c.assigned_to = a.assigned_to
	LEFT JOIN
	(SELECT
		all_main.assigned_to,
	    
	    SUM(IF(((award_proc_main.award_inprocess_main IS NULL) AND (award_proc_log.award_inprocess_log IS NOT NULL)), 1, IF(((award_proc_main.award_inprocess_main IS NOT NULL) AND (award_proc_log.award_inprocess_log IS NULL)), 1, IF(((award_proc_main.award_inprocess_main IS NOT NULL) AND (award_proc_log.award_inprocess_log IS NOT NULL)), 1, 0)))) AS total_awards_inprocess,
	    
	    SUM(IF(((award_comp_main.award_completed_main IS NULL) AND (award_comp_log.award_completed_log IS NOT NULL)), 1, IF(((award_comp_main.award_completed_main IS NOT NULL) AND (award_comp_log.award_completed_log IS NULL)), 1, IF(((award_comp_main.award_completed_main IS NOT NULL) AND (award_comp_log.award_completed_log IS NOT NULL)), 1, 0)))) AS total_awards_completed
	FROM
	(SELECT
		c_ca.id AS award_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to
	FROM
		contract.x2_certification_award AS c_ca
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
	GROUP BY award_id) AS all_main
	LEFT JOIN
	(SELECT
	    c_ca.id AS award_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    c_ca.id AS award_inprocess_main
	FROM
	    contract.x2_certification_award AS c_ca
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
	WHERE
	    c_ca.c_award_status = 'In process'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(c_ca.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY award_id) AS award_proc_main ON award_proc_main.award_id = all_main.award_id AND award_proc_main.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_ca.id AS award_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    MAX(chg.id) AS award_inprocess_log
	FROM
	    contract.x2_certification_award AS c_ca
	    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_ca.id
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
	WHERE
	    chg.type = 'Certification_award'
	AND
	    chg.fieldName = 'c_award_status'
	AND
	    chg.newValue = 'In process'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY award_id) AS award_proc_log ON award_proc_log.award_id = all_main.award_id AND award_proc_log.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_ca.id AS award_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    c_ca.id AS award_completed_main
	FROM
	    contract.x2_certification_award AS c_ca
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
	WHERE
	    c_ca.c_award_status = 'Awarded'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(c_ca.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY award_id) AS award_comp_main ON award_comp_main.award_id = all_main.award_id AND award_comp_main.assigned_to = all_main.assigned_to
	LEFT JOIN
	(SELECT
	    c_ca.id AS award_id,
	    concat(c_u.firstName,' ',c_u.lastName) AS assigned_to,
	    MAX(chg.id) AS award_completed_log
	FROM
	    contract.x2_certification_award AS c_ca
	    LEFT JOIN contract.x2_changelog AS chg ON chg.itemId = c_ca.id
	    LEFT JOIN contract.x2_users AS c_u ON c_u.username = c_ca.assignedTo
	WHERE
	    chg.type = 'Certification_award'
	AND
	    chg.fieldName = 'c_award_status'
	AND
	    chg.newValue = 'Awarded'
	AND
	    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
	GROUP BY award_id) AS award_comp_log ON award_comp_log.award_id = all_main.award_id AND award_comp_log.assigned_to = all_main.assigned_to
	WHERE
	    (award_proc_main.award_inprocess_main != '' OR award_proc_log.award_inprocess_log != '' OR award_comp_main.award_completed_main != '' OR award_comp_log.award_completed_log != '')
	GROUP BY assigned_to
	ORDER BY assigned_to ASC) AS d ON d.assigned_to = a.assigned_to
	WHERE
		(b.new_contracts_completed != '' OR c.total_external_inprocess != '' OR c.total_external_completed != '' OR c.total_internal_inprocess != '' OR c.total_internal_completed != '' OR d.total_awards_inprocess != '' OR d.total_awards_completed != '')
	GROUP BY assigned_to
	ORDER BY assigned_to ASC";

	$mainRESULT = mysqli_query($allConn, $mainQUERY);

	if (mysqli_num_rows($mainRESULT) > 0) {
		while ($mainROW = mysqli_fetch_array($mainRESULT)) {

			$complianceTeamDataArray[] = array(
				"assigned_to" => ucwords(strtolower($mainROW["assigned_to"])),
				"new_contracts_inprocess" => "---",
				"new_contracts_completed" => $mainROW["new_contracts_completed"] == "" ? "0" : $mainROW["new_contracts_completed"],
				"total_awards_inprocess" => $mainROW["total_awards_inprocess"] == "" ? "0" : $mainROW["total_awards_inprocess"],
				"total_awards_completed" => $mainROW["total_awards_completed"] == "" ? "0" : $mainROW["total_awards_completed"],
				"total_external_inprocess" => $mainROW["total_external_inprocess"] == "" ? "0" : $mainROW["total_external_inprocess"],
				"total_external_completed" => $mainROW["total_external_completed"] == "" ? "0" : $mainROW["total_external_completed"],
				"total_internal_inprocess" => $mainROW["total_internal_inprocess"] == "" ? "0" : $mainROW["total_internal_inprocess"],
				"total_internal_completed" => $mainROW["total_internal_completed"] == "" ? "0" : $mainROW["total_internal_completed"]
			);

		}
	}

	$complianceTeamDataArray[] = array(
		"assigned_to" => "Compliance Team",
		"new_contracts_inprocess" => $newContractsInprocessItem == "" ? "0" : $newContractsInprocessItem,
		"new_contracts_completed" => "---",
		"total_awards_inprocess" => "---",
		"total_awards_completed" => "---",
		"total_external_inprocess" => "---",
		"total_external_completed" => "---",
		"total_internal_inprocess" => "---",
		"total_internal_completed" => "---"
	);

$mailContent = '<!DOCTYPE html>
<html>
<body>
	<table style="width: 100%;">
		<tr>
			<td style="text-align: center;"><img src="cid:companyLogo"></td>
		</tr>
		<tr>
			<td style="background-color: #2266AA;padding: 7px;"></td>
		</tr>
		<tr>
			<td style="background-color: #ccc;padding: 3px;"></td>
		</tr>
		<tr>
			<td style="font-size: 20px;font-weight: bold;">C&C Matrix Report<span style="font-size: 18px;color: #2266AA;"> ('.$titleHead.')</span><span style="font-size: 16px;color: #449D44;"> ('.$titleDate.')</span></td>
		</tr>
		<tr>
			<td>
				<br>
				<center>
				<table style="width: 100%;border: 1px solid #ddd;">
					<thead>
						<tr style="background-color: #ccc;color: #000;">
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;" rowspan="2">Assigned To</th>
							<th style="text-align: center;vertical-align: middle;" colspan="2">New Contracts</th>
							<th style="text-align: center;vertical-align: middle;" colspan="2">Certification / Award</th>
							<th style="text-align: center;vertical-align: middle;" colspan="2">Client Audits</th>
							<th style="text-align: center;vertical-align: middle;" colspan="2">Internal Audits</th>
						</tr>
						<tr style="background-color: #ccc;color: #000;">
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">In Process</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">Completed</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">In Process</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">Completed</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">In Process</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">Completed</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">In Process</th>
							<th style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">Completed</th>
						</tr>
					</thead>
					<tbody>';
						$newContractsInprocess = $newContractsCompleted = $totalAwardsInprocess = $totalAwardsCompleted = $totalExternalInprocess = $totalExternalCompleted = $totalInternalInprocess = $totalInternalCompleted = array();

						foreach ($complianceTeamDataArray AS $complianceTeamDataArrayKey => $complianceTeamDataArrayValue) {

							$assigned_to = $complianceTeamDataArrayValue["assigned_to"];

							$new_contracts_inprocess = $complianceTeamDataArrayValue["new_contracts_inprocess"];

							$new_contracts_completed = $complianceTeamDataArrayValue["new_contracts_completed"];

							$total_awards_inprocess = $complianceTeamDataArrayValue["total_awards_inprocess"];

							$total_awards_completed = $complianceTeamDataArrayValue["total_awards_completed"];

							$total_external_inprocess = $complianceTeamDataArrayValue["total_external_inprocess"];

							$total_external_completed = $complianceTeamDataArrayValue["total_external_completed"];

							$total_internal_inprocess = $complianceTeamDataArrayValue["total_internal_inprocess"];

							$total_internal_completed = $complianceTeamDataArrayValue["total_internal_completed"];

							$newContractsInprocess[] = $new_contracts_inprocess == "---" ? "0" : $new_contracts_inprocess;

							$newContractsCompleted[] = $new_contracts_completed == "---" ? "0" : $new_contracts_completed;

							$totalAwardsInprocess[] = $total_awards_inprocess == "---" ? "0" : $total_awards_inprocess;

							$totalAwardsCompleted[] = $total_awards_completed == "---" ? "0" : $total_awards_completed;

							$totalExternalInprocess[] = $total_external_inprocess == "---" ? "0" : $total_external_inprocess;

							$totalExternalCompleted[] = $total_external_completed == "---" ? "0" : $total_external_completed;

							$totalInternalInprocess[] = $total_internal_inprocess == "---" ? "0" : $total_internal_inprocess;

							$totalInternalCompleted[] = $total_internal_completed == "---" ? "0" : $total_internal_completed;

							$mailContent.='<tr>
								<td style="text-align: left;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$assigned_to.'</td>
								<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$new_contracts_inprocess.'</td>
								<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$new_contracts_completed.'</td>
								<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$total_awards_inprocess.'</td>
								<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$total_awards_completed.'</td>
								<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$total_external_inprocess.'</td>
								<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$total_external_completed.'</td>
								<td style="text-align: center;vertical-align: middle;border-right: 1px solid #ddd;border-bottom: 1px solid #ddd;">'.$total_internal_inprocess.'</td>
								<td style="text-align: center;vertical-align: middle;border-bottom: 1px solid #ddd;">'.$total_internal_completed.'</td>
							</tr>';

						}
					$mailContent.='</tbody>
					<tfoot>
						<tr style="background-color: #ccc;color: #000;font-size: 16px;">
							<th style="text-align: center;vertical-align: middle;">Total</th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($newContractsInprocess).'</th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($newContractsCompleted).'</th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($totalAwardsInprocess).'</th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($totalAwardsCompleted).'</th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($totalExternalInprocess).'</th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($totalExternalCompleted).'</th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($totalInternalInprocess).'</th>
							<th style="text-align: center;vertical-align: middle;">'.array_sum($totalInternalCompleted).'</th>
						</tr>
					</tfoot>
				</table>
				</center>
			</td>
		</tr>
		<tr>
			<td style="color: #555;text-align: right;font-size: 13px;"><br>* Auto generated notification. Please DO NOT reply *<br><hr style="border: 1px dashed #ccc;"></td>
		</tr>
	</table>
</body>
</html>';

	// Add a recipient
	$mail->addAddress('vtech.compl@vtechsolution.com');
	$mail->addBcc('ravip@vtechsolution.us');

	echo $mailContent;

	include("../../functions/email-send-config.php");
?>
