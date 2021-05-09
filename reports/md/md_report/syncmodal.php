<?php

	include_once("../../../security.php");
	include_once("../../../config.php");

	if ($_POST) {
		
		$dataType = $dataId = $taxPerc = $primeChargePct = $primeChargeDlr = $anyChargeDlr = $mspChrgPct = $mspChrgDlr = $primeChrgPct = $primeChrgDlr = $clientType = $percentageValue = $insertCOUNT = $insertSTATEMENTS = "";
		
		$oldList = $newList = $finalList = $insertList = $output = array();

		foreach (explode(",", $_POST['oldData']) AS $dataValue) {
			$dataList = array();
			$dataList = explode(":", $dataValue);
			$oldList[$dataList[0]] = $dataList[1];
		}

		$dataType = $_POST['dataType'];
		$dataId = $_POST['dataId'];
		
		if ($dataType == 'tax') {
			$taxPerc = $_POST['taxPerc'];
		
			$newList = array("id" => $dataId,
			"type" => $dataType,
			"charge_pct" => $taxPerc);

			$finalList = array_diff_assoc($oldList, $newList);

			foreach ($finalList as $finalKey => $finalValue) {
				$insertList[] = "INSERT INTO md_setting_history(old_id,old_type,old_rate_type,old_rate,sync_by) VALUES('$dataId','$dataType','$finalKey','$finalValue','$user')";
			}

			$insertCOUNT = count(array_unique($insertList));
			$insertSTATEMENTS = implode(";", $insertList);

			if (mysqli_query($vtechMappingdbConn, "UPDATE tax_settings SET charge_pct = '$taxPerc', added_by = '$user' WHERE id = '$dataId'")) {
				if ($insertCOUNT > 0) {
					mysqli_multi_query($vtechMappingdbConn, $insertSTATEMENTS);
				}
				$output = $newList;
				echo json_encode($output);
				exit();
			} else {
				$output['data'] = "Error";
				echo json_encode($output);
				exit();
			}
		} elseif ($dataType == 'candidate') {
			$primeChargePct = $_POST['primeChargePct'];
			$primeChargeDlr = $_POST['primeChargeDlr'];
			$anyChargeDlr = $_POST['anyChargeDlr'];

			$newList = array("id" => $dataId,
			"type" => $dataType,
			"c_primeCharge_pct" => $primeChargePct,
			"c_primeCharge_dlr" => $primeChargeDlr,
			"c_anyCharge_dlr" => $anyChargeDlr);

			$finalList = array_diff_assoc($oldList, $newList);

			foreach ($finalList as $finalKey => $finalValue) {
				$insertList[] = "INSERT INTO md_setting_history(old_id,old_type,old_rate_type,old_rate,sync_by) VALUES('$dataId','$dataType','$finalKey','$finalValue','$user')";
			}

			$insertCOUNT = count(array_unique($insertList));
			$insertSTATEMENTS = implode(";", $insertList);

			if (mysqli_query($vtechMappingdbConn, "UPDATE candidate_fees SET c_primeCharge_pct = '$primeChargePct', c_primeCharge_dlr = '$primeChargeDlr', c_anyCharge_dlr = '$anyChargeDlr', added_by = '$user' WHERE id = '$dataId'")) {
				if ($insertCOUNT > 0) {
					mysqli_multi_query($vtechMappingdbConn, $insertSTATEMENTS);
				}
				$output = $newList;
				echo json_encode($output);
				exit();
			} else {
				$output['data'] = "Error";
				echo json_encode($output);
				exit();
			}
			
		} elseif ($dataType == 'client') {
			$mspChrgPct = $_POST['mspChrgPct'];
			$mspChrgDlr = $_POST['mspChrgDlr'];
			$primeChrgPct = $_POST['primeChrgPct'];
			$primeChrgDlr = $_POST['primeChrgDlr'];

			$newList = array("id" => $dataId,
			"type" => $dataType,
			"mspChrg_pct" => $mspChrgPct,
			"mspChrg_dlr" => $mspChrgDlr,
			"primeChrg_pct" => $primeChrgPct,
			"primeChrg_dlr" => $primeChrgDlr);

			$finalList = array_diff_assoc($oldList, $newList);

			foreach ($finalList as $finalKey => $finalValue) {
				$insertList[] = "INSERT INTO md_setting_history(old_id,old_type,old_rate_type,old_rate,sync_by) VALUES('$dataId','$dataType','$finalKey','$finalValue','$user')";
			}

			$insertCOUNT = count(array_unique($insertList));
			$insertSTATEMENTS = implode(";", $insertList);

			if (mysqli_query($vtechMappingdbConn, "UPDATE client_fees SET mspChrg_pct = '$mspChrgPct', mspChrg_dlr = '$mspChrgDlr', primeChrg_pct = '$primeChrgPct', primeChrg_dlr = '$primeChrgDlr', added_by = '$user' WHERE id = '$dataId'")) {
				if ($insertCOUNT > 0) {
					mysqli_multi_query($vtechMappingdbConn, $insertSTATEMENTS);
				}
				$output = $newList;
				echo json_encode($output);
				exit();
			} else {
				$output['data'] = "Error";
				echo json_encode($output);
				exit();
			}
		} elseif ($dataType == 'clientMarkupMargin') {
			$clientType = $_POST['clientType'];
			$percentageValue = $_POST['percentageValue'];

			$newList = array(
				"id" => $dataId,
				"type" => $dataType,
				"clientType" => $clientType,
				"percentageValue" => $percentageValue
			);

			$finalList = array_diff_assoc($oldList, $newList);

			foreach ($finalList as $finalKey => $finalValue) {
				$insertList[] = "INSERT INTO md_setting_history(old_id,old_type,old_rate_type,old_rate,sync_by) VALUES('$dataId','$dataType','$finalKey','$finalValue','$user')";
			}

			$insertCOUNT = count(array_unique($insertList));
			$insertSTATEMENTS = implode(";", $insertList);

			if (mysqli_query($vtechMappingdbConn, "UPDATE client_markup_margin SET type = '$clientType', value = '$percentageValue', added_by = '$user' WHERE id = '$dataId'")) {
				if ($insertCOUNT > 0) {
					mysqli_multi_query($vtechMappingdbConn, $insertSTATEMENTS);
				}
				$output = $newList;
				echo json_encode($output);
				exit();
			} else {
				$output['data'] = "Error";
				echo json_encode($output);
				exit();
			}
		}
		
	}

?>