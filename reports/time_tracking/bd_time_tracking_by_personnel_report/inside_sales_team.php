<?php
	error_reporting(0);
	include_once("../../../config.php");
	include_once("../../../functions/reporting-service.php");
?>
	<script>
		$(document).ready(function(){
			var insideSalesTeamReport = $(".inside-sales-team-report").DataTable({
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
			    CONCAT(mu.firstname,' ',mu.lastname) AS user_name,
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
			        ef.value AS personnel,
					MIN(DATE_FORMAT(sub.date, '%Y-%m-%d')) AS first_submission_date,
					MIN(DATE_FORMAT(inter.date, '%Y-%m-%d')) AS first_interview_date,
					MIN(DATE_FORMAT(inter_dec.date, '%Y-%m-%d')) AS first_interview_decline_date,
					MIN(DATE_FORMAT(offer.date, '%Y-%m-%d')) AS first_offer_date,
					MIN(DATE_FORMAT(place.date, '%Y-%m-%d')) AS first_placement_date,
					MIN(DATE_FORMAT(ext.date, '%Y-%m-%d')) AS first_extension_date,
					MIN(DATE_FORMAT(fail_del.date, '%Y-%m-%d')) AS first_failed_delivery_date,
					MIN(DATE_FORMAT(ete.date_start, '%Y-%m-%d')) AS first_gp_date
			    FROM
			        cats.extra_field AS ef
			        LEFT JOIN cats.joborder AS j ON j.company_id = ef.data_item_id
			        
			        LEFT JOIN cats.candidate_joborder_status_history AS sub ON sub.joborder_id = j.joborder_id AND sub.status_to = '400'
			        
			        LEFT JOIN cats.candidate_joborder_status_history AS inter ON inter.joborder_id = j.joborder_id AND inter.status_to = '500'
			        
			        LEFT JOIN cats.candidate_joborder_status_history AS inter_dec ON inter_dec.joborder_id = j.joborder_id AND inter_dec.status_to = '560'
			        
			        LEFT JOIN cats.candidate_joborder_status_history AS offer ON offer.joborder_id = j.joborder_id AND offer.status_to = '600'
			        
			        LEFT JOIN cats.candidate_joborder_status_history AS place ON place.joborder_id = j.joborder_id AND place.status_to = '800'
			        
			        LEFT JOIN cats.candidate_joborder_status_history AS ext ON ext.joborder_id = j.joborder_id AND ext.status_to = '620'
			        
			        LEFT JOIN cats.candidate_joborder_status_history AS fail_del ON fail_del.joborder_id = j.joborder_id AND fail_del.status_to = '900'
			        
			        LEFT JOIN vtech_mappingdb.system_integration AS si ON (si.c_inside_sales1 = ef.value OR si.c_inside_sales2 = ef.value OR si.c_research_by = ef.value)
			        LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = si.h_employee_id
			    WHERE
			        ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By')
			    AND
			        ef.value = '$joinedUserName'
			    GROUP BY personnel");

			    $firstDayROW = mysqli_fetch_array($firstDayQUERY);

			    $newEmployeeArray[] = array(
			    	"user_id" => $joinedUserROW["user_id"],
			        "user_name" => ucwords($joinedUserROW["user_name"]),
			        "date_of_joining" => $joinedUserROW["date_of_joining"],
					
					"daterange_type" => $filterValue,

					"first_submission_date" => $newEmployeeROW["first_submission_date"],
					"first_submission_days" => $newEmployeeROW["first_submission_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_submission_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
					
					"first_interview_date" => $newEmployeeROW["first_interview_date"],
					"first_interview_days" => $newEmployeeROW["first_interview_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_interview_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
					
					"first_interview_decline_date" => $newEmployeeROW["first_interview_decline_date"],
					"first_interview_decline_days" => $newEmployeeROW["first_interview_decline_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_interview_decline_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
					
					"first_offer_date" => $newEmployeeROW["first_offer_date"],
					"first_offer_days" => $newEmployeeROW["first_offer_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_offer_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
					
					"first_placement_date" => $newEmployeeROW["first_placement_date"],
					"first_placement_days" => $newEmployeeROW["first_placement_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_placement_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
					
					"first_extension_date" => $newEmployeeROW["first_extension_date"],
					"first_extension_days" => $newEmployeeROW["first_extension_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_extension_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
					
					"first_failed_delivery_date" => $newEmployeeROW["first_failed_delivery_date"],
					"first_failed_delivery_days" => $newEmployeeROW["first_failed_delivery_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_failed_delivery_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24)),
					
					"first_gp_date" => $newEmployeeROW["first_gp_date"],
					"first_gp_days" => $newEmployeeROW["first_gp_date"] == "" ? "0" : round((strtotime($newEmployeeROW["first_gp_date"]) - strtotime($joinedUserROW["date_of_joining"])) / (60 * 60 * 24))
			    );    
			}

		}
		
		$output = '<div class="row">
			<div class="col-md-12">
				<table class="table table-striped table-bordered cs-team-report">
					<thead>
						<tr class="thead-tr-style">
							<th rowspan="2">Personnel</th>';
						if ($dateRangeType == "month") {
							$output .= '<th rowspan="2">Months</th>';
						} elseif ($dateRangeType == "quarter") {
							$output .= '<th rowspan="2">Quarters</th>';
						}
							$output .= '<th rowspan="2">Joining Date</th>
							<th colspan="8">Total No. of Days for First</th>
						</tr>
						<tr class="thead-tr-style">
							<th>Submission</th>
							<th>Interview</th>
							<th>Interview Decline</th>
							<th>Offer</th>
							<th>Placed</th>
							<th>Extension</th>
							<th>Delivery Failed</th>
							<th>GP / Revenue</th>
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
							<td>'.$newEmployeeValue["first_submission_days"].'</td>
							<td>'.$newEmployeeValue["first_interview_days"].'</td>
							<td>'.$newEmployeeValue["first_interview_decline_days"].'</td>
							<td>'.$newEmployeeValue["first_offer_days"].'</td>
							<td>'.$newEmployeeValue["first_placement_days"].'</td>
							<td>'.$newEmployeeValue["first_extension_days"].'</td>
							<td>'.$newEmployeeValue["first_failed_delivery_days"].'</td>
							<td>'.$newEmployeeValue["first_gp_days"].'</td>
						</tr>';
					}
				$output .= '</tbody>
				</table>
			</div>
		</div>';

		echo $output;
	}
?>