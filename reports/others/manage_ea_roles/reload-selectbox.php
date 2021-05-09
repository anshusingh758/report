<?php
	include('../../../config.php');
	include("../../../functions/reporting-service.php");
	if ($_POST) {
		if ($_POST['mapType'] == 'employee') {
			$eaPersonnelList[] = '<option value="">Select EA Personnel</option>';
		}
		$eaPersonnelList[] = eaPersonnelList($catsConn);
		print_r($eaPersonnelList);
	}
?>