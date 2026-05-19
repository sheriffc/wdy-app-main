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

class Admin
{
    function manageUsers($db,$group, $id,$logging){

        return Editor::inst( $db, 'user','id' )
//            ->debug( true )
            ->fields(
                Field::inst( 'user.id' )->set( false ),
                Field::inst( 'user.username' ),
                Field::inst( 'user.name' ),
                Field::inst( 'user.email' ),
                Field::inst( 'user.phone' ),
                Field::inst( 'user.user_type_id' )
                    ->options( Options::inst()
                        ->table('user_type')
                        ->value('type_id')
                        ->label('type_name')
                        ->order( 'display_order')
                        ->where( function ($q) use ($group) {
                            if($group<999) {
                                $q->where('type_id', $group, '<');
                            }
                            $q->where('active', 1, '=');
                        })
                    )
                ,
                Field::inst( 'user_type.type_name' ),
                Field::inst( 'user.requested_role' ),
                Field::inst( 'user.granted_role' ),
                Field::inst( 'user.mobile_access' )
                    ->setFormatter( function ( $val, $data, $opts ) {
                        return ! $val ? 0 : 1;
                    } ),
                Field::inst( 'user.active' )
                    ->setFormatter( function ( $val, $data, $opts ) {
                        return ! $val ? 0 : 1;
                    } ),
                Field::inst( 'user.created_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ->set (false),
                Field::inst( 'user.updated_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ->set (false),
                Field::inst( 'user.email_verified_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ->set (false),
            )
            ->where( function ( $q ) use ($group) {
              $q->where( function ($r) use ($group) {
                  $r->where( 'user.user_type_id', $group, '<' );
                  $r->or_where( 'user.user_type_id', '' );
                  $r->or_where( 'user.user_type_id', null, );
              });
            })
            ->leftJoin("user_type","user.user_type_id = user_type.type_id AND user_type.active")
//            ->join(
//                Mjoin::inst( 'user_scope_group' )
//                    ->link( 'user.id', 'user_scope_group_user_link.user_id' )
//                    ->link( 'user_scope_group.id', 'user_scope_group_user_link.group_id' )
//                    ->order( 'group_name asc' )
//                    ->fields(
//                        Field::inst( 'id' )
//                            ->options( Options::inst()
//                                ->table( 'user_scope_group' )
//                                ->value( 'id' )
//                                ->label( 'group_name' )
//                            ),
//                        Field::inst( 'group_name' ),
//                        Field::inst( 'group_description' )
//                    )
//            )
            ->on('preEdit', function($e, $id, &$values){
                $rec = $e->db()->sql("SELECT user_type_id FROM user WHERE id='{$id}'")->fetch();
                $beforeTypeId = $rec['user_type_id'];
                $afterTypeId = $values['user']['user_type_id'];

                if(empty($beforeTypeId) && $afterTypeId>0){
                    $adminName = \Auth::user()->name;
                    $rec = $e->db()->sql("SELECT type_name FROM user_type WHERE type_id='{$values['user']['user_type_id']}'")->fetch();
                    $typeName = $rec['type_name'];
                    $userId = $id;
                    if($values['user']['user_type_id'] !== 0){
                        logger("send email");
                        $users = \App\Models\User::where('id', $userId)->get();
                        foreach ($users as $user) {
                            $user->notify(new UserPrivilegesSet([
                                'admin_name'=>$adminName,
                                'user_type_name'=>$typeName
                            ]));
                        }
                    }
                }
            })
            ->on('postEdit', function($e, $id, $values, $row){
                //trigger a scope cache check
                UserScopeCacheDt::run($e->db());
            })
            ;
    }
    function mobilePasswordReset($db,$group, $id,$logging){

        return Editor::inst( $db, 'android_password_resets','id' )
//            ->debug( true )
            ->fields(
                Field::inst( 'android_password_resets.id' )->set( false ),
                Field::inst( 'android_password_resets.username' ),
                Field::inst( 'android_password_resets.install_id' ),
                Field::inst( 'android_password_resets.proposed_new_password_hash' ),
                Field::inst( 'android_password_resets.status' ),
                Field::inst( 'android_password_resets.remarks' ),
                Field::inst( 'android_password_resets.created_at' ),
                Field::inst( 'android_password_resets.expires_at' ),
                Field::inst( 'android_password_resets.updated_at' ),
                Field::inst( 'android_password_resets.updated_by' ),
                Field::inst( 'user.name' ),
                Field::inst( 'user.user_type_id' ),
                Field::inst( 'user_type.type_name' ),
                Field::inst( 'user.phone' ),
                Field::inst( 'user.granted_role' ),
            )
            ->leftJoin("user","user.username = android_password_resets.username AND user.active")
            ->leftJoin("user_type","user.user_type_id = user_type.type_id")
//            ->where("android_password_resets.status", "pending")
            ->where("user.user_type_id", $group, '<')
            ;
    }

    function manageUsersScopeCustom($db,$group, $id,$logging){
        return Editor::inst( $db, 'user_scope_custom_assignment','id' )
//            ->debug( true )
            ->fields(
                Field::inst( 'user_scope_custom_assignment.user_id' ),
                Field::inst( 'user_scope_custom_assignment.school_uuid' )
                    ->options( Options::inst()
                        ->table('school')
                        ->value('uuid')
                        ->label('name')
                    )
                ,
                Field::inst( 'school.name' ),
                Field::inst( 'district_office.name' ),
                Field::inst( 'user_scope_custom_assignment.created_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ,
            )
            ->leftJoin('school', 'user_scope_custom_assignment.school_uuid = school.uuid')
            ->leftJoin('district_office', 'school.district_office_uuid = district_office.uuid')
            ->where( 'user_scope_custom_assignment.user_id', $_POST['id'] )
            ->on( 'preCreate', function ( $e, $values ) {
                $userId = $_POST['id'];
                $schoolId = $values['user_scope_custom_assignment']['school_uuid'];
                $e->field('user_scope_custom_assignment.user_id')->setValue($userId);
                $e->field('user_scope_custom_assignment.created_at')->setValue(date("Y-m-d H:i:s"));

                $clearFields=false;
                //check if permission already exists for user
                if(!$clearFields){
                    $sql = "SELECT * FROM user_scope_custom_assignment WHERE user_id='$userId' AND school_uuid='$schoolId'";
                    $res = $e->db()->sql($sql)->fetch();
                    if($res) $clearFields = true;
                }

                if($clearFields){
                    $e->field('user_scope_custom_assignment.user_id')->set( false );
                    $e->field('user_scope_custom_assignment.school_uuid')->set( false );
                    $e->field('user_scope_custom_assignment.created_at')->set( false );
                }
            })
            ->on('postCreate', function ( $e, $id, &$values, &$row ) {
                $this->clearHash($_POST['id'],$e);
            })
            ->on('postRemove', function ( $e,  &$id, &$values ) {
                $this->clearHash($_POST['id'],$e);
            })
            ;
    }

    function manageUsersScopeGroup($db,$group, $id,$logging){
        return Editor::inst( $db, 'user_scope_group_user_link','id' )
//            ->debug( true )
            ->fields(
                Field::inst( 'user_scope_group_user_link.user_id' ),
                Field::inst( 'user_scope_group_user_link.group_id' )
                    ->options( Options::inst()
                        ->table('user_scope_group')
                        ->value('id')
                        ->label('group_name')
                    )
                ,
                Field::inst( 'user_scope_group.group_name' ),
                Field::inst( 'user_scope_group.group_description' ),
                Field::inst( 'user_scope_group_user_link.created_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ,
            )
            ->leftJoin('user_scope_group', 'user_scope_group.id = user_scope_group_user_link.group_id')
            ->where( 'user_scope_group_user_link.user_id', $_POST['id'] )
            ->on( 'preCreate', function ( $e, $values ) {
                $userId = $_POST['id'];
                $groupId = $values['user_scope_group_user_link']['group_id'];
                $e->field('user_scope_group_user_link.user_id')->setValue($userId);
                $e->field('user_scope_group_user_link.created_at')->setValue(date("Y-m-d H:i:s"));

                $clearFields=false;
                //check if permission already exists for user
                if(!$clearFields){
                    $sql = "SELECT * FROM user_scope_group_user_link WHERE user_id='$userId' AND group_id='$groupId'";
                    $res = $e->db()->sql($sql)->fetch();
                    if($res) $clearFields = true;
                }

                if($clearFields){
                    $e->field('user_scope_group_user_link.user_id')->set( false );
                    $e->field('user_scope_group_user_link.group_id')->set( false );
                    $e->field('user_scope_group_user_link.created_at')->set( false );
                }
            })
            ->on('postCreate', function ( $e, $id, &$values, &$row ) {
                $this->clearHash($_POST['id'],$e);
            })
            ->on('postRemove', function ( $e,  &$id, &$values ) {
                $this->clearHash($_POST['id'],$e);
            })
            ;
    }

    function clearHash($userId,$e){
        //clear hash so it can be regenerated
//        $user = User::find($userId);
//        $user->scope_hash = null;
//        $user->save();
        $e->db()->update('user',['scope_hash'=>null],['id'=>$userId]);
    }

    function manageScopeGroups($db,$group, $id,$logging){
        return Editor::inst( $db, 'user_scope_group','id' )
//            ->debug( true )
            ->fields(
                Field::inst( 'user_scope_group.group_name' ),
                Field::inst( 'user_scope_group.group_description' ),
                Field::inst( 'user_scope_group.district_selection' )
                    ->options( Options::inst()
                        ->table('district_office')
                        ->value('uuid')
                        ->label('name')
                    ),
                Field::inst( 'user_scope_group.school_selection' )
                    ->options( Options::inst()
                        ->table('school')
                        ->value('uuid')
                        ->label('name')
                    )
                ,
                Field::inst( 'user_scope_group.active' )
                    ->setFormatter( function ( $val, $data, $opts ) {
                    return ! $val ? 0 : 1;
                } ),
                Field::inst( 'user_scope_group.display_order' ),
                Field::inst( 'user_scope_group.created_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ->set (false),
                Field::inst( 'user_scope_group.updated_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ->set (false),
            )
            ;
    }

    function manageSchools($db,$group, $id,$logging){

//        $uid = Auth::user()->id;

        return Editor::inst( $db, 'school','uuid' )
//            ->debug( true )
            ->fields(
                Field::inst( 'school.uuid' )->set( false ),
                Field::inst( 'school.name' ),
                Field::inst( 'school.emis_id' ),
                Field::inst( 'school.payroll_sid' ),
                Field::inst( 'school.wideya_id' ),
                Field::inst( 'school.fabinc_recordid' ),
                Field::inst( 'school.school_education_level_oid' )
                    ->options( Options::inst()
                        ->table('option_list')
                        ->value('item_id')
                        ->label('item_name')
                        ->where( function ($q) {
                            $q->where('list_name', 'school_education_level', '=');
                        })
                    ),
                Field::inst( 'education_level.item_name' ),
                Field::inst( 'school.district_office_uuid' )
                    ->options( Options::inst()
                        ->table('district_office')
                        ->value('uuid')
                        ->label('name')
                        ->where( function ($q) {
                            $q->where('active', 1, '=');
                        })
                    ),
                Field::inst( 'district_office.name' ),
                Field::inst( 'school.district_id' ),
                Field::inst( 'school.chiefdom_id' ),
                Field::inst( 'chiefdom.name' )->set( false ),
                Field::inst( 'school.council_name' ),
                Field::inst( 'school.section_name' ),
                Field::inst( 'school.town_name' ),
                Field::inst( 'school.address' ),
                Field::inst( 'school.lat' ),
                Field::inst( 'school.lng' ),
                Field::inst( 'school.media_photo_uuid' ),
                Field::inst( 'school.created_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ->set (false),
                Field::inst( 'school.updated_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ->set (false),
            )
            ->leftJoin( 'option_list as education_level',   "education_level.list_name='school_education_level' AND school.school_education_level_oid = education_level.item_id")
            ->leftJoin( 'district_office',   "district_office.uuid = school.district_office_uuid AND district_office.active")
            ->leftJoin( 'geo as chiefdom',   "chiefdom.id = school.chiefdom_id")
            ;
    }
    function manageDistrictOffices($db,$group, $id,$logging){

//        $uid = Auth::user()->id;

        return Editor::inst( $db, 'district_office','uuid' )
//            ->debug( true )
            ->fields(
                Field::inst( 'district_office.uuid' )->set( false ),
                Field::inst( 'district_office.name' ),
                Field::inst( 'district_office.district_id' )->set( false ),
                Field::inst( 'district_office.district_code' ),
                Field::inst( 'district_office.lat' ),
                Field::inst( 'district_office.lng' ),
                Field::inst( 'district_office.active' )
                    ->setFormatter( function ( $val, $data, $opts ) {
                        return ! $val ? 0 : 1;
                    } ),
                Field::inst( 'district_office.created_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ->set (false),
                Field::inst( 'district_office.updated_at' )
                    ->getFormatter( Format::dateSqlToFormat( 'd-m-y H:i' ) )
                    ->set (false),
            );
    }
}
