<?php
	include("../../../security.php");
	include("../../../config.php");
	if ($_POST) {

		$personnelName = $_POST['personnelName'];
		$type = $_POST['type'];
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		$individualCandidate = $_POST['individualCandidate'];
		$individualGp = $_POST['individualGp'];
		$individualIncentive = $_POST['individualIncentive'];
		$individualIncentivePerc = $_POST['individualIncentivePerc'];
		$totalOpportunities = $_POST['totalOpportunities'];
		$totalBonus = $_POST['totalBonus'];
		$teamCandidate = $_POST['teamCandidate'];
		$teamGp = $_POST['teamGp'];
		$teamIncentive = $_POST['teamIncentive'];
		$teamIncentivePerc = $_POST['teamIncentivePerc'];
		$finalIncentive = $_POST['finalIncentive'];
		$adjustmentMethod = $_POST['adjustmentMethod'];
		$adjustmentAmount = $_POST['adjustmentAmount'];
		$adjustmentComment = $_POST['adjustmentComment'];
		$finalAmount = $_POST['finalAmount'];

		//$detailLink=isset($_SERVER["HTTPS"]) ? 'https:' : 'http:';
		$detailLink = $_POST["detailLink"];

		$key = CURL_SESSION_KEY;
		$userSession = $_SESSION['user'];
		$memberSession = $_SESSION['userMember'];

		$detailLink .= '&curl_session_key='.$key.'&user='.$userSession.'&userMember='.$memberSession;

		$detailData = "";

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $detailLink,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"postman-token: e71c1a3f-ef9e-8515-4be2-235e7b30c128"
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			$detailData = $response;
		}

		$query = "INSERT INTO usbd_incentive_data(person_name,type,start_date,end_date,individual_candidate,individual_gp,individual_incentive,individual_incentive_perc,total_opportunities,total_bonus,team_candidate,team_gp,team_incentive,team_incentive_perc,final_incentive,adjustment_method,adjustment_amount,adjustment_comment,final_amount,detail_data,addedby) VALUES('$personnelName','$type','$startDate','$endDate','$individualCandidate','$individualGp','$individualIncentive','$individualIncentivePerc','$totalOpportunities','$totalBonus','$teamCandidate','$teamGp','$teamIncentive','$teamIncentivePerc','$finalIncentive','$adjustmentMethod','$adjustmentAmount','$adjustmentComment','$finalAmount','$detailData','$user')";

		if (mysqli_query($misReportsConn, $query)) {
			echo "true";
		} else {
			echo "false";
		}
	}
?>
