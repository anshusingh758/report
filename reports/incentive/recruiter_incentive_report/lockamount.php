<?php
	include("../../../security.php");
	include('../../../config.php');
	if($_POST){
		foreach($_POST["checked_id"] as $key=>$value){
			$person_id=$_POST["person_id"][$key];
			$person_name=$_POST["person_name"][$key];
			$manager_name=$_POST["manager_name"][$key];
			$type_data=$_POST["type_data"][$key];
			$period=$_POST["period"][$key];
			$total_candidate=$_POST["total_candidate"][$key];
			$total_join=$_POST["total_join"][$key];
			$mainamount=$_POST["mainamount"][$key];
			$rec_per_hire=$_POST["rec_per_hire"][$key];
			$rec_new_acc_crack=$_POST["rec_new_acc_crack"][$key];
			$final_incentive=$_POST["final_incentive"][$key];
			$adjustment_method=$_POST["adjustment_method"][$key];
			$adjustment_amount=$_POST["adjustment_amount"][$key];
			$adjustment_comment=$_POST["adjustment_comment"][$key];
			$final_amount=$_POST["final_amount"][$key];

			$detail_link = $_POST["detail_link"][$key];

			$key=CURL_SESSION_KEY;
			$userSession=$_SESSION['user'];
			$memberSession=$_SESSION['userMember'];

			$detail_link.='&curl_session_key='.$key.'&user='.$userSession.'&userMember='.$memberSession;

			$detail_data='';
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $detail_link,
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
			if($err){
				echo "cURL Error #:" . $err;
			}else{
				$detail_data=$response;
			}

			$query[]="INSERT INTO incentive_data(person_id,person_name,manager_name,type,period,total_candidate,total_join,incentive_amount,rec_per_hire,rec_new_acc_crack,final_incentive,adjustment_method,adjustment_amount,adjustment_comment,final_amount,detail_data,addedby) VALUES('$person_id','$person_name','$manager_name','$type_data','$period','$total_candidate','$total_join','$mainamount','$rec_per_hire','$rec_new_acc_crack','$final_incentive','$adjustment_method','$adjustment_amount','$adjustment_comment','$final_amount','$detail_data','$user')";
		}

		$query1=array_unique($query);
		$query2=count(array_unique($query1));
		$query4=implode(";",$query1);
		if($query2>0){
			mysqli_multi_query($misReportsConn,$query4);
		}
	}
?>
