SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.ID,
    bi.GovernmentID,
    bi.Sex,
    bi.StudyType,
    bi.Nationality,
    s.Name AS "Programme",
    s2.Name AS "School",
    CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM `grades-published` gp2 
            WHERE gp2.StudentNo = bi.ID 
                AND gp2.Grade IN ('NE','F','D+','D','DEF')
                AND NOT EXISTS (
                    SELECT 1 
                    FROM `grades-published` gp3 
                    WHERE gp3.StudentNo = gp2.StudentNo 
                        AND gp3.CourseNo = gp2.CourseNo 
                        AND gp3.Grade NOT IN ('NE','F','D+','D','DEF')
                )
        ) THEN 'REPEAT COURSE'
        ELSE 'CLEARED'
    END AS PreviousYearResults
FROM `basic-information` bi 
INNER JOIN `student-study-link` ssl2 ON bi.ID = ssl2.StudentID 
INNER JOIN study s ON ssl2.StudyID = s.ID
INNER JOIN `grades-published` gp on gp.StudentNo = bi.ID and gp.AcademicYear = 2024
INNER JOIN schools s2 ON s2.ID = s.ParentID 
GROUP BY bi.ID;