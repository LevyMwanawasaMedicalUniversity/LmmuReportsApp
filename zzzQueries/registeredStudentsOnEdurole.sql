SELECT 
    `basic-information`.FirstName,
    `basic-information`.MiddleName,
    `basic-information`.Surname,
    `basic-information`.ID,
    `basic-information`.Sex,
    `basic-information`.GovernmentID,
    `basic-information`.PrivateEmail,
    `basic-information`.MobilePhone,
    study.Name AS ProgrammeName,
    study.ShortName AS ProgrammeCode,
    schools.Description AS School,
    `basic-information`.StudyType,
    CASE 
        WHEN `basic-information`.ID LIKE '240%' THEN 'NEWLY ADMITTED'
        ELSE 'RETURNING STUDENT'
    END AS StudentType,
    CASE 
        WHEN programmes.ProgramName LIKE '%y1' THEN 'YEAR 1'
        WHEN programmes.ProgramName LIKE '%y2' THEN 'YEAR 2'
        WHEN programmes.ProgramName LIKE '%y3' THEN 'YEAR 3'
        WHEN programmes.ProgramName LIKE '%y4' THEN 'YEAR 4'
        WHEN programmes.ProgramName LIKE '%y5' THEN 'YEAR 5'
        WHEN programmes.ProgramName LIKE '%y6' THEN 'YEAR 6'
        WHEN programmes.ProgramName LIKE '%y8' THEN 'YEAR 1'
        WHEN programmes.ProgramName LIKE '%y9' THEN 'YEAR 2'
        ELSE 'NO REGISTRATION'
    END AS YearOfStudy
FROM `basic-information`
JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
JOIN study ON `student-study-link`.StudyID = study.ID
JOIN schools ON study.ParentID = schools.ID
JOIN `course-electives` ON `basic-information`.ID = `course-electives`.StudentID AND `course-electives`.Year = :academicYear
JOIN courses ON `course-electives`.CourseID = courses.ID
JOIN `program-course-link` ON courses.ID = `program-course-link`.CourseID
JOIN programmes ON `program-course-link`.ProgramID = programmes.ID
WHERE LENGTH(`basic-information`.ID) > 7
GROUP BY `basic-information`.ID;