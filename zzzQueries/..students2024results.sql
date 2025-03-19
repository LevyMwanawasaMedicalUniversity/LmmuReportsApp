WITH MaxYearTable AS (
    SELECT 
        spl.StudyID,
        ss.ID AS StudentID,
        MAX(p.Year) AS MaxYear
    FROM `basic-information` ss
    JOIN `student-study-link` ssl2 ON ssl2.StudentID = ss.ID
    JOIN `study-program-link` spl ON spl.StudyID = ssl2.StudyID
    JOIN programmes p ON p.ID = spl.ProgramID
    GROUP BY spl.StudyID, ss.ID 
)
SELECT 
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.Sex,
    bi.ID,
    bi.GovernmentID,
    bi.PrivateEmail,
    CASE
        WHEN c.Year + 1 = my.MaxYear THEN 'FinalistByEstimation'
        ELSE 'Not In Final Year'
    END AS EstimationFinalistStatus,
    CASE 
        WHEN gp2.Grade IN ('NE','F','D+','D','DEF') THEN c.`Year`
        ELSE
            CASE 
                WHEN my.MaxYear < (c.`Year` + 1) THEN 'STUDENT HAS GRADUATED'
                WHEN gp2.Grade IS NULL THEN 'NOT APPLICABLE'
                ELSE c.`Year` + 1
            END
    END AS EstimatedCurrentYearOfStudy, 
    s.Name AS Programme,
    s.ShortName,
    s2.Name AS School,
    gp.AcademicYear,
    gp.CourseNo,
    c.CourseDescription,
    gp.CAMarks,
    gp.ExamMarks,
    gp.TotalMarks,
    gp.Grade
FROM `grades-published` gp
INNER JOIN `basic-information` bi ON bi.ID = gp.StudentNo 
INNER JOIN courses c ON c.Name = gp.CourseNo 
INNER JOIN `student-study-link` ssl2 ON bi.ID = ssl2.StudentID 
INNER JOIN study s ON s.ID = ssl2.StudyID 
LEFT JOIN `grades-published` gp2 ON gp2.StudentNo = bi.ID
INNER JOIN schools s2 ON s2.ID = s.ParentID 
INNER JOIN MaxYearTable my ON my.StudentID = bi.ID
WHERE gp.AcademicYear = 2024
GROUP BY
    bi.ID;