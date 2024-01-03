SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.GovernmentID,
    bi.StudyType,
    s.Name,
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS "Registration Status",
    CASE 
        WHEN g.StudentNo IS NOT NULL THEN
            CASE 
                WHEN MIN(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) >= 1 THEN
                    CONCAT('Year', LEFT(CAST(MIN(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) AS CHAR), LENGTH(MIN(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED))) - 2))
                ELSE
                    'No Year Found'
            END
        ELSE 'NO Year Reported'
    END AS "Year Reported",
    CASE 
        WHEN g.StudentNo IS NOT NULL AND g.AcademicYear = 2022 THEN 'HAS 2022'
        ELSE 'NO 2022'
    END AS "2022 RESULT STATUS"
FROM
    edurole.`basic-information` bi
LEFT JOIN balances b ON b.StudentID = bi.ID 
INNER JOIN `grades` AS g ON g.StudentNo = bi.ID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
LEFT JOIN `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2023
INNER JOIN study s ON s.ID = ssl2.StudyID
WHERE 
    (bi.ID LIKE '210%'
    OR bi.ID LIKE '220%'
    OR bi.ID LIKE '230%'
    OR bi.ID LIKE '190%')
    AND LENGTH(bi.ID) >= 6
    AND "2022 RESULT STATUS" = 'HAS 2022'
GROUP BY
    bi.ID
HAVING
    ("Registration Status" = 'NO REGISTRATION' OR "Registration Status" IS NULL);