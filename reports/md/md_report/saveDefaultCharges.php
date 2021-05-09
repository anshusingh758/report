<?php
	include_once("../../../security.php");
	include_once("../../../config.php");

	if ($_POST) {
		$updateQUERY = array();

		$adminFeesPercentage = $_POST["adminFeesPercentage"];
		$markupPercentage = $_POST["markupPercentage"];
		$marginPercentage = $_POST["marginPercentage"];
		$errorValue = $_POST["errorValue"];

		if ($errorValue == "false") {
			$updateQUERY[] = "UPDATE mis_reports.incentive_criteria SET value = '$adminFeesPercentage', addedBy = '$user' WHERE personnel = 'Default MD Percentage' AND comment = 'Admin Fees'";

			$updateQUERY[] = "UPDATE mis_reports.incentive_criteria SET value = '$markupPercentage', addedBy = '$user' WHERE personnel = 'Default MD Percentage' AND comment = 'vTech Markup'";

			$updateQUERY[] = "UPDATE mis_reports.incentive_criteria SET value = '$marginPercentage', addedBy = '$user' WHERE personnel = 'Default MD Percentage' AND comment = 'vTech Margin'";

			$updateCOUNT = count(array_unique($updateQUERY));
			$updateSTATEMENTS = implode(";", $updateQUERY);
			if ($updateCOUNT > 0) {
				if (mysqli_multi_query($allConn, $updateSTATEMENTS)) {
					echo "success";
				} else {
					echo "error";
				}
			} else {
				echo "error";
			}
		} else {
			echo "error";
		}
	}

?>