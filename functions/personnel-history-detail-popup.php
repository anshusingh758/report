<?php
	error_reporting(0);
	include_once("../config.php");
	include_once("reporting-service.php");

	if ($_POST) {
		$data = json_decode($_POST["data"], true);

		$output = "<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>";
					foreach ($data as $key => $value) {
						$output .= "<th colspan='11' style='font-size: 15px;'>".$key."</th>";
					}
				$output .= "</tr>
				<tr class='thead-tr-style'>
					<th>Client</th>
					<th>Shared<br>With</th>
					<th>Submission</th>
					<th>Interview</th>
					<th>Interview<br>Decline</th>
					<th>Offer</th>
					<th>Placed</th>
					<th>Extension</th>
					<th>Delivery<br>Failed</th>
					<th>From<br>Date</th>
					<th>To<br>Date</th>
				</tr>
			</thead>
			<tbody>";
			
			$clientId = $totalSubmission = $totalInterview = $totalInterviewDecline = $totalOffer = $totalPlaced = $totalExtension = $totalDeliveryFailed = $sharedWith = array();

			foreach ($data as $key => $value) {
				foreach ($value as $list => $item) {
					
					$submission = $interview = $interviewDecline = $offer = $placed = $extension = $deliveryFailed = $clientIdCount = $clientIdFinalCount = "0";

					$clientId[] = $item["companyId"];
					
					$clientIdCount = array_count_values($clientId);
					$clientIdFinalCount = $clientIdCount[$item["companyId"]];
					
					if ($item["shared_with"] != "---" && $clientIdFinalCount <= 1) {
						$sharedWith[] = $item["shared_with"];
					}

					$totalSubmission[] = $item["submission"];
					$totalInterview[] = $item["interview"];
					$totalInterviewDecline[] = $item["interviewDecline"];
					$totalOffer[] = $item["offer"];
					$totalPlaced[] = $item["placed"];
					$totalExtension[] = $item["extension"];
					$totalDeliveryFailed[] = $item["deliveryFailed"];

					if ($item["submission"] != "") {
						$submission = $item["submission"];
					}
					if ($item["interview"] != "") {
						$interview = $item["interview"];
					}
					if ($item["interviewDecline"] != "") {
						$interviewDecline = $item["interviewDecline"];
					}
					if ($item["offer"] != "") {
						$offer = $item["offer"];
					}
					if ($item["placed"] != "") {
						$placed = $item["placed"];
					}
					if ($item["extension"] != "") {
						$extension = $item["extension"];
					}
					if ($item["deliveryFailed"] != "") {
						$deliveryFailed = $item["deliveryFailed"];
					}

					$output .= "<tr class='tbody-tr-style'>
						<td nowrap>".$item["companyName"]."</td>
						<td>".$item["shared_with"]."</td>
						<td>".$submission."</td>
						<td>".$interview."</td>
						<td>".$interviewDecline."</td>
						<td>".$offer."</td>
						<td>".$placed."</td>
						<td>".$extension."</td>
						<td>".$deliveryFailed."</td>
						<td nowrap>".$item["startDate"]."</td>
						<td nowrap>".$item["endDate"]."</td>
					</tr>";
				}
			}
			
			if (count($sharedWith) != "0") {
				$totalClients = ((count(array_unique($clientId)) - count($sharedWith)) + (count($sharedWith) / 2))." + ".(count($sharedWith) / 2);
			} else {
				$totalClients = count(array_unique($clientId));
			}

			$output .= "</tbody>
				<tfoot>
					<tr class='tfoot-tr-style'>
						<th>".$totalClients."</th>
						<th>".count($sharedWith)."</th>
						<th>".array_sum($totalSubmission)."</th>
						<th>".array_sum($totalInterview)."</th>
						<th>".array_sum($totalInterviewDecline)."</th>
						<th>".array_sum($totalOffer)."</th>
						<th>".array_sum($totalPlaced)."</th>
						<th>".array_sum($totalExtension)."</th>
						<th>".array_sum($totalDeliveryFailed)."</th>
						<th colspan='2'></th>
					</tr>
				</tfoot>
			</table>";
	
		echo $output;
	}
?>