-- check latest version_code per user
SELECT
    json_data->>'$.params.user_id' AS user_id,
    json_data->>'$.params.username' AS username,
    s.name as school_name,
    g_dis.name as district_name,
    json_data->>'$.params.version_code' AS latest_version_code,
    aal.created_at AS latest_timestamp
FROM android_api_log aal
INNER JOIN (
    SELECT max(id) id
    FROM android_api_log
    GROUP BY json_data->>'$.params.user_id'
) AS aal_latest ON aal.id = aal_latest.id
LEFT JOIN user_scope_custom_assignment AS usca
    ON usca.user_id = json_data->>'$.params.user_id'
LEFT JOIN school s
    ON s.uuid = usca.school_uuid
LEFT JOIN geo g_dis ON g_dis.id = s.district_id
WHERE user_action = 'check_in'
  AND s.name IS NOT NULL
    -- 	AND json_data->>'$.params.version_code' < 9
-- GROUP BY json_data->>'$.params.user_id' -- comment out to see multiple school assignments for users assigned to many
ORDER BY latest_timestamp DESC
-- ORDER BY latest_version_code DESC
-- ORDER BY g_dis.name
;

-- count users per latest_version_code
SELECT
    json_data->>'$.params.version_code' AS latest_version_code,
    count(*)
FROM android_api_log aal
INNER JOIN (
    SELECT max(id) id
    FROM android_api_log
    GROUP BY json_data->>'$.params.user_id'
) AS aal_latest ON aal.id = aal_latest.id
WHERE user_action = 'check_in'
GROUP BY latest_version_code
ORDER BY latest_version_code DESC
;
