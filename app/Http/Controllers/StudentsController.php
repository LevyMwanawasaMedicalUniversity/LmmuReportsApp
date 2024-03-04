<?php

namespace App\Http\Controllers;

use App\Models\BasicInformation;
use App\Models\CourseElectives;
use App\Models\CourseRegistration;
use App\Models\Courses;
use App\Models\SisReportsSageInvoices;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        // Check if the student IDs already exist in the students table and update their status
        foreach ($studentIdsChunks as $studentIdsChunk) {
            Student::whereIn('student_number', $studentIdsChunk)
                ->where('status', '!=', 4) // Exclude students with status 4
                ->update(['status' => 4]);
        }

        // Insert new students with a status of 4 and create accounts for them
        foreach ($studentIdsChunks as $studentIdsChunk) {
            $studentsToInsert = [];
            foreach ($studentIdsChunk as $studentId) {
                $studentsToInsert[] = [
                    'student_number' => $studentId,
                    'academic_year' => 2024,
                    'term' => 1,
                    'status' => 4
                ];
            }

            // Batch insert new students
            Student::insert($studentsToInsert);
        }

        // Get all existing users
        $existingUsers = User::whereIn('name', $studentIds)->get()->keyBy('name');

        // Create accounts for each student
        foreach ($studentIdsChunks as $studentIdsChunk) {
            foreach ($studentIdsChunk as $studentId) {
                // Check if the student number exists in the students table
                $student = Student::where('student_number', $studentId)->first();
                if ($student) {
                    // If the student number exists, check if a user account exists for the student
                    $user = User::where('name', $studentId)->first();
                    if (!$user) {
                        // If a user account doesn't exist, create it
                        $this->createUserAccount($studentId);
                    }
                } else {
                    // If the student number doesn't exist, insert the student and create a user account
                    Student::create([
                        'student_number' => $studentId,
                        'academic_year' => 2024,
                        'term' => 1,
                        'status' => 4
                    ]);
                    $this->createUserAccount($studentId);
                }
            }
        }
        // Provide a success message
        return redirect()->back()->with('success', 'Students imported successfully and accounts created.');
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
            $email = $studentId . $email;
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
        $dataArray = $this->getCoursesForFailedStudents($studentId);
        $failed = 1;
    
        if (!$dataArray) {
            $dataArray = $this->findUnregisteredStudentCourses($studentId);
            $failed = 2;
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
    
        // Convert the array to a collection of objects
        $dataArray = collect($dataArray)->map(function($item) {
            return (object) $item;
        });
    
        return ['dataArray' => $dataArray, 'failed' => $failed];
        // Batch insert the new courses
    }

    public function studentRegisterForCourses($studentId){
        $studentsPayments = $this->getStudentsPayments($studentId)->first();
        // return $studentsPayments;
        
        $getResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId); 
        $courses = $getResults['dataArray'];
        $failed = $getResults['failed'];
        
        $coursesArray = $courses->pluck('Course')->toArray();
        // return $coursesArray;
        $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();

        // If the student number starts with 190, replace 2023 with 2019 in CodeRegisteredUnder
        if (str_starts_with($studentId, '190')) {
            $studentsProgramme = $studentsProgramme->map(function ($studentProgramme) {
                $studentProgramme->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $studentProgramme->CodeRegisteredUnder);
                return $studentProgramme;
            });
        }

        // return $studentsProgramme;
        $programeCode = trim($studentsProgramme[0]->CodeRegisteredUnder);

        // return $programeCode;   
        $theNumberOfCourses = $this->getCoursesInASpecificProgrammeCode($programeCode)->get()->count();
        // return $theNumberOfCourses;
        // return $getInvoiceForStudentsProgramme;        
        $getAllCoursesQuery = $this->getAllCoursesAttachedToProgrammeForAStudent($studentId)->get();
        
        if (str_starts_with($studentId, '190')) {
            $getAllCoursesQuery = $getAllCoursesQuery->map(function ($getAllCoursesQuery) {
                $getAllCoursesQuery->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $getAllCoursesQuery->CodeRegisteredUnder);
                return $getAllCoursesQuery;
            });
        }
        $allInvoicesArray = SisReportsSageInvoices::all()->mapWithKeys(function ($item) {
            return [trim($item['InvoiceDescription']) => $item];
        })->toArray();
        $currentStudentsCourses = $studentsProgramme->map(function ($course) use ($allInvoicesArray) {
            $courseArray = $course->toArray();
            $key = trim($course->CodeRegisteredUnder);
            $matchedKey = null;
        
            // Find a key in $allInvoicesArray that contains $key
            foreach ($allInvoicesArray as $invoiceKey => $invoice) {
                if (stripos($invoiceKey, $key) !== false) {
                    $matchedKey = $invoiceKey;
                    break;
                }
            }
        
            if ($matchedKey) {
                $mergedArray = array_merge($courseArray, $allInvoicesArray[$matchedKey]);
                if ($mergedArray == $courseArray) {
                    // Log the course that didn't merge
                    Log::info('No merge for course: ' . $key);
                }
                $courseArray = $mergedArray;
            } else {
                // Log the course that didn't find a match in $allInvoicesArray
                Log::info('No match found for course: ' . $key);
            }
        
            // Append the number of courses to the course array
            $courseArray['numberOfCourses'] = $this->getCoursesInASpecificProgrammeCode($course->CodeRegisteredUnder)->get()->count();
        
            return (object) $courseArray;
        });
        return view('allStudents.studentSelfRegistration',compact('courses','currentStudentsCourses','studentsPayments','failed','studentId','theNumberOfCourses'));
    }

    

    public function submitCourses(Request $request){
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

    

    public function registerStudent($studentId){
        
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)->where('Year', 2024)->where('Semester', 1)->get();
        if($checkRegistration->count() > 0){
            
            return view('allStudents.registrationPage',compact('studentId','checkRegistration'));
        }
        $studentsPayments = $this->getStudentsPayments($studentId)->first();
        // return $studentsPayments;
        
        $getResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId); 
        $courses = $getResults['dataArray'];
        $failed = $getResults['failed'];
        
        $coursesArray = $courses->pluck('Course')->toArray();
        // return $coursesArray;
        $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();
        // return $studentsProgramme;
        // If the student number starts with 190, replace 2023 with 2019 in CodeRegisteredUnder
        if (str_starts_with($studentId, '190')) {
            $studentsProgramme = $studentsProgramme->map(function ($studentProgramme) {
                $studentProgramme->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $studentProgramme->CodeRegisteredUnder);
                return $studentProgramme;
            });
        }

        // return $studentsProgramme;
        $programeCode = trim($studentsProgramme[0]->CodeRegisteredUnder);

        // return $programeCode;   
        $theNumberOfCourses = $this->getCoursesInASpecificProgrammeCode($programeCode)->get()->count();
        // return $theNumberOfCourses;
        // return $getInvoiceForStudentsProgramme;        
        $getAllCoursesQuery = $this->getAllCoursesAttachedToProgrammeForAStudent($studentId)->get();
        
        if (str_starts_with($studentId, '190')) {
            $getAllCoursesQuery = $getAllCoursesQuery->map(function ($getAllCoursesQuery) {
                $getAllCoursesQuery->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $getAllCoursesQuery->CodeRegisteredUnder);
                return $getAllCoursesQuery;
            });
        }
        $allInvoicesArray = SisReportsSageInvoices::all()->mapWithKeys(function ($item) {
            return [trim($item['InvoiceDescription']) => $item];
        })->toArray();
        $currentStudentsCourses = $studentsProgramme->map(function ($course) use ($allInvoicesArray) {
            $courseArray = $course->toArray();
            $key = trim($course->CodeRegisteredUnder);
            $matchedKey = null;        
            // Find a key in $allInvoicesArray that contains $key
            foreach ($allInvoicesArray as $invoiceKey => $invoice) {
                if (stripos($invoiceKey, $key) !== false) {
                    $matchedKey = $invoiceKey;
                    break;
                }
            }
        
            if ($matchedKey) {
                $mergedArray = array_merge($courseArray, $allInvoicesArray[$matchedKey]);
                if ($mergedArray == $courseArray) {
                    // Log the course that didn't merge
                    Log::info('No merge for course: ' . $key);
                }
                $courseArray = $mergedArray;
            } else {
                // Log the course that didn't find a match in $allInvoicesArray
                Log::info('No match found for course: ' . $key);
            }
        
            // Append the number of courses to the course array
            $courseArray['numberOfCourses'] = $this->getCoursesInASpecificProgrammeCode($course->CodeRegisteredUnder)->get()->count();
        
            return (object) $courseArray;
        });
        // return $currentStudentsCourses;

        $allCourses = $getAllCoursesQuery->map(function ($course) use ($allInvoicesArray) {
            $courseArray = $course->toArray();
            $key = trim($course->CodeRegisteredUnder);
            $matchedKey = null;
        
            // Find a key in $allInvoicesArray that contains $key
            foreach ($allInvoicesArray as $invoiceKey => $invoice) {
                if (stripos($invoiceKey, $key) !== false) {
                    $matchedKey = $invoiceKey;
                    break;
                }
            }
        
            if ($matchedKey) {
                $mergedArray = array_merge($courseArray, $allInvoicesArray[$matchedKey]);
                if ($mergedArray == $courseArray) {
                    // Log the course that didn't merge
                    Log::info('No merge for course: ' . $key);
                }
                $courseArray = $mergedArray;
            } else {
                // Log the course that didn't find a match in $allInvoicesArray
                Log::info('No match found for course: ' . $key);
            }
            return (object) $courseArray;
        });
        // return $allCourses;
        // return $currentStudentsCourses;
        return view('allStudents.adminRegisterStudent',compact('courses','allCourses','currentStudentsCourses','studentsPayments','failed','studentId','theNumberOfCourses'));
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
