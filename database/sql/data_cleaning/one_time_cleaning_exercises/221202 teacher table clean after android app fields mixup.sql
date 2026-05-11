update teacher t
set t.employment_role_oid = null,
    t.updated_at = CURRENT_TIMESTAMP,
    t.updated_by = 0
-- select * from teacher t
where t.employment_role_oid regexp '[0-9]{6}'
;

-- select * from teacher t
update teacher t
inner join teacher_payroll tp on t.pin = tp.pin
set t.employment_status_oid = 'payroll',
    t.nassit_number = tp.nassit_number,
    t.updated_at = CURRENT_TIMESTAMP,
    t.updated_by = 0
where t.employment_status_oid is null
  and char_length(t.pin) > 0
;
