<?php
	include("../../../config.php");
	include("../../../security.php");
	
	if($_POST){
		$dept_name = $_POST['selectDepartment'];
		$editManagerQUERY = "DELETE FROM sales_manager WHERE dept_name = '$dept_name'";
		if(mysqli_query($vtechMappingdbConn, $editManagerQUERY)){
			foreach($_POST['editManager'] AS $manager_name){
				$editManagerQUERYX[] = "INSERT INTO sales_manager(dept_name,manager_name,addedby) VALUES ('$dept_name','$manager_name','$user')";
			}
	        $delQRY = implode(";", $editManagerQUERYX);
	        if(mysqli_multi_query($vtechMappingdbConn, $delQRY)){
	        	echo "success";
	        } else {
	        	echo "error";
	        }
		}
	}
?>