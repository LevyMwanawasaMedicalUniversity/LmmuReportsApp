SELECT
    bi.ID as "Student Number",
    bi.FirstName,
    bi.Surname,
    bi.GovernmentID,
    bi.Sex,
    s.Name as "Programme",
    s2.Name as "School",
    bi.StudyType 
FROM
    `basic-information` bi 
INNER JOIN `student-study-link` ssl2 on ssl2.StudentID = bi.ID 
INNER JOIN study s on s.ID = ssl2.StudyID 
INNER JOIN schools s2 on s2.ID = s.ParentID 
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
    AND NOT EXISTS (
        SELECT gp3.CourseNo
        FROM `grades-published` gp3
        INNER JOIN courses c2 on gp3.CourseNo = c2.Name
        INNER JOIN `program-course-link` pcl2 on pcl2.CourseID = c2.ID
        INNER JOIN programmes p2 on pcl2.ProgramID = p2.ID
        WHERE gp3.StudentNo = bi.ID
        AND gp3.AcademicYear = 2023
        AND gp3.Grade IN ('NE')
        AND p2.ID = p.ID 
    )      
GROUP BY bi.ID, bi.FirstName, bi.Surname, bi.GovernmentID, bi.Sex, s.Name,s2.Name, bi.StudyType;