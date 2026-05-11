INSERT INTO user_access_token_link
SELECT
    null,
    json_data->>'$.user_id' user_id,
    json_data->>'$.access_token' access_token,
    created_at
FROM android_api_log
WHERE user_action='authentication successful' AND created_at > (SELECT MAX(created_at) FROM user_access_token_link)
GROUP BY user_id, access_token
