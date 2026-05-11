INSERT INTO school_academic_year (
    uuid,
    academic_year_name,
    academic_year,
    date_from,
    date_to,
    active,
    created_at,
    updated_at,
    synced_at
) VALUES (
    UUID(),
    '2023-2024',
    2023,
    '2023-09-04',
    '0000-00-00',
    1,
    NOW(),
    NOW(),
    NOW()
)
;

-- fill end date of previous year
UPDATE school_academic_year
SET date_to = '2023-09-03',
    updated_at = NOW(),
    synced_at = NOW()
WHERE academic_year = 2022
;
