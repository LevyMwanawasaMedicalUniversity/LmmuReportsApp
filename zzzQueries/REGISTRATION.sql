SELECT  bi.FirstName, 
        bi.MiddleName, 
        bi.Surname, 
        bi.PrivateEmail, 
        ssl2.StudentID, 
        s.Name, 
        ce.EnrolmentDate,
        edurole.schools.Description,
        bi.StudyType,
        CASE 
        WHEN p.ProgramName LIKE '%y1' THEN 'YEAR 1'
        WHEN p.ProgramName LIKE '%y2' THEN 'YEAR 2'
        WHEN p.ProgramName LIKE '%y3' THEN 'YEAR 3'
        WHEN p.ProgramName LIKE '%y4' THEN 'YEAR 4'
        WHEN p.ProgramName LIKE '%y5' THEN 'YEAR 5'
        WHEN p.ProgramName LIKE '%y6' THEN 'YEAR 6'
        ELSE 'NO REGISTRATION'
    END AS "Year Of Study"
FROM edurole.schools
INNER JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID
INNER JOIN `course-electives` ce ON ssl2.StudentID = ce.StudentID
INNER JOIN `basic-information` bi ON ce.StudentID = bi.ID 
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
WHERE s.ShortName = 'BSCCS'
AND ce.`Year` = 2023
AND ce.EnrolmentDate  > '2023-06-15'
GROUP BY ce.StudentID;