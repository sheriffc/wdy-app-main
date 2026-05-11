UPDATE teacher
SET end_reason_oid = NULL,
    updated_at = CURRENT_TIMESTAMP,
    updated_by = 0
WHERE end_reason_oid IN ('male','female')
;
