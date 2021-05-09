<?php
	include_once("../../../config.php");
	if(isset($_POST["title"])) {  
		$query = "SELECT 
			com.title,
			com.resource_from,
			com.resource_to,
			com.margin_min,
			com.margin_max,
			com.color 
		FROM 
			vtech_mappingdb.client_optimization_matrix AS com
		WHERE
			 com.title = '".$_POST["title"]."'";
		$result = mysqli_query($allConn, $query);
		$row = mysqli_fetch_array($result);  
		echo json_encode($row);  
 	} 
?> 