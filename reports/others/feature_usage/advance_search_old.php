<?php
	include('../../../config.php');

	$multipleMonth = $_REQUEST['multipleMonth'];
	$fromDate = $_REQUEST['fromDate'];
	$toDate = $_REQUEST['toDate'];
	$multipleQuarter = $_REQUEST['multipleQuarter'];
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
		COUNT(ascl.id) AS view_count";

	$query = "SELECT
		count(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_count,
	    IF(cjsh.status_to = '400', 'submission', IF(cjsh.status_to = '500', 'interview', IF(cjsh.status_to = '560', 'interview_declined', IF(cjsh.status_to = '600', 'offer', IF(cjsh.status_to = '800', 'placed', IF(cjsh.status_to = '620', 'extension', IF(cjsh.status_to = '900', 'delivery_failed', 'Other'))))))) AS status_data";

	if ($multipleMonth !== '') {
		$query .=", EXTRACT(YEAR_MONTH FROM cjsh.date) as date_data";
		$viewCountQuery .=", EXTRACT(YEAR_MONTH FROM ascl.date) as date_data";
	}

	if ($multipleQuarter !== '') {
		$query .=", YEAR(cjsh.date) AS year, QUARTER(cjsh.date) as quarter, CONCAT(YEAR(cjsh.date), 'Q', QUARTER(cjsh.date)) as date_data";
		$viewCountQuery .=", YEAR(ascl.date) AS year, QUARTER(ascl.date) as quarter, CONCAT(YEAR(ascl.date), 'Q', QUARTER(ascl.date)) as date_data";
	}

	$query .= " FROM cats.candidate_joborder_status_history AS cjsh";

	$viewCountQuery .= " FROM vtech_mappingdb.advance_search_candidate_log AS ascl";

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

		$query .= " WHERE
			cjsh.status_to IN (400,500,560,600,800,620,900)
		AND
			cjsh.candidate_id IN (SELECT ascl.cats_candidate_id FROM vtech_mappingdb.advance_search_candidate_log AS ascl WHERE EXTRACT(YEAR_MONTH FROM ascl.date) IN (".$multipleMonth."))
		AND
			EXTRACT(YEAR_MONTH FROM cjsh.date) IN (".$multipleMonth.")
		GROUP BY status_data, date_data
		ORDER BY date_data";

		$viewCountQuery .= " WHERE EXTRACT(YEAR_MONTH FROM ascl.date) IN (".$multipleMonth.")
		GROUP BY date_data
		ORDER BY date_data";
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

		$query .= " WHERE
			cjsh.status_to IN (400,500,560,600,800,620,900)
		AND
			cjsh.candidate_id IN (SELECT ascl.cats_candidate_id FROM vtech_mappingdb.advance_search_candidate_log AS ascl WHERE CONCAT(YEAR(ascl.date), 'Q', QUARTER(ascl.date)) in ('".$multipleQuarter."'))
		AND
			CONCAT(YEAR(cjsh.date), 'Q', QUARTER(cjsh.date)) in ('".$multipleQuarter."')
		GROUP BY status_data,year, quarter
		ORDER BY year DESC, quarter DESC";

		$viewCountQuery .= " WHERE CONCAT(YEAR(ascl.date), 'Q', QUARTER(ascl.date)) in ('".$multipleQuarter."')
		GROUP BY year, quarter
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

		$query .= " WHERE
			cjsh.status_to IN (400,500,560,600,800,620,900)
		AND
			cjsh.candidate_id IN (SELECT ascl.cats_candidate_id FROM vtech_mappingdb.advance_search_candidate_log AS ascl WHERE ascl.date BETWEEN '".$fromDate[2]."-".$fromDate[0]."-".$fromDate[1]." 00:00:00' and '".$toDate[2]."-".$toDate[0]."-".$toDate[1]." 23:59:59')
		AND
			cjsh.date BETWEEN '".$fromDate[2]."-".$fromDate[0]."-".$fromDate[1]." 00:00:00' and '".$toDate[2]."-".$toDate[0]."-".$toDate[1]." 23:59:59'
		GROUP BY status_data";

		$viewCountQuery .= " WHERE ascl.date BETWEEN '".$fromDate[2]."-".$fromDate[0]."-".$fromDate[1]." 00:00:00' and '".$toDate[2]."-".$toDate[0]."-".$toDate[1]." 23:59:59'";
	}

	$viewCountResult = mysqli_query($allConn, $viewCountQuery) or die('viewCountQuery Error');
	while ($viewCountRow = mysqli_fetch_assoc($viewCountResult)) {
		foreach ($templateUsage as $key => $value) {
			if ($value["Status"] == "total_view") {
				if (isset($viewCountRow["date_data"]) && isset($value[$viewCountRow["date_data"]]) && ($isMultipleMonth || $isMultipleQuarter)) {
					$templateUsage[$key][$viewCountRow["date_data"]] = $viewCountRow["view_count"];
				} else {
					$templateUsage[$key]["date_range"] = $viewCountRow["view_count"];
				}
			}
		}
	}

	$resultQuery = mysqli_query($allConn, $query) or die('Query Error');
	while($resultRow = mysqli_fetch_assoc($resultQuery)) {
		foreach ($templateUsage as $key => $value) {
			if ($value["Status"] == $resultRow["status_data"]) {
				if (isset($resultRow["date_data"]) && isset($value[$resultRow["date_data"]]) && ($isMultipleMonth || $isMultipleQuarter)) {
					$templateUsage[$key][$resultRow["date_data"]] = $resultRow["total_count"];
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
			if ($key == 'Status') {
				$result .='<th align="center">'.$templateList[$value].'</th>';
			} else {
				$result .='<td align="center" '.($value == 0 ? "class=\"red\"" : "").'>'. $value.'</td>';
			}
		}

		$result .= '</tr>';
	}
	$result .= '</tbody></table>';
	echo $result;
?>
