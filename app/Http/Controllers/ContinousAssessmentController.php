<?php

namespace App\Http\Controllers;

use App\Models\LMMAXCourseAssessment;
use App\Models\LMMAXCourseAssessmentScores;
use App\Models\LMMAXStudentsContinousAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContinousAssessmentController extends Controller
{
    public function studentsCAResults()
    {
        // $courseAssessments = LMMAXCourseAssessment::join('course_assessment_scores', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
        //     ->join('students_continous_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
        //     ->where('students_continous_assessments.student_id', $studentNumber)
        //     ->select('students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'),'course_assessments.course_id','course_assessment_scores.course_code')
        //     ->groupBy('course_assessments.course_id','students_continous_assessments.student_id')
        //     ->get();
        $academicYear= 2024;
        $studentNumber = Auth::user()->name;
        $results = LMMAXStudentsContinousAssessment::join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
            // ->join('course_assessment_scores', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->where('students_continous_assessments.student_id', $studentNumber)
            ->where('course_assessments.academic_year', $academicYear)    
            ->select('students_continous_assessments.students_continous_assessment_id','students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'),'students_continous_assessments.course_id')
            ->groupBy('students_continous_assessments.student_id','students_continous_assessments.course_id')
            ->get();
        // $courseAssessmentScores = LMMAXCourseAssessmentScores::all();
        // $moodleCourses = MoodleCourses::all();
        // return  $results;

        return view('allStudents.continousAssessment.viewCa', compact('results','studentNumber'));
    }

    public function viewCaComponents($courseId)
    {
        $studentNumber = Auth::user()->name;
        $academicYear= 2024;

        
        $results = LMMAXStudentsContinousAssessment::join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
            // ->join('course_assessment_scores', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->join('assessment_types', 'assessment_types.id', '=', 'students_continous_assessments.ca_type')
            //->join('c_a_type_marks_allocations','')
            //->join('c_a_type_marks_allocations', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
            ->where('students_continous_assessments.student_id', $studentNumber)
            ->where('course_assessments.academic_year', $academicYear)    
            ->where('students_continous_assessments.course_id', $courseId)
            ->select('students_continous_assessments.delivery_mode','students_continous_assessments.study_id','students_continous_assessments.students_continous_assessment_id','students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'),'students_continous_assessments.course_id','students_continous_assessments.ca_type','assessment_types.assesment_type_name')
            ->groupBy('students_continous_assessments.student_id','students_continous_assessments.course_id','students_continous_assessments.ca_type')
            ->get();

        // return $results;
        return view('allStudents.continousAssessment.viewCaComponents', compact('results','studentNumber'));
    }

    public function viewInSpecificCaComponent( $courseId,$caType)
    {
        $studentNumber = Auth::user()->name;
        $academicYear= 2024;
        

        
        $results = LMMAXCourseAssessment::join('course_assessment_scores', 'course_assessment_scores.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->join('assessment_types', 'assessment_types.id', '=', 'course_assessments.ca_type')
            ->where('course_assessments.course_id', $courseId)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessment_scores.student_id', $studentNumber)
            ->orderBy('course_assessments.course_assessments_id', 'asc')
            ->get();
        // return $results;
        return view('allStudents.continousAssessment.viewInSpecificCaComponent', compact('results','studentNumber'));
    }
}
