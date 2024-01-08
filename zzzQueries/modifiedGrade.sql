SELECT 
	gm.ID, 
	gm.GradeID, 
	gm.StudentID, 
	gm.CID, 
	gm.CA, 
	gm.Exam, 
	gm.Total, 
	gm.Grade, 
	gm.`DateTime`,
	bi.FirstName as "Submitted By",
	bi.Surname as "Submitted By",
	bi2.FirstName as "Reviewed By",
	bi2.Surname as "Reviewed By",
	bi3.FirstName as "Approved By",
	bi3.Surname as "Approved By", 
	gm.`Type`
FROM edurole.`grade-modified` gm 
inner join `basic-information` bi on bi.ID = gm.SubmittedBy 
inner join `basic-information` bi2 on bi2.ID = gm.ReviewedBy
inner join `basic-information` bi3 on bi3.ID = gm.ApprovedBy
where CID  = 'GIT301';