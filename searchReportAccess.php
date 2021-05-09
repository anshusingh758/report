<?php
	include("config.php");

	if ($_POST) {
		$output = "";
		$givenId = $_POST["givenId"];

		$query = mysqli_query($allConn, "SELECT
			u.uid AS user_id,
		    CONCAT(u.first_name,' ',u.last_name) AS user_name
		FROM
			mis_reports.users AS u
			JOIN mis_reports.mapping AS m ON m.uid = u.uid
		    JOIN mis_reports.reports AS r ON r.rid = m.rid
		WHERE
			r.rid = '$givenId'
		AND
			u.ustatus = '1'
		GROUP BY user_id
		ORDER BY user_id ASC");

		if (mysqli_num_rows($query) > 0) {
			$output = '<div class="row" style="margin-top: 25px;margin-bottom: 25px;">
				<div class="col-md-4">
					<label>View Users :</label>
					<table id="viewReportAccessTable" class="table table-bordered table-striped">
						<thead>
							<tr style="background-color: #337AB7;color: #fff;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">ID</th>
								<th style="text-align: center;vertical-align: middle;">User</th>
							</tr>
						</thead>
						<tbody>';

			while ($row = mysqli_fetch_array($query)) {
	            $output .='<tr style="font-size:13px;">
	                <td style="text-align: center;vertical-align: middle;">'.$row['user_id'].'</td>
	                <td style="text-align: center;vertical-align: middle;">'.$row['user_name'].'</td>
	            </tr>';
			}

			$output.='</tbody>
					</table>
				</div>
			</div>';

			echo $output;
		}
	}
?>
