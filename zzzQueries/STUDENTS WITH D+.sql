SELECT    
    gp.CourseNo,
    gp.Grade
FROM 
    edurole.`grades-published` gp
INNER JOIN 
    `basic-information` bi ON bi.ID = gp.StudentNo
INNER JOIN 
    `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
INNER JOIN 
    study s ON s.ID = ssl2.StudyID
INNER JOIN 
    schools s2 ON s2.ID = s.ParentID
WHERE 
    gp.Grade in ('D+','NE') AND gp.AcademicYear = 2023
GROUP BY 
    gp.CourseNo;

Select 