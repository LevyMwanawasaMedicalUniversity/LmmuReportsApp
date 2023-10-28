<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use App\Models\Student;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Exception;
use Illuminate\Http\Request;

class DocketController extends Controller
{
    public function index(Request $request){
        $academicYear= 2023;

        if($request->input('student-number')){
            $student = Student::query()
                        ->where('student_number','=', $request->input('student-number'))
                        ->first();
            if($student){
                $getStudentNumber = $student->student_number;
                $studentNumbers = [$getStudentNumber];
                $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::pluck('student_number')->toArray();
            $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
        }
        return view('docket.index', compact('results'));
    }

    public function showStudent($studentId){
        $dataArray  = $this->getCoursesForFailedStudents($studentId);
        $academicYear= 2023;
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
        
        $existingCourse = Courses::where('Student', $studentId)
                ->get();
    
        if (count($existingCourse) <= 0) {

            foreach ($dataArray  as $item) {
                $student = $item['Student'];
                $program = $item['Program'];
                $course = $item['Course'];
                $grade = $item['Grade'];
        
                // Check if a record with the same Student, Program, and Grade exists
                
                    // If the record doesn't exist, insert it
                    Courses::create([
                        'Student' => $student,
                        'Program' => $program,
                        'Course' => $course,
                        'Grade' => $grade,
                    ]);
        
                    // Keep track of inserted courses
            }
        }

        // Retrieve all unique Student values from the Course model
        $courses = Courses::where('Student', $studentId)->get();
        // return $courses;

        // Pass the $students variable to the view
        // return view('your.view.name', compact('students'));
        
        return view('docket.show',compact('courses','studentResults'));
    }

    public function updateCoursesForStudent(Request $request, $studentId) {
        try {
            Courses::where('Student', $studentId)->delete();
            
            
            $newData = $request->input('courses');   
            // Initialize variables to hold course and program
            $course = null;
            $program = null;
            $newData;
            
            // Initialize an empty result array
            $result = [];

            // Iterate through the $newData array and build the result
            $resultIndex = 0; // Initialize the index for the result array
            foreach ($newData as $key => $value) {
                if ($key === "Course") {
                    // Create a new entry with Course and Program
                    $result[$resultIndex] = [
                        "Course" => $value,"Program" => $value,
                    ];
                    $resultIndex++;
                } else {
                    // Add the Program to the current entry
                    $result[$resultIndex] = [
                        "Program" => $value,
                    ];
                    // Move to the next result index
                    
                }
            }

            return $result;
            if($newData){
    
                foreach ($newData as $data) {
                    
                    // Check if the data is for a course
                    if ((isset($data['Course']))) {
                        if(isset($data['Program'])){
                            $course = $data['Course'];
                            $program = $data['Program'];
                        }
                    }else{
                        return back()->with('success', 'ISSET I FAILED.');
                    }
                    
                    // Check if both course and program are available
                    $existingCourse = Courses::where('Student', $studentId)
                        ->where('Program', $program)
                        ->where('Course', $course)
                        ->get();
                    if ($course !== null && $program !== null) {
                        // Create a new record in the Courses table
                        if (count($existingCourse) <= 0) {
                            // Create a new record in the Courses table
                            Courses::create([
                                'Student' => $studentId,
                                'Course' => $data['Course'],
                                'Program' => $data['Program'],
                                'Grade' => null
                            ]);
                        
                        }else{
                            return back()->with('error', 'WRONDDDD');
                        }
                        
                    
                    }else{
                        return $data;
                    }
                }
        
                return $newData;
            }else{
                return back()->with('success', 'UPDATED grgrg COURSES.');
            }
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }

    public function import(){
        return view('docket.import');
    }

    public function uploadStudents(Request $request)
    {
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'term' => 'required',
        ]);

        // Get the academic year and term from the form
        $academicYear = $request->input('academicYear');
        $term = $request->input('term');

        // Process the uploaded file
        if ($request->hasFile('excelFile')) {
            $file = $request->file('excelFile');

            // Initialize the Box/Spout reader
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());

            $isHeaderRow = true; // Flag to identify the header row

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    if ($isHeaderRow) {
                        $isHeaderRow = false;
                        continue;
                    }

                    // Assuming the student number is in the first column (index 1)
                    $studentNumber = $row->getCellAtIndex(0)->getValue();

                    // Check if the student number already exists within the same academic year and term
                    $isDuplicate = Student::where('student_number', $studentNumber)
                        ->where('academic_year', $academicYear)
                        ->where('term', $term)
                        ->exists();

                    if (!$isDuplicate) {
                        // Insert a new student record into the database
                        Student::create([
                            'student_number' => $studentNumber,
                            'academic_year' => $academicYear,
                            'term' => $term,
                        ]);
                    }
                }
            }

            $reader->close();

            // Provide a success message
            return redirect()->back()->with('success', 'Students imported successfully.');
        }

        // Handle errors or validation failures
        return redirect()->back()->with('error', 'Failed to upload students.');
    }
}
