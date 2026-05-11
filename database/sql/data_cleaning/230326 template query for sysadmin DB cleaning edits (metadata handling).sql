-- Standard template for sysadmin DB cleaning / edits,
--     to ensure sync timestamps/metadata are maintained correctly
UPDATE xxx
SET
    -- You may wish you omit updated_at for minor data cleaning
    --     in order to preserve the original updated_at/by metadata from the school
    updated_at = NOW(),
    updated_by = -1,
    synced_at = NOW(),
    synced_by = -1,
    synced_by_install_id = 'server',
-- field_to_edit = 'new value'
;
