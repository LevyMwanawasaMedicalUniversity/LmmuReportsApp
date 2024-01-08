<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user(); // Get the currently logged-in user

        // Check if the user has the "Student" role
        $hasStudentRole = $user->hasRole('Student');

        if ($hasStudentRole) {

            $academicYear= 2023;
            $user_id = auth()->user()->id;
            $user = User::find($user_id);
            $studentId = $user->name;
            $student = Student::query()
                            ->where('student_number','=', $studentId)
                            ->first();
            
            if(!is_null($student)){
                $studentNumbers = [$studentId];
                $studentResults = $this->getAppealStudentDetails($academicYear, $studentNumbers)->first();
            }else{
                return back()->with('error', 'NOT STUDENT.');               
            }             
            
            if($student->status == 3){
                $studentExistsInStudentsTable = Courses::where('Student', $studentId)->whereNotNull('updated_at')->exists();
                $studentCoursesUpdated = Student::where('student_number', $studentId)->whereNotNull('course_updated')->exists();
                if (!$studentExistsInStudentsTable || !$studentCoursesUpdated ) {
                    $this->setAndUpdateCoursesForCurrentYear($studentId);
                }
            }else{
                $this->setAndUpdateCourses($studentId);
            }
            
            // Retrieve all unique Student values from the Course model
            $courses = Courses::where('Student', $studentId)->get();
            $status = $student->status;
            if($status ==1){
                return view('docket.studentViewDocket',compact('studentResults','courses'));
            }elseif($status ==2){
                return view('docketNmcz.studentViewDocket',compact('studentResults','courses'));
            }elseif($status ==3){
                return view('docketSupsAndDef.studentViewDocket',compact('studentResults','courses'));
            }
        }else{
            return view('home');
        }
        // return $courses;       
    }

    private function setAndUpdateCourses($studentId) {
        $dataArray = $this->getCoursesForFailedStudents($studentId);
    
        if (!$dataArray) {
            $dataArray = $this->findUnregisteredStudentCourses($studentId);
        }
    
        if (empty($dataArray)) {
            return; // No data to insert, so exit early
        }
    
        // Check if there are rows with "No Value" for the specific 'Student'
        $hasNoValueCourses = Courses::where('Student', $studentId)
            ->where('Course', 'No Value')
            ->exists();
    
        $studentCourses = Courses::where('Student', $studentId)
            ->whereIn('Course', array_column($dataArray, 'Course'))
            ->get();
    
        $coursesToInsert = [];
    
        foreach ($dataArray as $item) {
            $course = $item['Course'];
    
            // Check if Grade is "No Value" and Course is "No Value"
            if ($item['Course'] === "No Value" && $course === "No Value") {
                // If Grade and Course are both "No Value", don't insert these rows
                continue;
            }
    
            if (!in_array($course, $studentCourses->pluck('Course')->toArray())) {
                $coursesToInsert[] = [
                    'Student' => $item['Student'],
                    'Program' => $item['Program'],
                    'Course' => $course,
                    'Grade' => $item['Grade'],
                ];
    
                // Update the list of existing courses for the student
                $studentCourses->push(['Course' => $course]);
            }
        }
    
        if (!empty($coursesToInsert) && $hasNoValueCourses) {
            // Delete rows with "No Value" entries
            Courses::where('Student', $studentId)
                // ->where('Course', 'No Value')
                ->delete();
            
            // Batch insert the new courses
            Courses::insert($coursesToInsert);
        }
    }
}
