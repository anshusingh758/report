<?php

	$personnelSalary = $finalDataValue["personnel_salary"];

	if ($personnelSalary != 0 && $personnelSalary != "") {
        $expectedROI = round(((($personnelSalary * $departmentRate) / 365) * $finalDataValue["given_days"]), 2);

        $achievedROIPercentage = round((($directorAchievedGpTarget2 / $expectedROI) * 100));
        /*
        if ($achievedROIPercentage > 100) {
            $achievedROIPercentage = 100;
        }*/

		$achievedROIStageFontColor = "#fff";

        if ($achievedROIPercentage < 30) {
            $achievedROIStage = "Poor";
            $achievedROIStageColor = "red";
        } else if ($achievedROIPercentage >= 30 && $achievedROIPercentage < 50) {
            $achievedROIStage = "Ok";
            $achievedROIStageColor = "yellow";
            $achievedROIStageFontColor = "#000";
        } else if ($achievedROIPercentage >= 50 && $achievedROIPercentage < 80) {
            $achievedROIStage = "Good";
            $achievedROIStageColor = "green";
        } else if ($achievedROIPercentage >= 80 && $achievedROIPercentage <= 100) {
            $achievedROIStage = "Great";
            $achievedROIStageColor = "blue";
        } else {
            $achievedROIStage = "Excellent";
            $achievedROIStageColor = "#FF1493";
        }
    }

?>