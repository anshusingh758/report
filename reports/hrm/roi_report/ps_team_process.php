<?php

    $gpQuery = "SELECT
        ehd.*
    FROM
        vtechhrm.employees AS e
        LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
        LEFT JOIN vtech_mappingdb.employee_history_detail AS ehd ON ehd.employee_id = e.id
        LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
    WHERE
        si.c_inside_post_sales = '$personnelName'
    AND
        ehd.id IN (SELECT MAX(id) FROM vtech_mappingdb.employee_history_detail WHERE employee_id = ehd.employee_id AND ((created_at BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') OR (((created_at NOT BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') AND created_at < '$startDate 00:00:00') OR ((created_at NOT BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59') AND created_at > '$endDate 23:59:59'))))";

    if ($includePeriod == "true") {
        $gpQuery .= " AND
            e.custom7 BETWEEN '$yearStartDate 00:00:00' AND '$yearEndDate 23:59:59'
        AND
            ete.date_start BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        GROUP BY e.id";
    } else {
        $gpQuery .= " AND
            ete.date_start BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        GROUP BY e.id";
    }

    $gpResult = mysqli_query($allConn, $gpQuery);

    if (mysqli_num_rows($gpResult) > 0) {
        while ($gpRow = mysqli_fetch_array($gpResult)) {
            $benefitList = str_replace($delimiter, $delimiter[0], $gpRow["benefit_list"]);

            $taxRate = round(employeeTaxRateCalculator($taxSettingsTableData,$gpRow["benefit"],$benefitList,$gpRow["employment_id"],$gpRow["pay_rate"]), 2);

            $mspFees = round((($gpRow["client_msp_charge_percentage"] / 100) * $gpRow["bill_rate"]) + $gpRow["client_msp_charge_dollar"], 2);

            $primeCharges = round(((($gpRow["client_prime_charge_percentage"] / 100) * $gpRow["bill_rate"]) + (($gpRow["employee_prime_charge_percentage"] / 100) * $gpRow["bill_rate"]) + $gpRow["employee_prime_charge_dollar"] + $gpRow["employee_any_charge_dollar"] + $gpRow["client_prime_charge_dollar"]), 2);

            $candidateRate = round(($gpRow["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

            $grossMargin = round(($gpRow["bill_rate"] - $candidateRate), 2);

            $totalHour = round(array_sum($employeeTimeEntryTableData[$gpRow["employee_id"]]), 2);

            $totalGrossProfit[] = round(($grossMargin * $totalHour), 2);
        }

        $achievedGpTarget = round(array_sum($totalGrossProfit), 2);

        if ($mainROW["sow_product_cost"] != "" && $mainROW["sow_product_cost"] != NULL) {
	        $achievedGpTarget = $achievedGpTarget + $mainROW["sow_product_cost"];
	    }

        $achievedGpTargetPercentage = round((($achievedGpTarget * 100) / $expectedGPTarget));
/*
        if ($achievedGpTargetPercentage > 100) {
            $achievedGpTargetPercentage = 100;
        }*/
    }
/*
    $placementQuery = mysqli_query($allConn, "SELECT
        COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
    FROM
        cats.user AS u
        LEFT JOIN cats.extra_field AS ef ON ef.value = CONCAT(u.first_name,' ',u.last_name)
        LEFT JOIN cats.company AS comp ON comp.company_id = ef.data_item_id
        LEFT JOIN cats.joborder AS job ON job.company_id = comp.company_id
        LEFT JOIN cats.candidate_joborder AS cj ON cj.joborder_id = job.joborder_id
        LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
    WHERE
        cjsh.status_to = '800'
    AND
        ef.field_name = 'Inside Post Sales'
    AND
        DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
    AND
        ef.value = '$personnelName'
    AND
        cjsh.candidate_id NOT IN (SELECT
        cjsh.candidate_id
    FROM
        cats.user AS u
        LEFT JOIN cats.extra_field AS ef ON ef.value = CONCAT(u.first_name,' ',u.last_name)
        LEFT JOIN cats.company AS comp ON comp.company_id = ef.data_item_id
        LEFT JOIN cats.joborder AS job ON job.company_id = comp.company_id
        LEFT JOIN cats.candidate_joborder AS cj ON cj.joborder_id = job.joborder_id
        LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
    WHERE
        cjsh.status_to = '620'
    AND
        ef.field_name = 'Inside Post Sales'
    AND
        ef.value = '$personnelName'
    AND
        DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')
    GROUP BY u.user_id");

    if (mysqli_num_rows($placementQuery) > 0) {
    	$placementRow = mysqli_fetch_array($placementQuery);
        $achievedPlacementTarget = $placementRow["total_placed"];
    	$achievedPlacementTargetPercentage = round((($achievedPlacementTarget * 100) / $expectedPlacementTarget), 2);
    }
*/
?>