
   
SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    s.ShortName,
    s.Name,
    program.YEAR1 AS "Invoice",
    program.YEAR1 AS " Total Invoice",
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS "Registration Status",
    CASE 
        WHEN p.ProgramName LIKE '%y1' THEN 'YEAR 1'
        WHEN p.ProgramName LIKE '%y2' THEN 'YEAR 2'
        WHEN p.ProgramName LIKE '%y3' THEN 'YEAR 3'
        WHEN p.ProgramName LIKE '%y4' THEN 'YEAR 4'
        WHEN p.ProgramName LIKE '%y5' THEN 'YEAR 5'
        WHEN p.ProgramName LIKE '%y6' THEN 'YEAR 6'
        ELSE 'NO REGISTRATION'
    END AS "Year Of Study"
FROM
    edurole.`basic-information` bi
INNER JOIN `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
LEFT JOIN `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2023
LEFT JOIN courses c ON ce.CourseID = c.ID
LEFT JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
LEFT JOIN programmes p ON p.ID = pcl.ProgramID
INNER JOIN study s ON s.ID = ssl2.StudyID
INNER JOIN (
    SELECT
        'BScEH' AS ProgrammeCode, 12400 AS YEAR1, 12150 AS YEAR2
    UNION ALL SELECT 'BScPH', 12400, 12150
    UNION ALL SELECT 'BScPHN', 12400, 12150
    UNION ALL SELECT 'BScMHCP', 12400, 12150
    UNION ALL SELECT 'BScND', 12400, 12150
    UNION ALL SELECT 'BAGC', 12350, 12350
    UNION ALL SELECT 'BPHY', 11350, 11100
    UNION ALL SELECT 'BScRAD', 11350, 11100
    UNION ALL SELECT 'BScNUR', 12506, 12150
    UNION ALL SELECT 'BScPHNUR', 12506, 12150
    UNION ALL SELECT 'BScMID', 12506, 12150
    UNION ALL SELECT 'BScMHN', 12506, 12150
    UNION ALL SELECT 'DipCMSG', 10570 , 10350
    UNION ALL SELECT 'DipCMSP', 10570 , 10350
    UNION ALL SELECT 'DipCMSP', 14706 , 14706
    UNION ALL SELECT 'DipHNP', 14706 , 14706
    UNION ALL SELECT 'MPH', 14625  , 14625
    UNION ALL SELECT 'MScID', 16575  , 16575    
    UNION ALL SELECT 'BScBMS', 12400, 12150
) AS program ON SUBSTRING_INDEX(p.ProgramName, '-', 1) = program.ProgrammeCode
WHERE
    bi.StudyType = 'dISTANCE'
    AND s.Name = "Natural Science"
GROUP BY
    bi.ID;

SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    s.ShortName,
    s.Name,
    NumberOfPayments,
    TotalPayments AS 'Total Payments',
    TotalPayments - (program.YEAR1 * 0.25) AS Balance,
    CASE
        WHEN (TotalPayments) > (program.YEAR1 * 0.25) THEN 'ELIGIBLE TO REGISTER'
        ELSE 'INELIGIBLE TO REGISTER'
    END AS Eligibility,
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS "Registration Status"
FROM
    edurole.`basic-information` bi
