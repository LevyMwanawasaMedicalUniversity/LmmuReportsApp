//////210, 220, 230 
SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    p.ProgramName as "Registration Code",
    bi.StudyType as "Mode Of Study",
    s.Name as "Programme Name",
    CASE 
        WHEN s.Name = "Natural Science" THEN "NS"
        ELSE  s2.Name
    END AS "School",
    CASE 
        WHEN ce.Approved = 1 THEN "Courses Approved"
        ELSE 'Courses Not Approved'
    END AS "Course Approval",
    CASE 
        WHEN bi.ID LIKE '230%' THEN program.YEAR1
        ELSE program.YEAR2
    END AS "Invoice 2023",
    CASE 
        WHEN bi.ID LIKE '230%' THEN program.YEAR1
        WHEN bi.ID LIKE '220%' THEN (program.YEAR1 + program.YEAR2)
        WHEN bi.ID LIKE '210%' THEN (program.YEAR1 + (program.YEAR2*2))
        WHEN bi.ID LIKE '190%' THEN (program.YEAR1 + (program.YEAR2*3))        
        ELSE program.YEAR2
    END AS "Total Invoice",    
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
INNER JOIN `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2023
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
INNER JOIN study s ON s.ID = ssl2.StudyID
INNER JOIN schools s2 ON s.ParentID  = s2.ID
INNER JOIN (
    SELECT
        'AdvDipCA-FT' AS ProgrammeCode, 14570 AS YEAR1, 14350 AS YEAR2
    UNION ALL SELECT 'AdvDipCO-FT', 14570, 14350
    UNION ALL SELECT 'AdvDipONC-FT', 11481, 11481
    UNION ALL SELECT 'AdvDipOPHNUR-FT', 14706, 14350
    UNION ALL SELECT 'BAGC-FT', 12350, 12350
    UNION ALL SELECT 'BScCA-FT', 19567, 19317
    UNION ALL SELECT 'BDS-FT', 29700, 29450
    UNION ALL SELECT 'MBChB-FT', 29700, 29450
    UNION ALL SELECT 'MMEDGS-FT', 22625, 22625
    UNION ALL SELECT 'MMEDID-FT', 22625, 22625
    UNION ALL SELECT 'MMEDIM-FT', 22625, 22625
    UNION ALL SELECT 'MMEDOB-FT', 22625, 22625
    UNION ALL SELECT 'MMEDPCH-FT', 22625, 22625
    UNION ALL SELECT 'MSCFE-FT', 32950, 32950
    UNION ALL SELECT 'MSCHPE-FT', 16625, 16625
    UNION ALL SELECT 'MSCOPTH-FT', 19625, 19625
    UNION ALL SELECT 'MSCOPTO-FT', 19625, 19625
    UNION ALL SELECT 'PDDIPEMC-FT', 16625, 16625
    UNION ALL SELECT 'PHDHPE-FT', 17625 , 17625 
    UNION ALL SELECT 'Bpharm-FT', 19567, 19317
    UNION ALL SELECT 'BPHY-FT', 19567, 19317
    UNION ALL SELECT 'BScBMS-FT', 22400, 22150
    UNION ALL SELECT 'BScND-FT', 19567, 19317
    UNION ALL SELECT 'BScCO-FT', 19567, 19317
    UNION ALL SELECT 'BScCS-FT', 19567, 19317
    UNION ALL SELECT 'BScMHCP-FT', 19567, 19317
    UNION ALL SELECT 'BScMiD-FT', 19673, 19317
    UNION ALL SELECT 'BScNUR-FT', 19673, 19317
    UNION ALL SELECT 'BScON-FT', 19673, 19317
    UNION ALL SELECT 'BScOPT-FT', 19567, 19317
    UNION ALL SELECT 'BScPH-FT', 16700, 16450
    UNION ALL SELECT 'BScPHNUR-FT', 16806, 16450
    UNION ALL SELECT 'BScPHN-FT', 16700, 16450
    UNION ALL SELECT 'BScRAD-FT', 19567, 19317
    UNION ALL SELECT 'BScSLT-FT', 19567, 19317
    UNION ALL SELECT 'BScEH-FT', 16700, 16450
    UNION ALL SELECT 'BScMHN-FT', 19673, 19317
    UNION ALL SELECT 'CertCHAHM-FT', 8040, 8040
    UNION ALL SELECT 'CertHCHW-FT', 8040, 8040
    UNION ALL SELECT 'CertDA-FT', 8040, 8040
    UNION ALL SELECT 'CertEMC-FT', 8040, 8040
    UNION ALL SELECT 'DipBMS-FT', 14570, 14350
    UNION ALL SELECT 'DipCMSG-FT', 12570, 12350
    UNION ALL SELECT 'DipCMSP-FT', 12350, 12350
    UNION ALL SELECT 'DipDTech-FT', 12570, 12350
    UNION ALL SELECT 'DipDTh-FT', 12570, 12350
    UNION ALL SELECT 'DipEMC-FT', 12570, 12350
    UNION ALL SELECT 'DipEH-FT', 12570, 12350
    UNION ALL SELECT 'DipGC-FT', 12350, 12350    
    UNION ALL SELECT 'DipMHN-FT', 12706, 12350
    UNION ALL SELECT 'DipMID-FT', 12706, 12350
    UNION ALL SELECT 'DipOPT-FT', 12570, 12350
    UNION ALL SELECT 'DipPH-FT', 12570, 12350
    UNION ALL SELECT 'DipPHN-FT', 12706, 12350
    UNION ALL SELECT 'DipRN-FT', 12706, 12350
    UNION ALL SELECT 'DipONC-FT', 12706, 12350
    UNION ALL SELECT 'DIPPO-FT', 12570, 12350
    UNION ALL SELECT 'BScPH-DE', 12400, 12150
    UNION ALL SELECT 'BScPHN-DE', 12400, 12150
    UNION ALL SELECT 'BScMHCP-DE', 12400, 12150
    UNION ALL SELECT 'BScND-DE', 12400, 12150
    UNION ALL SELECT 'BAGC-DE', 12350, 12350
    UNION ALL SELECT 'BPHY-DE', 11350, 11100
    UNION ALL SELECT 'BScRAD-DE', 11350, 11100
    UNION ALL SELECT 'BScNUR-DE', 12506, 12150
    UNION ALL SELECT 'BScPHNUR-DE', 12506, 12150
    UNION ALL SELECT 'BScMID-DE', 12506, 12150
    UNION ALL SELECT 'BScMHN-DE', 12506, 12150
    UNION ALL SELECT 'DipCMSG-DE', 10570 , 10350
    UNION ALL SELECT 'DipCMSP-DE', 10570 , 10350
    UNION ALL SELECT 'DipHNP-DE', 14706 , 14706
    UNION ALL SELECT 'MPH-DE', 14625  , 14625
    UNION ALL SELECT 'MScID-DE', 16575  , 16575    
    UNION ALL SELECT 'BScBMS-DE', 12400, 12150
    UNION ALL SELECT 'BScEH-DE', 12400, 12150
) AS program ON SUBSTRING_INDEX(p.ProgramName, '-', 2) = program.ProgrammeCode
WHERE
 	bi.ID LIKE '210%'
	OR bi.ID LIKE '220%'
    OR bi.ID LIKE '230%'
GROUP BY
    bi.ID;

/////////190
SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    p.ProgramName as "Registration Code",
    bi.StudyType as "Mode Of Study",
    s.Name as "Programme Name",
    CASE 
        WHEN s.Name = "Natural Science" THEN "NS"
        ELSE  s2.Name
    END AS "School",
    CASE 
        WHEN ce.Approved = 1 THEN "Courses Approved"
        ELSE 'Courses Not Approved'
    END AS "Course Approval",
    CASE 
        WHEN bi.ID LIKE '230%' THEN program.YEAR1
        ELSE program.YEAR2
    END AS "Invoice 2023",
    CASE 
        WHEN bi.ID LIKE '230%' THEN program.YEAR1
        WHEN bi.ID LIKE '220%' THEN (program.YEAR1 + program.YEAR2)
        WHEN bi.ID LIKE '210%' THEN (program.YEAR1 + (program.YEAR2*2))
        WHEN bi.ID LIKE '190%' THEN (program.YEAR1 + (program.YEAR2*3))        
        ELSE program.YEAR2
    END AS "Total Invoice",    
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
INNER JOIN `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2023
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
INNER JOIN study s ON s.ID = ssl2.StudyID
INNER JOIN schools s2 ON s.ParentID  = s2.ID
INNER JOIN (
    SELECT
        'BScEH-FT' AS ProgrammeCode, 15770 AS YEAR1, 15225 AS YEAR2
    UNION ALL SELECT 'BScPH-FT', 15770, 15225
    UNION ALL SELECT 'CertCHAHM-FT', 7170, 7170
    UNION ALL SELECT 'CertDA-FT', 7170, 7170
    UNION ALL SELECT 'CertEMC-FT', 7170, 7170
    UNION ALL SELECT 'BScPHN-FT', 15770, 15225
    UNION ALL SELECT 'BScBMS-FT', 21470, 20925
    UNION ALL SELECT 'DipEH-FT', 11670, 11125
    UNION ALL SELECT 'BScND-FT', 18637, 18092
    UNION ALL SELECT 'BScCA-FT', 18637, 18092
    UNION ALL SELECT 'BScOPT-FT', 18637, 18092
    UNION ALL SELECT 'BScCO-FT', 18637, 18092
    UNION ALL SELECT 'BScCS-FT', 18637, 18092
    UNION ALL SELECT 'BAGC-FT', 15550, 12350
    UNION ALL SELECT 'DipGC-FT', 11450, 11450
    UNION ALL SELECT 'BScNUR-FT', 18773, 18092
    UNION ALL SELECT 'BScON-FT', 18773, 18092
    UNION ALL SELECT 'DipMID-FT', 11806, 11125
    UNION ALL SELECT 'BScPHNUR-FT', 15906, 15225
    UNION ALL SELECT 'BScMHN-FT', 18773, 18092
    UNION ALL SELECT 'BScMID-FT', 18773, 18092
    UNION ALL SELECT 'BScMHCP-FT', 18637 , 18092
    UNION ALL SELECT 'MBCHB-FT', 28770, 28225
    UNION ALL SELECT 'MScHPE-FT', 16625, 16625
    UNION ALL SELECT 'PHdHPE-FT', 17625, 17625
    UNION ALL SELECT 'DipPHN-FT', 11806, 11125
    UNION ALL SELECT 'DipRN-FT', 11806, 11125
    UNION ALL SELECT 'DipMHN-FT', 11806, 11125
    UNION ALL SELECT 'BScBMS-FT', 21470, 20925
    UNION ALL SELECT 'DipBMS-FT', 13670, 13125
    UNION ALL SELECT 'DipCMSG-FT', 11670, 11125
    UNION ALL SELECT 'DipCMSP-FT', 11670, 11125
    UNION ALL SELECT 'DipDTECH-FT', 11670, 11125
    UNION ALL SELECT 'DipDTH-FT', 11670, 11125
    UNION ALL SELECT 'DipOPT-FT', 11670, 11125
    UNION ALL SELECT 'DipPH-FT', 11670, 11125
    UNION ALL SELECT 'DipEMC-FT', 11670, 11125
    UNION ALL SELECT 'DiGC-FT', 11450, 11450
    UNION ALL SELECT 'BScPH-DE', 13470, 10925
    UNION ALL SELECT 'BScEH-DE', 13470, 10925
    UNION ALL SELECT 'BScPHN-DE', 12400, 12150
    UNION ALL SELECT 'DipCMSG-DE', 9670 , 9125
    UNION ALL SELECT 'DipEH-DE', 9670 , 9125  
) AS program ON SUBSTRING_INDEX(p.ProgramName, '-', 2) = program.ProgrammeCode
WHERE
 	bi.ID LIKE '190%'
