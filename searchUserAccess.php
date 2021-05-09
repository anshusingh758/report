<?php
	include("config.php");

	if ($_POST) {
		$output = "";
		$givenId = $_POST["givenId"];

		$query = mysqli_query($allConn, "SELECT
			r.rid AS report_id,
		    r.rname AS report_name,
		    c.catname AS report_category
		FROM
			mis_reports.reports AS r
			JOIN mis_reports.mapping AS m ON m.rid = r.rid
		    JOIN mis_reports.category AS c ON c.catid = r.catid
		WHERE
			m.uid = '$givenId'
		AND
			r.rstatus = '1'
		GROUP BY report_id
		ORDER BY report_id ASC");

		if (mysqli_num_rows($query) > 0) {
			$output = '<div class="row" style="margin-top: 25px;margin-bottom: 25px;">
				<div class="col-md-6">
					<label>View Reports :</label>
					<table id="viewUserAccessTable" class="table table-bordered table-striped">
						<thead>
							<tr style="background-color: #337AB7;color: #fff;font-size: 13px;">
								<th style="text-align: center;vertical-align: middle;">ID</th>
								<th style="text-align: center;vertical-align: middle;">Reports</th>
								<th style="text-align: center;vertical-align: middle;">Category</th>
							</tr>
						</thead>
						<tbody>';

			while ($row = mysqli_fetch_array($query)) {
	            $output .='<tr style="font-size:13px;">
	                <td style="text-align: center;vertical-align: middle;">'.$row['report_id'].'</td>
	                <td style="vertical-align: middle;">'.$row['report_name'].'</td>
	                <td style="text-align: center;vertical-align: middle;">'.$row['report_category'].'</td>
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
