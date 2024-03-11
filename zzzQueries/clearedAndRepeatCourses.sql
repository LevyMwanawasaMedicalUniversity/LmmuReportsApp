
    
SELECT *
FROM (
    SELECT
        bi.ID AS "Student Number",
        bi.FirstName,
        bi.Surname,
        bi.GovernmentID,
        bi.Sex,
        s.Name,
        s2.Name AS SchoolName,
        bi.StudyType,
        CASE
            WHEN gp.StudentNo IS NOT NULL THEN
                LEFT(
                    CASE
                        WHEN MAX(CAST(REGEXP_SUBSTR(gp.CourseNo, '[0-9]+') AS UNSIGNED)) >= 1 THEN
                            CONCAT('Year', LEFT(CAST(MAX(CAST(REGEXP_SUBSTR(gp.CourseNo, '[0-9]+') AS UNSIGNED)) AS CHAR), LENGTH(MAX(CAST(REGEXP_SUBSTR(gp.CourseNo, '[0-9]+') AS UNSIGNED))) - 2))
                        ELSE
                            'No Year Found'
                    END,
                    5
                )
            ELSE 'NO RESULTS'
        END AS YearOfStudy,
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM `grades-published` gp2
                WHERE gp2.StudentNo = bi.ID
                AND gp2.AcademicYear = 2023
                AND gp2.Grade IN ('NE', 'F', 'D', 'D+')
            ) THEN 'Repeat Course'
            ELSE 'Clear Pass'
        END AS Result
    FROM
        `basic-information` bi
    INNER JOIN `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
    INNER JOIN study s ON s.ID = ssl2.StudyID
    INNER JOIN schools s2 ON s.ParentID = s2.ID 
    INNER JOIN `study-program-link` spl ON spl.StudyID = s.ID
    INNER JOIN programmes p ON p.ID = spl.ProgramID
    INNER JOIN `program-course-link` pcl ON pcl.ProgramID = p.ID
    INNER JOIN courses c ON c.ID = pcl.CourseID
    INNER JOIN `grades-published` gp ON gp.CourseNo = c.Name AND gp.AcademicYear = 2023 AND gp.StudentNo = bi.ID
    GROUP BY bi.ID
) AS subquery
WHERE Result = 'Repeat Course';



SELECT
    ID,
    FirstName,
    Surname,
    GovernmentID,
    Sex,
    Name AS StudyName,
    SchoolName,
    StudyType,
    CASE
        WHEN Result = 'Clear Pass' THEN
            CONCAT('Year', CAST(RIGHT(YearOfStudy, 1) + 1 AS CHAR))
        ELSE
            YearOfStudy
    END AS YearOfStudy,
    Result
FROM (
    SELECT
        bi.ID,
        bi.FirstName,
        bi.Surname,
        bi.GovernmentID,
        bi.Sex,
        s.Name,
        s2.Name AS SchoolName,
        bi.StudyType,
        CASE
            WHEN gp.StudentNo IS NOT NULL THEN
                LEFT(
                    CASE
                        WHEN MAX(CAST(REGEXP_SUBSTR(gp.CourseNo, '[0-9]+') AS UNSIGNED)) >= 1 THEN
                            CONCAT('Year', LEFT(CAST(MAX(CAST(REGEXP_SUBSTR(gp.CourseNo, '[0-9]+') AS UNSIGNED)) AS CHAR), LENGTH(MAX(CAST(REGEXP_SUBSTR(gp.CourseNo, '[0-9]+') AS UNSIGNED))) - 2))
                        ELSE
                            'No Year Found'
                    END,
                    5
                )
            ELSE 'NO RESULTS'
        END AS YearOfStudy,
        CASE
            WHEN EXISTS (
                SELECT 1
                FROM `grades-published` gp2
                WHERE gp2.StudentNo = bi.ID
                AND gp2.AcademicYear = 2023
                AND gp2.Grade IN ('NE', 'F', 'D', 'D+')
            ) THEN 'Repeat Course'
            ELSE 'Clear Pass'
        END AS Result
    FROM
        `basic-information` bi
    INNER JOIN `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
    INNER JOIN study s ON s.ID = ssl2.StudyID
    INNER JOIN schools s2 ON s.ParentID = s2.ID 
    INNER JOIN `study-program-link` spl ON spl.StudyID = s.ID
    INNER JOIN programmes p ON p.ID = spl.ProgramID
    INNER JOIN `program-course-link` pcl ON pcl.ProgramID = p.ID
    INNER JOIN courses c ON c.ID = pcl.CourseID
    INNER JOIN `grades-published` gp ON gp.CourseNo = c.Name AND gp.AcademicYear = 2023 AND gp.StudentNo = bi.ID
    GROUP BY bi.ID
) AS subquery
WHERE Result = 'Clear Pass';