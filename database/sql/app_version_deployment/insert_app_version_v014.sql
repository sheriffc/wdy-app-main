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
    'TSC Wi De Ya App Installer SCHOOL v-014.apk', -- e.g. 'TSC Wi De Ya App Installer SCHOOL v-013.apk'
    -- ** Values independent of app variant: **
    14, -- e.g. 13
    '3.3.0', -- e.g. '3.2.0'
    4, -- e.g. 4 (corresponds to AppDatabase.kt version)
    1, -- set active to 0 if you don't want to deploy yet, or 1 if you do
    CONCAT(
        'Important App Update for NEW ACADEMIC YEAR (24MB download size):',
        '\n– New version ensures a smooth transition to the new academic year',
        '\n– Various bug fixes and usability improvements',
        '\n– Allow new classrooms to have same name as old classrooms',
        '\n– Prevent issue with stale attendance submission reminder hanging over from previous day',
        '\n\nGENERAL NOTICE: you need to sync to roll over to the new academic year, then your old classrooms will disappear (but your learners will remain), then you will need to create your new classrooms for this year and then assign the appropriate learners to them'
     ), -- description of changes contained in this version
    '300-school phase: pre new academic year rollover to 2023/4', -- e.g. '300-school phase: start of second half of rollout/training (northern half)'
    NOW()
)

-- if any previous versions have any critical bugs such that you want to force existing users to
--     update before they can continue using the app, then you can set force-update=1 on all previous versions
--     where this applies. DO NOT set force_update=1 on the current/active version,
--     since it is a restrospective flag applying individually to previous versions.
