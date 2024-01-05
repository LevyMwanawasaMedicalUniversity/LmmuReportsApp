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

    public function sendEmailNotice(){
        $academicYear = 2023;
        $studentNumbers = Student::where('status', 1)->pluck('student_number')->toArray();
        
        $studentsDetails = $this->getAppealStudentDetails($academicYear, $studentNumbers)
                    ->get()
                    ->filter(function ($student) {
                        return $student->RegistrationStatus == 'NO REGISTRATION';
                    });
        foreach ($studentsDetails as $student) {
            $studentNumber = $student->StudentID;
            $this->sendEmailNotification($studentNumber);            
        }
        return back()->with('success', 'Emails sent successfully.');
    }

    public function resetAllStudentsPasswords()
    {
        set_time_limit(1200000);
    
        $academicYear = 2023;
    
        User::join('students', 'students.student_number', '=', 'users.name')
            ->where('students.status', 1)
            ->role('Student') // Using Spatie's role scope
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Examination', 'Finance', 'Academics', 'Administrator']);
            })
            ->chunk(200, function ($students) use ($academicYear) {
                foreach ($students as $student) {
                    $studentNumbers = [$student->name];
                    $studentsDetails = $this->getAppealStudentDetails($academicYear, $studentNumbers)
                        ->get()
                        ->filter(function ($studentDetail) {
                            return $studentDetail->RegistrationStatus === 'NO REGISTRATION';
                        });
    
                    if ($studentsDetails->isEmpty()) {
                        continue; // Skip the iteration if no student details found
                    }
    
                    $studentDetail = $studentsDetails->first();
    
                    if ($studentDetail->GovernmentID === null) {
                        continue; // Skip the iteration if GovernmentID is null
                    }
    
                    $studentNumber = [$studentDetail->StudentID];
                    $studentResults = $this->getAppealStudentDetails($academicYear, $studentNumber)->first();
    
                    if ($studentResults && $studentResults->RegistrationStatus == 'NO REGISTRATION') {
                        $nrc = trim($studentDetail->GovernmentID); // Access GovernmentID property on the first student detail
                        $email = filter_var($studentDetail->PrivateEmail, FILTER_VALIDATE_EMAIL) ? trim($studentDetail->PrivateEmail) : $student->name . '@lmmu.ac.zm';
    
                        $student->update([
                            'password' => bcrypt($nrc),
                            'email' => $email,
                        ]);
    
                        $this->sendEmailNotification($student->name);
                    }
                }
            });
    
        return back()->with('success', 'Passwords reset successfully.');
    }
    


    public function uploadStudents(Request $request)
    {
        set_time_limit(1200000);
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'term' => 'required',
            'status' => 'required',
        ]);

        // Get the academic year and term from the form
        $academicYear = $request->input('academicYear');
        $term = $request->input('term');
        $status = $request->input('status');

        // return $status;

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
                    if($status == 3){
                        // Update existing students
                        
                        
                        Student::whereIn('student_number', $existingStudents)
                            ->update(['status' => 3]);

                        foreach ($existingStudents as $studentId) {
                            $privateEmail = BasicInformation::find($studentId);
                            $email = $privateEmail->PrivateEmail;
                        
                            // Check if the user exists before trying to update
                            $user = User::where('name', $studentId)->first();
                            if ($user) {
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    // $email is a valid email address
                                    $user->update(['email' => $email]);
                                } else {
                                    // $email is not a valid email address
                                    $user->update(['email' => $studentId . '@lmmu.ac.zm']);
                                }
                            }
                        
                            $this->setAndUpdateCoursesForCurrentYear($studentId);
                            $this->sendTestEmail($studentId);
                        }
                    }                    // Insert new students
                    $newStudents = array_diff($chunk, $existingStudents);
                    $studentsToInsert = [];
                    foreach ($newStudents as $studentNumber) {
                        $studentsToInsert[] = [
                            'student_number' => $studentNumber,
                            'academic_year' => $academicYear,
                            'term' => $term,
                            'status' => $status
                        ];
                    }

                    Student::insert($studentsToInsert);

                    // Trigger your setAndSaveCourses function for new students
                    foreach ($newStudents as $studentNumber) {
                        if($status == 3){
                            $this->setAndSaveCoursesForCurrentYear($studentNumber);
                        }else{
                            $this->setAndSaveCourses($studentNumber);
                        }
                        $this->sendTestEmail($studentNumber);                        

                        $getNrc = BasicInformation::find($studentNumber);
                        $nrc = trim($getNrc->GovernmentID); // Access GovernmentID property on the first student detail
                        $email = $getNrc->PrivateEmail;
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $email = trim($getNrc->PrivateEmail);
                        } else {
                            $email = $studentNumber . '@lmmu.ac.zm';
                        }

                        $nrc = trim($getNrc->GovernmentID);
                        
                            $student = User::create([
                                'name' => $studentNumber,
                                'email' => $email,
                                'password' => bcrypt($nrc),                                
                            ]);
                                                    
                        // Create the "Student" role if it doesn't exist
                        $studentRole = Role::firstOrCreate(['name' => 'Student']);
                        
                        // Assign the "Student" role to the user
                        $student->assignRole($studentRole); 
                        
                        // Find or create the "Student" permission
                        $studentPermission = Permission::firstOrCreate(['name' => 'Student']);
                        
                        // Assign the "Student" permission to the user
                        $student->givePermissionTo($studentPermission);
                    }
                }

                // Provide a success message
                return redirect()->back()->with('success', 'Students imported successfully and the dockets have been sent.');
            } catch (\Throwable $e) {
                // Handle any unexpected errors during import
                return redirect()->back()->with('error', 'Failed to upload students: ' . $e->getMessage());
            }
        }

        // Handle errors or validation failures
        return redirect()->back()->with('error', 'Failed to upload students.');
    }

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

    public function indexSupsAndDef(Request $request){
        $academicYear= 2023;
        $courseName = null;
        $courseId = null;
        if($request->input('student-number')){
            $student = Student::query()
                        ->where('student_number','=', $request->input('student-number'))
                        ->where('status','=', 3)
                        ->first();
            if($student){
                $getStudentNumber = $student->student_number;
                $studentNumbers = [$getStudentNumber];
                $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::where('status', 3)->pluck('student_number')->toArray();
            $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
        }
        return view('docketSupsAndDef.index', compact('results','courseName','courseId'));
    }

    
    public function assignStudentsRoles(){
        set_time_limit(1200000);
        $users = User::join('students', 'students.student_number', '=', 'users.name')
            ->where('students.status', 1)
            ->doesntHave('roles')
            ->get();

        // Loop through the users
        foreach ($users as $user) {
            // Create the "Student" role if it doesn't exist
            $studentRole = Role::firstOrCreate(['name' => 'Student']);                        
            // Assign the "Student" role to the user
            $user->assignRole($studentRole);                    
            // Find or create the "Student" permission
            $studentPermission = Permission::firstOrCreate(['name' => 'Student']);                    
            // Assign the "Student" permission to the user
            $user->givePermissionTo($studentPermission);
        }

        return back()->with('success', 'Roles Updated successfully.');
    }

    public function students2023ExamResults($studentNumber){
        $academicYear= 2023;
        $studentNumbers = [$studentNumber];
        $studentsDetails = $this->getAppealStudentDetails($academicYear, $studentNumbers)->get();
        $student = $studentsDetails->first();

        if($student->RegistrationStatus == 'NO REGISTRATION'){
            $results = $this->getStudent2023ExamResults($studentNumber,$academicYear);
            return view('docket.examinationResults', compact('results','studentNumber'));
        }else{
            return back()->with('error', 'UNAUTHORIZED ACCESS');
        }
    }

    public function createAccountsForStudentsNotInUsersTableAndSendEmails(){
        $academicYear = 2023;
        $studentNumbers = Student::where('status', 1)->pluck('student_number')->toArray();
        
        $studentsDetails = $this->getAppealStudentDetails($academicYear, $studentNumbers)
                    ->get()
                    ->filter(function ($student) {
                        return $student->RegistrationStatus == 'NO REGISTRATION';
                    });
        foreach ($studentsDetails as $student) {
            $studentNumber = $student->StudentID;
            $getNrc = BasicInformation::find($studentNumber);
            $nrc = trim($getNrc->GovernmentID);
            $email = $studentNumber . '@lmmu.ac.zm';
        
            // Check if the student_number or email exists in the users table
            $user = User::where('name', $studentNumber)
                        ->orWhere('email', $email)
                        ->first();
        
            if (!$user) {
                User::create([
                    'name' => $studentNumber,
                    'email' => $email,
                    'password' => bcrypt($nrc),
                ]);
        
                $this->sendEmailNotification($studentNumber);
            }
        }
        return back()->with('success', 'Emails sent successfully.');
    }

    

    public function exportAppealStudents(){
        $studentNumbers = Student::where('status', 1)
            ->whereHas('user', function ($query) {
                $query->where('created_at', '<=', '2023-12-06');
            })
            ->pluck('student_number')
            ->toArray();
        $academicYear= 2023;
        $headers = [
            'First Name',
            'Middle Name',
            'Surname',
            'Email',
            'Balance',            
            'Gender',
            'Student Number',
            'NRC',
            'Programme',
            'School',
            'Study Mode',
            'Year Of Study',
            'Registration Status'          
        ];
        
        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'PrivateEmail',
            'Amount',
            'Sex',
            'StudentID',                        
            'GovernmentID',
            'Name',
            'Description',
            'StudyType',
            'YearOfStudy',
            'RegistrationStatus'
        ];
        
        $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->get();        
        $filename = 'StudentsOnSisReports';
        
        return $this->exportData($headers, $rowData, $results, $filename);
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
        $status = $student->status;
        
        if($status == 3){
            $this->setAndUpdateCoursesForCurrentYear($studentId);
            
        }else{
            
            $this->setAndUpdateCourses($studentId);
        }
        // Retrieve all unique Student values from the Course model
        $courses = Courses::where('Student', $studentId)->get();
        // return $courses;

        // Pass the $students variable to the view
        // return view('your.view.name', compact('students'));
        
        return view('docket.show',compact('courses','studentResults','status'));
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

            // if($student->status == 3){
            //     $this->setAndSaveCoursesForCurrentYear($studentId);
            // }else{
            //     $this->setAndSaveCourses($studentId);
            // }
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
