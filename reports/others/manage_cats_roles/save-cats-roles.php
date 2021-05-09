<?php
	include('../../../config.php');
	include('../../../security.php');
	if ($_POST) {
		$selectedValue = $_POST['idValue'];
		
		$userId = implode(",", $_POST['checked_id']);

		if ($_POST['type'] == 'department') {
			foreach ($_POST['checked_id'] as $key => $value) {
				$departmentSelectQUERY = mysqli_query($vtechMappingdbConn, "SELECT * FROM manage_cats_roles WHERE user_id = '$value'");
				if (mysqli_num_rows($departmentSelectQUERY) > 0) {
					mysqli_query($vtechMappingdbConn, "UPDATE manage_cats_roles SET department = '$selectedValue', addedby = '$user' WHERE user_id = '$value'");
				} else {
					mysqli_query($vtechMappingdbConn, "INSERT INTO manage_cats_roles(user_id,department,addedby) VALUES('$value','$selectedValue','$user')");
				}
			}
			echo "success";
		} elseif ($_POST['type'] == 'designation') {
			foreach ($_POST['checked_id'] as $key => $value) {
				$designationSelectQUERY = mysqli_query($vtechMappingdbConn, "SELECT * FROM manage_cats_roles WHERE user_id = '$value'");
				if (mysqli_num_rows($designationSelectQUERY) > 0) {
					mysqli_query($vtechMappingdbConn, "UPDATE manage_cats_roles SET designation = '$selectedValue', addedby = '$user' WHERE user_id = '$value'");
				} else {
					mysqli_query($vtechMappingdbConn, "INSERT INTO manage_cats_roles(user_id,designation,addedby) VALUES('$value','$selectedValue','$user')");
				}
			}
			echo "success";
		} elseif ($_POST['type'] == 'manager') {
			if (mysqli_query($catsConn, "UPDATE user SET notes = '$selectedValue' WHERE user_id IN ($userId)")) {
				echo "success";
			} else {
				echo "error";
			}
		} else {
			echo "error";
		}
	}
?>