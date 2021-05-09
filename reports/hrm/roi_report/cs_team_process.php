<?php

    $gpQuery = "SELECT
        e.id AS employee_id,
        e.custom1 AS benefit,
        e.custom2 AS benefit_list,
        CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
        CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,
        es.id AS employment_id,
        es.name AS employment_type,
        comp.company_id,
        comp.name AS company_name,
        u.user_id AS recruiter_id,
        CONCAT(u.first_name,' ',u.last_name) AS recruiter_name,
        u.notes AS recruiter_manager,
        clf.mspChrg_pct AS client_msp_charge_percentage,
        clf.primechrg_pct AS client_prime_charge_percentage,
        clf.primeChrg_dlr AS client_prime_charge_dollar,
        clf.mspChrg_dlr AS client_msp_charge_dollar,
        cnf.c_primeCharge_pct AS employee_prime_charge_percentage,
        cnf.c_primeCharge_dlr AS employee_prime_charge_dollar,
        cnf.c_anyCharge_dlr AS employee_any_charge_dollar
    FROM
        vtechhrm.employees AS e
        LEFT JOIN vtechhrm.employeeprojects AS ep ON ep.employee = e.id
        LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
        LEFT JOIN vtechhrm.employeetimeentry AS ete ON ete.employee = e.id
        LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id
        LEFT JOIN cats.company AS comp ON comp.company_id = si.c_company_id
        LEFT JOIN cats.user AS u ON u.user_id = si.c_recruiter_id
        LEFT JOIN vtech_mappingdb.client_fees AS clf ON clf.client_id = comp.company_id
        LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON cnf.emp_id = e.id
    WHERE
        u.email = '$emailaddress'
    AND
        ep.project != '6'";

    if ($includePeriod == "true") {
        $gpQuery .= " AND
            DATE_FORMAT(e.custom7, '%Y-%m-%d') BETWEEN '$yearStartDate' AND '$yearEndDate'
        AND
            DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
        GROUP BY employee_id";
    } else {
        $gpQuery .= " AND
            DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
        GROUP BY employee_id";
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
        $managerAchievedGpTarget[$mainDataValue["manager_name"]][$dateRangeValue["date_range_value"]][] = $achievedGpTarget;
        $directorAchievedGpTarget[$mainDataValue["director_name"]][$dateRangeValue["date_range_value"]][] = $achievedGpTarget;
/*
        if ($mainROW["sow_product_cost"] != "" && $mainROW["sow_product_cost"] != NULL) {
	        $achievedGpTarget = $achievedGpTarget + $mainROW["sow_product_cost"];
	    }
*/
        $achievedGpTargetPercentage = round((($achievedGpTarget * 100) / $expectedGPTarget));
/*
        if ($achievedGpTargetPercentage > 100) {
            $achievedGpTargetPercentage = 100;
        }*/
    }

    $placementQuery = mysqli_query($allConn, "SELECT
        COUNT(DISTINCT cjsh.candidate_joborder_status_history_id) AS total_placed
    FROM
        cats.user AS u
        LEFT JOIN cats.candidate_joborder AS cj ON cj.added_by = u.user_id
        LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
    WHERE
        cjsh.status_to = '800'
    AND
        DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate'
    AND
        u.email = '$emailaddress'/*
    AND
        cjsh.candidate_id NOT IN (SELECT
        cjsh.candidate_id
    FROM
        cats.user AS u
        LEFT JOIN cats.candidate_joborder AS cj ON cj.added_by = u.user_id
        LEFT JOIN cats.candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
    WHERE
        cjsh.status_to = '620'
    AND
        u.email = '$emailaddress'
    AND
        u.access_level != '0'
    AND
        DATE_FORMAT(cjsh.date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')*/
    GROUP BY u.user_id");

    if (mysqli_num_rows($placementQuery) > 0) {
    	$placementRow = mysqli_fetch_array($placementQuery);
        $achievedPlacementTarget = $placementRow["total_placed"];
        $managerAchievedPlacementTarget[$mainDataValue["manager_name"]][$dateRangeValue["date_range_value"]][] = $achievedPlacementTarget;
        $directorAchievedPlacementTarget[$mainDataValue["director_name"]][$dateRangeValue["date_range_value"]][] = $achievedPlacementTarget;
    	$achievedPlacementTargetPercentage = round((($achievedPlacementTarget * 100) / $expectedPlacementTarget) , 2);
    }

?>