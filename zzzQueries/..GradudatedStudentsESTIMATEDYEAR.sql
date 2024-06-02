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
    p.ProgramName AS "2023 Programme",
    c.Year as "2023 Year Of Study",
--     max_year_table.MaxYear AS MaxProgrammeYear,    
    CASE 
        WHEN gp2.Grade IN ('NE','F','D+','D','DEF') THEN c.`Year`
        ELSE
            CASE 
                WHEN max_year_table.MaxYear < (c.`Year` + 1) THEN 'STUDENT HAS GRADUATED'
                WHEN gp2.Grade IS NULL THEN 'NOT APPLICABLE'
                ELSE c.`Year` + 1
            END
    END AS EstimatedCurrentYearOfStudy,
    CASE 
        WHEN `basic-information`.ID LIKE '240%' THEN 'NEWLY ADMITTED'
        ELSE 'RETURNING STUDENT'
    END AS StudentType,
    CASE 
        WHEN gp2.Grade IN ('NE','F','D+','D','DEF') THEN 'REPEAT COURSE'
        WHEN gp2.Grade IS NULL THEN 'NOT APPLICABLE'
        ELSE 'CLEARED'
    END AS YearOfStudy
FROM `basic-information`
JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
JOIN study ON `student-study-link`.StudyID = study.ID
JOIN schools ON study.ParentID = schools.ID
LEFT JOIN `grades-published` gp ON gp.StudentNo = `basic-information`.ID AND gp.AcademicYear = 2023
LEFT JOIN `grades-published` gp2 ON gp2.StudentNo = `basic-information`.ID
INNER JOIN courses c ON c.Name = gp.CourseNo
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID 
INNER JOIN programmes p ON p.ID = pcl.ProgramID
INNER JOIN `study-program-link` spl ON spl.ProgramID = p.ID
INNER JOIN study s ON s.ID = spl.StudyID
INNER JOIN (
    SELECT 
        spl.StudyID,
        ss.ID AS StudentID,
        MAX(p.Year) AS MaxYear
    FROM `basic-information` ss
    INNER JOIN `student-study-link` ssl2 ON ssl2.StudentID = ss.ID
    INNER JOIN `study-program-link` spl ON spl.StudyID = ssl2.StudyID
    INNER JOIN programmes p ON p.ID = spl.ProgramID
    GROUP BY spl.StudyID, ss.ID
) max_year_table ON max_year_table.StudentID = `basic-information`.ID AND max_year_table.StudyID = s.ID
WHERE LENGTH(`basic-information`.ID) > 7
GROUP BY `basic-information`.ID, `basic-information`.FirstName, `basic-information`.MiddleName, `basic-information`.Surname, 
        `basic-information`.Sex, `basic-information`.GovernmentID, `basic-information`.PrivateEmail, `basic-information`.MobilePhone, 
        study.Name, study.ShortName, schools.Description, `basic-information`.StudyType, p.ProgramName, c.`Year`, max_year_table.MaxYear;
