SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    s.Name,
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS "Registration Status",
    CASE 
    	WHEN bi.ID LIKE "230%" then program.YEAR1
    	ELSE program.YEAR2
    END as "2023 Invoice"
    
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
    UNION ALL SELECT 'BScCA', 19567, 19317
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
    UNION ALL SELECT 'Bpharm', 19567, 19317
    UNION ALL SELECT 'BPHY', 19567, 19317
    UNION ALL SELECT 'BScBMS', 22400, 22150
    UNION ALL SELECT 'BScND', 19567, 19317
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
    UNION ALL SELECT 'BScRAD', 19567, 19317
    UNION ALL SELECT 'BScSLT', 19567, 19317
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
    bi.StudyType = 'Distance'
    AND 
 	(bi.ID LIKE '210%'
	OR bi.ID LIKE '220%'
    OR bi.ID LIKE '230%')
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
	(`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    s.Name,
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS "Registration Status",
    CASE 
    	WHEN bi.ID LIKE "230%" then program.YEAR1
    	ELSE program.YEAR2
    END as "2023 Invoice"
    
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
) AS program ON s.ShortName = program.ProgrammeCode
WHERE
    bi.StudyType = 'Distance'
    AND 
 	(bi.ID LIKE '210%'
	OR bi.ID LIKE '220%'
    OR bi.ID LIKE '230%')
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
	(`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);
	
	


SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    s.Name,
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS "Registration Status",
    CASE 
    	WHEN bi.ID LIKE "230%" then program.YEAR1
    	ELSE program.YEAR2
    END as "2023 Invoice"
    
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
        'BScEH' AS ProgrammeCode, 15770 AS YEAR1, 15225 AS YEAR2
    UNION ALL SELECT 'BScPH', 15770, 15225
    UNION ALL SELECT 'CertCHAHM', 7170, 7170
    UNION ALL SELECT 'CertDA', 7170, 7170
    UNION ALL SELECT 'CertEMC', 7170, 7170
    UNION ALL SELECT 'BScPHN', 15770, 15225
    UNION ALL SELECT 'BScBMS', 21470, 20925
    UNION ALL SELECT 'BScEH', 15770, 15225
    UNION ALL SELECT 'DipEH', 11670, 11125
    UNION ALL SELECT 'BScND', 18637, 18092
    UNION ALL SELECT 'BScCA', 18637, 18092
    UNION ALL SELECT 'BScOPT', 18637, 18092
    UNION ALL SELECT 'BScCO', 18637, 18092
    UNION ALL SELECT 'BScCS', 18637, 18092
    UNION ALL SELECT 'BAGC', 15550, 12350
    UNION ALL SELECT 'DipGC', 11450, 11450
    UNION ALL SELECT 'BScNUR', 18773, 18092
    UNION ALL SELECT 'BScON', 18773, 18092
    UNION ALL SELECT 'DipMID', 11806, 11125
    UNION ALL SELECT 'BScPHNUR', 15906, 15225
    UNION ALL SELECT 'BScMHN', 18773, 18092
    UNION ALL SELECT 'BScNUR', 18773, 18092
    UNION ALL SELECT 'BScON', 18773, 18092
    UNION ALL SELECT 'BScMID', 18773, 18092
    UNION ALL SELECT 'BScMHCP', 18637 , 18092
    UNION ALL SELECT 'DipMID', 11806, 11125
    UNION ALL SELECT 'MBCHB', 28770, 28225
    UNION ALL SELECT 'MScHPE', 16625, 16625
    UNION ALL SELECT 'PHdHPE', 17625, 17625
    UNION ALL SELECT 'DipPHN', 11806, 11125
    UNION ALL SELECT 'DipRN', 11806, 11125
    UNION ALL SELECT 'DipMHN', 11806, 11125
    UNION ALL SELECT 'BScBMS', 21470, 20925
    UNION ALL SELECT 'DipBMS', 13670, 13125
    UNION ALL SELECT 'DipCMSG', 11670, 11125
    UNION ALL SELECT 'DipCMSP', 11670, 11125
    UNION ALL SELECT 'DipDTECH', 11670, 11125
    UNION ALL SELECT 'DipDTH', 11670, 11125
    UNION ALL SELECT 'DipOPT', 11670, 11125
    UNION ALL SELECT 'DipPH', 11670, 11125
    UNION ALL SELECT 'DipEMC', 11670, 11125
    UNION ALL SELECT 'DiGC', 11450, 11450
)  AS program ON s.ShortName = program.ProgrammeCode
WHERE
    bi.StudyType = 'Fulltime'
    AND 
 	(bi.ID LIKE '190%')
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
	(`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);
	


SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    s.Name,
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS "Registration Status",
    CASE 
    	WHEN bi.ID LIKE "230%" then program.YEAR1
    	ELSE program.YEAR2
    END as "2023 Invoice"
    
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
        'BScEH' AS ProgrammeCode, 13470 AS YEAR1, 10925 AS YEAR2
    UNION ALL SELECT 'BScPH', 13470, 10925
    UNION ALL SELECT 'BScPHN', 12400, 12150
    UNION ALL SELECT 'DipCMSG', 9670 , 9125
    UNION ALL SELECT 'DipEH', 9670 , 9125    
)  AS program ON s.ShortName = program.ProgrammeCode
WHERE
    bi.StudyType = 'Distance'
    AND 
 	(bi.ID LIKE '190%')
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
	(`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);
	


