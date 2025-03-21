   
SELECT bi.FirstName,
	bi.Surname,	 
	bi.ID, 
	s.Name as "Programme",
	s2.Name as "School",
	sg.StudentID as "NRC",
	bi.PrivateEmail,
	bi.MobilePhone,
	a.Progress,
	a.`DateTime` 
FROM edurole.`subject-grades` sg
INNER JOIN `basic-information` bi ON bi.GovernmentID = sg.StudentID
inner JOIN  `student-study-link` ssl2 on bi.ID  = ssl2.StudentID 
inner join study s on ssl2.StudyID = s.ID 
inner join schools s2 on s2.ID = s.ParentID 
INNER  JOIN applicants a on a.StudentID = bi.ID 
WHERE a.Progress = 'Accepted' and a.`DateTime` > '2024-09-01'
GROUP BY sg.StudentID;