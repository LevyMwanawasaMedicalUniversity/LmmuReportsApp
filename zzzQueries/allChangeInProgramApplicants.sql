SELECT FirstName,
	bi.MiddleName, 
	bi.Surname, 
	bi.Sex, 
	bi.ID, 
	bi.GovernmentID,
	s.Name as "Current Programme",
	s3.Name as "Requested Program Change",
	r.Description  as "Reason",
	s.ShortName,
	s2.Name as "School",
	bi.PrivateEmail, 
	bi.MaritalStatus, 
	bi.StudyType, 
	bi.Status,
	r.DateIncident as "Date Submitted"
FROM edurole.`basic-information` bi
INNER JOIN `subject-grades` sg on sg.StudentID = bi.GovernmentID 
inner join records r on r.UserID = bi.ID
inner JOIN  `student-study-link` ssl2 on bi.ID  = ssl2.StudentID
inner join study s on ssl2.StudyID = s.ID 
inner join schools s2 on s2.ID = s.ParentID
inner join study s3 on r.NewValue = s3.ID
WHERE r.Reason  = "Change of Program"
GROUP BY bi.ID;