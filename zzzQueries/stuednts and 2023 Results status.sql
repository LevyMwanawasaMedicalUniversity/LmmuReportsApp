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
        WHEN gp.Grade IN ('NE','F','D+','D','DEF')  THEN 'REPEAT COURSE'
        WHEN gp.Grade is null then 'NOT APPLICABLE'        
        ELSE 'CLEARDED'
    END AS YearOfStudy
FROM `basic-information`
JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
JOIN study ON `student-study-link`.StudyID = study.ID
JOIN schools ON study.ParentID = schools.ID
LEFT JOIN `grades-published` gp on gp.StudentNo = `basic-information`.ID and gp.AcademicYear = 2023
WHERE LENGTH(`basic-information`.ID) > 7
GROUP BY `basic-information`.ID;