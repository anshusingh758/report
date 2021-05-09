<?php
	error_reporting(0);
	include_once("../config.php");
    
	if ($_POST) {

		$status = $_POST["status"];
		$allDate = $_POST["date"];
		$dateSplit = explode("/", $allDate);
		$startDate = $dateSplit[0];
		$endDate = $dateSplit[1];

		$output = "<table class='table table-striped table-bordered get-rate-usage-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th>User Name</th>
					<th>Contact Name</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>";
			// if ($status == "get_rate_usage") {
					$query = mysqli_query($allConn, "SELECT 
						c.name AS contactname,
						sf.sales_contact_id,
						sf.created_date, 
						CONCAT(u.firstName,' ',u.lastName) AS username
					FROM 
						vtech_mappingdb.sales_selected_service_final AS sf 
						LEFT JOIN vtechcrm.x2_contacts AS c ON c.id = sf.sales_contact_id 
						LEFT JOIN vtechcrm.x2_users AS u ON u.id = sf.entered_by
					WHERE
						DATE_FORMAT(sf.created_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
						AND 
						sf.activated_tab_id = '$status'
					GROUP BY 
						sf.sales_contact_id 
					ORDER BY sf.created_date DESC");
			 

				while ($row = mysqli_fetch_array($query)) {

					$output .= "<tr class='tbody-tr-style'>";
					$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'>".$row["username"]."</td>";
					$output .= "<td style='text-align: left;vertical-align: middle;padding-left: 7px;'><a href='https://sales.vtechsolution.com/index.php/contacts/id/".$row["sales_contact_id"]."' target='_blank' class='hyper-link-text'>".$row["contactname"]."</a></td>";
					$output .= "<td nowrap>".$row["created_date"]."</td>
					</tr>";

				}
			// }
			$output .= "</tbody>
			</table>";
		echo $output;
	}
?>