GROUP BY
    bi.ID;
    
//////////ACCOUNTS qUERY///////////////////
WITH LatestInvoiceDates AS (
    SELECT 
        pa.AccountLink,
        MAX(pa.TxDate) AS LatestTxDate
    FROM 
        LMMU_Live.dbo.PostAR pa
    WHERE 
        pa.Description LIKE '%-%-%' AND pa.Debit > 0
    GROUP BY 
        pa.AccountLink
)
SELECT 
    cl.DCLink, 
    cl.Account, 
    cl.Name,
    SUM(CASE WHEN pa.Description NOT LIKE '%reversal%' THEN pa.Credit ELSE 0 END) AS TotalPayment,
    CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN 'Invoiced' ELSE 'Not Invoiced' END AS "2023 Invoice Status",
    CONVERT(VARCHAR, lid.LatestTxDate, 23) AS LatestInvoiceDate
FROM 
    LMMU_Live.dbo.Client cl 
INNER JOIN 
    LMMU_Live.dbo.PostAR pa ON pa.AccountLink = cl.DCLink
LEFT JOIN 
    LatestInvoiceDates lid ON pa.AccountLink = lid.AccountLink
GROUP BY 
    cl.DCLink, 
    cl.Account, 
    cl.Name,
    CONVERT(VARCHAR, lid.LatestTxDate, 23),
    CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN 'Invoiced' ELSE 'Not Invoiced' END; 

    
