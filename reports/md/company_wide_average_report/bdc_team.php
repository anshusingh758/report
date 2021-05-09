<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../functions/reporting-service.php");
?>
	<script>
		$(document).ready(function(){
			var bdcTeamReport = $(".bdc-team-report").DataTable({
			    dom: "Bfrtip",
			    "bPaginate": false,
			    "bFilter": false,
			    bInfo: false,
		        buttons:[
		        ]
			});
		});
	</script>
<?php
	if ($_POST) {
		$output = $dateRangeType = "";
		$dateRange = $newEmployeeArray = $totalReport = $averageDailyReport = $averageReport = array();

		if ($_POST["multipleMonth"] != "") {
			$dateRange = monthDateRange(array_unique(explode(",", $_POST["multipleMonth"])));
		} elseif ($_POST["multipleQuarter"] != "") {
			$dateRange = quarterDateRange(array_unique(explode(",", $_POST["multipleQuarter"])));
		} elseif ($_POST["startDate"] != "" && $_POST["endDate"] != "") {
			$dateRange = normalDateRange($_POST["startDate"], $_POST["endDate"]);
		}

		$dateRangeType = $dateRange["filter_type"];

		array_shift($dateRange);

		$personnelData = "'".implode("', '", salesGroupPersonnelList($sales_connect,"2"))."'";

		$taxSettingsTableData = taxSettingsTable($allConn);

		foreach ($dateRange as $dateRangeKey => $dateRangeValue) {
			$startDate = $dateRangeValue["start_date"];
			$endDate = $dateRangeValue["end_date"];

			$employeeTimeEntryTableData = employeeTimeEntryTable($allConn,$startDate,$endDate);

			$totalDays = round((strtotime($dateRangeValue["end_date"]) - strtotime($dateRangeValue["start_date"])) / (60 * 60 * 24) + 1);

			$filterValue = $dateRangeValue["filter_value"];

			$joinedUserQUERY = mysqli_query($allConn, "SELECT
			    mu.id AS user_id,
			    LOWER(CONCAT(TRIM(mu.firstname),' ',TRIM(mu.lastname))) AS user_name,
			    mes.date_of_joining
			FROM
			    vtechhrm_in.main_users AS mu
			    JOIN vtechhrm_in.main_employees AS me ON me.user_id = mu.id
			    JOIN vtechhrm_in.main_employees_summary AS mes ON mes.user_id = me.user_id
			WHERE
			    me.department_id IN (5,36,39)
			AND
			    mes.date_of_joining BETWEEN '$startDate' AND '$endDate'
			GROUP BY mu.id");

			while ($joinedUserROW = mysqli_fetch_array($joinedUserQUERY)) {
			    $joinedUserName = $joinedUserROW["user_name"];

			    $firstDayQUERY = mysqli_query($allConn, "SELECT
			        a.personnel_name,
			        b.first_account_date,
			        c.first_contact_date,
			        d.first_meaningful_date,
			        d.first_meeting_date,
			        IF((e.first_log_opportunity_date IS NOT NULL OR e.first_log_opportunity_date != ''), e.first_log_opportunity_date, IF((e.first_opportunity_date IS NOT NULL OR e.first_opportunity_date != ''), e.first_opportunity_date, '')) AS first_opportunity_date,
			        IF((e.first_log_won_date IS NOT NULL OR e.first_log_won_date != ''), e.first_log_won_date, IF((e.first_won_date IS NOT NULL OR e.first_won_date != ''), e.first_won_date, '')) AS first_won_date,
			        f.first_revenue_date
			    FROM
			    (SELECT '$joinedUserName' AS personnel_name) AS a
			    LEFT JOIN
			    (SELECT
			        LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) AS personnel_name,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%Y-%m-%d')) AS first_account_date
			    FROM
			        vtechcrm.x2_events AS e
			        LEFT JOIN vtechcrm.x2_accounts AS act ON act.id = e.associationId
			        LEFT JOIN vtechcrm.x2_users AS u ON e.user = u.username
			    WHERE
			        LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) = '$joinedUserName'
			    AND
			        e.associationType = 'Accounts'
			    AND
			        e.type = 'record_create'
			    GROUP BY personnel_name) AS b ON b.personnel_name = a.personnel_name
			    LEFT JOIN
			    (SELECT
			        LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) AS personnel_name,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(e.timestamp), '%Y-%m-%d')) AS first_contact_date
			    FROM
			        vtechcrm.x2_events AS e
			        LEFT JOIN vtechcrm.x2_contacts AS c ON c.id = e.associationId
			        LEFT JOIN vtechcrm.x2_users AS u ON e.user = u.username
			    WHERE
			        LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) = '$joinedUserName'
			    AND
			        e.associationType = 'Contacts'
			    AND
			        e.type = 'record_create'
			    GROUP BY personnel_name) AS c ON c.personnel_name = a.personnel_name
			    LEFT JOIN
			    (SELECT
			        LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) AS personnel_name,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(mf.createDate), '%Y-%m-%d')) AS first_meaningful_date,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(me.createDate), '%Y-%m-%d')) AS first_meeting_date
			    FROM
			        vtechcrm.x2_users AS u
			        LEFT JOIN vtechcrm.x2_actions AS mf ON mf.completedBy = u.username AND mf.associationType IN ('accounts','contacts','opportunities') AND mf.type = 'meaningfulData'
			        LEFT JOIN vtechcrm.x2_actions AS me ON me.completedBy = u.username AND me.associationType IN ('accounts','contacts','opportunities') AND me.type = 'event'
			    WHERE
			        LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) = '$joinedUserName'
			    GROUP BY personnel_name) AS d ON d.personnel_name = a.personnel_name
			    LEFT JOIN
			    (SELECT
			        LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) AS personnel_name,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(work.createDate), '%Y-%m-%d')) AS first_opportunity_date,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(work_chg.timestamp), '%Y-%m-%d')) AS first_log_opportunity_date,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(won.createDate), '%Y-%m-%d')) AS first_won_date,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(won_chg.timestamp), '%Y-%m-%d')) AS first_log_won_date
			    FROM
			        vtechcrm.x2_users AS u
			        LEFT JOIN vtechcrm.x2_opportunities AS work ON (work.assignedTo = u.username OR work.c_research_by = u.username) AND work.salesStage = 'Working'
			        LEFT JOIN vtechcrm.x2_changelog AS work_chg ON work_chg.itemId = work.id AND work_chg.type = 'Opportunity' AND work_chg.fieldName = 'salesStage' AND work_chg.newValue = 'Working'

			        LEFT JOIN vtechcrm.x2_opportunities AS won ON (won.assignedTo = u.username OR won.c_research_by = u.username) AND won.salesStage = 'Won'
			        LEFT JOIN vtechcrm.x2_changelog AS won_chg ON won_chg.itemId = work.id AND won_chg.type = 'Opportunity' AND won_chg.fieldName = 'salesStage' AND won_chg.newValue = 'Won'
			    WHERE
			        LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) = '$joinedUserName'
			    GROUP BY personnel_name) AS e ON e.personnel_name = a.personnel_name
			    LEFT JOIN
			    (SELECT
			        LOWER(ef.value) AS personnel_name,
			        MIN(DATE_FORMAT(ete.date_start, '%Y-%m-%d')) AS first_revenue_date
			    FROM
			        cats.extra_field AS ef
			        LEFT JOIN vtech_mappingdb.system_integration AS si ON (si.c_inside_sales1 = ef.value OR si.c_inside_sales2 = ef.value OR si.c_research_by = ef.value)
			        LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = si.h_employee_id
			    WHERE
			        LOWER(ef.value) = '$joinedUserName'
			    GROUP BY personnel_name) AS f ON f.personnel_name = a.personnel_name");

			    $firstDayROW = mysqli_fetch_array($firstDayQUERY);

			    $newEmployeeArray[$joinedUserROW["user_id"]] = array(
			        "user_name" => ucwords($joinedUserROW["user_name"]),
			        "date_of_joining" => $joinedUserROW["date_of_joining"],
			        "first_account_date" => $firstDayROW["first_account_date"],
			        "first_account_days" => $firstDayROW["first_account_date"] == "" ? "0" : round((strtotime($firstDayROW["first_account_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
			        "first_contact_date" => $firstDayROW["first_contact_date"],
			        "first_contact_days" => $firstDayROW["first_contact_date"] == "" ? "0" : round((strtotime($firstDayROW["first_contact_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
			        "first_meaningful_date" => $firstDayROW["first_meaningful_date"],
			        "first_meaningful_days" => $firstDayROW["first_meaningful_date"] == "" ? "0" : round((strtotime($firstDayROW["first_meaningful_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
			        "first_meeting_date" => $firstDayROW["first_meeting_date"],
			        "first_meeting_days" => $firstDayROW["first_meeting_date"] == "" ? "0" : round((strtotime($firstDayROW["first_meeting_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
			        "first_opportunity_date" => $firstDayROW["first_opportunity_date"],
			        "first_opportunity_days" => $firstDayROW["first_opportunity_date"] == "" ? "0" : round((strtotime($firstDayROW["first_opportunity_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
			        "first_won_date" => $firstDayROW["first_won_date"],
			        "first_won_days" => $firstDayROW["first_won_date"] == "" ? "0" : round((strtotime($firstDayROW["first_won_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
			        "first_revenue_date" => $firstDayROW["first_revenue_date"],
			        "first_revenue_days" => $firstDayROW["first_revenue_date"] == "" ? "0" : round((strtotime($firstDayROW["first_revenue_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24))
			    );
			}

			$taxRate = $mspFees = $primeCharges = $candidateRate = "";

			$totalGP = array();

			$delimiter = array("","[","]",'"');

			$subQUERY = "SELECT
				ef.value AS personnel,
				e.id AS employee_id,
				e.employee_id AS emp_id,
				e.status AS employee_status,
				e.custom1 AS benefit,
				e.custom2 AS benefit_list,
				CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
				CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
				es.id AS employment_id,
				es.name AS employment_type,
				si.c_company_id AS company_id,
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
				LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
				LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
				LEFT JOIN cats.extra_field AS ef ON ef.data_item_id = comp.company_id
				LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
				LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
			WHERE
			    ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By')
			AND
				ef.value IN ($personnelData)
			AND
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY employee_id,personnel";

			$subRESULT = mysqli_query($allConn, $subQUERY);

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

			$totalGpPerHour = round(array_sum($totalGP), 2);

			$mainQUERY = "SELECT
				COUNT(DISTINCT a.personnel_name) AS total_personnel,
				SUM(a.personnel_live_status) AS total_active_personnel,
			    IF((SUM(b.total_accounts) IS NULL OR SUM(b.total_accounts) = ''), 0, SUM(b.total_accounts)) AS total_accounts,
			    IF((SUM(b.total_contacts) IS NULL OR SUM(b.total_contacts) = ''), 0, SUM(b.total_contacts)) AS total_contacts,
			    IF((SUM(c.total_first_touch) IS NULL OR SUM(c.total_first_touch) = ''), 0, SUM(c.total_first_touch)) AS total_first_touch,
			    SUM(g.total_meaningful) AS total_meaningful,
			    SUM(g.total_meeting) AS total_meeting,
			    IF((SUM(e.total_show_ups) IS NULL OR SUM(e.total_show_ups) = ''), 0, SUM(e.total_show_ups)) AS total_show_ups,
			    IF((SUM(d.total_follow_ups) IS NULL OR SUM(d.total_follow_ups) = ''), 0, SUM(d.total_follow_ups)) AS total_follow_ups,
			    SUM(h.total_pipeline) AS total_pipeline,
			    SUM(h.total_working) AS total_working,
			    SUM(h.total_submitted) AS total_submitted,
			    SUM(h.total_second_stage) AS total_second_stage,
			    SUM(h.total_won) AS total_won,
			    SUM(h.total_shared_won) AS total_shared_won,
			    IF((SUM(f.total_requirements) IS NULL OR SUM(f.total_requirements) = ''), 0, SUM(f.total_requirements)) AS total_requirements,
			    IF((SUM(i.resources_working) IS NULL OR SUM(i.resources_working) = ''), 0, SUM(i.resources_working)) AS resources_working,
			    SUM(g.total_call) AS total_call,
			    SUM(g.total_email) AS total_email,
			    SUM(g.total_comment) AS total_comment
			FROM
			(SELECT
				CONCAT(u.firstName,' ',u.lastName) AS personnel_name,
				u.status AS personnel_status,
				IF(u.status != 0, 1, 0) AS personnel_live_status
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
				ef.value_map AS personnel_name,
				COUNT(DISTINCT e.id) AS resources_working
			FROM
				vtechhrm.employees AS e
				JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
				JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
				JOIN vtechhrm.employeetimeentry AS ete ON e.id = ete.employee
				JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
				JOIN cats.company AS comp ON comp.company_id = si.c_company_id
				JOIN cats.contract_mapping AS ef ON ef.data_item_id = comp.company_id
			WHERE
				ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By')
			AND
				ef.value_map IN ($personnelData)
			AND
				ep.project != '6'
			AND
				DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
			GROUP BY personnel_name) AS i ON i.personnel_name = a.personnel_name
			WHERE
				(b.total_accounts != '' OR b.total_contacts != '' OR c.total_first_touch != '' OR d.total_follow_ups != '' OR e.total_show_ups != '' OR f.total_requirements != '' OR g.total_call != '' OR g.total_email != '' OR g.total_comment != '' OR g.total_meeting != '' OR g.total_meaningful != '' OR h.total_pipeline != '' OR h.total_working != '' OR h.total_submitted != '' OR h.total_second_stage != '' OR h.total_won != '' OR i.resources_working != '' OR a.personnel_status != '0')";

			$mainRESULT = mysqli_query($sales_connect, $mainQUERY);

			if (mysqli_num_rows($mainRESULT) > 0) {
				while ($mainROW = mysqli_fetch_array($mainRESULT)) {
					$totalReport[] = array(
						"daterange_type" => $filterValue,
						"total_personnel" => $mainROW["total_personnel"],
						"total_active_personnel" => $mainROW["total_active_personnel"],
						"total_accounts" => $mainROW["total_accounts"],
						"total_contacts" => $mainROW["total_contacts"],
						"total_first_touch" => $mainROW["total_first_touch"],
						"total_meaningful" => $mainROW["total_meaningful"],
						"total_meeting" => $mainROW["total_meeting"],
						"total_show_ups" => $mainROW["total_show_ups"],
						"total_follow_ups" => $mainROW["total_follow_ups"],
						"total_pipeline" => $mainROW["total_pipeline"],
						"total_working" => $mainROW["total_working"],
						"total_submitted" => $mainROW["total_submitted"],
						"total_second_stage" => $mainROW["total_second_stage"],
						"total_won" => $mainROW["total_won"] + 0,
						"total_requirements" => $mainROW["total_requirements"],
						"resources_working" => $mainROW["resources_working"],
						"total_gp_per_hour" => $totalGpPerHour,
						"total_new_employees" => count($newEmployeeArray)
					);

					$averageDailyReport[] = array(
						"daterange_type" => $filterValue,
						"total_accounts" => round((($mainROW["total_accounts"] / $mainROW["total_personnel"])), 2),
						"total_contacts" => round((($mainROW["total_contacts"] / $mainROW["total_personnel"])), 2),
						"total_first_touch" => round((($mainROW["total_first_touch"] / $mainROW["total_personnel"])), 2),
						"total_meaningful" => round((($mainROW["total_meaningful"] / $mainROW["total_personnel"])), 2),
						"total_meeting" => round((($mainROW["total_meeting"] / $mainROW["total_personnel"])), 2),
						"total_show_ups" => round((($mainROW["total_show_ups"] / $mainROW["total_personnel"])), 2),
						"total_follow_ups" => round((($mainROW["total_follow_ups"] / $mainROW["total_personnel"])), 2),
						"total_pipeline" => round((($mainROW["total_pipeline"] / $mainROW["total_personnel"])), 2),
						"total_working" => round((($mainROW["total_working"] / $mainROW["total_personnel"])), 2),
						"total_submitted" => round((($mainROW["total_submitted"] / $mainROW["total_personnel"])), 2),
						"total_second_stage" => round((($mainROW["total_second_stage"] / $mainROW["total_personnel"])), 2),
						"total_won" => round((($mainROW["total_won"] / $mainROW["total_personnel"])), 3),
						"total_requirements" => round((($mainROW["total_requirements"] / $mainROW["total_personnel"])), 2),
						"resources_working" => round((($mainROW["resources_working"] / $mainROW["total_personnel"])), 2),
						"total_gp_per_hour" => round((($totalGpPerHour / $mainROW["total_personnel"])), 2),

						"average_accounts" => round((array_sum(array_column($newEmployeeArray, "first_account_days"))), 2),
						"average_contacts" => round((array_sum(array_column($newEmployeeArray, "first_contact_days"))), 2),
						"average_meaningful" => round((array_sum(array_column($newEmployeeArray, "first_meaningful_days"))), 2),
						"average_meeting" => round((array_sum(array_column($newEmployeeArray, "first_meeting_days"))), 2),
						"average_opportunity" => round((array_sum(array_column($newEmployeeArray, "first_opportunity_days"))), 2),
						"average_won" => round((array_sum(array_column($newEmployeeArray, "first_won_days"))), 2),
						"average_revenue" => round((array_sum(array_column($newEmployeeArray, "first_revenue_days"))), 2)
					);

					$averageReport[] = array(
						"daterange_type" => $filterValue,
						"total_accounts" => round((($mainROW["total_accounts"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_contacts" => round((($mainROW["total_contacts"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_first_touch" => round((($mainROW["total_first_touch"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_meaningful" => round((($mainROW["total_meaningful"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_meeting" => round((($mainROW["total_meeting"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_show_ups" => round((($mainROW["total_show_ups"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_follow_ups" => round((($mainROW["total_follow_ups"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_pipeline" => round((($mainROW["total_pipeline"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_working" => round((($mainROW["total_working"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_submitted" => round((($mainROW["total_submitted"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_second_stage" => round((($mainROW["total_second_stage"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_won" => round((($mainROW["total_won"] / $mainROW["total_personnel"]) / $totalDays), 3),
						"total_requirements" => round((($mainROW["total_requirements"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"resources_working" => round((($mainROW["resources_working"] / $mainROW["total_personnel"]) / $totalDays), 2),
						"total_gp_per_hour" => round((($totalGpPerHour / $mainROW["total_personnel"]) / $totalDays), 2),

						"average_accounts" => round((array_sum(array_column($newEmployeeArray, "first_account_days")) / count($newEmployeeArray)), 2),
						"average_contacts" => round((array_sum(array_column($newEmployeeArray, "first_contact_days")) / count($newEmployeeArray)), 2),
						"average_meaningful" => round((array_sum(array_column($newEmployeeArray, "first_meaningful_days")) / count($newEmployeeArray)), 2),
						"average_meeting" => round((array_sum(array_column($newEmployeeArray, "first_meeting_days")) / count($newEmployeeArray)), 2),
						"average_opportunity" => round((array_sum(array_column($newEmployeeArray, "first_opportunity_days")) / count($newEmployeeArray)), 2),
						"average_won" => round((array_sum(array_column($newEmployeeArray, "first_won_days")) / count($newEmployeeArray)), 2),
						"average_revenue" => round((array_sum(array_column($newEmployeeArray, "first_revenue_days")) / count($newEmployeeArray)), 2)
					);
				}
			}
		}
		
		if (array_sum(array_column($totalReport, "total_personnel")) == 0) {
			$output = '<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<img src="'.IMAGE_PATH.'/no-record-found.png">
				</div>
			</div>';
		} else {
			$output = '<div class="row">
				<div class="col-md-4 col-md-offset-4 report-headline">
					Total Report
				</div>
				<div class="col-md-12">
					<table class="table table-striped table-bordered bdc-team-report">
						<thead>
							<tr class="thead-tr-style">';
							if ($dateRangeType == "month") {
								$output .= '<th>Months</th>';
							} elseif ($dateRangeType == "quarter") {
								$output .= '<th>Quarters</th>';
							}
								$output .= '<th data-toggle="tooltip" data-placement="top" title="Active Employees">Team<br>Size</th>
								<th>New Accounts</th>
								<th>New Contacts</th>
								<th>First Touch</th>
								<th>Meaningful Conversions</th>
								<th>Appointments / Meetings</th>
								<th>ShowUps</th>
								<th>FollowUps</th>
								<th>Pipeline</th>
								<th>Working</th>
								<th>Submitted</th>
								<th>Second Stage</th>
								<th>Won</th>
								<th>Accounts<br>Got<br>Requirements</th>
								<th>GP Revenue</th>
								<th>New Team Members Joined</th>
							</tr>
						</thead>
						<tbody>';
						foreach ($totalReport as $totalReportKey => $totalReportValue) {
							$output .= '<tr>';
								if ($dateRangeType != "daterange") {
									$output .= '<td>'.$totalReportValue["daterange_type"].'</td>';
								}
								$output .= '<td>'.$totalReportValue["total_active_personnel"].'</td>
								<td>'.$totalReportValue["total_accounts"].'</td>
								<td>'.$totalReportValue["total_contacts"].'</td>
								<td>'.$totalReportValue["total_first_touch"].'</td>
								<td>'.$totalReportValue["total_meaningful"].'</td>
								<td>'.$totalReportValue["total_meeting"].'</td>
								<td>'.$totalReportValue["total_show_ups"].'</td>
								<td>'.$totalReportValue["total_follow_ups"].'</td>
								<td>'.$totalReportValue["total_pipeline"].'</td>
								<td>'.$totalReportValue["total_working"].'</td>
								<td>'.$totalReportValue["total_submitted"].'</td>
								<td>'.$totalReportValue["total_second_stage"].'</td>
								<td>'.$totalReportValue["total_won"].'</td>
								<td>'.$totalReportValue["total_requirements"].'</td>
								<td>'.$totalReportValue["total_gp_per_hour"].'</td>
								<td>'.$totalReportValue["total_new_employees"].'</td>
							</tr>';
						}
					$output .= '</tbody>
					</table>
				</div>
			</div>';

			$output .= '<div class="row" style="margin-top: 10px;">
				<div class="col-md-4 col-md-offset-4 report-headline">
					Average Daily Report
				</div>
				<div class="col-md-12">
					<table class="table table-striped table-bordered bdc-team-report">
						<thead>
							<tr class="thead-tr-style">';
							if ($dateRangeType == "month") {
								$output .= '<th rowspan="2">Months</th>';
							} elseif ($dateRangeType == "quarter") {
								$output .= '<th rowspan="2">Quarters</th>';
							}
								$output .= '<th colspan="14">Average Daily</th>
								<th colspan="7">Average time taken to make first (in day)</th>
							</tr>
							<tr class="thead-tr-style">
								<th>New Accounts</th>
								<th>New Contacts</th>
								<th>First Touch</th>
								<th>Meaningful Conversions</th>
								<th>Appointments / Meetings</th>
								<th>ShowUps</th>
								<th>FollowUps</th>
								<th>Pipeline</th>
								<th>Working</th>
								<th>Submitted</th>
								<th>Second Stage</th>
								<th>Won</th>
								<th>Accounts<br>Got<br>Requirements</th>
								<th>GP Revenue</th>
								<th>New Accounts</th>
								<th>New Contacts</th>
								<th>Meaningful Conversions</th>
								<th>Appointments / Meetings</th>
								<th>Working Opportunity</th>
								<th>Won</th>
								<th>GP Revenue</th>
							</tr>
						</thead>
						<tbody>';
						foreach ($averageDailyReport as $averageDailyReportKey => $averageDailyReportValue) {
							$output .= '<tr>';
								if ($dateRangeType != "daterange") {
									$output .= '<td>'.$averageDailyReportValue["daterange_type"].'</td>';
								}
								$output .= '<td>'.$averageDailyReportValue["total_accounts"].'</td>
								<td>'.$averageDailyReportValue["total_contacts"].'</td>
								<td>'.$averageDailyReportValue["total_first_touch"].'</td>
								<td>'.$averageDailyReportValue["total_meaningful"].'</td>
								<td>'.$averageDailyReportValue["total_meeting"].'</td>
								<td>'.$averageDailyReportValue["total_show_ups"].'</td>
								<td>'.$averageDailyReportValue["total_follow_ups"].'</td>
								<td>'.$averageDailyReportValue["total_pipeline"].'</td>
								<td>'.$averageDailyReportValue["total_working"].'</td>
								<td>'.$averageDailyReportValue["total_submitted"].'</td>
								<td>'.$averageDailyReportValue["total_second_stage"].'</td>
								<td>'.$averageDailyReportValue["total_won"].'</td>
								<td>'.$averageDailyReportValue["total_requirements"].'</td>
								<td>'.$averageDailyReportValue["total_gp_per_hour"].'</td>
								<td>'.$averageDailyReportValue["average_accounts"].'</td>
								<td>'.$averageDailyReportValue["average_contacts"].'</td>
								<td>'.$averageDailyReportValue["average_meaningful"].'</td>
								<td>'.$averageDailyReportValue["average_meeting"].'</td>
								<td>'.$averageDailyReportValue["average_opportunity"].'</td>
								<td>'.$averageDailyReportValue["average_won"].'</td>
								<td>'.$averageDailyReportValue["average_revenue"].'</td>
							</tr>';
						}
					$output .= '</tbody>
					</table>
				</div>
			</div>';

			$output .= '<div class="row" style="margin-top: 10px;">
				<div class="col-md-4 col-md-offset-4 report-headline">
					Average Per Person Report
				</div>
				<div class="col-md-12">
					<table class="table table-striped table-bordered bdc-team-report">
						<thead>
							<tr class="thead-tr-style">';
							if ($dateRangeType == "month") {
								$output .= '<th rowspan="2">Months</th>';
							} elseif ($dateRangeType == "quarter") {
								$output .= '<th rowspan="2">Quarters</th>';
							}
								$output .= '<th colspan="14">Average Daily (per Personnel)</th>
								<th colspan="7">Average time taken to make first by New Personnel (in day)</th>
							</tr>
							<tr class="thead-tr-style">
								<th>New Accounts</th>
								<th>New Contacts</th>
								<th>First Touch</th>
								<th>Meaningful Conversions</th>
								<th>Appointments / Meetings</th>
								<th>ShowUps</th>
								<th>FollowUps</th>
								<th>Pipeline</th>
								<th>Working</th>
								<th>Submitted</th>
								<th>Second Stage</th>
								<th>Won</th>
								<th>Accounts<br>Got<br>Requirements</th>
								<th>GP Revenue</th>
								<th>New Accounts</th>
								<th>New Contacts</th>
								<th>Meaningful Conversions</th>
								<th>Appointments / Meetings</th>
								<th>Working Opportunity</th>
								<th>Won</th>
								<th>GP Revenue</th>
							</tr>
						</thead>
						<tbody>';
						foreach ($averageReport as $averageReportKey => $averageReportValue) {
							$output .= '<tr>';
								if ($dateRangeType != "daterange") {
									$output .= '<td>'.$averageReportValue["daterange_type"].'</td>';
								}
								$output .= '<td>'.$averageReportValue["total_accounts"].'</td>
								<td>'.$averageReportValue["total_contacts"].'</td>
								<td>'.$averageReportValue["total_first_touch"].'</td>
								<td>'.$averageReportValue["total_meaningful"].'</td>
								<td>'.$averageReportValue["total_meeting"].'</td>
								<td>'.$averageReportValue["total_show_ups"].'</td>
								<td>'.$averageReportValue["total_follow_ups"].'</td>
								<td>'.$averageReportValue["total_pipeline"].'</td>
								<td>'.$averageReportValue["total_working"].'</td>
								<td>'.$averageReportValue["total_submitted"].'</td>
								<td>'.$averageReportValue["total_second_stage"].'</td>
								<td>'.$averageReportValue["total_won"].'</td>
								<td>'.$averageReportValue["total_requirements"].'</td>
								<td>'.$averageReportValue["total_gp_per_hour"].'</td>
								<td>'.$averageReportValue["average_accounts"].'</td>
								<td>'.$averageReportValue["average_contacts"].'</td>
								<td>'.$averageReportValue["average_meaningful"].'</td>
								<td>'.$averageReportValue["average_meeting"].'</td>
								<td>'.$averageReportValue["average_opportunity"].'</td>
								<td>'.$averageReportValue["average_won"].'</td>
								<td>'.$averageReportValue["average_revenue"].'</td>
							</tr>';
						}
					$output .= '</tbody>
					</table>
				</div>
			</div>';
		}

		echo $output;
	}
?>