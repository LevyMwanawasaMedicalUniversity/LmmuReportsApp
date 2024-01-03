SELECT
    bi.FirstName,
    bi.Surname,
    gp.StudentNo, 
    gp.ProgramNo, 
    gp.CourseNo, 
    gp.Grade,
    s.Name,
    s2.Description
FROM edurole.`grades-published` gp
inner join `basic-information` bi on gp.`user` = bi.ID 
inner join courses c on c.Name  = gp.CourseNo 
INNER JOIN `program-course-link` pcl ON c.ID = pcl.CourseID
INNER JOIN programmes p ON pcl.ProgramID = p.ID
INNER JOIN `study-program-link` spl  ON p.ID = spl.ProgramID 
INNER JOIN study s ON spl.StudyID = s.ID
INNER JOIN schools s2 ON s.ParentID = s2.ID 
WHERE Grade REGEXP '^[0-9]+$'
and AcademicYear  = 2023 
GROUP BY gp.ID ;