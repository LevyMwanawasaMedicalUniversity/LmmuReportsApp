SELECT bi.FirstName,
	bi.Surname,	 
	bi.ID, 
	s.Name as "Programme",
	s2.Name as "School",
	sg.StudentID as "NRC",
	bi.PrivateEmail,
	bi.MobilePhone,
	sg.Grade, 
	sg.GradePoints
FROM edurole.`subject-grades` sg
INNER JOIN `basic-information` bi ON bi.GovernmentID = sg.StudentID
inner JOIN  `student-study-link` ssl2 on bi.ID  = ssl2.StudentID 
inner join study s on ssl2.StudyID = s.ID 
inner join schools s2 on s2.ID = s.ParentID 
WHERE sg.Grade REGEXP '^[^0-9]+$'
GROUP BY sg.StudentID;
