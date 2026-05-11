-- -- SQL WDY Pre-Pilot Seeding -- --

-- Queries marked as "SEED QUERY" are the ones that are actually used.
-- The rest are just for checking the inputs and outputs, inc. flagging dodgy data values in preexisting data
-- These scripts now also handle further additive additions of extra new schools (with their associated teachers/persons),
--     without disturbing the initial seed.
-- To do this, put the new EMIS codes in a standalone table, and put this table name into the relevant line
--     in each script (flagged by a comment in each)



-- ** PAYROLL ** --

-- check empty school assignments in payroll
select * from tsctrm.payroll
where yearmonth = (SELECT max(yearmonth) FROM tsctrm.cache_yearmonth)
  and (rc is null or rc = '' or rc = 0 or rc < 100000)
;

-- check missing names in payroll
select *
from tsctrm.payroll p
where p.yearmonth = (SELECT max(yearmonth) FROM tsctrm.cache_yearmonth)
  and (p.last_name = '' or p.last_name is null or p.first_name = '' or p.first_name is null)
;

-- SEED QUERY: pull FULL PAYROLL LIST for seeding
-- N.B. uses TRM database so must have access to this
INSERT INTO teacher_payroll (
    uuid,
    yearmonth,
    school_sid,
    school_emis_id,
    first_name,
    middle_name,
    last_name,
    sex,
    date_of_birth,
    pin,
    nin,
    nassit_number,
    created_at,
    updated_at,
    deleted_at  -- necessary to un-delete anyone who has reappeared on payroll
) SELECT * FROM (
    SELECT
        UUID() AS uuid,
        p.yearmonth,
        p.rc AS school_sid,
        NULL AS school_emis_id,
        upper(p.`first_name`) AS first_name,
        NULL AS middle_name, -- placeholder
        upper(p.`last_name`) AS last_name,
        lower(p.gender) AS sex,
        p.date_of_birth,
        p.emp_id AS pin,
        p.nin,
        p.nassit_no AS nassit_number,
        CURRENT_TIMESTAMP AS created_at,
        CURRENT_TIMESTAMP AS updated_at,
        NULL AS deleted_at
    FROM tsctrm.payroll p
    WHERE p.yearmonth = (SELECT max(yearmonth) FROM tsctrm.cache_yearmonth)
) AS new
    ON DUPLICATE KEY UPDATE
        yearmonth = new.yearmonth,
        school_sid = new.school_sid,
        school_emis_id = new.school_emis_id,
        first_name = new.first_name,
        middle_name = new.middle_name,
        last_name = new.last_name,
        sex = new.sex,
        date_of_birth = new.date_of_birth,
        pin = new.pin,
        nin = new.nin,
        nassit_number = new.nassit_number,
        deleted_at = new.deleted_at
;

-- SEED QUERY: soft delete any payroll teachers who have disappeared from payroll
-- N.B. uses TRM database so must have access to this
UPDATE teacher_payroll wdyp
    LEFT JOIN tsctrm.payroll p ON wdyp.pin = p.emp_id
    AND p.yearmonth = (SELECT max(yearmonth) FROM tsctrm.cache_yearmonth)
    SET deleted_at = CURRENT_TIMESTAMP
WHERE p.emp_id IS NULL
;




-- ** SCHOOLS ** --

-- check all school rollout emiscodes match to the ASC
select c.idemis_code as rollout_emiscode, s.emiscode as asc_emiscode, s.sid as asc_sid, s.*, c.*
from cga_300sch_list_230319 c
    left join asc21_schools_schooldata s on s.emiscode = c.idemis_code
order by s.emiscode
;

-- check dupe emiscodes in WDY school rollout list
select idemis_code, count(idemis_code) as count
from cga_300sch_list_230319
group by idemis_code
order by count desc
;

-- check dupe emiscodes in ASC, and see if any are included in our rollout
select * from
    (select emiscode, count(emiscode) as count
     from asc21_schools_schooldata
     group by emiscode
     having count > 1) as dupes
