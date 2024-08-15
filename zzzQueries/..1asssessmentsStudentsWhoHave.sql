SELECT 
    COUNT(DISTINCT student_id) AS student_count
FROM 
    students_continous_assessments
WHERE 
    student_id NOT IN (
        SELECT 
            students_continous_assessments.student_id
        FROM 
            students_continous_assessments
        JOIN 
            course_assessments 
            ON course_assessments.course_assessments_id = students_continous_assessments.course_assessment_id
        GROUP BY 
            students_continous_assessments.student_id,
            students_continous_assessments.course_id,
            students_continous_assessments.delivery_mode,
            students_continous_assessments.study_id
        HAVING 
            SUM(students_continous_assessments.sca_score) < 20
    );