<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");
	if ($_POST) {
		$clientGroup = $output = "";
		$employeeList = array();
		
		$clientGroup = implode(",", $_POST["client-list"]);
		
		$employeeList = hrmEmployeeList($vtechhrmConn,$clientGroup);

		foreach ($employeeList as $employeeKey => $employeeValue) {
			$output .= "<option value='".$employeeValue['id']."'>".$employeeValue['name']."</option>";
		}
		echo $output;
	}

?>