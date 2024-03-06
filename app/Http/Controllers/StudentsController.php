<?php

namespace App\Http\Controllers;

use App\Mail\ExistingStudentMail;
use App\Mail\NewStudentMail;
use App\Models\BasicInformation;
use App\Models\CourseElectives;
use App\Models\CourseRegistration;
use App\Models\Courses;
use App\Models\EduroleCourses;
use App\Models\SisReportsSageInvoices;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StudentsController extends Controller
{
    public function importStudentsFromBasicInformation(){
        set_time_limit(120000000);
        $maxAttempts = 10;
    
        $studentIds = $this->getStudentsToImport()->pluck('StudentID')->toArray();
        $studentIdsChunks = array_chunk($studentIds, 1000);
    
        $existingUsers = User::whereIn('name', $studentIds)->get()->keyBy('name');
    
        foreach ($studentIdsChunks as $studentIdsChunk) {
            foreach ($studentIdsChunk as $studentId) {
                $student = Student::where('student_number', $studentId)->first();
    
                if (!isset($existingUsers[$studentId])) {
                    $this->createUserAccount($studentId);
                }
    
                $privateEmail = BasicInformation::find($studentId);
                $sendingEmail = trim($privateEmail->PrivateEmail);
                $email = $this->validateAndPrepareEmail($privateEmail->PrivateEmail, $studentId);
                $studentAccount = User::where('name', $studentId)->first();
                $studentAlreadyCreatedAndUpdated = Student::where('student_number', $studentId)->where('status', 4)->exists();
                if ($studentAlreadyCreatedAndUpdated) {
                    $studentAccount->update(['email' => $email]);
                    $this->sendEmailToStudent($sendingEmail, $studentId, $maxAttempts);
                    continue;
                } 
    
                if ($student) {
                    $student->update(['status' => 4]);
                    $studentAccount->update(['email' => $email]);
                    $this->sendEmailToStudent($sendingEmail, $studentId, $maxAttempts);
                } else {
                    Student::create([
                        'student_number' => $studentId,
                        'academic_year' => 2024,
                        'term' => 1,
                        'status' => 4
                    ]);
                    
                    $this->sendEmailToStudent($sendingEmail, $studentId, $maxAttempts);
                }
            }
        }
    
        return redirect()->back()->with('success', 'Students imported successfully and accounts created.');
    }
    
    private function validateAndPrepareEmail($email, $studentId) {
        $email = trim($email);
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $email = $studentId . $email . '@lmmu.ac.zm';
        } elseif($email == null) {
            $email = $studentId . '@lmmu.ac.zm';
        }
        return $email;
    }
    
    private function sendEmailToStudent($email, $studentId, $maxAttempts) {
        $sendingEmail = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'registration@lmmu.ac.zm';
        $attempts = 0;
        while ($attempts < $maxAttempts) {
            try {                
                Mail::to($sendingEmail)->send(new ExistingStudentMail($studentId));                
                break;
            } catch (\Exception $e) {
                error_log('Unable to send email: ' . $e->getMessage());
                $attempts++;
                if ($attempts === $maxAttempts) {
                    error_log('Failed to send email after ' . $maxAttempts . ' attempts.');
                }
                sleep(1);
            }
        }
    }

    public function importSingleStudent(){
        return view('allStudents.importSingleStudent');
    }

    public function uploadSingleStudent(Request $request){
        $maxAttempts = 10; // Define max attempts for email sending
        $studentId = $request->input('studentId');
        $results = $this->checkIfStudentIsRegistered($studentId)->exists();
        if ($results) {
            return redirect()->back()->with('error', 'Student already registered.');
        }
        $results = BasicInformation::find($studentId);
        if (!$results) {
            return redirect()->back()->with('error', 'Student not found on Edurole.');
        }
        $email = trim($results->PrivateEmail);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $sendingEmail = $email;
        } else {
            $sendingEmail = 'registration@lmmu.ac.zm';
        }
        $student = Student::where('student_number', $studentId)->first();
        if ($student) {
            $student->update(['status' => 4]);
            $this->sendEmailToStudent($sendingEmail, $studentId, $maxAttempts, new ExistingStudentMail($student));
        } else {
            Student::create([
                'student_number' => $studentId,
                'academic_year' => 2024,
                'term' => 1,
                'status' => 4
            ]);
            $existingUsers = User::where('name', $studentId)->get()->keyBy('name');
            if (!isset($existingUsers[$studentId])) {
                $this->createUserAccount($studentId);
                $this->sendEmailToStudent($sendingEmail, $studentId, $maxAttempts, new NewStudentMail($studentId));
            }            
        }           
        return redirect()->route('students.showStudent',$studentId)->with('success', 'Student created successfully.');
    }

    private function createUserAccount($studentId){
        // Get the student's email from BasicInformation
        $basicInfo = BasicInformation::find($studentId);
        $email = trim($basicInfo->PrivateEmail);
        $email =  $this->validateAndPrepareEmail($email, $studentId);
        try {
            $user = User::create([
                'name' => $studentId,
                'email' => $email,
                'password' => '12345678',
            ]);

            // Assign roles and permissions to the user
            $studentRole = Role::firstOrCreate(['name' => 'Student']);
            $studentPermission = Permission::firstOrCreate(['name' => 'Student']);
            $user->assignRole($studentRole);
            $user->givePermissionTo($studentPermission);
        } catch (Exception $e) {
            // Handle any errors during user account creation
        }
    }

    public function printIDCard( $studentId){

        
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
        ->where('Year', 2024)
        ->where('Semester', 1)
        ->exists();

        if (!$checkRegistration) {            
            return redirect()->back()->with('error', 'UNREGISTERED STUDENT');
        }

        $studentInformation = $this->getAppealStudentDetails(2024, [$studentId])->first();
        // return $studentInformation;

        return view('allStudents.printIdCard',compact('studentInformation'));
    }

    public function viewDocket(){
        $user = Auth::user(); // Get the currently logged-in user

        // If the user doesn't have the "Student" role, return the home view
        if (!$user->hasRole('Student')) {
            return view('home');
        }

        // return $user->getRoleNames();

        $student = Student::where('student_number', $user->name)->first();
        // return $student;

        // If the student doesn't exist, return back with an error message
        if (is_null($student)) {
            return back()->with('error', 'NOT STUDENT.');
        }

        $academicYear = 2023;
        $studentResults = $this->getAppealStudentDetails($academicYear, [$user->name])->first();

        // Update courses based on the student's status
        if ($student->status == 3 && !Courses::where('Student', $user->name)->whereNotNull('updated_at')->exists()) {
            $this->setAndUpdateCoursesForCurrentYear($user->name);
        } else {
            $this->setAndUpdateCourses($user->name);
        }
        // return $student->status;

        $courses = Courses::where('Student', $user->name)->get();

        // Cast the status to an integer
        $status = (int) $student->status;

        // Return the appropriate view based on the student's status
        $viewName = match ($status) {
            1 => 'docket.studentViewDocket',
            4 => 'docket.studentViewDocket',
            2 => 'docketNmcz.studentViewDocket',
            3 => 'docketSupsAndDef.studentViewDocket',
        };

        return view($viewName, compact('studentResults', 'courses'));
    }   

    public function setAndSaveCoursesForCurrentYearRegistration($studentId){
        // First, attempt to get courses for failed students
        $dataArray = $this->getCoursesForFailedStudents($studentId);
        $failed = $dataArray ? 1 : 2;
    
        // If no failed courses found, try to find unregistered student courses
        if (!$dataArray) {
            $dataArray = $this->findUnregisteredStudentCourses($studentId);
        }
    
        // If still no data, exit early
        if (empty($dataArray)) {
            return ['dataArray' => collect(), 'failed' => $failed];
        }
    
        // Retrieve existing courses for the student
        $existingCourses = Courses::where('Student', $studentId)
            ->whereIn('Course', collect($dataArray)->pluck('Course'))
            ->pluck('Course')
            ->toArray();
    
        // Prepare new courses to insert
        $coursesToInsert = collect($dataArray)->reject(function ($item) use ($existingCourses) {
            return in_array($item['Course'], $existingCourses);
        })->map(function ($item) use ($studentId) {
            return [
                'Student' => $studentId,
                'Program' => $item['Program'],
                'Course' => $item['Course'],
                'Grade' => $item['Grade'],
            ];
        });
    
        // Insert new courses if there are any
        if ($coursesToInsert->isNotEmpty()) {
            Courses::insert($coursesToInsert->all());
        }
    
        // Convert the array to a collection of objects
        $dataArray = collect($dataArray)->map(function ($item) {
            return (object)$item;
        });
    
        return ['dataArray' => $dataArray, 'failed' => $failed];
    }

    public function deleteEntireRegistration(Request $request){
        $studentId = $request->input('studentId');
        $year = $request->input('year');
        
    
        CourseRegistration::where('StudentID', $studentId)
            ->where('Year', $year)
            ->delete();
    
        return redirect()->back()->with('success', 'Registration deleted successfully');
    }

    public function deleteCourseInRegistration(Request $request){
        $studentId = $request->input('studentId');
        $year = $request->input('year');
        $courseId = $request->input('courseId');
        
        CourseRegistration::where('StudentID', $studentId)
            ->where('Year', $year)
            ->where('CourseID', $courseId)
            ->delete();
    
        return redirect()->back()->with('success', 'Registration deleted successfully');
    }

    public function studentRegisterForCourses($studentId) {
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();
    
        if ($checkRegistration) {
            $checkRegistration = collect($this->getStudentRegistration($studentId));
            $courseIds = $checkRegistration->pluck('CourseID')->toArray();
            
            $checkRegistration = EduroleCourses::query()->whereIn('Name', $courseIds)->get();
            
            $studentInformation = $this->getAppealStudentDetails(2024, [$studentId])->first();
            
            return view('allStudents.registrationPage', compact('studentId','checkRegistration','studentInformation'));
        }
    
        $studentsPayments = $this->getStudentsPayments($studentId)->first();
    
        $registrationResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId); 
        $courses = $registrationResults['dataArray'];
        $failed = $registrationResults['failed'];
        
        $coursesArray = $courses->pluck('Course')->toArray();
    
        $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();
    
        if (str_starts_with($studentId, '190')) {
            $studentsProgramme->transform(function ($studentProgramme) {
                $studentProgramme->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $studentProgramme->CodeRegisteredUnder);
                return $studentProgramme;
            });
        }
        if ($studentsProgramme->isEmpty()) {
            return redirect()->back()->with('warning', 'No courses found for the student.');
        }
    
        $programeCode = trim($studentsProgramme->first()->CodeRegisteredUnder);
        $theNumberOfCourses = $this->getCoursesInASpecificProgrammeCode($programeCode)->count();
    
        $allInvoicesArray = SisReportsSageInvoices::all()->mapWithKeys(function ($item) {
            return [trim($item['InvoiceDescription']) => $item];
        })->toArray();
    
        // $processCourse = function ($course) use ($allInvoicesArray) {
        //     $courseArray = $course->toArray();
        //     $key = trim($course->CodeRegisteredUnder);
        //     $matchedKey = array_key_exists($key, $allInvoicesArray) ? $key : null;
            
        //     if ($matchedKey) {
        //         $courseArray = array_merge($courseArray, $allInvoicesArray[$matchedKey]);
        //     } else {
        //         Log::info('No match found for course: ' . $key);
        //     }
    
        //     $courseArray['numberOfCourses'] = $this->getCoursesInASpecificProgrammeCode($course->CodeRegisteredUnder)->count();
            
        //     return (object) $courseArray;
        // };
    
        $currentStudentsCourses = $studentsProgramme;
        $studentDetails = $this->getAppealStudentDetails(2024, [$studentId])->first();
    
        return view('allStudents.studentSelfRegistration', compact('studentDetails','courses', 'currentStudentsCourses', 'studentsPayments', 'failed', 'studentId', 'theNumberOfCourses'));
    }

    public function registerStudent($studentId) {
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();
    
        if ($checkRegistration) {
            $checkRegistration = collect($this->getStudentRegistration($studentId));
            $courseIds = $checkRegistration->pluck('CourseID')->toArray();
            
            $checkRegistration = EduroleCourses::query()->whereIn('Name', $courseIds)->get();
            
            $studentInformation = $this->getAppealStudentDetails(2024, [$studentId])->first();
            
            return view('allStudents.registrationPage', compact('studentId','checkRegistration','studentInformation'));
        }
    
        $studentsPayments = $this->getStudentsPayments($studentId)->first();
        // No need to return $studentsPayments here
        
        $registrationResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId);
        $courses = $registrationResults['dataArray'];
        // return $courses;
        $failed = $registrationResults['failed'];
        
        $coursesArray = $courses->pluck('Course')->toArray();
        $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();
        
        // If the student number starts with 190, replace 2023 with 2019 in CodeRegisteredUnder
        if (str_starts_with($studentId, '190')) {
            $studentsProgramme->transform(function ($studentProgramme) {
                $studentProgramme->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $studentProgramme->CodeRegisteredUnder);
                return $studentProgramme;
            });
        }
        
        if ($studentsProgramme->isEmpty()) {
            return redirect()->back()->with('warning', 'No courses found for the student. Student Has Graduated');
        }

        
        $programeCode = trim($studentsProgramme->first()->CodeRegisteredUnder);
    
        $theNumberOfCourses = $this->getCoursesInASpecificProgrammeCode($programeCode)->count();
    
        $allInvoicesArray = SisReportsSageInvoices::all()->mapWithKeys(function ($item) {
            return [trim($item['InvoiceDescription']) => $item];
        })->toArray();
        // return $allInvoicesArray;
        // $processCourse = function ($course) use ($allInvoicesArray) {
        //     $courseArray = $course->toArray();
        //     $key = trim($course->CodeRegisteredUnder);
        //     $matchedKey = array_key_exists($key, $allInvoicesArray) ? $key : null;
            
        //     if ($matchedKey) {
        //         $courseArray = array_merge($courseArray, $allInvoicesArray[$matchedKey]);
        //     } else {
        //         Log::info('No match found for course: ' . $key);
        //     }
    
        //     $courseArray['numberOfCourses'] = $this->getCoursesInASpecificProgrammeCode($course->CodeRegisteredUnder)->count();
            
        //     return (object) $courseArray;
        // };
        
    
        // $currentStudentsCourses = $studentsProgramme->map($processCourse);
        $currentStudentsCourses = $studentsProgramme;
        // return $currentStudentsCourses;
        $allCourses = $this->getAllCoursesAttachedToProgrammeForAStudent($studentId)->get();
        if (str_starts_with($studentId, '190')) {
            $allCourses->transform(function ($allCourses) {
                $allCourses->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $allCourses->CodeRegisteredUnder);
                return $allCourses;
            });
        }
        $studentDetails = $this->getAppealStudentDetails(2024, [$studentId])->first();
    
        return view('allStudents.adminRegisterStudent', compact('studentDetails','courses', 'allCourses', 'currentStudentsCourses', 'studentsPayments', 'failed', 'studentId', 'theNumberOfCourses'));
    }  

    public function adminSubmitCourses(Request $request){
        $studentId = $request->input('studentNumber');
        $courses = explode(',', $request->input('courses'));
        $academicYear = 2024;
        

        // Insert into CourseRegistration table
        foreach ($courses as $course) {
            CourseRegistration::create([
                'StudentID' => $studentId,
                'CourseID' => $course,
                'EnrolmentDate' => now(),
                'Year' => $academicYear,
                'Semester' => 1,
            ]);
        }

        return redirect()->back()->with('success', 'Courses submitted successfully.');
    }  
    
    public function studentSubmitCourseRegistration(Request $request){
        $studentId = $request->input('studentNumber');
        $courses = explode(',', $request->input('courses'));
        $academicYear = 2024;
        

        // Insert into CourseRegistration table
        foreach ($courses as $course) {
            CourseRegistration::create([
                'StudentID' => $studentId,
                'CourseID' => $course,
                'EnrolmentDate' => now(),
                'Year' => $academicYear,
                'Semester' => 1,
            ]);
        }

        return redirect()->back()->with('success', 'Courses submitted successfully.');
    }

    private function getStudentRegistration($studentId){
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
                ->where('Year', 2024)
                ->where('Semester', 1)
                ->get();
        return $checkRegistration;
    }

    public function viewAllStudents(Request $request){
        $academicYear= 2024;
        $courseName = null;
        $courseId = null;
        if($request->input('student-number')){
            $students = Student::query()
                        ->where('student_number', 'like', '%' . $request->input('student-number') . '%')
                        ->where('status','=', 4)
                        ->get();
            if($students){
                $studentNumbers = $students->pluck('student_number')->toArray();
                $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(30);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::where('status', 4)->pluck('student_number')->toArray();
            $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(30);
        }
        return view('allStudents.index', compact('results','courseName','courseId'));
    }
}
