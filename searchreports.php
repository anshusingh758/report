<?php
	include("config.php");

	if ($_POST) {
		$reportId3 = "";
		$reportId = $reportName = $reportId2 = array();

		$selectedCategoryList = explode(",", $_POST["id"]);
		$categoryId = $selectedCategoryList[0];
		$userId = $selectedCategoryList[1];

		$query = mysqli_query($allConn, "SELECT
			r.rid,
			r.rname
		FROM
			mis_reports.reports AS r
		WHERE
			r.rstatus != '0'
		AND
			r.catid = '$categoryId'
		GROUP BY r.rid
		ORDER BY r.rname ASC");
        while ($row = mysqli_fetch_array($query)) {
			$reportId[] = $row["rid"];
			$reportName[] = $row["rname"];
        }

		$query = mysqli_query($allConn, "SELECT
			r.rid,
			r.rname
		FROM
			mis_reports.mapping AS m
			JOIN mis_reports.users AS u ON m.uid = u.uid
			JOIN mis_reports.reports AS r ON m.rid=r.rid
		WHERE
			m.uid = '$userId'
		AND
			m.catid = '$categoryId'
		GROUP BY r.rid
		ORDER BY r.rname ASC");
		
		while ($row = mysqli_fetch_array($query)) {
			$reportId2[] = $row['rid'];
		}

		$reportId3 = array_diff($reportId, $reportId2);

        foreach ($reportId as $key => $value) {
        	if (array_search($value, $reportId3) !== false) {
        		echo "<option value='".$reportId[$key]."'>".$reportName[$key]."</option>";
        	} else {
	        	echo "<option value=".$reportId[$key]." selected>".$reportName[$key]."</option>";
        	}
        }
	}
?>

