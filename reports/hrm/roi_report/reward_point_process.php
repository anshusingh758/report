<?php
	$feedbackQuery = mysqli_query($allConn, "SELECT
		mu.id AS personnel_id,
		mu.userfullname AS personnel_name,
	    mu.isactive AS personnel_status,
	    me.date_of_joining,
    	me.date_of_confirmation,
        mf.feedback_comment,
        mf.feedback_name AS feedback_given_by_name,
        mf.feedback_type,
        mf.feedback_from AS feedback_given_by_title,
        DATE_FORMAT(mf.feedback_date, '%m-%d-%Y') AS feedback_date,
        mf.authorized_by AS feedback_authorized_by_name,
        mr.feedback_point
    FROM
    	vtechhrm_in.main_empfeedback AS mf
    	LEFT JOIN vtechhrm_in.main_rewardpoints AS mr ON mr.feedback_from = mf.feedback_from
    	LEFT JOIN vtechhrm_in.main_users AS mu ON mu.id = mf.user_id
	    LEFT JOIN vtechhrm_in.main_employees AS me ON me.user_id = mf.user_id
    WHERE
    	me.department_id IN ($departmentIdList)
    AND
        mf.feedback_date BETWEEN '$startDate' AND '$endDate'
    AND
        mf.isactive = '1'
    ORDER BY mf.id DESC");

	if (mysqli_num_rows($feedbackQuery) > 0) {
		while ($feedbackRow = mysqli_fetch_array($feedbackQuery)) {
			if ($feedbackRow["feedback_type"] == "Positive") {
				$rewardPoint[$feedbackRow["personnel_id"]][] = $feedbackRow["feedback_point"];
	        } elseif ($feedbackRow["feedback_type"] == "Negative") {
	        	$rewardPoint[$feedbackRow["personnel_id"]][] = ($feedbackRow["feedback_point"] / 2);
	        }

	        $rewardPointBrief[$feedbackRow["personnel_id"]][] = array(
	        	"personnel_id" => $feedbackRow["personnel_id"],
				"personnel_name" => $feedbackRow["personnel_name"],
				"personnel_status" => $feedbackRow["personnel_status"],
				"date_of_joining" => $feedbackRow["date_of_joining"],
				"date_of_confirmation" => $feedbackRow["date_of_confirmation"],
	        	"feedback_comment" => $feedbackRow["feedback_comment"],
				"feedback_given_by_name" => $feedbackRow["feedback_given_by_name"],
				"feedback_type" => $feedbackRow["feedback_type"],
				"feedback_given_by_title" => $feedbackRow["feedback_given_by_title"],
				"feedback_date" => $feedbackRow["feedback_date"],
				"feedback_authorized_by_name" => $feedbackRow["feedback_authorized_by_name"],
				"feedback_point" => $feedbackRow["feedback_point"]
	        );
		}
	}
?>