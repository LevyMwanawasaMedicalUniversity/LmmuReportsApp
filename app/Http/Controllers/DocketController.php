<?php

namespace App\Http\Controllers;

use App\Mail\ExistingStudentMail;
use App\Mail\NewStudentMail;
use App\Mail\NMCZRepeatCourseEmail;
use App\Models\AllCourses;
use App\Models\BasicInformation;
use App\Models\Billing;
use App\Models\CourseElectives;
use App\Models\CourseRegistration;
use App\Models\Courses;
use App\Models\LMMAXStudentsContinousAssessment;
use App\Models\NMCZRepeatCourses;
use App\Models\SageClient;
use App\Models\SisCourses;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use ZipArchive;

class DocketController extends Controller
{
    public function testAssess(){

        $assessmnets = LMMAXStudentsContinousAssessment::all();

        return $assessmnets;
    }

    public function sendEmailNotice(){
        set_time_limit(1200000);
        $academicYear = 2024;
        $studentNumbers = $this->getAllStudentsRegisteredInASpecificAcademicYear($academicYear )->pluck('ID')->toArray();
        // return $studentNumbers;
        
        
        
        $studentsDetails = $this->getAppealStudentDetails($academicYear, $studentNumbers)
                    ->get();
        // return $studentsDetails;
        foreach ($studentsDetails as $student) {
            $studentNumber = $student->StudentID;
            $this->sendEmailNotification($studentNumber);            
        }
        return back()->with('success', 'Emails sent successfully.');
    }

    public function updateNameInUsersTableToMatchStudentIdCollectedFromBasicInformationUsingEmail(){
        set_time_limit(1200000); // Increase the maximum execution time
    
        $academicYear = 2023;
        $studentNumbers = Student::pluck('student_number')->toArray();
        $failedUpdates = []; // Array to store failed updates
    
        // Get all users with role 'Student' and whose name matches any of the student numbers
        $users = User::whereHas('students', function ($query) use ($studentNumbers) {
            $query->whereIn('student_number', $studentNumbers);
        })
        ->whereIn('name', $studentNumbers)
        ->role('Student')
        ->get();
    
        // Update emails for these users
        foreach ($users as $user) {
            try {
                $user->update(['email' => $user->name . $user->name . '@lmmu.ac.zm']);
            } catch (\Exception $e) {
                $failedUpdates[] = ['id' => $user->id, 'error' => $e->getMessage()];
            }
        }
    
        $studentsDetails = $this->getAppealStudentDetails($academicYear, $studentNumbers)->get();
    
        foreach ($studentsDetails as $student) {
            try {
                $studentNumber = $student->StudentID;
                $email = trim($student->PrivateEmail);
                $user = User::where('name', $studentNumber)->first();
                if($user){
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $user->update(['email' => $email]);
                    } else {
                        $user->update(['email' => $studentNumber . '@lmmu.ac.zm']);
                    }
                }
            } catch (\Exception $e) {
                $failedUpdates[] = ['id' => $studentNumber, 'error' => $e->getMessage()];
            }
        }
    
        // Export failed updates to CSV
        $file = fopen('failed_updates.csv', 'w');
        fputcsv($file, ['ID', 'Error']);
        foreach ($failedUpdates as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    
        return back()->with('success', 'Emails Users Updated. Failed updates exported to failed_updates.csv.');
    }

