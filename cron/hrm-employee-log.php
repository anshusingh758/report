<?php
	include_once("../config.php");
	include_once("../functions/reporting-service.php");

	$insertQUERY = array();

	$marginNewEntryQUERY = mysqli_query($allConn, "SELECT * FROM vtech_mappingdb.hrm_employee_change_detail");

	if (mysqli_num_rows($marginNewEntryQUERY) > 0) {
		while ($marginNewEntryROW = mysqli_fetch_array($marginNewEntryQUERY)) {

			$employeeId = mysqli_real_escape_string($allConn, $marginNewEntryROW["employee_id"]);
			$empId = mysqli_real_escape_string($allConn, $marginNewEntryROW["emp_id"]);
			$employeeName = mysqli_real_escape_string($allConn, $marginNewEntryROW["employee_name"]);
			$employeeStatus = mysqli_real_escape_string($allConn, $marginNewEntryROW["employee_status"]);
			$joinDate = mysqli_real_escape_string($allConn, $marginNewEntryROW["join_date"]);
			$terminationDate = mysqli_real_escape_string($allConn, $marginNewEntryROW["termination_date"]);
			$benefit = mysqli_real_escape_string($allConn, $marginNewEntryROW["benefit"]);
			$benefitList = mysqli_real_escape_string($allConn, $marginNewEntryROW["benefit_list"]);
			$employmentId = mysqli_real_escape_string($allConn, $marginNewEntryROW["employment_id"]);
			$employmentType = mysqli_real_escape_string($allConn, $marginNewEntryROW["employment_type"]);
			$billRate = mysqli_real_escape_string($allConn, $marginNewEntryROW["bill_rate"]);
			$payRate = mysqli_real_escape_string($allConn, $marginNewEntryROW["pay_rate"]);
			$otRate = mysqli_real_escape_string($allConn, $marginNewEntryROW["ot_rate"]);
			$jobId = mysqli_real_escape_string($allConn, $marginNewEntryROW["job_id"]);
			$jobTitle = mysqli_real_escape_string($allConn, $marginNewEntryROW["job_title"]);
			$jobCity = mysqli_real_escape_string($allConn, $marginNewEntryROW["job_city"]);
			$jobState = mysqli_real_escape_string($allConn, $marginNewEntryROW["job_state"]);
			$recruiterId = mysqli_real_escape_string($allConn, $marginNewEntryROW["recruiter_id"]);
			$recruiterName = mysqli_real_escape_string($allConn, $marginNewEntryROW["recruiter_name"]);
			$recruiterManager = mysqli_real_escape_string($allConn, $marginNewEntryROW["recruiter_manager"]);
			$companyId = mysqli_real_escape_string($allConn, $marginNewEntryROW["company_id"]);
			$companyName = mysqli_real_escape_string($allConn, $marginNewEntryROW["company_name"]);
			$companyManagerId = mysqli_real_escape_string($allConn, $marginNewEntryROW["company_manager_id"]);
			$companyManagerName = mysqli_real_escape_string($allConn, $marginNewEntryROW["company_manager_name"]);
			$K401Rate = mysqli_real_escape_string($allConn, $marginNewEntryROW["K401_rate"]);
			$H1BRate = mysqli_real_escape_string($allConn, $marginNewEntryROW["H1B_rate"]);
			$healthInsuranceRate = mysqli_real_escape_string($allConn, $marginNewEntryROW["health_insurance_rate"]);
			$tenPaidHolidaysRate = mysqli_real_escape_string($allConn, $marginNewEntryROW["ten_paid_holidays_rate"]);
			$tenPaidLeaveRate = mysqli_real_escape_string($allConn, $marginNewEntryROW["ten_paid_leave_rate"]);
			$withBenefitTaxRate = mysqli_real_escape_string($allConn, $marginNewEntryROW["with_benefit_tax_rate"]);
			$withoutBenefitTaxRate = mysqli_real_escape_string($allConn, $marginNewEntryROW["without_benefit_tax_rate"]);
			$clientMspChargePercentage = mysqli_real_escape_string($allConn, $marginNewEntryROW["client_msp_charge_percentage"]);
			$clientMspChargeDollar = mysqli_real_escape_string($allConn, $marginNewEntryROW["client_msp_charge_dollar"]);
			$clientPrimeChargePercentage = mysqli_real_escape_string($allConn, $marginNewEntryROW["client_prime_charge_percentage"]);
			$clientPrimeChargeDollar = mysqli_real_escape_string($allConn, $marginNewEntryROW["client_prime_charge_dollar"]);
			$employeePrimeChargePercentage = mysqli_real_escape_string($allConn, $marginNewEntryROW["employee_prime_charge_percentage"]);
			$employeePrimeChargeDollar = mysqli_real_escape_string($allConn, $marginNewEntryROW["employee_prime_charge_dollar"]);
			$employeeAnyChargeDollar = mysqli_real_escape_string($allConn, $marginNewEntryROW["employee_any_charge_dollar"]);
			
			if ($marginNewEntryROW["benefit"] == "With Benefits") {
				$taxRate = round($marginNewEntryROW["pay_rate"] * (($marginNewEntryROW["K401_rate"] + $marginNewEntryROW["H1B_rate"] + $marginNewEntryROW["health_insurance_rate"] + $marginNewEntryROW["ten_paid_holidays_rate"] + $marginNewEntryROW["ten_paid_leave_rate"] + $marginNewEntryROW["with_benefit_tax_rate"]) / 100), 2);
			} elseif ($marginNewEntryROW["benefit"] == "Without Benefits" || $marginNewEntryROW["benefit"] == "" || $marginNewEntryROW["benefit"] == "Not Applicable") {
				$taxRate = round($marginNewEntryROW["pay_rate"] * ($marginNewEntryROW["without_benefit_tax_rate"] / 100), 2);
			} else {
				$taxRate = 0;
			}

			$mspFees = round((($marginNewEntryROW["client_msp_charge_percentage"] / 100) * $marginNewEntryROW["bill_rate"]) + $marginNewEntryROW["client_msp_charge_dollar"], 2);

			$primeCharges = round(((($marginNewEntryROW["client_prime_charge_percentage"] / 100) * $marginNewEntryROW["bill_rate"]) + (($marginNewEntryROW["employee_prime_charge_percentage"] / 100) * $marginNewEntryROW["bill_rate"]) + $marginNewEntryROW["employee_prime_charge_dollar"] + $marginNewEntryROW["employee_any_charge_dollar"] + $marginNewEntryROW["client_prime_charge_dollar"]), 2);

			$candidateRate = round(($marginNewEntryROW["pay_rate"] + $taxRate + $mspFees + $primeCharges), 2);

			$grossMargin = round(($marginNewEntryROW["bill_rate"] - $candidateRate), 2);

			$insertQUERY[] = "INSERT INTO vtech_mappingdb.hrm_employee_log(
				employee_id,
				emp_id,
				employee_name,
				employee_status,
				join_date,
				termination_date,
				benefit,
				benefit_list,
				employment_id,
				employment_type,
				bill_rate,
				pay_rate,
				ot_rate,
				job_id,
				job_title,
				job_city,
				job_state,
				recruiter_id,
				recruiter_name,
				recruiter_manager,
				company_id,
				company_name,
				company_manager_id,
				company_manager_name,
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
				tax_rate,
				msp_fees,
				prime_charges,
				candidate_rate,
				margin
			) VALUES(
			'$employeeId',
			'$empId',
			'$employeeName',
			'$employeeStatus',
			'$joinDate',
			'$terminationDate',
			'$benefit',
			'$benefitList',
			'$employmentId',
			'$employmentType',
			'$billRate',
			'$payRate',
			'$otRate',
			'$jobId',
			'$jobTitle',
			'$jobCity',
			'$jobState',
			'$recruiterId',
			'$recruiterName',
			'$recruiterManager',
			'$companyId',
			'$companyName',
			'$companyManagerId',
			'$companyManagerName',
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
			'$taxRate',
			'$mspFees',
			'$primeCharges',
			'$candidateRate',
			'$grossMargin'
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