INNER JOIN (
    SELECT
        StudentID, 
        COUNT(TransactionID) AS NumberOfPayments,
        SUM(Amount) AS TotalPayments
    FROM
        transactions
    GROUP BY
        StudentID
) AS t ON t.StudentID = bi.ID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
LEFT JOIN `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2023
INNER JOIN study s ON s.ID = ssl2.StudyID
INNER JOIN (
    SELECT
        'AdvDipCA' AS ProgrammeCode, 14570 AS YEAR1, 14350 AS YEAR2
    UNION ALL SELECT 'AdvDipCO', 14570, 14350
    UNION ALL SELECT 'AdvDipONC', 11481, 11481
    UNION ALL SELECT 'AdvDipOPHNUR', 14706, 14350
    UNION ALL SELECT 'BAGC', 12350, 12350
    UNION ALL SELECT 'BScCA', 19576, 19317
    UNION ALL SELECT 'BDS', 29700, 29450
    UNION ALL SELECT 'MBChB', 29700, 29450
    UNION ALL SELECT 'MMEDGS', 22625, 22625
    UNION ALL SELECT 'MMEDID', 22625, 22625
    UNION ALL SELECT 'MMEDIM', 22625, 22625
    UNION ALL SELECT 'MMEDOB', 22625, 22625
    UNION ALL SELECT 'MMEDPCH', 22625, 22625
    UNION ALL SELECT 'MSCFE', 32950, 32950
    UNION ALL SELECT 'MSCHPE', 16625, 16625
    UNION ALL SELECT 'MSCOPTH', 19625, 19625
    UNION ALL SELECT 'MSCOPTO', 19625, 19625
    UNION ALL SELECT 'PDDIPEMC', 16625, 16625
    UNION ALL SELECT 'PHDHPE', 17625 , 17625 
    UNION ALL SELECT 'Bpharm', 19576, 19317
    UNION ALL SELECT 'BPHY', 19576, 19317
    UNION ALL SELECT 'BScBMS', 22400, 22150
    UNION ALL SELECT 'BScND', 19576, 19317
    UNION ALL SELECT 'BScCO', 19567, 19317
    UNION ALL SELECT 'BScCS', 19567, 19317
    UNION ALL SELECT 'BScMHCP', 19567, 19317
    UNION ALL SELECT 'BScMiD', 19673, 19317
    UNION ALL SELECT 'BScNUR', 19673, 19317
    UNION ALL SELECT 'BScON', 19673, 19317
    UNION ALL SELECT 'BScOPT', 19567, 19317
    UNION ALL SELECT 'BScPH', 16700, 16450
    UNION ALL SELECT 'BScPHNUR', 16806, 16450
    UNION ALL SELECT 'BScPHN', 16700, 16450
    UNION ALL SELECT 'BScRAD', 19576, 19317
    UNION ALL SELECT 'BScSLT', 19576, 19317
    UNION ALL SELECT 'BScEH', 16700, 16450
    UNION ALL SELECT 'BScMHN', 19673, 19317
    UNION ALL SELECT 'CertCHAHM', 8040, 8040
    UNION ALL SELECT 'CertHCHW', 8040, 8040
    UNION ALL SELECT 'CertDA', 8040, 8040
    UNION ALL SELECT 'CertEMC', 8040, 8040
    UNION ALL SELECT 'DipBMS', 14570, 14350
    UNION ALL SELECT 'DipCMSG', 12570, 12350
    UNION ALL SELECT 'DipCMSP', 12350, 12350
    UNION ALL SELECT 'DipDTech', 12570, 12350
    UNION ALL SELECT 'DipDTh', 12570, 12350
    UNION ALL SELECT 'DipEMC', 12570, 12350
    UNION ALL SELECT 'DipEH', 12570, 12350
    UNION ALL SELECT 'DipGC', 12350, 12350    
    UNION ALL SELECT 'DipMHN', 12706, 12350
    UNION ALL SELECT 'DipMID', 12706, 12350
    UNION ALL SELECT 'DipOPT', 12570, 12350
    UNION ALL SELECT 'DipPH', 12570, 12350
    UNION ALL SELECT 'DipPHN', 12706, 12350
    UNION ALL SELECT 'DipRN', 12706, 12350
    UNION ALL SELECT 'DipONC', 12706, 12350
    UNION ALL SELECT 'DIPPO', 12570, 12350
) AS program ON s.ShortName = program.ProgrammeCode
WHERE
    bi.StudyType = 'Fulltime'
    AND bi.ID LIKE '230%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID;


   
SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    s.ShortName,
    s.Name,
    program.YEAR2 AS "Invoice",
    (program.YEAR1 + program.YEAR2 * 3) AS " Total Invoice",
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS "Registration Status",
    CASE 
        WHEN p.ProgramName LIKE '%y1' THEN 'YEAR 1'
        WHEN p.ProgramName LIKE '%y2' THEN 'YEAR 2'
        WHEN p.ProgramName LIKE '%y3' THEN 'YEAR 3'
        WHEN p.ProgramName LIKE '%y4' THEN 'YEAR 4'
        WHEN p.ProgramName LIKE '%y5' THEN 'YEAR 5'
        WHEN p.ProgramName LIKE '%y6' THEN 'YEAR 6'
        ELSE 'NO REGISTRATION'
    END AS "Year Of Study"
FROM
    edurole.`basic-information` bi
INNER JOIN `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
LEFT JOIN `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2023
LEFT JOIN courses c ON ce.CourseID = c.ID
LEFT JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
LEFT JOIN programmes p ON p.ID = pcl.ProgramID
INNER JOIN study s ON s.ID = ssl2.StudyID
INNER JOIN (
    SELECT
        'BScEH' AS ProgrammeCode, 13470 AS YEAR1, 10925 AS YEAR2
    UNION ALL SELECT 'BScPH', 13470, 10925
    UNION ALL SELECT 'BScPHN', 12400, 12150
    UNION ALL SELECT 'DipCMSG', 9670 , 9125
    UNION ALL SELECT 'DipEH', 9670 , 9125    
) AS program ON s.ShortName = program.ProgrammeCode
WHERE
    bi.StudyType = 'Distance'
    AND bi.ID LIKE "190%"
GROUP BY
    bi.ID;