left join cga_300sch_list_230319 as cga_list on dupes.emiscode = cga_list.idemis_code
;

-- check how many dupe SIDs in the rollout schools
select sch_sid, count(sch_sid) as count
from cga_300sch_list_230319
group by sch_sid
order by count desc
;

-- check school name duplication: check how bad the situation is
select idschool_name, iddistrict, idchiefdom, c.* from cga_300sch_list_230319 c
order by idschool_name
;

-- check 999999 SIDs
select sch_sid, c.* from cga_60sch_list c
where sch_sid <> 999999
;

-- SEED QUERY: pull SCHOOL data for seeding
-- insert into z_school_test ( -- FOR TESTING
insert into school (
    uuid,
    emis_id,
    payroll_sid,
    wideya_id,
    waec_id,
    fabinc_recordid,
    `name`,
    school_education_level_oid,
    district_office_uuid,
    district_id,
    chiefdom_id,
    section_name,
    town_name,
    address,
    lat,
    lng,
    media_photo_uuid,
    rollout_batch,
    active,
    created_at,
    created_by,
    updated_at,
    updated_by,
    deleted_at,
    deleted_by
)
-- ; -- FOR TESTING
select
    UUID() as uuid,
    s.emiscode as emis_id,
    -- do a 'merge' of sorts between SID held by raw ASC (priority), and if not found take value from Fab Inc
    if ((c.sch_sid <> 999999
            and c.sch_sid <> 666666
            and c.sch_sid is not null
            and c.sch_sid <> ''),
        c.sch_sid,
        if ((s.sid <> 999999
            and s.sid <> 666666
            and s.sid is not null
            and s.sid <> ''),
            s.sid,
            null)
        ) as payroll_sid,
    null as wideya_id,
    if ((s.waec <> 999999 and s.waec <> ''), s.waec, null) as waec_id,
    s.recordid as fabinc_recordid,
    upper(concat(
        s.schoolname, ' (',
        s.districtname, ', ',
        s.chiefdomname, ', ',
        s.town, ')'
        )) as `name`,
    ol_sl.item_id as school_education_level_oid,
    dof.uuid as district_office_uuid,
    g_dis.id as district_id,
    g_chief.id as chiefdom_id,
    s.section as section_name,
    s.town as town_name,
    null as address,
    s.latitude as lat,
    s.longitude as lng,
    null as media_photo_uuid,
    2 as rollout_batch, -- MUST SET THIS PER BATCH, e.g. 2
    1 as active,
    CURRENT_TIMESTAMP as created_at,
    -1 as created_by,
    CURRENT_TIMESTAMP as updated_at,
    -1 as updated_by,
    null as deleted_at,
    null as deleted_by
from asc21_schools_schooldata s
         inner join cga_300sch_list_230319 as c on s.emiscode = c.idemis_code -- replace with whatever list of schools you want to additively seed from
         left join geo g_chief on s.location_chiefdom_recordid = g_chief.fabinc_recordid and g_chief.type = 3
         left join geo g_dis on g_chief.parent_id = g_dis.id
         left join district_office dof on dof.district_id = g_dis.id and g_dis.type = 2
         left join option_list ol_sl on s.schoollevel_recordid = ol_sl.item_asc_fabinc_recordid and ol_sl.list_name = 'school_education_level'
order by s.emiscode
;


-- check how many dupe SIDs in the rollout schools
select payroll_sid, count(payroll_sid) as count
from school
group by payroll_sid
order by count desc
;




-- ** TEACHERS & TEACHER-PERSONS ** --

-- SEED QUERY: pull TEACHERS + PERSONS (FROM PAYROLL) for seeding

-- These are sister queries, filling both the teacher table and person table with corresponding records.
-- MUST BE DONE AFTER SCHOOL TABLE ALREADY SEEDED, AND PAYROLL TABLE TOO.
-- Bit awkward due to person.uuid needing to be generated randomly but inserted into both tables.
-- Do teacher table first and generate both UUIDs in it, then do person table after,
--     during which you need to pull the person_uuid field from teacher table.
-- Need to be careful of dupe SIDS, meaning that some payroll rows will be inserted twice into teacher/person,
--    and so teacher PIN/NIN/etc is no longer a unique identifier of teacher records;
--    Only teacher.uuid and person.uuid are safe identifiers for joins etc.


-- TEACHER TABLE
-- insert into z_teacher_test ( -- FOR TESTING
insert into teacher (
    uuid,
    person_uuid,
    school_uuid,
    employment_status_oid,
    pin,
    nassit_number,
    active,
    created_at,
    created_by,
    updated_at,
    updated_by
)
-- ; -- FOR TESTING
select
    UUID() as teacher_uuid,
    UUID() as person_uuid,
    s.uuid as school_uuid,
    'payroll' as employment_status_oid,
    p.pin as pin,
    p.nassit_number as nassit_number,
    1 as active,
    CURRENT_TIMESTAMP as created_at,
    -1 as created_by,
    CURRENT_TIMESTAMP as updated_at,
    -1 as updated_by
from teacher_payroll p
inner join school s
    on s.payroll_sid = p.school_sid
        -- filter out dodgy SID values; occasionally can be blank (empty string) which is v dangerous since
        -- there can be both schools and payroll items with blank SID that then match together
        and s.payroll_sid not in ('', '0', '999999')
-- OPTIONAL: the below line is for adding teachers from additional schools without disturbing the teachers already in the system
inner join cga_300sch_list_230319 c on s.emis_id = c.idemis_code
where p.deleted_at is null
;

-- PERSON TABLE
-- insert into z_person_test ( -- FOR TESTING
insert into person (
    uuid,
    first_name,
-- 	middle_name, -- placeholder
    last_name,
    sex_oid,
    date_of_birth,
    nin,
-- 	null as portrait_uuid, -- placeholder
-- 	null as phone_1, -- placeholder
-- 	null as phone_2, -- placeholder
-- 	null as email, -- placeholder
-- 	null as address, -- placeholder
    created_at,
    created_by,
    updated_at,
    updated_by
)
-- ; -- FOR TESTING
select
    t.person_uuid as person_uuid,
    upper(p.`first_name`) as first_name,
-- 	null as middle_name, -- placeholder
    upper(p.`last_name`) as last_name,
    lower(p.sex) as sex_oid,
    p.date_of_birth,
    p.nin,
-- 	null as portrait_uuid, -- placeholder
-- 	null as phone_1, -- placeholder
-- 	null as phone_2, -- placeholder
-- 	null as email, -- placeholder
-- 	null as address, -- placeholder
    CURRENT_TIMESTAMP as created_at,
    -1 as created_by,
    CURRENT_TIMESTAMP as updated_at,
    -1 as updated_by
from teacher t
inner join teacher_payroll p on t.pin = p.pin
    and p.deleted_at is null
-- OPTIONAL: the below is for adding teachers from additional schools without disturbing the teachers already in the system
left join school s on t.school_uuid = s.uuid
inner join cga_300sch_list_230319 c on s.emis_id = c.idemis_code
;


-- old way using TRM payroll table directly
/* from tmis.payroll p
left join asc21_schools_schooldata s
	on s.sid = p.rc
	and p.yearmonth = '202210'
	and s.sid <> ''
	and s.sid is not null
inner join cga_60sch_list as c on s.emiscode = c.idemis_code
order by s.emiscode */
;

-- check seeding output is sensible
select count(*) from teacher;
select count(*) from person;

select * from teacher t
inner join person p on t.person_uuid = p.uuid
inner join school s on t.school_uuid = s.uuid
;

-- see how many non-duplicated preloaded teachers there would have been
--    (confer with the number of dupe SIDs in the rollout school,
--    which determines the amount of duplicity above this number)
select * from teacher_payroll tp
where school_sid in (select payroll_sid from school)
    and tp.deleted_at is null
;
