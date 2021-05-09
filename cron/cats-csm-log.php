<?php
	include_once("../config.php");
	include_once("../functions/reporting-service.php");

	$insertQUERY = array();

	$csmQUERY = mysqli_query($allConn, "SELECT
		u.user_id,
	    u.notes
	FROM
		cats.user AS u
	WHERE
		NOT EXISTS(SELECT
	    	*
	    FROM
	    	vtech_mappingdb.cats_csm_log AS ccl
	    WHERE
			ccl.user_id = IF(u.user_id IS NULL, '', u.user_id)
	    AND
			ccl.notes = IF(u.notes IS NULL, '', u.notes)
	    AND
			ccl.id IN (SELECT max(id) FROM vtech_mappingdb.cats_csm_log WHERE user_id = ccl.user_id GROUP BY user_id)
	    GROUP BY ccl.user_id)
	GROUP BY u.user_id");

	if (mysqli_num_rows($csmQUERY) > 0) {
		while ($csmROW = mysqli_fetch_array($csmQUERY)) {

			$userId = mysqli_real_escape_string($allConn, $csmROW["user_id"]);
			$notes = mysqli_real_escape_string($allConn, $csmROW["notes"]);
			
			$insertQUERY[] = "INSERT INTO vtech_mappingdb.cats_csm_log(user_id,notes) VALUES('$userId','$notes')";

		}
	}

	if (count($insertQUERY) > 0) {
		$insertROW = implode(";", $insertQUERY);
		if (mysqli_multi_query($allConn, $insertROW)) {
			echo "Total (".count($insertQUERY).") New Records Inserted!";
		} else {
			echo "Error Inserting New Records!";
		}
	} else {
		echo "No New Records!";
	}
?>