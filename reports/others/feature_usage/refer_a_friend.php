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
		"referred" => "Referred", 
		"resume_upload" => "Resume Upload",
		"apply_count" => "Apply Count"
	);

	foreach ($templateList as $key => $value) {
		$templateUsage[] = array("Type" => $key);
	}

	$referredCountQuery = "SELECT count(id) AS referred";

	$uploadCountQuery = "SELECT count(id) AS resume_upload";

	$applyCountQuery = "SELECT count(id) AS apply_count";

	if ($multipleMonth !== '') {
		$referredCountQuery .=", EXTRACT(YEAR_MONTH FROM rfi.date_created) as datec";

		$uploadCountQuery .=", EXTRACT(YEAR_MONTH FROM rci.date_created) as datec";

		$applyCountQuery .=", EXTRACT(YEAR_MONTH FROM rji.date_created) as datec";
	}

	if ($multipleQuarter !== '') {
		$referredCountQuery .=", YEAR(rfi.date_created) AS year, QUARTER(rfi.date_created) as quarter, CONCAT(YEAR(rfi.date_created), 'Q', QUARTER(rfi.date_created)) as datec";

		$uploadCountQuery .=", YEAR(rci.date_created) AS year, QUARTER(rci.date_created) as quarter, CONCAT(YEAR(rci.date_created), 'Q', QUARTER(rci.date_created)) as datec";

		$applyCountQuery .=", YEAR(rji.date_created) AS year, QUARTER(rji.date_created) as quarter, CONCAT(YEAR(rji.date_created), 'Q', QUARTER(rji.date_created)) as datec";
	}

	$referredCountQuery .= " FROM vtech_mappingdb.referral_info AS rfi";

	$uploadCountQuery .= " FROM vtech_mappingdb.referral_cats_info AS rci";

	$applyCountQuery .= " FROM vtech_mappingdb.referral_jobs_info AS rji";

	// For multiple months
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

		$referredCountQuery .= " WHERE EXTRACT(YEAR_MONTH FROM rfi.date_created) IN (".$multipleMonth.")
								 GROUP BY datec
								 ORDER BY  datec";

		$uploadCountQuery .= " WHERE EXTRACT(YEAR_MONTH FROM rci.date_created) IN (".$multipleMonth.")
							   GROUP BY datec
							   ORDER BY  datec";

		$applyCountQuery .= " WHERE EXTRACT(YEAR_MONTH FROM rji.date_created) IN (".$multipleMonth.")
							  GROUP BY datec
							  ORDER BY  datec";

	} elseif ($multipleQuarter !== '') {
		// For multiple quaters
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

		$referredCountQuery .= " WHERE CONCAT(YEAR(rfi.date_created), 'Q', QUARTER(rfi.date_created)) in ('".$multipleQuarter."')
					GROUP BY year, quarter
					ORDER BY year DESC, quarter DESC";

		$uploadCountQuery .= " WHERE CONCAT(YEAR(rci.date_created), 'Q', QUARTER(rci.date_created)) in ('".$multipleQuarter."')
					GROUP BY year, quarter
					ORDER BY year DESC, quarter DESC";

		$applyCountQuery .= " WHERE CONCAT(YEAR(rji.date_created), 'Q', QUARTER(rji.date_created)) in ('".$multipleQuarter."')
					GROUP BY year, quarter
					ORDER BY year DESC, quarter DESC";
	} else {
		// For dates range
		$isMultipleMonth = false;
		$isMultipleQuarter = false;
		$displayDateList['date_range'] = $fromDate.' - '.$toDate;
		$fromDate = explode('/', $fromDate);
		$toDate = explode('/', $toDate);

		foreach ($templateUsage as $key => $value) {
			$templateUsage[$key] = $value + array("date_range" => 0);
		}

		$referredCountQuery .= " WHERE rfi.date_created BETWEEN '".$fromDate[2]."-".$fromDate[0]."-".$fromDate[1]." 00:00:00' and '".$toDate[2]."-".$toDate[0]."-".$toDate[1]." 23:59:59'";

		$uploadCountQuery .= " WHERE rci.date_created BETWEEN '".$fromDate[2]."-".$fromDate[0]."-".$fromDate[1]." 00:00:00' and '".$toDate[2]."-".$toDate[0]."-".$toDate[1]." 23:59:59'";

		$applyCountQuery .= " WHERE rji.date_created BETWEEN '".$fromDate[2]."-".$fromDate[0]."-".$fromDate[1]." 00:00:00' and '".$toDate[2]."-".$toDate[0]."-".$toDate[1]." 23:59:59'";
	}

	$referredCountResult = mysqli_query($allConn, $referredCountQuery);
	$referredCount = mysqli_num_rows($referredCountResult);

	while ($referredCountRow = mysqli_fetch_array($referredCountResult)) {
		foreach ($templateUsage as $templateUsageKey => $templateUsageValue) {
			if ($templateUsageValue["Type"] == "referred") {
				if (isset($referredCountRow["datec"]) && isset($templateUsageValue[$referredCountRow["datec"]]) && ($isMultipleMonth || $isMultipleQuarter)) {
					$templateUsage[$templateUsageKey][$referredCountRow["datec"]] = $referredCountRow["referred"];
				} else {
					$templateUsage[$templateUsageKey]["date_range"] = $referredCountRow["referred"];
				}
			}
		}
	}

	$uploadCountResult = mysqli_query($allConn, $uploadCountQuery);
	$uploadCount = mysqli_num_rows($uploadCountResult);

	while ($uploadCountRow = mysqli_fetch_array($uploadCountResult)) {
		foreach ($templateUsage as $templateUsageKey => $templateUsageValue) {
			if ($templateUsageValue["Type"] == "resume_upload") {
				if (isset($uploadCountRow["datec"]) && isset($templateUsageValue[$uploadCountRow["datec"]]) && ($isMultipleMonth || $isMultipleQuarter)) {
					$templateUsage[$templateUsageKey][$uploadCountRow["datec"]] = $uploadCountRow["resume_upload"];
				} else {
					$templateUsage[$templateUsageKey]["date_range"] = $uploadCountRow["resume_upload"];
				}
			}
		}
	}

	$applyCountResult = mysqli_query($allConn, $applyCountQuery);
	$applyCount = mysqli_num_rows($applyCountResult);

	while ($applyCountRow = mysqli_fetch_array($applyCountResult)) {
		foreach ($templateUsage as $templateUsageKey => $templateUsageValue) {
			if ($templateUsageValue["Type"] == "apply_count") {
				if (isset($applyCountRow["datec"]) && isset($templateUsageValue[$applyCountRow["datec"]]) && ($isMultipleMonth || $isMultipleQuarter)) {
					$templateUsage[$templateUsageKey][$applyCountRow["datec"]] = $applyCountRow["apply_count"];
				} else {
					$templateUsage[$templateUsageKey]["date_range"] = $applyCountRow["apply_count"];
				}
			}
		}
	}
	
		$tableHeaders = array_keys($templateUsage[0]);
		$result = '<table class="table table-bordered table-striped"><thead><tr class="bg-primary">';

		foreach ($tableHeaders as $value) {
			$result .= '<th>'.(isset($displayDateList[$value]) ? $displayDateList[$value]: $value).'</th>';
		}
		
	if ($referredCount != 0 OR $uploadCount != 0 OR $applyCount != 0) {
		
		$result .= '</tr></thead><tbody>';

		foreach ($templateUsage as $valueList) {
			$result	.= '<tr>';

			foreach ($valueList as $key => $value) {
				if ($key == 'Type') {
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
										
						$result .='<td align="center"><a style="cursor: pointer;" class="refer-a-friend-popup hyper-link-text" data-popup="'.$firstDateForPopup.'/'.$lastDateForPopup.'" data-status="'.$valueList['Type'].'" data-titlename="'.$templateList[$valueList['Type']].' ('.$displayDateList[$key].')" >';
						$result .= $value;
						$result .='</a></td>';
					
					}	 
				}
			}
			$result .= '</tr>';
		}
	} else {
			$headerLength = count($tableHeaders) + 1;
			$result .= '<tr><td colspan ="'.$headerLength.'" align ="center" >No data found.</td></tr>';
		}
	$result .= '</tbody></table>';
	
	echo $result;
?>