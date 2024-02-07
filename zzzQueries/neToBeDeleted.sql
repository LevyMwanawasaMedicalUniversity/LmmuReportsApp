
SELECT
    bi.ID as "Student Number",
    bi.FirstName,
    bi.Surname,
    bi.GovernmentID,
    bi.Sex,
    s.Name,
    bi.StudyType,
    p.ProgramName 
FROM
    `basic-information` bi 
INNER JOIN `student-study-link` ssl2 on ssl2.StudentID = bi.ID 
INNER JOIN study s on s.ID = ssl2.StudyID 
INNER JOIN `study-program-link` spl on spl.StudyID = s.ID 
INNER JOIN programmes p on p.ID = spl.ProgramID
INNER JOIN `program-course-link` pcl on pcl.ProgramID = p.ID
INNER JOIN courses c on c.ID = pcl.CourseID
INNER JOIN `grades-published` gp on gp.CourseNo = c.Name and gp.StudentNo = bi.ID 
WHERE     
    EXISTS (
        SELECT 1
        FROM `grades-published` gp2
        WHERE gp2.StudentNo = bi.ID
        AND gp2.AcademicYear = 2023
        AND gp2.Grade IN ('NE')
    )
    and NOT EXISTS (
        SELECT gp3.CourseNo
        FROM `grades-published` gp3
        INNER JOIN courses c2 on gp3.CourseNo = c.Name
        inner join `program-course-link` pcl2 on pcl2.CourseID = c2.ID
        inner join programmes p2 on pcl2.ProgramID = p2.ID
        WHERE gp3.StudentNo = bi.ID
        AND gp3.AcademicYear = 2023
        AND gp3.Grade IN ('NE')
    )      
GROUP BY bi.ID;