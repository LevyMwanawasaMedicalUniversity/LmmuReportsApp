SELECT 
	bi.FirstName, 
	bi.Surname,  
	gp.`user`, 
	gp.userdate, 
	gp.usertime, 
	gp.StudentNo, 
	gp.AcademicYear, 
	gp.CourseNo, 
	gp.CAMarks, 
	gp.ExamMarks, 
	gp.TotalMarks, 
	gp.Grade
FROM edurole.`grades-published` gp
inner join `basic-information` bi ON bi.ID =  gp.`user`
where gp.CourseNo = 'GIT301' AND gp.AcademicYear = 2023;