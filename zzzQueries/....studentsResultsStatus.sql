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

    -- Checking 2023 result status, defaulting to 'No Results' if no 2023 grades
    CASE 
        WHEN EXISTS (
            SELECT 1
            FROM `grades-published` gp_2023
            WHERE gp_2023.StudentNo = `basic-information`.ID
            AND gp_2023.AcademicYear = 2023
            AND gp_2023.Grade IN ('NE','F','D+','D','DEF')
        )
        THEN 'REPEAT COURSE'
        WHEN NOT EXISTS (
            SELECT 1
            FROM `grades-published` gp_2023
            WHERE gp_2023.StudentNo = `basic-information`.ID
            AND gp_2023.AcademicYear = 2023
        )
        THEN 'NO RESULTS'
        ELSE 'CLEARED'
    END AS 2023ResultStatus,

    -- Checking if the student has previous failed courses not cleared in later academic years
    CASE 
        WHEN EXISTS (
            SELECT 1
            FROM `grades-published` gp_previous
            WHERE gp_previous.StudentNo = `basic-information`.ID
            AND gp_previous.AcademicYear < 2023
            AND gp_previous.Grade IN ('NE','F','D+','D','DEF')
            AND NOT EXISTS (
                SELECT 1
                FROM `grades-published` gp_cleared
                WHERE gp_cleared.StudentNo = gp_previous.StudentNo
                AND gp_cleared.CourseNo = gp_previous.CourseNo
                AND gp_cleared.AcademicYear > gp_previous.AcademicYear
                AND gp_cleared.Grade NOT IN ('NE','F','D+','D','DEF')
            )
        )
        THEN 'REPEAT COURSE'
        WHEN NOT EXISTS (
            SELECT 1
            FROM `grades-published` gp_previous
            WHERE gp_previous.StudentNo = `basic-information`.ID
        )
        THEN 'NO RESULTS'
        ELSE 'CLEARED'
    END AS PreviousResultStatus

FROM `basic-information`

-- Joins to get student details, study, and school info
JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
JOIN study ON `student-study-link`.StudyID = study.ID
JOIN schools ON study.ParentID = schools.ID

-- Ensure there are results for 2023
WHERE LENGTH(`basic-information`.ID) > 7
  AND EXISTS (
      SELECT 1
      FROM `grades-published` gp_2023
      WHERE gp_2023.StudentNo = `basic-information`.ID
      AND gp_2023.AcademicYear = 2023
  );
