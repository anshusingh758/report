<?php
	error_reporting(0);
	include_once("../config.php");

	if ($_POST) {
		$startDate = $_POST["start_date"];
		$endDate = $_POST["end_date"];
		$twilioNumber = str_replace(' ', '',$_POST["number"]);
		$resultTwilioHistoryArray = json_decode($_POST['listArray'], true);

		function secondToTimeFormat($seconds) {
  			$t = round($seconds);
  			return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
		}
?>
		<table class='table table-striped table-bordered scrollable-datatable' width='100%'>
			<thead>
				<tr class='thead-tr-style'>
					<th colspan='8' style='font-size: 15px;'><?php echo 'From Number : +'.$twilioNumber; ?></th>
				</tr>
				<tr class='thead-tr-style'>
					<th>To</th>
					<th>Duration</th>
					<th>Status</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
			<?php
				if ($twilioNumber != "") {
					foreach ($resultTwilioHistoryArray as $twilioHistoryKey => $twilioHistoryValue) {
						 ?>
						<tr class='tbody-tr-style'>
							<td nowrap><?php echo str_replace(' ', '','+'.$twilioHistoryValue["to"]); ?></td>
							<td nowrap><?php echo secondToTimeFormat($twilioHistoryValue["duration"]); ?></td>
							<td nowrap><?php echo $twilioHistoryValue["status"]; ?></td>
							<td nowrap><?php echo $twilioHistoryValue["date"]; ?></td>
						</tr>
			<?php
					}
		 		} 
		 	?>
			</tbody>
		</table>
<?php
	} 
?>
