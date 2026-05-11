<?php

namespace App\Api;

use Illuminate\Support\Facades\DB;

class PasswordResetQueries
{
    public static function passwordRequestStatus($installId){

        return DB::select("
        SELECT
        id,
        username,
        install_id,
        status,
        remarks,
        created_at,
        expires_at,
        updated_at,
        updated_by
        FROM android_password_resets
        WHERE install_id = ?
            AND (install_id IS NOT NULL AND install_id <> '')
        ORDER BY updated_at DESC
        ",[$installId]);
    }
}
