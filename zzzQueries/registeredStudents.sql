SELECT
    bi.FirstName, 
    bi.MiddleName,
    bi.Surname,	
    bi.ID,
    bi.GovernmentID,
    bi.PrivateEmail,
    bi.MobilePhone,
    s.Name AS "ProgrammeName",
    s2.Description AS "School",
    bi.StudyType,
    CASE 
        WHEN bi.ID LIKE '240%' THEN 'NEWLY ADMITTED'
        ELSE 'RETURNING STUDENT'
    END AS 'Student Type',    
    CASE 
        WHEN p.ProgramName LIKE '%y1' THEN 'YEAR 1'
        WHEN p.ProgramName LIKE '%y2' THEN 'YEAR 2'
        WHEN p.ProgramName LIKE '%y3' THEN 'YEAR 3'
        WHEN p.ProgramName LIKE '%y4' THEN 'YEAR 4'
        WHEN p.ProgramName LIKE '%y5' THEN 'YEAR 5'
        WHEN p.ProgramName LIKE '%y6' THEN 'YEAR 6'
        WHEN p.ProgramName LIKE '%y8' THEN 'YEAR 1'
        WHEN p.ProgramName LIKE '%y9' THEN 'YEAR 2'
        ELSE 'NO REGISTRATION'
    END AS "Year Of Study"
FROM
    `basic-information` bi
INNER JOIN `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
INNER JOIN study s ON ssl2.StudyID  = s.ID
INNER JOIN schools s2 on s2.ID = s.ParentID
INNER JOIN `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2024
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
WHERE
    LENGTH(bi.ID) > 7    
--     AND (
--         bi.ID LIKE '190%'
--         OR bi.ID LIKE '210%'
--         OR bi.ID LIKE '220%'
--         OR bi.ID LIKE '230%'
--     )
GROUP BY
    bi.ID;