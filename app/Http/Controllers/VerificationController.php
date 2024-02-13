<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verifyStudent($studentNumber) {
        $results = $this->checkIfStudentIsRegistered($studentNumber)->get()
        ->values(); 
    
        // Group the results by student ID
        // $groupedResults = $results->groupBy('StudentID');
    
        // // Transform the grouped results to the desired format
        // $formattedResults = $groupedResults->map(function ($studentCourses) {
        //     $studentInfo = $studentCourses->first(); // Take the first record for student info
        //     $courses = $studentCourses->map(function ($course) {
        //         return [
        //             'CourseName' => $course->CourseName,
        //             'CourseDescription' => $course->CourseDescription,
        //         ];
        //     });
    
        //     return [
        //         'FirstName' => $studentInfo->FirstName,
        //         'MiddleName' => $studentInfo->MiddleName,
        //         'Surname' => $studentInfo->Surname,
        //         'StudyType' => $studentInfo->StudyType,
        //         'Sex' => $studentInfo->Sex,
        //         'StudentID' => $studentInfo->StudentID,
        //         'GovernmentID' => $studentInfo->GovernmentID,
        //         'ProgrammeName' => $studentInfo->ProgrammeName,
        //         'School' => $studentInfo->School,
        //         'YearOfStudy' => $studentInfo->YearOfStudy,
        //         'Courses' => $courses->toArray(),
        //     ];
        // });
    
        return response()->json(['data' =>  $results]);
    }
}
