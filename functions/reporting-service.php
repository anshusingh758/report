<?php

	function findSessionItem($misReportsConn,$user,$reportId) {
		
		$sessionQUERY = mysqli_query($misReportsConn, "SELECT
			*
		FROM
			mapping AS m
			JOIN users AS u ON u.uid = m.uid
			JOIN reports AS r ON r.rid = m.rid
		WHERE
			m.uid = '$user'
		AND
			m.rid = '$reportId'
		AND
			u.ustatus = '1'
		AND
			r.rstatus = '1'");

		return $sessionQUERY;
	}

	function catsClientList($catsConn) {
		$optionList = array();
		$clientQUERY = mysqli_query($catsConn, "SELECT
			company_id,
			name
		FROM
			company
		GROUP BY company_id
		ORDER BY name ASC");
		while($clientROW = mysqli_fetch_array($clientQUERY)) {
			$optionList[] = array("id" => $clientROW['company_id'],
			"name" => $clientROW['name']);
		}
		return $optionList;
	}

	function catsIndustriesList($catsConn) {
		$optionList = array();

		$industriesQUERY = mysqli_query($catsConn, "SELECT
			value
		FROM
			extra_field
		WHERE
			field_name = 'Industry Type'
		AND
			value !=''
		GROUP BY value");
		while($industriesROW = mysqli_fetch_array($industriesQUERY)) {
			$optionList[] = $industriesROW['value'];
		}

		return $optionList;
	}

	function hrmEmployeeList($vtechhrmConn,$clientGroup) {
		$optionList = array();

		$employeeQUERY = "SELECT
			e.id,
			CONCAT(e.first_name,' ',e.last_name) AS employee_name,
			e.status
		FROM
			employees AS e
			LEFT JOIN vtechhrm.employeeprojects AS ep ON e.id = ep.employee
			LEFT JOIN vtech_mappingdb.system_integration AS si ON si.h_employee_id = e.id";

		if ($clientGroup == "All" || $clientGroup == "") {
			$employeeQUERY .= " WHERE
				ep.project != '6'
			GROUP BY e.id
			ORDER BY employee_name ASC";
		} else {
			$employeeQUERY .= " WHERE
				si.c_company_id IN ($clientGroup)
			AND
				ep.project != '6'
			GROUP BY e.id
			ORDER BY employee_name ASC";
		}

		$employeeRESULT = mysqli_query($vtechhrmConn, $employeeQUERY);

		while($employeeROW = mysqli_fetch_array($employeeRESULT)) {
			$optionList[] = array("id" => $employeeROW['id'],
			"name" => $employeeROW['employee_name']." - ".$employeeROW["status"]);
		}
		
		return $optionList;
	}

	function catsExtraFieldPersonnelList($catsConn,$fieldName) {
		$optionList = $personnelGroup = array();

		$fieldList = "'".$fieldName."'";
		
		if ($fieldName == "Inside Sales") {
			$fieldList = "'Inside Sales Person1','Inside Sales Person2'";
		}
		
		$personnelQUERY = mysqli_query($catsConn, "SELECT
		    extra_field_options
		FROM
		    extra_field_settings
		WHERE
		    field_name IN ($fieldList)");

		while ($personnelROW = mysqli_fetch_array($personnelQUERY)) {
		    $personnelList = explode(",", str_replace("+", " ", $personnelROW['extra_field_options']));

		    foreach ($personnelList AS $personnelKey => $personnelValue) {
		        if ($personnelValue != "") {
		            $personnelGroup[] = $personnelValue;
		        }
		    }
		}

		//$personnelGroup[] = "Anand Moghe";

		$optionList = array_unique($personnelGroup);

		return $optionList;
	}

	function catsExtraFieldPersonnelListByDirector($allConn,$personnelFullName) {
		$optionList = $personnelGroup = $personnelGroup2 = array();

		if ($personnelFullName == 'Pratish Naik') {
			$personnelFullName = 'Chirag Sulakhe';
		}
		
		$personnelQUERY2 = mysqli_query($allConn, "SELECT
		    efs.extra_field_options
		FROM
		    cats.extra_field_settings AS efs
		WHERE
		    efs.field_name = 'Manager - Client Service'");

		while ($personnelROW2 = mysqli_fetch_array($personnelQUERY2)) {
		    $personnelList2 = explode(",", str_replace("+", " ", $personnelROW2['extra_field_options']));

		    foreach ($personnelList2 AS $personnelKey2 => $personnelValue2) {
		        if ($personnelValue2 != "") {
		            $personnelGroup2[] = $personnelValue2;
		        }
		    }
		}

		//$personnelGroup2[] = "Anand Moghe";


		$personnelQUERY = mysqli_query($allConn, "SELECT
		    CONCAT(u.first_name,' ',u.last_name) AS personnel_name
		FROM
		    cats.user AS u
		    LEFT JOIN vtech_mappingdb.manage_cats_roles AS mcr ON mcr.user_id = u.user_id
		WHERE
		    u.notes = '$personnelFullName'
		AND
			mcr.department = 'CS Team'
		GROUP BY u.user_id");

		while ($personnelROW = mysqli_fetch_array($personnelQUERY)) {
		    $personnelGroup[] = $personnelROW["personnel_name"];
		}

		$personnelGroup[] = $personnelFullName;

		$optionList = array_unique(array_intersect($personnelGroup,$personnelGroup2));

		return $optionList;
	}

	function findManagerIdFromCatsExtraFieldList($catsConn) {
		$finalList = $optionList = array();

		$personnelQUERY = mysqli_query($catsConn, "SELECT
			extra_field_options
		FROM
			extra_field_settings
		WHERE
			field_name = 'Manager - Client Service'");

		$personnelROW = mysqli_fetch_array($personnelQUERY);

		$personnelGroup = explode(",", str_replace("+", " ", $personnelROW['extra_field_options']));
		unset($personnelGroup[0]);
		$optionList = array_unique($personnelGroup);

		$optionLists = "'".implode("', '",$optionList)."'";

		$userQUERY = mysqli_query($catsConn, "SELECT
			user_id,
			CONCAT(first_name,' ',last_name) AS name
		FROM
			user
		WHERE
			CONCAT(first_name,' ',last_name) IN ($optionLists)
		GROUP BY user_id
		ORDER BY name");

		while($userROW = mysqli_fetch_array($userQUERY)) {
			$finalList[] = array("id" => $userROW['user_id'],
			"name" => $userROW['name']);
		}
		
		return $finalList;
	}

	function findClientByManager($catsConn,$personnel) {
		$optionList = array();
		$clientQUERY = mysqli_query($catsConn, "SELECT
			c.company_id,
			c.name
		FROM
			company AS c
			JOIN user AS u ON u.user_id = c.owner
		WHERE
			CONCAT(u.first_name,' ',u.last_name) = '$personnel'
		GROUP BY c.name
		ORDER BY c.name ASC");
		while($clientROW = mysqli_fetch_array($clientQUERY)) {
			$optionList[] = array("id" => $clientROW['company_id'],
			"name" => $clientROW['name']);
		}
		return $optionList;
	}

	function findClientByPersonnel($catsConn,$personnel,$type) {
		$optionList = array();
		$typeList = "'".$type."'";
		if ($type == "Inside Sales") {
			$typeList = "'Inside Sales Person1','Inside Sales Person2','Research By'";
		}
		$clientQUERY = mysqli_query($catsConn, "SELECT
			c.company_id,
			c.name
		FROM
			company AS c
			JOIN extra_field AS e ON c.company_id = e.data_item_id
		WHERE
			e.value = '$personnel'
		AND
			e.field_name IN ($typeList)
		GROUP BY c.name
		ORDER BY c.name ASC");
		while($clientROW = mysqli_fetch_array($clientQUERY)) {
			$optionList[] = array("id" => $clientROW['company_id'],
			"name" => $clientROW['name']);
		}
		return $optionList;
	}

	function catsRecruiterList($catsConn,$personnel) {
		$optionList = array();
		
		$personnelGroupList = "";

		if (is_array($personnel)) {
			$personnelGroupList = "'".implode("', '", $personnel)."'";
		} elseif ($personnel != "") {
			$personnelGroupList = "'".$personnel."'";
		}

		$recruiterQUERY = "SELECT
			u.user_id,
			CONCAT(u.first_name,' ',u.last_name) AS recruiter,
			IF(u.access_level = '0', 'Inactive', 'Active') AS status
		FROM
			user AS u
		    JOIN vtech_mappingdb.manage_cats_roles AS mcr ON u.user_id = mcr.user_id
		WHERE
			mcr.department = 'CS Team'
		AND
			mcr.designation NOT IN ('Manager - Client Services','Senior Manager - Client Services','Associate Manager - Client Service')";

		if ($personnelGroupList != "") {
			$recruiterQUERY .= " AND
				u.notes IN ($personnelGroupList)
			GROUP BY user_id
			ORDER BY recruiter ASC";
		} else {
			$recruiterQUERY .= " GROUP BY user_id
			ORDER BY recruiter ASC";
		}

		$recruiterRESULT = mysqli_query($catsConn, $recruiterQUERY);
		while($recruiterROW = mysqli_fetch_array($recruiterRESULT)) {
			$optionList[] = array(
				"id" => $recruiterROW['user_id'],
				"name" => $recruiterROW['recruiter'],
				"status" => $recruiterROW["status"]
			);
		}

		return $optionList;
	}

	function sourcingTeamList($allConn) {
		$optionList = array();
		
		$sourcingTeamQUERY = "SELECT
			u.user_id AS sourcing_personnel_id,
			CONCAT(u.first_name,' ',u.last_name) AS sourcing_personnel_name
		FROM
			cats.user AS u
			JOIN vtech_mappingdb.manage_cats_roles AS mcr ON mcr.user_id = u.user_id
		WHERE
			mcr.designation LIKE '%Sourcing%'
		GROUP BY sourcing_personnel_id
		ORDER BY sourcing_personnel_name ASC";

		$sourcingTeamRESULT = mysqli_query($allConn, $sourcingTeamQUERY);
		while($sourcingTeamROW = mysqli_fetch_array($sourcingTeamRESULT)) {
			$optionList[] = array("id" => $sourcingTeamROW['sourcing_personnel_id'],
			"name" => $sourcingTeamROW['sourcing_personnel_name']);
		}

		return $optionList;
	}

	function catsUserList($catsConn) {
		$optionList = array();
		
		$recruiterQUERY = "SELECT
			u.user_id,
			CONCAT(u.first_name,' ',u.last_name) AS recruiter
		FROM
			user AS u
		GROUP BY user_id
		ORDER BY recruiter ASC";

		$recruiterRESULT = mysqli_query($catsConn, $recruiterQUERY);
		while($recruiterROW = mysqli_fetch_array($recruiterRESULT)) {
			$optionList[] = array("id" => $recruiterROW['user_id'],
			"name" => $recruiterROW['recruiter']);
		}

		return $optionList;
	}

	function taxSettingsTable($allConn) {
		$taxSettings = array();

		$taxQUERY = mysqli_query($allConn, "SELECT
			ts.id,
			ts.empst_id,
			ts.emp_type,
			ts.benefits,
			ts.charge_pct
		FROM
			vtech_mappingdb.tax_settings AS ts
		GROUP BY ts.id");

		while ($taxROW = mysqli_fetch_array($taxQUERY)) {
			$taxSettings[$taxROW["empst_id"]][$taxROW["benefits"]] = $taxROW["charge_pct"];
		}

		return $taxSettings;
	}

	function employeeTaxRateCalculator($taxSettingsTableData,$benefit,$benefitList,$employmentId,$payRate) {
		$taxRate = 0;

		if ($benefit == "With Benefits") {
			$benefitListGroup = explode(",", $benefitList);

			foreach ($benefitListGroup as $benefitListKey => $benefitListValue) {
				$taxRate += round(($payRate * ($taxSettingsTableData[$employmentId][$benefitListValue] / 100)), 2);
			}

			$taxRate = round(($taxRate + ($payRate * ($taxSettingsTableData[$employmentId]["Tax"] / 100))), 2);
		} elseif ($benefit == "Without Benefits" || $benefit == "" || $benefit == "Not Applicable") {
			$taxRate = round(($payRate * ($taxSettingsTableData[$employmentId]["Without Benefits"] / 100)), 2);
		}

		return $taxRate;
	}

	function employeeTaxRate($vtechMappingdbConn,$benefit,$benefitList,$employmentId,$payRate) {
		$taxRate = 0;

		if ($benefit == "With Benefits") {
			$benefitListGroup = explode(",", $benefitList);

			foreach ($benefitListGroup as $benefitListKey => $benefitListValue) {
				$taxQUERY = mysqli_query($vtechMappingdbConn, "SELECT
					charge_pct
				FROM
					tax_settings
				WHERE
					empst_id = '$employmentId'
				AND
					benefits LIKE '%$benefitListValue%'");
				
				$taxROW = mysqli_fetch_array($taxQUERY);

				$taxRate += $payRate * ($taxROW["charge_pct"] / 100);
			}

			$mainTaxQUERY = mysqli_query($vtechMappingdbConn, "SELECT
				charge_pct
			FROM
				tax_settings
			WHERE
				empst_id = '$employmentId'
			AND
				benefits = 'Tax'");

			$mainTaxROW = mysqli_fetch_array($mainTaxQUERY);

			$taxRate = $taxRate + ($payRate * ($mainTaxROW["charge_pct"] / 100));

		} elseif ($benefit == "Without Benefits" || $benefit == "" || $benefit == "Not Applicable") {
			$taxQUERY = mysqli_query($vtechMappingdbConn, "SELECT
				charge_pct
			FROM
				tax_settings
			WHERE
				empst_id = '$employmentId'
			AND
				benefits = 'Without Benefits'");
			
			$taxROW = mysqli_fetch_array($taxQUERY);
			$taxRate = $payRate * ($taxROW["charge_pct"] / 100);
		}

		return $taxRate;
	}

	function decimalHours($time) {
		$tms = explode(":", $time);
		return ($tms[0] + ($tms[1] / 60) + ($tms[2] / 3600));
	}

	function employeeTimeEntryTable($allConn,$fromDate,$toDate) {
		$employeeTimeEntryData = array();
	
		$employeeTimeEntryQUERY = mysqli_query($allConn, "SELECT
			ete.employee,
			ete.time_start,
			ete.time_end
		FROM
			vtechhrm.employeetimeentry AS ete
		WHERE
			DATE_FORMAT(ete.date_start, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
		GROUP BY ete.id");
		
		while ($employeeTimeEntryROW = mysqli_fetch_array($employeeTimeEntryQUERY)) {
			$employeeTimeEntryData[$employeeTimeEntryROW["employee"]][] = (decimalHours($employeeTimeEntryROW["time_end"]) - decimalHours($employeeTimeEntryROW["time_start"]));
		}

		return $employeeTimeEntryData;
	}

	function employeeWorkingHours($vtechhrmConn,$fromDate,$toDate,$employeeId) {
		$totalHour = 0;
	
		$timeEntryQUERY = mysqli_query($vtechhrmConn, "SELECT
			time_start,
			time_end
		FROM
			employeetimeentry
		WHERE
			employee = '$employeeId'
		AND
			date_format(date_start, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
		
		while ($timeEntryROW = mysqli_fetch_array($timeEntryQUERY)) {
			$totalHour += (decimalHours($timeEntryROW["time_end"]) - decimalHours($timeEntryROW["time_start"]));
		}

		return $totalHour;
	}

	function employeeRegularHours($allConn,$fromDate,$toDate,$employeeId,$counterDate) {
		$regularHours = $givenDateArray = array();
	
		$timeEntryQUERY = mysqli_query($allConn, "SELECT
			date_format(ete.date_start, '%Y-%m-%d') AS given_date,
			ete.time_start,
			ete.time_end
		FROM
			vtechhrm.employeetimeentry AS ete
		WHERE
			ete.employee = '$employeeId'
		AND
			date_format(ete.date_start, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
		
		while ($timeEntryROW = mysqli_fetch_array($timeEntryQUERY)) {
			$regularHours[$timeEntryROW["given_date"]] = (decimalHours($timeEntryROW["time_end"]) - decimalHours($timeEntryROW["time_start"]));
			$givenDateArray[] = $timeEntryROW["given_date"];
		}

		foreach($counterDate AS $counterDateKey => $counterDateValue) {
			if (!in_array($counterDateValue, $givenDateArray)) {
				$regularHours[$counterDateValue] = "";
			}
		}

		ksort($regularHours);
		
		return $regularHours;
	}

	function employeeOverTimeHours($allConn,$fromDate,$toDate,$employeeId,$counterDate) {
		$overTimeHours = $givenDateArray = array();
	
		$overTimeQUERY = mysqli_query($allConn, "SELECT
			DATE_FORMAT(vt.date_start, '%Y-%m-%d') AS given_date,
			vt.overtime
		FROM
			vtechhrm.vtech_timesheet AS vt
		WHERE
			vt.employee = '$employeeId'
		AND
			DATE_FORMAT(vt.date_start, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
		GROUP BY vt.id");
		
		while ($overTimeROW = mysqli_fetch_array($overTimeQUERY)) {
			$overTimeHours[$overTimeROW["given_date"]] = $overTimeROW["overtime"];
			$givenDateArray[] = $overTimeROW["given_date"];
		}

		foreach($counterDate AS $counterDateKey => $counterDateValue) {
			if (!in_array($counterDateValue, $givenDateArray)) {
				$overTimeHours[$counterDateValue] = "";
			}
		}

		ksort($overTimeHours);
		
		return $overTimeHours;
	}

	function isEmployeeInvoiced($allConn,$fromDate,$toDate,$employeeId,$counterDate) {
		$invoiceCount = $givenDateArray = array();
	
		$invoiceQUERY = mysqli_query($allConn, "SELECT
			ir.id,
			DATE_FORMAT(ir.date, '%Y-%m-%d') AS given_date
		FROM
			vtech_mappingdb.invoice_report AS ir
		WHERE
			ir.eid = '$employeeId'
		AND
			ir.date BETWEEN '$fromDate' AND '$toDate'
		GROUP BY ir.id");
		
		while ($invoiceROW = mysqli_fetch_array($invoiceQUERY)) {
			$invoiceCount[$invoiceROW["given_date"]] = "1";
			$givenDateArray[] = $invoiceROW["given_date"];
		}

		foreach($counterDate AS $counterDateKey => $counterDateValue) {
			if (!in_array($counterDateValue, $givenDateArray)) {
				$invoiceCount[$counterDateValue] = "0";
			}
		}

		ksort($invoiceCount);
		
		return $invoiceCount;
	}

	function eaPersonnelList($catsConn) {
		$optionList = array();
		$eaTeamQUERY = mysqli_query($catsConn, "SELECT
			concat(u.first_name,' ',u.last_name) AS personnelName
		FROM
			user AS u
		    JOIN vtech_mappingdb.manage_cats_roles AS mcr ON u.user_id = mcr.user_id
		WHERE
			mcr.department = 'EA Team'
		AND
			u.access_level != '0'
		AND
			u.user_id != '1437'");
		while($eaTeamROW = mysqli_fetch_array($eaTeamQUERY)) {
			$optionList[] = "<option value='".ucwords($eaTeamROW['personnelName'])."'>".ucwords($eaTeamROW['personnelName'])."</option>";
		}
		return $optionList;
	}

	function catsRolesDepartmentList($vtechMappingdbConn) {
		$optionList = array();
		$optionList[] = '<option value="">Select Department</option>';
		$departmentQUERY = mysqli_query($vtechMappingdbConn, "SELECT name FROM department ORDER BY name ASC");
		while($departmentROW = mysqli_fetch_array($departmentQUERY)) {
			$optionList[] = "<option value='".ucwords($departmentROW['name'])."'>".ucwords($departmentROW['name'])."</option>";
		}
		return $optionList;
	}

	function catsRolesDesignationList($allConn) {
		$optionList = array();
		$optionList[] = '<option value="">Select Designation</option>';
		$designationQUERY = mysqli_query($allConn, "SELECT jobtitlename FROM vtechhrm_in.main_jobtitles GROUP BY jobtitlename ORDER BY jobtitlename ASC");
		while($designationROW = mysqli_fetch_array($designationQUERY)) {
			$optionList[] = "<option value='".ucwords($designationROW['jobtitlename'])."'>".ucwords($designationROW['jobtitlename'])."</option>";
		}
		return $optionList;
	}

	function catsRolesManagerList($catsConn) {
		$optionList = array();
		$optionList[] = '<option value="">Select Manager</option>';

		$managerListQuery = mysqli_query($catsConn, "SELECT
			extra_field_options
		FROM
			extra_field_settings
		WHERE
			field_name IN ('Manager - Client Service','Manager - Sourcing')");

		while ($managerListRow = mysqli_fetch_array($managerListQuery)) {
			$managerListNameGroup = array();
			$managerListNameGroup = explode(",", str_replace("+", " ", $managerListRow['extra_field_options']));
			unset($managerListNameGroup[0]);

			foreach ($managerListNameGroup as $managerListNameGroupKey => $managerListNameGroupValue) {
				$managerListNameGroupItem[] = $managerListNameGroupValue;
			}
		}

		$managerListNameFinalGroup = "'" . implode( "','",  $managerListNameGroupItem) . "'";

		$userQUERY = mysqli_query($catsConn, "SELECT
		    user.user_id AS uid,
		    concat(first_name,' ',last_name) AS manager_name
		FROM
		    user
		WHERE
		    concat(first_name,' ',last_name) IN (".$managerListNameFinalGroup.")
		AND
		    access_level!='0'
		GROUP BY manager_name
		ORDER BY manager_name ASC");
		while($userROW = mysqli_fetch_array($userQUERY)) {
			$optionList[] = "<option value='".ucwords($userROW['manager_name'])."'>".ucwords($userROW['manager_name'])."</option>";
		}
		$optionList[] = '<option value="Other">Other</option>';
		return $optionList;
	}

	function salesRolesDepartmentList($sales_connect) {
		$optionList = array();
		$optionList[] = '<option value="">Select Department</option>';
		$departmentQUERY = mysqli_query($sales_connect, "SELECT
		    id AS dept_id,
		    name AS dept_name
		FROM
		    x2_roles
		WHERE
		    id NOT IN(1,2)
		ORDER BY dept_name ASC");
		while($departmentROW = mysqli_fetch_array($departmentQUERY)){
			$optionList[] = "<option value='".ucwords($departmentROW['dept_name'])."'>".ucwords($departmentROW['dept_name'])."</option>";
		}
		$optionList[] = '<option value="Other">Other</option>';
		return $optionList;
	}

	function salesRolesManagerList($sales_connect) {
		$optionList = array();
		$optionList[] = '<option value="">Select Manager</option>';
		$managerQUERY = mysqli_query($sales_connect, "SELECT
			u.id AS manager_id,
		    CONCAT(u.firstName, ' ', u.lastName) AS manager_name
		FROM
		    x2_users AS u
		    JOIN x2_role_to_user AS user_roles ON user_roles.userId = u.id
		WHERE
			user_roles.roleId = '9'
		AND
			u.status = '1'
		GROUP BY manager_name
		ORDER BY manager_name ASC");
		while($managerROW = mysqli_fetch_array($managerQUERY)){
			$optionList[] = "<option value='".ucwords($managerROW['manager_name'])."'>".ucwords($managerROW['manager_name'])."</option>";
		}
		$optionList[] = '<option value="Other">Other</option>';
		return $optionList;
	}

	function salesPersonnelList($sales_connect) {
		$optionList = array();
		$insidePersonnelQUERY = mysqli_query($sales_connect, "SELECT
			concat(u.firstName,' ',u.lastName) AS personnelName,
			u.status AS personnelStatus
		FROM
			x2_users AS u
		    JOIN vtech_mappingdb.manage_sales_roles AS msr ON msr.user_id = u.id
		WHERE
			msr.department = 'Inside Sales'
		AND
			msr.manager_name = 'Mohsin Shaikh'");

		while($insidePersonnelROW = mysqli_fetch_array($insidePersonnelQUERY)){
			$optionList[] = "<option value='".ucwords($insidePersonnelROW['personnelName'])."'>".ucwords($insidePersonnelROW['personnelName'])."</option>";
		}

		$insidePersonnelQUERY2 = mysqli_query($sales_connect, "SELECT
			concat(u.firstName,' ',u.lastName) AS personnelName,
			u.status AS personnelStatus
		FROM
			x2_users AS u
		    JOIN vtech_mappingdb.manage_sales_roles AS msr ON msr.user_id = u.id
		WHERE
			msr.department = 'Inside Sales'
		AND
			msr.manager_name = 'Haresh Vataliya'");

		while($insidePersonnelROW2 = mysqli_fetch_array($insidePersonnelQUERY2)){
			$optionList[] = "<option value='".ucwords($insidePersonnelROW2['personnelName'])."'>".ucwords($insidePersonnelROW2['personnelName'])."</option>";
		}

		$onsitePersonnelQUERY = mysqli_query($sales_connect, "SELECT
			concat(u.firstName,' ',u.lastName) AS personnelName,
			u.status AS personnelStatus
		FROM
			x2_users AS u
		    JOIN vtech_mappingdb.manage_sales_roles AS msr ON msr.user_id = u.id
		WHERE
			msr.department IN ('OnSite Sales','OnSite Post Sales')");

		while($onsitePersonnelROW = mysqli_fetch_array($onsitePersonnelQUERY)){
			$optionList[] = "<option value='".ucwords($onsitePersonnelROW['personnelName'])."'>".ucwords($onsitePersonnelROW['personnelName'])."</option>";
		}
		
		return $optionList;
	}

	function bdgMatrixPersonnelList($sales_connect) {
		$optionList = array();
		$insidePersonnelQUERY = mysqli_query($sales_connect, "SELECT
			concat(u.firstName,' ',u.lastName) AS personnelName,
			u.status AS personnelStatus
		FROM
			x2_users AS u
		    JOIN vtech_mappingdb.manage_sales_roles AS msr ON msr.user_id = u.id
		WHERE
			msr.department = 'Inside Sales'
		AND
			msr.manager_name = 'Mohsin Shaikh'");

		while($insidePersonnelROW = mysqli_fetch_array($insidePersonnelQUERY)){
			$optionList[] = ucwords($insidePersonnelROW['personnelName']);
		}

		return $optionList;
	}

	function bdcMatrixPersonnelList($sales_connect) {
		$optionList = array();
		$insidePersonnelQUERY = mysqli_query($sales_connect, "SELECT
			concat(u.firstName,' ',u.lastName) AS personnelName,
			u.status AS personnelStatus
		FROM
			x2_users AS u
		    JOIN vtech_mappingdb.manage_sales_roles AS msr ON msr.user_id = u.id
		WHERE
			msr.department = 'Inside Sales'
		AND
			msr.manager_name = 'Haresh Vataliya'");

		while($insidePersonnelROW = mysqli_fetch_array($insidePersonnelQUERY)){
			$optionList[] = "<option value='".ucwords($insidePersonnelROW['personnelName'])."'>".ucwords($insidePersonnelROW['personnelName'])."</option>";
		}

		return $optionList;
	}

	function usbdMatrixPersonnelList($sales_connect) {
		$optionList = array();
		$onsitePersonnelQUERY = mysqli_query($sales_connect, "SELECT
			concat(u.firstName,' ',u.lastName) AS personnelName,
			u.status AS personnelStatus
		FROM
			x2_users AS u
		    JOIN vtech_mappingdb.manage_sales_roles AS msr ON msr.user_id = u.id
		WHERE
			msr.department IN ('OnSite Sales','OnSite Post Sales')");

		while($onsitePersonnelROW = mysqli_fetch_array($onsitePersonnelQUERY)){
			$optionList[] = "<option value='".ucwords($onsitePersonnelROW['personnelName'])."'>".ucwords($onsitePersonnelROW['personnelName'])."</option>";
		}
		
		return $optionList;
	}

	function salesGroupList($sales_connect) {
		$optionList = array();
		
		$salesGroupQUERY = mysqli_query($sales_connect, "SELECT
			id,
			name
		FROM
			x2_groups");
		
		while($salesGroupROW = mysqli_fetch_array($salesGroupQUERY)) {
			$optionList[] = array("id" => $salesGroupROW['id'],
			"name" => $salesGroupROW['name']);
		}
		
		return $optionList;
	}

	function salesGroupPersonnelList($sales_connect,$groupId) {
		$optionList = array();
		
		$salesGroupPersonnelQUERY = mysqli_query($sales_connect, "SELECT
			CONCAT(u.firstName,' ',u.lastName) AS personnel
		FROM
			x2_users AS u
		    JOIN x2_group_to_user AS gu ON u.id = gu.userId
		    JOIN x2_groups AS g ON gu.groupId = g.id
		WHERE
			g.id = '$groupId'
		GROUP BY personnel
		ORDER BY personnel ASC");
		
		while($salesGroupPersonnelROW = mysqli_fetch_array($salesGroupPersonnelQUERY)) {
			$optionList[] = $salesGroupPersonnelROW['personnel'];
		}
		
		return $optionList;
	}

	function findManagerIdFromName($catsConn,$personnelValueList) {
		$managerId = "";

		$query = mysqli_query($catsConn, "SELECT user_id FROM user WHERE CONCAT(first_name,' ',last_name) = '$personnelValueList'");

		$row = mysqli_fetch_array($query);

		$managerId = $row["user_id"];

		return $managerId;
	}

	function findRecruiterIdFromManager($catsConn,$personnelValueList) {
		$optionList = "";

		$query = mysqli_query($catsConn, "SELECT
			user_id
		FROM
			user
		WHERE
			notes = '$personnelValueList'");

		while ($row = mysqli_fetch_array($query)) {
			$optionList[] = $row["user_id"];
		}

		return $optionList;
	}

	function findClientListByPersonnelList($allConn,$personnelList,$listType,$startDate,$endDate) {
		$compareList = $sharedWithArray = $historyPersonnelList = array();

		if ($listType == "'Inside Sales'") {
			$listType = "'Inside Sales Person1','Inside Sales Person2','Research By'";
		}

		$logQUERY = mysqli_query($allConn, "SELECT
			cpl.company_id,
		    cpl.personnel,
		    cpl.personnel_type,
		    cpl.start_date,
		    cpl.end_date,
		    (SELECT COUNT(ef.value) FROM cats.extra_field AS ef WHERE ef.data_item_id = cpl.company_id AND ef.value != '' AND ef.field_name IN ($listType)) AS total_share,
		    (SELECT GROUP_CONCAT(ef.value) FROM cats.extra_field AS ef WHERE ef.data_item_id = cpl.company_id AND ef.value != '' AND ef.field_name IN ($listType)) AS shared_with
		FROM
			vtech_mappingdb.cats_personnel_log AS cpl
		WHERE
			cpl.personnel_type IN ($listType)
		AND
			cpl.personnel IN ($personnelList)
		AND
			cpl.start_date <= '$endDate'
		GROUP BY cpl.id");

		while ($logROW = mysqli_fetch_array($logQUERY)) {
			
			$isAllow = "true";
			
			if (strtotime($logROW["start_date"]) < strtotime($startDate)) {
				$fromDate = $startDate;
			} else {
				$fromDate = $logROW["start_date"];
			}

			if ($logROW["end_date"] == "") {
				$toDate = $endDate;
			} elseif (strtotime($logROW["end_date"]) > strtotime($endDate)) {
				$toDate = $endDate;
			} else {
				$toDate = $logROW["end_date"];
			}

			$compareList[] = array(
				"company_id" => $logROW["company_id"],
				"personnel" => $logROW["personnel"],
				"personnel_type" => $logROW["personnel_type"]
			);

			foreach ($compareList as $compareKey => $compareValue) {
				if ($compareValue["company_id"] == $logROW["company_id"] && $compareValue["personnel"] == $logROW["personnel"] && $compareValue["personnel_type"] != $logROW["personnel_type"]) {
					$isAllow = "false";
				}
			}

			$sharedWith = $logROW["shared_with"];
			$sharedWithArray = explode(",", $sharedWith);

			$sharedWithArray = array_diff($sharedWithArray, array($logROW["personnel"]));

			if (count($sharedWithArray) > 0) {
				$sharedWith = implode(",", $sharedWithArray);
			} else {
				$sharedWith = "---";
			}
			
			if ($isAllow == "true") {
				$historyPersonnelList[$logROW["personnel"]][] = array(
					"company_id" => $logROW["company_id"],
					"start_date" => $fromDate,
					"end_date" => $toDate,
					"total_share" => $logROW["total_share"],
					"shared_with" => $sharedWith
				);
			}
		}

		return $historyPersonnelList;
	}

	function findClientListByCSMList($allConn,$personnelList,$startDate,$endDate) {
		$compareList = $sharedWithArray = $historyPersonnelList = array();

		$logQUERY = mysqli_query($allConn, "SELECT
			cpl.company_id,
			CONCAT(u.first_name,' ',u.last_name) AS personnel,
		    cpl.personnel_type,
		    cpl.start_date,
		    cpl.end_date
		FROM
			vtech_mappingdb.cats_personnel_log AS cpl
			LEFT JOIN cats.user AS u ON u.user_id = cpl.personnel
		WHERE
			cpl.personnel_type = 'Owner'
		AND
			CONCAT(u.first_name,' ',u.last_name) IN ($personnelList)
		AND
			cpl.start_date <= '$endDate'
		GROUP BY cpl.id");

		while ($logROW = mysqli_fetch_array($logQUERY)) {
			
			$isAllow = "true";
			
			if (strtotime($logROW["start_date"]) < strtotime($startDate)) {
				$fromDate = $startDate;
			} else {
				$fromDate = $logROW["start_date"];
			}

			if ($logROW["end_date"] == "") {
				$toDate = $endDate;
			} elseif (strtotime($logROW["end_date"]) > strtotime($endDate)) {
				$toDate = $endDate;
			} else {
				$toDate = $logROW["end_date"];
			}

			$compareList[] = array(
				"company_id" => $logROW["company_id"],
				"personnel" => $logROW["personnel"],
				"personnel_type" => $logROW["personnel_type"]
			);

			foreach ($compareList as $compareKey => $compareValue) {
				if ($compareValue["company_id"] == $logROW["company_id"] && $compareValue["personnel"] == $logROW["personnel"] && $compareValue["personnel_type"] != $logROW["personnel_type"]) {
					$isAllow = "false";
				}
			}

			if ($isAllow == "true") {
				$historyPersonnelList[$logROW["personnel"]][] = array(
					"company_id" => $logROW["company_id"],
					"start_date" => $fromDate,
					"end_date" => $toDate
				);
			}
		}

		return $historyPersonnelList;
	}

	function monthDateRange($item) {
		$dateRange = array();

		$dateRange["filter_type"] = "month";

		foreach ($item as $itemKey => $itemValue) {
			$explodedMonth = explode("/", $itemValue);
			$modifiedDate = $explodedMonth[1]."-".$explodedMonth[0];

			$dateRange[] = array(
				"start_date" => date("Y-m-01", strtotime($modifiedDate)),
				"end_date" => date("Y-m-t", strtotime($modifiedDate)),
				"filter_value" => $itemValue
			);
		}

		return $dateRange;
	}

	function quarterDateRange($item) {
		$dateRange = array();

		$dateRange["filter_type"] = "quarter";

		foreach ($item as $itemKey => $itemValue) {
			$explodedQuarter = explode("/", $itemValue);
			$quarterTitle = $explodedQuarter[0];
			$quarterYear = $explodedQuarter[1];
			
			if ($quarterTitle == "Q1") {
				$dateRange[] = array(
					"start_date" => $quarterYear."-01-01",
					"end_date" => $quarterYear."-03-31",
					"filter_value" => $itemValue
				);
			} elseif ($quarterTitle == "Q2") {
				$dateRange[] = array(
					"start_date" => $quarterYear."-04-01",
					"end_date" => $quarterYear."-06-30",
					"filter_value" => $itemValue
				);
			} elseif ($quarterTitle == "Q3") {
				$dateRange[] = array(
					"start_date" => $quarterYear."-07-01",
					"end_date" => $quarterYear."-09-30",
					"filter_value" => $itemValue
				);
			} elseif ($quarterTitle == "Q4") {
				$dateRange[] = array(
					"start_date" => $quarterYear."-10-01",
					"end_date" => $quarterYear."-12-31",
					"filter_value" => $itemValue
				);
			}
		}

		return $dateRange;
	}

	function normalDateRange($startDate,$endDate) {
		$dateRange = array();

		$dateRange["filter_type"] = "daterange";

		$dateRange[] = array(
			"start_date" => date("Y-m-d", strtotime($startDate)),
			"end_date" => date("Y-m-d", strtotime($endDate)),
			"filter_value" => "none"
		);

		return $dateRange;
	}

    function geRangeMatrixDeatil($manageMatrixColumnList, $marginValue, $resourceValue) {
    	$returnValue = '';
    	foreach ($manageMatrixColumnList as $key => $value) {
    		if ($value['margin_min'] == $marginValue['margin_min'] && $value['margin_max'] == $marginValue['margin_max'] && $value['resource_from'] == $resourceValue['resource_from'] && $value['resource_to'] == $resourceValue['resource_to']) {
    			$returnValue = $value;
    		}
    	}
    	return $returnValue;
    }

    function getSowData($allConn,$year) {
		$sowData = array();

		$getSowCostQuery = mysqli_query($allConn, "SELECT
				vmu.userfullname,
				vep.sow_product_cost
			FROM
				vtechhrm_in.main_empperformance AS vep
				LEFT JOIN vtechhrm_in.main_users AS vmu ON vmu.id = vep.user_id
			WHERE
				vep.year = '$year'
			GROUP BY vep.user_id
			ORDER BY userfullname ASC");

			if (mysqli_num_rows($getSowCostQuery) > 0) {
				while ($sowRow = mysqli_fetch_array($getSowCostQuery)) {
					$sowData[strtolower(trim($sowRow['userfullname']))] = $sowRow['sow_product_cost'];
				}
			}

		return $sowData;
	}

	function timesheetWeeksList() {    
        $dateList = array();

        $startDate = strtotime(date("Y-m-01", strtotime("-60 months")));
        $endDate = strtotime(date("Y-m-d", strtotime("today")));

        while (date("l", $startDate) != "Sunday") {
            $startDate = strtotime('+1 day', $startDate);
        }

        for ($startDate; $startDate <= $endDate; $startDate = strtotime('+7 day', $startDate)) {
            $lastDate = strtotime('+6 day', $startDate);

            $isSlected = "No";

            if (strtotime(date("Y-m-d")) >= $startDate && strtotime(date("Y-m-d")) <= $lastDate) {
                $isSlected = "Yes";
            }

            $dateList[] = array(
                "is_selected" => $isSlected,
                "date_range" => date("m/d/Y", $startDate)." to ".date("m/d/Y", $lastDate),
                "selected_range" => array(
                    "start_date" => date("Y-m-d", $startDate),
                    "end_date" => date("Y-m-d", $lastDate)
                )
            );
        }

        unset($startDate, $endDate, $isSlected);

        return array_reverse($dateList);
    }

    function hrmIndiaDepartmentRateinfo($allConn) {
    	$output = array();

    	$query = mysqli_query($allConn, "SELECT
    		mdr.id,
    		mdr.department,
    		mdr.department_group,
    		mdr.rate
    	FROM
    		vtechhrm_in.main_departmentrate AS mdr
    	WHERE
    		mdr.isactive = '1'
    	GROUP BY mdr.id");

    	if (mysqli_num_rows($query) > 0) {
    		while ($row = mysqli_fetch_array($query)) {
    			$output[] = array(
    				"department_name" => $row["department"]." Team",
					"department_detail" => array(
						"department_id" => implode(",", explode(",", $row["department_group"])),
    					"department_name" => $row["department"]." Team",
						"department_rate" => $row["rate"]
					)
    			);
    		}
    	}

    	unset($query, $row);

    	return $output;
    }

    function catsUserListForTwilioCall($catsConn,$managerName) {
		$optionList = array();
		
		$recruiterQUERY = "SELECT
			u.phone_work,
			CONCAT(u.first_name,' ',u.last_name) AS recruiter
		FROM
			user AS u
		WHERE u.phone_work != ''
		AND u.access_level != 0";
		
		if ($managerName != "") {
			$recruiterQUERY .= " AND
				u.notes = '$managerName'
				GROUP BY user_id
				ORDER BY recruiter ASC";
		} else {
			$recruiterQUERY .= "
				GROUP BY user_id
				ORDER BY recruiter ASC";
		}

		$recruiterRESULT = mysqli_query($catsConn, $recruiterQUERY);

		
		while($recruiterROW = mysqli_fetch_array($recruiterRESULT)) {
			$phoneWork = $recruiterROW['phone_work'];
			$phones = preg_replace('/\D+/', '', $phoneWork);
 			$phone = '+1'.$phones;
			$optionList[] = array("phone_work" => $phone,
			"name" => $recruiterROW['recruiter']);
		}

		return $optionList;
	}
	
?>