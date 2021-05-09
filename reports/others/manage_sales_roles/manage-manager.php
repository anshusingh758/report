<?php 
	include("../../../config.php");

	if($_POST){
		$team_name = $_POST['dname'];

		$teamQUERY = mysqli_query($vtechMappingdbConn, "SELECT manager_name FROM sales_manager WHERE dept_name = '$team_name'");
		while($teamROW = mysqli_fetch_array($teamQUERY)){
			$man_name[] = $teamROW['manager_name'];
		}

		$manQUERY = "SELECT
			u.id AS uid,
		    CONCAT(u.firstName, ' ', u.lastName) AS uname
		FROM
		    x2_users AS u
		    JOIN x2_role_to_user AS roles ON roles.userId = u.id
		WHERE
			roles.roleId = '9'
		AND
			u.status = '1'
		GROUP BY uname";
		$manRESULT = mysqli_query($sales_connect, $manQUERY);
		while($manROW = mysqli_fetch_array($manRESULT)){
			$man_name2[] = $manROW['uname'];
		}
		
		$arr_diff = array_diff($man_name2, $man_name);

		foreach($man_name2 as $key => $man_name3){
			if(array_search($man_name3, $arr_diff) !== false){
				echo "<option value='".$man_name2[$key]."'>".$man_name2[$key]."</option>";
			}else{
				echo "<option value='".$man_name2[$key]."' selected>".$man_name2[$key]."</option>";
			}
		}
	}
?>