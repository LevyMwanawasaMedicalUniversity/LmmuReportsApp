#SAGE QUERY 
SELECT [DCLink]
    ,[Account]
    ,[Name]
    ,[Title]      
    ,[Contact_Person]
    ,[Physical1]
    ,[Physical2]
    ,[DCBalance]
    
FROM [LMMU_Live].[dbo].[Client]

WHERE [DCBalance] IS NULL
    AND ([Account] LIKE '190%'
    OR [Account] LIKE '210%'
    OR [Account] LIKE '220%'
    OR [Account] LIKE '230%')
    AND LEN([Account]) = 9
SELECT *
    FROM [dbo].[_btblInvoiceLines];
SELECT *
    FROM [LMMU].[dbo].[_etblARAPBatches];

SELECT *
    FROM [LMMU].[dbo].[_etblARAPBatchHistoryLines];

SELECT *
    FROM [LMMU].[dbo].[_etblARAPBatchLines];

SELECT *
    FROM [LMMU].[dbo].[_rtblAgents];
SELECT idInvoiceLines, iInvoiceID, iOrigLineID, iGrvLineID, iLineDocketMode, cDescription, iUnitsOfMeasureStockingID, iUnitsOfMeasureCategoryID, iUnitsOfMeasureID, fQuantity, fQtyChange, fQtyToProcess, fQtyLastProcess, fQtyProcessed, fQtyReserved, fQtyReservedChange, cLineNotes, fUnitPriceExcl, fUnitPriceIncl, iUnitPriceOverrideReasonID, fUnitCost, fLineDiscount, iLineDiscountReasonID, iReturnReasonID, fTaxRate, bIsSerialItem, bIsWhseItem, fAddCost, cTradeinItem, iStockCodeID, iJobID, iWarehouseID, iTaxTypeID, iPriceListNameID, fQuantityLineTotIncl, fQuantityLineTotExcl, fQuantityLineTotInclNoDisc, fQuantityLineTotExclNoDisc, fQuantityLineTaxAmount, fQuantityLineTaxAmountNoDisc, fQtyChangeLineTotIncl, fQtyChangeLineTotExcl, fQtyChangeLineTotInclNoDisc, fQtyChangeLineTotExclNoDisc, fQtyChangeLineTaxAmount, fQtyChangeLineTaxAmountNoDisc, fQtyToProcessLineTotIncl, fQtyToProcessLineTotExcl, fQtyToProcessLineTotInclNoDisc, fQtyToProcessLineTotExclNoDisc, fQtyToProcessLineTaxAmount, fQtyToProcessLineTaxAmountNoDisc, fQtyLastProcessLineTotIncl, fQtyLastProcessLineTotExcl, fQtyLastProcessLineTotInclNoDisc, fQtyLastProcessLineTotExclNoDisc, fQtyLastProcessLineTaxAmount, fQtyLastProcessLineTaxAmountNoDisc, fQtyProcessedLineTotIncl, fQtyProcessedLineTotExcl, fQtyProcessedLineTotInclNoDisc, fQtyProcessedLineTotExclNoDisc, fQtyProcessedLineTaxAmount, fQtyProcessedLineTaxAmountNoDisc, fUnitPriceExclForeign, fUnitPriceInclForeign, fUnitCostForeign, fAddCostForeign, fQuantityLineTotInclForeign, fQuantityLineTotExclForeign, fQuantityLineTotInclNoDiscForeign, fQuantityLineTotExclNoDiscForeign, fQuantityLineTaxAmountForeign, fQuantityLineTaxAmountNoDiscForeign, fQtyChangeLineTotInclForeign, fQtyChangeLineTotExclForeign, fQtyChangeLineTotInclNoDiscForeign, fQtyChangeLineTotExclNoDiscForeign, fQtyChangeLineTaxAmountForeign, fQtyChangeLineTaxAmountNoDiscForeign, fQtyToProcessLineTotInclForeign, fQtyToProcessLineTotExclForeign, fQtyToProcessLineTotInclNoDiscForeign, fQtyToProcessLineTotExclNoDiscForeign, fQtyToProcessLineTaxAmountForeign, fQtyToProcessLineTaxAmountNoDiscForeign, fQtyLastProcessLineTotInclForeign, fQtyLastProcessLineTotExclForeign, fQtyLastProcessLineTotInclNoDiscForeign, fQtyLastProcessLineTotExclNoDiscForeign, fQtyLastProcessLineTaxAmountForeign, fQtyLastProcessLineTaxAmountNoDiscForeign, fQtyProcessedLineTotInclForeign, fQtyProcessedLineTotExclForeign, fQtyProcessedLineTotInclNoDiscForeign, fQtyProcessedLineTotExclNoDiscForeign, fQtyProcessedLineTaxAmountForeign, fQtyProcessedLineTaxAmountNoDiscForeign, iLineRepID, iLineProjectID, iLedgerAccountID, iModule, bChargeCom, bIsLotItem, iMFPID, iLineID, iLinkedLineID, fQtyLinkedUsed, fUnitPriceInclOrig, fUnitPriceExclOrig, fUnitPriceInclForeignOrig, fUnitPriceExclForeignOrig, iDeliveryMethodID, fQtyDeliver, dDeliveryDate, iDeliveryStatus, fQtyForDelivery, bPromotionApplied, fPromotionPriceExcl, fPromotionPriceIncl, cPromotionCode, iSOLinkedPOLineID, fLength, fWidth, fHeight, iPieces, iPiecesToProcess, iPiecesLastProcess, iPiecesProcessed, iPiecesReserved, iPiecesDeliver, iPiecesForDelivery, fQuantityUR, fQtyChangeUR, fQtyToProcessUR, fQtyLastProcessUR, fQtyProcessedUR, fQtyReservedUR, fQtyReservedChangeUR, fQtyDeliverUR, fQtyForDeliveryUR, fQtyLinkedUsedUR, iPiecesLinkedUsed, iSalesWhseID, [_btblInvoiceLines_iBranchID], [_btblInvoiceLines_dCreatedDate], [_btblInvoiceLines_dModifiedDate], [_btblInvoiceLines_iCreatedBranchID], [_btblInvoiceLines_iModifiedBranchID], [_btblInvoiceLines_iCreatedAgentID], [_btblInvoiceLines_iModifiedAgentID], [_btblInvoiceLines_iChangeSetID], [_btblInvoiceLines_Checksum], iMajorIndustryCodeID, iCancellationReasonID, bReverseChargeApplied, fRecommendedRetailPrice, iSelectedBarcodeID
FROM LMMU_Live.dbo.[_btblInvoiceLines];

