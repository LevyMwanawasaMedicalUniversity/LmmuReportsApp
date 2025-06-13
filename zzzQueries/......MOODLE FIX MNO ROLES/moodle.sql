INSERT INTO mdl_role_assignments (roleid, contextid, userid, timemodified)
SELECT 
    5, -- Replace with your student role ID if different
    ctx.id,
    ue.userid,
    UNIX_TIMESTAMP()
FROM 
    mdl_user_enrolments ue
JOIN 
    mdl_enrol e ON e.id = ue.enrolid
JOIN 
    mdl_context ctx ON ctx.instanceid = e.courseid AND ctx.contextlevel = 50
WHERE 
    e.courseid = 4026 -- Specifically targeting course ID 4026, so add the id of the course you are targetting
    AND NOT EXISTS (
        SELECT 1 
        FROM mdl_role_assignments ra 
        WHERE ra.contextid = ctx.id AND ra.userid = ue.userid
    );