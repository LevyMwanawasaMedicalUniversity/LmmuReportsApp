SELECT bi.FirstName,
	bi.Surname,	 
	bi.ID, 
	s.Name as "Programme",
	s.ShortName,
	s2.Name as "School",
	sg.StudentID as "NRC",
	bi.PrivateEmail,
	bi.MobilePhone,
	sg.Grade, 
	sg.GradePoints,
	a.`DateTime`
FROM edurole.`subject-grades` sg
INNER JOIN `basic-information` bi ON bi.GovernmentID = sg.StudentID
inner JOIN  `student-study-link` ssl2 on bi.ID  = ssl2.StudentID 
INNER JOIN applicants a on bi.ID = a.StudentID 
inner join study s on ssl2.StudyID = s.ID 
inner join schools s2 on s2.ID = s.ParentID 
WHERE s.ShortName in ('BSCMHCP','BSCCS','DIPCMSG','DIPCMSP','BSCMHN','DIPMHN','BSCMHN')
GROUP BY sg.StudentID;
