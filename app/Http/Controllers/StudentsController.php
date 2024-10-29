<?php

namespace App\Http\Controllers;

use App\Mail\ExistingStudentMail;
use App\Mail\NewStudentMail;
use App\Models\BasicInformation;
use App\Models\BasicInformationSR;
use App\Models\Billing;
use App\Models\CourseElectives;
use App\Models\CourseRegistration;
use App\Models\Courses;
use App\Models\EduroleCourses;
use App\Models\Grades;
use App\Models\GradesPublished;
use App\Models\MoodleCourses;
use App\Models\MoodleEnroll;
use App\Models\MoodleUserEnrolments;
use App\Models\MoodleUsers;
use App\Models\NMCZRepeatCourses;
use App\Models\SageClient;
use App\Models\SisReportsSageInvoices;
use App\Models\Student;
use App\Models\Study;
use App\Models\StudyProgramLink;
use App\Models\User;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StudentsController extends Controller
{
    public function importStudentsFromLMMAX(){
        set_time_limit(12000000);
        $maxAttempts = 10;
    
        try {
            // Get student IDs to import from LMMAX
            $studentIds = $this->getStudentsFromLMMAX()->pluck('student_id')->toArray();
            $studentIdsChunks = array_chunk($studentIds, 1000); // Chunk the student IDs for processing
    
            // Retrieve existing users and basic information for the student IDs
            $existingUsers = User::whereIn('name', $studentIds)->get()->keyBy('name');
            $basicInformations = BasicInformation::whereIn('ID', $studentIds)->get()->keyBy('ID');
    
            foreach ($studentIdsChunks as $studentIdsChunk) {
                foreach ($studentIdsChunk as $studentId) {
                    // Check if the student already exists with the required status
                    $student = Student::where('student_number', $studentId)
                                    ->where('status', 6)
                                    ->first();
                    if ($student) {
                        continue; // Skip if the student already exists
                    }
    
                    // Retrieve and validate the student's private email
                    $privateEmail = $basicInformations->get($studentId);
                    if (!$privateEmail) {
                        continue; // Skip if no email found
                    }
    
                    // Create a user account if it doesn't already exist
                    if (!isset($existingUsers[$studentId])) {
                        $this->createUserAccount($studentId);
                    }
    
                    $sendingEmail = $this->validateAndPrepareEmail($privateEmail->PrivateEmail, $studentId);
    
                    // Create or update the student record
                    Student::updateOrCreate(
                        ['student_number' => $studentId],
                        ['academic_year' => 2024, 'term' => 1, 'status' => 6]
                    );
    
                    // Send an email to the new student
                    $this->sendEmailToStudent($sendingEmail, $studentId, $maxAttempts);
                }
            }
    
            // Provide a success message
            return redirect()->back()->with('success', 'Students imported successfully and accounts created.');
        } catch (\Exception $e) {
            // Handle any errors that occur during the process
            return redirect()->back()->with('error', 'An error occurred while importing students: ' . $e->getMessage());
        }
    }
    

    // public function importStudentsFromLMMAX() {
    //     set_time_limit(12000000);
    //     $maxAttempts = 3;
    //     $studentIds = $this->getStudentsFromLMMAX()->pluck('student_id')->toArray();
    //     $studentIdsChunks = array_chunk($studentIds, 500); // Reduced chunk size for better performance
    
    //     $successfulImports = 0;
    //     $failedImports = 0;
    
    //     foreach ($studentIdsChunks as $studentIdsChunk) {
    //         try {
    //             $existingUsers = User::whereIn('name', $studentIdsChunk)->get()->keyBy('name');
    //             $basicInformations = BasicInformation::whereIn('ID', $studentIdsChunk)->get()->keyBy('ID');
    
    //             foreach ($studentIdsChunk as $studentId) {
    //                 $attempts = 0;
    //                 $success = false;
    
    //                 while ($attempts < $maxAttempts && !$success) {
    //                     $attempts++;
    
    //                     try {
    //                         $student = Student::where('student_number', $studentId)
    //                             ->where('status', 6)
    //                             ->lockForUpdate() // Lock the row for update to avoid conflicts
    //                             ->first(); 
    
    //                         if ($student) {
    //                             $success = true;
    //                             $successfulImports++;
    //                             break;
    //                         }
    
    //                         $privateEmail = $basicInformations->get($studentId);
    
    //                         if ($privateEmail) {
    //                             if (!isset($existingUsers[$studentId])) {
    //                                 $this->createUserAccount($studentId);
    //                             }
    //                             $sendingEmail = $this->validateAndPrepareEmail($privateEmail->PrivateEmail, $studentId);
    //                         } else {
    //                             $success = true;
    //                             $successfulImports++;
    //                             break;
    //                         }
    
    //                         Student::updateOrCreate(
    //                             ['student_number' => $studentId],
    //                             ['academic_year' => 2024, 'term' => 1, 'status' => 6]
    //                         );
    
    //                         $this->queueEmailToStudent($sendingEmail, $studentId, $maxAttempts);
    
    //                         $success = true;
    //                         $successfulImports++;
    //                     } catch (\Exception $e) {
    //                         if ($e->getCode() == 1205 && $attempts < $maxAttempts) {
    //                             // Retry on lock timeout
    //                             continue;
    //                         } else {
    //                             // Log the error and move to the next student
    //                             $failedImports++;
    //                             error_log('Failed to import student ID ' . $studentId . ': ' . $e->getMessage());
    //                             break;
    //                         }
    //                     }
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             // Log general errors in chunk processing
    //             error_log('Failed to process chunk: ' . $e->getMessage());
    //             $failedImports += count($studentIdsChunk); // Assume all failed in this chunk
    //         }
    //     }
    
    //     // Return a summary of the import process
    //     return redirect()->back()->with('success', "Students imported successfully. Total: {$successfulImports}, Failed: {$failedImports}.");
    // }

    public function updateYearAndSemesterForEnrolmentDate()
    {
        try {
            CourseElectives::whereNull('Year')
                ->where('EnrolmentDate', '>', '2024-01-01')
                ->update([
                    'Year' => 2024,
                    'Semester' => 1
                ]);

            return response()->json(['message' => 'Update completed successfully.'], 200);
        } catch (QueryException $e) {
            // Check if it's a duplicate entry error
            if ($e->getCode() == 23000) {
                // Log the error if needed
                Log::warning('Duplicate entry error ignored: ' . $e->getMessage());

                return response()->json(['message' => 'Update completed with some duplicate entry errors ignored.'], 200);
            } else {
                // Re-throw the exception if it's not a duplicate entry error
                throw $e;
            }
        }
    }
    
    
    
    public function importStudentsFromBasicInformation(){
        set_time_limit(12000000);
        $maxAttempts = 10;

        // Get student IDs to import from Edurole
        $studentIds = $this->getStudentsToImport()->pluck('StudentID')->toArray();
        // From LMMAX
        // $studentIds = $this->getStudentsFromLMMAX()->pluck('student_id')->toArray();
        // return $studentIds;
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            $studentIdsChunks = array_chunk($studentIds, 1000);

            // Get existing users
            $existingUsers = User::whereIn('name', $studentIds)->get()->keyBy('name');

            // Eager load BasicInformation for all students
            $basicInformations = BasicInformation::whereIn('ID', $studentIds)->get()->keyBy('ID');

            foreach ($studentIdsChunks as $studentIdsChunk) {
                foreach ($studentIdsChunk as $studentId) {
                    // Check if student exists with required status
                    $student = Student::where('student_number', $studentId)
                        ->where('status', 4)
                        ->first(); 
                    if ($student) {
                        continue;
                    } 

                    $registrationResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId);
                    $courses = $registrationResults['dataArray'];
                    $coursesArray = $courses->pluck('Course')->toArray();
                    $coursesNamesArray = $courses->pluck('Program')->toArray();
                    $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();
                    if($studentsProgramme->isEmpty()){
                        $studentsProgramme = $this->getAllCoursesAttachedToProgrammeNamesForAStudentBasedOnCourses($studentId, $coursesNamesArray)->get();
                    }
                    $isStudentRegistered = $this->checkIfStudentIsRegistered($studentId)->exists();

                    $isStudentRegisteredOnSisReports = $this->checkIfStudentIsRegisteredOnSisReports($studentId, 2024)->exists();
                    if ($studentsProgramme->isEmpty() || $isStudentRegistered) {
                        Student::updateOrCreate(
                            ['student_number' => $studentId],
                            ['academic_year' => 2023, 'term' => 1, 'status' => 3]
                        );
                        continue;
                    }                                  

                    // If a user account doesn't exist, create it
                    if (!isset($existingUsers[$studentId])) {
                        $this->createUserAccount($studentId);
                    }

                    // Get and prepare student's private email
                    $privateEmail = BasicInformation::find($studentId);
                    $sendingEmail = $this->validateAndPrepareEmail($privateEmail->PrivateEmail,$studentId);

                    // Create or update student record
                    Student::updateOrCreate(
                        ['student_number' => $studentId],
                        ['academic_year' => 2024, 'term' => 1, 'status' => 4]
                    );

                    // Send email to new student
                    // $this->sendEmailToStudent($sendingEmail, $studentId, $maxAttempts);
                }
            }

            // Commit the transaction
            DB::commit();

            // Provide a success message
            return redirect()->back()->with('success', 'Students imported successfully and accounts created.');
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while importing students: ' . $e->getMessage());
        }
    }

    public function exportAllStudents(){
        set_time_limit(12000000);
        $maxAttempts = 10;
        
        // Get student IDs to import
        $studentIds = $this->getStudentsToImport()->pluck('StudentID')->toArray();
        $studentIdsChunks = array_chunk($studentIds, 1000);
    
        // Create a writer and open it
        $filePath = storage_path('app/students.xlsx');
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);
    
        // Write the header row to the CSV file
        $headerRow = WriterEntityFactory::createRowFromArray(['Student ID', 'Courses', 'Programs']);
        $writer->addRow($headerRow);
    
        foreach ($studentIdsChunks as $studentIdsChunk) {
            foreach ($studentIdsChunk as $studentId) {
                $registrationResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId);
                $courses = $registrationResults['dataArray'];
                $coursesArray = $courses->pluck('Course')->toArray();
                $coursesNamesArray = $courses->pluck('Program')->toArray();
                $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();
                if($studentsProgramme->isEmpty()){
                    $studentsProgramme = $this->getAllCoursesAttachedToProgrammeNamesForAStudentBasedOnCourses($studentId, $coursesNamesArray)->get();
                }
    
                // Only write the student data to the CSV file if there are courses and programs
                if (!empty($coursesArray) && !empty($coursesNamesArray) && !$studentsProgramme->isEmpty()) {
                    $dataRow = WriterEntityFactory::createRowFromArray([$studentId, implode(', ', $coursesArray), implode(', ', $coursesNamesArray)]);
                    $writer->addRow($dataRow);
                }
            }
        }
    
        // Close the writer
        $writer->close();
    
        // Provide a success message
        return response()->download($filePath, 'students.xlsx')->deleteFileAfterSend();
    }
    
    private function validateAndPrepareEmail($email, $studentId) {
        $email = trim($email);
    
        // Check if the email is empty
        if (empty($email)) {
            $email = $studentId . '@lmmu.ac.zm';
        }

        $email = trim($email);
        // Check if the email already exists for another user
        $existingUser = User::where('email', $email)->where('name', '!=', $studentId)->exists();
        $checkIfUserAlreadyHasEmail = User::where('name', $studentId)->where('email', $email)->exists();   
        if($existingUser){
            $email = $studentId . $email;
            User::where('name', $studentId)->update(['email' => $email]);
        }elseif(!$checkIfUserAlreadyHasEmail) {
            // If the email doesn't exist for the current user, update it
            User::where('name', $studentId)->update(['email' => $email]);
        }     
    
        return $email;
    }

    private function sendEmailToStudent($email, $studentId, $maxAttempts) {
        Log::info('Email sent to ' . $email . ' for student ' . $studentId);
        $email = trim($email);
        $sendingEmail = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'registration@lmmu.ac.zm';
        // $sendingEmail = 'azwel.simwinga@lmmu.ac.zm';
        $attempts = 0;
        $studentId = $studentId;
        while ($attempts < $maxAttempts) {
            try {
                Mail::to($sendingEmail)->send(new ExistingStudentMail($studentId));
                Log::info('Email sent to ' . $sendingEmail . ' for student ' . $studentId);
                break;
            } catch (\Exception $e) {
                error_log('Unable to send email: '. $maxAttempts . ' attempts.' .$studentId . $e->getMessage());
                $attempts++;
                if ($attempts === $maxAttempts) {
                    error_log('Failed to send email after ' . $maxAttempts . ' attempts.' .$studentId);
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
        // if ($results) {
        //     return redirect()->back()->with('error', 'Student already registered.');
        // }
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
            $student->update(['status' => 6]);
            // $this->sendEmailToStudent($sendingEmail, $studentId, $maxAttempts, new ExistingStudentMail($student));
        } else {
            Student::create([
                'student_number' => $studentId,
                'academic_year' => 2024,
                'term' => 1,
                'status' => 6
            ]);
            $existingUsers = User::where('name', $studentId)->get()->keyBy('name');
            if (!isset($existingUsers[$studentId])) {
                $this->createUserAccount($studentId);
                // $this->sendEmailToStudent($sendingEmail, $studentId, $maxAttempts, new NewStudentMail($studentId));
            }            
        }     
        return redirect()->back()->with('success', 'Student imported successfully.');      
        // return redirect()->route('students.showStudent',$studentId)->with('success', 'Student created successfully.');
    }

    public function createUserAccount($studentId){
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

    public function printIDCardStudentNurandMid( $studentId){

        
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
        ->where('Year', 2024)
        ->where('Semester', 1)
        ->exists();

        // return $checkRegistration;

        if (!$checkRegistration) {            
            return redirect()->back()->with('error', 'UNREGISTERED STUDENT');
        }

        $studentInformation = BasicInformationSR::join('student_study_link_s_r_s', 'basic_information_s_r_s.StudentID', '=', 'student_study_link_s_r_s.student_id')
            ->join('study_s_r_s', 'student_study_link_s_r_s.study_id', '=', 'study_s_r_s.study_id')
            ->join('school_s_r_s', 'study_s_r_s.parent_id', '=', 'school_s_r_s.school_id')
            ->where('basic_information_s_r_s.StudentID', $studentId)
            // ->select('basic_information_s_r_s.*', 'study_s_r_s.*', 'school_s_r_s.*')
            ->first();

        return $studentInformation;

        return view('allStudents.printIdCard',compact('studentInformation'));
    }

    
    // public function viewDocketOld()
    // {
    //     $user = Auth::user(); // Get the currently logged-in user

    //     // If the user doesn't have the "Student" role, return the home view
    //     if (!$user->hasRole('Student')) {
    //         return view('home');
    //     }

    //     $student = Student::where('student_number', $user->name)->first();

    //     // If the student doesn't exist, return back with an error message
    //     if (is_null($student)) {
    //         return back()->with('error', 'NOT STUDENT.');
    //     }

    //     $academicYear = 2023;
    //     $studentResults = $this->getAppealStudentDetails($academicYear, [$user->name])->first();
    //     $isStudentRegistered = $this->checkIfStudentIsRegistered($user->name)->exists();
    //     $checkIfApproved = $this->checkIfStudentIsRegistered($user->name)->where('course-electives.Approved', 1)->exists();
    //     $isStudentRegisteredOnSisReports = $this->checkIfStudentIsRegisteredOnSisReports($student, 2024)->exists();
    //     // return $checkIfApproved;
    //     // return $user->name;
    //     $results2023PreviouseYear = Grades::where('StudentNo', $user->name)
    //         ->where('AcademicYear', 2023);
        
    //     // Clone the base query to get repeat courses with specific grades
    //     $repeatCourses = (clone $results2023PreviouseYear)
    //         ->whereIn('Grade', ['D', 'D+', 'F', 'NE'])
    //         ->get();
        
    //     // Get all the results for the year 2023 without filtering grades
    //     $results2023 = $results2023PreviouseYear->get();

    //     try{        
    //         $courseName = $results2023->first()->CourseNo;
            
    //         $coursesResults = EduroleCourses::where('Name', $courseName)
    //             // ->where('Year', 2023) // Uncomment if you want to filter by year
    //             ->get();
            
    //         $previousYearOfStudy = $coursesResults->first()->Year;
    //         $currentYearOfStudy = $previousYearOfStudy + 1;
            
    //         $studyId = $studentResults->StudyID;  // Make sure $studentResults is defined
            
    //         $studentStudy = Study::where('ID', $studyId)->first();
            
    //         $highestYear = StudyProgramLink::where('study-program-link.StudyID', $studyId)
    //             ->join('programmes', 'study-program-link.ProgramID', '=', 'programmes.ID')
    //         ->max('programmes.Year');
    //     // return 'highestYear: ' . $highestYear . ', currentYearOfStudy: ' . $currentYearOfStudy;
    //     }catch(\Exception $e){
    //         $highestYear = 0;
    //         $currentYearOfStudy =20;
    //     }
    //     $registeredOnEdurole   = 0;
        
    //     // Check the conditions
    //     if (($highestYear != $currentYearOfStudy) ) {
    //         // Make sure $isStudentRegistered is defined somewhere before this block
    //         if( $repeatCourses->isEmpty()){
            
    //             if (!$isStudentRegistered && !$isStudentRegisteredOnSisReports) {
    //                 return redirect()->back()->with('error', 'Student not registered.');
    //             }

    //             // if (!$checkIfApproved) {
    //             //     return redirect()->back()->with('error', 'Courses not approved. Please contact your coordinator for Course Approval.');
    //             // }
    //             // Update courses based on the student's status


    //             if($isStudentRegistered){

    //                 $courses = CourseElectives::join('courses', 'course-electives.CourseID', '=', 'courses.ID')
    //                     ->where('course-electives.StudentID', $user->name)
    //                     ->where('course-electives.Year', 2024)
    //                     ->select('course-electives.*', 'courses.*')
    //                     ->get();

    //                 $registeredOnEdurole = 1;

    //                     // return $courses;
    //             }elseif($isStudentRegisteredOnSisReports){
    //                 $courses= "none";
    //                 $registeredOnEdurole = 0;

    //                     // return $courses;                    
    //             }else{
    //                 return redirect()->back()->with('error', 'Student not registered.');
    //             }
    //             // if (!Courses::where('Student', $user->name)
    //             //     ->whereNotNull('updated_at')
    //             //     ->where('updated_at', '>', '2024-09-19')
    //             //     ->exists()) {
    //             //     if ($isStudentRegistered) {
    //             //         $this->setAndUpdateRegisteredCourses($user->name);
    //             //     } else {
    //             //         $this->setAndUpdateCoursesForCurrentYear($user->name);
    //             //     }
    //             // } else {
    //             //     $this->setAndUpdateCourses($user->name);
    //             // }

    //             // try {
    //             //     $subQuery = Billing::select(
    //             //         'StudentID',
    //             //         'Amount',
    //             //         'Year',
    //             //         DB::raw('ROW_NUMBER() OVER (PARTITION BY StudentID, Year ORDER BY Date DESC) AS rn')
    //             //     )
    //             //     ->where('Description', 'NOT LIKE', '%NULL%')
    //             //     ->where('PackageName', 'NOT LIKE', '%NULL%')
    //             //     // ->whereNotNull('Approval')
    //             //     ->where('Year', 2024)
    //             //     ->where('StudentID', $user->name)
    //             //     ->first();

    //             //     $invoice2024 = $subQuery->Amount;
    //             // } catch (\Exception $e) {
    //             //     return redirect()->back()->with('error', 'No invoice found for 2024. Please ensure that your courses are approved and you have been invoiced for 2024. Visit your coordinator for course approval and accounts for invoicing if you have not been invoiced.');
    //             // }
    //             // return $invoice2024;
    //             // if (!$invoice2024) {
    //             //     return redirect()->back()->with('error', 'No invoice found for 2024. Please ensure that your courses are approved and you have been invoiced for 2024. Visit your coordinator for course approval and accounts for invoicing if you have not been invoiced.');
    //             // }

    //             // $studentPaymentInformation = SageClient::select(
    //             //     'DCLink',
    //             //     'Account',
    //             //     'Name',
    //             //     DB::raw('SUM(CASE 
    //             //         WHEN pa.Description LIKE \'%reversal%\' THEN 0  
    //             //         WHEN pa.Description LIKE \'%FT%\' THEN 0
    //             //         WHEN pa.Description LIKE \'%DE%\' THEN 0  
    //             //         WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0          
    //             //         ELSE pa.Credit 
    //             //         END) AS TotalPayments'),
    //             //     DB::raw('SUM(pa.Credit) as TotalCredit'),
    //             //     DB::raw('SUM(pa.Debit) as TotalDebit'),
    //             //     DB::raw('SUM(pa.Debit) - SUM(pa.Credit) as TotalBalance')
    //             // )
    //             // ->where('Account', $user->name)
    //             // ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
    //             // ->groupBy('DCLink', 'Account', 'Name')
    //             // ->first();

    //             // $balance = $studentPaymentInformation->TotalBalance;
    //             // $percentageOfInvoice = ($balance / $invoice2024) * 100;

    //             // if ($percentageOfInvoice > 25) {
    //             //     return redirect()->back()->with('error', 'You must have cleared at least 75% of your 2024 fees to view your docket.');
    //             // }
    //         }
    //     }

    //     $imageUrl = "https://edurole.lmmu.ac.zm/datastore/identities/pictures/{$user->name}.png";
    //     $logoUrl = "https://edurole.lmmu.ac.zm/templates/mobile/images/header.png";

    //     $imageDataUri = $this->convertImageToDataUri($imageUrl);
    //     $logoDataUri = $this->convertImageToDataUri($logoUrl);

    //     // $courses = Courses::where('Student', $user->name)->get();

    //     // Cast the status to an integer
    //     $status = (int) $student->status;

    //     // Update the student's status to 6 if it's not already 6
    //     if ($status !== 6) {
    //         $student->status = 6;
    //         $student->save();
    //     }

    //     // Return the appropriate view based on the student's status
    //     $viewName = match ($status) {
    //         1, 6 => 'docket.studentViewDocket',
    //         2 => 'docketNmcz.studentViewDocket',
    //         3 => 'docketSupsAndDef.studentViewDocket',
    //         default => 'home', // Fallback view if status doesn't match any case
    //     };
    //     return $courses . ' ' . $isStudentRegistered;

    //     // return view($viewName, compact('studentResults', 'courses', 'imageDataUri', 'logoDataUri'));
    // }

    public function viewDocket()
    {
        $user = Auth::user(); // Get the currently logged-in user

        // If the user doesn't have the "Student" role, return the home view
        if (!$user->hasRole('Student')) {
            return view('home');
        }

        $student = Student::where('student_number', $user->name)->first();

        // If the student doesn't exist, return back with an error message
        if (is_null($student)) {
            return back()->with('error', 'NOT STUDENT.');
        }

        $academicYear = 2023;
        $studentResults = $this->getAppealStudentDetails($academicYear, [$user->name])->first();
        $isStudentRegistered = $this->checkIfStudentIsRegistered($user->name)->exists();
        $checkIfApproved = $this->checkIfStudentIsRegistered($user->name)->where('course-electives.Approved', 1)->exists();
        $isStudentRegisteredOnSisReports = $this->checkIfStudentIsRegisteredOnSisReports($user->name, 2024)->exists();

        // return $isStudentRegisteredOnSisReports;
        // return $checkIfApproved;
        // return $user->name;
        $results2023PreviouseYear = Grades::where('StudentNo', $user->name)
            ->where('AcademicYear', 2023);
        
        // Clone the base query to get repeat courses with specific grades
        $repeatCourses = (clone $results2023PreviouseYear)
            ->whereIn('Grade', ['D', 'D+', 'F', 'NE'])
            ->get();
        
        // Get all the results for the year 2023 without filtering grades
        $results2023 = $results2023PreviouseYear->get();

        try{        
            $courseName = $results2023->first()->CourseNo;
            
            $coursesResults = EduroleCourses::where('Name', $courseName)
                // ->where('Year', 2023) // Uncomment if you want to filter by year
                ->get();
            
            $previousYearOfStudy = $coursesResults->first()->Year;
            $currentYearOfStudy = $previousYearOfStudy + 1;
            
            $studyId = $studentResults->StudyID;  // Make sure $studentResults is defined
            
            $studentStudy = Study::where('ID', $studyId)->first();
            
            $highestYear = StudyProgramLink::where('study-program-link.StudyID', $studyId)
                ->join('programmes', 'study-program-link.ProgramID', '=', 'programmes.ID')
            ->max('programmes.Year');
        // return 'highestYear: ' . $highestYear . ', currentYearOfStudy: ' . $currentYearOfStudy;
        }catch(\Exception $e){
            $highestYear = 0;
            $currentYearOfStudy =20;
        }
        $courses = null; // Initialize courses as null to avoid undefined variable errors
        $registeredOnEdurole = 0;
        
        // Check the conditions
        // if ( ) {
            // Make sure $isStudentRegistered is defined somewhere before this block
            // if( ){
            
        if (!$isStudentRegistered && !$isStudentRegisteredOnSisReports && !($highestYear != $currentYearOfStudy)  && !$repeatCourses->isEmpty() ) {
            return redirect()->back()->with('error', 'Student not registered.');
        }
        if($isStudentRegistered){

            $courses = CourseElectives::join('courses', 'course-electives.CourseID', '=', 'courses.ID')
                ->where('course-electives.StudentID', $user->name)
                ->where('course-electives.Year', 2024)
                ->select('courses.Name','courses.CourseDescription')
                ->get();

            $registeredOnEdurole = 1;

                // return $courses;
        }elseif($isStudentRegisteredOnSisReports){
            $courses= CourseRegistration::join('courses_s_r_s', 'course_registration.CourseID', '=', 'courses_s_r_s.course_name')
                ->where('course_registration.StudentID', $user->name)
                ->where('course_registration.Year', 2024)
                ->select('courses_s_r_s.course_name','courses_s_r_s.course_description')
                ->get();
            $registeredOnEdurole = 0;

                // return $courses;                    
        }else{
            return redirect()->back()->with('error', 'Student not registered.');
        }                
            // }
        // }

        $imageUrl = "https://edurole.lmmu.ac.zm/datastore/identities/pictures/{$user->name}.png";
        $logoUrl = "https://edurole.lmmu.ac.zm/templates/mobile/images/header.png";

        $imageDataUri = $this->convertImageToDataUri($imageUrl);
        $logoDataUri = $this->convertImageToDataUri($logoUrl);

        // $courses = Courses::where('Student', $user->name)->get();

        // Cast the status to an integer
        $status = (int) $student->status;

        // Update the student's status to 6 if it's not already 6
        if ($status !== 6) {
            $student->status = 6;
            $student->save();
        }

        // Return the appropriate view based on the student's status
        // $viewName = match ($status) {
        //     1, 6 => 'docket.studentViewDocket',
        //     2 => 'docketNmcz.studentViewDocket',
        //     3 => 'docketSupsAndDef.studentViewDocket',
        //     default => 'home', // Fallback view if status doesn't match any case
        // };
        // return $courses . ' ' . $registeredOnEdurole;

        return view('docket.studentViewDocket', compact('studentResults', 'courses', 'imageDataUri', 'logoDataUri','registeredOnEdurole'));
    }

    private function convertImageToDataUri($url)
    {
        try {
            $response = Http::withOptions(['verify' => false])->get($url);

            if ($response->successful()) {
                $imageContent = $response->body();
                $imageBase64 = base64_encode($imageContent);
                $imageMimeType = $response->header('Content-Type') ?? 'image/png'; // Default to PNG if Content-Type is missing

                // Create the data URI
                return 'data:' . $imageMimeType . ';base64,' . $imageBase64;
            } else {
                // Handle cases where the image is not found or request failed
                return null;
            }
        } catch (\Exception $e) {
            // Handle exceptions (e.g., network errors)
            return null;
        }
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
        set_time_limit(12000000);
        $studentId = $request->input('studentId');
        $year = $request->input('year');
        $courses = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', $year)
            ->get();
        $coursesArray = $courses->pluck('CourseID')->toArray();
        $this->deletCoursesFromMoodleEnrollment($studentId,$coursesArray);       
        $courses = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', $year)
            ->delete();
        return redirect()->back()->with('success', 'Registration deleted successfully');
    }

    public function deleteCourseInRegistration(Request $request){
        $studentId = $request->input('studentId');
        $year = $request->input('year');
        $courseId = $request->input('courseId');
        $studentStatus = $request->input('studentStatus');
        $courseArray = [$courseId];
        if($studentStatus != 5){
            $this->deletCoursesFromMoodleEnrollment($studentId,$courseArray);
        }        
        CourseRegistration::where('StudentID', $studentId)
            ->where('Year', $year)
            ->where('CourseID', $courseId)
            ->delete();
        return redirect()->back()->with('success', 'Registration deleted successfully');
    }

    public function deleteCourseFromNMCZCourses(Request $request){
        $studentId = $request->input('studentId');
        $year = $request->input('year');
        $courseId = $request->input('courseId');       
        NMCZRepeatCourses::where('studnent_number', $studentId)
            ->where('academic_year', $year)
            ->where('course_code', $courseId)
            ->delete();
        return redirect()->back()->with('success', 'Registration deleted successfully');
    }

    private function deletCoursesFromMoodleEnrollment($studentId,$coursesArray){        
        foreach ($coursesArray as $course) {
            $existingUser = MoodleUsers::where('username', $studentId)->first();
            if($existingUser){
                $course = MoodleCourses::where('idnumber', $course)->first();
                $courseId = $course->id;
                $enrolId = MoodleEnroll::where('courseid', $courseId)->first();
                Log::info($enrolId);
                MoodleUserEnrolments::where('userid', $existingUser->id)->where('enrolid', $enrolId->id)->delete();                
            }
        }
    }

    public function studentNMCZRegisterForRepeatCourses($studentId) {
        $todaysDate = date('Y-m-d');
        $deadLine = '2024-05-31';       
        
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();
        $getStudentStaus = Student::query()->where('student_number', $studentId)->first();
        $studentStatus = $getStudentStaus->status;
        if ($checkRegistration) {
            if($studentStatus != 5){
                $checkRegistration = collect($this->getStudentRegistration($studentId));
                $courseIds = $checkRegistration->pluck('CourseID')->toArray();
                
                $checkRegistration = EduroleCourses::query()->whereIn('Name', $courseIds)->get();
            }else{

                $checkRegistration = CourseRegistration::where('StudentID', $studentId)
                        ->where('Year', 2024)
                        ->get();
            }            
            
            $studentInformation = $this->getAppealStudentDetails(2024, [$studentId])->first();
            
            return view('allStudents.registrationPage', compact('studentStatus','studentId','checkRegistration','studentInformation'));
        }
        if($todaysDate > $deadLine){
            return redirect()->back()->with('error', 'Registration Deadline has passed.');
        }
    
        $studentsPayments = $this->getStudentsPayments($studentId)->first();
    
        // $registrationResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId); 
        $courses = NMCZRepeatCourses::where('studnent_number', $studentId)->get();
        $failed = 1;
        
        $coursesArray = NMCZRepeatCourses::where('studnent_number', $studentId)->pluck('course_code')->toArray();
        $coursesNamesArray = $courses->pluck('Program')->toArray();     
    
        $allInvoicesArray = SisReportsSageInvoices::all()->mapWithKeys(function ($item) {
            return [trim($item['InvoiceDescription']) => $item];
        })->toArray();
    
        $studentDetails = $this->getAppealStudentDetails(2024, [$studentId])->first();
        if(strpos($studentDetails->StudentID, '190') === 0){
            $year = 2019;
        }else{
            $year = 2023;
        }
        $studentsInvoice = SisReportsSageInvoices::where('InvoiceProgrammeCode','=',$studentDetails->ShortName)
                ->where('InvoiceModeOfStudy','=', $studentDetails->StudyType)                
                ->where('InvoiceYearOfInvoice','=',$year)
                ->get();
        // return $studentsInvoice;
        $invoiceFirstYear = $studentsInvoice->where('InvoiceYearOfStudy', 'Y1')->first();
        $invoiceSecondYear = $studentsInvoice->where('InvoiceYearOfStudy', 'Y2')->first();
        $invoiceThirdYear = $studentsInvoice->where('InvoiceYearOfStudy', 'Y3')->first();
        if($studentDetails->StudyType == 'Fulltime'){
            $modeOfStudy = 'FT';
        }else{
            $modeOfStudy = 'DE';
        }

        $programmeStudyCode = $studentDetails->ShortName . '-' . $modeOfStudy . '-'. $year . '-Y3';
        // return $programmeStudyCode;
        $theNumberOfCourses = $this->getCoursesInASpecificProgrammeCode($programmeStudyCode)->count();
        // return $theNumberOfCourses;
        $totalInvoiceAccumulated = ($invoiceFirstYear->InvoiceAmount ? $invoiceFirstYear->InvoiceAmount : 0) + ($invoiceSecondYear->InvoiceAmount ? $invoiceSecondYear->InvoiceAmount : 0) + ($invoiceThirdYear->InvoiceAmount ? $invoiceThirdYear->InvoiceAmount : 0);
        $amount = $invoiceThirdYear ?  $invoiceThirdYear->InvoiceAmount : 0;
        $totalPaymentsByStudent = $studentsPayments->TotalPayments;

        $amountAfterInvoicing = $totalInvoiceAccumulated - $totalPaymentsByStudent;

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
        $actualBalance = $studentPaymentInformation->TotalBalance;
        // return $amountAfterInvoicing;
        return view('allStudents.studentSelfNMCZRegistration', compact('actualBalance','studentStatus','amountAfterInvoicing','studentDetails','courses', 'studentsPayments', 'failed', 'studentId','amount','theNumberOfCourses','programmeStudyCode'));
    }

    public function studentRegisterForCourses($studentId) {
        set_time_limit(100000000);
        $getStudentStaus = Student::query()->where('student_number', $studentId)->first();
        $studentStatus = $getStudentStaus->status;

        if ($studentStatus == 5) {
            return redirect()->route('nmcz.registration', $studentId);
        }
        $todaysDate = date('Y-m-d');
        $deadLine = '2024-12-20';       
        
        $isStudentRegistered = $this->checkIfStudentIsRegistered($studentId)->exists();
        // $isStudentsStatus4 = Student::query()->where('student_number', $studentId)->where('status', 4)->exists();
        // if (!$isStudentsStatus4) {
        //     return redirect()->back()->with('error', 'Student can not register.');
        // }
        if ($isStudentRegistered) {
            return redirect()->back()->with('error', 'Student already registered On Edurole.');
        }
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();

            $studentsPayments = SageClient::select('DCLink', 'Account', 'Name',
            DB::raw('SUM(CASE WHEN pa.Description LIKE \'%reversal%\' THEN 0 WHEN pa.Description LIKE \'%FT%\' THEN 0 WHEN pa.Description LIKE \'%DE%\' THEN 0 WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0 ELSE pa.Credit END) AS TotalPayments'),
            DB::raw('SUM(pa.Credit) as TotalCredit'),
            DB::raw('SUM(pa.Debit) as TotalDebit'),
            DB::raw('SUM(pa.Debit) - SUM(pa.Credit) as TotalBalance'),
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0    
                WHEN pa.TxDate < \'2024-01-01\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2024'),            
            
        )
        ->where('Account', $studentId)
        ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
        ->groupBy('DCLink', 'Account', 'Name')
        ->first();
    
        $actualBalance = $studentsPayments->TotalBalance;

        // $studentsPayments = $this->getStudentsPayments($studentId)->first();
    
        $registrationResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId); 
        $courses = $registrationResults['dataArray'];
        $failed = $registrationResults['failed'];
    
        if ($checkRegistration) {
            $checkRegistration = collect($this->getStudentRegistration($studentId));
            $courseIds = $checkRegistration->pluck('CourseID')->toArray();
            
            $checkRegistration = EduroleCourses::query()->whereIn('Name', $courseIds)->get();
            
            $studentInformation = $this->getAppealStudentDetails(2024, [$studentId])->first();
            
            return view('allStudents.registrationPage', compact('studentStatus','studentId','checkRegistration','studentInformation','failed'));
        }
        if($todaysDate > $deadLine){
            return redirect()->back()->with('error', 'Registration on Sis Reports is Closed.');
        }       
        
        $coursesArray = $courses->pluck('Course')->toArray();
        $coursesNamesArray = $courses->pluck('Program')->toArray();
        $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();
        if($studentsProgramme->isEmpty()){
            $studentsProgramme = $this->getAllCoursesAttachedToProgrammeNamesForAStudentBasedOnCourses($studentId, $coursesNamesArray)->get();
        }
    
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

        
    
        return view('allStudents.studentSelfRegistration', compact('actualBalance','studentStatus','studentDetails','courses', 'currentStudentsCourses', 'studentsPayments', 'failed', 'studentId', 'theNumberOfCourses'));
    }

    public function registerStudent($studentId) {
        set_time_limit(100000000);
        
        // Fetch student status with select to reduce data load
        $getStudentStaus = Student::query()->select('status')->where('student_number', $studentId)->first();
        $studentStatus = $getStudentStaus->status;
    
        if ($studentStatus == 5) {
            return redirect()->route('nmcz.registration', $studentId);
        }
    
        $todaysDate = date('Y-m-d');
        $deadLine = '2024-12-20'; 
        
        // Check if student is already registered
        // $isStudentRegistered = $this->checkIfStudentIsRegistered($studentId)->exists();
        $isStudentsStatus4 = Student::query()->where('student_number', $studentId)->where('status', 4)->exists();
        
        // if ($isStudentRegistered) {
        //     return redirect()->back()->with('error', 'Student already registered On Edurole.');
        // }
    
        // Check if the student has registered courses for the specified year and semester
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();
    
        // Optimize student payment query
        $studentsPayments = SageClient::select('DCLink', 'Account', 'Name',
            DB::raw('SUM(CASE WHEN pa.Description LIKE \'%reversal%\' THEN 0 WHEN pa.Description LIKE \'%FT%\' THEN 0 WHEN pa.Description LIKE \'%DE%\' THEN 0 WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0 ELSE pa.Credit END) AS TotalPayments'),
            DB::raw('SUM(pa.Credit) as TotalCredit'),
            DB::raw('SUM(pa.Debit) as TotalDebit'),
            DB::raw('SUM(pa.Debit) - SUM(pa.Credit) as TotalBalance'),
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0    
                WHEN pa.TxDate < \'2024-01-01\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2024'),            
            
        )
        ->where('Account', $studentId)
        ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
        ->groupBy('DCLink', 'Account', 'Name')
        ->first();
    
        $actualBalance = $studentsPayments->TotalBalance;

        // Handle registration process
        $registrationResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId);
        $courses = $registrationResults['dataArray'];
        $failed = $registrationResults['failed'];
    
        if ($checkRegistration) {
            $checkRegistration = collect($this->getStudentRegistration($studentId));
            $courseIds = $checkRegistration->pluck('CourseID')->toArray();
    
            $checkRegistration = EduroleCourses::query()->whereIn('Name', $courseIds)->get();
            
            // Cache appeal student details to avoid redundant calls
            $studentInformation = Cache::remember("appeal_student_details_{$studentId}_2024", 60, function () use ($studentId) {
                return $this->getAppealStudentDetails(2024, [$studentId])->first();
            });
    
            return view('allStudents.registrationPage', compact('actualBalance', 'studentStatus', 'studentId', 'checkRegistration', 'studentInformation','failed'));
        }
    
        if ($todaysDate > $deadLine) {
            return redirect()->back()->with('error', 'Registration on Sis Reports is Closed.');
        }
    
        // Fetch students payment once and use where needed
        // $studentsPayments = $this->getStudentsPayments($studentId)->first();       
    
        $coursesArray = $courses->pluck('Course')->toArray();
        $coursesNamesArray = $courses->pluck('Program')->toArray();
    
        $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();
        if ($studentsProgramme->isEmpty()) {
            $studentsProgramme = $this->getAllCoursesAttachedToProgrammeNamesForAStudentBasedOnCourses($studentId, $coursesNamesArray)->get();
        }
    
        // Update CodeRegisteredUnder for students with IDs starting with 190
        if (str_starts_with($studentId, '190')) {
            $studentsProgramme->transform(function ($studentProgramme) {
                $studentProgramme->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $studentProgramme->CodeRegisteredUnder);
                return $studentProgramme;
            });
        }
    
        if ($studentsProgramme->isEmpty()) {
            // $programeCode = trim($studentsProgramme->first()->CodeRegisteredUnder);
            $currentStudentsCourses = [];
        }
    
       
    
        // Fetch course count with optimized query
        $theNumberOfCourses = $this->getCoursesInASpecificProgrammeCode($programeCode)->count();
    
        // Cache all invoices to avoid loading from database multiple times
        $allInvoicesArray = Cache::remember('sis_reports_sage_invoices', 60, function () {
            return SisReportsSageInvoices::all()->mapWithKeys(function ($item) {
                return [trim($item['InvoiceDescription']) => $item];
            })->toArray();
        });
    
        
    
        // Get all courses and modify CodeRegisteredUnder if student ID starts with 190
        $allCourses = $this->getAllCoursesAttachedToProgrammeForAStudent($studentId)->get();
        if (str_starts_with($studentId, '190')) {
            $allCourses->transform(function ($allCourse) {
                $allCourse->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $allCourse->CodeRegisteredUnder);
                return $allCourse;
            });
        }
    
        // Cache appeal student details to avoid redundant calls
        $studentDetails = Cache::remember("appeal_student_details_{$studentId}_2024", 60, function () use ($studentId) {
            return $this->getAppealStudentDetails(2024, [$studentId])->first();
        });
    
        return view('allStudents.adminRegisterStudent', compact(
            'actualBalance', 'studentStatus', 'studentDetails', 'courses',
            'allCourses', 'currentStudentsCourses', 'studentsPayments', 'failed',
            'studentId', 'theNumberOfCourses'
        ));
    }
    

    public function adminSubmitCourses(Request $request){
        $studentId = $request->input('studentNumber');
        $courses = $request->input('courses'); // Directly retrieve courses as an array
        $academicYear = 2024;

        $moodleController = new MoodleController();

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
            $moodleController->addStudentsToMoodleAndEnrollInCourses([$studentId]);

            return redirect()->back()->with('success', 'Courses submitted successfully.');
        }  
        
    public function studentSubmitCourseRegistration(Request $request){
        $studentId = $request->input('studentNumber');
        $courses = $request->input('courses'); // Directly retrieve courses as an array
        $academicYear = 2024;

        $moodleController = new MoodleController();

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

        $moodleController->addStudentsToMoodleAndEnrollInCourses([$studentId]);

        return redirect()->back()->with('success', 'Courses submitted successfully.');
    }

    public function bulkEnrollOnMooodle(Request $request){
        set_time_limit(12000000);
        $studentIds = CourseRegistration::pluck('StudentID')
                                ->unique()
                                ->toArray();
        $moodleController = new MoodleController();
        $moodleController->addStudentsToMoodleAndEnrollInCourses($studentIds);
        return redirect()->back()->with('success', 'Students enrolled successfully.');
    }

    public function bulkEnrollFromEduroleOnMooodle(Request $request){
        set_time_limit(12000000);
        $studentIds = CourseElectives::where('Year', 2024)
                            ->pluck('StudentID')
                            ->unique()
                            ->toArray();

        // return $studentIds;
        $moodleController = new MoodleController();
        $moodleController->addStudentsFromEduroleToMoodleAndEnrollInCourses($studentIds);
        return redirect()->back()->with('success', 'Students enrolled successfully.');
    }    

    public function viewAllStudents(Request $request){
        $academicYear= 2024;
        $courseName = null;
        $courseId = null;
        if($request->input('student-number')){
            $students = Student::query()
                        ->where('student_number', 'like', '%' . $request->input('student-number') . '%')
                        ->where('status','=', 6)
                        ->get();
            if($students){
                $studentNumbers = $students->pluck('student_number')->toArray();
                $results = $this->getAppealStudentDetails($academicYear, $studentNumbers);
                $count = $results->get()->count();
                $results = $results->paginate(30);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::where('status', 6)->pluck('student_number')->toArray();
            $results = $this->getAppealStudentDetails($academicYear, $studentNumbers);
            $count = $results->get()->count();
            $results = $results->paginate(30);
        }
        return view('allStudents.index', compact('results','courseName','courseId','count'));
    }

    public function getGraduatedStudents(){
        set_time_limit(12000000);
        $studentIds = $this->getStudentsToImport()->pluck('StudentID')->toArray();
        $studentIdsChunks = array_chunk($studentIds, 1000);
        $graduatedStudents = []; // Array to store student IDs
    
        foreach ($studentIdsChunks as $studentIdsChunk) {
            foreach ($studentIdsChunk as $studentId) {
                $registrationResults = $this->setAndSaveCoursesForCurrentYearRegistration($studentId);
                $courses = $registrationResults['dataArray'];
                $failed = $registrationResults['failed'];
    
                $coursesArray = $courses->pluck('Course')->toArray();
                $coursesNamesArray = $courses->pluck('Program')->toArray();
                $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();
                if($studentsProgramme->isEmpty()){
                    $studentsProgramme = $this->getAllCoursesAttachedToProgrammeNamesForAStudentBasedOnCourses($studentId, $coursesNamesArray)->get();
                }
    
                if (str_starts_with($studentId, '190')) {
                    $studentsProgramme->transform(function ($studentProgramme) {
                        $studentProgramme->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $studentProgramme->CodeRegisteredUnder);
                        return $studentProgramme;
                    });
                }
    
                if ($studentsProgramme->isEmpty()) {
                    $graduatedStudents[] = $studentId; // Add student ID to the array
                }
            }
        }
    
        // Export the array to a CSV file
        $file = fopen('graduated_students.csv', 'w');
        foreach ($graduatedStudents as $studentId) {
            fputcsv($file, [$studentId]);
        }
        fclose($file);
    
        return redirect()->back()->with('success', 'Graduated students exported to CSV');
    }
}
