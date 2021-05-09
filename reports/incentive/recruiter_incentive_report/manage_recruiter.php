<?php
	include('../../../config.php');
	
	if($_POST){
		$ownerx=$_POST['manager_name'];

		foreach($ownerx AS $ownery){
			$qry="SELECT
					user_id AS user_id,
					concat(first_name,' ',last_name) AS recnm
				FROM
					user
				WHERE
					access_level!='0'
				AND
					notes = '$ownery'
				GROUP BY recnm
				ORDER BY recnm ASC";
			$res=mysqli_query($catsConn,$qry);

			while($row=mysqli_fetch_array($res)){
				echo "<option value=".$row['user_id'].">".$row['recnm']."</option>";
			}
		}
	}
?>