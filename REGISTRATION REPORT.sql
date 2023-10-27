////2019

SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    p.ProgramName,
    program.ProgrammeCode,
    bi.StudyType,
    s.ShortName,
    s.Name,
    s2.
    
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
INNER JOIN schools s2 on s.ParentID = s2.ID 
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