<?php
	include_once("constants.php");
	session_start();
	$session_id = session_id();

	if (isset($_REQUEST["curl_session_key"]) && $_REQUEST["curl_session_key"] != "" && $_REQUEST["curl_session_key"] == CURL_SESSION_KEY) {
		if ($_REQUEST["user"] == "") {
			$_REQUEST["user"] = 1;
			$_REQUEST["userMember"] = "Admin";
		}

		$_SESSION["user"] = $_REQUEST["user"];
		$_SESSION["userMember"] = $_REQUEST["userMember"];
	}
	
	if (isset($_SESSION["user"]) && isset($_SESSION["userMember"])) {
		$user = $_SESSION["user"];
		$userMember = $_SESSION["userMember"];
	} else {
		$user = "Guest";
		$userMember = "Guest Member";
	}
?>