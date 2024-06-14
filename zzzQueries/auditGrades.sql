SELECT 
    `grade-modified`.`ID`,
    `grade-modified`.`GradeID`,
    `grade-modified`.`StudentID`,
    `grade-modified`.`CID`,
    `grade-modified`.`CA`,
    `grade-modified`.`Exam`,
    `grade-modified`.`Total`,
    `grade-modified`.`Grade`,
    `grade-modified`.`DateTime`,
    `bi`.`FirstName` as `SubmittedByFirstName`,
    `bi`.`Surname` as `SubmittedBySurname`,
    `bi2`.`FirstName` as `ReviewedByFirstName`,
    `bi2`.`Surname` as `ReviewedBySurname`,
    `bi3`.`FirstName` as `ApprovedByFirstName`,
    `bi3`.`Surname` as `ApprovedBySurname`,
    `grade-modified`.`Type`
FROM `grade-modified`
JOIN `basic-information` as `bi` ON `bi`.`ID` = `grade-modified`.`SubmittedBy`
JOIN `basic-information` as `bi2` ON `bi2`.`ID` = `grade-modified`.`ReviewedBy`
JOIN `basic-information` as `bi3` ON `bi3`.`ID` = `grade-modified`.`ApprovedBy`
ORDER BY `grade-modified`.`DateTime` DESC