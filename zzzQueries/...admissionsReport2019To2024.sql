SELECT 
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.ID,
    bi.Sex,
    bi.GovernmentID,
    bi.PrivateEmail,
    bi.MobilePhone,
    s.Name AS ProgrammeName,
    s.ShortName AS ProgrammeCode,
    schools.Description AS School,
    bi.StudyType,
    CASE
        WHEN bi.ID LIKE '190%' THEN '2019'
        WHEN bi.ID LIKE '210%' THEN '2020'
        WHEN bi.ID LIKE '220%' THEN '2021'
        WHEN bi.ID LIKE '230%' THEN '2022'
        WHEN bi.ID LIKE '240%' THEN '2023'
        ELSE '2024'
    END AS Year   
FROM 
    `basic-information` bi
JOIN `student-study-link` ssl3 ON ssl3.StudentID = bi.ID
JOIN study s ON ssl3.StudyID = s.ID
JOIN schools ON s.ParentID = schools.ID
LEFT JOIN `applicants` a ON bi.ID = a.StudentID
WHERE 
    LENGTH(bi.ID) > 7
    AND (
        bi.ID LIKE '190%' 
        OR bi.ID LIKE '210%' 
        OR bi.ID LIKE '220%' 
        OR bi.ID LIKE '230%'
        OR (bi.ID LIKE '240%' AND a.Progress = 'Accepted')
    )
GROUP BY 
    bi.ID;