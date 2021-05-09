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

	$teamArray = array();

	$personnelData = "'".implode("', '",salesGroupPersonnelList($sales_connect,"1"))."'";

	$taxSettingsTableData = taxSettingsTable($allConn);

	foreach ($weekList as $weekListKey => $weekListValue) {
		$startDate = $weekListValue[0];
		$endDate = $weekListValue[1];

		$dateRange = "(".date("m-d-y", strtotime($startDate))." ~ ".date("m-d-y", (strtotime($endDate) - 86400)).")";

		$teamArray["week_date"][$weekListKey] = $dateRange;

		$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$startDate,$endDate);

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
			h.total_won,
			h.total_shared_won,
			j.client_checklist_question_first_touch,
			j.interview_question_follow_ups,
			j.premeeting_question_follow_ups
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
		    
		    SUM(IF(((won_mainn.won_main IS NULL) AND (won_logg.won_log IS NOT NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NOT NULL)), 1, 0)))) AS total_won,

		    SUM(IF(all_main.opp_share = 1, IF(((won_mainn.won_main IS NULL) AND (won_logg.won_log IS NOT NULL)), 0.5, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NULL)), 0.5, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NOT NULL)), 0.5, 0))), IF(((won_mainn.won_main IS NULL) AND (won_logg.won_log IS NOT NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NULL)), 1, IF(((won_mainn.won_main IS NOT NULL) AND (won_logg.won_log IS NOT NULL)), 1, 0))))) AS total_shared_won
		FROM
		(SELECT
		    opp.id AS opportunity_id,
		    CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
		    COUNT(DISTINCT CASE WHEN opp.assignedTo != 'admin' AND opp.assignedTo != '' AND opp.assignedTo != 'Anyone' AND opp.c_research_by != 'admin' AND opp.c_research_by != '' AND opp.c_research_by != 'Anyone' AND opp.assignedTo != opp.c_research_by THEN opp.id END) AS opp_share
		FROM
		    vtechcrm.x2_opportunities AS opp
		    LEFT JOIN vtechcrm.x2_users AS u ON u.username = opp.assignedTo OR u.username = opp.c_research_by
		WHERE
		    CONCAT(u.firstName,' ',u.lastName) IN ($personnelData)
		GROUP BY opportunity_id,personnel_name) AS all_main
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
		GROUP BY opportunity_id,personnel_name) AS pip_main ON pip_main.opportunity_id = all_main.opportunity_id AND pip_main.personnel_name = all_main.personnel_name
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
		GROUP BY opportunity_id,personnel_name) AS wor_main ON wor_main.opportunity_id = all_main.opportunity_id AND wor_main.personnel_name = all_main.personnel_name
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
		GROUP BY opportunity_id,personnel_name) AS sub_main ON sub_main.opportunity_id = all_main.opportunity_id AND sub_main.personnel_name = all_main.personnel_name
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
		GROUP BY opportunity_id,personnel_name) AS sec_main ON sec_main.opportunity_id = all_main.opportunity_id AND sec_main.personnel_name = all_main.personnel_name
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
		GROUP BY opportunity_id,personnel_name) AS won_mainn ON won_mainn.opportunity_id = all_main.opportunity_id AND won_mainn.personnel_name = all_main.personnel_name
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
		GROUP BY opportunity_id,personnel_name) AS pip_log ON pip_log.opportunity_id = all_main.opportunity_id AND pip_log.personnel_name = all_main.personnel_name
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
		GROUP BY opportunity_id,personnel_name) AS wor_log ON wor_log.opportunity_id = all_main.opportunity_id AND wor_log.personnel_name = all_main.personnel_name
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
		GROUP BY opportunity_id,personnel_name) AS sub_log ON sub_log.opportunity_id = all_main.opportunity_id AND sub_log.personnel_name = all_main.personnel_name
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
		GROUP BY opportunity_id,personnel_name) AS sec_log ON sec_log.opportunity_id = all_main.opportunity_id AND sec_log.personnel_name = all_main.personnel_name
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
		GROUP BY opportunity_id,personnel_name) AS won_logg ON won_logg.opportunity_id = all_main.opportunity_id AND won_logg.personnel_name = all_main.personnel_name
		WHERE
		    (pip_main.pipeline_main != '' OR wor_main.working_main != '' OR sub_main.submitted_main != '' OR sec_main.second_stage_main != '' OR won_mainn.won_main != '' OR pip_log.pipeline_log != '' OR wor_log.working_log != '' OR sub_log.submitted_log != '' OR sec_log.second_stage_log != '' OR won_logg.won_log != '')
		GROUP BY personnel_name) AS h ON h.personnel_name = a.personnel_name
		LEFT JOIN
		(SELECT
			aa.personnel_name,
		    COUNT(DISTINCT mcca.min_client_checklist_answer) AS client_checklist_question_first_touch,
		    COUNT(DISTINCT mia.min_interview_answer) AS interview_question_follow_ups,
		    COUNT(DISTINCT mpa.min_premeeting_answer) AS premeeting_question_follow_ups
		FROM
		(SELECT
			CONCAT(u.firstName, ' ', u.lastName) AS personnel_name
		FROM
			vtechcrm.x2_users AS u
		WHERE
			CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
		GROUP BY personnel_name) AS aa
		LEFT JOIN
		(SELECT
		    CONCAT(u.firstName, ' ', u.lastName) AS personnel_name,
		    MIN(vqf.id) AS min_client_checklist_answer
		FROM
			vtech_tools.vtech_question_bank AS vqb
		    JOIN vtech_tools.vtech_question_feedback AS vqf ON vqf.question_id = vqb.id
		    JOIN vtechcrm.x2_users AS u ON u.id = vqf.user_id
		WHERE
			CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
		AND
			vqb.type = 'sales_client_qualification_checklist'
		AND
			DATE_FORMAT(vqf.created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY vqf.candidate_id) AS mcca ON mcca.personnel_name = aa.personnel_name
		LEFT JOIN
		(SELECT
		    CONCAT(u.firstName, ' ', u.lastName) AS personnel_name,
		    MIN(vqf.id) AS min_interview_answer
		FROM
			vtech_tools.vtech_question_bank AS vqb
		    JOIN vtech_tools.vtech_question_feedback AS vqf ON vqf.question_id = vqb.id
		    JOIN vtechcrm.x2_users AS u ON u.id = vqf.user_id
		WHERE
			CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
		AND
			vqb.type = 'sales_interview_questions'
		AND
			DATE_FORMAT(vqf.created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY vqf.candidate_id) AS mia ON mia.personnel_name = aa.personnel_name
		LEFT JOIN
		(SELECT
		    CONCAT(u.firstName, ' ', u.lastName) AS personnel_name,
		    MIN(vqf.id) AS min_premeeting_answer
		FROM
			vtech_tools.vtech_question_bank AS vqb
		    JOIN vtech_tools.vtech_question_feedback AS vqf ON vqf.question_id = vqb.id
		    JOIN vtechcrm.x2_users AS u ON u.id = vqf.user_id
		WHERE
			CONCAT(u.firstName, ' ', u.lastName) IN ($personnelData)
		AND
			vqb.type = 'sales_premeeting_questions'
		AND
			DATE_FORMAT(vqf.created_at, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
		GROUP BY vqf.candidate_id) AS mpa ON mpa.personnel_name = aa.personnel_name
		GROUP BY personnel_name) AS j ON j.personnel_name = a.personnel_name
		WHERE
			(b.total_accounts != '' OR b.total_contacts != '' OR c.total_first_touch != '' OR d.total_follow_ups != '' OR e.total_show_ups != '' OR f.total_requirements != '' OR g.total_call != '' OR g.total_email != '' OR g.total_comment != '' OR g.total_meeting != '' OR g.total_meaningful != '' OR h.total_pipeline != '' OR h.total_working != '' OR h.total_submitted != '' OR h.total_second_stage != '' OR h.total_won != '' OR a.personnel_status != '0')
		GROUP BY personnel_name";

		$mainRESULT = mysqli_query($sales_connect, $mainQUERY);
		
		if (mysqli_num_rows($mainRESULT) > 0) {
			while ($mainROW = mysqli_fetch_array($mainRESULT)) {
				
				$personnelName = implode("_", explode(" ", strtolower($mainROW['personnel_name'])));

				$totalAccounts = $totalContacts = $totalFirstTouch = $totalMeaningful = $totalMeetings = $totalShowUps = $totalFollowUps = $totalPipeline = $totalWorking = $totalSubmitted = $totalSecondStage = $totalWon = $totalWonDiff = $totalRequirements = 0;

				if ($mainROW["total_accounts"] != "") {
					$totalAccounts = $mainROW["total_accounts"];
				}

				if ($mainROW["total_contacts"] != "") {
					$totalContacts = $mainROW["total_contacts"];
				}

				if ($mainROW["total_first_touch"] != "") {
					$totalFirstTouch = $mainROW["total_first_touch"] + $mainROW["client_checklist_question_first_touch"];
				} else {
					$totalFirstTouch = $mainROW["client_checklist_question_first_touch"];
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
					$totalFollowUps = $mainROW["total_follow_ups"] + $mainROW["interview_question_follow_ups"] + $mainROW["premeeting_question_follow_ups"];
				} else {
					$totalFollowUps = $mainROW["interview_question_follow_ups"] + $mainROW["premeeting_question_follow_ups"];
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

				if ($mainROW["total_shared_won"] != "") {
					$totalWonDiff = $mainROW["total_shared_won"] + 0;
				}

				if ($mainROW["total_requirements"] != "") {
					$totalRequirements = $mainROW["total_requirements"];
				}

				/////// Total GP Calculation START ///////
				
				$personnelNameValue = strtolower($mainROW["personnel_name"]);

				$taxRate = $mspFees = $primeCharges = $candidateRate = $grossMargin = $totalHour = 0;

				$totalGP = array();

				$delimiter = array("","[","]",'"');

				$currentDate = date("Ym");

				$subQUERY = "SELECT
					e.id AS employee_id,
					e.employee_id AS emp_id,
					e.status AS employee_status,
					e.custom1 AS benefit,
					e.custom2 AS benefit_list,
					CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
					CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
					es.id AS employment_id,
					es.name AS employment_type,
					comp.company_id AS company_id,
					comp.name AS company_name,
					CAST((PERIOD_DIFF($currentDate,date_format(comp.date_created, '%Y%m')) / 12) AS DECIMAL(10,1)) AS cats_company_age,
					date_format(comp.date_created, '%Y-%m-%d') AS cats_company_create_date,
					(SELECT ic.value FROM mis_reports.incentive_criteria AS ic WHERE ic.personnel = 'Matrix' AND ic.comment = 'Client Age') AS given_company_age,
					(SELECT COUNT(*) AS share_amount FROM cats.extra_field AS ef WHERE ef.data_item_id = comp.company_id and ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By') AND ef.value != '') AS share_amount,
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
				    LEFT JOIN vtech_mappingdb.system_integration AS si ON e.id = si.h_employee_id
					LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
				    LEFT JOIN cats.extra_field AS ef ON ef.data_item_id = comp.company_id
					LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
					LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
				WHERE
					LOWER(ef.value) = '$personnelNameValue'
				AND
				    (ef.field_name = 'Inside Sales Person1' OR ef.field_name = 'Inside Sales Person2' OR ef.field_name = 'Research By')
				AND
					ep.project != '6'
				AND
					date_format(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
				GROUP BY employee_id";

				$subRESULT = mysqli_query($vtechhrmConn, $subQUERY);

				if (mysqli_num_rows($subRESULT) > 0) {
					while ($subROW = mysqli_fetch_array($subRESULT)) {

						$benefitList = str_replace($delimiter, $delimiter[0], $subROW["benefit_list"]);

						//$taxRate = round(employeeTaxRate($vtechMappingdbConn,$subROW["benefit"],$benefitList,$subROW["employment_id"],$subROW["pay_rate"]), 2);

						$taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$subROW["benefit"],$benefitList,$subROW["employment_id"],$subROW["pay_rate"]), 2);

						$mspFees = round((($subROW["client_msp_charge_percentage"] / 100) * $subROW["bill_rate"]) + $subROW["client_msp_charge_dollar"], 2);

						$primeCharges = round(((($subROW["client_prime_charge_percentage"] / 100) * $subROW["bill_rate"]) + (($subROW["employee_prime_charge_percentage"] / 100) * $subROW["bill_rate"]) + $subROW["employee_prime_charge_dollar"] + $subROW["employee_any_charge_dollar"] + $subROW["client_prime_charge_dollar"]), 2);

						$candidateRate = round(($subROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

						$grossMargin = round(($subROW["bill_rate"] - $candidateRate), 2);

						//$totalHour = round(employeeWorkingHours($vtechhrmConn,$startDate,$endDate,$subROW["employee_id"]), 2);

						$totalHour = round(array_sum($employeeTimeEntryTableData[$subROW["employee_id"]]), 2);

						$totalGP[] = round(($grossMargin * $totalHour) / $subROW["share_amount"], 2);
					}
				}
				/////// Total GP Calculation END ///////

				
				$teamArray["personnel_list"][] = $personnelName;
				$teamArray["new_accounts"][$personnelName][$weekListKey] = $totalAccounts;
				$teamArray["new_contacts"][$personnelName][$weekListKey] = $totalContacts;
				$teamArray["first_touch"][$personnelName][$weekListKey] = $totalFirstTouch;
				$teamArray["meaningful_conversations"][$personnelName][$weekListKey] = $totalMeaningful;
				$teamArray["meetings"][$personnelName][$weekListKey] = $totalMeetings;
				$teamArray["show_ups"][$personnelName][$weekListKey] = $totalShowUps;
				$teamArray["follow_ups"][$personnelName][$weekListKey] = $totalFollowUps;
				$teamArray["pipeline"][$personnelName][$weekListKey] = $totalPipeline;
				$teamArray["working"][$personnelName][$weekListKey] = $totalWorking;
				$teamArray["submitted"][$personnelName][$weekListKey] = $totalSubmitted;
				$teamArray["second_stage"][$personnelName][$weekListKey] = $totalSecondStage;
				$teamArray["won"][$personnelName][$weekListKey][0] = $totalWon;
				$teamArray["won"][$personnelName][$weekListKey][1] = $totalWonDiff;
				$teamArray["accounts_got_requirements"][$personnelName][$weekListKey] = $totalRequirements;
				$teamArray["total_gP_revenue"][$personnelName][$weekListKey] = array_sum($totalGP);
			}
		}
	}

	$teamPersonnelList = array_unique($teamArray['personnel_list']);

	$mail->Subject = $mailSubject = 'BDG Matrix || Team || '.date("m-d-Y (l)", strtotime('today'));

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
				<td style="font-size: 18px;font-weight: bold;">BDG Matrix Notification || <span style="color: #2266AA;">Team</span> || <span style="color: #449D44;">'.date("m-d-Y (l)", strtotime('today')).'</span></td>
			</tr>
			<tr>
				<td style="font-size: 15px;font-weight: bold;color: red;">4 : Four Weeks Ago : '.$teamArray['week_date']['four_weeks_ago'].'</td>
			</tr>
			<tr>
				<td style="font-size: 15px;font-weight: bold;c;olor: green;">3 : Three Weeks Ago : '.$teamArray['week_date']['three_weeks_ago'].'</td>
			</tr>
			<tr>
				<td style="font-size: 15px;font-weight: bold;color: blue;">2 : Two Weeks Ago : '.$teamArray['week_date']['two_weeks_ago'].'</td>
			</tr>
			<tr>
				<td style="font-size: 15px;font-weight: bold;color: indigo;">1 : Past Week : '.$teamArray['week_date']['past_week'].'</td>
			</tr>
			<tr>
				<td><br>
					<center>
					<table style="width: 100%;border: 1px solid #ddd;">
						<thead>
							<tr style="background-color: #ccc;color: #000;font-size: 14px;">
								<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="2">Title</th>';
							foreach ($teamPersonnelList as $teamPersonnelListKey => $teamPersonnelListValue) {
								$mailContent .= '<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" colspan="4">'.ucwords(implode(" ", explode("_", $teamPersonnelListValue))).'</th>';
							}
							$mailContent .= '</tr>
							<tr style="color: #fff;">';
							foreach ($teamPersonnelList as $teamPersonnelListKeys => $teamPersonnelListValues) {
							$mailContent .= '<th style="font-size: 13px;text-align: center;vertical-align: middle;background-color: red;border-right: 1px solid #ddd;">4</th>
								<th style="font-size: 13px;text-align: center;vertical-align: middle;background-color: green;border-right: 1px solid #ddd;">3</th>
								<th style="font-size: 13px;text-align: center;vertical-align: middle;background-color: blue;border-right: 1px solid #ddd;">2</th>
								<th style="font-size: 13px;text-align: center;vertical-align: middle;background-color: indigo;border-right: 1px solid #ddd;">1</th>';
							}
						$mailContent .= '</tr>
							</thead>
						<tbody>';
						array_shift($teamArray);
						array_shift($teamArray);
						foreach ($teamArray as $teamArrayKey => $teamArrayItem) {
						$mailContent.='<tr style="font-size: 15px;">
								<td style="text-align: left;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.ucwords(implode(" ", explode("_", $teamArrayKey))).'</td>';
							if ($teamArrayKey != 'won') {
								foreach ($teamArrayItem as $teamArrayItemKey => $teamArrayItemValue) {
							$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$teamArrayItem[$teamArrayItemKey]['four_weeks_ago'].'</td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$teamArrayItem[$teamArrayItemKey]['three_weeks_ago'].'</td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$teamArrayItem[$teamArrayItemKey]['two_weeks_ago'].'</td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #777;">'.$teamArrayItem[$teamArrayItemKey]['past_week'].'</td>';
								}
							} else {
								foreach ($teamArrayItem as $teamArrayItemKey => $teamArrayItemValue) {
							$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;position: relative;">'.$teamArrayItem[$teamArrayItemKey]['four_weeks_ago'][0].'<div style="font-size: 10px;color: #333;position: absolute;bottom: 0;right: 0;color: #2266AA;font-weight: bold;">'.$teamArrayItem[$teamArrayItemKey]['four_weeks_ago'][1].'</div></td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;position: relative;">'.$teamArrayItem[$teamArrayItemKey]['three_weeks_ago'][0].'<div style="font-size: 10px;color: #333;position: absolute;bottom: 0;right: 0;color: #2266AA;font-weight: bold;">'.$teamArrayItem[$teamArrayItemKey]['three_weeks_ago'][1].'</div></td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;position: relative;">'.$teamArrayItem[$teamArrayItemKey]['two_weeks_ago'][0].'<div style="font-size: 10px;color: #333;position: absolute;bottom: 0;right: 0;color: #2266AA;font-weight: bold;">'.$teamArrayItem[$teamArrayItemKey]['two_weeks_ago'][1].'</div></td>
									<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #777;position: relative;">'.$teamArrayItem[$teamArrayItemKey]['past_week'][0].'<div style="font-size: 10px;color: #333;position: absolute;bottom: 0;right: 0;color: #2266AA;font-weight: bold;">'.$teamArrayItem[$teamArrayItemKey]['past_week'][1].'</div></td>';
								}
							}
							$mailContent.='</tr>';
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
	$mail->addAddress('vishnu.n@vtechsolution.com');
	$mail->addAddress('deepak.s@vtechsolution.com');
	$mail->addAddress('michael.p@vtechsolution.com');
	$mail->addAddress('kalpesh.j@vtechsolution.com');
	$mail->addBcc('ravip@vtechsolution.us');

	echo $mailContent;
	
	include("../../functions/email-send-config.php");
?>
