<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW school_monitoring_view AS
            SELECT s.uuid school_uuid,
                s.name school,
                gd.name district,
                gc.name chiefdom,
                sch_teachers.count_teachers,
                sch_learners.count_learners,
                classroom.count_classrooms,
                teacher_attendance.last_teacher_submitted_date,
                learner_attendance.last_learner_submitted_date,
                school_contact.phone_number
            FROM school s
            LEFT JOIN geo gd ON gd.id = s.district_id and gd.type = 2
            LEFT JOIN geo gc ON gc.id = s.chiefdom_id and gc.type = 3
            LEFT JOIN(
                SELECT COUNT(uuid) count_teachers,school_uuid
                FROM teacher t
                WHERE (t.deleted_at IS NULL)
                GROUP BY school_uuid
            ) sch_teachers ON sch_teachers.school_uuid = s.uuid
            LEFT JOIN(
                SELECT COUNT(l.uuid) count_learners, sla.school_uuid
                FROM learner l
                LEFT JOIN school_learner_admission sla ON sla.learner_uuid =l.uuid
                WHERE (l.deleted_at IS NULL)
                GROUP BY sla.school_uuid
            ) sch_learners ON sch_learners.school_uuid = s.uuid
            LEFT JOIN(
                SELECT COUNT(uuid) count_classrooms, school_uuid
                FROM school_group sg
                WHERE (sg.deleted_at IS NULL)
                GROUP BY school_uuid
            ) classroom ON classroom.school_uuid = s.uuid
            LEFT JOIN(
                SELECT
                    school_uuid,
                    MAX(date) teacher_attendance_date ,
                    DATE_FORMAT(MAX(date), '%W %D  %b %Y') last_teacher_submitted_date
                FROM person_attendance pa
                WHERE entity_type_oid = 'teacher'
                AND (pa.deleted_at IS NULL)
                GROUP BY school_uuid
            )teacher_attendance ON teacher_attendance.school_uuid = s.uuid
            LEFT JOIN(
                SELECT school_uuid, DATE_FORMAT(MAX(date), '%W %D  %b %Y')  last_learner_submitted_date
                FROM person_attendance pa
                WHERE entity_type_oid = 'learner'
                AND (pa.deleted_at IS NULL)
                GROUP BY school_uuid
            )learner_attendance ON learner_attendance.school_uuid = s.uuid
            LEFT JOIN (
                SELECT t.school_uuid,IFNULL(p.phone_1,p.phone_2) phone_number FROM teacher t
                LEFT JOIN person p ON p.uuid = t.person_uuid
                WHERE employment_role_oid = 'head_teacher' OR employment_role_oid = 'vice_principal'
                GROUP BY school_uuid
            ) school_contact ON school_contact.school_uuid = s.uuid
            WHERE (s.deleted_at IS NULL)
            ORDER BY teacher_attendance.teacher_attendance_date ASC
    ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW school_monitoring_view");
    }
};
