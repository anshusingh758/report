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
		$dateRange = $newEmployeeArray = array();

		if ($_POST["multipleMonth"] != "") {
			$dateRange = monthDateRange(array_unique(explode(",", $_POST["multipleMonth"])));
		} elseif ($_POST["multipleQuarter"] != "") {
			$dateRange = quarterDateRange(array_unique(explode(",", $_POST["multipleQuarter"])));
		} elseif ($_POST["startDate"] != "" && $_POST["endDate"] != "") {
			$dateRange = normalDateRange($_POST["startDate"], $_POST["endDate"]);
		}

		$dateRangeType = $dateRange["filter_type"];

		array_shift($dateRange);

		foreach ($dateRange as $dateRangeKey => $dateRangeValue) {
			$startDate = $dateRangeValue["start_date"];
			$endDate = $dateRangeValue["end_date"];

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

			        IF((e.first_log_pipeline_date IS NOT NULL OR e.first_log_pipeline_date != ''), e.first_log_pipeline_date, IF((e.first_pipeline_date IS NOT NULL OR e.first_pipeline_date != ''), e.first_pipeline_date, '')) AS first_pipeline_date,

			        IF((e.first_log_working_date IS NOT NULL OR e.first_log_working_date != ''), e.first_log_working_date, IF((e.first_working_date IS NOT NULL OR e.first_working_date != ''), e.first_working_date, '')) AS first_working_date,

			        IF((e.first_log_submitted_date IS NOT NULL OR e.first_log_submitted_date != ''), e.first_log_submitted_date, IF((e.first_submitted_date IS NOT NULL OR e.first_submitted_date != ''), e.first_submitted_date, '')) AS first_submitted_date,

			        IF((e.first_log_second_stage_date IS NOT NULL OR e.first_log_second_stage_date != ''), e.first_log_second_stage_date, IF((e.first_second_stage_date IS NOT NULL OR e.first_second_stage_date != ''), e.first_second_stage_date, '')) AS first_second_stage_date,

			        IF((e.first_log_won_date IS NOT NULL OR e.first_log_won_date != ''), e.first_log_won_date, IF((e.first_won_date IS NOT NULL OR e.first_won_date != ''), e.first_won_date, '')) AS first_won_date,

			        f.first_revenue_date,

			        g.first_first_touch_date,
			        g.first_followup_date,

			        h.first_showup_date,

			        i.first_requirement_date
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

			        MIN(DATE_FORMAT(FROM_UNIXTIME(pipe.createDate), '%Y-%m-%d')) AS first_pipeline_date,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(pipe_chg.timestamp), '%Y-%m-%d')) AS first_log_pipeline_date,

			        MIN(DATE_FORMAT(FROM_UNIXTIME(work.createDate), '%Y-%m-%d')) AS first_working_date,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(work_chg.timestamp), '%Y-%m-%d')) AS first_log_working_date,

			        MIN(DATE_FORMAT(FROM_UNIXTIME(sub.createDate), '%Y-%m-%d')) AS first_submitted_date,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(sub_chg.timestamp), '%Y-%m-%d')) AS first_log_submitted_date,

			        MIN(DATE_FORMAT(FROM_UNIXTIME(sec_stage.createDate), '%Y-%m-%d')) AS first_second_stage_date,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(sec_stage_chg.timestamp), '%Y-%m-%d')) AS first_log_second_stage_date,

			        MIN(DATE_FORMAT(FROM_UNIXTIME(won.createDate), '%Y-%m-%d')) AS first_won_date,
			        MIN(DATE_FORMAT(FROM_UNIXTIME(won_chg.timestamp), '%Y-%m-%d')) AS first_log_won_date
			    FROM
			        vtechcrm.x2_users AS u

			        LEFT JOIN vtechcrm.x2_opportunities AS pipe ON (pipe.assignedTo = u.username OR pipe.c_research_by = u.username) AND pipe.salesStage = 'Pipeline'
			        LEFT JOIN vtechcrm.x2_changelog AS pipe_chg ON pipe_chg.itemId = pipe.id AND pipe_chg.type = 'Opportunity' AND pipe_chg.fieldName = 'salesStage' AND pipe_chg.newValue = 'Pipeline'

			        LEFT JOIN vtechcrm.x2_opportunities AS work ON (work.assignedTo = u.username OR work.c_research_by = u.username) AND work.salesStage = 'Working'
			        LEFT JOIN vtechcrm.x2_changelog AS work_chg ON work_chg.itemId = work.id AND work_chg.type = 'Opportunity' AND work_chg.fieldName = 'salesStage' AND work_chg.newValue = 'Working'

			        LEFT JOIN vtechcrm.x2_opportunities AS sub ON (sub.assignedTo = u.username OR sub.c_research_by = u.username) AND sub.salesStage = 'Submitted'
			        LEFT JOIN vtechcrm.x2_changelog AS sub_chg ON sub_chg.itemId = sub.id AND sub_chg.type = 'Opportunity' AND sub_chg.fieldName = 'salesStage' AND sub_chg.newValue = 'Submitted'

			        LEFT JOIN vtechcrm.x2_opportunities AS sec_stage ON (sec_stage.assignedTo = u.username OR sec_stage.c_research_by = u.username) AND sec_stage.salesStage = 'Second Stage'
			        LEFT JOIN vtechcrm.x2_changelog AS sec_stage_chg ON sec_stage_chg.itemId = sec_stage.id AND sec_stage_chg.type = 'Opportunity' AND sec_stage_chg.fieldName = 'salesStage' AND sec_stage_chg.newValue = 'Second Stage'

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
			    GROUP BY personnel_name) AS f ON f.personnel_name = a.personnel_name
			    LEFT JOIN
			    (SELECT
					LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) AS personnel_name,
					MIN(DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d')) AS first_first_touch_date,
					MIN(DATE_FORMAT(FROM_UNIXTIME(act.createDate), '%Y-%m-%d')) AS first_followup_date
				FROM
					vtechcrm.x2_actions AS act
					LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
				WHERE
					LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) = '$joinedUserName'
				AND
					act.associationType IN ('accounts','contacts','opportunities')
				AND
					act.type IN ('note','call','emaildata','meaningfulData','event')
				GROUP BY personnel_name) AS g ON g.personnel_name = a.personnel_name
				LEFT JOIN
				(SELECT
					LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) AS personnel_name,
				    MIN(DATE_FORMAT(FROM_UNIXTIME(act.completeDate), '%Y-%m-%d')) AS first_showup_date
				FROM
				    vtechcrm.x2_actions AS act
				    LEFT JOIN vtechcrm.x2_users AS u ON u.username = act.completedBy
				WHERE
					LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) = '$joinedUserName'
				AND
					act.type = 'event'
				AND
					act.complete = 'Yes'
				GROUP BY personnel_name) AS h ON h.personnel_name = a.personnel_name
				LEFT JOIN
				(SELECT
				    LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) AS personnel_name,
				    MIN(DATE_FORMAT(job.date_created, '%Y-%m-%d')) AS first_requirement_date
				FROM
					contract.x2_opportunities AS copp
				    LEFT JOIN vtechcrm.x2_users AS u ON u.username = copp.assignedTo OR u.username = copp.c_research_by
				   	LEFT JOIN cats.contract_mapping AS cm ON cm.field_name = 'Contract No' AND cm.value_map = copp.c_solicitation_number
				    LEFT JOIN cats.joborder AS job ON job.company_id = cm.data_item_id
				WHERE
					LOWER(CONCAT(TRIM(u.firstName),' ',TRIM(u.lastName))) = '$joinedUserName'
				GROUP BY personnel_name) AS i ON i.personnel_name = a.personnel_name");

			    $firstDayROW = mysqli_fetch_array($firstDayQUERY);

			    $newEmployeeArray[] = array(
			    	"user_id" => $joinedUserROW["user_id"],
			        "user_name" => ucwords($joinedUserROW["user_name"]),
			        "date_of_joining" => $joinedUserROW["date_of_joining"],

			        "daterange_type" => $filterValue,

			        "first_account_date" => $firstDayROW["first_account_date"],
			        "first_account_days" => $firstDayROW["first_account_date"] == "" ? "0" : round((strtotime($firstDayROW["first_account_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_contact_date" => $firstDayROW["first_contact_date"],
			        "first_contact_days" => $firstDayROW["first_contact_date"] == "" ? "0" : round((strtotime($firstDayROW["first_contact_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_first_touch_date" => $firstDayROW["first_first_touch_date"],
			        "first_first_touch_days" => $firstDayROW["first_first_touch_date"] == "" ? "0" : round((strtotime($firstDayROW["first_first_touch_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_meaningful_date" => $firstDayROW["first_meaningful_date"],
			        "first_meaningful_days" => $firstDayROW["first_meaningful_date"] == "" ? "0" : round((strtotime($firstDayROW["first_meaningful_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_meeting_date" => $firstDayROW["first_meeting_date"],
			        "first_meeting_days" => $firstDayROW["first_meeting_date"] == "" ? "0" : round((strtotime($firstDayROW["first_meeting_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_showup_date" => $firstDayROW["first_showup_date"],
			        "first_showup_days" => $firstDayROW["first_showup_date"] == "" ? "0" : round((strtotime($firstDayROW["first_showup_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_followup_date" => $firstDayROW["first_followup_date"],
			        "first_followup_days" => $firstDayROW["first_followup_date"] == "" ? "0" : round((strtotime($firstDayROW["first_followup_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_pipeline_date" => $firstDayROW["first_pipeline_date"],
			        "first_pipeline_days" => $firstDayROW["first_pipeline_date"] == "" ? "0" : round((strtotime($firstDayROW["first_pipeline_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_working_date" => $firstDayROW["first_working_date"],
			        "first_working_days" => $firstDayROW["first_working_date"] == "" ? "0" : round((strtotime($firstDayROW["first_working_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_submitted_date" => $firstDayROW["first_submitted_date"],
			        "first_submitted_days" => $firstDayROW["first_submitted_date"] == "" ? "0" : round((strtotime($firstDayROW["first_submitted_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_second_stage_date" => $firstDayROW["first_second_stage_date"],
			        "first_second_stage_days" => $firstDayROW["first_second_stage_date"] == "" ? "0" : round((strtotime($firstDayROW["first_second_stage_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_won_date" => $firstDayROW["first_won_date"],
			        "first_won_days" => $firstDayROW["first_won_date"] == "" ? "0" : round((strtotime($firstDayROW["first_won_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_requirement_date" => $firstDayROW["first_requirement_date"],
			        "first_requirement_days" => $firstDayROW["first_requirement_date"] == "" ? "0" : round((strtotime($firstDayROW["first_requirement_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),

			        "first_revenue_date" => $firstDayROW["first_revenue_date"],
			        "first_revenue_days" => $firstDayROW["first_revenue_date"] == "" ? "0" : round((strtotime($firstDayROW["first_revenue_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24))
			    );
			}
		}

		$output = '<div class="row">
			<div class="col-md-12">
				<table class="table table-striped table-bordered bdc-team-report">
					<thead>
						<tr class="thead-tr-style">
							<th rowspan="2">Personnel</th>';
						if ($dateRangeType == "month") {
							$output .= '<th rowspan="2">Months</th>';
						} elseif ($dateRangeType == "quarter") {
							$output .= '<th rowspan="2">Quarters</th>';
						}
							$output .= '<th rowspan="2">Joining Date</th>
							<th colspan="14">Total No. of Days for First</th>
						</tr>
						<tr class="thead-tr-style">
							<th>Account</th>
							<th>Contact</th>
							<th>First Touch</th>
							<th>Meaningful Conversion</th>
							<th>Appointment / Meeting</th>
							<th>ShowUp</th>
							<th>FollowUp</th>
							<th>Pipeline</th>
							<th>Working</th>
							<th>Submitted</th>
							<th>Second Stage</th>
							<th>Won</th>
							<th>Account<br>Got<br>Requirement</th>
							<th>GP Revenue</th>
						</tr>
					</thead>
					<tbody>';
					foreach ($newEmployeeArray as $newEmployeeKey => $newEmployeeValue) {
						$output .= '<tr>
							<td>'.$newEmployeeValue["user_name"].'</td>';
							if ($dateRangeType != "daterange") {
								$output .= '<td>'.$newEmployeeValue["daterange_type"].'</td>';
							}
							$output .= '<td>'.$newEmployeeValue["date_of_joining"].'</td>
							<td>'.$newEmployeeValue["first_account_days"].'</td>
							<td>'.$newEmployeeValue["first_contact_days"].'</td>
							<td>'.$newEmployeeValue["first_first_touch_days"].'</td>
							<td>'.$newEmployeeValue["first_meaningful_days"].'</td>
							<td>'.$newEmployeeValue["first_meeting_days"].'</td>
							<td>'.$newEmployeeValue["first_showup_days"].'</td>
							<td>'.$newEmployeeValue["first_followup_days"].'</td>
							<td>'.$newEmployeeValue["first_pipeline_days"].'</td>
							<td>'.$newEmployeeValue["first_working_days"].'</td>
							<td>'.$newEmployeeValue["first_submitted_days"].'</td>
							<td>'.$newEmployeeValue["first_second_stage_days"].'</td>
							<td>'.$newEmployeeValue["first_won_days"].'</td>
							<td>'.$newEmployeeValue["first_requirement_days"].'</td>
							<td>'.$newEmployeeValue["first_revenue_days"].'</td>
						</tr>';
					}
				$output .= '</tbody>
				</table>
			</div>
		</div>';

		echo $output;
	}
?>