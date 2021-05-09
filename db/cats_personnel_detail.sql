CREATE VIEW cats_personnel_detail AS SELECT
	comp.company_id,
    ef.value AS personnel,
    ef.field_name AS personnel_type,
	DATE_FORMAT(comp.date_created, '%Y-%m-%d') AS company_create_date
FROM
	cats.company AS comp
    JOIN cats.extra_field AS ef ON comp.company_id = ef.data_item_id
WHERE
	ef.field_name IN ('Inside Sales Person1','Inside Sales Person2','Research By','Inside Post Sales','OnSite Sales Person','OnSite Post Sales')
AND
	ef.value != ''
GROUP BY ef.field_name,comp.company_id