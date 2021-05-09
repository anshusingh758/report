<?php
	error_reporting(0);
	include_once("../config.php");

	if ($_POST) {
		$output = "";

		if ($_POST["mark_type"] == "minus") {
			$quickbookTimesheetId = $_POST["quickbook_timesheet_id"];

			if (mysqli_query($allConn, "DELETE FROM quickbook.quickbook_timesheet WHERE id = '$quickbookTimesheetId'")) {
				$output = "success";
			} else {
				$output = "error";
			}
		} else {
			$hrmEmployeeId = $_POST["hrm_employee_id"];
			$hrmEmployeeName = mysqli_real_escape_string($allConn, $_POST["hrm_employee_name"]);
			$hrmEmploymentStatus = $_POST["hrm_employment_status"];
			$hrmBillRate = $_POST["hrm_billrate"];
			$hrmOvertimeBillRate = $_POST["hrm_overtime_billrate"];
			$qbConsultantId = $_POST["qb_consultant_id"];
			$qbConsultantName = mysqli_real_escape_string($allConn, $_POST["qb_consultant_name"]);
			$qbConsultantType = $_POST["qb_consultant_type"];
			$qbServiceId = $_POST["qb_service_id"];
			$qbServiceName = mysqli_real_escape_string($allConn, $_POST["qb_service_name"]);
			$qbCustomerId = $_POST["qb_customer_id"];
			$qbCustomerName = mysqli_real_escape_string($allConn, $_POST["qb_customer_name"]);
			$qbFilterId = $_POST["qb_filter_id"];
			$qbFilterName = $_POST["qb_filter_name"];
			$qbSyncId = $_POST["qb_sync_id"];
			$qbSyncName = $_POST["qb_sync_name"];
			$dateStart = $_POST["date_start"];
			$dateEnd = $_POST["date_end"];
			$creationType = $_POST["creation_type"];

			if (mysqli_query($allConn, "INSERT INTO quickbook.quickbook_timesheet(hrm_employee_id, hrm_employee_name, hrm_employment_status, hrm_billrate, hrm_overtime_billrate, qb_consultant_id, qb_consultant_name, qb_consultant_type, qb_service_id, qb_service_name, qb_customer_id, qb_customer_name, qb_filter_id, qb_filter_name, qb_sync_id, qb_sync_name, date_start, date_end, creation_type) VALUES('$hrmEmployeeId', '$hrmEmployeeName', '$hrmEmploymentStatus', '$hrmBillRate', '$hrmOvertimeBillRate', '$qbConsultantId', '$qbConsultantName', '$qbConsultantType', '$qbServiceId', '$qbServiceName', '$qbCustomerId', '$qbCustomerName', '$qbFilterId', '$qbFilterName', '$qbSyncId', '$qbSyncName', '$dateStart', '$dateEnd', '$creationType')")) {
				$output = "success";
			} else {
				$output = "error";
			}
		}

		echo $output;
	}
?>