#VERY IMPORTANT
SELECT SUM(fForeignCredit) AS TotalForeignCredit
FROM LMMU_Live.dbo.PostAR
WHERE TxDate >= '2022-11-01' AND TxDate < '2023-08-01';

SELECT
    YEAR(TxDate) AS TransactionYear,
    MONTH(TxDate) AS TransactionMonth,
    SUM(fForeignCredit) AS MonthlyRevenue
FROM
    LMMU_Live.dbo.PostAR
WHERE
    TxDate >= '2022-01-01'
   	AND TxDate <= '2022-08-31'
GROUP BY
    YEAR(TxDate),
    MONTH(TxDate)
ORDER BY
    TransactionYear,
    TransactionMonth;
  
   
   TxDate >= '2022-01-01'
   AND TxDate <= '2022-08-31'
   
   
   ce.EnrolmentDate  >= '2022-11-01' AND ce.EnrolmentDate <= '2023-08-01' 
   #################################

   ###########VERY IMPORTANT REGISTRATION
   SELECT 
	bi.FirstName, 
	bi.MiddleName, 
	bi.Surname, 
	bi.PrivateEmail, 
	ssl2.StudentID,
	ce.EnrolmentDate as "Registration Date",
	s.Name as "Programme Name",
	CASE
		 when s.Name = "Natural Science" then 'NS'
		 ELSE edurole.schools.Description
	END AS "School",
	ce.`Year` as "Academic Year"
FROM edurole.schools
INNER JOIN 
	study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID
INNER JOIN `course-electives` ce ON ssl2.StudentID = ce.StudentID
INNER JOIN `basic-information` bi ON ce.StudentID = bi.ID 
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
WHERE ce.`Year` = "2023" 
GROUP BY bi.ID ;
#######################

