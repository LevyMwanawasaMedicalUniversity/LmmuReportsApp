SELECT DISTINCT 
    basic_information_s_r_s.FirstName,
    basic_information_s_r_s.MiddleName,
    basic_information_s_r_s.Surname,
    basic_information_s_r_s.StudentID AS ID,
    basic_information_s_r_s.GovernmentID,
    basic_information_s_r_s.Sex,
    basic_information_s_r_s.PrivateEmail,
    basic_information_s_r_s.MobilePhone,
    study_s_r_s.study_name AS ProgrammeName,
    study_s_r_s.study_shortname AS ProgrammeCode,
    schools_s_r_s.school_name AS School,
    basic_information_s_r_s.StudyType,
    CASE 
        WHEN basic_information_s_r_s.StudentID LIKE '240%' THEN 'NEWLY ADMITTED'
        ELSE 'RETURNING STUDENT'
    END AS StudentType,
    CASE 
        WHEN program_s_r_s.program_name LIKE '%y1' THEN 'YEAR 1'
        WHEN program_s_r_s.program_name LIKE '%y2' THEN 'YEAR 2'
        WHEN program_s_r_s.program_name LIKE '%y3' THEN 'YEAR 3'
        WHEN program_s_r_s.program_name LIKE '%y4' THEN 'YEAR 4'
        WHEN program_s_r_s.program_name LIKE '%y5' THEN 'YEAR 5'
        WHEN program_s_r_s.program_name LIKE '%y6' THEN 'YEAR 6'
        WHEN program_s_r_s.program_name LIKE '%y8' THEN 'YEAR 1'
        WHEN program_s_r_s.program_name LIKE '%y9' THEN 'YEAR 2'
        ELSE 'NO REGISTRATION'
    END AS YearOfStudy
FROM basic_information_s_r_s
JOIN student_study_link_s_r_s ON student_study_link_s_r_s.student_id = basic_information_s_r_s.StudentID
JOIN study_s_r_s ON student_study_link_s_r_s.study_id = study_s_r_s.study_id
JOIN schools_s_r_s ON study_s_r_s.parent_id = schools_s_r_s.school_id
JOIN course_registration ON basic_information_s_r_s.StudentID = course_registration.StudentID AND course_registration.Year = 2024
JOIN courses_s_r_s ON course_registration.CourseID = courses_s_r_s.course_name
JOIN program_course_links_s_r_s ON courses_s_r_s.course_id = program_course_links_s_r_s.course_id
JOIN program_s_r_s ON program_course_links_s_r_s.program_id = program_s_r_s.programme_id
WHERE LENGTH(basic_information_s_r_s.StudentID) > 7 AND (study_s_r_s.study_name = 'bscBMS' OR study_s_r_s.study_name = 'DIPBMS');