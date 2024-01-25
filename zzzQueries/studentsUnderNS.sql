SELECT
	bi.FirstName,
	bi.Surname,
	bi.ID,
	s.Name as "Programme Name",
	p.ProgramName,
	s.ShortName as "Programme Code",
	s.StudyType as "Mode Of Study",
	s2.Name as "School",
	bi.FirstName as "First Name",
	bi.Surname as "Last Name"
FROM edurole.study as s
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID
inner join `basic-information` bi on bi.ID =  ssl2.StudentID 
inner join `course-electives` ce on ce.StudentID  = bi.ID and ce.`Year` = 2024
inner join `program-course-link` pcl on pcl.CourseID = ce.CourseID
inner join courses c on c.ID = ce.CourseID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
inner join schools s2 on s.ParentID = s2.ID 
where c.Name  in ('PHY101','MAT101','BIO101','CHM101')
-- AND p.ProgramName like "dip%"
and LENGTH(bi.ID) >= 7
Group By bi.ID;