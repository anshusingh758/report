<?php
	include('../../../config.php');
	include('../../../security.php');
	if ($_POST) {
		$mapType = ucwords($_POST['mapType']);

		foreach ($_POST["eaPersonnelGroup"] as $key => $value) {
			$eaPersonnelGroup[] = $value;
		}
		
		$eaPersonnelList = implode(",", $eaPersonnelGroup);

		foreach ($_POST["checked_id"] as $key => $values) {
			$selectQUERY = mysqli_query($vtechMappingdbConn, "SELECT * FROM manage_ea_roles WHERE reference_type = '$mapType' AND reference_id = '$values'");
			if (mysqli_num_rows($selectQUERY) > 0) {
				$selectROW = mysqli_fetch_array($selectQUERY);
				$finalQuery[] = "UPDATE manage_ea_roles SET mapping_value = '$eaPersonnelList', addedby = '$user' WHERE reference_type = '$mapType' AND reference_id = '$values'";
				mysqli_query($vtechMappingdbConn, "INSERT INTO ea_roles_history (old_map_id,old_map_value,old_sync_date,sync_by) VALUES ('".$selectROW['reference_id']."','".$selectROW['mapping_value']."','".$selectROW['date_time']."','$user')");
			} else {
				$finalQuery[] = "INSERT INTO manage_ea_roles (reference_id,reference_type,mapping_value,addedby) VALUES ('$values','$mapType','$eaPersonnelList','$user')";
			}
		}
	
		$finalQueryGroup = implode(";", $finalQuery);

		if (mysqli_multi_query($vtechMappingdbConn, $finalQueryGroup)) {
			echo $eaPersonnelList;
		}
	}
?>