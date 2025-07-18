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
use App\Models\StudentStudyLink;
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
        ->where('Year', 2025)
        ->where('Semester', 1)
        ->exists();

        if (!$checkRegistration) {            
            return redirect()->back()->with('error', 'UNREGISTERED STUDENT');
        }

        $studentInformation = $this->getAppealStudentDetails(2025, [$studentId])->first();
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

    public function viewDocket()
    {
        $user = Auth::user();

        if (!$user->hasRole('Student')) {
            return view('home');
        }

        $student = Student::where('student_number', $user->name)->first();

        if (is_null($student)) {
            return back()->with('error', 'NOT STUDENT.');
        }

        // Update student status if needed
        $status = (int) $student->status;
        if ($status !== 6) {
            $student->status = 6;
            $student->save();
        }

        return $this->getDocketData($user->name);
    }

    public function viewSupplementaryDocket( $studentId = null)
    {
        $user = Auth::user();

        $studentNumber = $user->hasRole('Student') ? (int) $user->name : (int) $studentId;

        if (!$studentNumber) {
            return back()->with('error', 'No student number provided.');
        }

        $student = Student::where('student_number', $studentNumber)->first();
        // return $student;

        if (is_null($student)) {
            return back()->with('error', 'NOT STUDENT.');
        }

        $studentNumber = (string) $student->student_number;
        

        $isStudentRegisteredOnEdurole = $this->checkIfStudentIsRegistered($studentNumber)->exists();
        // $allResults = $this->getAllStudentExamResults($student);        

        // return $allResults;
        // return "we here";
        $isStudentRegisteredOnSisReports = CourseRegistration::where('StudentID', $studentNumber)
            ->where('Year', 2024)
            // ->where('Semester', 1)
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
            DB::raw('
                (SUM(CASE 
                    WHEN pa.Description LIKE \'%FT%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Debit 
                    WHEN pa.Description LIKE \'%DE%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Debit 
                    ELSE 0 
                END) - SUM(CASE 
                    WHEN pa.Description LIKE \'%FT%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Credit 
                    WHEN pa.Description LIKE \'%DE%\' AND pa.TxDate < \'2024-01-01\' THEN pa.Credit 
                    ELSE 0 
                END)) - SUM(CASE 
                    WHEN pa.Description LIKE \'%reversal%\' THEN 0 
                    WHEN pa.Description LIKE \'%FT%\' THEN 0 
                    WHEN pa.Description LIKE \'%DE%\' THEN 0 
                    WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0 
                    ELSE pa.Credit 
                END) AS BalanceFrom2023
            ')
        )
        ->where('Account', $studentNumber)
        ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
        ->groupBy('DCLink', 'Account', 'Name')
        ->first();
        if (!$studentsPayments) {
            return back()->with('error', 'Student not found in Sage.');
        }else{
            $balanceFrom2023 = $studentsPayments->BalanceFrom2023;
        }
        // $balanceFrom2023 = $studentsPayments->BalanceFrom2023;

        // return $balanceFrom2023;
        
        if ((!$isStudentRegisteredOnSisReports && !$isStudentRegisteredOnEdurole) && ($balanceFrom2023 > 0)) {
            if($balanceFrom2023 > 0){
                return back()->with('error', 'Student has a balance from 2023 of K'.$balanceFrom2023);
            }else{
                return back()->with('error', 'Student not registered on Edurole or SIS Reports.');
            }        
        }

        // Update student status if needed
        $status = (int) $student->status;
        if ($status !== 6) {
            $student->status = 6;
            $student->save();
        }
        $supplementary = 1;

        return $this->getDocketSupplementaryData($studentNumber, $supplementary);
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
        $year = 2025;
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
        $deadLine = '2025-05-31';       
        
        $isStudentRegistered = $this->checkIfStudentIsRegistered($studentId)->exists();
        // $isStudentsStatus4 = Student::query()->where('student_number', $studentId)->where('status', 4)->exists();
        // if (!$isStudentsStatus4) {
        //     return redirect()->back()->with('error', 'Student can not register.');
        // }
        if ($isStudentRegistered) {
            return redirect()->back()->with('error', 'Student already registered On Edurole.');
        }
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', 2025)
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
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0    
                WHEN pa.TxDate < \'2025-01-01\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2025')            
            
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
            
            $studentInformation = $this->getAppealStudentDetails(2025, [$studentId])->first();
            
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
    
        $programeCode = trim($studentsProgramme->first()->CodeRegisteredUnder);
        $theNumberOfCourses = $this->getCoursesInASpecificProgrammeCode($programeCode)->count();
    
        $allInvoicesArray = SisReportsSageInvoices::all()->mapWithKeys(function ($item) {
            return [trim($item['InvoiceDescription']) => $item];
        })->toArray();
    
        $currentStudentsCourses = $studentsProgramme;
        $studentDetails = $this->getAppealStudentDetails(2025, [$studentId])->first();
        
    
        return view('allStudents.studentSelfRegistration', compact('actualBalance','studentStatus','studentDetails','courses', 'currentStudentsCourses', 'studentsPayments', 'failed', 'studentId', 'theNumberOfCourses'));
    }

    public function registerStudent($studentId) {
        set_time_limit(100000000);
    
        // Fetch student status with select to reduce data load
        $getStudentStatus = Student::query()->select('status')->where('student_number', $studentId)->first();
        $studentStatus = $getStudentStatus->status ?? null;
    
        if ($studentStatus === 5) {
            return redirect()->route('nmcz.registration', $studentId);
        }
    
        $todaysDate = date('Y-m-d');
        $deadLine = '2025-08-01';
    
        // Check if the student has status 4 instead of fetching the whole registration data if not necessary
        $isStudentsStatus4 = Student::query()->where('student_number', $studentId)->where('status', 4)->exists();
    
        // Check if the student has registered courses for the specified year and semester
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', 2025)
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
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0    
                WHEN pa.TxDate < \'2025-01-01\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2025') 
        )
        ->where('Account', $studentId)
        ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
        ->groupBy('DCLink', 'Account', 'Name')
        ->first();
    
        $actualBalance = $studentsPayments->TotalBalance ?? 0;
    
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
    
            return view('allStudents.registrationPage', compact('actualBalance', 'studentStatus', 'studentId', 'checkRegistration', 'studentInformation', 'failed'));
        }
    
        if ($todaysDate > $deadLine) {
            return redirect()->back()->with('error', 'Registration on Sis Reports is closed.');
        }
    
        $coursesArray = $courses->pluck('Course')->toArray();
        $coursesNamesArray = $courses->pluck('Program')->toArray();
    
        // Fetch program courses, with fallback if empty
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
    
        // Default handling if no program is found
        $firstProgramme = $studentsProgramme->first();
        $programeCode = $firstProgramme ? trim($firstProgramme->CodeRegisteredUnder) : 'N/A';
        $theNumberOfCourses = $programeCode !== 'N/A' ? $this->getCoursesInASpecificProgrammeCode($programeCode)->count() : 0;
    
        // Cache all invoices to avoid loading from the database multiple times
        $allInvoicesArray = Cache::remember('sis_reports_sage_invoices', 60, function () {
            return SisReportsSageInvoices::all()->mapWithKeys(function ($item) {
                return [trim($item['InvoiceDescription']) => $item];
            })->toArray();
        });
    
        $currentStudentsCourses = $studentsProgramme;
    
        // Fetch all courses and update CodeRegisteredUnder if student ID starts with 190
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

    public function registerStudentWithCarryOver($studentId) {
        set_time_limit(100000000);

        // Process common registration logic with admin flag set to true
        $data = $this->processCarryOverRegistration($studentId, true);
        
        // If result is a redirect response or a view, return it directly
        if ($data instanceof \Illuminate\Http\RedirectResponse || $data instanceof \Illuminate\View\View) {
            return $data;
        }

        Log::info($data);
        
        // Return admin registration view
        return view('allStudents.adminRegisterStudentWithCarryOver', $data);
    }

    /**
     * Process carry-over registration logic common to both student and admin registration
     *
     * @param string $studentId The student ID
     * @param bool $isAdmin Whether this is being called from an admin context
     * @return array|\Illuminate\Http\RedirectResponse Data for the view or a redirect response
     */
    private function processCarryOverRegistration($studentId, $isAdmin = false) 
    {
        // Get student status
        $studentStatus = $this->getStudentStatus($studentId);

        $getStudentStudyId = StudentStudyLink::where('StudentID', $studentId)->first()->StudyID;

        if($getStudentStudyId != 55 ){
            return redirect()->back()->with('error', 'Student is not eligible for this registration');  
        }
        
        // Redirect to NMCZ registration if student status is 5
        if ($studentStatus == 5) {
            return redirect()->route('nmcz.registration', $studentId);
        }
        
        $todaysDate = date('Y-m-d');
        $deadLine = '2024-12-20';
        
        // Check if student is already registered (only for student self-registration)
        if (!$isAdmin) {
            $isStudentRegistered = $this->checkIfStudentIsRegistered($studentId)->exists();
            // If needed, uncomment to redirect if already registered
            // if ($isStudentRegistered) {
            //     return redirect()->back()->with('error', 'Student already registered On Edurole.');
            // }
        }
        
        // Check for existing course registration
        $checkRegistration = $this->hasExistingRegistration($studentId);
        
        // Get student payment information
        $studentsPayments = $this->getStudentPayments($studentId);
        $actualBalance = $studentsPayments->TotalBalance ?? 0;
        
        // Process student courses
        $courseData = $this->processStudentCourses($studentId);
        extract($courseData);
        
        // Handle existing registration
        if ($checkRegistration) {
            return $this->handleExistingRegistration($studentId, $failed, $studentStatus);
        }
        
        // Get program data
        $programData = $this->getStudentProgramData($studentId, $courses);
        $studentsProgramme = $programData['studentsProgramme'];
        $programeCode = $programData['programeCode'];
        $theNumberOfCourses = $programData['theNumberOfCourses'];
        
        // Determine the correct year to use based on student ID
        $useYear = '2023';
        if (substr($studentId, 0, 3) === '190') {
            $useYear = '2019';
        }
        
        // Determine eligibility based on balance
        $registrationFee = 0;
        $isEligible = false;
        $useCorrectYearProgram = true;
        
        // Calculate registration fee from programs - ensuring we use the correct year version
        if (isset($studentsProgramme) && !empty($studentsProgramme)) {
            $firstProgram = $studentsProgramme->first();
            $programName = $firstProgram->CodeRegisteredUnder ?? '';
            
            // Check if we need to adjust the program year
            $programParts = explode('-', $programName);
            $alternativeProgramName = '';
            
            if (count($programParts) >= 3) {
                // If program contains a year that doesn't match our target year
                if (strpos($programParts[2], $useYear) === false) {
                    // Create alternative program name with the target year
                    $programParts[2] = $useYear;
                    $alternativeProgramName = implode('-', $programParts);
                    
                    // First try the original program
                    $sisInvoice = \App\Models\SageInvoice::where('Description', '=', $programName)->first();
                    
                    // If not found or we explicitly want to use the correct year version, try the alternative
                    if ($useCorrectYearProgram || !$sisInvoice) {
                        $alternativeInvoice = \App\Models\SageInvoice::where('Description', '=', $alternativeProgramName)->first();
                        if ($alternativeInvoice) {
                            $sisInvoice = $alternativeInvoice;
                            \Log::info("Using alternative program invoice: $alternativeProgramName instead of $programName for student $studentId");
                        }
                    }
                } else {
                    // The program already has the correct year
                    $sisInvoice = \App\Models\SageInvoice::where('Description', '=', $programName)->first();
                }
            } else {
                // If the program doesn't follow the expected format, use it as-is
                $sisInvoice = \App\Models\SageInvoice::where('Description', '=', $programName)->first();
            }
            
            if ($sisInvoice) {
                $registrationFee = round($sisInvoice->InvTotExclDEx * 0.25, 2);
            }
        }
        
        // Now determine eligibility based on actual balance and registration fee
        if ($actualBalance < 0 && abs($actualBalance) >= $registrationFee) {
            $isEligible = true;
        }
        
        // Get cached invoices
        $allInvoicesArray = $this->getCachedInvoices();
        
        // Set current student courses
        $currentStudentsCourses = $studentsProgramme;
        
        // Get student details
        $studentDetails = $this->getCachedStudentDetails($studentId);
        
        // Get all courses for admin view if needed
        $allCourses = $isAdmin ? $this->getAllCoursesForAdmin($studentId) : null;
        
        // Return all data needed for the views
        return compact(
            'actualBalance', 'studentStatus', 'studentDetails', 'courses',
            'allCourses', 'currentStudentsCourses', 'studentsPayments', 'failed',
            'studentId', 'theNumberOfCourses', 'carryOverCoursesCount', 'carryOverCourses',
            'isEligible', 'registrationFee'
        );
    }

    private function getStudentStatus($studentId) 
    {
        return Student::query()
            ->select('status')
            ->where('student_number', $studentId)
            ->first()
            ->status ?? null;
    }

    private function hasExistingRegistration($studentId)
    {
        return CourseRegistration::where('StudentID', $studentId)
            ->where('Year', 2025)
            ->where('Semester', 1)
            ->exists();
    }

    private function getStudentPayments($studentId)
    {
        return SageClient::select('DCLink', 'Account', 'Name',
            DB::raw('SUM(CASE WHEN pa.Description LIKE \'%reversal%\' THEN 0 WHEN pa.Description LIKE \'%FT%\' THEN 0 WHEN pa.Description LIKE \'%DE%\' THEN 0 WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0 ELSE pa.Credit END) AS TotalPayments'),
            DB::raw('SUM(pa.Credit) as TotalCredit'),
            DB::raw('SUM(pa.Debit) as TotalDebit'),
            DB::raw('SUM(pa.Debit) - SUM(pa.Credit) as TotalBalance'),
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0    
                WHEN pa.TxDate > \'2024-01-01\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2024'), 
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0    
                WHEN pa.TxDate > \'2025-01-01\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2025')            
        )
        ->where('Account', $studentId)
        ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
        ->groupBy('DCLink', 'Account', 'Name')
        ->first();
    }

    private function processStudentCourses($studentId)
    {
        // Get carry-over/repeat courses
        $carryOverCourses = $this->getCoursesForFailedStudents($studentId);
        $carryOverCoursesCount = count($carryOverCourses);
        Log::info('Carry-over Courses Count: ' . $carryOverCoursesCount);   
        
        // Get current year courses if needed
        $currentYearCourses = [];
        if ($carryOverCoursesCount <= 2) {
            $currentYearCourses = $this->findUnregisteredStudentCourses($studentId);
        }
        
        // Set the failed flag based on whether we have carry-over courses
        $failed = !empty($carryOverCourses) ? 1 : 2;
        
        // Combine courses based on the carry-over count
        if ($carryOverCoursesCount <= 2 && !empty($currentYearCourses)) {
            // Combine carry-over and current year courses
            $combinedCourses = array_merge($carryOverCourses, $currentYearCourses);
            $courses = collect($combinedCourses)->map(function ($item) {
                return (object)$item;
            });
        } else {
            // Only use carry-over courses
            $courses = collect($carryOverCourses)->map(function ($item) {
                return (object)$item;
            });
        }
        
        return [
            'carryOverCourses' => $carryOverCourses,
            'carryOverCoursesCount' => $carryOverCoursesCount,
            'courses' => $courses,
            'failed' => $failed
        ];
    }

    private function handleExistingRegistration($studentId, $failed, $studentStatus)
    {
        $checkRegistration = collect($this->getStudentRegistration($studentId));
        $courseIds = $checkRegistration->pluck('CourseID')->toArray();
        
        $checkRegistration = EduroleCourses::query()->whereIn('Name', $courseIds)->get();
        
        // Get cached student details
        $studentInformation = $this->getCachedStudentDetails($studentId);
        
        return view('allStudents.registrationPage', compact('studentStatus', 'studentId', 'checkRegistration', 'studentInformation', 'failed'));
    }

    private function getStudentProgramData($studentId, $courses)
    {
        $coursesArray = $courses->pluck('Course')->toArray();
        $coursesNamesArray = $courses->pluck('Program')->toArray();
        
        // Fetch program courses, with fallback if empty
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
        
        // Get program code and course count
        $programeCode = trim($studentsProgramme->first()->CodeRegisteredUnder ?? 'N/A');
        $theNumberOfCourses = $programeCode !== 'N/A' ? $this->getCoursesInASpecificProgrammeCode($programeCode)->count() : 0;
        
        return [
            'studentsProgramme' => $studentsProgramme,
            'programeCode' => $programeCode,
            'theNumberOfCourses' => $theNumberOfCourses
        ];
    }

    private function getCachedStudentDetails($studentId)
    {
        return Cache::remember("appeal_student_details_{$studentId}_2025", 60, function () use ($studentId) {
            return $this->getAppealStudentDetails(2025, [$studentId])->first();
        });
    }

    private function getAllCoursesForAdmin($studentId)
    {
        $allCourses = $this->getAllCoursesAttachedToProgrammeForAStudent($studentId)->get();
        if (str_starts_with($studentId, '190')) {
            $allCourses->transform(function ($allCourse) {
                $allCourse->CodeRegisteredUnder = str_replace('-2023-', '-2019-', $allCourse->CodeRegisteredUnder);
                return $allCourse;
            });
        }
        return $allCourses;
    }

    private function getCachedInvoices()
    {
        return Cache::remember('sis_reports_sage_invoices', 60, function () {
            return SisReportsSageInvoices::all()->mapWithKeys(function ($item) {
                return [trim($item['InvoiceDescription']) => $item];
            })->toArray();
        });
    }

    public function adminSubmitCourses(Request $request){
        $studentId = $request->input('studentNumber');
        $courses = $request->input('courses'); // Directly retrieve courses as an array
        $academicYear = 2025;

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
        $academicYear = 2025;

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

    public function studentSubmitCourseRegistrationDipEHBridging(Request $request){
        $studentId = $request->input('studentNumber');
        $courses = $request->input('courses'); // Directly retrieve courses as an array
        $academicYear = 2025;

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
        $studentIds = CourseElectives::where('Year', 2025)
                            ->pluck('StudentID')
                            ->unique()
                            ->toArray();

        // return $studentIds;
        $moodleController = new MoodleController();
        $moodleController->addStudentsFromEduroleToMoodleAndEnrollInCourses($studentIds);
        return redirect()->back()->with('success', 'Students enrolled successfully.');
    }    

    public function viewAllStudents(Request $request){
        $academicYear= 2025;
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

    public function studentRegisterForCoursesWithCarryOver($studentId) {
        set_time_limit(100000000);
        
        // Process common registration logic
        $data = $this->processCarryOverRegistration($studentId);
        
        // If result is a redirect response, return it
        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }
        
        // Return student self-registration view
        return view('allStudents.studentSelfRegistrationWithCarryOver', $data);
    }
    
    /**
     * Sync student data with the LMMU library API
     *
     * @param Request $request
     * @param bool $isBulk Whether to sync multiple students or a single student
     * @return \Illuminate\Http\JsonResponse
     */

    // Removed duplicate isAlreadyExistsError method - using checkForAlreadyExistsError instead

/**
 * Sync student data with the LMMU library API
 *
 * @param Request $request
 * @param string|null $studentId
 * @param bool $isBulk Whether to sync multiple students or a single student
 * @return \Illuminate\Http\JsonResponse
 */
    public function syncStudentsWithLibrary(Request $request, $studentId = null, $isBulk = false)
    {
        set_time_limit(12000000); // Set a long timeout for bulk operations
        
        Log::info('Starting library sync operation', [
            'bulk' => $isBulk,
            'studentId' => $studentId ?: 'bulk operation'
        ]);
        
        try {
            // API configuration from services.php
            $baseUrl = config('services.library_api.url');
            $accessKey = config('services.library_api.key');
            $timeout = config('services.library_api.timeout', 30);
            
            // Parse the base URL to ensure we handle the endpoint correctly
            // Remove any trailing "/student" if present in the base URL from .env
            $baseUrl = rtrim(str_replace('/student', '', $baseUrl), '/');
            $endpoint = $isBulk ? '/students' : '/student';
            $academicYear = config('app.academic_year', 2025); // Use config or fallback to 2025
            
            Log::info('Using library API endpoint', [
                'baseUrl' => $baseUrl,
                'endpoint' => $endpoint,
                'fullUrl' => $baseUrl . $endpoint
            ]);
            
            // Data preparation
            if ($isBulk) {
                // Get all registered students for the current academic year
                $registeredStudents = $this->getStudentNumbersForRegisteredStudents();
                
                if (empty($registeredStudents)) {
                    Log::warning('No registered students found for library sync', ['academicYear' => $academicYear]);
                    return response()->json([
                        'success' => false,
                        'message' => 'No registered students found for the current academic year'
                    ], 404);
                }
                
                // Build the students array from database records
                $students = [];
                
                // Log the total number of students to process
                Log::info('Starting library sync for ' . count($registeredStudents) . ' students');
                
                foreach ($registeredStudents as $studentId) {
                    // Get student details from the database
                    $studentDetails = $this->getAppealStudentDetails($academicYear, [$studentId])->first();
                    
                    if (!$studentDetails) {
                        Log::warning("Student details not found for ID: {$studentId}");
                        continue;
                    }
                    
                    // Extract required fields and format them for the API
                    $student = [
                        'admission_no' => $studentDetails->StudentID,
                        'email' => $studentDetails->PrivateEmail ?: "{$studentDetails->StudentID}@lmmu.ac.zm",
                        'first_name' => $studentDetails->FirstName,
                        'last_name' => $studentDetails->Surname
                    ];
                    
                    // Add optional fields if available
                    if ($studentDetails->Sex) {
                        $student['gender'] = strtolower(substr($studentDetails->Sex, 0, 1)); // Convert to 'm' or 'f'
                    }
                    
                    $students[] = $student;
                    
                    // Log progress for every 50 students
                    if (count($students) % 50 === 0) {
                        Log::info("Processed student batch", ['count' => count($students)]);
                    }
                }
                
                if (empty($students)) {
                    Log::warning('No valid student data found for library sync');
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid student data found to sync'
                    ], 404);
                }
                
                $data = [
                    'students' => $students
                ];
                
                Log::info('Prepared data for ' . count($students) . ' students');
            } else {
                // For single student, get data from database using the studentId
                if (!$studentId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Student ID is required for single student sync'
                    ], 400);
                }
                
                $studentDetails = $this->getAppealStudentDetails($academicYear, [$studentId])->first();
                
                if (!$studentDetails) {
                    return response()->json([
                        'success' => false,
                        'message' => "Student details not found for ID: {$studentId}"
                    ], 404);
                }
                
                Log::info("Processing single student", ['studentId' => $studentId]);
                
                $data = [
                    'student' => [
                        'admission_no' => $studentDetails->StudentID,
                        'email' => $studentDetails->PrivateEmail ?: "{$studentDetails->StudentID}@lmmu.ac.zm",
                        'first_name' => $studentDetails->FirstName,
                        'last_name' => $studentDetails->Surname
                    ]
                ];
                
                // Add optional fields if available
                if ($studentDetails->Sex) {
                    $data['student']['gender'] = strtolower(substr($studentDetails->Sex, 0, 1)); // Convert to 'm' or 'f'
                }
            }
            
            // Make API request with timeout, retry logic, and proper headers
            $maxRetries = 3;
            $retryDelay = 1000; // 1 second in milliseconds
            
            // Initialize response variable
            $response = null;
            $attempt = 0;
            $lastException = null;
            
            // Construct the full URL
            $apiUrl = $baseUrl . $endpoint;
            
            // Log the request details
            Log::info('Making library API request', [
                'url' => $apiUrl,
                'method' => 'POST',
                'isBulk' => $isBulk,
                'dataKeys' => array_keys($data)
            ]);
            
            // Retry loop
            while ($attempt < $maxRetries) {
                $attempt++;
                
                try {
                    // Make the API call with appropriate timeout and headers
                    $response = Http::timeout($timeout)
                        ->withHeaders([
                            'access-key' => $accessKey,
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                        ])
                        ->post($apiUrl, $data);
                    
                    // If successful or it's a "already exists" error, break the retry loop
                    if ($response->successful() || $this->checkForAlreadyExistsError($response)) {
                        break;
                    }
                    
                    // Log retry attempt
                    Log::warning("Library API request failed, attempt {$attempt}/{$maxRetries}", [
                        'status' => $response->status(),
                        'endpoint' => $endpoint,
                        'student_id' => $isBulk ? 'bulk' : $studentId
                    ]);
                    
                    // Wait before retrying
                    if ($attempt < $maxRetries) {
                        usleep($retryDelay * 1000 * $attempt); // Exponential backoff
                    }
                } catch (\Exception $e) {
                    $lastException = $e;
                    
                    Log::warning("Library API request exception, attempt {$attempt}/{$maxRetries}", [
                        'exception' => $e->getMessage(),
                        'endpoint' => $endpoint,
                        'student_id' => $isBulk ? 'bulk' : $studentId
                    ]);
                    
                    // Wait before retrying
                    if ($attempt < $maxRetries) {
                        usleep($retryDelay * 1000 * $attempt); // Exponential backoff
                    }
                }
            }
            
            // If all retries failed with exceptions and no response was obtained
            if (!$response && $lastException) {
                throw $lastException;
            }
            
            // Log the final response status and details for better debugging
            if (!$response->successful()) {
                Log::error('Library API Error Response', [
                    'status' => $response->status(),
                    'error' => $response->json(),
                    'url' => $apiUrl,
                    'endpoint' => $endpoint,
                    'student_id' => $isBulk ? 'bulk' : $studentId,
                    'attempts' => $attempt,
                    'response_body' => $response->body()
                ]);
            } else {
                Log::info('Library API Success Response', [
                    'status' => $response->status(),
                    'url' => $apiUrl,
                    'endpoint' => $endpoint,
                    'student_id' => $isBulk ? 'bulk' : $studentId,
                    'attempts' => $attempt
                ]);
            }
            
            // Check if the request was successful or if it's just an 'already exists' error
            $alreadyExists = $this->checkForAlreadyExistsError($response);
            if ($response->successful() || $alreadyExists) {
                // If it was a student-already-exists error, log it but treat as success
                if ($alreadyExists) {
                    Log::info('Student already exists in library system - treating as success', [
                        'student_id' => $isBulk ? 'bulk' : $studentId,
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $isBulk ? 'Students synced successfully' : 'Student synced successfully',
                    'count' => $isBulk ? count($data['students']) : 1,
                    'already_exists' => $alreadyExists,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to sync with library API',
                    'error' => $response->json(),
                    'status' => $response->status()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Library API Sync Error', [
                'message' => $e->getMessage(),
                'student_id' => $studentId ?: 'bulk operation'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while syncing with the library API',
                'error' => app()->environment('production') ? 'Internal server error' : $e->getMessage()
            ], 500);
        }
    }

