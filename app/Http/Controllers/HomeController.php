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
            if($student){
                $getStudentNumber = $student->student_number;
                $studentNumbers = [$getStudentNumber];
                $studentResults = $this->getAppealStudentDetails($academicYear, $studentNumbers)->first();
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }             
            
            $this->setAndSaveCourses($studentId);
            // Retrieve all unique Student values from the Course model
            $courses = Courses::where('Student', $studentId)->get();
            return view('docket.studentViewDocket',compact('studentResults','courses'));
        }else{
            return view('home');
        }
        // return $courses;
       
    }

    private function setAndSaveCourses($studentId) {
        $dataArray = $this->getCoursesForFailedStudents($studentId);
    
        if (!$dataArray) {
            $dataArray = $this->findUnregisteredStudentCourses($studentId);
        }
    
        if (empty($dataArray)) {
            return; // No data to insert, so exit early
        }
    
        $studentCourses = Courses::where('Student', $studentId)
            ->whereIn('Course', array_column($dataArray, 'Course'))
            ->get()
            ->pluck('Course')
            ->toArray();
    
        $coursesToInsert = [];
    
        foreach ($dataArray as $item) {
            $course = $item['Course'];
    
            if (!in_array($course, $studentCourses)) {
                $coursesToInsert[] = [
                    'Student' => $item['Student'],
                    'Program' => $item['Program'],
                    'Course' => $course,
                    'Grade' => $item['Grade'],
                ];
    
                // Update the list of existing courses for the student
                $studentCourses[] = $course;
            }
        }
    
        if (!empty($coursesToInsert)) {
            // Batch insert the new courses
            Courses::insert($coursesToInsert);
        }
    }
}
