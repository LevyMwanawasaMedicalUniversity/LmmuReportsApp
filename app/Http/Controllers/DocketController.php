<?php

namespace App\Http\Controllers;

use App\Models\AllCourses;
use App\Models\Courses;
use App\Models\Student;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Exception;
use Illuminate\Http\Request;

class DocketController extends Controller
{
    public function index(Request $request){
        $academicYear= 2023;
        $courseName = null;
        $courseId = null;
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
        return view('docket.index', compact('results','courseName','courseId'));
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

    public function showStudent($studentId){
        // try{
            
        // }catch(Exception $e){
            
        // }
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
        
               
        
        $this->setAndSaveCourses($studentId);
        // Retrieve all unique Student values from the Course model
        $courses = Courses::where('Student', $studentId)->get();
        // return $courses;

        // Pass the $students variable to the view
        // return view('your.view.name', compact('students'));
        
        return view('docket.show',compact('courses','studentResults'));
    }

    public function updateCoursesForStudent(Request $request, $studentId) {
        try {
            // First, delete all existing courses for the student
            
            
            // Get the 'dataArray' from the request
            $dataArray = $request->input('dataArray');

           $dataArray;
            
            if (!empty($dataArray)) {
                Courses::where('Student', $studentId)->delete();
                foreach ($dataArray as $data) {
                    // Check if both 'Course' and 'Program' are available in the data
                    if (isset($data['Course']) && isset($data['Program'])) {
                        // Create a new record in the Courses table
                        Courses::firstOrCreate([
                            'Student' => $studentId,
                            'Course' => $data['Course'],
                            'Program' => $data['Program'],
                            'Grade' => null // You can set a default grade here if needed
                        ]);
                    } else {
                        return back()->with('error', 'Invalid data format.');
                    }
                }
            }
    
            
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function import(){
        return view('docket.import');
    }

    public function uploadStudents(Request $request)
    {
        set_time_limit(1200000);
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
            $studentNumbers = [];

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    if ($isHeaderRow) {
                        $isHeaderRow = false;
                        continue;
                    }

                    // Assuming the student number is in the first column (index 1)
                    $studentNumber = $row->getCellAtIndex(0)->getValue();

                    $studentNumbers[] = $studentNumber;
                }
            }

            $reader->close();

            // Split studentNumbers into chunks
            $chunkSize = 10; // Adjust this as needed
            $chunks = array_chunk($studentNumbers, $chunkSize);

            try {
                // Process each chunk
                foreach ($chunks as $chunk) {
                    // Check for duplicate students in a single database query
                    $existingStudents = Student::whereIn('student_number', $chunk)
                        ->where('academic_year', $academicYear)
                        ->where('term', $term)
                        ->pluck('student_number')
                        ->toArray();

                    // Insert new students
                    $newStudents = array_diff($chunk, $existingStudents);
                    $studentsToInsert = [];
                    foreach ($newStudents as $studentNumber) {
                        $studentsToInsert[] = [
                            'student_number' => $studentNumber,
                            'academic_year' => $academicYear,
                            'term' => $term,
                        ];
                    }

                    Student::insert($studentsToInsert);

                    // Trigger your setAndSaveCourses function for new students
                    foreach ($newStudents as $studentNumber) {
                        $this->setAndSaveCourses($studentNumber);
                    }
                }

                // Provide a success message
                return redirect()->back()->with('success', 'Students imported successfully.');
            } catch (\Throwable $e) {
                // Handle any unexpected errors during import
                return redirect()->back()->with('error', 'Failed to upload students: ' . $e->getMessage());
            }
        }

        // Handle errors or validation failures
        return redirect()->back()->with('error', 'Failed to upload students.');
    }
    
    public function importCourseFromSis(Request $request) {
        $getCoursesFromSis = $this->getSisCourses();
    
        foreach ($getCoursesFromSis as $course) {
            AllCourses::firstOrCreate(
                [
                    'course_code' => $course->Name,
                ],
                [
                    'course_name' => $course->CourseDescription,
                ]
            );
        }

        if ($request->has('course-code')) {
            $courseCode = $request->input('course-code');
            $courses = AllCourses::where('course_code', 'like', '%' . $courseCode . '%')->get();
        } else {
            $courses = AllCourses::all();
        }
    
        return view('docket.courses', compact('courses'));
    }

    public function selectCourses($studentId){

        $courses = AllCourses::all();
        return view('docket.selectcourses', compact('studentId','courses'));
    }

    public function storeCourses(Request $request, $studentId) {
        $selectedCourses = $request->input('course');
    
        foreach ($selectedCourses as $course) {
            $theCourse = AllCourses::find($course);
    
            Courses::firstOrCreate(
                [
                    'Student' => $studentId,
                    'Program' => $theCourse->course_name,
                    'Course' => $theCourse->course_code
                ]
            );
        }
    
        return redirect()->route('docket.showStudent', $studentId)->with('success', 'Courses updated successfully.');
    }

    public function viewExaminationList($courseId){

        $theCourse = AllCourses::find($courseId);
        $academicYear = 2023;
        $courseCode = $theCourse->course_code;
        $courseName = $theCourse->course_name;

        $studentNumbers = Courses::where('Course', $courseCode)->pluck('Student')->toArray();
                
        $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);


        return view('docket.index', compact('results','courseName','courseId'));
    }

    public function exportListExamList($courseId){
        $theCourse = AllCourses::find($courseId);
        $academicYear = 2023;
        $courseCode = $theCourse->course_code;
        $courseName = $theCourse->course_name;

        $studentNumbers = Courses::where('Course', $courseCode)->pluck('Student')->toArray();

        $headers = [
            'First Name',
            'Middle Name',
            'Surname',
            'Gender',
            'Student Number',
            'Mode Of Study',
            'Year of Study',
            'NRC',
            'Programme Code',
            'Balance',
            'Registration'
        ];
        
        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'Sex',
            'StudentID',
            'StudyType',
            'YearOfStudy',
            'GovernmentID',
            'Programme Code',
            'Amount',
            'RegistrationStatus'
        ];
        $filename = 'ExaminationListFor' . $courseName;
        $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->get();

        return $this->exportData($headers, $rowData, $results, $filename);
    }

}
