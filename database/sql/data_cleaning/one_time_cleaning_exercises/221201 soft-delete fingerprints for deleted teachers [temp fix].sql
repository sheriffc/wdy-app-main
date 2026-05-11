-- select * from
update
    person_fingerprint pf
    inner join person p on pf.person_uuid = p.uuid
    inner join teacher t on p.uuid = t.person_uuid
    set pf.deleted_at = CURRENT_TIMESTAMP,
        pf.deleted_by = 0,
        pf.updated_at = CURRENT_TIMESTAMP,
        pf.updated_by = 0
where (t.deleted_at is not null and t.deleted_at <> '')
  and (pf.deleted_at is null or pf.deleted_at = '')
;
