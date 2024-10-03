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
),
YearOfReporting AS (
    SELECT
        bi3.ID as StudentId,
        MIN(c3.Year) as YearReported    
    FROM `basic-information` bi3
    LEFT JOIN `grades-published` gp4 ON gp4.StudentNo = bi3.ID 
    LEFT JOIN courses c3 ON gp4.CourseNo = c3.Name 
    GROUP BY bi3.ID
),
BillingData AS (
    SELECT 
        StudentID, 
        SUM(Amount) AS TotalAmount,
        SUM(CASE WHEN Year = 2024 THEN Amount ELSE 0 END) AS Invoice2024
    FROM (
        -- Select most recent billing entry per year
        SELECT 
            billing.StudentID,
            billing.Amount,
            billing.Year,
            ROW_NUMBER() OVER (PARTITION BY billing.StudentID, billing.Year ORDER BY billing.Date DESC) AS rn
        FROM billing
        WHERE Description NOT LIKE '%NULL%' 
        AND PackageName NOT LIKE '%NULL%'
    ) ranked_billing
    -- Select only the most recent record per year (where rn = 1)
    WHERE rn = 1
    GROUP BY StudentID
)
SELECT 
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,    
    bi.Sex,
    bi.ID,
    bi.GovernmentID,
    s.Name as "Programme",
    s.ShortName,
    s2.Name as "School",
    bi.PrivateEmail,
    bi.MobilePhone,
    bd.TotalAmount as "Total Invoice",
    bd.Invoice2024 as "2024 Invoice",
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
    CASE 
        WHEN bi.ID LIKE '240%' THEN 'NEWLY ADMITTED'
        ELSE 'RETURNING STUDENT' 
    END AS StudentType,
    CASE 
        WHEN gp2.Grade IN ('NE','F','D+','D','DEF') THEN 'REPEAT COURSE'
        WHEN gp2.Grade IS NULL THEN 'NOT APPLICABLE'
        ELSE 'CLEARED'
    END AS YearOfStudy
FROM `basic-information` bi
INNER JOIN `student-study-link` ssl2 ON bi.ID = ssl2.StudentID 
INNER JOIN study s ON ssl2.StudyID = s.ID 
LEFT JOIN `grades-published` gp ON gp.StudentNo = bi.ID AND gp.AcademicYear = 2023
LEFT JOIN `grades-published` gp2 ON gp2.StudentNo = bi.ID
LEFT JOIN courses c ON c.Name = gp.CourseNo
LEFT JOIN `program-course-link` pcl ON pcl.CourseID = c.ID 
LEFT JOIN programmes p ON p.ID = pcl.ProgramID
LEFT JOIN `study-program-link` spl ON spl.ProgramID = p.I
LEFT JOIN study s3 ON s3.ID = spl.StudyID
LEFT JOIN MaxYearTable my ON my.StudentID = bi.ID
LEFT JOIN YearOfReporting yor ON yor.StudentId = bi.ID
INNER JOIN BillingData bd ON bd.StudentID = bi.ID
INNER JOIN `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2024
INNER JOIN schools s2 ON s2.ID = s.ParentID 
WHERE bi.ID = 190102249
GROUP BY bi.ID;