<?php

namespace App\Queries;

use Illuminate\Support\Facades\DB;

class Admin
{
    function viewLatestTraceReports($limit){
        $sql = "
        SELECT
            t.id,
            t.created_at,
            u.id user_id,
            u.username,
            -- version_number was renamed to version_code for consistency, but need to support old app submissions
            COALESCE(t.as_json->>'$.version_number',t.as_json->>'$.version_code') version_code,
            -- use decoded stacktrace if it exists
            COALESCE(t.as_json->>'$.stacktrace_decoded', t.as_json->>'$.stacktrace') stack_trace
        FROM android_trace t
        INNER JOIN user u ON u.id = JSON_UNQUOTE(JSON_EXTRACT(t.as_json, '$.user_id'))
        ORDER BY t.created_at DESC
        LIMIT $limit
        ";
        return DB::select($sql);
    }

}
