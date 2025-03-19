SELECT
    MAX(bi.FirstName) AS FirstName,
    MAX(bi.MiddleName) AS MiddleName,
    MAX(bi.Surname) AS Surname,
    bi.ID,
    MAX(bi.Sex) AS Sex,
    MAX(bi.GovernmentID) AS GovernmentID,
    MAX(bi.PrivateEmail) AS PrivateEmail,
    MAX(bi.MobilePhone) AS MobilePhone,
    MAX(study.Name) AS ProgrammeName,
    MAX(study.ShortName) AS ProgrammeCode,
    MAX(schools.Description) AS School,
    MAX(bi.StudyType) AS StudyType,
    MAX(p.ProgramName) AS "2023 Programme",
    MAX(c.`Year`) AS "2023 Year Of Study",

    -- For EstimatedCurrentYearOfStudy, check all years for repeat courses
    CASE
        -- If there are any uncleated failures, student should repeat current year
        WHEN EXISTS (
            SELECT 1
            FROM `grades-published` gp_check
            WHERE gp_check.StudentNo = bi.ID
            AND gp_check.Grade IN ('NE','F','D+','D','DEF')
            AND NOT EXISTS (
                SELECT 1
                FROM `grades-published` gp_cleared
                WHERE gp_cleared.StudentNo = gp_check.StudentNo
                AND gp_cleared.CourseNo = gp_check.CourseNo
                AND gp_cleared.AcademicYear > gp_check.AcademicYear
                AND gp_cleared.Grade NOT IN ('NE','F','D+','D','DEF')
            )
        ) THEN MAX(c.`Year`)
        ELSE
            CASE
                WHEN MAX(max_year_table.MaxYear) < (MAX(c.`Year`) + 1) THEN 'STUDENT HAS GRADUATED'
                WHEN NOT EXISTS (
                    SELECT 1
                    FROM `grades-published` gp_any
                    WHERE gp_any.StudentNo = bi.ID
                ) THEN 'NOT APPLICABLE'
                ELSE MAX(c.`Year`) + 1
            END
    END AS EstimatedCurrentYearOfStudy,

    -- Check for graduates with research courses in 2024
    CASE
        WHEN MAX(max_year_table.MaxYear) < (MAX(c.`Year`) + 1) THEN 
            CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM `grades-published` gp_research
                    JOIN courses rc ON rc.Name = gp_research.CourseNo
                    WHERE gp_research.StudentNo = bi.ID
                    AND gp_research.AcademicYear = 2024
                    AND rc.CourseDescription LIKE '%Research%'
                ) THEN 'GRADUATE WITH RESEARCH'
                ELSE 'GRADUATE WITHOUT RESEARCH'
            END
        ELSE 'NOT A GRADUATE'
    END AS ResearchStatus,

    CASE
        WHEN bi.ID LIKE '240%' THEN 'NEWLY ADMITTED'
        ELSE 'RETURNING STUDENT'
    END AS StudentType,

    -- For YearOfStudy, check all years for repeat courses
    CASE
        WHEN EXISTS (
            SELECT 1
            FROM `grades-published` gp_status
            WHERE gp_status.StudentNo = bi.ID
            AND gp_status.Grade IN ('NE','F','D+','D','DEF')
            AND NOT EXISTS (
                SELECT 1
                FROM `grades-published` gp_cleared
                WHERE gp_cleared.StudentNo = gp_status.StudentNo
                AND gp_cleared.CourseNo = gp_status.CourseNo
                AND gp_cleared.AcademicYear > gp_status.AcademicYear
                AND gp_cleared.Grade NOT IN ('NE','F','D+','D','DEF')
            )
        ) THEN 'REPEAT COURSE'
        WHEN NOT EXISTS (
            SELECT 1
            FROM `grades-published` gp_any
            WHERE gp_any.StudentNo = bi.ID
        ) THEN 'NOT APPLICABLE'
        ELSE 'CLEARED'
    END AS YearOfStudy
FROM `basic-information` bi
JOIN `student-study-link` ON `student-study-link`.StudentID = bi.ID
JOIN study ON `student-study-link`.StudyID = study.ID
JOIN schools ON study.ParentID = schools.ID
LEFT JOIN `grades-published` gp ON gp.StudentNo = bi.ID AND gp.AcademicYear = 2024
LEFT JOIN `grades-published` gp2 ON gp2.StudentNo = bi.ID
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
) max_year_table ON max_year_table.StudentID = bi.ID AND max_year_table.StudyID = s.ID
WHERE LENGTH(bi.ID) > 7
GROUP BY bi.ID;