///////////////////////2023 payments only

   
WITH LatestInvoiceDates AS (
    SELECT 
        pa.AccountLink,
        MAX(pa.TxDate) AS LatestTxDate
    FROM 
        LMMU_Live.dbo.PostAR pa
    WHERE 
        pa.Description LIKE '%-%-%' AND pa.Debit > 0
    GROUP BY 
        pa.AccountLink
)
SELECT 
    cl.DCLink, 
    cl.Account, 
    cl.Name,
    SUM(CASE 
            WHEN pa.Description  LIKE '%reversal%' THEN 0 
            WHEN pa.TxDate < '2023-01-01' THEN 0 
            ELSE pa.Credit 
            END) AS TotalPayment2023,
    CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN 'Invoiced' ELSE 'Not Invoiced' END AS "2023 Invoice Status",
    CONVERT(VARCHAR, lid.LatestTxDate, 23) AS LatestInvoiceDate
FROM 
    LMMU_Live.dbo.Client cl 
INNER JOIN 
    LMMU_Live.dbo.PostAR pa ON pa.AccountLink = cl.DCLink
LEFT JOIN 
    LatestInvoiceDates lid ON pa.AccountLink = lid.AccountLink
WHERE 
    pa.TxDate > '2023-11-06'  -- Add this condition to filter TxDate
