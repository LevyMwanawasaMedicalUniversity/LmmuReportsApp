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
    public function importStudentsFromBasicInformation()
    {
        set_time_limit(12000000);
        // Join BasicInformation with GradesPublished and select the student IDs
        $studentIds = $this->getStudentsToImport()->pluck('StudentID')->toArray();

        // Split studentIds into chunks to avoid MySQL placeholder limit
        $studentIdsChunks = array_chunk($studentIds, 1000); // Adjust the chunk size as needed

        // Get all existing users
        $existingUsers = User::whereIn('name', $studentIds)->get()->keyBy('name');

        foreach ($studentIdsChunks as $studentIdsChunk) {
            $studentsToInsert = [];
            foreach ($studentIdsChunk as $studentId) {
                $ifStudentExistsOnRequiredStatus = Student::where('student_number', $studentId)
                        ->where('status', 4)
                        ->exists();
                if ($ifStudentExistsOnRequiredStatus) {
                    continue;
                }
                $registrationResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId);
                $courses = $registrationResults['dataArray'];

                $allNoValue = true;
                foreach ($courses as $course) {
                    if ($course->Course !== 'NO VALUE' || $course->Grade !== 'NO VALUE' || $course->Program !== 'NO VALUE') {
                        $allNoValue = false;
                        break;
                    }
                }
                if ($allNoValue) {
                    continue;
                }

                $results = $this->checkIfStudentIsRegistered($studentId)->exists();
                if ($results) {
                    continue;
                }
                $results = BasicInformation::find($studentId);          
                
                $email = $results->PrivateEmail;
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // $email is a valid email address
                    // $sendingEmail = $email;
                    $sendingEmail = 'azwel.simwinga@lmmu.ac.zm';
                } else {
                    // $email is not a valid email address
                    $sendingEmail = 'azwel.simwinga@lmmu.ac.zm';
                }
                $student = Student::where('student_number', $studentId)->first();
                if ($student) {
                    // If the student number exists, update its status
                    $student->update(['status' => 4]);

                    // Send email to existing student
                    Mail::to($sendingEmail)->send(new ExistingStudentMail($student));
                } else {
                    // If the student number doesn't exist, prepare to insert the student
                    Student::create([
                        'student_number' => $studentId,
                        'academic_year' => 2024,
                        'term' => 1,
                        'status' => 4
                    ]);
                    // Send email to new student
                    Mail::to($sendingEmail)->send(new NewStudentMail($studentId));
                }

                // Check if a user account exists for the student
                if (!isset($existingUsers[$studentId])) {
                    // If a user account doesn't exist, create it
                    $this->createUserAccount($studentId);
                }
            }

            // Batch insert new students
            if (!empty($studentsToInsert)) {
                Student::insert($studentsToInsert);
            }
        }

        // Provide a success message
        return redirect()->back()->with('success', 'Students imported successfully and accounts created.');
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

        return view('allStudents.studentIDCard',compact('studentInformation'));
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

    private function createUserAccount($studentId)
    {
        // Get the student's email from BasicInformation
        $basicInfo = BasicInformation::find($studentId);
        $email = $basicInfo->PrivateEmail;
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $email = $studentId . $email . '@lmmu.ac.zm';
        }elseif($email == null){
            $email = $studentId . '@lmmu.ac.zm';
        }
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


    public function setAndSaveCoursesForCurrentYearRegistration($studentId) {
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

        // return $studentsProgramme;
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

    private function getStudentRegistration($studentId)
    {
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
            $student = Student::query()
                        ->where('student_number','=', $request->input('student-number'))
                        ->where('status','=', 4)
                        ->first();
            if($student){
                $getStudentNumber = $student->student_number;
                $studentNumbers = [$getStudentNumber];
                $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::where('status', 4)->pluck('student_number')->toArray();
            $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
        }
        return view('allStudents.index', compact('results','courseName','courseId'));
    }
}
