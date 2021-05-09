<?php
	include_once("../../../constants.php");

	if ($_POST) {
		$multipleMonth = $_POST["multipleMonth"];
		$fromDate =  $_POST["fromDate"];
		$toDate = $_POST["toDate"];
		$multipleQuarter =  $_POST["multipleQuarter"];
		$clientName = $_POST["clientName"];
		$recruiterName =  $_POST["recruiterName"];
	
		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_URL => SAAS_PATH_HTTPS .'/api/v1/keystring/keystringreport',
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 400,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => "POST",
		    CURLOPT_POSTFIELDS => "multipleMonth=".json_encode($multipleMonth)."&fromDate=".json_encode($fromDate)."&toDate=".json_encode($toDate)."&multipleQuarter=".json_encode($multipleQuarter)."&clientName=".json_encode($clientName)."&recruiterName=".json_encode($recruiterName)
		));

	    $response = curl_exec($curl);
	    curl_close($curl);
	    $responseArray = json_decode($response, TRUE);
?>
<style>
	table.innerTableClass tr {
		border-bottom: 1px solid #ddd;
	}
	table.innerTableClass tr:last-child {
		border-bottom: none;
	}
	table.innerTableClass tr td {
		border-right: 1px solid #ddd;
	}
	table.innerTableClass tr td:last-child {
		border-right: none;
	}
	table#keystringtTable thead tr {
		background-color: #ccc;
	}
	table#keystringtTable thead tr th {
		text-align: left;vertical-align: middle;
	}
	table#keystringtTable tbody tr td {
		text-align: center;vertical-align: middle;
	}
</style>

<table id="keystringtTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
      	<tr>
	      	<th>Sr.<br>No</th>
	        <th>Recruiter</th>
	        <th>CS Manager</th>
	    <?php
	        if ($multipleMonth != '') {
	    ?>
	    	<th>Month</th>
	   	<?php
	   		}
	   		if ($multipleQuarter != '') {
	   	?>
	   		<th>Quater</th> <?php } ?>
	        <th>Key String</th>
	        <th style="width: 106px;">Date Time</th>
	        <th>Job Portal Name</th>
      	</tr>
    </thead>
    <tbody>
	<?php
		if (!empty($responseArray)) {
			$counter = 0;
	 		foreach ($responseArray as $keyOfCandidate => $getTheCandidateList) {
	 			
  	?>
		<tr>
	  		<td><?php echo ++$counter; ?></td>
	  		<td><?php echo $getTheCandidateList['recruiterName']; ?></td>
	  		<td><?php echo $getTheCandidateList['managerName']; ?></td>
	  		<?php
	  			if ($multipleMonth != '') {
	      			echo "<td>".date("F", strtotime($getTheCandidateList['date']))."</td>";
	      		} elseif ($multipleQuarter != '') {
	      			$monthNumber = date("n", strtotime($getTheCandidateList['date']));
		      		if($monthNumber <= 3) {
						echo "<td>Q1</td>";
					} elseif($month <= 6) {
						echo "<td>Q2</td>";
					} elseif($month <= 9) { 
						echo "<td>Q3</td>";
					} else {
						echo "<td>Q4</td>";
					}
	      		}
	      	?>
	  		<td style="text-align: left;">
	    	<a class="keystringBriefView" data-output='<?php echo json_encode($getTheCandidateList['keystring']); ?>' data-keyword = '<?php echo $getTheCandidateList['keyword']; ?>'><?php echo $getTheCandidateList['keyword']; ?></a>
			</td>
	  		<td><?php  echo $getTheCandidateList['date']; ?></td>
	  		<td>
	  			<table class="innerTableClass">
	      		<?php foreach ($getTheCandidateList["logs"] as $ikey => $ivalue) {
	      		?>
	      			<tr>
	      				<td><?php echo $ivalue["resource"]; ?></td>
	      				<td><?php echo $ivalue["count"]; ?></td>
	      			</tr>
	      		<?php
	      			}
	      		?>
	  			</table>
	  		</td>
	  	</tr>
  	<?php
      		}
      	}
    ?>
    </tbody>
</table>
<script>
	$(document).ready(function() {
    	$('#keystringtTable').DataTable();
	});
</script>
<?php
	}
?>