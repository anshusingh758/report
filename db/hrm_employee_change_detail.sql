CREATE VIEW hrm_employee_change_detail AS SELECT
    hed.*
FROM
    vtech_mappingdb.hrm_employee_detail AS hed
WHERE
    NOT EXISTS(SELECT
        *
    FROM
        vtech_mappingdb.hrm_employee_log AS hel
    WHERE
        hel.employee_id = IF(hed.employee_id IS NULL, '', hed.employee_id)
    AND
        hel.employee_status = IF(hed.employee_status IS NULL, '', hed.employee_status)
    AND
        hel.join_date = IF(hed.join_date IS NULL, '', hed.join_date)
    AND
        hel.termination_date = IF(hed.termination_date IS NULL, '', hed.termination_date)
    AND
        hel.benefit = IF(hed.benefit IS NULL, '', hed.benefit)
    AND
        hel.benefit_list = IF(hed.benefit_list IS NULL, '', hed.benefit_list)
    AND
        hel.employment_id = IF(hed.employment_id IS NULL, '', hed.employment_id)
    AND
        CAST(hel.bill_rate AS DECIMAL (10,2)) = IF(CAST(hed.bill_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.bill_rate AS DECIMAL (10,2)))
    AND
        CAST(hel.pay_rate AS DECIMAL (10,2)) = IF(CAST(hed.pay_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.pay_rate AS DECIMAL (10,2)))
    AND
        CAST(hel.ot_rate AS DECIMAL (10,2)) = IF(CAST(hed.ot_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.ot_rate AS DECIMAL (10,2)))
    AND
        hel.job_id = IF(hed.job_id IS NULL, '', hed.job_id)
    AND
        hel.recruiter_id = IF(hed.recruiter_id IS NULL, '', hed.recruiter_id)
    AND
        hel.company_id = IF(hed.company_id IS NULL, '', hed.company_id)
    AND
        CAST(hel.K401_rate AS DECIMAL (10,2)) = IF(CAST(hed.K401_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.K401_rate AS DECIMAL (10,2)))
    AND
        CAST(hel.H1B_rate AS DECIMAL (10,2)) = IF(CAST(hed.H1B_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.H1B_rate AS DECIMAL (10,2)))
    AND
        CAST(hel.health_insurance_rate AS DECIMAL (10,2)) = IF(CAST(hed.health_insurance_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.health_insurance_rate AS DECIMAL (10,2)))
    AND
        CAST(hel.ten_paid_holidays_rate AS DECIMAL (10,2)) = IF(CAST(hed.ten_paid_holidays_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.ten_paid_holidays_rate AS DECIMAL (10,2)))
    AND
        CAST(hel.ten_paid_leave_rate AS DECIMAL (10,2)) = IF(CAST(hed.ten_paid_leave_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.ten_paid_leave_rate AS DECIMAL (10,2)))
    AND
        CAST(hel.with_benefit_tax_rate AS DECIMAL (10,2)) = IF(CAST(hed.with_benefit_tax_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.with_benefit_tax_rate AS DECIMAL (10,2)))
    AND
        CAST(hel.without_benefit_tax_rate AS DECIMAL (10,2)) = IF(CAST(hed.without_benefit_tax_rate AS DECIMAL (10,2)) IS NULL, '', CAST(hed.without_benefit_tax_rate AS DECIMAL (10,2)))
    AND
        CAST(hel.client_msp_charge_percentage AS DECIMAL (10,2)) = IF(CAST(hed.client_msp_charge_percentage AS DECIMAL (10,2)) IS NULL, '', CAST(hed.client_msp_charge_percentage AS DECIMAL (10,2)))
    AND
        CAST(hel.client_msp_charge_dollar AS DECIMAL (10,2)) = IF(CAST(hed.client_msp_charge_dollar AS DECIMAL (10,2)) IS NULL, '', CAST(hed.client_msp_charge_dollar AS DECIMAL (10,2)))
    AND
        CAST(hel.client_prime_charge_percentage AS DECIMAL (10,2)) = IF(CAST(hed.client_prime_charge_percentage AS DECIMAL (10,2)) IS NULL, '', CAST(hed.client_prime_charge_percentage AS DECIMAL (10,2)))
    AND
        CAST(hel.client_prime_charge_dollar AS DECIMAL (10,2)) = IF(CAST(hed.client_prime_charge_dollar AS DECIMAL (10,2)) IS NULL, '', CAST(hed.client_prime_charge_dollar AS DECIMAL (10,2)))
    AND
        CAST(hel.employee_prime_charge_percentage AS DECIMAL (10,2)) = IF(CAST(hed.employee_prime_charge_percentage AS DECIMAL (10,2)) IS NULL, '', CAST(hed.employee_prime_charge_percentage AS DECIMAL (10,2)))
    AND
        CAST(hel.employee_prime_charge_dollar AS DECIMAL (10,2)) = IF(CAST(hed.employee_prime_charge_dollar AS DECIMAL (10,2)) IS NULL, '', CAST(hed.employee_prime_charge_dollar AS DECIMAL (10,2)))
    AND
        CAST(hel.employee_any_charge_dollar AS DECIMAL (10,2)) = IF(CAST(hed.employee_any_charge_dollar AS DECIMAL (10,2)) IS NULL, '', CAST(hed.employee_any_charge_dollar AS DECIMAL (10,2)))
    AND
        hel.id IN (SELECT max(id) FROM vtech_mappingdb.hrm_employee_log WHERE employee_id = hel.employee_id)
    GROUP BY hed.employee_id)
GROUP BY employee_id