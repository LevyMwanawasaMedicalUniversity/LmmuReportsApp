SELECT
    bi.FirstName, 
    bi.MiddleName,
    bi.Surname,	
    bi.ID,
    bi.GovernmentID,
    bi.PrivateEmail,
    bi.MobilePhone,
    bi.StudyType,
    p.ProgramName as "2022 Cleared Results",
    s.Name as 'Programme',
    s2.Name as 'School'
FROM
    `basic-information` bi 
INNER JOIN grades g ON bi.ID = g.StudentNo 
INNER JOIN courses c ON c.Name = g.CourseNo and g.AcademicYear = 2022 
INNER JOIN `program-course-link` pcl ON c.ID = pcl.CourseID
INNER JOIN programmes p ON p.ID = pcl.ProgramID
INNER JOIN `study-program-link` spl on spl.ProgramID = p.ID
INNER JOIN study s on s.ID  = spl.StudyID 
INNER JOIN schools s2 on s2.ID = s.ParentID
WHERE
    g.AcademicYear != 2023
    AND g.Grade NOT IN ('D+', 'D', 'F', 'DQ', 'NE','WP')
    AND (p.ProgramName = 'BSCRAD-FT-2023-Y4'
        OR p.ProgramName = 'BSCRAD-DE-2023-Y4'
        OR p.ProgramName = 'BSCRAD-FT-2019-Y4'
        OR p.ProgramName = 'BSCRAD-DE-2019-Y4'
        
        OR p.ProgramName = 'BAGC-FT-2023-Y3'
        OR p.ProgramName = 'BAGC-DE-2023-Y3'
        OR p.ProgramName = 'BAGC-FT-2019-Y3'
        OR p.ProgramName = 'BAGC-DE-2019-Y3'
        
        OR p.ProgramName = 'BPHARM-FT-2023-Y4'
        OR p.ProgramName = 'BPHARM-DE-2023-Y4'
        OR p.ProgramName = 'BPHARM-FT-2019-Y4'
        OR p.ProgramName = 'BPHARM-DE-2019-Y4'
        
        OR p.ProgramName = 'BPHY-FT-2023-Y4'
        OR p.ProgramName = 'BPHY-DE-2023-Y4'
        OR p.ProgramName = 'BPHY-FT-2019-Y4'
        OR p.ProgramName = 'BPHY-DE-2019-Y4'
        
        OR p.ProgramName = 'BSCND-FT-2023-Y4'
        OR p.ProgramName = 'BSCND-DE-2023-Y4'
        OR p.ProgramName = 'BSCND-FT-2019-Y4'
        OR p.ProgramName = 'BSCND-DE-2019-Y4'
        
        OR p.ProgramName = 'BSCCA-FT-2023-Y3'
        OR p.ProgramName = 'BSCCA-DE-2023-Y3'
        OR p.ProgramName = 'BSCCA-FT-2019-Y3'
        OR p.ProgramName = 'BSCCA-DE-2019-Y3'
        
        OR p.ProgramName = 'BSCCS-FT-2023-Y3'
        OR p.ProgramName = 'BSCCS-DE-2023-Y3'
        OR p.ProgramName = 'BSCCS-FT-2019-Y3'
        OR p.ProgramName = 'BSCCS-DE-2019-Y3'
        
        OR p.ProgramName = 'BSCOPT-FT-2023-Y3'
        OR p.ProgramName = 'BSCOPT-DE-2023-Y3'
        OR p.ProgramName = 'BSCOPT-FT-2019-Y3'
        OR p.ProgramName = 'BSCOPT-DE-2019-Y3'
        
        OR p.ProgramName = 'BSCMHCP-FT-2023-Y3'
        OR p.ProgramName = 'BSCMHCP-DE-2023-Y3'
        OR p.ProgramName = 'BSCMHCP-FT-2019-Y3'
        OR p.ProgramName = 'BSCMHCP-DE-2019-Y3'
        
        OR p.ProgramName = 'BSCCO-FT-2023-Y3'
        OR p.ProgramName = 'BSCCO-DE-2023-Y3'
        OR p.ProgramName = 'BSCCO-FT-2019-Y3'
        OR p.ProgramName = 'BSCCO-DE-2019-Y3'
        
        OR p.ProgramName = 'BSCBMS-FT-2023-Y4'
        OR p.ProgramName = 'BSCBMS-DE-2023-Y4'
        OR p.ProgramName = 'BSCBMS-FT-2019-Y4'
        OR p.ProgramName = 'BSCBMS-DE-2019-Y4'
        
        OR p.ProgramName = 'BSCPHN-FT-2023-Y4'
        OR p.ProgramName = 'BSCPHN-DE-2023-Y4'
        OR p.ProgramName = 'BSCPHN-FT-2019-Y4'
        OR p.ProgramName = 'BSCPHN-DE-2019-Y4'
        
        OR p.ProgramName = 'BSCEH-FT-2023-Y4'
        OR p.ProgramName = 'BSCEH-DE-2023-Y4'
        OR p.ProgramName = 'BSCEH-FT-2019-Y4'
        OR p.ProgramName = 'BSCEH-DE-2019-Y4'
        
        OR p.ProgramName = 'BSCPH-FT-2023-Y4'
        OR p.ProgramName = 'BSCPH-DE-2023-Y4'
        OR p.ProgramName = 'BSCPH-FT-2019-Y4'
        OR p.ProgramName = 'BSCPH-DE-2019-Y4'
        
        OR p.ProgramName = 'BSCNUR-FT-2023-Y4'
        OR p.ProgramName = 'BSCNUR-DE-2023-Y4'
        OR p.ProgramName = 'BSCNUR-FT-2019-Y4'
        OR p.ProgramName = 'BSCNUR-DE-2019-Y4'
        
        OR p.ProgramName = 'BSCON-FT-2023-Y3'
        OR p.ProgramName = 'BSCON-DE-2023-Y3'
        OR p.ProgramName = 'BSCON-FT-2019-Y3'
        OR p.ProgramName = 'BSCON-DE-2019-Y3'
        
        OR p.ProgramName = 'BSCMID-FT-2023-Y4'
        OR p.ProgramName = 'BSCMID-DE-2023-Y4'
        OR p.ProgramName = 'BSCMID-FT-2019-Y4'
        OR p.ProgramName = 'BSCMID-DE-2019-Y4'
        
        OR p.ProgramName = 'BSCPHNUR-FT-2023-Y3'
        OR p.ProgramName = 'BSCPHNUR-DE-2023-Y3'
        OR p.ProgramName = 'BSCPHNUR-FT-2019-Y3'
        OR p.ProgramName = 'BSCPHNUR-DE-2019-Y3'
        
        OR p.ProgramName = 'BSCMHN-FT-2023-Y4'
        OR p.ProgramName = 'BSCMHN-DE-2023-Y4'
        OR p.ProgramName = 'BSCMHN-FT-2019-Y4'
        OR p.ProgramName = 'BSCMHN-DE-2019-Y4'
    )
    GROUP BY bi.ID
    
;