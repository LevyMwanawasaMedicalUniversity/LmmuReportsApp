<?php

namespace App\Http\Controllers;

use App\Models\BasicInformation;
use App\Models\MoodleUsers;
use App\Models\MoodleUserEnrolments;
use App\Models\CourseElectives;
// use App\Models\CourseRegistration;  // SIS Reports model commented out
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MoodleStatusController extends Controller
{
    public function index(Request $request)
    {
        // Set a reasonable timeout
        set_time_limit(300);
        
        $studentId = $request->input('student_id');
        $studentInfo = null;
        $moodleAccount = null;
        $enrollments = null;
        $courseCount = null;
        
        if ($studentId) {
            // Get student basic information
            $studentInfo = BasicInformation::where('ID', $studentId)->first();
            
            // Get student Moodle account
            $moodleAccount = MoodleUsers::where('username', $studentId)->first();
            
            // If student has a Moodle account, get their enrollments
            if ($moodleAccount) {
                $enrollments = DB::table('mdl_user_enrolments')
                    ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
                    ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
                    ->where('mdl_user_enrolments.userid', $moodleAccount->id)
                    ->select('mdl_course.fullname', 'mdl_course.shortname', 'mdl_user_enrolments.status')
                    ->get();
                    
                $courseCount = $enrollments->count();
            }
            
            // Get expected enrollments based on student registrations from Edurole
            $expectedCourses = CourseElectives::where('StudentID', $studentId)->get();
            $expectedCoursesCount = $expectedCourses->count();
            
            // Comment out SIS Reports data
            // $sisReportsCourses = CourseRegistration::where('StudentID', $studentId)->get();
            // $sisReportsCoursesCount = $sisReportsCourses->count();
            
            // Set SIS Reports variables to null since we're not using them
            $sisReportsCourses = null;
            $sisReportsCoursesCount = 0;
        }
        
        // Get overall stats for monitoring
        $totalStudents = BasicInformation::count();
        $totalMoodleAccounts = MoodleUsers::count();
        $totalEnrollments = MoodleUserEnrolments::count();
        
        // Get 5 students with most enrollments - could indicate problems
        $studentsWithMostEnrollments = DB::table('mdl_user_enrolments')
            ->join('mdl_users', 'mdl_users.id', '=', 'mdl_user_enrolments.userid')
            ->select('mdl_users.username', DB::raw('count(*) as enrollment_count'))
            ->groupBy('mdl_users.username')
            ->orderBy('enrollment_count', 'desc')
            ->limit(5)
            ->get();
            
        // Get students with recent errors (searching logs would be implementation-specific)
        // This is a placeholder - actual implementation would depend on your logging system
        $recentErrors = [];
        
        return view('moodle.status', compact(
            'studentId',
            'studentInfo',
            'moodleAccount',
            'enrollments',
            'courseCount',
            'expectedCourses',
            'expectedCoursesCount',
            'sisReportsCourses',
            'sisReportsCoursesCount',
            'totalStudents',
            'totalMoodleAccounts',
            'totalEnrollments',
            'studentsWithMostEnrollments',
            'recentErrors'
        ));
    }
    
    public function checkStudentStatus($studentId)
    {
        // Set a reasonable timeout
        set_time_limit(30);
        
        $result = [
            'student_exists' => false,
            'moodle_account_exists' => false,
            'has_enrollments' => false,
            'enrollment_count' => 0,
            'expected_courses' => 0
        ];
        
        // Check if student exists
        $student = BasicInformation::where('ID', $studentId)->first();
        if ($student) {
            $result['student_exists'] = true;
            
            // Check for Moodle account
            $moodleAccount = MoodleUsers::where('username', $studentId)->first();
            if ($moodleAccount) {
                $result['moodle_account_exists'] = true;
                
                // Check enrollments
                $enrollments = MoodleUserEnrolments::where('userid', $moodleAccount->id)->get();
                $result['enrollment_count'] = $enrollments->count();
                $result['has_enrollments'] = $enrollments->count() > 0;
            }
            
            // Check expected courses (only from Edurole, not SIS Reports)
            $expectedCourses = CourseElectives::where('StudentID', $studentId)->get();
            $result['expected_courses'] = $expectedCourses->count();
        }
        
        return response()->json($result);
    }
}
