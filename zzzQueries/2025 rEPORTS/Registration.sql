WITH program_data_lookup AS (
    SELECT StudyType, ProgrammeCode, YEAR1, YEAR2, 0 AS is_190
    FROM (
        SELECT 'Fulltime' AS StudyType, 'AdvDipCA' AS ProgrammeCode, 14570 AS YEAR1, 14350 AS YEAR2
        UNION ALL SELECT 'Fulltime', 'AdvDipCO', 14570, 14350
        UNION ALL SELECT 'Fulltime', 'AdvDipONC', 11481, 11481
        UNION ALL SELECT 'Fulltime', 'AdvDipOPHNUR', 14706, 14350
        UNION ALL SELECT 'Fulltime', 'BAGC', 12350, 12350
        UNION ALL SELECT 'Fulltime', 'BScCA', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BDS', 29700, 29450
        UNION ALL SELECT 'Fulltime', 'MBChB', 29700, 29450
        UNION ALL SELECT 'Fulltime', 'MMEDGS', 22625, 22625
        UNION ALL SELECT 'Fulltime', 'MMEDID', 22625, 22625
        UNION ALL SELECT 'Fulltime', 'MMEDIM', 22625, 22625
        UNION ALL SELECT 'Fulltime', 'MMEDOB', 22625, 22625
        UNION ALL SELECT 'Fulltime', 'MMEDPCH', 22625, 22625
        UNION ALL SELECT 'Fulltime', 'MSCFE', 32950, 32950
        UNION ALL SELECT 'Fulltime', 'MSCHPE', 16625, 16625 
        UNION ALL SELECT 'Fulltime', 'MSCOPTH', 19625, 19625
        UNION ALL SELECT 'Fulltime', 'MSCOPTO', 19625, 19625
        UNION ALL SELECT 'Fulltime', 'PDDIPEMC', 16625, 16625
        UNION ALL SELECT 'Fulltime', 'PHDHPE', 17625, 17625 
        UNION ALL SELECT 'Fulltime', 'Bpharm', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BPHY', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BScBMS', 22400, 22150
        UNION ALL SELECT 'Fulltime', 'BScND', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BScCO', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BScCS', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BScMHCP', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BScMiD', 19673, 19317
        UNION ALL SELECT 'Fulltime', 'BScNUR', 19673, 19317
        UNION ALL SELECT 'Fulltime', 'BScON', 19673, 19317
        UNION ALL SELECT 'Fulltime', 'BScOPT', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BScPH', 16700, 16450
        UNION ALL SELECT 'Fulltime', 'BScPHNUR', 16806, 16450
        UNION ALL SELECT 'Fulltime', 'BScPHN', 16700, 16450
        UNION ALL SELECT 'Fulltime', 'BScRAD', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BScSLT', 19567, 19317
        UNION ALL SELECT 'Fulltime', 'BScEH', 16700, 16450
        UNION ALL SELECT 'Fulltime', 'BScMHN', 19673, 19317
        UNION ALL SELECT 'Fulltime', 'BSc.MHN', 19673, 19317
        UNION ALL SELECT 'Fulltime', 'CertCHAHM', 8040, 8040
        UNION ALL SELECT 'Fulltime', 'CertHCHW', 8040, 8040
        UNION ALL SELECT 'Fulltime', 'CertDA', 8040, 8040
        UNION ALL SELECT 'Fulltime', 'CertEMC', 8040, 8040
        UNION ALL SELECT 'Fulltime', 'DipBMS', 14570, 14350
        UNION ALL SELECT 'Fulltime', 'DipCMSG', 12570, 12350
        UNION ALL SELECT 'Fulltime', 'DipCMSP', 12350, 12350
        UNION ALL SELECT 'Fulltime', 'DipMHCP', 12350, 12350   
        UNION ALL SELECT 'Fulltime', 'DipDTech', 12570, 12350
        UNION ALL SELECT 'Fulltime', 'DipDTh', 12570, 12350
        UNION ALL SELECT 'Fulltime', 'DipEMC', 12570, 12350
        UNION ALL SELECT 'Fulltime', 'DipEH', 12570, 12350
        UNION ALL SELECT 'Fulltime', 'DipGC', 12350, 12350    
        UNION ALL SELECT 'Fulltime', 'DipMHN', 12706, 12350
        UNION ALL SELECT 'Fulltime', 'DipMID', 12706, 12350
        UNION ALL SELECT 'Fulltime', 'DipOPT', 12570, 12350
        UNION ALL SELECT 'Fulltime', 'DipPH', 12570, 12350
        UNION ALL SELECT 'Fulltime', 'DipPHN', 12706, 12350
        UNION ALL SELECT 'Fulltime', 'DipRN', 12706, 12350
        UNION ALL SELECT 'Fulltime', 'DipONC', 12706, 12350
        UNION ALL SELECT 'Fulltime', 'DIPPO', 12570, 12350
        UNION ALL SELECT 'Distance', 'BScEH', 12400, 12150
        UNION ALL SELECT 'Distance', 'BScPH', 12400, 12150
        UNION ALL SELECT 'Distance', 'BScPHN', 12400, 12150
        UNION ALL SELECT 'Distance', 'BScMHCP', 12400, 12150
        UNION ALL SELECT 'Distance', 'BScND', 12400, 12150
        UNION ALL SELECT 'Distance', 'BAGC', 12350, 12350
        UNION ALL SELECT 'Distance', 'BPHY', 11350, 11100
        UNION ALL SELECT 'Distance', 'BScRAD', 11350, 11100
        UNION ALL SELECT 'Distance', 'BScNUR', 12506, 12150
        UNION ALL SELECT 'Distance', 'BScPHNUR', 12506, 12150
        UNION ALL SELECT 'Distance', 'BScMID', 12506, 12150
        UNION ALL SELECT 'Distance', 'BScMHN', 12506, 12150
        UNION ALL SELECT 'Distance', 'BSc.MHN', 12506, 12150
        UNION ALL SELECT 'Distance', 'DipCMSG', 10570, 10350
        UNION ALL SELECT 'Distance', 'DipCMSP', 10570, 10350
        UNION ALL SELECT 'Distance', 'DipMHCP', 14706, 14706
        UNION ALL SELECT 'Distance', 'DipHNP', 14706, 14706
        UNION ALL SELECT 'Distance', 'MPH', 14625, 14625
        UNION ALL SELECT 'Distance', 'MScID', 16575, 16575    
        UNION ALL SELECT 'Distance', 'BScBMS', 21470, 20925
    ) pd    
    UNION ALL    
    SELECT StudyType, ProgrammeCode, YEAR1, YEAR2, 1 AS is_190
    FROM (
        SELECT 'Fulltime' AS StudyType, 'BScEH' AS ProgrammeCode, 15770 AS YEAR1, 15225 AS YEAR2
        UNION ALL SELECT 'Fulltime', 'BScPH', 15770, 15225
        UNION ALL SELECT 'Fulltime', 'CertCHAHM', 7170, 7170
        UNION ALL SELECT 'Fulltime', 'CertDA', 7170, 7170
        UNION ALL SELECT 'Fulltime', 'CertEMC', 7170, 7170
        UNION ALL SELECT 'Fulltime', 'BScPHN', 15770, 15225
        UNION ALL SELECT 'Fulltime', 'BScBMS', 21470, 20925
        UNION ALL SELECT 'Fulltime', 'BScEH', 15770, 15225
        UNION ALL SELECT 'Fulltime', 'DipEH', 11670, 11125
        UNION ALL SELECT 'Fulltime', 'BScND', 18637, 18092
        UNION ALL SELECT 'Fulltime', 'Bpharm', 18637, 18092
        UNION ALL SELECT 'Fulltime', 'BScCA', 18637, 18092
        UNION ALL SELECT 'Fulltime', 'BScOPT', 18637, 18092
        UNION ALL SELECT 'Fulltime', 'BScCO', 18637, 18092
        UNION ALL SELECT 'Fulltime', 'BScCS', 18637, 18092
        UNION ALL SELECT 'Fulltime', 'BAGC', 15550, 12350
        UNION ALL SELECT 'Fulltime', 'DipGC', 11450, 11450
        UNION ALL SELECT 'Fulltime', 'BScNUR', 18773, 18092
        UNION ALL SELECT 'Fulltime', 'BScON', 18773, 18092
        UNION ALL SELECT 'Fulltime', 'DipMID', 11806, 11125
        UNION ALL SELECT 'Fulltime', 'BScPHNUR', 15906, 15225
        UNION ALL SELECT 'Fulltime', 'BScMHN', 18773, 18092
        UNION ALL SELECT 'Fulltime', 'BSc.MHN', 18773, 18092
        UNION ALL SELECT 'Fulltime', 'BScNUR', 18773, 18092
        UNION ALL SELECT 'Fulltime', 'BScON', 18773, 18092
        UNION ALL SELECT 'Fulltime', 'BScMID', 18773, 18092
        UNION ALL SELECT 'Fulltime', 'BScMHCP', 18637, 18092
        UNION ALL SELECT 'Fulltime', 'DipMID', 11806, 11125
        UNION ALL SELECT 'Fulltime', 'MBCHB', 28770, 28225
        UNION ALL SELECT 'Fulltime', 'MScHPE', 16625, 16625
        UNION ALL SELECT 'Fulltime', 'PHdHPE', 17625, 17625
        UNION ALL SELECT 'Fulltime', 'DipPHN', 11806, 11125
        UNION ALL SELECT 'Fulltime', 'DipRN', 11806, 11125
        UNION ALL SELECT 'Fulltime', 'DipMHN', 11806, 11125
        UNION ALL SELECT 'Fulltime', 'BScBMS', 21470, 20925
        UNION ALL SELECT 'Fulltime', 'DipBMS', 13670, 13125
        UNION ALL SELECT 'Fulltime', 'DipCMSG', 11670, 11125
        UNION ALL SELECT 'Fulltime', 'DipCMSP', 11670, 11125
        UNION ALL SELECT 'Fulltime', 'DipDTECH', 11670, 11125
        UNION ALL SELECT 'Fulltime', 'DipDTH', 11670, 11125
        UNION ALL SELECT 'Fulltime', 'DipOPT', 11670, 11125
        UNION ALL SELECT 'Fulltime', 'DipPH', 11670, 11125
        UNION ALL SELECT 'Fulltime', 'DipEMC', 11670, 11125
        UNION ALL SELECT 'Fulltime', 'DiGC', 11450, 11450
        UNION ALL SELECT 'Distance', 'BScEH', 13470, 10925
        UNION ALL SELECT 'Distance', 'BScPH', 13470, 10925
        UNION ALL SELECT 'Distance', 'BScPHN', 12400, 12150
        UNION ALL SELECT 'Distance', 'DipCMSG', 9670, 9125
        UNION ALL SELECT 'Distance', 'DipEH', 9670, 9125
    ) pd190
),
filtered_students AS (
    SELECT 
        ID, FirstName, MiddleName, Surname, Sex, GovernmentID, 
        PrivateEmail, MobilePhone, StudyType, Status,
        SUBSTRING(ID, 1, 3) AS id_prefix
    FROM `basic-information`
    WHERE LENGTH(ID) > 7 AND Status = 'Approved'
),
student_study_links AS (
    SELECT StudentID, StudyID
    FROM `student-study-link`
),
max_year_data AS (
    SELECT 
        ss.ID AS StudentID,
        MAX(p.Year) AS MaxYear
    FROM filtered_students ss
    JOIN student_study_links ssl2 ON ssl2.StudentID = ss.ID
    JOIN `study-program-link` spl ON spl.StudyID = ssl2.StudyID
    JOIN programmes p ON p.ID = spl.ProgramID
    GROUP BY ss.ID
),
year_reporting_data AS (
    SELECT
        bi3.ID as StudentId,
        MIN(c3.Year) as YearReported
    FROM filtered_students bi3
    LEFT JOIN `grades-published` gp4 ON gp4.StudentNo = bi3.ID 
    LEFT JOIN courses c3 ON gp4.CourseNo = c3.Name 
    GROUP BY bi3.ID
),
student_research_status AS (
    SELECT 
        bi.ID,
        CASE
            WHEN EXISTS (
                SELECT 1 
                FROM max_year_data my
                WHERE my.StudentID = bi.ID 
                AND my.MaxYear < (
                    SELECT COALESCE(MAX(c.Year) + 1, 0)
                    FROM `grades-published` gp
                    JOIN courses c ON c.Name = gp.CourseNo
                    WHERE gp.StudentNo = bi.ID
                )
            ) THEN -- This is a graduate
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM `grades-published` gp_research
                        JOIN courses rc ON rc.Name = gp_research.CourseNo
                        WHERE gp_research.StudentNo = bi.ID
                        AND gp_research.AcademicYear = 2025
                        AND rc.CourseDescription LIKE '%Research%'
                    ) THEN 'GRADUATE WITH RESEARCH'
                    ELSE 'GRADUATE WITHOUT RESEARCH'
                END
            ELSE 'NOT APPLICABLE'
        END AS ResearchStatus
    FROM filtered_students bi
),
student_year_info AS (
    SELECT 
        bi.ID,
        bi.id_prefix,
        MAX(c.Year) AS current_year,
        yr.YearReported,
        my.MaxYear,
        MAX(p2.Year) AS RegisteredCurrentYearOfStudy,
        CASE WHEN bi.id_prefix = '250' THEN 'NEWLY ADMITTED' ELSE 'RETURNING STUDENT' END AS StudentType,
        CASE
            WHEN my.MaxYear IS NOT NULL AND MAX(c.Year) IS NOT NULL AND my.MaxYear < (MAX(c.Year) + 1) THEN 'GRADUATED'
            ELSE 'NOT GRADUATED'
        END AS GraduationStatus
    FROM filtered_students bi
    LEFT JOIN `grades-published` gp ON gp.StudentNo = bi.ID AND gp.AcademicYear = 2024
    LEFT JOIN courses c ON c.Name = gp.CourseNo
    LEFT JOIN `course-electives` ce ON bi.ID = ce.StudentID AND (ce.`Year` = 2025 OR ce.`EnrolmentDate` > '2025-01-01')
    LEFT JOIN `program-course-link` pcl2 ON pcl2.CourseID = ce.CourseID 
    LEFT JOIN programmes p2 ON p2.ID = pcl2.ProgramID
    LEFT JOIN max_year_data my ON my.StudentID = bi.ID
    LEFT JOIN year_reporting_data yr ON yr.StudentId = bi.ID
    GROUP BY bi.ID, bi.id_prefix, yr.YearReported, my.MaxYear
),
registration_status AS (
    SELECT 
        bi.ID,
        MAX(CASE WHEN ce3.StudentID IS NOT NULL THEN 'REGISTERED' ELSE 'NO REGISTRATION' END) AS reg_status_2026,
        MAX(CASE WHEN ce.StudentID IS NOT NULL THEN 'REGISTERED' ELSE 'NO REGISTRATION' END) AS reg_status_2025,
        MAX(CASE WHEN ce1.StudentID IS NOT NULL THEN 'REGISTERED' ELSE 'NO REGISTRATION' END) AS reg_status_2024,
        MAX(CASE WHEN ce2.StudentID IS NOT NULL THEN 'REGISTERED' ELSE 'NO REGISTRATION' END) AS reg_status_2023
    FROM filtered_students bi
    LEFT JOIN `course-electives` ce ON bi.ID = ce.StudentID AND (ce.`Year` = 2025 OR ce.`EnrolmentDate` > '2025-01-01')
    LEFT JOIN `course-electives` ce1 ON bi.ID = ce1.StudentID AND ce1.`Year` = 2024
    LEFT JOIN `course-electives` ce2 ON bi.ID = ce2.StudentID AND ce2.`Year` = 2023
    LEFT JOIN `course-electives` ce3 ON bi.ID = ce3.StudentID AND ce3.`Year` = 2026
    GROUP BY bi.ID
),
student_results AS (
    SELECT 
        bi.ID,
        MAX(CASE 
            WHEN EXISTS (
                SELECT 1 
                FROM `grades-published` gp2 
                WHERE gp2.StudentNo = bi.ID 
                    AND gp2.Grade IN ('NE','F','D+','D','DEF')
                    AND NOT EXISTS (
                        SELECT 1 
                        FROM `grades-published` gp3 
                        WHERE gp3.StudentNo = gp2.StudentNo 
                            AND gp3.CourseNo = gp2.CourseNo 
                            AND gp3.Grade NOT IN ('NE','F','D+','D','DEF')
                    )
            ) THEN 'REPEAT COURSE'
            WHEN NOT EXISTS (SELECT 1 FROM `grades-published` WHERE StudentNo = bi.ID) THEN 'NOT APPLICABLE'
            ELSE 'CLEARED'
        END) AS results_status,
        COALESCE(
            NULLIF(
                MAX(CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM `grades-published` gp2 
                        WHERE gp2.StudentNo = bi.ID 
                            AND gp2.Grade IN ('NE','F','D+','D','DEF')
                            AND NOT EXISTS (
                                SELECT 1 
                                FROM `grades-published` gp3 
                                WHERE gp3.StudentNo = gp2.StudentNo 
                                    AND gp3.CourseNo = gp2.CourseNo 
                                    AND gp3.Grade NOT IN ('NE','F','D+','D','DEF')
                            )
                    ) THEN c.`Year`
                    WHEN my.MaxYear < (c.`Year` + 1) THEN 'STUDENT HAS GRADUATED'
                    WHEN NOT EXISTS (SELECT 1 FROM `grades-published` WHERE StudentNo = bi.ID) THEN 'NOT APPLICABLE'
                    ELSE c.`Year` + 1
                END),
                NULL
            ),
            'NOT APPLICABLE'
        ) AS EstimatedCurrentYearOfStudy
    FROM filtered_students bi
    LEFT JOIN `grades-published` gp ON gp.StudentNo = bi.ID AND gp.AcademicYear = 2024
    LEFT JOIN courses c ON c.Name = gp.CourseNo
    LEFT JOIN max_year_data my ON my.StudentID = bi.ID
    GROUP BY bi.ID
),
program_study_data AS (
    SELECT
        sslink.StudentID,
        MAX(s.Name) AS ProgrammeName,
        MAX(s.ShortName) AS ProgrammeCode,
        MAX(sch.Description) AS School
    FROM filtered_students AS bi
    INNER JOIN student_study_links AS sslink ON sslink.StudentID = bi.ID
    INNER JOIN `study` AS s ON sslink.StudyID = s.ID
    INNER JOIN `schools` AS sch ON s.ParentID = sch.ID
    GROUP BY sslink.StudentID
),
final_year_graduation_status AS (
    SELECT 
        bi.ID,
        CASE 
            -- First check if they took final year courses in their most recent academic year
            WHEN EXISTS (
                SELECT 1 
                FROM `grades-published` gp
                JOIN courses c ON c.Name = gp.CourseNo
                JOIN max_year_data my ON my.StudentID = gp.StudentNo
                WHERE gp.StudentNo = bi.ID
                AND gp.AcademicYear = (SELECT MAX(AcademicYear) FROM `grades-published` WHERE StudentNo = bi.ID)
                AND c.Year = my.MaxYear  -- Confirms these are final year courses
            ) THEN 
                -- Then check if they passed all those final year courses
                CASE WHEN NOT EXISTS (
                    SELECT 1 
                    FROM `grades-published` gp
                    JOIN courses c ON c.Name = gp.CourseNo
                    JOIN max_year_data my ON my.StudentID = gp.StudentNo
                    WHERE gp.StudentNo = bi.ID
                    AND gp.AcademicYear = (SELECT MAX(AcademicYear) FROM `grades-published` WHERE StudentNo = bi.ID)
                    AND c.Year = my.MaxYear  -- Final year courses
                    AND gp.Grade IN ('D', 'F', 'NE', 'D+', 'DQ+', 'DEF')  -- Failed grades
                ) THEN 'GRADUATED'
                ELSE 'RETURNING_STUDENT_REPEATING_COURSES'
                END
            -- Student hasn't taken final year courses yet
            ELSE 'NOT_IN_FINAL_YEAR'
        END AS GraduationStatus,
        
        -- Count of failed final year courses for additional insight
        (
            SELECT COUNT(*)
            FROM `grades-published` gp
            JOIN courses c ON c.Name = gp.CourseNo
            JOIN max_year_data my ON my.StudentID = gp.StudentNo
            WHERE gp.StudentNo = bi.ID
            AND c.Year = my.MaxYear  -- Final year courses
            AND gp.Grade IN ('D', 'F', 'NE', 'D+', 'DQ+', 'DEF')  -- Failed grades
        ) AS FailedFinalYearCourses,
        
        -- Most recent academic year the student took courses in
        (
            SELECT MAX(AcademicYear)
            FROM `grades-published` 
            WHERE StudentNo = bi.ID
        ) AS MostRecentAcademicYear
    FROM filtered_students bi
)
SELECT DISTINCT 
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.ID,
    bi.Sex,
    bi.GovernmentID,
    bi.PrivateEmail,
    bi.MobilePhone,
    psd.ProgrammeName,
    psd.ProgrammeCode,
    psd.School,
    bi.StudyType,
    syi.current_year as "2024 Year Of Study",
    syi.YearReported,
    syi.MaxYear,
    syi.RegisteredCurrentYearOfStudy,
    CASE
        WHEN syi.RegisteredCurrentYearOfStudy = syi.MaxYear THEN 'FinalistByRegistration'
        ELSE 'Not In Final Year'
    END AS RegistrationFinalistStatus,
    
    CASE
        WHEN (syi.id_prefix IN ('220','230','240','190') AND gp.StudentNo IS NOT NULL) THEN 'ACTIVE STUDENT'
        WHEN (syi.id_prefix = '250' AND a.StudentID IS NOT NULL) THEN 'ACTIVE STUDENT'
        ELSE 'INACTIVE ACCOUNT'
    END AS StudentStatusEstimation,
    
    CASE
        WHEN syi.current_year + 1 = syi.MaxYear THEN 'FinalistByEstimation'
        ELSE 'Not In Final Year'
    END AS EstimationFinalistStatus,
    
    sr.EstimatedCurrentYearOfStudy,
    syi.StudentType,
    sr.results_status AS `2024ResultsStatus`,
    srs.ResearchStatus,
    syi.GraduationStatus,
    
    -- New fields from final_year_graduation_status
    fygs.GraduationStatus AS "Final Year Graduation Status",
    fygs.FailedFinalYearCourses AS "Failed Final Year Courses",
    fygs.MostRecentAcademicYear AS "Most Recent Academic Year",
    
    CASE 
        WHEN fygs.GraduationStatus = 'GRADUATED' THEN 'GRADUATED'
        WHEN fygs.GraduationStatus = 'RETURNING_STUDENT_REPEATING_COURSES' THEN 'RETURNING - REPEATING COURSES'
        WHEN syi.id_prefix = '250' THEN 'NEW STUDENT'
        WHEN EXISTS (SELECT 1 FROM `grades-published` WHERE StudentNo = bi.ID) THEN 'RETURNING STUDENT'
        ELSE 'NO ACADEMIC HISTORY'
    END AS "Student Academic Status",
    
    rs.reg_status_2026 AS "Registration Status 2026", 
    rs.reg_status_2025 AS "Registration Status 2025",
    rs.reg_status_2024 AS "Registration Status 2024",
    rs.reg_status_2023 AS "Registration Status 2023",