#STUDENT BILLING AGAINST PAYMENT PER YEAR 
#STUDENTS AT 100%
#STUDENTS AT 75% FOR EXAMINATIONS
#STUDENTS AT 50%
#STUDENTS AT 25%
#STUDENTS BELOW 25%
#first year students and how much they have paid so far distance ibbs,son,sohs,sophes,somcs,drpgs
SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    s.Name as "Programme",
    s2.Name as "School",    
    NumberOfPayments,
    TotalPayments AS 'Total Payments',
    CASE 
	    WHEN 
	    	p.ProgramName LIKE '%y1' OR p.ProgramName IS NULL THEN 
	    		ROUND((TotalPayments * 100)/(program.YEAR1),2)    
	    	ELSE 
				ROUND((TotalPayments * 100)/(program.YEAR2),2)
    END AS "Percentage Paid",
    CASE 
	    WHEN 
	    	p.ProgramName LIKE '%y1' OR p.ProgramName IS NULL THEN 
	    		(program.YEAR1)    
	    	ELSE 
				(program.YEAR2)
    END AS "Invoice", 
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
LEFT JOIN (
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
LEFT JOIN `program-course-link` pcl on pcl.CourseID  = c.ID 
LEFT JOIN programmes p on p.ID = pcl.ProgramID 
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
    AND bi.ID LIKE '230%'
    AND LENGTH(bi.ID) >= 4
    AND s2.Name = 'DRPGS'
GROUP BY
    bi.ID;
#first year students and how much they have paid so far fulltime
SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    s.Name as "Programme",
    s2.Name as "School",    
    NumberOfPayments,
    TotalPayments AS 'Total Payments',
    CASE 
        WHEN 
            p.ProgramName LIKE '%y1' OR p.ProgramName IS NULL THEN 
	    		ROUND((TotalPayments * 100)/(program.YEAR1),2)    
            ELSE 
				ROUND((TotalPayments * 100)/(program.YEAR2),2)
    END AS "Percentage Paid",
    CASE 
        WHEN 
            p.ProgramName LIKE '%y1' OR p.ProgramName IS NULL THEN 
                (program.YEAR1)    
            ELSE 
				(program.YEAR2)
    END AS "Invoice", 
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
END AS "YearOfStudy"
FROM
    edurole.`basic-information` bi
LEFT JOIN (
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
LEFT JOIN `program-course-link` pcl on pcl.CourseID  = c.ID 
LEFT JOIN programmes p on p.ID = pcl.ProgramID 
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
    bi.StudyType = 'Fulltime'
    AND bi.ID LIKE '230%'
    AND LENGTH(bi.ID) >= 4
    AND s2.Name = 'drpgs'
GROUP BY
    bi.ID;
#NATURAL SCIENCE FULLTIMESELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    p.ProgramName,
    bi.ID,
    bi.StudyType,
    s.Name as "Programme",
    s2.Name as "School",    
    NumberOfPayments,
    TotalPayments AS 'Total Payments',
    CASE 
	    WHEN 
	    	p.ProgramName LIKE '%y1' OR p.ProgramName IS NULL THEN 
	    		ROUND((TotalPayments * 100)/(program.YEAR1),2)    
	    	ELSE 
				ROUND((TotalPayments * 100)/(program.YEAR2),2)
    END AS "Percentage Paid",
    CASE 
	    WHEN 
	    	p.ProgramName LIKE '%y1' OR p.ProgramName IS NULL THEN 
	    		(program.YEAR1)    
	    	ELSE 
				(program.YEAR2)
    END AS "Invoice", 
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
LEFT JOIN (
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
LEFT JOIN `program-course-link` pcl on pcl.CourseID  = c.ID 
LEFT JOIN programmes p on p.ID = pcl.ProgramID 
left JOIN (
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
) AS program ON SUBSTRING_INDEX(p.ProgramName, '-', 1) = program.ProgrammeCode
WHERE
    bi.StudyType = 'Fulltime'
    AND bi.ID LIKE '230%'
    AND LENGTH(bi.ID) >= 4
    AND s2.Name = 'drpgs'
    AND s.ShortName  ='ns'
GROUP BY
    bi.ID;
#NATURAL SCIENCE DISTANCESELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    p.ProgramName,
    bi.ID,
    bi.StudyType,
    s.Name as "Programme",
    s2.Name as "School",    
    NumberOfPayments,
    TotalPayments AS 'Total Payments',
    CASE 
	    WHEN 
	    	p.ProgramName LIKE '%y1' OR p.ProgramName IS NULL THEN 
	    		ROUND((TotalPayments * 100)/(program.YEAR1),2)    
	    	ELSE 
				ROUND((TotalPayments * 100)/(program.YEAR2),2)
    END AS "Percentage Paid",
    CASE 
	    WHEN 
	    	p.ProgramName LIKE '%y1' OR p.ProgramName IS NULL THEN 
	    		(program.YEAR1)    
	    	ELSE 
				(program.YEAR2)
    END AS "Invoice", 
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
LEFT JOIN (
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
LEFT JOIN `program-course-link` pcl on pcl.CourseID  = c.ID 
LEFT JOIN programmes p on p.ID = pcl.ProgramID 
left JOIN (
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
    AND bi.ID LIKE '230%'
    AND LENGTH(bi.ID) >= 4
    AND s2.Name = 'drpgs'
    AND s.ShortName  ='ns'
GROUP BY
    bi.ID;


#COUNTS NUMBER OF REGISTERED STUDENTS PER PROGRAM PER COURSE
SELECT
    c.Name AS "Course Code",
    c.CourseDescription AS "Course Name",
    p.ProgramName AS "Programme Code",
    s.Name AS "Programme Name",
    s2.Name AS "School Name",
    s.StudyType,
    COALESCE(course_counts.`Num Registered Students`, 0) AS "Num Registered Students"
FROM
    edurole.courses c
INNER JOIN
    `program-course-link` pcl ON c.ID = pcl.CourseID
INNER JOIN
    programmes p ON pcl.ProgramID = p.ID
INNER JOIN
    `study-program-link` spl ON p.ID = spl.ProgramID
INNER JOIN
    study s ON s.ID = spl.StudyID
INNER JOIN
    schools s2 ON s.ParentID = s2.ID
LEFT JOIN
    (
        SELECT
            c.ID AS CourseID,
            s.ID AS ProgramID,
            COUNT(DISTINCT ce.StudentID) AS `Num Registered Students`
        FROM
            courses c
        INNER JOIN
            `program-course-link` pcl ON c.ID = pcl.CourseID
        INNER JOIN
            programmes p ON pcl.ProgramID = p.ID
        INNER JOIN
            `course-electives` ce ON c.ID = ce.CourseID
        INNER JOIN
            `basic-information` bi ON ce.StudentID = bi.ID
        INNER JOIN
            `student-study-link` ssl2 ON bi.ID = ssl2.StudentID
        INNER JOIN
            study s ON ssl2.StudyID = s.ID
        WHERE
            ce.`Year` = 2023 
        GROUP BY
            c.ID, s.ID
    ) course_counts ON c.ID = course_counts.CourseID AND s.ID = course_counts.ProgramID
ORDER BY
    s.Name;
    


#STUDENTS WITH balances
SELECT 
    edurole.`basic-information`.FirstName,
    edurole.`basic-information`.MiddleName,
    edurole.`basic-information`.Surname, 
    edurole.`basic-information`.PrivateEmail , 
    edurole.`basic-information`.ID,
    b.Amount , 
    b.LastUpdate , 
    b.LastTransaction 
FROM 
    edurole.`basic-information` 
LEFT JOIN balances b ON b.StudentID = edurole.`basic-information`.ID 
inner join `student-study-link` ssl2 on ssl2.StudentID = edurole.`basic-information`.ID
left join study s on s.ID = ssl2.StudyID
WHERE  b.Amount >= 1;

#STUDENTS REGISTERED 2023
SELECT edurole.`basic-information`.FirstName, edurole.`basic-information`.MiddleName, edurole.`basic-information`.Surname, edurole.`basic-information`.PrivateEmail , edurole.`basic-information`.ID
 FROM edurole.`basic-information` LEFT JOIN `course-electives` ce ON ce.StudentID = edurole.`basic-information`.ID WHERE ce.`Year` = '2023' GROUP BY ce.StudentID ;

#PROGRAM PER SCHOOL
SELECT edurole.study.IntakeStart, edurole.study.IntakeEnd, edurole.study.Delivery, edurole.study.IntakeMax, edurole.study.Name, edurole.study.ShortName, s.Name 
FROM edurole.study left join schools s ON edurole.study.ParentID  = s.ID WHERE s.Name = 'SOMCS';



SELECT edurole.`basic-information`.FirstName, edurole.`basic-information`.MiddleName, edurole.`basic-information`.Surname, s.Name , edurole.`basic-information`.ID,b.Amount, edurole.`basic-information`.GovernmentID
FROM edurole.`basic-information` inner join `student-study-link` ssl2 on edurole.`basic-information`.ID  = ssl2.StudentID LEFT JOIN study s ON ssl2.StudyID = s.ID LEFT JOIN balances b ON b.StudentID = edurole.`basic-information`.ID  WHERE s.ShortName = 'DipCMSG' and ssl2.StudentID LIKE  '210%' 
;


SELECT *  
FROM balances b WHERE b.StudentID  = 210100562;


#total payments made by student in a program

SELECT 
    edurole.`basic-information`.FirstName, 
    edurole.`basic-information`.MiddleName, 
    edurole.`basic-information`.Surname, s.Name , 
    edurole.`basic-information`.ID, edurole.`basic-information`.GovernmentID,
     SUM(t.Amount) AS "Total of Payments"
FROM 
    edurole.`basic-information` 
    left join `student-study-link` ssl2 on edurole.`basic-information`.ID  = ssl2.StudentID 
    LEFT JOIN study s ON ssl2.StudyID = s.ID LEFT JOIN transactions t ON edurole.`basic-information`.ID = t.StudentID  
    WHERE s.ShortName = 'BScCS' and ssl2.StudentID LIKE  '190%' 
GROUP BY edurole.`basic-information`.ID;

SELECT edurole.`basic-information`.FirstName, edurole.`basic-information`.MiddleName, edurole.`basic-information`.Surname, s.Name , edurole.`basic-information`.ID, edurole.`basic-information`.GovernmentID, SUM(tb.Amount)
FROM edurole.`basic-information` left join `student-study-link` ssl2 on edurole.`basic-information`.ID  = ssl2.StudentID LEFT JOIN study s ON ssl2.StudyID = s.ID LEFT JOIN `transactions-bk` tb ON edurole.`basic-information`.ID = tb.StudentID  WHERE s.ShortName = 'BScCS' and ssl2.StudentID LIKE  '190%' 
;
#total payments made by student in a program and school
SELECT edurole.`basic-information`.FirstName, edurole.`basic-information`.MiddleName, edurole.`basic-information`.Surname, s.Name , edurole.`basic-information`.ID, edurole.`basic-information`.GovernmentID, SUM(t.Amount) AS "Total of Payments"
FROM 
    edurole.`basic-information` 
        left join `student-study-link` ssl2 on edurole.`basic-information`.ID  = ssl2.StudentID 
        LEFT JOIN study s ON ssl2.StudyID = s.ID LEFT JOIN transactions t ON edurole.`basic-information`.ID = t.StudentID 
        LEFT JOIN schools s2  ON s.ParentID = s2.ID 
        WHERE s2.Name = 'IBBS' and ssl2.StudentID LIKE  '210%' and edurole.`basic-information`.StudyType = "Fulltime" 
GROUP BY edurole.`basic-information`.ID;

#Students with student number in Year of Study eg all 210 students
SELECT 
edurole.`basic-information`.FirstName, 
edurole.`basic-information`.MiddleName, 
edurole.`basic-information`.Surname,
edurole.`basic-information`.ID,
edurole.`basic-information`.GovernmentID,
edurole.`basic-information`.Sex,
edurole.`basic-information`.StudyType,
edurole.`basic-information`.PrivateEmail,
edurole.`basic-information`.MobilePhone,
edurole.`basic-information`.DateOfBirth,
s.Name as "Programme Name", 
s.ShortName as "Programme Code",
s2.Description as "School"
FROM edurole.`basic-information` 
inner join `student-study-link` ssl2 on edurole.`basic-information`.ID  = ssl2.StudentID 
inner JOIN study s ON ssl2.StudyID = s.ID
LEFT JOIN schools s2  ON s.ParentID = s2.ID 
WHERE  edurole.`basic-information`.ID like "220%"
GROUP BY edurole.`basic-information`.ID;


#all REGISTERED StudentS IN pROGRAM regardless of academic year
SELECT 
edurole.`basic-information`.FirstName, 
edurole.`basic-information`.MiddleName, 
edurole.`basic-information`.Surname, s.Name , 
edurole.`basic-information`.ID, 
edurole.`basic-information`.PrivateEmail 
FROM edurole.`basic-information` 
inner join `student-study-link` ssl2 on edurole.`basic-information`.ID  = ssl2.StudentID 
inner JOIN study s ON ssl2.StudyID = s.ID 
inner JOIN `course-electives` ce 
ON edurole.`basic-information`.ID = ce.StudentID
WHERE s.ShortName = 'MBcHB' and ssl2.StudentID Not LIKE '230%' 
GROUP BY ce.StudentID ;

#2023 only registered students
SELECT edurole.`basic-information`.FirstName, 
        edurole.`basic-information`.MiddleName, edurole.`basic-information`.Surname, edurole.`basic-information`.ID ,edurole.`basic-information`.StudyType, s.Name ,edurole.`basic-information`.GovernmentID, edurole.`basic-information`.PrivateEmail 
FROM edurole.`basic-information` 
left join `student-study-link` ssl2 on edurole.`basic-information`.ID  = ssl2.StudentID 
inner JOIN study s ON ssl2.StudyID = s.ID 
inner JOIN `course-electives` ce ON edurole.`basic-information`.ID = ce.StudentID
WHERE ce.`Year` = '2023' and edurole.`basic-information`.ID LIKE '230%' GROUP BY ce.StudentID;

#all 2023 and programs
SELECT edurole.`basic-information`.FirstName, edurole.`basic-information`.MiddleName, edurole.`basic-information`.Surname, s.Name , edurole.`basic-information`.ID, edurole.`basic-information`.PrivateEmail 
FROM edurole.`basic-information` 
left join `student-study-link` ssl2 on edurole.`basic-information`.ID  = ssl2.StudentID 
inner JOIN study s ON ssl2.StudyID = s.ID 
inner JOIN `course-electives` ce ON edurole.`basic-information`.ID = ce.StudentID
WHERE ce.`Year` = '2023' GROUP BY ce.StudentID;

#NUMBER OF STUDENTS REGISTERD PER COURSE
SELECT
    courses.Name AS "Course Code",
    courses.CourseDescription AS "Course Name",
    p.ProgramName AS "Programme Code",
    s.Name AS "Programme Name",
    s2.Name AS "School Name",
    s.StudyType ,
    COUNT(ce.StudentID) AS "Num Registered Students"
FROM
    edurole.courses
INNER JOIN
    `program-course-link` pcl ON courses.ID = pcl.CourseID
INNER JOIN
    programmes p ON pcl.ProgramID = p.ID
INNER JOIN
    `course-electives` ce ON ce.CourseID = courses.ID
INNER JOIN
    `study-program-link` spl ON p.ID = spl.ProgramID
INNER JOIN
    study s ON s.ID = spl.StudyID
INNER JOIN
	schools s2 on s.ParentID = s2.ID
WHERE
	ce.`Year` = 2023
GROUP BY
    courses.ID,
    s.ID
ORDER BY
    s.Name

SELECT bi.ID, bi.FirstName, bi.Surname, bi.GovernmentID, bi.Sex, s.Name 
from `basic-information` bi join `student-study-link` ssl2 
on bi.ID = ssl2.StudentID join study s on ssl2.StudyID = s.ID 
where bi.ID in (select distinct StudentID from `course-electives` 
where StudentID like "230%" and year = 2023)

#selects students information registered per year in year of study
SELECT s.ID, edurole.schools.ParentID, edurole.schools.Established, p.ProgramName, ssl2.StudentID, s.Name, edurole.schools.Description, edurole.schools.Dean,
    CASE
        WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
        ELSE 'NO REGISTRATION'
    END AS `Registration Status`
FROM edurole.schools
LEFT JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID
INNER JOIN `course-electives` ce ON ssl2.StudentID = ce.StudentID
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
WHERE edurole.schools.Name = 'SOMCS' AND p.ProgramName LIKE '%y1' AND ce.`Year` = 2022
GROUP BY ce.StudentID;

#Students registered in a Programme and there year of study in a specified academic year
SELECT  bi.FirstName, 
        bi.MiddleName, 
        bi.Surname, 
        bi.PrivateEmail, 
        ssl2.StudentID, 
        s.Name, 
        edurole.schools.Description, 
        edurole.schools.Dean
FROM edurole.schools
INNER JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID
INNER JOIN `course-electives` ce ON ssl2.StudentID = ce.StudentID
INNER JOIN `basic-information` bi ON ce.StudentID = bi.ID 
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
WHERE s.ShortName = 'MBCHB' AND p.ProgramName LIKE '%y1' AND ce.`Year` = 2023
GROUP BY ce.StudentID;

#Students registered in a Programme and there year of study in a specified academic year
SELECT bi.FirstName, bi.MiddleName, bi.Surname, bi.PrivateEmail, ssl2.StudentID, s.Name, edurole.schools.Description, edurole.schools.Dean
FROM edurole.schools
INNER JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID
INNER JOIN `course-electives` ce ON ssl2.StudentID = ce.StudentID
INNER JOIN `basic-information` bi ON ce.StudentID = bi.ID 
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
WHERE s.ShortName = 'MBCHB' AND p.ProgramName LIKE '%y1' AND ce.`Year` = 2023
GROUP BY ce.StudentID;

#Registered student 230 with year of study
#selects students information registered per year in year of study
SELECT
    edurole.schools.ID,
    edurole.schools.ParentID,
    edurole.schools.Established,
    p.ProgramName,
    ssl2.StudentID,
    s.Name,
    edurole.schools.Description,
    edurole.schools.Dean,
    CASE
        WHEN p.ProgramName LIKE '%y1' THEN 'YEAR 1'
        WHEN p.ProgramName LIKE '%y2' THEN 'YEAR 2'
        WHEN p.ProgramName LIKE '%y3' THEN 'YEAR 3'
        WHEN p.ProgramName LIKE '%y4' THEN 'YEAR 4'
        WHEN p.ProgramName LIKE '%y5' THEN 'YEAR 5'
        WHEN p.ProgramName LIKE '%y6' THEN 'YEAR 6'
        ELSE 'NONE'
    END AS "Year Of Study"
FROM
    edurole.schools
LEFT JOIN
    study s ON edurole.schools.ID = s.ParentID
INNER JOIN
    `student-study-link` ssl2 ON ssl2.StudyID = s.ID
INNER JOIN
    `course-electives` ce ON ssl2.StudentID = ce.StudentID
INNER JOIN
    courses c ON ce.CourseID = c.ID
INNER JOIN
    `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN
    programmes p ON p.ID = pcl.ProgramID
WHERE
    edurole.`basic-information`.ID LIKE '230%'
GROUP BY
    edurole.schools.ID,
    edurole.schools.ParentID,
    edurole.schools.Established,
    p.ProgramName,
    ssl2.StudentID,
    s.Name,
    edurole.schools.Description,
    edurole.schools.Dean;


#select students who are registered by student number with their year of study
SELECT bi.FirstName , bi.MiddleName , bi.Surname  ,bi.StudyType as 'Mode Of Study' ,bi.Sex, ssl2.StudentID as 'Student Number',bi.GovernmentID , 
CASE
    WHEN p.ProgramName LIKE '%y1' THEN 'YEAR 1'
    WHEN p.ProgramName LIKE '%y2' THEN 'YEAR 2'
    WHEN p.ProgramName LIKE '%y3' THEN 'YEAR 3'
    WHEN p.ProgramName LIKE '%y4' THEN 'YEAR 4'
    WHEN p.ProgramName LIKE '%y5' THEN 'YEAR 5'
    ELSE 'YEAR 6'
END AS "Year Of Study", s.Name as 'Programme Name', edurole.schools.Description as 'School'
FROM edurole.schools
INNER JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID 
INNER JOIN `course-electives` ce ON ssl2.StudentID = ce.StudentID
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID 
INNER JOIN programmes p ON p.ID = pcl.ProgramID
INNER JOIN edurole.`basic-information` bi ON bi.ID = ce.StudentID 
WHERE ce.StudentID LIKE '230%'
AND ce.Year
GROUP BY ce.StudentID;

#counts students information registered per year in year of study
SELECT COUNT(DISTINCT ce.StudentID) 
FROM edurole.schools 
INNER JOIN study s ON edurole.schools.ID = s.ParentID 
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID 
INNER JOIN `course-electives` ce ON ssl2.StudentID =ce.StudentID
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl on pcl.CourseID  = c.ID 
INNER JOIN programmes p on p.ID = pcl.ProgramID 
WHERE edurole.schools.Name = 'DRGS' and p.ProgramName LIKE '%y2' and ce.`Year` = 2022;


#Docs data GET REGISTERED AND UNREGISTERED PER ACADEMIC YEAR 2019,2020,2021 AND 2022
SELECT FirstName, MiddleName, Surname, s.Name ,edurole.`basic-information`.ID,
CASE 
    WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
    ELSE 'NO REGISTERATION'
END AS "Registration Status",
g.AcademicYear as "Academic Year"  
FROM edurole.`basic-information` 
left join `course-electives` ce on ce.StudentID  = edurole.`basic-information`.ID 
inner join `student-study-link` ssl2 on ssl2.StudentID = edurole.`basic-information`.ID
left join study s on s.ID = ssl2.StudyID
inner join grades g on g.StudentNo = edurole.`basic-information`.ID
WHERE g.AcademicYear  = "2019"
and (edurole.`basic-information`.StudyType = "Distance" OR edurole.`basic-information`.StudyType = "Fulltime")
#and edurole.`basic-information`.ID NOT LIKE "230%"
#and edurole.`basic-information`.ID NOT LIKE "220%"
#and edurole.`basic-information`.ID NOT LIKE "210%"
GROUP BY edurole.`basic-information`.ID;

#NEW QUERY FOR STUDENTS THAT HAVNT PaID
SELECT 
    bi.FirstName, 
    bi.MiddleName, 
    bi.Surname, 
    s.Name, 
    bi.ID, 
    bi.GovernmentID,
    bi.StudyType,
    bi.Sex,
    bi.Status,
    SUM(t.Amount) AS "Total of Payments",
    CASE 
        WHEN g.StudentNo IS NOT NULL THEN
            CASE 
                WHEN MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) >= 1 THEN
                    CONCAT('Year', LEFT(CAST(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) AS CHAR), LENGTH(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED))) - 2))
                ELSE
                    'No Year Found'
            END
        ELSE 'NO RESULTS'
    END AS "Results"
FROM 
    `edurole`.`basic-information` AS bi
    LEFT JOIN `student-study-link` AS ssl2 ON bi.ID = ssl2.StudentID 
    LEFT JOIN `study` AS s ON ssl2.StudyID = s.ID 
    LEFT JOIN `transactions` AS t ON bi.ID = t.StudentID 
    left join balances b on b.StudentID = bi.ID 
    LEFT JOIN `grades` AS g ON g.StudentNo = bi.ID
WHERE 
    bi.StudyType != 'Staff' 
    AND b.LastTransaction = 0 
    AND LENGTH(bi.ID) = 9
    
    AND (
        bi.ID LIKE '190%'
        OR bi.ID LIKE '210%'
        OR bi.ID LIKE '220%'
        OR bi.ID LIKE '230%'
    )
GROUP BY 
    bi.ID
HAVING 
    SUM(t.Amount) IS NULL;

#EXCEMPOTIONS 
SELECT
  bi.FirstName,
  bi.MiddleName,
  bi.Surname,  
  bi.ID,
  bi.GovernmentID,
  s.Name as "Programme Name",
  s2.Description as "School Name",
  bi.StudyType,
  bi.Sex,
  CASE
    WHEN g.StudentNo IS NOT NULL THEN
      CASE
        WHEN MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) >= 1 THEN
          CONCAT('Year', LEFT(CAST(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) AS CHAR), LENGTH(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED))) - 2))
        ELSE
          'No Year Found'
      END
    ELSE 'NO RESULTS'
  END AS Results,
  CASE
    WHEN g.StudentNo IS NOT NULL THEN
      CASE
        WHEN Min(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) >= 1 THEN
          CONCAT('Year', LEFT(CAST(Min(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) AS CHAR), LENGTH(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED))) - 2))
        ELSE
          'No Year Found'
      END
    ELSE 'NO RESULTS'
  END AS EntryYear
FROM
  `edurole`.`basic-information` AS bi
  INNER JOIN `student-study-link` AS ssl2 ON bi.ID = ssl2.StudentID
  INNER JOIN `study` AS s ON ssl2.StudyID = s.ID
  INNER JOIN `grades` AS g ON g.StudentNo = bi.ID
  INNER JOIN schools s2 ON s.ParentID = s2.ID 
WHERE
  bi.StudyType != 'Staff'  
  AND LENGTH(bi.ID) = 9
  AND (
    bi.ID LIKE '190%'
    OR bi.ID LIKE '210%'
    OR bi.ID LIKE '220%'
    #OR bi.ID LIKE '230%'
  )
GROUP BY
  bi.ID
HAVING
  EntryYear NOT LIKE 'Year1%';
    

#Docs data GET REGISTERED AND UNREGISTERED PER ACADEMIC YEAR 2019,2020,2021 AND 2023
SELECT FirstName, MiddleName, Surname, s.Name ,edurole.`basic-information`.ID,
CASE 
    WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED'
    ELSE 'NO REGISTERATION'
END AS "Registration Status",
ce.`Year`  as "Academic Year"  
FROM edurole.`basic-information` 
left join `course-electives` ce on ce.StudentID  = edurole.`basic-information`.ID 
LEFT join `student-study-link` ssl2 on ssl2.StudentID = edurole.`basic-information`.ID
left join study s on s.ID = ssl2.StudyID
#inner join grades g on g.StudentNo = edurole.`basic-information`.ID
WHERE
(edurole.`basic-information`.StudyType = "Distance" OR edurole.`basic-information`.StudyType = "Fulltime")
#and edurole.`basic-information`.ID NOT LIKE "230%"
#and edurole.`basic-information`.ID NOT LIKE "220%"
#and edurole.`basic-information`.ID NOT LIKE "210%"
GROUP BY edurole.`basic-information`.ID;

#STUDENTS WWITHOUT RESULTS PER ACADEMIC YEAR comment eg "230%" number accordingly
SELECT 
    edurole.`basic-information`.FirstName , 
    edurole.`basic-information`.MiddleName , 
    edurole.`basic-information`.Surname,
    edurole.`basic-information`.ID,
    edurole.`basic-information`.GovernmentID,
    edurole.`basic-information`.Sex,
    s.Name as 'Programme of Study',
    s2.Name as 'School',
    edurole.`basic-information`.StudyType as 'Mode Of Study',    
    edurole.`basic-information`.PrivateEmail
FROM edurole.`basic-information`
INNER JOIN edurole.`student-study-link` ssl2 ON ssl2.StudentID = edurole.`basic-information`.ID 
INNER JOIN study s ON ssl2.StudyID = s.ID
INNER JOIN schools s2 ON s.ParentID = s2.ID 
WHERE edurole.`basic-information`.ID NOT IN (
        SELECT g.StudentNo 
        FROM edurole.grades g 
        WHERE g.AcademicYear  = '2022'
    )
AND edurole.`basic-information`.ID NOT LIKE "230%"
#AND edurole.`basic-information`.ID NOT LIKE "220%"
#AND edurole.`basic-information`.ID NOT LIKE "210%"
AND edurole.`basic-information`.StudyType != 'staff'
AND LENGTH(edurole.`basic-information`.ID) = 9
GROUP BY edurole.`basic-information`.ID;


#REGISTERED STUDENTS DETAILS

SELECT bi.FirstName,bi.MiddleName , bi.Surname , ssl2.StudentID, bi.GovernmentID , s.Name as "Programme Name", p.ProgramName as "Programme Code" , edurole.schools.Description
FROM edurole.schools LEFT JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID 
INNER JOIN `course-electives` ce ON ssl2.StudentID =ce.StudentID
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl on pcl.CourseID  = c.ID 
JOIN `basic-information` bi ON bi.ID = ssl2.StudentID 
INNER JOIN programmes p on p.ID = pcl.ProgramID 
WHERE p.ProgramName LIKE '%y1' and ce.`Year` = 2023 GROUP BY ce.StudentID;

SELECT bi.FirstName,bi.MiddleName , bi.Surname , ssl2.StudentID, bi.GovernmentID , s.Name as "Programme Name", p.ProgramName as "Programme Code" , edurole.schools.Description
FROM edurole.schools LEFT JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID 
INNER JOIN `course-electives` ce ON ssl2.StudentID =ce.StudentID
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl on pcl.CourseID  = c.ID 
JOIN `basic-information` bi ON bi.ID = ssl2.StudentID 
INNER JOIN programmes p on p.ID = pcl.ProgramID 
GROUP BY ce.StudentID;


#ALL STUDENTS IN EDUROLE
SELECT bi.FirstName,bi.MiddleName , bi.Surname , ssl2.StudentID, bi.StudyType, bi.GovernmentID , 
s.Name as "Programme Name", SUBSTRING(p.ProgramName,-2) as "Year of study" , edurole.schools.Description
FROM edurole.schools LEFT JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID 
INNER JOIN `course-electives` ce ON ssl2.StudentID =ce.StudentID
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl on pcl.CourseID  = c.ID 
JOIN `basic-information` bi ON bi.ID = ssl2.StudentID 
INNER JOIN programmes p on p.ID = pcl.ProgramID 
GROUP BY ce.StudentID;

#PRINTS REGISTRATION DETAILS 
SELECT
    bi.FirstName, 
    bi.MiddleName,
    bi.Surname,	
    bi.ID,
    bi.GovernmentID,
    bi.PrivateEmail,
    bi.MobilePhone,
    p.ProgramName AS "Programme Code",
    s.Name AS "Programme Name",
    edurole.schools.Description AS "School",
    bi.StudyType,
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
    edurole.schools
LEFT JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID
LEFT JOIN `course-electives` ce ON ssl2.StudentID = ce.StudentID AND ce.`Year` = 2023
LEFT JOIN courses c ON ce.CourseID = c.ID
LEFT JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
LEFT JOIN programmes p ON p.ID = pcl.ProgramID
LEFT JOIN `basic-information` bi ON bi.ID = ssl2.StudentID 
WHERE
    ce.`Year` = 2023 or
    ce.`Year` IS NULL 
    AND LENGTH(bi.ID) = 9    
    AND (
        bi.ID LIKE '190%'
        OR bi.ID LIKE '210%'
        OR bi.ID LIKE '220%'
        OR bi.ID LIKE '230%'
    )
GROUP BY
    bi.ID;

    ###############################
    ######ELIGIBLE###################
    ###################

#QUERY STUDENTS ELIBLE TO REGISTER THAT ARE UNREGISTERED 2019 INTAKE FULLTIME
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
    TotalPayments - ((program.YEAR2 * 2) + program.YEAR1) AS Balance,
    CASE
        WHEN (TotalPayments - ((program.YEAR2 * 2) + program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
) AS program ON s.ShortName = program.ProgrammeCode
WHERE
    bi.StudyType = 'Fulltime'
    AND bi.ID LIKE '190%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'ELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);

#QUERY STUDENTS ELIBLE TO REGISTER THAT ARE UNREGISTERED 2021 INTAKE FULLTIME
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
    TotalPayments - ((program.YEAR2) + program.YEAR1) AS Balance,
    CASE
        WHEN (TotalPayments - ((program.YEAR2) + program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
    AND bi.ID LIKE '210%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'ELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);

#QUERY STUDENTS ELIBLE TO REGISTER THAT ARE UNREGISTERED 2022 INTAKE FULLTIME
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
    TotalPayments - (program.YEAR1) AS Balance,
    CASE
        WHEN (TotalPayments - ( program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'                                                                                                                                  
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
    AND bi.ID LIKE '220%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'ELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


#QUERY STUDENTS ELIBLE TO REGISTER THAT ARE UNREGISTERED 2023 INTAKE FULLTIME
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
    TotalPayments AS Balance,
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
    bi.ID
HAVING
    Eligibility = 'ELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


#DISTANCE 2019 ELIGIBLE TO REGISTER

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
    TotalPayments - ((program.YEAR2 * 2) + program.YEAR1) AS Balance,
    CASE
        WHEN (TotalPayments - ((program.YEAR2 * 2) + program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
        'BScEH' AS ProgrammeCode, 13470 AS YEAR1, 10925 AS YEAR2
    UNION ALL SELECT 'BScPH', 13470, 10925
    UNION ALL SELECT 'BScPHN', 12400, 12150
    UNION ALL SELECT 'DipCMSG', 9670 , 9125
    UNION ALL SELECT 'DipEH', 9670 , 9125    
) AS program ON s.ShortName = program.ProgrammeCode
WHERE
    bi.StudyType = 'Distance'
    AND bi.ID LIKE '190%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'ELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);

#DISTANCE 2021 ELIGIBLE TO REGISTER
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
    TotalPayments - ((program.YEAR2) + program.YEAR1) AS Balance,
    CASE
        WHEN (TotalPayments - ((program.YEAR2) + program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
    AND bi.ID LIKE '210%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'ELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


#DISTANCE 2022 ELIGIBLE TO REGISTER
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
    TotalPayments - (program.YEAR1) AS Balance,
    CASE
        WHEN (TotalPayments - ( program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
    AND bi.ID LIKE '220%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'ELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


#DISTANCE 2023 ELIGIBLE TO REGISTER
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
    TotalPayments AS Balance,
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
    AND bi.ID LIKE '230%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'ELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);

###############################
######NOT ELIGIBLE###################
###################


#QUERY STUDENTS NOT ELIBLE TO REGISTER THAT ARE UNREGISTERED 2019 INTAKE FULLTIME
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
    (TotalPayments - ((program.YEAR2 * 2) + program.YEAR1)) - (program.YEAR2 * 0.25) AS Balance,
    CASE
        WHEN (TotalPayments - ((program.YEAR2 * 2) + program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
) AS program ON s.ShortName = program.ProgrammeCode
WHERE
    bi.StudyType = 'Fulltime'
    AND bi.ID LIKE '190%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'INELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


#QUERY STUDENTS not ELIBLE TO REGISTER THAT ARE UNREGISTERED 2021 INTAKE FULLTIME
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
    (TotalPayments - ((program.YEAR2) + program.YEAR1)) - (program.YEAR2 * 0.25) AS Balance,
    CASE
        WHEN (TotalPayments - ((program.YEAR2) + program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
    AND bi.ID LIKE '210%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'INELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


    #QUERY STUDENTS NOT ELIBLE TO REGISTER THAT ARE UNREGISTERED 2022 INTAKE FULLTIME
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
    (TotalPayments - (program.YEAR1)) - (program.YEAR2 * 0.25) AS Balance,
    CASE
        WHEN (TotalPayments - ( program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
    AND bi.ID LIKE '220%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'INELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


#QUERY STUDENTS NOT ELIBLE TO REGISTER THAT ARE UNREGISTERED 2023 INTAKE FULLTIME
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
    bi.ID
HAVING
    Eligibility = 'INELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


#DISTANCE 2019 NOT ELIGIBLE TO REGISTER

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
    (TotalPayments - ((program.YEAR2 * 2) + program.YEAR1)) - (program.YEAR2 * 0.25) AS Balance,
    CASE
        WHEN (TotalPayments - ((program.YEAR2 * 2) + program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
        'BScEH' AS ProgrammeCode, 13470 AS YEAR1, 10925 AS YEAR2
    UNION ALL SELECT 'BScPH', 13470, 10925
    UNION ALL SELECT 'BScPHN', 12400, 12150
    UNION ALL SELECT 'DipCMSG', 9670 , 9125
    UNION ALL SELECT 'DipEH', 9670 , 9125    
) AS program ON s.ShortName = program.ProgrammeCode
WHERE
    bi.StudyType = 'Distance'
    AND bi.ID LIKE '190%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'INELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);

#DISTANCE 2021 NOT ELIGIBLE TO REGISTER
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
    (TotalPayments - ((program.YEAR2) + program.YEAR1)) - (program.YEAR2 * 0.25) AS Balance,
    CASE
        WHEN (TotalPayments - ((program.YEAR2) + program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
    AND bi.ID LIKE '210%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'INELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);

#DISTANCE 2022 NOT ELIGIBLE TO REGISTER
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
    (TotalPayments - (program.YEAR1)) - (program.YEAR2 * 0.25) AS Balance,
    CASE
        WHEN (TotalPayments - ( program.YEAR1)) > (program.YEAR2 * 0.25) THEN 'ELIGIBLE TO REGISTER'
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
    AND bi.ID LIKE '220%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'INELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);

#DISTANCE 2023 NOT ELIGIBLE TO REGISTER
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
    AND bi.ID LIKE '230%'
    AND LENGTH(bi.ID) >= 4
GROUP BY
    bi.ID
HAVING
    Eligibility = 'INELIGIBLE TO REGISTER'
    AND (`Registration Status` = 'NO REGISTRATION' OR `Registration Status` IS NULL);


wbrwtnfjnvpnwvkd

#QUERY REGISTERED AFTER A DATE 
SELECT  bi.FirstName, 
        bi.MiddleName, 
        bi.Surname, 
        bi.PrivateEmail, 
        ssl2.StudentID, 
        s.Name, 
        ce.EnrolmentDate,
        edurole.schools.Description,
        bi.StudyType,
        CASE 
        WHEN p.ProgramName LIKE '%y1' THEN 'YEAR 1'
        WHEN p.ProgramName LIKE '%y2' THEN 'YEAR 2'
        WHEN p.ProgramName LIKE '%y3' THEN 'YEAR 3'
        WHEN p.ProgramName LIKE '%y4' THEN 'YEAR 4'
        WHEN p.ProgramName LIKE '%y5' THEN 'YEAR 5'
        WHEN p.ProgramName LIKE '%y6' THEN 'YEAR 6'
        ELSE 'NO REGISTRATION'
    END AS "Year Of Study"
FROM edurole.schools
INNER JOIN study s ON edurole.schools.ID = s.ParentID
INNER JOIN `student-study-link` ssl2 ON ssl2.StudyID = s.ID
INNER JOIN `course-electives` ce ON ssl2.StudentID = ce.StudentID
INNER JOIN `basic-information` bi ON ce.StudentID = bi.ID 
INNER JOIN courses c ON ce.CourseID = c.ID
INNER JOIN `program-course-link` pcl ON pcl.CourseID = c.ID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
WHERE s.ShortName = 'BSCCS'
AND ce.`Year` = 2023
AND ce.EnrolmentDate  > '2023-06-15'
GROUP BY ce.StudentID;


#get students and whether theyn have results and which year their most recent results are

SELECT 
    bi.FirstName, 
    bi.MiddleName, 
    bi.Surname, 
    s.Name, 
    bi.ID, 
    bi.GovernmentID,
    bi.PrivateEmail,
    bi.MobilePhone,
    bi.StudyType,
    bi.Sex,
    bi.Status,
    s2.Name,
    #SUM(t.Amount) AS "Total of Payments",
    CASE 
        WHEN g.StudentNo IS NOT NULL THEN
            CASE 
                WHEN MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) >= 1 THEN
                    CONCAT('Year', LEFT(CAST(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED)) AS CHAR), LENGTH(MAX(CAST(REGEXP_SUBSTR(g.CourseNo, '[0-9]+') AS UNSIGNED))) - 2))
                ELSE
                    'No Year Found'
            END
        ELSE 'NO RESULTS'
    END AS "Results"
FROM 
    `edurole`.`basic-information` AS bi
    LEFT JOIN `student-study-link` AS ssl2 ON bi.ID = ssl2.StudentID 
	LEFT JOIN `study` AS s ON ssl2.StudyID = s.ID 
    #LEFT JOIN `transactions` AS t ON bi.ID = t.StudentID 
    LEFT join balances b on b.StudentID = bi.ID 
    LEFT JOIN schools s2  ON s.ParentID = s2.ID 
    LEFT JOIN `grades` AS g ON g.StudentNo = bi.ID
WHERE 
    bi.StudyType != 'Staff' 
    #AND b.Amount  = 0
    #AND b.Original = 0
    #AND b.LastTransaction = 0
    AND LENGTH(bi.ID) = 9
    
    AND (
        bi.ID LIKE '190%'
        OR bi.ID LIKE '210%'
        OR bi.ID LIKE '220%'
        OR bi.ID LIKE '230%'
    )
GROUP BY 
    bi.ID