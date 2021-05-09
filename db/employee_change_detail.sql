CREATE VIEW employee_change_detail AS SELECT
    ecd.*
FROM
    vtech_mappingdb.employee_current_detail AS ecd
WHERE
    NOT EXISTS(SELECT
        *
    FROM
        vtech_mappingdb.employee_history_detail AS ehd
    WHERE
        ehd.employee_id = IF(ecd.employee_id IS NULL, '', ecd.employee_id)
    AND
        ehd.benefit = IF(ecd.benefit IS NULL, '', ecd.benefit)
    AND
        ehd.benefit_list = IF(ecd.benefit_list IS NULL, '', ecd.benefit_list)
    AND
        ehd.employment_id = IF(ecd.employment_id IS NULL, '', ecd.employment_id)
    AND
        CAST(ehd.bill_rate AS DECIMAL (10,2)) = IF(CAST(ecd.bill_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.bill_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.pay_rate AS DECIMAL (10,2)) = IF(CAST(ecd.pay_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.pay_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.ot_rate AS DECIMAL (10,2)) = IF(CAST(ecd.ot_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.ot_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.K401_rate AS DECIMAL (10,2)) = IF(CAST(ecd.K401_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.K401_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.H1B_rate AS DECIMAL (10,2)) = IF(CAST(ecd.H1B_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.H1B_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.health_insurance_rate AS DECIMAL (10,2)) = IF(CAST(ecd.health_insurance_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.health_insurance_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.ten_paid_holidays_rate AS DECIMAL (10,2)) = IF(CAST(ecd.ten_paid_holidays_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.ten_paid_holidays_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.ten_paid_leave_rate AS DECIMAL (10,2)) = IF(CAST(ecd.ten_paid_leave_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.ten_paid_leave_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.with_benefit_tax_rate AS DECIMAL (10,2)) = IF(CAST(ecd.with_benefit_tax_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.with_benefit_tax_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.without_benefit_tax_rate AS DECIMAL (10,2)) = IF(CAST(ecd.without_benefit_tax_rate AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.without_benefit_tax_rate AS DECIMAL (10,2)))
    AND
        CAST(ehd.client_msp_charge_percentage AS DECIMAL (10,2)) = IF(CAST(ecd.client_msp_charge_percentage AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.client_msp_charge_percentage AS DECIMAL (10,2)))
    AND
        CAST(ehd.client_msp_charge_dollar AS DECIMAL (10,2)) = IF(CAST(ecd.client_msp_charge_dollar AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.client_msp_charge_dollar AS DECIMAL (10,2)))
    AND
        CAST(ehd.client_prime_charge_percentage AS DECIMAL (10,2)) = IF(CAST(ecd.client_prime_charge_percentage AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.client_prime_charge_percentage AS DECIMAL (10,2)))
    AND
        CAST(ehd.client_prime_charge_dollar AS DECIMAL (10,2)) = IF(CAST(ecd.client_prime_charge_dollar AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.client_prime_charge_dollar AS DECIMAL (10,2)))
    AND
        CAST(ehd.employee_prime_charge_percentage AS DECIMAL (10,2)) = IF(CAST(ecd.employee_prime_charge_percentage AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.employee_prime_charge_percentage AS DECIMAL (10,2)))
    AND
        CAST(ehd.employee_prime_charge_dollar AS DECIMAL (10,2)) = IF(CAST(ecd.employee_prime_charge_dollar AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.employee_prime_charge_dollar AS DECIMAL (10,2)))
    AND
        CAST(ehd.employee_any_charge_dollar AS DECIMAL (10,2)) = IF(CAST(ecd.employee_any_charge_dollar AS DECIMAL (10,2)) IS NULL, '', CAST(ecd.employee_any_charge_dollar AS DECIMAL (10,2)))
    AND
        ehd.ea_person = IF(ecd.ea_person IS NULL, '', ecd.ea_person)
    AND
        ehd.id IN (SELECT max(id) FROM vtech_mappingdb.employee_history_detail WHERE employee_id = ehd.employee_id)
    GROUP BY ehd.employee_id)
GROUP BY ecd.employee_id