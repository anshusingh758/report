<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");
	if ($_POST) {
		$groupId = $output = "";
		$groupId = $_POST["groupId"];
		$salesGroupPersonnel = array();

		$salesGroupPersonnel = salesGroupPersonnelList($sales_connect,$groupId);
		sort($salesGroupPersonnel);
		foreach ($salesGroupPersonnel as $salesGroupPersonnelKey => $salesGroupPersonnelValue) {
			$output .= "<option value='".$salesGroupPersonnelValue."'>".$salesGroupPersonnelValue."</option>";
		}
		echo $output;
	}
?>