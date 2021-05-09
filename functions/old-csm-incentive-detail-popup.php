<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");

	if ($_POST) {
		$id = $_POST["id"];
		$name = $_POST["name"];
		$target = $_POST["target"];
		$amount = $_POST["amount"];

		$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th colspan='13' style='font-size: 15px;'>".$name."</th>
				</tr>
				<tr class='thead-tr-style'>
					<th>Recruiter</th>
					<th>Recruiter<br>Manager</th>
					<th>Candidate</th>
					<th>Client</th>
					<th>Client Manager</th>
					<th>CATS Date</th>
					<th>CATS Status</th>
					<th>HRM Date</th>
					<th>HRM Status</th>
					<th>Termination Date</th>
					<th>3 Months<br>Completed</th>
					<th>Margin</th>
					<th>Additional<br>Incentive<br>Amount</th>
				</tr>
			</thead>
			<tbody>";
			
			$query = mysqli_query($misReportsConn, "SELECT detail_data FROM incentive_data WHERE id = '$id'");
			
			$row = mysqli_fetch_array($query);
			
			$dataObject = json_decode($row["detail_data"], true);

			foreach ($dataObject AS $dataObjectKey => $dataObjectValue) {

				$output .= "<tr class='tbody-tr-style'>
					<td>".$dataObject[$dataObjectKey]["recruiter"]."</td>
					<td>".$dataObject[$dataObjectKey]["recruiter_manager"]."</td>
					<td>".$dataObject[$dataObjectKey]["candidate"]."</td>
					<td>".$dataObject[$dataObjectKey]["client"]."</td>";

				if ($dataObject[$dataObjectKey]["client_manager"] != $name) {
					$output .= "<td style='color: red;'>".$dataObject[$dataObjectKey]["client_manager"]."</td>";
				} else {
					$output .= "<td>".$dataObject[$dataObjectKey]["client_manager"]."</td>";
				}

				$output .= "<td>".$dataObject[$dataObjectKey]["cats_placement_date"]."</td>";
				
				if ($dataObject[$dataObjectKey]["cats_status"] == "Extension") {
					$output .= "<td style='color: red;'>".$dataObject[$dataObjectKey]["cats_status"]."</td>";
				} else {
					$output .= "<td>".$dataObject[$dataObjectKey]["cats_status"]."</td>";
				}

				$output .= "<td>".$dataObject[$dataObjectKey]["join_date"]."</td>
					<td>".$dataObject[$dataObjectKey]["hrm_status"]."</td>
					<td>".$dataObject[$dataObjectKey]["termi_date"]."</td>";

				if ($dataObject[$dataObjectKey]["eligibility"] == "No") {
					$output .= "<td style='color: red;'>".$dataObject[$dataObjectKey]["eligibility"]."</td>";
				} else {
					$output .= "<td>".$dataObject[$dataObjectKey]["eligibility"]."</td>";
				}

				$output .= "<td>".$dataObject[$dataObjectKey]["margin"]."</td>
					<td>".$totalAdditionalIncentive[] = $dataObject[$dataObjectKey]["additional_incentive"]."</td>
				</tr>";

			}
			
			$output .= "</tbody>
				<tfoot>
					<tr class='tfoot-tr-style'>
						<th colspan='12'>Total Target Placement : ".$target."<br>Total Target Incentive : ".$amount."</th>
						<th>".array_sum($totalAdditionalIncentive)."</th>
					</tr>
				</tfoot>
			</table>";
	
		echo $output;
	}
?>