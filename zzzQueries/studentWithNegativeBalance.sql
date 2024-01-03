SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.GovernmentID,
    bi.StudyType,
    s.Name,
    b.Amount
FROM
    edurole.`basic-information` bi
inner join balances b on b.StudentID = bi.ID 
inner JOIN `grades-published` gp on bi.ID = gp.StudentNo
INNER JOIN `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
LEFT JOIN `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2023
INNER JOIN study s ON s.ID = ssl2.StudyID
where b.Amount  < 0
GROUP by bi.ID;