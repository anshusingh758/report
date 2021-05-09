<?php
	include_once("../config.php");
	include_once("../functions/reporting-service.php");

	$insertQUERY = $updateQUERY = array();

	$personnelQUERY = mysqli_query($allConn, "(SELECT
	 	*
	FROM
		vtech_mappingdb.cats_personnel_change_detail)
	UNION
	(SELECT
		comp.company_id,
	    comp.owner AS personnel,
	    'Owner' AS personnel_type,
		DATE_FORMAT(comp.date_created, '%Y-%m-%d') AS company_create_date,
	    IF((SELECT COUNT(id) FROM vtech_mappingdb.cats_personnel_log WHERE company_id = comp.company_id AND personnel_type = 'Owner' GROUP BY company_id) > 0, 'No', 'Yes') AS is_new_entry,
	    IF((SELECT COUNT(id) FROM vtech_mappingdb.cats_personnel_log WHERE company_id = comp.company_id AND personnel_type = 'Owner' GROUP BY company_id) > 0, 'No', 'Yes') AS is_new_personnel_type,
	    (SELECT MAX(id) FROM vtech_mappingdb.cats_personnel_log WHERE company_id = comp.company_id AND personnel_type = 'Owner' GROUP BY company_id) AS old_owner_id
	FROM
	    cats.company AS comp
	WHERE
	    NOT EXISTS(SELECT
	        *
	    FROM
	        vtech_mappingdb.cats_personnel_log AS cpl
	    WHERE
	        cpl.company_id = IF(comp.company_id IS NULL, '', comp.company_id)
	    AND
	        cpl.personnel = IF(comp.owner IS NULL, '', comp.owner)
	    AND
	        cpl.personnel_type = 'Owner'
	    AND
	        cpl.id IN (SELECT max(id) FROM vtech_mappingdb.cats_personnel_log WHERE company_id = cpl.company_id AND personnel_type = 'Owner')
	    GROUP BY cpl.personnel_type,cpl.company_id)
	GROUP BY comp.company_id)");

	if (mysqli_num_rows($personnelQUERY) > 0) {
		while ($personnelROW = mysqli_fetch_array($personnelQUERY)) {
			$companyId = $personnelROW["company_id"];
			$personnel = $personnelROW["personnel"];
			$personnelType = $personnelROW["personnel_type"];
			$oldPersonnelId = $personnelROW["old_personnel_id"];

			if ($personnelROW["is_new_entry"] == "Yes") {
				$startDate = $personnelROW["company_create_date"];
			} else {
				$startDate = date("Y-m-d");
			}
			
			$endDate = date("Y-m-d", strtotime("-1 day"));
				
			$insertQUERY[] = "INSERT INTO vtech_mappingdb.cats_personnel_log(company_id,personnel,personnel_type,start_date,end_date,is_live) VALUES('$companyId','$personnel','$personnelType','$startDate','','Yes')";
				
			if ($personnelROW["is_new_personnel_type"] == "No") {
				$updateQUERY[] = "UPDATE vtech_mappingdb.cats_personnel_log SET end_date = '$endDate', is_live = 'No' WHERE id = '$oldPersonnelId'";
			}

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

	if (count($updateQUERY) > 0) {
		$updateROW = implode(";", $updateQUERY);
		if (mysqli_multi_query($allConn, $updateROW)) {
			echo "<br>Total (".count($updateQUERY).") Records Updated!";
		} else {
			echo "<br>Error in Updating Records!";
		}
	} else {
		echo "<br>No Old Records!";
	}
?>