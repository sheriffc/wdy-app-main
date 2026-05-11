-- Production DB clean pre 300 school rollout (after 2-school pretest in Freetown)

-- Revoke access token from users so these server-side edits don't get overridden
UPDATE user
SET client_access_token = NULL,
	client_access_created_at = NULL
;

-- person timestamps mistake

UPDATE person
SET
	updated_at = created_at
-- 	synced_at = CURRENT_TIMESTAMP -- doesn't exist yet in production DB, not merged yet
WHERE updated_at = '0000-00-00'
	AND created_by = 0
;

-- change sysadmin created_by / updated_by values to -1 instead of 0

UPDATE school
-- UPDATE teacher
-- UPDATE person
SET
	created_by = -1,
	updated_at = updated_at
WHERE created_by = 0
;

UPDATE school
-- UPDATE teacher
-- UPDATE person
SET
	updated_by = -1,
	updated_at = updated_at
WHERE updated_by = 0
;
