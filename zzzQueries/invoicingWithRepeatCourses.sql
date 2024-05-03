WITH GradeData AS (
    -- Calculate total and failed courses for each student, considering academic years
    SELECT
        StudentNo,
        AcademicYear,
        COUNT(*) AS TotalCourses,
        SUM(CASE 
            WHEN Grade IN ('NE', 'D', 'D+') THEN 1
            ELSE 0
        END) AS FailedCourses
    FROM
        `grades`
    GROUP BY
        StudentNo,
        AcademicYear
),
BaseInvoice AS (
    -- Determine the base invoice for each student depending on ID pattern
    SELECT
        bi.ID,
        s.Name AS StudyName,
        CASE 
            WHEN bi.ID LIKE '240%' THEN program.YEAR1
            WHEN bi.ID LIKE '230%' THEN program.YEAR1 + program.YEAR2
            WHEN bi.ID LIKE '220%' THEN program.YEAR1 + (program.YEAR2 * 2)
            WHEN bi.ID LIKE '210%' THEN program.YEAR1 + (program.YEAR2 * 3)
            ELSE program.YEAR2
        END AS BaseInvoice,
        SUBSTR(bi.ID, 1, 3) AS IDPrefix
    FROM
        `basic-information` bi
    INNER JOIN
        `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
    INNER JOIN
        `study` s ON s.ID = ssl2.StudyID
    INNER JOIN
        (SELECT
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
),
InvoiceAdjustments AS (
    -- Calculate adjusted invoices based on repeat courses for each academic year
    SELECT
        BaseInvoice.ID,
        GradeData.AcademicYear,
        BaseInvoice.BaseInvoice,
        GradeData.TotalCourses,
        GradeData.FailedCourses,
        CASE 
            WHEN GradeData.TotalCourses > 0 THEN 
                BaseInvoice.BaseInvoice * (GradeData.FailedCourses / GradeData.TotalCourses)
            ELSE 
                BaseInvoice.BaseInvoice
        END AS AdjustedInvoice
    FROM
        BaseInvoice
    LEFT JOIN
        GradeData ON GradeData.StudentNo = BaseInvoice.ID
)
SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.GovernmentID,
    bi.StudyType,
    s.Name AS StudyName,
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS "Registration Status",
    InvoiceAdjustments.AdjustedInvoice AS "Total Invoice",
    InvoiceAdjustments.AcademicYear,
    InvoiceAdjustments.TotalCourses,
    InvoiceAdjustments.FailedCourses
FROM
    `basic-information` bi
LEFT JOIN
    `balances` b ON b.StudentID = bi.ID 
LEFT JOIN
    `grades` AS g ON g.StudentNo = bi.ID
LEFT JOIN
    `GradeData` ON `GradeData`.StudentNo = bi.ID
LEFT JOIN
    `course-electives` ce ON bi.ID = ce.StudentID AND ce.`Year` = 2023
INNER JOIN
    `student-study-link` ssl2 ON ssl2.StudentID = bi.ID
INNER JOIN
    `study` s ON s.ID = ssl2.StudyID
INNER JOIN
    `InvoiceAdjustments` ON `InvoiceAdjustments`.ID = bi.ID
WHERE
    bi.StudyType = 'Fulltime'
    AND 
    (bi.ID LIKE '210%' OR bi.ID LIKE '220%' OR bi.ID LIKE '230%' OR bi.ID LIKE '240%')
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID,
    InvoiceAdjustments.AcademicYear;
