INSERT INTO app_version (
    uri,
    filename,
    version_code,
    version_name,
    db_schema,
    active,
    description,
    internal_notes,
    updated_at
) VALUES (
    -- ** Values dependent on app variant: **
    'https://wideya.org/app-download', -- e.g. 'https://wideya.org/app-download' or 'https://demo.wideya.org/app-download'
    'TSC Wi De Ya App Installer SCHOOL v-013.apk', -- e.g. 'TSC Wi De Ya App Installer SCHOOL v-013.apk'
    -- ** Values independent of app variant: **
    13, -- e.g. 13
    '3.2.0', -- e.g. '3.2.0'
    4, -- e.g. 4 (corresponds to AppDatabase.kt version)
    1, -- set active to 0 if you don't want to deploy yet, or 1 if you do
    CONCAT(
        'Important App Update (24MB download size):',
        '\n– Fix issue with teacher attendance list sometimes showing empty',
        '\n– Improve stability of learner attendance buttons',
        '\n– New feature: analysis for learner attendance',
        '\n– New feature: counter on Data Sync page for how many records need uploading',
        '\n– Improve multi-select of learners',
        '\n– Design \'Demo\' app variant to be more visually distinguishable',
        '\n– Improve handling of academic year rollover',
        '\n– Various other improvements and fixes'
    ), -- description of changes contained in this version
    '300-school phase: start of second half of rollout/training (northern half)', -- e.g. '300-school phase: start of second half of rollout/training (northern half)'
    NOW()
)

-- if any previous versions have any critical bugs such that you want to force existing users to
--     update before they can continue using the app, then you can set force-update=1 on all previous versions
--     where this applies. DO NOT set force_update=1 on the current/active version,
--     since it is a restrospective flag applying individually to previous versions.
