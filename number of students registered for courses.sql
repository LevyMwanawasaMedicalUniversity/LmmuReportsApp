SELECT
--     bi.ID,
--     bi.FirstName AS FirstName,
--     bi.MiddleName AS MiddleName,
--     bi.Surname AS Surname,
--     p.ProgramName AS ProgramName,
--     c.ID AS CourseID,
    c.Name AS CourseName,
    c.CourseDescription ,
    COUNT(ce.ID) AS NumberOfRegistrations
FROM programmes p
INNER JOIN `program-course-link` pcl ON pcl.ProgramID = p.ID 
INNER JOIN courses c ON c.ID = pcl.CourseID
INNER JOIN `study-program-link` spl ON spl.ProgramID = p.ID
INNER JOIN study s ON spl.StudyID = s.ID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID
INNER JOIN `basic-information` bi ON bi.ID = ssl2.StudentID
INNER JOIN `course-electives` ce ON ce.CourseID = c.ID AND ce.StudentID = bi.ID AND ce.`Year` = 2023