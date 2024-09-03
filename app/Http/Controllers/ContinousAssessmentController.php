<?php

namespace App\Http\Controllers;

use App\Models\CourseElectives;
use App\Models\CourseRegistration;
use App\Models\EduroleCourses;
use App\Models\LMMAXCourseAssessment;
use App\Models\LMMAXCourseAssessmentScores;
use App\Models\LMMAXCourseComponentAllocation;
use App\Models\LMMAXStudentsContinousAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
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

        $checkRegistration = CourseElectives::where('StudentID', $studentNumber)
            ->where('Year', 2024)
            // ->where('Semester', 1)
            ->exists();

        // return $checkRegistration;
        if (!$checkRegistration) {            
            return redirect()->back()->with('error', 'UNREGISTERED STUDENT. Complete Course Registration In Order To View Results');
        }

        $results = LMMAXStudentsContinousAssessment::join('course_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
            // ->join('course_assessment_scores', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->where('students_continous_assessments.student_id', $studentNumber)
            ->where('course_assessments.academic_year', $academicYear)    
            ->select('students_continous_assessments.students_continous_assessment_id','students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'),'students_continous_assessments.course_id','students_continous_assessments.study_id','students_continous_assessments.delivery_mode')
            ->groupBy('students_continous_assessments.student_id','students_continous_assessments.course_id')
            ->get();
        // $courseAssessmentScores = LMMAXCourseAssessmentScores::all();
        // $moodleCourses = MoodleCourses::all();
        // return  $results;

        return view('allStudents.continousAssessment.viewCa', compact('results','studentNumber'));
    }

    public function viewCaComponents(Request $request)
    {
        $studentNumber = Auth::user()->name;
        $academicYear= 2024;

        $delivery = Crypt::decrypt($request->delivery_mode);
        $studyId = Crypt::decrypt($request->study_id);
        $courseId = Crypt::decrypt($request->course_id);
        if ($request->course_component_id) {
            $componentId = Crypt::decrypt($request->course_component_id);
        } else {
            $componentId = null;
        }

        if ($request->component_name) {
            $componentName = Crypt::decrypt($request->component_name);
        } else {
            $componentName = "";
        }

        $checkRegistration = CourseElectives::where('StudentID', $studentNumber)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();

        // return $checkRegistration;
        if (!$checkRegistration) {            
            return redirect()->back()->with('error', 'UNREGISTERED STUDENT. Complete Course Registration In Order To View Results');
        }
        
        $results = LMMAXCourseAssessment::join('students_continous_assessments', 'course_assessments.course_assessments_id', '=', 'students_continous_assessments.course_assessment_id')
            // ->join('course_assessment_scores', 'course_assessments.course_assessments_id', '=', 'course_assessment_scores.course_assessment_id')
            ->join('assessment_types', 'assessment_types.id', '=', 'students_continous_assessments.ca_type')
            //->join('c_a_type_marks_allocations','')
            //->join('c_a_type_marks_allocations', 'assessment_types.id', '=', 'c_a_type_marks_allocations.assessment_type_id')
            ->where('students_continous_assessments.student_id', $studentNumber)
            // ->where('course_assessments.academic_year', $academicYear)    
            ->where('students_continous_assessments.course_id', $courseId)
            ->where('students_continous_assessments.delivery_mode', $delivery)  
            ->where('students_continous_assessments.component_id', $componentId)
            ->select('students_continous_assessments.component_id','students_continous_assessments.delivery_mode','students_continous_assessments.study_id','students_continous_assessments.students_continous_assessment_id','students_continous_assessments.student_id', DB::raw('SUM(students_continous_assessments.sca_score) as total_marks'),'students_continous_assessments.course_id','students_continous_assessments.ca_type','assessment_types.assesment_type_name')
            ->groupBy('students_continous_assessments.student_id','students_continous_assessments.course_id','students_continous_assessments.ca_type')
            ->get();
            if ($results->isEmpty()) {
                return redirect()->back()->with('warning', 'No Results Uploaded Yet');
            }

        // return $results;
        return view('allStudents.continousAssessment.viewCaComponents', compact('results','studentNumber','componentName'));
    }

    public function viewCaComponentsWithComponent(Request $request){
        $studentNumber = Auth::user()->name;
        $academicYear= 2024;
        $delivery = Crypt::decrypt($request->delivery_mode);
        $studyId = Crypt::decrypt($request->study_id);
        $courseId = Crypt::decrypt($request->course_id);

        $checkRegistration = CourseElectives::where('StudentID', $studentNumber)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();

        // return $checkRegistration;
        if (!$checkRegistration) {            
            return redirect()->back()->with('error', 'UNREGISTERED STUDENT. Complete Course Registration In Order To View Results');
        }

        $results = LMMAXCourseComponentAllocation::join('course_components', 'course_components.course_components_id', '=', 'course_component_allocations.course_component_id')
            ->where('course_id', $courseId)
            ->where('delivery_mode', $delivery)
            ->where('study_id', $studyId)
            ->where('academic_year', $academicYear)
            ->get();

        $course = EduroleCourses::where('ID', $courseId)->first();
        $courseName = $course->CourseDescription;
        $courseCode = $course->Name;    
        // return $results;
        return view('allStudents.continousAssessment.viewCourseComponents', compact('results','studentNumber','courseName','courseCode'));
    }

    public function viewInSpecificCaComponent(Request $request, $courseId,$caType)
    {
        $studentNumber = Auth::user()->name;
        $academicYear= 2024;

        if ($request->component_name) {
            $componentName = Crypt::decrypt($request->component_name);
        } else {
            $componentName = "";
        }

        if ($request->component_id) {
            $componentId = Crypt::decrypt($request->component_id);
        } else {
            $componentId = null;
        }
        
        $checkRegistration = CourseElectives::where('StudentID', $studentNumber)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();

        // return $checkRegistration;
        if (!$checkRegistration) {            
            return redirect()->back()->with('error', 'UNREGISTERED STUDENT. Complete Course Registration In Order To View Results');
        }
        
        $results = LMMAXCourseAssessment::join('course_assessment_scores', 'course_assessment_scores.course_assessment_id', '=', 'course_assessments.course_assessments_id')
            ->join('assessment_types', 'assessment_types.id', '=', 'course_assessments.ca_type')
            ->where('course_assessments.course_id', $courseId)
            ->where('course_assessments.ca_type', $caType)
            ->where('course_assessments.academic_year', $academicYear)
            ->where('course_assessment_scores.student_id', $studentNumber)
            ->where('course_assessments.component_id', $componentId)
            ->orderBy('course_assessments.course_assessments_id', 'asc')
            ->get();
        if ($results->isEmpty()) {
            return redirect()->back()->with('warning', 'No Results Uploaded Yet');
        }
        
        return view('allStudents.continousAssessment.viewInSpecificCaComponent', compact('componentName','results','studentNumber'));
    }
}
