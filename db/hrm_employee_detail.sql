CREATE VIEW hrm_employee_detail AS SELECT
	e.id AS employee_id,

	e.employee_id AS emp_id,

	CONCAT(e.first_name,' ',e.last_name) AS employee_name,

	e.status AS employee_status,
	
	DATE_FORMAT(e.custom7, '%Y-%m-%d') AS join_date,

	DATE_FORMAT(e.termination_date, '%Y-%m-%d') AS termination_date,

	e.custom1 AS benefit,
	
	e.custom2 AS benefit_list,
	
	es.id AS employment_id,

	es.name AS employment_type,

	CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS bill_rate,
	
	CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS pay_rate,

	CAST(replace(e.custom5,'$','') AS DECIMAL (10,2)) AS ot_rate,

	job.joborder_id AS job_id,

	job.title AS job_title,

	job.city AS job_city,

	job.state AS job_state,
	
	u_rec.user_id AS recruiter_id,
	
	concat(u_rec.first_name,' ',u_rec.last_name) AS recruiter_name,

	u_rec.notes AS recruiter_manager,

	comp.company_id,

	comp.name AS company_name,

	u_comp.user_id AS company_manager_id,

	concat(u_comp.first_name,' ',u_comp.last_name) AS company_manager_name,

	IF(e.custom1 = 'With Benefits', IF(LOCATE('401K', e.custom2) != 0, ts_401K.charge_pct, '0'), '0') AS K401_rate,
	
	IF(e.custom1 = 'With Benefits', IF(LOCATE('H1B', e.custom2) != 0, ts_H1B.charge_pct, '0'), '0') AS H1B_rate,
	
	IF(e.custom1 = 'With Benefits', IF(LOCATE('Health Insurance', e.custom2) != 0, ts_health_insurance_rate.charge_pct, '0'), '0') AS health_insurance_rate,
	
	IF(e.custom1 = 'With Benefits', IF(LOCATE('Ten Paid Holidays', e.custom2) != 0, ts_ten_paid_holidays_rate.charge_pct, '0'), '0') AS ten_paid_holidays_rate,
	
	IF(e.custom1 = 'With Benefits', IF(LOCATE('Ten Paid Leave', e.custom2) != 0, ts_ten_paid_leave_rate.charge_pct, '0'), '0') AS ten_paid_leave_rate,
	
	IF(e.custom1 = 'With Benefits', ts_with_benefit_tax_rate.charge_pct, '0') AS with_benefit_tax_rate,
	
	IF((e.custom1 = 'Without Benefits' OR e.custom1 = '' OR e.custom1 = 'Not Applicable'), ts_without_benefit_tax_rate.charge_pct, '0') AS without_benefit_tax_rate,

	clf.mspChrg_pct AS client_msp_charge_percentage,

	clf.mspChrg_dlr AS client_msp_charge_dollar,
	
	clf.primechrg_pct AS client_prime_charge_percentage,
	
	clf.primeChrg_dlr AS client_prime_charge_dollar,
	
	cnf.c_primeCharge_pct AS employee_prime_charge_percentage,
	
	cnf.c_primeCharge_dlr AS employee_prime_charge_dollar,
	
	cnf.c_anyCharge_dlr AS employee_any_charge_dollar
FROM
	vtechhrm.employees AS e
	
	LEFT JOIN vtechhrm.employeeprojects AS ep ON e.id = ep.employee
	
	LEFT JOIN vtechhrm.employmentstatus AS es ON e.employment_status = es.id
    
    LEFT JOIN vtech_mappingdb.system_integration AS si ON e.id = si.h_employee_id
	
	LEFT JOIN cats.company AS comp ON si.c_company_id = comp.company_id
	
	LEFT JOIN cats.joborder AS job ON si.c_joborder_id = job.joborder_id

	LEFT JOIN cats.user AS u_rec ON si.c_recruiter_id = u_rec.user_id

	LEFT JOIN cats.user AS u_comp ON comp.owner = u_comp.user_id
	
	LEFT JOIN vtech_mappingdb.client_fees AS clf ON comp.company_id = clf.client_id
	
	LEFT JOIN vtech_mappingdb.candidate_fees AS cnf ON e.id = cnf.emp_id
	
	LEFT JOIN vtech_mappingdb.tax_settings AS ts_401K ON es.id = ts_401K.empst_id AND ts_401K.benefits = '401K'
	
	LEFT JOIN vtech_mappingdb.tax_settings AS ts_H1B ON es.id = ts_H1B.empst_id AND ts_H1B.benefits = 'H1B'
	
	LEFT JOIN vtech_mappingdb.tax_settings AS ts_health_insurance_rate ON es.id = ts_health_insurance_rate.empst_id AND ts_health_insurance_rate.benefits = 'Health Insurance'
	
	LEFT JOIN vtech_mappingdb.tax_settings AS ts_ten_paid_holidays_rate ON es.id = ts_ten_paid_holidays_rate.empst_id AND ts_ten_paid_holidays_rate.benefits = 'Ten Paid Holidays'

	LEFT JOIN vtech_mappingdb.tax_settings AS ts_ten_paid_leave_rate ON es.id = ts_ten_paid_leave_rate.empst_id AND ts_ten_paid_leave_rate.benefits = 'Ten Paid Leave'
	
	LEFT JOIN vtech_mappingdb.tax_settings AS ts_with_benefit_tax_rate ON es.id = ts_with_benefit_tax_rate.empst_id AND ts_with_benefit_tax_rate.benefits = 'Tax'
	
	LEFT JOIN vtech_mappingdb.tax_settings AS ts_without_benefit_tax_rate ON es.id = ts_without_benefit_tax_rate.empst_id AND ts_without_benefit_tax_rate.benefits = 'Without Benefits'
WHERE
	ep.project != '6'
GROUP BY employee_id