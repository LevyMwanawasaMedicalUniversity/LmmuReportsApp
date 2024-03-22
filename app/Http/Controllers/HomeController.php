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
    public function index(){
        if(Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Academics') || Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Developer')) {
            return redirect()->route('landing.page');
        } elseif(Auth::user()->hasRole('Student') || Auth::user()->hasRole('Dosa') || Auth::user()->hasRole('Examination')) {
            return view('home');
        }       
    }

    public function landingPage(){
        // return "here";
        $academicYear = 2024;
        $students = Student::query()
            ->join('course_registration', 'students.student_number', '=', 'course_registration.StudentID')
            ->where('course_registration.Year', $academicYear)
            ->get();
            // return $students;
        $studentsArray = $students->pluck('student_number')->toArray();
        $registeredCoursesArray = $students->pluck('CourseID')->toArray();
        // return
        // return $studentsArray;
        $eduroleRegisteredStudents = $this->getRegistrationsFromeEduroleBasedOnReturningAndNewlyAdmittedStudents($academicYear)->get();
        $eduroleArray = $eduroleRegisteredStudents->pluck('ID')->toArray();
        // return $eduroleArray;
        $sisReportsRegisteredStudents = $this->getRegistrationsFromSisReportsBasedOnReturningAndNewlyAdmittedStudents($academicYear)->get();
        
        return $sisReportsRegisteredStudents;
        return view('landingPage', compact('eduroleRegisteredStudents', 'sisReportsRegisteredStudents'));
        
    }
    
    

    public function setAndUpdateCourses($studentId) {
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
