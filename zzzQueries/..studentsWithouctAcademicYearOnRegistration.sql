SELECT *
FROM edurole.`course-electives` WHERE `Year` IS NULL
AND EnrolmentDate > '2024-01-01'
and StudentID = 230300409;

UPDATE edurole.`course-electives`
SET `Year` = 2024, Semester = 1
WHERE `Year` IS NULL
AND EnrolmentDate > '2024-01-01'
and StudentID = 230300409;
