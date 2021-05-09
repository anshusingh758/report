<?php
	include_once("../../config.php");
	include_once("../../functions/reporting-service.php");
	// include_once("../../PHPMailer/PHPMailerAutoload.php");
	include_once("../../email-config.php");

	//Embed Image
	$mail->AddEmbeddedImage("../../images/company_logo.png", "companyLogo");

	// Add a sender
	$mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');

	$weekList = array(
		"four_weeks_ago" => array(
			date("Y-m-d",strtotime('last Monday -21 days')),
			date("Y-m-d",strtotime('last Sunday -21 days'))
		),
		"three_weeks_ago" => array(
			date("Y-m-d",strtotime('last Monday -14 days')),
			date("Y-m-d",strtotime('last Sunday -14 days'))
		),
		"two_weeks_ago" => array(
			date("Y-m-d",strtotime('last Monday -7 days')),
			date("Y-m-d",strtotime('last Sunday -7 days'))
		),
		"past_week" => array(
			date("Y-m-d",strtotime('last Monday')),
			date("Y-m-d",strtotime('last Sunday'))
		)
	);

	$personnelArray = array();

	$personnelData = "'".implode("', '",salesGroupPersonnelList($sales_connect,"3"))."'";
	
	foreach ($weekList as $weekListKey => $weekListValue) {
		$startDate = $weekListValue[0];
		$endDate = $weekListValue[1];

		$dateRange = "(".date("m-d-y", strtotime($startDate))." ~ ".date("m-d-y", (strtotime($endDate) - 86400)).")";

		$mainQUERY = "SELECT
			a.personnel_name,
		    a.personnel_status,
		    a.personnel_emailid,
		    b.total_accounts,
		    b.total_contacts,
		    c.total_first_touch,
		    d.total_follow_ups,
		    e.total_show_ups,
		    f.total_requirements,
		    g.total_call,
		    g.total_email,
		    g.total_comment,
		    g.total_meeting,
		    g.total_meaningful,
		    h.total_pipeline,
			h.total_working,
			h.total_submitted,
			h.total_second_stage,
			h.total_won
		FROM
		(SELECT
			CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
			u.status AS personnel_status,
			u.emailAddress AS personnel_emailid
		FROM
			vtechcrm.x2_users AS u
		WHERE
			CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		GROUP BY personnel_name) AS a
		LEFT JOIN
		(SELECT
			CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
			COUNT(DISTINCT CASE WHEN (e.associationType = 'Accounts') THEN act.id END) AS total_accounts,
			COUNT(DISTINCT CASE WHEN (e.associationType = 'Contacts') THEN c.id END) AS total_contacts
		FROM
			vtechcrm.x2_events AS e
			LEFT JOIN vtechcrm.x2_accounts AS act ON act.id = e.associationId
			LEFT JOIN x2_contacts AS c ON c.id = e.associationId
			LEFT JOIN vtechcrm.x2_users AS u ON e.user = u.username
		WHERE
			CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		 	e.type = 'record_create'
		AND
			DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY personnel_name) AS b ON b.personnel_name = a.personnel_name
		LEFT JOIN
		(SELECT
			ft.personnel_name,
			COUNT(DISTINCT ft.id) AS total_first_touch
		FROM
		(SELECT
			MIN(actx.id) AS min_id,
			act.id,
			CONCAT(u.firstName, ' ', u.lastName) AS personnel_name
		FROM
			vtechcrm.x2_actions AS act
			LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
			LEFT JOIN vtechcrm.x2_actions AS actx ON actx.associationId = act.associationId AND actx.associationType = act.associationType AND actx.type IN ('note','call','emaildata','meaningfulData','event') AND actx.completedBy = act.completedBy
		WHERE
			CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
		AND
			act.associationType IN ('accounts','contacts','opportunities')
		AND
			act.type IN ('note','call','emaildata','meaningfulData','event')
		AND
			DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY act.id
		HAVING min_id = id) AS ft
		GROUP BY personnel_name) AS c ON c.personnel_name = a.personnel_name
		LEFT JOIN
		(SELECT
			fu.personnel_name,
			COUNT(DISTINCT fu.id) AS total_follow_ups
		FROM
		(SELECT
			MIN(actx.id) AS min_id,
			act.id,
			CONCAT(u.firstName, ' ', u.lastName) AS personnel_name
		FROM
			vtechcrm.x2_actions AS act
			LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
			LEFT JOIN vtechcrm.x2_actions AS actx ON actx.associationId = act.associationId AND actx.associationType = act.associationType AND actx.type IN ('note','call','emaildata','meaningfulData','event') AND actx.completedBy = act.completedBy
		WHERE
			CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
		AND
			act.associationType IN ('accounts','contacts','opportunities')
		AND
			act.type IN ('note','call','emaildata','meaningfulData','event')
		AND
			DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY act.id
		HAVING min_id != id) AS fu
		GROUP BY personnel_name) AS d ON d.personnel_name = a.personnel_name
		LEFT JOIN
		(SELECT
			CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    COUNT(DISTINCT act.id) AS total_show_ups
		FROM
		    vtechcrm.x2_actions AS act
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
		WHERE
			CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
			act.type = 'event'
		AND
			act.complete = 'Yes'
		AND
			DATE_FORMAT(FROM_UNIXTIME(act.completeDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY personnel_name) AS e ON e.personnel_name = a.personnel_name
		LEFT JOIN
		(SELECT
			tr.personnel_name,
		    COUNT(DISTINCT tr.id) AS total_requirements
		FROM
		(SELECT
			copp.id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    COUNT(job.joborder_id) AS total_job
		FROM
			contract.x2_opportunities AS copp
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = copp.assignedTo OR u.username = copp.c_research_by
		   	LEFT JOIN cats.contract_mapping AS cm ON cm.value_map = copp.c_solicitation_number AND cm.field_name = 'Contract No'
		    LEFT JOIN cats.joborder AS job ON job.company_id = cm.data_item_id
		WHERE
			CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
			DATE_FORMAT(FROM_UNIXTIME(copp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY copp.id
		HAVING total_job >= 1) AS tr
		GROUP BY tr.personnel_name) AS f ON f.personnel_name = a.personnel_name
		LEFT JOIN
		(SELECT
			CONCAT(u.firstName, ' ', u.lastName) AS personnel_name,
		    COUNT(DISTINCT CASE WHEN act.type = 'call' THEN act.id END) AS total_call,
		    COUNT(DISTINCT CASE WHEN act.type = 'emaildata' THEN act.id END) AS total_email,
		    COUNT(DISTINCT CASE WHEN act.type = 'note' THEN act.id END) AS total_comment,
		    COUNT(DISTINCT CASE WHEN act.type = 'event' THEN act.id END) AS total_meeting,
		    COUNT(DISTINCT CASE WHEN act.type = 'meaningfulData' THEN act.id END) AS total_meaningful
		FROM
			vtechcrm.x2_actions AS act
			LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
		WHERE
			CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
		AND
			act.associationType IN ('accounts','contacts','opportunities')
		AND
			act.type IN ('note','call','emaildata','meaningfulData','event')
		AND
			DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY personnel_name) AS g ON g.personnel_name = a.personnel_name
		LEFT JOIN
		(SELECT
		    all_main.personnel_name,
		    
		    SUM(IF(((pip_main.pipeline_main IS NULL) AND (pip_log.pipeline_log IS NOT NULL)), 1, IF(((pip_main.pipeline_main IS NOT NULL) AND (pip_log.pipeline_log IS NULL)), 1, IF(((pip_main.pipeline_main IS NOT NULL) AND (pip_log.pipeline_log IS NOT NULL)), 1, 0)))) AS total_pipeline,
		    
		    SUM(IF(((wor_main.working_main IS NULL) AND (wor_log.working_log IS NOT NULL)), 1, IF(((wor_main.working_main IS NOT NULL) AND (wor_log.working_log IS NULL)), 1, IF(((wor_main.working_main IS NOT NULL) AND (wor_log.working_log IS NOT NULL)), 1, 0)))) AS total_working,
		    
		    SUM(IF(((sub_main.submitted_main IS NULL) AND (sub_log.submitted_log IS NOT NULL)), 1, IF(((sub_main.submitted_main IS NOT NULL) AND (sub_log.submitted_log IS NULL)), 1, IF(((sub_main.submitted_main IS NOT NULL) AND (sub_log.submitted_log IS NOT NULL)), 1, 0)))) AS total_submitted,
		    
		    SUM(IF(((sec_main.second_stage_main IS NULL) AND (sec_log.second_stage_log IS NOT NULL)), 1, IF(((sec_main.second_stage_main IS NOT NULL) AND (sec_log.second_stage_log IS NULL)), 1, IF(((sec_main.second_stage_main IS NOT NULL) AND (sec_log.second_stage_log IS NOT NULL)), 1, 0)))) AS total_second_stage,
		    
		    SUM(IF(((won_mainn.won_main IS NULL) AND (won_logg.won_log IS NOT NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NOT NULL)), 1, 0)))) AS total_won
		FROM
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		GROUP BY opportunity_id) AS all_main
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    opp.id AS pipeline_main
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    opp.salesStage = 'Pipeline'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS pip_main ON pip_main.opportunity_id = all_main.opportunity_id AND pip_main.personnel_name = all_main.personnel_name
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    opp.id AS working_main
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    opp.salesStage = 'Working'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS wor_main ON wor_main.opportunity_id = all_main.opportunity_id AND wor_main.personnel_name = all_main.personnel_name
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    opp.id AS submitted_main
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    opp.salesStage = 'Submitted'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS sub_main ON sub_main.opportunity_id = all_main.opportunity_id AND sub_main.personnel_name = all_main.personnel_name
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    opp.id AS second_stage_main
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    opp.salesStage = 'Second Stage'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS sec_main ON sec_main.opportunity_id = all_main.opportunity_id AND sec_main.personnel_name = all_main.personnel_name
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    opp.id AS won_main
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    opp.salesStage = 'Won'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(opp.createDate), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS won_mainn ON won_mainn.opportunity_id = all_main.opportunity_id AND won_mainn.personnel_name = all_main.personnel_name
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    MAX(chg.id) AS pipeline_log
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    chg.type = 'Opportunity'
		AND
		    chg.fieldName = 'salesStage'
		AND
		    chg.newValue = 'Pipeline'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS pip_log ON pip_log.opportunity_id = all_main.opportunity_id AND pip_log.personnel_name = all_main.personnel_name
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    MAX(chg.id) AS working_log
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    chg.type = 'Opportunity'
		AND
		    chg.fieldName = 'salesStage'
		AND
		    chg.newValue = 'Working'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS wor_log ON wor_log.opportunity_id = all_main.opportunity_id AND wor_log.personnel_name = all_main.personnel_name
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    MAX(chg.id) AS submitted_log
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    chg.type = 'Opportunity'
		AND
		    chg.fieldName = 'salesStage'
		AND
		    chg.newValue = 'Submitted'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS sub_log ON sub_log.opportunity_id = all_main.opportunity_id AND sub_log.personnel_name = all_main.personnel_name
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    MAX(chg.id) AS second_stage_log
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    chg.type = 'Opportunity'
		AND
		    chg.fieldName = 'salesStage'
		AND
		    chg.newValue = 'Second Stage'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS sec_log ON sec_log.opportunity_id = all_main.opportunity_id AND sec_log.personnel_name = all_main.personnel_name
		LEFT JOIN
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    MAX(chg.id) AS won_log
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_changelog AS chg ON chg.itemId = opp.id
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		AND
		    chg.type = 'Opportunity'
		AND
		    chg.fieldName = 'salesStage'
		AND
		    chg.newValue = 'Won'
		AND
		    DATE_FORMAT(FROM_UNIXTIME(chg.timestamp), '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY opportunity_id) AS won_logg ON won_logg.opportunity_id = all_main.opportunity_id AND won_logg.personnel_name = all_main.personnel_name
		WHERE
		    (pip_main.pipeline_main != '' OR wor_main.working_main != '' OR sub_main.submitted_main != '' OR sec_main.second_stage_main != '' OR won_mainn.won_main != '' OR pip_log.pipeline_log != '' OR wor_log.working_log != '' OR sub_log.submitted_log != '' OR sec_log.second_stage_log != '' OR won_logg.won_log != '')
		GROUP BY personnel_name) AS h ON h.personnel_name = a.personnel_name
		WHERE
			(b.total_accounts != '' OR b.total_contacts != '' OR c.total_first_touch != '' OR d.total_follow_ups != '' OR e.total_show_ups != '' OR f.total_requirements != '' OR g.total_call != '' OR g.total_email != '' OR g.total_comment != '' OR g.total_meeting != '' OR g.total_meaningful != '' OR h.total_pipeline != '' OR h.total_working != '' OR h.total_submitted != '' OR h.total_second_stage != '' OR h.total_won != '' OR a.personnel_status != '0')
		GROUP BY personnel_name";

		$mainRESULT = mysqli_query($sales_connect, $mainQUERY);
		
		if (mysqli_num_rows($mainRESULT) > 0) {
			while ($mainROW = mysqli_fetch_array($mainRESULT)) {
				
				$personnelName = implode("_", explode(" ", strtolower($mainROW['personnel_name'])));
				
				$personnelEmailID = $mainROW['personnel_emailid'];

				$totalAccounts = $totalContacts = $totalFirstTouch = $totalMeaningful = $totalMeetings = $totalShowUps = $totalFollowUps = $totalPipeline = $totalWorking = $totalSubmitted = $totalSecondStage = $totalWon = $totalRequirements = "0";

				if ($mainROW["total_accounts"] != "") {
					$totalAccounts = $mainROW["total_accounts"];
				}

				if ($mainROW["total_contacts"] != "") {
					$totalContacts = $mainROW["total_contacts"];
				}

				if ($mainROW["total_first_touch"] != "") {
					$totalFirstTouch = $mainROW["total_first_touch"];
				}

				if ($mainROW["total_meaningful"] != "") {
					$totalMeaningful = $mainROW["total_meaningful"];
				}

				if ($mainROW["total_meeting"] != "") {
					$totalMeetings = $mainROW["total_meeting"];
				}

				if ($mainROW["total_show_ups"] != "") {
					$totalShowUps = $mainROW["total_show_ups"];
				}

				if ($mainROW["total_follow_ups"] != "") {
					$totalFollowUps = $mainROW["total_follow_ups"];
				}

				if ($mainROW["total_pipeline"] != "") {
					$totalPipeline = $mainROW["total_pipeline"];
				}

				if ($mainROW["total_working"] != "") {
					$totalWorking = $mainROW["total_working"];
				}

				if ($mainROW["total_submitted"] != "") {
					$totalSubmitted = $mainROW["total_submitted"];
				}

				if ($mainROW["total_second_stage"] != "") {
					$totalSecondStage = $mainROW["total_second_stage"];
				}

				if ($mainROW["total_won"] != "") {
					$totalWon = $mainROW["total_won"];
				}

				if ($mainROW["total_requirements"] != "") {
					$totalRequirements = $mainROW["total_requirements"];
				}

				$personnelArray[$personnelName]["email"] = $personnelEmailID;
				$personnelArray[$personnelName]["week_date"][$weekListKey] = $dateRange;
				$personnelArray[$personnelName]["new_accounts"][$weekListKey] = $totalAccounts;
				$personnelArray[$personnelName]["new_contacts"][$weekListKey] = $totalContacts;
				$personnelArray[$personnelName]["first_touch"][$weekListKey] = $totalFirstTouch;
				$personnelArray[$personnelName]["meaningful_conversations"][$weekListKey] = $totalMeaningful;
				$personnelArray[$personnelName]["meetings"][$weekListKey] = $totalMeetings;
				$personnelArray[$personnelName]["show_ups"][$weekListKey] = $totalShowUps;
				$personnelArray[$personnelName]["follow_ups"][$weekListKey] = $totalFollowUps;
				$personnelArray[$personnelName]["pipeline"][$weekListKey] = $totalPipeline;
				$personnelArray[$personnelName]["working"][$weekListKey] = $totalWorking;
				$personnelArray[$personnelName]["submitted"][$weekListKey] = $totalSubmitted;
				$personnelArray[$personnelName]["second_stage"][$weekListKey] = $totalSecondStage;
				$personnelArray[$personnelName]["won"][$weekListKey] = $totalWon;
				$personnelArray[$personnelName]["accounts_got_requirements"][$weekListKey] = $totalRequirements;
			}
		}
	}

	foreach ($personnelArray as $personnelArrayKey => $personnelArrayItem) {
		
		$personnelEmail = $personnelArrayItem["email"];
		
		$mail->Subject = $mailSubject = "US BD Matrix || ".ucwords(implode(" ", explode("_", $personnelArrayKey)))." || ".date("m-d-Y (l)", strtotime("today"));

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
					<td style="font-size: 18px;font-weight: bold;">US BD Matrix Notification || <span style="color: #2266AA;">'.ucwords(implode(" ", explode("_", $personnelArrayKey))).'</span> || <span style="color: #449D44;">'.date("m-d-Y (l)", strtotime('today')).'</span></td>
				</tr>
				<tr>
					<td><br>
						<center>
						<table style="width: 100%;border: 1px solid #ddd;">
							<thead>
								<tr style="background-color: #ccc;color: #000;font-size: 14px;">
									<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Title</th>
									<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Four Weeks Ago<div style="border-top: 1px solid #ddd;padding-top: 5px;color: #2266AA;font-size: 12px;">'.$personnelArrayItem['week_date']['four_weeks_ago'].'</div></th>
									<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Three Weeks Ago<div style="border-top: 1px solid #ddd;padding-top: 5px;color: #2266AA;font-size: 12px;">'.$personnelArrayItem['week_date']['three_weeks_ago'].'</div></th>
									<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Two Weeks Ago<div style="border-top: 1px solid #ddd;padding-top: 5px;color: #2266AA;font-size: 12px;">'.$personnelArrayItem['week_date']['two_weeks_ago'].'</div></th>
									<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">Past Week<div style="border-top: 1px solid #ddd;padding-top: 5px;color: #2266AA;font-size: 12px;">'.$personnelArrayItem['week_date']['past_week'].'</div></th>
								</tr>
							</thead>
							<tbody>';
							array_shift($personnelArrayItem);
							array_shift($personnelArrayItem);
							foreach ($personnelArrayItem as $personnelArrayItemKey => $personnelArrayItemValue) {
							$mailContent.='<tr style="font-size: 15px;">
									<td style="text-align: left;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.ucwords(implode(" ", explode("_", $personnelArrayItemKey))).'</td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$personnelArrayItem[$personnelArrayItemKey]['four_weeks_ago'].'</td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$personnelArrayItem[$personnelArrayItemKey]['three_weeks_ago'].'</td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$personnelArrayItem[$personnelArrayItemKey]['two_weeks_ago'].'</td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.$personnelArrayItem[$personnelArrayItemKey]['past_week'].'</td>
									</tr>';
							}
							$mailContent.='</tbody>
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
		$mail->addAddress($personnelEmail);
		$mail->addBcc('ravip@vtechsolution.us');

		echo $mailContent;
		
		include("../../functions/email-send-config.php");
	}
?>
