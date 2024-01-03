#NON 230
SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    bi.Sex,
    s.ShortName,
    s.Name,
    ((program.YEAR2 * 0) + (program.YEAR1 * 0)) AS "Previous Years Invoice",
    ((program.YEAR2 * 0) + program.YEAR1) AS "Total Invoice inclusive 2023",
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
    END AS "Year Of Study",
    CASE 
        WHEN g.StudentNo IS NOT NULL THEN
            CASE 
                WHEN MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) >= 1 THEN
                    CONCAT('YEAR ', LEFT(CAST(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) AS CHAR), LENGTH(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED))) - 2))
                ELSE
                    'No Year Found'
            END
        ELSE 'NO RESULTS'
    END AS "Most Recent Exam Results"
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
LEFT JOIN courses c ON ce.CourseID = c.ID
LEFT JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
LEFT JOIN programmes p ON p.ID = pcl.ProgramID
LEFT JOIN `grades` AS g ON g.StudentNo = bi.ID
INNER  JOIN (
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
    bi.StudyType = 'Fulltime'
    AND bi.ID LIKE '230%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID;

#################################################
#natural science QUERY
WITH ProgramData AS (
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
)
SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.GovernmentID,
    bi.ID,
    bi.StudyType,
    bi.Sex,
    s.ShortName,
    s.Name,
    s2.Description,
    ((program.YEAR2 * 0) + (program.YEAR1 * 0)) AS "Previous Years Invoice",
    ((program.YEAR2 * 0) + program.YEAR1) AS "Total Invoice inclusive 2023",
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
    END AS "Year Of Study",
    CASE 
        WHEN g.StudentNo IS NOT NULL THEN
            CASE 
                WHEN MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) >= 1 THEN
                    CONCAT('YEAR ', LEFT(CAST(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) AS CHAR), LENGTH(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED))) - 2))
                ELSE
                    'No Year Found'
            END
        ELSE 'NO RESULTS'
    END AS "Most Recent Exam Results"
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
INNER JOIN schools s2 ON s.ParentID = s2.ID 
LEFT JOIN courses c ON ce.CourseID = c.ID
LEFT JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
LEFT JOIN programmes p ON p.ID = pcl.ProgramID
LEFT JOIN `grades` AS g ON g.StudentNo = bi.ID
INNER JOIN ProgramData program ON p.ProgramName LIKE CONCAT(program.ProgrammeCode, '%')
WHERE
    bi.StudyType = 'Fulltime'
    AND bi.ID LIKE '230%'
    AND LENGTH(bi.ID) >= 4
    AND s.ShortName = "NS"
GROUP BY
    bi.ID;

#SAGE

SELECT 
    cl.DCLink, 
    cl.Account, 
    cl.Name,
    pa.Debit,
    pa.Credit,
    pa.TxDate,
    pa.Description AS TransactionDescription,
    SUM(pa.Debit) AS TotalInvoice,
    SUM(pa.Credit) AS TotalPayment,
    ((SUM(pa.Debit)) - (SUM(pa.Credit))) as OutstandingBalance,
    COUNT(CASE WHEN pa.Credit > 0 THEN 1 END) AS NumberOfTransactions,    
    COUNT(CASE WHEN pa.Debit > 0 THEN 1 END) AS NumberOfInvoices,
    SUM(CASE WHEN pa.Credit > 0 THEN 1 ELSE 0 END) as TotalNumberOfTransactions,
    SUM(CASE WHEN pa.Debit > 0 THEN 1 ELSE 0 END) as TotalNumberOfInvoices
FROM 
    LMMU_Live.dbo.Client cl 
INNER JOIN 
    LMMU_Live.dbo.PostAR pa ON pa.AccountLink = cl.DCLink
GROUP BY
    cl.DCLink, 
    cl.Account, 
    cl.Name,
    pa.TxDate,
    pa.Debit,
    pa.Credit,
    pa.Description;
   
  SELECT Description, InvTotExclDEx 
FROM LMMU_Live.dbo.InvNum;

 
SELECT 
    cl.DCLink, 
    cl.Account, 
    cl.Name,
    SUM(pa.Debit) AS Invoice,
    SUM(pa.Credit) AS TotalPayment,
    ((SUM(pa.Debit)) - (SUM(pa.Credit))) as OutstandingBalance,
    COUNT(CASE WHEN pa.Credit > 0 THEN 1 END) AS NumberOfTransactions,
    COUNT(CASE WHEN pa.Debit > 0 THEN 1 END) AS NumberOfInvoices
FROM 
    LMMU_Live.dbo.Client cl 
INNER JOIN 
    LMMU_Live.dbo.PostAR pa ON pa.AccountLink = cl.DCLink
GROUP BY 
    cl.DCLink, 
    cl.Account, 
    cl.Name;