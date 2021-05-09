<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");
	if ($_POST) {
		$manager = $output = $type = $default = "";

		$manager = $_POST["manager"];
		
		if (isset($_POST["type"])) {
			$type = $_POST["type"];
		}

		if (isset($_POST["default"])) {
			$default = json_decode($_POST["default"]);
		}

		$recruiterList = array();
		
		if (($type == "CS Manager" || $type == "Management") && $manager == "") {
			$recruiterList = catsRecruiterList($catsConn,$default);
		} else {
			$recruiterList = catsRecruiterList($catsConn,$manager);
		}

		foreach ($recruiterList as $recruiterKey => $recruiterValue) {
			$output .= "<option value='".$recruiterValue['id']."'>".$recruiterValue['name']."</option>";
		}
		echo $output;
	}
?>