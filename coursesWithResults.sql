
SELECT
    c.Name AS "Course Code",
    c.CourseDescription AS "Course Name",
    p.ProgramName,
    s.Name as "Programme",
    s2.Name as "School",
    bi.FirstName as "First Name",
    bi.Surname  as "Last Name",
    CASE
        WHEN (COUNT(CASE WHEN g.AcademicYear = 2023 THEN g.CAMarks END) = 0) THEN 'No Results'
        ELSE 'Results Found'
    END AS "Unpublished Results"
--     CASE
--         WHEN (COUNT(CASE WHEN gp.AcademicYear  = 2023 THEN gp.CAMarks END) = 0) THEN 'No Results'
--         ELSE 'Results Found'
--     END AS "Published Status"
FROM courses c
LEFT JOIN edurole.grades g ON c.Name = g.CourseNo AND g.AcademicYear = 2023
-- LEFT JOIN `grades-published` gp ON c.Name = gp.CourseNo AND gp.AcademicYear = 2023
INNER JOIN `program-course-link` pcl ON c.ID = pcl.CourseID
INNER JOIN programmes p ON pcl.ProgramID = p.ID
INNER JOIN `study-program-link` spl  ON p.ID = spl.ProgramID 
INNER JOIN study s ON spl.StudyID = s.ID
INNER JOIN schools s2 ON s.ParentID = s2.ID 
INNER JOIN `basic-information` bi on bi.ID = s.ProgrammesAvailable 
GROUP BY p.ProgramName,c.Name;