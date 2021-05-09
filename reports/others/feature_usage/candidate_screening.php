<?php
	include('../../../config.php');

	$multipleMonth = $_REQUEST['multipleMonth'];
	$fromDate = $_REQUEST['fromDate'];
	$toDate = $_REQUEST['toDate'];
	$multipleQuarter = $_REQUEST['multipleQuarter'];
	$isMultipleMonth = true;
	$isMultipleQuarter = false;
	$templateUsage = $displayDateList = array();

	$templateList = array(
		"screening_level_one" => "Screening Level-1",
		"screening_level_two" => "Screening Level-2",
		"pre_resume_feedback" => "Pre Submission PS Team Feedback on Resume",
		"post_resume_feedback" => "Post Submission Client Feedback on Resume",
		"post_candidate_feedback" => "Post Interview Candidate Feedback",
		"post_client_feedback" => "Post Interview Client Feedback"
	);

	foreach ($templateList as $key => $value) {
		$templateUsage[] = array("template" => $key);
	}

	$query = "SELECT
		count(id) as total_count,
		screening_type";

	if ($multipleMonth !== '') {
		$query .=", EXTRACT(YEAR_MONTH FROM created_at) as datec";
	}

	if ($multipleQuarter !== '') {
		$query .=", YEAR(created_at) AS year, QUARTER(created_at) as quarter, CONCAT(YEAR(created_at), 'Q', QUARTER(created_at)) as datec";
	}

	$query .= " FROM vtech_tools.vtech_feedback_mapping";

	if ($multipleMonth !== '') {
		$isMultipleQuarter = false;
		$multipleMonth = explode(',', $multipleMonth);
		$monthlyData = array();

		foreach($multipleMonth as $index => $value) {
			$value = explode('/', $value);
			$multipleMonth[$index] = $value[1].$value[0];
			$monthlyData[$multipleMonth[$index]] = 0;
			$displayDateList[$multipleMonth[$index]] = implode($value, '/');
		}

		krsort($monthlyData);

		foreach ($templateUsage as $key => $value) {
			$templateUsage[$key] = $value + $monthlyData;
		}

		$multipleMonth = implode($multipleMonth, ',');

		$query .= " WHERE EXTRACT(YEAR_MONTH FROM created_at) IN (".$multipleMonth.")
					GROUP BY screening_type, datec
					ORDER BY  datec";
	} elseif ($multipleQuarter !== '') {
		$isMultipleMonth = false;
		$isMultipleQuarter = true;
		$multipleQuarter = explode(',', $multipleQuarter);
		$monthlyData = array();

		foreach($multipleQuarter as $index => $value) {
			$value = explode('/', $value);
			$multipleQuarter[$index] = $value[1].$value[0];
			$monthlyData[$multipleQuarter[$index]] = 0;
			$displayDateList[$multipleQuarter[$index]] = implode($value, '/');
		}

		krsort($monthlyData);

		foreach ($templateUsage as $key => $value) {
			$templateUsage[$key] = $value + $monthlyData;
		}

		$multipleQuarter = implode($multipleQuarter, "','");

		$query .= " WHERE CONCAT(YEAR(created_at), 'Q', QUARTER(created_at)) in ('".$multipleQuarter."')
					GROUP BY screening_type, year, quarter
					ORDER BY year DESC, quarter DESC";
	} else {
		$isMultipleMonth = false;
		$isMultipleQuarter = false;
		$displayDateList['date_range'] = $fromDate.' - '.$toDate;

		$fromDate = explode('/', $fromDate);
		$toDate = explode('/', $toDate);

		foreach ($templateUsage as $key => $value) {
			$templateUsage[$key] = $value + array("date_range" => 0);
		}

		$query .= " WHERE created_at BETWEEN '".$fromDate[2]."-".$fromDate[0]."-".$fromDate[1]." 00:00:00' and '".$toDate[2]."-".$toDate[0]."-".$toDate[1]." 23:59:59'
					GROUP BY screening_type";
	}

	$resultQuery = mysqli_query($allConn, $query) or die('Query Error');

	while($resultRow = mysqli_fetch_assoc($resultQuery)) {
		foreach ($templateUsage as $key => $value) {
			if ($value["template"] == $resultRow["screening_type"]) {
				if (isset($resultRow["datec"]) && isset($value[$resultRow["datec"]]) && ($isMultipleMonth || $isMultipleQuarter)) {
					$templateUsage[$key][$resultRow["datec"]] = $resultRow["total_count"];
				} else {
					$templateUsage[$key]["date_range"] = $resultRow["total_count"];
				}
			}
		}
	}

	$tableHeaders = array_keys($templateUsage[0]);

	$result = '<table class="table table-bordered table-striped"><thead><tr class="bg-primary">';

	foreach ($tableHeaders as $value) {
		$result .= '<th>'.(isset($displayDateList[$value]) ? $displayDateList[$value]: $value).'</th>';
	}

	$result .= '</tr></thead><tbody>';

	foreach ($templateUsage as $valueList) {
		$result	.= '<tr>';

		foreach ($valueList as $key => $value) {
			if ($key == 'template') {
				$result .='<th align="center">'.$templateList[$value].'</th>';
			} else {
				if ($value == 0){
					$result .='<td align="center" class="red">'. $value.'</td>';
				} else {
					if ($isMultipleMonth == 1 && $isMultipleQuarter == 0) {
						$dateGiven = explode("/", $displayDateList[$key]);
						$dateModified = $dateGiven[1]."-".$dateGiven[0];
			
						($firstDateForPopup = date("Y-m-01", strtotime($dateModified)));
						($lastDateForPopup = date("Y-m-t", strtotime($dateModified)));
			
					} elseif ($isMultipleMonth == 0 && $isMultipleQuarter == 1) {
						$dateGiven = explode("/", $displayDateList[$key]);
						$dateModified = $dateGiven[1]."-".$dateGiven[0];

						if ($dateGiven[0] == "Q1") {
							$firstDateForPopup = $dateGiven[1]."-01-01";
							$lastDateForPopup = $dateGiven[1]."-03-31";
						} elseif ($dateGiven[0] == "Q2") {
							$firstDateForPopup = $dateGiven[1]."-04-01";
							$lastDateForPopup = $dateGiven[1]."-06-30";
						} elseif ($dateGiven[0] == "Q3") {
							$firstDateForPopup = $dateGiven[1]."-07-01";
							$lastDateForPopup = $dateGiven[1]."-09-31";
						} elseif ($dateGiven[0] == "Q4") {
							$firstDateForPopup = $dateGiven[1]."-10-01";
							$lastDateForPopup = $dateGiven[1]."-12-31";
						}
					} else {
						$dateGiven = explode("-", $displayDateList[$key]);
						$firstDateForPopup = date("Y-m-d", strtotime($dateGiven[0]));
						$lastDateForPopup = date("Y-m-d", strtotime($dateGiven[1]));
					}
									
					$result .='<td align="center"><a style="cursor: pointer;" class="candidate-screening-popup hyper-link-text" data-popup="'.$firstDateForPopup.'/'.$lastDateForPopup.'" data-status="'.$valueList['template'].'" data-titlename="'.$templateList[$valueList['template']].' ('.$displayDateList[$key].')" >';
					$result .= $value;
					$result .='</a></td>';
				
				}	 
			}
		}

		$result .= '</tr>';
	}
	$result .= '</tbody></table>';
	
	echo $result;
?>
