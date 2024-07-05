select * from mdl_user mu where mu.username = 'guest';

INSERT INTO mdl_user (
    auth, confirmed, policyagreed, deleted, suspended, mnethostid, 
    username, password, idnumber, firstname, lastname, email, emailstop, 
    phone1, phone2, institution, department, address, city, country, lang, 
    calendartype, theme, timezone, firstaccess, lastaccess, lastlogin, 
    currentlogin, lastip, secret, picture, description, descriptionformat, 
    mailformat, maildigest, maildisplay, autosubscribe, 
    trackforums, timecreated, timemodified, trustbitmask, imagealt, 
    lastnamephonetic, firstnamephonetic, middlename, alternatename, moodlenetprofile
) 
VALUES (
    'manual', 1, 0, 0, 0, 1, 'guest', '!', '', 'Guest', 'User', 'guest@example.com', 0, 
    '', '', '', '', '', '', '', 'en', 'gregorian', '', '99', 0, 0, 0, 0, '', 
    '', 0, NULL, 1, 1, 2, 1, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, NULL, 
    NULL, NULL, NULL, NULL, ''
);

SELECT id FROM mdl_role WHERE shortname = 'guest';#6
SELECT id FROM mdl_context WHERE contextlevel = 10;#1

INSERT INTO mdl_role_assignments (roleid, contextid, userid, timemodified)
VALUES (6, 1, (SELECT id FROM mdl_user WHERE username = 'guest'), UNIX_TIMESTAMP());

INSERT INTO mdl_config (name, value) VALUES ('siteguest', (SELECT id FROM mdl_user WHERE username = 'guest'))
ON DUPLICATE KEY UPDATE value = (SELECT id FROM mdl_user WHERE username = 'guest');