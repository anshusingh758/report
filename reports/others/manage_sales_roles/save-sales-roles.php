<?php
	include('../../../config.php');
	include('../../../security.php');
	if ($_POST) {
		$selectedValue = $_POST['idValue'];
		
		$userId = implode(",", $_POST['checked_id']);

		if ($_POST['type'] == 'department') {
			foreach ($_POST['checked_id'] as $key => $value) {
				$departmentSelectQUERY = mysqli_query($vtechMappingdbConn, "SELECT * FROM manage_sales_roles WHERE user_id = '$value'");
				if (mysqli_num_rows($departmentSelectQUERY) > 0) {
					mysqli_query($vtechMappingdbConn, "UPDATE manage_sales_roles SET department = '$selectedValue', addedby = '$user' WHERE user_id = '$value'");
				} else {
					mysqli_query($vtechMappingdbConn, "INSERT INTO manage_sales_roles(user_id,department,addedby) VALUES('$value','$selectedValue','$user')");
				}
			}
			echo "success";
		} elseif ($_POST['type'] == 'manager') {
			foreach ($_POST['checked_id'] as $key => $value) {
				$managerSelectQUERY = mysqli_query($vtechMappingdbConn, "SELECT * FROM manage_sales_roles WHERE user_id = '$value'");
				if (mysqli_num_rows($managerSelectQUERY) > 0) {
					mysqli_query($vtechMappingdbConn, "UPDATE manage_sales_roles SET manager_name = '$selectedValue', addedby = '$user' WHERE user_id = '$value'");
				} else {
					mysqli_query($vtechMappingdbConn, "INSERT INTO manage_sales_roles(user_id,manager_name,addedby) VALUES('$value','$selectedValue','$user')");
				}
			}
			echo "success";
		} else {
			echo "error";
		}
	}
?>