    public function resetAllStudentsPasswords(){
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
                        $nrc = trim($studentResults->GovernmentID); // Access GovernmentID property on the first student detail
                        
                        $student->update([
                            'password' => '12345678'                            
                        ]);
                    
                        // $this->sendEmailNotification($student->name);
                    }
                }
            });

        return back()->with('success', 'Passwords reset successfully.');
    }

    public function sendNMCZRepeatCourseEmail($studentID){
        $privateEmail = BasicInformation::find($studentID);
    
        if ($privateEmail) {
            $email = trim($privateEmail->PrivateEmail);
        } else {
            // Handle the case where there's no BasicInformation record with the provided $studentID
            // For example, you might want to log an error message and return
            Log::error("No BasicInformation record found for student ID: $studentID");
            return "No BasicInformation record found for student ID: $studentID";
        }
    
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // $email is a valid email address
            $sendingEmail = $email;
        } else {
            // $email is not a valid email address
            $sendingEmail = 'azwel.simwinga@lmmu.ac.zm';
        }
        Mail::to($sendingEmail)->send(new NMCZRepeatCourseEmail($studentID));        
    
        return "Test email sent successfully!";

    }

    public function uploadNMCZRepeatStudents(Request $request){
        set_time_limit(1200000);
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
            'term' => 'required',
        ]);

        // Get the academic year, term, and status from the form
        $academicYear = $request->input('academicYear');
        $term = $request->input('term');
        $status = 5;

        // Process the uploaded file
        if ($request->hasFile('excelFile')) {
            $file = $request->file('excelFile');

            // Initialize the Box/Spout reader
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());

            $isHeaderRow = true; // Flag to identify the header row
            $data = [];

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    if ($isHeaderRow) {
                        $isHeaderRow = false;
                        continue;
                    }
                    // Assuming the student number is in the first column (index 1)
                    $studentNumber = $row->getCellAtIndex(0)->getValue();
                    // Assuming the repeat courses are in the second column (index 2)
                    $repeatCourses = explode(',', $row->getCellAtIndex(1)->getValue());

                    $data[] = [
                        'student_number' => $studentNumber,
                        'repeat_courses' => $repeatCourses,
                    ];
                }
            }

            $reader->close();

            try {
                foreach ($data as $entry) {
                    // Check if student exists
                    $existingStudent = Student::where('student_number', $entry['student_number'])
                        ->where('academic_year', $academicYear)
                        ->where('term', $term)
                        ->first();
                    $existingUsers = User::where('name', $entry['student_number'])->exists();

                    if ($existingStudent) {
                        // Update status if needed
                        if ($status == 3) {
                            $existingStudent->update(['status' => 5]);
                        }
                    } else {
                        // Insert new student
                        $existingStudent = Student::create([
                            'student_number' => $entry['student_number'],
                            'academic_year' => $academicYear,
                            'term' => $term,
                            'status' => $status
                        ]);
                    }

                    $studentNumber = trim($entry['student_number']);

                    $studentController = new StudentsController();

                    if (!$existingUsers) {
                        $studentController->createUserAccount($studentNumber);
                    }

                    $this->sendNMCZRepeatCourseEmail($studentNumber);
                    // Loop through repeat courses for the student
                    foreach ($entry['repeat_courses'] as $course) {
                        // Insert into NMCZRepeatCourses model
                        NMCZRepeatCourses::updateOrCreate([
                            'studnent_number' => $existingStudent->student_number,
                            'course_code' => trim($course),
                            'academic_year' => $academicYear,
                        ]);
                    }
                }
                // Provide a success message
                return redirect()->back()->with('success', 'Students and their repeat courses imported successfully.');
            } catch (\Throwable $e) {
                // Handle any unexpected errors during import
                return redirect()->back()->with('error', 'Failed to upload students and their repeat courses: ' . $e->getMessage());
            }
        }
        // Handle errors or validation failures
        return redirect()->back()->with('error', 'Failed to upload students and their repeat courses.');
    }

    public function uploadStudents(Request $request){
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
                            // $privateEmail = BasicInformation::find($studentId);
                            // $email = $privateEmail->PrivateEmail;
                        
                            // // Check if the user exists before trying to update
                            // $user = User::where('name', $studentId)->first();
                            // if ($user) {
                            //     if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            //         // $email is a valid email address
                            //         $user->update(['email' => $email]);
                            //     } else {
                            //         // $email is not a valid email address
                            //         $user->update(['email' => $studentId . '@lmmu.ac.zm']);
                            //     }
                            // }
                        
                            // $user = User::where('name', $studentId)->first();

                            // if ($user) {
                                // Find or create the "Student" role
                                // $studentRole = Role::firstOrCreate(['name' => 'Student']);

                                // // Assign the "Student" role to the user
                                // $user->assignRole($studentRole);

                                // // Find or create the "Student" permission
                                // $studentPermission = Permission::firstOrCreate(['name' => 'Student']);

                                // // Assign the "Student" permission to the user
                                // $user->givePermissionTo($studentPermission);

                            $this->setAndUpdateCoursesForCurrentYear($studentId);
                            // }
                            // $this->sendTestEmail($studentId);
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
                                            

                        $getNrc = BasicInformation::find($studentNumber);
                        $nrc = trim($getNrc->GovernmentID); // Access GovernmentID property on the first student detail
                        $email = $getNrc->PrivateEmail;
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $email = trim($getNrc->PrivateEmail);
                        } else {
                            $email = $studentNumber . '@lmmu.ac.zm';
                        }

                        $nrc = trim($getNrc->GovernmentID);

                        $existingUser = User::where('email', $email)->first();
                        if ($existingUser) {
                            
                            $email = $studentNumber . $email;
                        }
                        try{
                            $student = User::create([
                                'name' => $studentNumber,
                                'email' => $email,
                                'password' => '12345678',                                
                            ]);
                        }catch(Exception $e){
                            continue;
                        }
                                                    
                        // Create the "Student" role if it doesn't exist
                        $studentRole = Role::firstOrCreate(['name' => 'Student']);
                        
                        // Assign the "Student" role to the user
                        $student->assignRole($studentRole); 
                        
                        // Find or create the "Student" permission
                        $studentPermission = Permission::firstOrCreate(['name' => 'Student']);
                        
                        // Assign the "Student" permission to the user
                        $student->givePermissionTo($studentPermission);
                        // $this->sendTestEmail($studentNumber); 
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

    private function processStudentChunk($chunk, $academicYear, $term, $status){
        // Check for duplicate students in a single database query
        $existingStudents = Student::whereIn('student_number', $chunk)
            ->where('academic_year', $academicYear)
            ->where('term', $term)
            ->pluck('student_number')
            ->toArray();

        // Update existing students
        foreach ($existingStudents as $studentId) {
            $getStudentStatus = Student::where('student_number', $studentId)->first();
            if(!$getStudentStatus->status == 3){
                Student::where('student_number', $studentId)
                    ->update(['status' => 3]);

                $this->setAndUpdateCoursesForCurrentYear($studentId);
                $user = User::where('name', $studentId)->first();

                if ($user) {
                    // Find or create the "Student" role
                    $studentRole = Role::firstOrCreate(['name' => 'Student']);

                    // Assign the "Student" role to the user
                    $user->assignRole($studentRole);

                    // Find or create the "Student" permission
                    $studentPermission = Permission::firstOrCreate(['name' => 'Student']);

                    // Assign the "Student" permission to the user
                    $user->givePermissionTo($studentPermission);
                }
                $this->sendTestEmail($studentId);
            }
        }

        // Insert new students
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

            // Create new users and assign roles and permissions
            $getNrc = BasicInformation::find($studentNumber);
            $nrc = trim($getNrc->GovernmentID); // Access GovernmentID property on the first student detail
            $email = $getNrc->PrivateEmail;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = $studentNumber . '@lmmu.ac.zm';
            }

            $nrc = trim($getNrc->GovernmentID);
            $defaultPassword = '12345678';
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                $email = $studentNumber . $email;
            }

            try{
                $student = User::create([
                    'name' => $studentNumber,
                    'email' => $email,
                    'password' => Hash::make($defaultPassword),                                
                ]);
            }catch(Exception $e){
                continue;
            }

            // Create the "Student" role if it doesn't exist
            $studentRole = Role::firstOrCreate(['name' => 'Student']);

            // Assign the "Student" role to the user
            $student->assignRole($studentRole); 

            // Find or create the "Student" permission
            $studentPermission = Permission::firstOrCreate(['name' => 'Student']);

            // Assign the "Student" permission to the user
            $student->givePermissionTo($studentPermission);
            $this->sendTestEmail($studentNumber); 
        }
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
        $isStudentRegisteredOnEdurole = $this->checkIfStudentIsRegistered($studentNumber)->exists();
        $allResults = $this->getAllStudentExamResults($studentNumber);

        // return $allResults;
        // return "we here";
        $isStudentRegisteredOnSisReports = CourseRegistration::where('StudentID', $studentNumber)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();
        
        $results = $this->getStudent2023ExamResults($studentNumber,$academicYear);
        return view('docket.examinationResults', compact('results','studentNumber','allResults','isStudentRegisteredOnEdurole','isStudentRegisteredOnSisReports'));
        
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

    public function indexNmczRepeating(Request $request){
        $academicYear= 2024;
        $courseName = null;
        $courseId = null;
        if($request->input('student-number')){
            $student = Student::query()
                        ->where('student_number','=', $request->input('student-number'))
                        ->where('status','=', 5)
                        ->first();
            if($student){
                $getStudentNumber = $student->student_number;
                $studentNumbers = [$getStudentNumber];
                $results = $this->getAppealNMCZStudentDetails($academicYear, $studentNumbers)->paginate(15);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::where('status', 5)->pluck('student_number')->toArray();
            $results = $this->getAppealNMCZStudentDetails($academicYear, $studentNumbers)->paginate(15);
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

    public function resetStudent($studentId){
        $user = User::where('name', $studentId)->first();
        if ($user && $user->hasRole('Student')) {
            $username = $user->name;
            try {
                
                
                $privateEmail = BasicInformation::find($username)->PrivateEmail;
                if (!filter_var($privateEmail, FILTER_VALIDATE_EMAIL)) {
                    $privateEmail = $username . '@lmmu.ac.zm';
                }
                $user->update([
                    'email' => $privateEmail,
                    'password' => Hash::make('12345678')
                ]);
                $student = Student::where('student_number', $studentId)
                    ->where('status', 4)
                    ->first();
                if ($student) {
                    Mail::to($privateEmail)->send(new ExistingStudentMail($studentId));
                }
                
                $this->sendTestEmail($username);
            } catch (\Exception $e) {
                // Log the exception or handle it as needed
            }
        }
        return redirect()->back()->with('success', 'Password reset successfully.');
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

        try{
            $subQuery = Billing::select(
                'StudentID',
                'Amount',
                'Year',
                DB::raw('ROW_NUMBER() OVER (PARTITION BY StudentID, Year ORDER BY Date DESC) AS rn')
            )
            ->where('Description', 'NOT LIKE', '%NULL%')
            ->where('PackageName', 'NOT LIKE', '%NULL%')
            ->where ('Year', 2024)
            ->where('StudentID', $studentId)
            ->first();
            $invoice2024 = $subQuery->Amount;
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'No invoice found for 2024. Please ensure that your courses are approved and your have been invoiced for 2024. Visit your coordinator for courses approval and accounts for invoicing if you have not been invoiced.');
        }

        if(!$invoice2024){
            return redirect()->back()->with('error', 'No invoice found for 2024. Please ensure that your courses are approved and your have been invoiced for 2024. Visit your coordinator for courses approval and accounts for invoicing if you have not been invoiced.');
        }

        $studentPaymentInformation = SageClient::select    (
            'DCLink',
            'Account',
            'Name',            
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0          
                ELSE pa.Credit 
                END) AS TotalPayments'),
            DB::raw('SUM(pa.Credit) as TotalCredit'),
            DB::raw('SUM(pa.Debit) as TotalDebit'),
            DB::raw('SUM(pa.Debit) - SUM(pa.Credit) as TotalBalance'),
            
        )
        ->where('Account', $studentId)
        ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
        
        ->groupBy('DCLink', 'Account', 'Name')
        ->first();
        $balance = $studentPaymentInformation->TotalBalance;

        $percentageOfInvoice = ($balance / $invoice2024) * 100;   

        
        // return $percentageOfInvoice;

        if($percentageOfInvoice > 55){
            return redirect()->back()->with('error', 'You must have cleared at least 75% of your 2024 fees to view your docket.');
        }
        $isStudentRegistered = $this->checkIfStudentIsRegistered($studentId)->exists();
        $status = $student->status;
        
        // if($status == 3){
        $studentExistsInStudentsTable = Courses::where('Student', $studentId)->whereNotNull('updated_at')->exists();
        if (!$studentExistsInStudentsTable) {
            if($isStudentRegistered){
                $this->setAndUpdateRegisteredCourses($studentId);
            }else{
            $this->setAndUpdateCoursesForCurrentYear($studentId); 
        }}else {
            $this->setAndUpdateCourses($studentId);
        }                      
        // }        // Retrieve all unique Student values from the Course model
        // $courses = Courses::where('Student', $studentId)->get();     THIS IS THE OLD LOGIC
        // return $courses;
        $courses = Courses::where('Student', $studentId)->get();
        if($courses->isEmpty()){
            $courses = CourseElectives::select('courses.Name as CourseID', 'courses.CourseDescription as CourseName')
            ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
            ->where('course-electives.Year', 2024)
            ->where('course-electives.StudentID',  $studentId)->get();
        }
        // Pass the $students variable to the view
        // return view('your.view.name', compact('students'));
        
        return view('docket.show',compact('courses','studentResults','status'));
    }

    public function showStudentNmcz($studentId){
        // try{
            
        // }catch(Exception $e){
            
        // }
        $academicYear= 2024;
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
        // $this->setAndUpdateCourses($studentId);
        // Retrieve all unique Student values from the Course model
        // $courses = Courses::where('Student', $studentId)->get();
        $courses = CourseRegistration::where('StudentID',  $studentId)->get();
        if($courses->isEmpty()){
            $courses = CourseElectives::select('courses.Name as CourseID', 'courses.CourseDescription as CourseName')
            ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
            ->where('course-electives.Year', 2024)
            ->where('course-electives.StudentID',  $studentId)->get();
        }
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
                            ->where('status','=', 3)
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
                        Student::where('student_number', $studentId)->update(['course_updated' => 1]);
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

    public function nmczRepeatImport(){
        return view('docketNmcz.importRepeat');
    }

    public function allStudentsImport(){        
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

    public function bulkExportAllCoursesToPdfWithStudentsTakingThem(){
        set_time_limit(1200000);
        $zip = new ZipArchive;
        $zipFileName = 'all_courses.zip';
        $zipPath = public_path($zipFileName);
    
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            $courses = $this->getDefferedOrSuplementaryCourses();
            $pdfs = [];
            foreach ($courses as $course) {
                $courseDescription = str_replace('/', '-', $course->CourseDescription);
                $pdfs[$courseDescription.'-'.$course->Name] = $this->exportCoursesToPdfWithStudentsTakingThem($course->ID)->output();
            }
            foreach ($pdfs as $fileName => $pdf) {
                $zip->addFromString($fileName.'.pdf', $pdf);
            }
            $zip->close();
        }
    
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    
    public function exportCoursesToPdfWithStudentsTakingThem($courseId){
        $theCourse = SisCourses::find($courseId);
        $academicYear = 2023;
        $courseCode = $theCourse->Name;
        $courseName = $theCourse->CourseDescription;
    
        $studentNumbers = Courses::where('Course', $courseCode)
                ->join('students', 'students.student_number', '=', 'courses.Student')
                ->where('students.status', 3)
                ->with('students') // Eager loading
                ->pluck('Student')->toArray();
                
        $students = $this->getAppealStudentDetails($academicYear, $studentNumbers)->get();
    
        $pdf = Pdf::loadView('docket.exportCourses', compact('students','courseName','courseCode'));
        return $pdf;
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
