<?php

namespace App\DteConfig;

use App\Notifications\UserPrivilegesSet;
use App\Common\Scripts\UserScopeCacheDt;
use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Options,
    DataTables\Editor\MJoin
    ;
use DataTables\Editor\Format;
use Illuminate\Support\Facades\Auth;

class Reporting
{
    function schoolMonitoring($db,$group, $id,$logging){

         $editor = Editor::inst( $db, 'cache_school_info','uuid' )
//            ->debug( true )
            ->fields(
                Field::inst( 'uuid','school_uuid' )->set( false ),
                Field::inst( 'name','school' )->set( false ),
                Field::inst( 'district_name','district' )->set( false ),
                Field::inst( 'chiefdom_name','chiefdom' )->set( false ),
                Field::inst( 'teacher_total','count_teachers' )->set( false ),
                Field::inst( 'learners_total','count_learners' )->set( false ),
                Field::inst( 'count_classrooms' )->set( false ),
                Field::inst( 'last_teacher_submitted_date','teacher_attendance_date' )->set( false ),
                Field::inst( 'last_teacher_submitted_date_formated','last_teacher_submitted_date' )->set( false ),
                Field::inst( 'last_learner_submitted_date_formated','last_learner_submitted_date' )->set( false ),
            );
            if(Auth::check() && Auth::user()->user_type_id >= 40){
                $editor->fields(  
                    Field::inst( 'school_leader_phone_number' )->set( false ),
                    Field::inst( 'tablet_phone_number' )->set( false ),
                 );
            }

            return $editor;
    }

    function schoolMonitoringByDistrictId($db,$group,$id,$logging,$districtId){

        $editor = Editor::inst( $db, 'cache_school_info','uuid' )
            ->fields(
                Field::inst( 'uuid','school_uuid' )->set( false ),
                Field::inst( 'name','school' )->set( false ),
                Field::inst( 'district_name','district' )->set( false ),
                Field::inst( 'chiefdom_name','chiefdom' )->set( false ),
                Field::inst( 'teacher_total','count_teachers' )->set( false ),
                Field::inst( 'learners_total','count_learners' )->set( false ),
                Field::inst( 'count_classrooms' )->set( false ),
                Field::inst( 'last_teacher_submitted_date','teacher_attendance_date' )->set( false ),
                Field::inst( 'last_teacher_submitted_date_formated','last_teacher_submitted_date' )->set( false ),
                Field::inst( 'last_learner_submitted_date_formated','last_learner_submitted_date' )->set( false ),
            )->where( 'district_id', $districtId );

            if(Auth::check() && Auth::user()->user_type_id >= 40){
                $editor->fields(
                    Field::inst( 'school_leader_phone_number' )->set( false ),
                    Field::inst( 'tablet_phone_number' )->set( false ),
                 );
           }
        return $editor;
    }
}
