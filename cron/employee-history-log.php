<?php
	include_once("../config.php");
	include_once("../functions/reporting-service.php");

	$insertQUERY = array();

	$employeeHistoryQUERY = mysqli_query($allConn, "SELECT * FROM vtech_mappingdb.employee_change_detail");

	if (mysqli_num_rows($employeeHistoryQUERY) > 0) {
		while ($employeeHistoryROW = mysqli_fetch_array($employeeHistoryQUERY)) {

			$employeeId = mysqli_real_escape_string($allConn, $employeeHistoryROW["employee_id"]);
			
			$benefit = mysqli_real_escape_string($allConn, $employeeHistoryROW["benefit"]);
			
			$benefitList = mysqli_real_escape_string($allConn, $employeeHistoryROW["benefit_list"]);
			
			$employmentId = mysqli_real_escape_string($allConn, $employeeHistoryROW["employment_id"]);
			
			$employmentType = mysqli_real_escape_string($allConn, $employeeHistoryROW["employment_type"]);

			$billRate = mysqli_real_escape_string($allConn, $employeeHistoryROW["bill_rate"]);
			
			$payRate = mysqli_real_escape_string($allConn, $employeeHistoryROW["pay_rate"]);
			
			$otRate = mysqli_real_escape_string($allConn, $employeeHistoryROW["ot_rate"]);
			
			$K401Rate = mysqli_real_escape_string($allConn, $employeeHistoryROW["K401_rate"]);
			
			$H1BRate = mysqli_real_escape_string($allConn, $employeeHistoryROW["H1B_rate"]);
			
			$healthInsuranceRate = mysqli_real_escape_string($allConn, $employeeHistoryROW["health_insurance_rate"]);
			
			$tenPaidHolidaysRate = mysqli_real_escape_string($allConn, $employeeHistoryROW["ten_paid_holidays_rate"]);
			
			$tenPaidLeaveRate = mysqli_real_escape_string($allConn, $employeeHistoryROW["ten_paid_leave_rate"]);
			
			$withBenefitTaxRate = mysqli_real_escape_string($allConn, $employeeHistoryROW["with_benefit_tax_rate"]);
			
			$withoutBenefitTaxRate = mysqli_real_escape_string($allConn, $employeeHistoryROW["without_benefit_tax_rate"]);
			
			$clientMspChargePercentage = mysqli_real_escape_string($allConn, $employeeHistoryROW["client_msp_charge_percentage"]);
			
			$clientMspChargeDollar = mysqli_real_escape_string($allConn, $employeeHistoryROW["client_msp_charge_dollar"]);
			
			$clientPrimeChargePercentage = mysqli_real_escape_string($allConn, $employeeHistoryROW["client_prime_charge_percentage"]);
			
			$clientPrimeChargeDollar = mysqli_real_escape_string($allConn, $employeeHistoryROW["client_prime_charge_dollar"]);
			
			$employeePrimeChargePercentage = mysqli_real_escape_string($allConn, $employeeHistoryROW["employee_prime_charge_percentage"]);
			
			$employeePrimeChargeDollar = mysqli_real_escape_string($allConn, $employeeHistoryROW["employee_prime_charge_dollar"]);
			
			$employeeAnyChargeDollar = mysqli_real_escape_string($allConn, $employeeHistoryROW["employee_any_charge_dollar"]);

			$eaPerson = mysqli_real_escape_string($allConn, $employeeHistoryROW["ea_person"]);
			
			$insertQUERY[] = "INSERT INTO vtech_mappingdb.employee_history_detail(
				employee_id,
				benefit,
				benefit_list,
				employment_id,
				employment_type,
				bill_rate,
				pay_rate,
				ot_rate,
				K401_rate,
				H1B_rate,
				health_insurance_rate,
				ten_paid_holidays_rate,
				ten_paid_leave_rate,
				with_benefit_tax_rate,
				without_benefit_tax_rate,
				client_msp_charge_percentage,
				client_msp_charge_dollar,
				client_prime_charge_percentage,
				client_prime_charge_dollar,
				employee_prime_charge_percentage,
				employee_prime_charge_dollar,
				employee_any_charge_dollar,
				ea_person
			) VALUES(
			'$employeeId',
			'$benefit',
			'$benefitList',
			'$employmentId',
			'$employmentType',
			'$billRate',
			'$payRate',
			'$otRate',
			'$K401Rate',
			'$H1BRate',
			'$healthInsuranceRate',
			'$tenPaidHolidaysRate',
			'$tenPaidLeaveRate',
			'$withBenefitTaxRate',
			'$withoutBenefitTaxRate',
			'$clientMspChargePercentage',
			'$clientMspChargeDollar',
			'$clientPrimeChargePercentage',
			'$clientPrimeChargeDollar',
			'$employeePrimeChargePercentage',
			'$employeePrimeChargeDollar',
			'$employeeAnyChargeDollar',
			'$eaPerson'
		)";

		}
	}

	if (count($insertQUERY) > 0) {
		$insertROW = implode(";", $insertQUERY);
		if (mysqli_multi_query($allConn, $insertROW)) {
			echo "Total (".count($insertQUERY).") New Records Inserted!";
		} else {
			echo "Error Inserting New Records!";
		}
	} else {
		echo "No New Records!";
	}
?>