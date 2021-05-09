CREATE VIEW cats_personnel_change_detail AS SELECT
    cpd.*,
    IF((SELECT COUNT(id) FROM vtech_mappingdb.cats_personnel_log WHERE company_id = cpd.company_id GROUP BY company_id) > 0, 'No', 'Yes') AS is_new_entry,
    IF((SELECT COUNT(id) FROM vtech_mappingdb.cats_personnel_log WHERE company_id = cpd.company_id AND personnel_type = cpd.personnel_type GROUP BY company_id) > 0, 'No', 'Yes') AS is_new_personnel_type,
    (SELECT MAX(id) FROM vtech_mappingdb.cats_personnel_log WHERE company_id = cpd.company_id AND personnel_type = cpd.personnel_type GROUP BY company_id) AS old_personnel_id
FROM
    vtech_mappingdb.cats_personnel_detail AS cpd
WHERE
    NOT EXISTS(SELECT
        *
    FROM
        vtech_mappingdb.cats_personnel_log AS cpl
    WHERE
        cpl.company_id = IF(cpd.company_id IS NULL, '', cpd.company_id)
    AND
        cpl.personnel = IF(cpd.personnel IS NULL, '', cpd.personnel)
    AND
        cpl.personnel_type = IF(cpd.personnel_type IS NULL, '', cpd.personnel_type)
    AND
        cpl.id IN (SELECT max(id) FROM vtech_mappingdb.cats_personnel_log WHERE company_id = cpl.company_id AND personnel_type = cpl.personnel_type)
    GROUP BY cpl.personnel_type,cpl.company_id)
GROUP BY cpd.personnel_type,cpd.company_id