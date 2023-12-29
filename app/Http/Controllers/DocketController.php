<?php

namespace App\Http\Controllers;

use App\Models\AllCourses;
use App\Models\BasicInformation;
use App\Models\Courses;
use App\Models\Student;
use App\Models\User;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Exception;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DocketController extends Controller
{
    public function index(Request $request){
        $academicYear= 2023;
        $courseName = null;
        $courseId = null;
        if($request->input('student-number')){
            $student = Student::query()
                        ->where('student_number','=', $request->input('student-number'))
                        ->where('status','=', 1)
                        ->first();
            if($student){
                $getStudentNumber = $student->student_number;
                $studentNumbers = [$getStudentNumber];
                $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::where('status', 1)->pluck('student_number')->toArray();
            $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
        }
        return view('docket.index', compact('results','courseName','courseId'));
    }

    public function indexNmcz(Request $request){
        $academicYear= 2023;
        $courseName = null;
        $courseId = null;
        if($request->input('student-number')){
            $student = Student::query()
                        ->where('student_number','=', $request->input('student-number'))
                        ->where('status','=', 2)
                        ->first();
            if($student){
                $getStudentNumber = $student->student_number;
                $studentNumbers = [$getStudentNumber];
                $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::where('status', 2)->pluck('student_number')->toArray();
            $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
        }
        return view('docketNmcz.index', compact('results','courseName','courseId'));
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
                ->delete();
            
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
        
               
        
        $this->setAndUpdateCourses($studentId);
        // Retrieve all unique Student values from the Course model
        $courses = Courses::where('Student', $studentId)->get();
        // return $courses;

        // Pass the $students variable to the view
        // return view('your.view.name', compact('students'));
        
        return view('docket.show',compact('courses','studentResults'));
    }

    public function showStudentNmcz($studentId){
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
        
               
        
        $this->setAndUpdateCourses($studentId);
        // Retrieve all unique Student values from the Course model
        $courses = Courses::where('Student', $studentId)->get();
        // return $courses;

        // Pass the $students variable to the view
        // return view('your.view.name', compact('students'));
        
        return view('docketNmcz.show',compact('courses','studentResults'));
    }

    public function verifyStudent($studentId){

        $academicYear= 2023;
        if(is_numeric($studentId)){
            $student = Student::query()
                            ->where('student_number','=', $studentId)
                            ->first();
        }else{
            $student = []; 
        }
        if($student){

            //  $route = '/docket/showStudent/'.$studentId;
            // $url = url($route);
            //  $qrCode = QrCode::size(100)->generate($url);

            
            $getStudentNumber = $student->student_number;
            $studentNumbers = [$getStudentNumber];
            $studentResults = $this->getAppealStudentDetails($academicYear, $studentNumbers)->first();
            $this->setAndSaveCourses($studentId);
        // Retrieve all unique Student values from the Course model
            $courses = Courses::where('Student', $studentId)->get();
        }else{
            $studentResults = []; 
            $courses = [];
            // $qrCode = [];   
            // $url = [];          
        }        
        return view('docket.verify',compact('courses','studentResults'));
    }

    public function verifyStudentNmcz($studentId){

        $academicYear= 2023;
        if(is_numeric($studentId)){
            $student = Student::query()
                            ->where('student_number','=', $studentId)
                            ->first();
        }else{
            $student = []; 
        }
        if($student){

            //  $route = '/docket/showStudent/'.$studentId;
            // $url = url($route);
            //  $qrCode = QrCode::size(100)->generate($url);

            
            $getStudentNumber = $student->student_number;
            $studentNumbers = [$getStudentNumber];
            $studentResults = $this->getAppealStudentDetails($academicYear, $studentNumbers)->first();
            $this->setAndSaveCourses($studentId);
        // Retrieve all unique Student values from the Course model
            $courses = Courses::where('Student', $studentId)->get();
        }else{
            $studentResults = []; 
            $courses = [];
            // $qrCode = [];   
            // $url = [];          
        }        
        return view('docketNmcz.verify',compact('courses','studentResults'));
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
    public function importNmcz(){
        return view('docketNmcz.import');
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
