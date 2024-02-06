SELECT
    bi.ID as "Student Number",
    bi.FirstName,
    bi.Surname,
    bi.GovernmentID,
    bi.Sex,
    s.Name,
    bi.StudyType 
FROM
    `basic-information` bi 
INNER JOIN `student-study-link` ssl2 on ssl2.StudentID = bi.ID 
INNER JOIN study s on s.ID = ssl2.StudyID 
INNER JOIN `study-program-link` spl on spl.StudyID = s.ID 
INNER JOIN programmes p on p.ID = spl.ProgramID and p.ProgramName  = 'MBChB-FT-2023-Y3'
INNER JOIN `program-course-link` pcl on pcl.ProgramID = p.ID
INNER JOIN courses c on c.ID = pcl.CourseID  
INNER JOIN `grades-published` gp on gp.CourseNo = c.Name and gp.AcademicYear = 2023 and gp.StudentNo = bi.ID 
WHERE     
    NOT EXISTS (
        SELECT 1
        FROM `grades-published` gp2
        WHERE gp2.StudentNo = bi.ID
        AND gp2.AcademicYear = 2023
        AND gp2.Grade IN ('NE','F','D','D+')
    )
GROUP BY bi.ID;