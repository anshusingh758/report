<?php
	include('../../../config.php');

	function monthRange($dateRange) {
		foreach($dateRange as $dateRangeKey => $dateRangeValue) {
			$dateRangeValue = explode('/', $dateRangeValue);
			if($dateRange[0] == ''){
				$output = '';
				return $output;
			} else{
				$output[$dateRangeKey] = $dateRangeValue[1].$dateRangeValue[0];
			}
		}	
		return implode(",", $output);
	}

	function quarterRange($dateRange) {
		$output = array();

		foreach($dateRange as $dateRangeKey => $dateRangeValue) {
			$dateRangeValue = explode('/', $dateRangeValue);
			
			if ($dateRangeValue[0] == "Q1") {
				$output[$dateRangeKey] = "'".$dateRangeValue[1]."01', '".$dateRangeValue[1]."02', '".$dateRangeValue[1]."03'";
			} elseif ($dateRangeValue[0] == "Q2") {
				$output[$dateRangeKey] = "'".$dateRangeValue[1]."04', '".$dateRangeValue[1]."05', '".$dateRangeValue[1]."06'";
			} elseif ($dateRangeValue[0] == "Q3") {
				$output[$dateRangeKey] = "'".$dateRangeValue[1]."07', '".$dateRangeValue[1]."08', '".$dateRangeValue[1]."09'";
			} elseif ($dateRangeValue[0] == "Q4") {
				$output[$dateRangeKey] = "'".$dateRangeValue[1]."10', '".$dateRangeValue[1]."11', '".$dateRangeValue[1]."12'";
			}
		}

		return implode(",", $output);
	}

	function fullDateRange($startDate, $endDate) {
		return array(
			0 => date("Y-m-d", strtotime($startDate)),
			1 => date("Y-m-d", strtotime($endDate))
		);
	}

	$monthRange = monthRange(array_unique(explode(",", $_REQUEST["multipleMonth"])));

	$quarterRange = quarterRange(array_unique(explode(",", $_REQUEST["multipleQuarter"])));

	$dateRange = fullDateRange($_REQUEST["fromDate"], $_REQUEST["toDate"]);

	$isMultipleMonth = true;
	$isMultipleQuarter = false;

	$templateUsage = $displayDateList = $viewCount = array();

	$templateList = array(
		"total_view" => "Total View",
		"submission" => "Submission",
		"interview" => "Interview",
		"interview_declined" => "Interview Declined",
		"offer" => "Offer",
		"placed" => "Placed",
		"extension" => "Extension",
		"delivery_failed" => "Delivery Failed"
	);

	foreach ($templateList as $key => $value) {
		$templateUsage[] = array("Status" => $key);
	}

	$viewCountQuery = "SELECT
		COUNT(DISTINCT ascl.cats_candidate_id) AS view_count";

	$searchQuery = "SELECT
		count(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_count,
	    IF(cjsh.status_to = '400', 'submission', IF(cjsh.status_to = '500', 'interview', IF(cjsh.status_to = '560', 'interview_declined', IF(cjsh.status_to = '600', 'offer', IF(cjsh.status_to = '800', 'placed', IF(cjsh.status_to = '620', 'extension', IF(cjsh.status_to = '900', 'delivery_failed', 'Other'))))))) AS status_data";

	if ($monthRange !== '') {
		
		$viewCountQuery .= ", EXTRACT(YEAR_MONTH FROM ascl.date) as date_data";
	
		$searchQuery .= ", EXTRACT(YEAR_MONTH FROM cjsh.date) as date_data";

	}

	if ($quarterRange !== '') {
		
		$viewCountQuery .= ", CONCAT(YEAR(ascl.date), 'Q', QUARTER(ascl.date)) as date_data";
	
		$searchQuery .= ", CONCAT(YEAR(cjsh.date), 'Q', QUARTER(cjsh.date)) as date_data";
		
	}

	$viewCountQuery .= " FROM sovren.ai_matching_candidate_log AS ascl";

	$searchQuery .= " FROM
		sovren.ai_matching_candidate_log AS ascl
		LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.candidate_id = ascl.cats_candidate_id";

	if ($monthRange !== '') {

		$isMultipleQuarter = false;

		$multipleMonth = explode(',', $_REQUEST["multipleMonth"]);
		$monthlyData = array();

		foreach($multipleMonth as $multipleMonthKey => $multipleMonthValue) {
			$multipleMonthValue = explode('/', $multipleMonthValue);
			$multipleMonth[$multipleMonthKey] = $multipleMonthValue[1].$multipleMonthValue[0];
			$monthlyData[$multipleMonth[$multipleMonthKey]] = 0;
			$displayDateList[$multipleMonth[$multipleMonthKey]] = implode($multipleMonthValue, '/');
		}

		krsort($monthlyData);

		foreach ($templateUsage as $templateUsageKey => $templateUsageValue) {
			$templateUsage[$templateUsageKey] = $templateUsageValue + $monthlyData;
		}

		$viewCountQuery .= " WHERE EXTRACT(YEAR_MONTH FROM ascl.date) IN ($monthRange)
		GROUP BY date_data
		ORDER BY date_data";

		$searchQuery .= " WHERE
			cjsh.status_to IN (400,500,560,600,800,620,900)
		AND
			EXTRACT(YEAR_MONTH FROM ascl.date) IN ($monthRange)
		AND
			EXTRACT(YEAR_MONTH FROM cjsh.date) = EXTRACT(YEAR_MONTH FROM ascl.date)
		GROUP BY status_data, date_data
		ORDER BY date_data";

	} elseif ($quarterRange !== '') {

		$isMultipleMonth = false;
		$isMultipleQuarter = true;

		$multipleQuarter = explode(',', $_REQUEST["multipleQuarter"]);
		$quarterlyData = array();

		foreach($multipleQuarter as $multipleQuarterKey => $multipleQuarterValue) {
			$multipleQuarterValue = explode('/', $multipleQuarterValue);
			$multipleQuarter[$multipleQuarterKey] = $multipleQuarterValue[1].$multipleQuarterValue[0];
			$quarterlyData[$multipleQuarter[$multipleQuarterKey]] = 0;
			$displayDateList[$multipleQuarter[$multipleQuarterKey]] = implode($multipleQuarterValue, '/');
		}

		krsort($quarterlyData);

		foreach ($templateUsage as $templateUsageKey => $templateUsageValue) {
			$templateUsage[$templateUsageKey] = $templateUsageValue + $quarterlyData;
		}

		$viewCountQuery .= " WHERE
			EXTRACT(YEAR_MONTH FROM ascl.date) IN ($quarterRange)
		GROUP BY date_data
		ORDER BY date_data";

		$searchQuery .= " WHERE
			cjsh.status_to IN (400,500,560,600,800,620,900)
		AND
			EXTRACT(YEAR_MONTH FROM ascl.date) IN ($quarterRange)
		AND
			EXTRACT(YEAR_MONTH FROM cjsh.date) = EXTRACT(YEAR_MONTH FROM ascl.date)
		GROUP BY status_data, date_data
		ORDER BY date_data";

	} else {

		$isMultipleMonth = $isMultipleQuarter = false;

		$displayDateList['date_range'] = $_REQUEST["fromDate"].' - '.$_REQUEST["toDate"];

		foreach ($templateUsage as $templateUsageKey => $templateUsageValue) {
			$templateUsage[$templateUsageKey] = $templateUsageValue + array("date_range" => 0);
		}

		$viewCountQuery .= " WHERE DATE_FORMAT(ascl.date, '%Y-%m-%d') BETWEEN '$dateRange[0]' AND '$dateRange[1]'";

		$searchQuery .= " WHERE
			cjsh.status_to IN (400,500,560,600,800,620,900)
		AND
			DATE_FORMAT(ascl.date, '%Y-%m-%d') BETWEEN '$dateRange[0]' AND '$dateRange[1]'
		AND
			EXTRACT(YEAR_MONTH FROM cjsh.date) = EXTRACT(YEAR_MONTH FROM ascl.date)
		GROUP BY status_data";

	}
	
	$viewCountResult = mysqli_query($allConn, $viewCountQuery);
	
	while ($viewCountRow = mysqli_fetch_array($viewCountResult)) {
		foreach ($templateUsage as $templateUsageKey => $templateUsageValue) {
			if ($templateUsageValue["Status"] == "total_view") {
				if (isset($viewCountRow["date_data"]) && isset($templateUsageValue[$viewCountRow["date_data"]]) && ($isMultipleMonth || $isMultipleQuarter)) {
					$templateUsage[$templateUsageKey][$viewCountRow["date_data"]] = $viewCountRow["view_count"];
				} else {
					$templateUsage[$templateUsageKey]["date_range"] = $viewCountRow["view_count"];
				}
			}
		}
	}

	$searchResult = mysqli_query($allConn, $searchQuery);
	
	while($searchRow = mysqli_fetch_assoc($searchResult)) {
		foreach ($templateUsage as $templateUsagekey => $templateUsageValue) {
			if ($templateUsageValue["Status"] == $searchRow["status_data"]) {
				if (isset($searchRow["date_data"]) && isset($templateUsageValue[$searchRow["date_data"]]) && ($isMultipleMonth || $isMultipleQuarter)) {
					$templateUsage[$templateUsagekey][$searchRow["date_data"]] = $searchRow["total_count"];
				} else {
					$templateUsage[$templateUsagekey]["date_range"] = $searchRow["total_count"];
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
			if ($key == 'Status') {
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
									
					$result .='<td align="center"><a style="cursor: pointer;" class="ai-matching-detail-popup hyper-link-text" data-popup="'.$firstDateForPopup.'/'.$lastDateForPopup.'" data-status="'.$valueList['Status'].'" data-titlename="'.ucwords(implode(" ", explode("_", $valueList['Status']))).' ('.$displayDateList[$key].')" >';
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
