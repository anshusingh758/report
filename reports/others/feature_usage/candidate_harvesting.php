<?php
	include('../../../config.php');

	$multipleMonth = $_REQUEST['multipleMonth'];
	$fromDate = $_REQUEST['fromDate'];
	$toDate = $_REQUEST['toDate'];
	$multipleQuarter = $_REQUEST['multipleQuarter'];
	$isMultipleMonth = true;
	$isMultipleQuarter = false;
	$templateUsage = $displayDateList = array();
	
	$templateList = array("monster" => "Monster", "careerbuilder" => "Career Builder", "nexxt" => "Nexxt");

	foreach ($templateList as $key => $value) {
		$templateUsage[] = array("Source Portal" => $key);
	}

	$query = "SELECT `type`, COUNT(id) as jobboard_count ";

	if ($multipleMonth !== '') {
		$query .=", EXTRACT(YEAR_MONTH FROM created_date) as datec";
	}

	if ($multipleQuarter !== '') {
		$query .=", YEAR(created_date) AS year, QUARTER(created_date) as quarter, CONCAT(YEAR(created_date), 'Q', QUARTER(created_date)) as datec";
	}

	$query .= " FROM vtech_mappingdb.candidate_harvesting_records";

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

		$query .= " WHERE EXTRACT(YEAR_MONTH FROM created_date) IN (".$multipleMonth.")
					GROUP BY type, datec
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

		$query .= " WHERE CONCAT(YEAR(created_date), 'Q', QUARTER(created_date)) in ('".$multipleQuarter."')
					GROUP BY type,year, quarter
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
		$query .= " WHERE created_date BETWEEN '".$fromDate[2]."-".$fromDate[0]."-".$fromDate[1]." 00:00:00' and '".$toDate[2]."-".$toDate[0]."-".$toDate[1]." 23:59:59' GROUP BY type";
	}

	$resultQuery = mysqli_query($allConn, $query);
	$rowcount = mysqli_num_rows($resultQuery);

	$tableHeaders = array_keys($templateUsage[0]);
	$result = '<table class="table table-bordered table-striped"><thead><tr class="bg-primary">';

	foreach ($tableHeaders as $value) {
		$result .= '<th>'.(isset($displayDateList[$value]) ? $displayDateList[$value]: $value).'</th>';
	}

	$result .= '</tr></thead><tbody>';

	if ($rowcount > 0) {
		// Setup of values as per column
		while($resultRow = mysqli_fetch_assoc($resultQuery)) {
			foreach ($templateUsage as $key => $value) {
				if ($value["Source Portal"] == $resultRow["type"]) {
					if (isset($resultRow["datec"]) && isset($value[$resultRow["datec"]]) && ($isMultipleMonth || $isMultipleQuarter)) {
						$templateUsage[$key][$resultRow["datec"]] = $resultRow["jobboard_count"];
					} else {
						$templateUsage[$key]["date_range"] = $resultRow["jobboard_count"];
					}
				}
			}
		}

		foreach ($templateUsage as $valueList) {
			$result	.= '<tr>';

			foreach ($valueList as $key => $value) {
				if ($key == 'Source Portal') {
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
										
						$result .='<td align="center"><a style="cursor: pointer;" class="candidate-harvesting-popup hyper-link-text" data-popup="'.$firstDateForPopup.'/'.$lastDateForPopup.'" data-status="'.$valueList['Source Portal'].'" data-titlename="'.$templateList[$valueList['Source Portal']].' ('.$displayDateList[$key].')" >';
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
