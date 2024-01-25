SELECT 
	bi.ID,
	bi.FirstName, 
	bi.Surname, 
	bi.PrivateEmail,
	bi.StudyType, 
	s.Name, 
	p.ProgramName,
	CASE 
		when bi.StudyType = 'Fulltime' and p.ProgramName like '%-ft-%' then 'Correct'
		when bi.StudyType = 'Distance' and p.ProgramName like '%-de-%' then 'Correct'
		else 'WRONG'
	END AS CheckCorrectness	
from `basic-information` bi 
inner join `student-study-link` ssl2 on bi.ID = ssl2.StudentID
inner join study s on s.ID = ssl2.StudyID
inner join `study-program-link` spl on spl.StudyID = s.ID 
inner join programmes p on p.ID = spl.ProgramID 
join applicants a on bi.ID = a.StudentID 
where a.Progress = "Accepted" and LENGTH(bi.ID) > 7
GROUP by bi.ID;