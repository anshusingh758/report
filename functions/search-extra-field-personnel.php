<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");
	if ($_POST) {
		$fieldName = $output = "";
		$fieldName = $_POST["fieldName"];
		$personnelList = array();
		$personnelList = catsExtraFieldPersonnelList($catsConn,$fieldName);
		sort($personnelList);
		$output = "<option value=''>Select Option</option>";
		foreach ($personnelList as $personnelKey => $personnelValue) {
			$output .= "<option value='".$personnelValue."'>".ucwords($personnelValue)."</option>";
		}
		echo $output;
	}
	if ($_POST['twilioCsTeam']) {
		$fieldName = $output = "";
		$fieldName = $_POST["twilioCsTeam"];
		$personnelList = array();
		$personnelList = catsUserListForTwilioCall($catsConn);
		// sort($personnelList);
		$output = "";
		foreach ($personnelList as $personnelKey => $personnelValue) {
			$output .= "<option value='".$personnelValue['phone_work']."'>".ucwords($personnelValue['name'])."</option>";
		}
		echo $output;
	}
?>