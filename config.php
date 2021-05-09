<?php
	$catsConn=mysqli_connect("localhost","root","","cats") or die("Cats Error");
	$vtechMappingdbConn=mysqli_connect("localhost","root","","vtech_mappingdb") or die("vTech_MappingDb Error");
	$vtechhrmConn=mysqli_connect("localhost","root","","vtechhrm") or die("vtechhrm Error");
	$misReportsConn=mysqli_connect("localhost","root","","mis_reports") or die("mis_reports Error");
	$allConn=mysqli_connect("localhost","root","","") or die("Connection Error");
	$sales_connect=mysqli_connect("localhost","root","","vtechcrm") or die("Sales Error");
	$onboard=mysqli_connect("localhost","root","","vtech_candidate_onboarding") or die("vtech_candidate_onboarding Error");
	$vtechTools=mysqli_connect("localhost","root","","vtech_tools") or die("vtech_tools");
	$hrmIndiaConn=mysqli_connect("localhost","root","","hrm_india") or die("HRM India Error");

	//For MD Report EDIT File
	$mysqli = new mysqli("localhost","root","","vtech_mappingdb") or die("vTech_MappingDb Error");

	include_once 'constants.php';
?>
