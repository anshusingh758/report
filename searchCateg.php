<?php
	include("config.php");

	if ($_POST) {
		$output = "";

		$id = $_POST["id"];

		if ($id != "") {
			$query = mysqli_query($allConn, "SELECT
				c.catid,
				c.catname
			FROM
				mis_reports.category AS c
			GROUP BY catid
			ORDER BY catid ASC");
			$output = "<option value=''>Select Category</option>";
			
			while ($row = mysqli_fetch_array($query)) {
				$output .= "<option value='".$row['catid'].",".$id."'>".$row['catname']."</option>";
			}
			
			echo $output;
		} else {
			echo $output;
		}
	}
?>