/**
 * Check if an API response indicates that the student already exists
 *
 * @param \Illuminate\Http\Client\Response $response The HTTP response
 * @return bool True if the error is due to the student already existing
 */
    /**
     * Check if an API response indicates that the student already exists
     *
     * @param \Illuminate\Http\Client\Response|null $response The HTTP response
     * @return bool True if the error is due to the student already existing
     */
    protected function checkForAlreadyExistsError($response)
    {
        // If response is null, can't determine if it's an "already exists" error
        if (!$response) {
            return false;
        }
        
        // Check if the response is an error
        if ($response->successful()) {
            return false;
        }
        
        // Get the response data - handle potential JSON parsing failures
        try {
            $responseData = $response->json();
        } catch (\Exception $e) {
            Log::warning('Failed to parse JSON from library API response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        }
        
        // HTTP 409 Conflict status is commonly used for duplicates/already exists
        if ($response->status() === 409) {
            return true;
        }
        
        // Check for specific error messages that indicate the student already exists
        if (isset($responseData['error']) && is_string($responseData['error'])) {
            $errorMessage = strtolower($responseData['error']);
            
            return str_contains($errorMessage, 'already exists') || 
                str_contains($errorMessage, 'duplicate') || 
                str_contains($errorMessage, 'exists') ||
                str_contains($errorMessage, 'previously registered');
        }
        
        // Check for error message in other common response formats
        if (isset($responseData['message']) && is_string($responseData['message'])) {
            $errorMessage = strtolower($responseData['message']);
            
            return str_contains($errorMessage, 'already exists') || 
                str_contains($errorMessage, 'duplicate') || 
                str_contains($errorMessage, 'exists') ||
                str_contains($errorMessage, 'previously registered');
        }
        
        return false;
    }
    
    /**
     * Sync a single student with the LMMU library API
     *
     * @param mixed $requestOrStudentId Request object or student ID string
     * @param string|null $studentId Student ID to sync if first param is Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncSingleStudentWithLibrary($requestOrStudentId, $studentId = null)
    {
        // Check if first parameter is a Request object or a student ID
        if ($requestOrStudentId instanceof Request) {
            $request = $requestOrStudentId;
            
            // If studentId is not provided as second param, check if it's in the request body
            if (!$studentId && $request->has('student_id')) {
                $studentId = $request->student_id;
            }
            
            return $this->syncStudentsWithLibrary($request, $studentId, false);
        } else {
            // First parameter is the student ID
            $studentId = $requestOrStudentId;
            $request = new Request();
            
            return $this->syncStudentsWithLibrary($request, $studentId, false);
        }
    }
    
    /**
     * Sync multiple students with the LMMU library API
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncMultipleStudentsWithLibrary(Request $request)
    {
        return $this->syncStudentsWithLibrary($request, null, true);
    }
}
