<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");
	if ($_POST) {
		if ($_POST["item"] == "Select All") {
			$output = "";
			$clientList = array();
			$clientList = catsClientList($catsConn);
			foreach ($clientList as $clientKey => $clientValue) {
				$output .= "<option value='".$clientValue['id']."'>".$clientValue['name']."</option>";
			}
		}

		if ($_POST["personnel"] == "") {
			$output = "";
			$clientList = array();
			$clientList = catsClientList($catsConn);
			foreach ($clientList as $clientKey => $clientValue) {
				$output .= "<option value='".$clientValue['id']."'>".$clientValue['name']."</option>";
			}
		} elseif ($_POST["type"] == "Manager - Client Service") {
			$output = $personnel = "";
			$clientList = array();
			$personnel = $_POST["personnel"];
			$clientList = findClientByManager($catsConn,$personnel);
			foreach ($clientList as $clientKey => $clientValue) {
				$output .= "<option value='".$clientValue['id']."'>".$clientValue['name']."</option>";
			}
		} else {
			$output = $personnel = $type = "";
			$clientList = array();
			$personnel = $_POST["personnel"];
			$type = $_POST["type"];
			$clientList = findClientByPersonnel($catsConn,$personnel,$type);
			foreach ($clientList as $clientKey => $clientValue) {
				$output .= "<option value='".$clientValue['id']."'>".$clientValue['name']."</option>";
			}
		}
		
		echo $output;
	}
?>