GROUP BY 
    cl.DCLink, 
    cl.Account, 
    cl.Name,
    CONVERT(VARCHAR, lid.LatestTxDate, 23),
    CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN 'Invoiced' ELSE 'Not Invoiced' END; 

    //////////////////////LATES TRANSACTION DATE//////////////////////////
WITH LatestInvoiceDates AS (
    SELECT 
        pa.AccountLink,
        MAX(pa.TxDate) AS LatestTxDate
    FROM 
        LMMU_Live.dbo.PostAR pa
    WHERE 
        pa.Description LIKE '%-%-%' AND pa.Debit > 0
    GROUP BY 
        pa.AccountLink
)
SELECT 
    cl.DCLink, 
    cl.Account, 
    cl.Name,
    MAX(CASE WHEN pa.Credit > 0 THEN pa.TxDate END) AS LatestTransactionDate, -- Modified this line
    SUM(CASE 
	    WHEN pa.Description LIKE '%reversal%' THEN 0 
	    WHEN pa.TxDate < '2023-01-01' THEN 0 
	    ELSE pa.Credit 
	    END) AS TotalPayment2023,
    CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN 'Invoiced' ELSE 'Not Invoiced' END AS "2023 Invoice Status",
    CONVERT(VARCHAR, lid.LatestTxDate, 23) AS LatestInvoiceDate
FROM 
    LMMU_Live.dbo.Client cl 
INNER JOIN 
    LMMU_Live.dbo.PostAR pa ON pa.AccountLink = cl.DCLink
LEFT JOIN 
    LatestInvoiceDates lid ON pa.AccountLink = lid.AccountLink

GROUP BY 
    cl.DCLink, 
    cl.Account, 
    cl.Name,
    CONVERT(VARCHAR, lid.LatestTxDate, 23),
    CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN 'Invoiced' ELSE 'Not Invoiced' END
HAVING 
    MAX(CASE WHEN pa.Credit > 0 THEN pa.TxDate END) > '2023-11-08';