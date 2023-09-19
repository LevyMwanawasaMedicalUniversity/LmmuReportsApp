SELECT
    bi.FirstName,
    bi.MiddleName,
    bi.Surname,
    bi.PrivateEmail,
    bi.ID,
    bi.StudyType,
    IF(CHAR_LENGTH(bi.ID) >= 4, 'Yes', 'No') AS 'ID_Valid',
    IFNULL(t.NumberOfPayments, 0) AS NumberOfPayments,
    IFNULL(t.TotalPayments, 0) AS TotalPayments,
    IFNULL((t.TotalPayments / 19600) * 100, 0) AS 'Percentage Paid',
    IF((ssl2.Year IN (1, 2, 3, 4)), 'Yes', 'No') AS 'Invoice',
    IFNULL(ssl2.Year, 0) AS 'Year Of Study',
    IFNULL(s.Programme, '') AS Programme,
    IFNULL(s2.School, '') AS School,
    IFNULL(c.CourseCode, '') AS CourseCode,
    IFNULL(c.CourseName, '') AS CourseName,
    IFNULL(pcl.ProgramCode, '') AS ProgramCode,
    IFNULL(p.ProgrammeCode, '') AS ProgrammeCode,
    CASE
        WHEN ((bi.ID LIKE '230%' OR bi.ID LIKE '220%' OR bi.ID LIKE '210%') AND bi.StudyType = 'Fulltime') THEN
            CASE
                WHEN pcl.ProgramCode = 'AdvDipCA' THEN 14570
                WHEN pcl.ProgramCode = 'AdvDipCO' THEN 14570
                WHEN pcl.ProgramCode = 'AdvDipONC' THEN 11481
                WHEN pcl.ProgramCode = 'AdvDipOPHNUR' THEN 14706
                WHEN pcl.ProgramCode = 'BAGC' THEN 12350
                WHEN pcl.ProgramCode = 'BScCA' THEN 19567
                WHEN pcl.ProgramCode = 'BDS' THEN 29700
                WHEN pcl.ProgramCode = 'MBChB' THEN 29700
                WHEN pcl.ProgramCode = 'MMEDGS' THEN 22625
                WHEN pcl.ProgramCode = 'MMEDID' THEN 22625
                WHEN pcl.ProgramCode = 'MMEDIM' THEN 22625
                WHEN pcl.ProgramCode = 'MMEDOB' THEN 22625
                WHEN pcl.ProgramCode = 'MMEDPCH' THEN 22625
                WHEN pcl.ProgramCode = 'MSCFE' THEN 32950
                WHEN pcl.ProgramCode = 'MSCHPE' THEN 16625
                WHEN pcl.ProgramCode = 'MSCOPTH' THEN 19625
                WHEN pcl.ProgramCode = 'MSCOPTO' THEN 19625
                WHEN pcl.ProgramCode = 'PDDIPEMC' THEN 16625
                WHEN pcl.ProgramCode = 'PHDHPE' THEN 17625
                WHEN pcl.ProgramCode = 'Bpharm' THEN 19567
                WHEN pcl.ProgramCode = 'BPHY' THEN 19567
                WHEN pcl.ProgramCode = 'BScBMS' THEN 22400
                WHEN pcl.ProgramCode = 'BScND' THEN 19567
                WHEN pcl.ProgramCode = 'BScCO' THEN 19567
                WHEN pcl.ProgramCode = 'BScCS' THEN 19567
                WHEN pcl.ProgramCode = 'BScMHCP' THEN 19567
                WHEN pcl.ProgramCode = 'BScMiD' THEN 19673
                WHEN pcl.ProgramCode = 'BScNUR' THEN 19673
                WHEN pcl.ProgramCode = 'BScON' THEN 19673
                WHEN pcl.ProgramCode = 'BScOPT' THEN 19567
                WHEN pcl.ProgramCode = 'BScPH' THEN 16700
                WHEN pcl.ProgramCode = 'BScPHNUR' THEN 16806
                WHEN pcl.ProgramCode = 'BScPHN' THEN 16700
                WHEN pcl.ProgramCode = 'BScRAD' THEN 19567
                WHEN pcl.ProgramCode = 'BScSLT' THEN 19567
                WHEN pcl.ProgramCode = 'BScEH' THEN 16700
                WHEN pcl.ProgramCode = 'BScMHN' THEN 19673
                WHEN pcl.ProgramCode = 'CertCHAHM' THEN 8040
                WHEN pcl.ProgramCode = 'CertHCHW' THEN 8040
                WHEN pcl.ProgramCode = 'CertDA' THEN 8040
                WHEN pcl.ProgramCode = 'CertEMC' THEN 8040
                WHEN pcl.ProgramCode = 'DipBMS' THEN 14570
                WHEN pcl.ProgramCode = 'DipCMSG' THEN 12570
                WHEN pcl.ProgramCode = 'DipCMSP' THEN 12350
                WHEN pcl.ProgramCode = 'DipDTech' THEN 12570
                WHEN pcl.ProgramCode = 'DipDTh' THEN 12570
                WHEN pcl.ProgramCode = 'DipEMC' THEN 12570
                WHEN pcl.ProgramCode = 'DipEH' THEN 12570
                WHEN pcl.ProgramCode = 'DipGC' THEN 12350
                WHEN pcl.ProgramCode = 'DipMHN' THEN 12706
                WHEN pcl.ProgramCode = 'DipMID' THEN 12706
                WHEN pcl.ProgramCode = 'DipOPT' THEN 12570
                WHEN pcl.ProgramCode = 'DipPH' THEN 12570
                WHEN pcl.ProgramCode = 'DipPHN' THEN 12706
                WHEN pcl.ProgramCode = 'DipRN' THEN 12706
                WHEN pcl.ProgramCode = 'DipONC' THEN 12706
                WHEN pcl.ProgramCode = 'DIPPO' THEN 12570
                ELSE 0
            END
        WHEN ((bi.ID LIKE '230%' OR bi.ID LIKE '220%' OR bi.ID LIKE '210%') AND bi.StudyType = 'Distance') THEN
            CASE
                WHEN pcl.ProgramCode = 'BScEH' THEN 12400
                WHEN pcl.ProgramCode = 'BScPH' THEN 12400
                WHEN pcl.ProgramCode = 'BScPHN' THEN 12400
                WHEN pcl.ProgramCode = 'BScMHCP' THEN 12400
                WHEN pcl.ProgramCode = 'BScND' THEN 12400
                WHEN pcl.ProgramCode = 'BAGC' THEN 12350
                WHEN pcl.ProgramCode = 'BPHY' THEN 11350
                WHEN pcl.ProgramCode = 'BScRAD' THEN 11350
                WHEN pcl.ProgramCode = 'BScNUR' THEN 12506
                WHEN pcl.ProgramCode = 'BScPHNUR' THEN 12506
                WHEN pcl.ProgramCode = 'BScMID' THEN 12506
                WHEN pcl.ProgramCode = 'BScMHN' THEN 12506
                WHEN pcl.ProgramCode = 'DipCMSG' THEN 10570
                WHEN pcl.ProgramCode = 'DipCMSP' THEN 10570
                WHEN pcl.ProgramCode = 'DipCMSP' THEN 14706
                WHEN pcl.ProgramCode = 'DipHNP' THEN 14706
                WHEN pcl.ProgramCode = 'MPH' THEN 14625
                WHEN pcl.ProgramCode = 'MScID' THEN 16575
                WHEN pcl.ProgramCode = 'BScBMS' THEN 12400
                ELSE 0
            END
        WHEN (bi.ID LIKE '190%' AND bi.StudyType = 'Distance') THEN
            CASE
                WHEN pcl.ProgramCode = 'BScEH' THEN 13470
                WHEN pcl.ProgramCode = 'BScPH' THEN 13470
                WHEN pcl.ProgramCode = 'BScPHN' THEN 12400
                WHEN pcl.ProgramCode = 'DipCMSG' THEN 9670
                WHEN pcl.ProgramCode = 'DipEH' THEN 9670
                ELSE 0
            END
        WHEN (bi.ID LIKE '190%' AND bi.StudyType = 'Fulltime') THEN
            CASE
                WHEN pcl.ProgramCode = 'BScEH' THEN 15770
                WHEN pcl.ProgramCode = 'BScPH' THEN 15770
                WHEN pcl.ProgramCode = 'CertCHAHM' THEN 7170
                WHEN pcl.ProgramCode = 'CertDA' THEN 7170
                WHEN pcl.ProgramCode = 'CertEMC' THEN 7170
                WHEN pcl.ProgramCode = 'BScPHN' THEN 15770
                WHEN pcl.ProgramCode = 'BScBMS' THEN 21470
                WHEN pcl.ProgramCode = 'BScEH' THEN 15770
                WHEN pcl.ProgramCode = 'DipEH' THEN 11670
                WHEN pcl.ProgramCode = 'BScND' THEN 18637
                WHEN pcl.ProgramCode = 'BScCA' THEN 18637
                WHEN pcl.ProgramCode = 'BScOPT' THEN 18637
                WHEN pcl.ProgramCode = 'BScCO' THEN 18637
                WHEN pcl.ProgramCode = 'BScCS' THEN 18637
                WHEN pcl.ProgramCode = 'BAGC' THEN 15550
                WHEN pcl.ProgramCode = 'DipGC' THEN 11450
                WHEN pcl.ProgramCode = 'BScNUR' THEN 18773
                WHEN pcl.ProgramCode = 'BScON' THEN 18773
                WHEN pcl.ProgramCode = 'DipMID' THEN 11806
                WHEN pcl.ProgramCode = 'BScPHNUR' THEN 15906
                WHEN pcl.ProgramCode = 'BScMHN' THEN 18773
                WHEN pcl.ProgramCode = 'BScNUR' THEN 18773
                WHEN pcl.ProgramCode = 'BScON' THEN 18773
                WHEN pcl.ProgramCode = 'BScMID' THEN 18773
                WHEN pcl.ProgramCode = 'BScMHCP' THEN 18637
                WHEN pcl.ProgramCode = 'DipMID' THEN 11806
                WHEN pcl.ProgramCode = 'MBCHB' THEN 28770
                WHEN pcl.ProgramCode = 'MScHPE' THEN 16625
                WHEN pcl.ProgramCode = 'PHdHPE' THEN 17625
                WHEN pcl.ProgramCode = 'DipPHN' THEN 11806
                WHEN pcl.ProgramCode = 'DipRN' THEN 11806
                WHEN pcl.ProgramCode = 'DipMHN' THEN 11806
                WHEN pcl.ProgramCode = 'BScBMS' THEN 21470
                WHEN pcl.ProgramCode = 'DipBMS' THEN 13670
                WHEN pcl.ProgramCode = 'DipCMSG' THEN 11670
                WHEN pcl.ProgramCode = 'DipCMSP' THEN 11670
                WHEN pcl.ProgramCode = 'DipDTECH' THEN 11670
                WHEN pcl.ProgramCode = 'DipDTH' THEN 11670
                WHEN pcl.ProgramCode = 'DipOPT' THEN 11670
                WHEN pcl.ProgramCode = 'DipPH' THEN 11670
                WHEN pcl.ProgramCode = 'DipEMC' THEN 11670
                WHEN pcl.ProgramCode = 'DiGC' THEN 11450
                ELSE 0
            END
        ELSE 0
    END AS YEAR1,
    CASE
        WHEN ((bi.ID LIKE '230%' OR bi.ID LIKE '220%' OR bi.ID LIKE '210%') AND bi.StudyType = 'Fulltime') THEN
            CASE
                WHEN pcl.ProgramCode = 'AdvDipCA' THEN 14350
                WHEN pcl.ProgramCode = 'AdvDipCO' THEN 14350
                WHEN pcl.ProgramCode = 'AdvDipONC' THEN 11481
                WHEN pcl.ProgramCode = 'AdvDipOPHNUR' THEN 14350
                WHEN pcl.ProgramCode = 'BAGC' THEN 12350
                WHEN pcl.ProgramCode = 'BScCA' THEN 19317
                WHEN pcl.ProgramCode = 'BDS' THEN 29450
                WHEN pcl.ProgramCode = 'MBChB' THEN 29450
                WHEN pcl.ProgramCode = 'MMEDGS' THEN 22625
                WHEN pcl.ProgramCode = 'MMEDID' THEN 22625
                WHEN pcl.ProgramCode = 'MMEDIM' THEN 22625
                WHEN pcl.ProgramCode = 'MMEDOB' THEN 22625
                WHEN pcl.ProgramCode = 'MMEDPCH' THEN 22625
                WHEN pcl.ProgramCode = 'MSCFE' THEN 32950
                WHEN pcl.ProgramCode = 'MSCHPE' THEN 16625
                WHEN pcl.ProgramCode = 'MSCOPTH' THEN 19625
                WHEN pcl.ProgramCode = 'MSCOPTO' THEN 19625
                WHEN pcl.ProgramCode = 'PDDIPEMC' THEN 16625
                WHEN pcl.ProgramCode = 'PHDHPE' THEN 17625
                WHEN pcl.ProgramCode = 'Bpharm' THEN 19317
                WHEN pcl.ProgramCode = 'BPHY' THEN 19317
                WHEN pcl.ProgramCode = 'BScBMS' THEN 22150
                WHEN pcl.ProgramCode = 'BScND' THEN 19317
                WHEN pcl.ProgramCode = 'BScCO' THEN 19317
                WHEN pcl.ProgramCode = 'BScCS' THEN 19317
                WHEN pcl.ProgramCode = 'BScMHCP' THEN 19317
                WHEN pcl.ProgramCode = 'BScMiD' THEN 19317
                WHEN pcl.ProgramCode = 'BScNUR' THEN 19317
                WHEN pcl.ProgramCode = 'BScON' THEN 19317
                WHEN pcl.ProgramCode = 'BScOPT' THEN 19317
                WHEN pcl.ProgramCode = 'BScPH' THEN 16450
                WHEN pcl.ProgramCode = 'BScPHNUR' THEN 16450
                WHEN pcl.ProgramCode = 'BScPHN' THEN 16450
                WHEN pcl.ProgramCode = 'BScRAD' THEN 19317
                WHEN pcl.ProgramCode = 'BScSLT' THEN 19317
                WHEN pcl.ProgramCode = 'BScEH' THEN 16450
                WHEN pcl.ProgramCode = 'BScMHN' THEN 19317
                WHEN pcl.ProgramCode = 'CertCHAHM' THEN 8040
                WHEN pcl.ProgramCode = 'CertHCHW' THEN 8040
                WHEN pcl.ProgramCode = 'CertDA' THEN 8040
                WHEN pcl.ProgramCode = 'CertEMC' THEN 8040
                WHEN pcl.ProgramCode = 'DipBMS' THEN 14350
                WHEN pcl.ProgramCode = 'DipCMSG' THEN 12350
                WHEN pcl.ProgramCode = 'DipCMSP' THEN 12350
                WHEN pcl.ProgramCode = 'DipDTech' THEN 12350
                WHEN pcl.ProgramCode = 'DipDTh' THEN 12350
                WHEN pcl.ProgramCode = 'DipEMC' THEN 12350
                WHEN pcl.ProgramCode = 'DipEH' THEN 12350
                WHEN pcl.ProgramCode = 'DipGC' THEN 12350
                WHEN pcl.ProgramCode = 'DipMHN' THEN 12350
                WHEN pcl.ProgramCode = 'DipMID' THEN 12350
                WHEN pcl.ProgramCode = 'DipOPT' THEN 12350
                WHEN pcl.ProgramCode = 'DipPH' THEN 12350
                WHEN pcl.ProgramCode = 'DipPHN' THEN 12350
                WHEN pcl.ProgramCode = 'DipRN' THEN 12350
                WHEN pcl.ProgramCode = 'DipONC' THEN 12350
                WHEN pcl.ProgramCode = 'DIPPO' THEN 12350
                ELSE 0
            END
        WHEN ((bi.ID LIKE '230%' OR bi.ID LIKE '220%' OR bi.ID LIKE '210%') AND bi.StudyType = 'Distance') THEN
            CASE
                WHEN pcl.ProgramCode = 'BScEH' THEN 12150
                WHEN pcl.ProgramCode = 'BScPH' THEN 12150
                WHEN pcl.ProgramCode = 'BScPHN' THEN 12150
                WHEN pcl.ProgramCode = 'BScMHCP' THEN 12150
                WHEN pcl.ProgramCode = 'BScND' THEN 12150
                WHEN pcl.ProgramCode = 'BAGC' THEN 12350
                WHEN pcl.ProgramCode = 'BPHY' THEN 11100
                WHEN pcl.ProgramCode = 'BScRAD' THEN 11100
                WHEN pcl.ProgramCode = 'BScNUR' THEN 12150
                WHEN pcl.ProgramCode = 'BScPHNUR' THEN 12150
                WHEN pcl.ProgramCode = 'BScMID' THEN 12150
                WHEN pcl.ProgramCode = 'BScMHN' THEN 12150
                WHEN pcl.ProgramCode = 'DipCMSG' THEN 10350
                WHEN pcl.ProgramCode = 'DipCMSP' THEN 10350
                WHEN pcl.ProgramCode = 'DipCMSP' THEN 14706
                WHEN pcl.ProgramCode = 'DipHNP' THEN 14706
                WHEN pcl.ProgramCode = 'MPH' THEN 14625
                WHEN pcl.ProgramCode = 'MScID' THEN 16575
                WHEN pcl.ProgramCode = 'BScBMS' THEN 12150
                ELSE 0
            END
        WHEN (bi.ID LIKE '190%' AND bi.StudyType = 'Distance') THEN
            CASE
                WHEN pcl.ProgramCode = 'BScEH' THEN 10925
                WHEN pcl.ProgramCode = 'BScPH' THEN 10925
                WHEN pcl.ProgramCode = 'BScPHN' THEN 12150
                WHEN pcl.ProgramCode = 'DipCMSG' THEN 9125
                WHEN pcl.ProgramCode = 'DipEH' THEN 9125
                ELSE 0
            END
        WHEN (bi.ID LIKE '190%' AND bi.StudyType = 'Fulltime') THEN
            CASE
                WHEN pcl.ProgramCode = 'BScEH' THEN 15225
                WHEN pcl.ProgramCode = 'BScPH' THEN 15225
                WHEN pcl.ProgramCode = 'CertCHAHM' THEN 7170
                WHEN pcl.ProgramCode = 'CertDA' THEN 7170
                WHEN pcl.ProgramCode = 'CertEMC' THEN 7170
                WHEN pcl.ProgramCode = 'BScPHN' THEN 15225
                WHEN pcl.ProgramCode = 'BScBMS' THEN 20925
                WHEN pcl.ProgramCode = 'BScEH' THEN 15225
                WHEN pcl.ProgramCode = 'DipEH' THEN 11125
                WHEN pcl.ProgramCode = 'BScND' THEN 18092
                WHEN pcl.ProgramCode = 'BScCA' THEN 18092
                WHEN pcl.ProgramCode = 'BScOPT' THEN 18092
                WHEN pcl.ProgramCode = 'BScCO' THEN 18092
                WHEN pcl.ProgramCode = 'BScCS' THEN 18092
                WHEN pcl.ProgramCode = 'BAGC' THEN 12350
                WHEN pcl.ProgramCode = 'DipGC' THEN 11450
                WHEN pcl.ProgramCode = 'BScNUR' THEN 18092
                WHEN pcl.ProgramCode = 'BScON' THEN 18092
                WHEN pcl.ProgramCode = 'DipMID' THEN 11125
                WHEN pcl.ProgramCode = 'BScPHNUR' THEN 15225
                WHEN pcl.ProgramCode = 'BScMHN' THEN 18092
                WHEN pcl.ProgramCode = 'BScNUR' THEN 18092
                WHEN pcl.ProgramCode = 'BScON' THEN 18092
                WHEN pcl.ProgramCode = 'BScMID' THEN 18092
                WHEN pcl.ProgramCode = 'BScMHCP' THEN 18092
                WHEN pcl.ProgramCode = 'DipMID' THEN 11125
                WHEN pcl.ProgramCode = 'MBCHB' THEN 28225
                WHEN pcl.ProgramCode = 'MScHPE' THEN 16625
                WHEN pcl.ProgramCode = 'PHdHPE' THEN 17625
                WHEN pcl.ProgramCode = 'DipPHN' THEN 11125
                WHEN pcl.ProgramCode = 'DipRN' THEN 11125
                WHEN pcl.ProgramCode = 'DipMHN' THEN 11125
                WHEN pcl.ProgramCode = 'BScBMS' THEN 20925
                WHEN pcl.ProgramCode = 'DipBMS' THEN 13125
                WHEN pcl.ProgramCode = 'DipCMSG' THEN 11125
                WHEN pcl.ProgramCode = 'DipCMSP' THEN 11125
                WHEN pcl.ProgramCode = 'DipDTECH' THEN 11125
                WHEN pcl.ProgramCode = 'DipDTH' THEN 11125
                WHEN pcl.ProgramCode = 'DipOPT' THEN 11125
                WHEN pcl.ProgramCode = 'DipPH' THEN 11125
                WHEN pcl.ProgramCode = 'DipEMC' THEN 11125
                WHEN pcl.ProgramCode = 'DiGC' THEN 11450
                ELSE 0
            END
        ELSE 0
    END AS YEAR2
FROM
    bInfo bi
LEFT JOIN
    transcripts t ON bi.ID = t.ID
LEFT JOIN
    studentStudyLevel2 ssl2 ON bi.ID = ssl2.ID
LEFT JOIN
    ProgrammesCurrList pcl ON bi.ID = pcl.ID
LEFT JOIN
    programmes p ON pcl.ProgramCode = p.ProgrammeCode
LEFT JOIN
    schools s ON p.SchoolID = s.SchoolID
LEFT JOIN
    course c ON pcl.CourseCode = c.CourseCode
LEFT JOIN
    schools s2 ON c.SchoolID = s2.SchoolID
WHERE
    (bi.ID LIKE '230%' OR bi.ID LIKE '220%' OR bi.ID LIKE '210%' OR bi.ID LIKE '190%')
    AND (bi.StudyType = 'Fulltime' OR bi.StudyType = 'Distance');