-- Calculate invoices based on results history - invoice for years with results, regardless of graduation status
CASE 
    WHEN syi.id_prefix = '250' THEN 0
    WHEN EXISTS (SELECT 1 FROM `grades-published` WHERE StudentNo = bi.ID AND AcademicYear = 2024) THEN
        CASE
            WHEN syi.id_prefix = '190' THEN 
                (SELECT MAX(YEAR2) FROM program_data_lookup 
                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
            ELSE 
                (SELECT MAX(YEAR2) FROM program_data_lookup 
                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
        END
    ELSE 0
END as "2024 Invoice",

CASE 
    -- New students with '250' prefix get YEAR1 fee
    WHEN syi.id_prefix = '250' THEN 
        (SELECT MAX(YEAR1) FROM program_data_lookup 
         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
    
    -- Students with 2024 academic records get YEAR2 fee
    WHEN EXISTS (SELECT 1 FROM `grades-published` WHERE StudentNo = bi.ID AND AcademicYear = 2024) THEN
        CASE
            WHEN syi.id_prefix = '190' THEN 
                (SELECT MAX(YEAR2) FROM program_data_lookup 
                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
            ELSE 
                (SELECT MAX(YEAR2) FROM program_data_lookup 
                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
        END
        
    -- Students with ANY previous academic records are considered returning and get YEAR2 fee
    WHEN EXISTS (SELECT 1 FROM `grades-published` WHERE StudentNo = bi.ID) THEN
        CASE
            WHEN syi.id_prefix = '190' THEN 
                (SELECT MAX(YEAR2) FROM program_data_lookup 
                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
            ELSE 
                (SELECT MAX(YEAR2) FROM program_data_lookup 
                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
        END
    
    -- Only students with no academic history get YEAR1 fee
    ELSE
        (SELECT MAX(YEAR1) FROM program_data_lookup 
         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
END as "2025 Invoice",

CASE 
    WHEN syi.id_prefix = '250' THEN 0
    WHEN syi.id_prefix = '240' THEN 0
    WHEN syi.id_prefix = '230' THEN 0
    WHEN EXISTS (SELECT 1 FROM `grades-published` WHERE StudentNo = bi.ID AND AcademicYear = 2022) THEN
        CASE
            WHEN syi.id_prefix = '220' THEN 
                (SELECT MAX(YEAR1) FROM program_data_lookup 
                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
            WHEN syi.id_prefix = '190' THEN 
                (SELECT MAX(YEAR1) FROM program_data_lookup 
                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
            ELSE 0
        END
    ELSE 0
END as "Invoice Before 2024",

-- Calculate historical invoices based only on years with results
(
    SELECT COALESCE(SUM(
        CASE 
            WHEN yr = 2024 THEN 
                CASE
                    WHEN syi.id_prefix = '190' THEN 
                        (SELECT MAX(YEAR2) FROM program_data_lookup 
                         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
                    ELSE 
                        (SELECT MAX(YEAR2) FROM program_data_lookup 
                         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
                END
            WHEN yr = 2023 THEN 
                CASE
                    WHEN syi.id_prefix = '190' THEN 
                        (SELECT MAX(YEAR2) FROM program_data_lookup 
                         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
                    ELSE 
                        (SELECT MAX(YEAR2) FROM program_data_lookup 
                         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
                END
            WHEN yr = 2022 THEN 
                CASE
                    WHEN syi.id_prefix = '190' THEN 
                        (SELECT MAX(YEAR2) FROM program_data_lookup 
                         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
                    ELSE 
                        (SELECT MAX(YEAR2) FROM program_data_lookup 
                         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
                END
            WHEN yr = 2021 THEN 
                CASE
                    WHEN syi.id_prefix = '190' THEN 
                        (SELECT MAX(YEAR1) FROM program_data_lookup 
                         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
                    ELSE 
                        (SELECT MAX(YEAR1) FROM program_data_lookup 
                         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
                END
            ELSE 0
        END
    ), 0)
    FROM (
        SELECT DISTINCT AcademicYear AS yr
        FROM `grades-published`
        WHERE StudentNo = bi.ID AND AcademicYear < 2025
    ) years_with_results
) as "Invoice Before 2025",

-- Total invoice calculation based on count of distinct academic years with results
CASE 
    -- Newly admitted (250%) students always get YEAR1 invoice
    WHEN syi.id_prefix = '250' THEN 
        (SELECT MAX(YEAR1) FROM program_data_lookup 
         WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
    -- For other returning students, calculate based on years with results
    ELSE 
        (
            WITH YearsWithResults AS (
                SELECT COUNT(DISTINCT AcademicYear) AS YearCount
                FROM `grades-published`
                WHERE StudentNo = bi.ID
            )
            SELECT
                CASE
                    -- If no results, default to YEAR1 fee for their type
                    WHEN YearCount = 0 THEN
                        CASE 
                            WHEN syi.id_prefix = '190' THEN 
                                (SELECT MAX(YEAR1) FROM program_data_lookup 
                                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
                            ELSE 
                                (SELECT MAX(YEAR1) FROM program_data_lookup 
                                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
                        END
                    -- If 1 year of results, charge YEAR1 fee
                    WHEN YearCount = 1 THEN
                        CASE 
                            WHEN syi.id_prefix = '190' THEN 
                                (SELECT MAX(YEAR1) FROM program_data_lookup 
                                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1)
                            ELSE 
                                (SELECT MAX(YEAR1) FROM program_data_lookup 
                                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0)
                        END
                    -- For multiple years: YEAR1 + YEAR2*(years-1)
                    ELSE
                        CASE 
                            WHEN syi.id_prefix = '190' THEN 
                                (SELECT MAX(YEAR1) FROM program_data_lookup 
                                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1) +
                                (SELECT MAX(YEAR2) FROM program_data_lookup 
                                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 1) * (YearCount - 1)
                            ELSE 
                                (SELECT MAX(YEAR1) FROM program_data_lookup 
                                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0) +
                                (SELECT MAX(YEAR2) FROM program_data_lookup 
                                 WHERE ProgrammeCode = psd.ProgrammeCode AND StudyType = bi.StudyType AND is_190 = 0) * (YearCount - 1)
                        END
                END
            FROM YearsWithResults
        )
END as "Total Invoice"
FROM 
    filtered_students bi
JOIN student_year_info syi ON syi.ID = bi.ID
JOIN registration_status rs ON rs.ID = bi.ID
JOIN student_results sr ON sr.ID = bi.ID
JOIN program_study_data psd ON psd.StudentID = bi.ID
JOIN student_research_status srs ON srs.ID = bi.ID
JOIN final_year_graduation_status fygs ON fygs.ID = bi.ID
LEFT JOIN `grades-published` gp ON gp.StudentNo = bi.ID AND gp.AcademicYear = 2024
LEFT JOIN `applicants` a ON bi.ID = a.StudentID AND bi.id_prefix = '250' AND a.Progress = 'Accepted';