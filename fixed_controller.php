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
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentsController extends Controller
{
    // Keep all the existing methods, making sure you only have ONE processCarryOverRegistration method
    // ...

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
        
        // Determine eligibility based on balance
        $registrationFee = 0; // Will be set based on program data later
        $isEligible = false;
        
        // Calculate registration fee from the first program
        if (isset($studentsProgramme) && !empty($studentsProgramme)) {
            $firstProgram = $studentsProgramme->first();
            $programName = $firstProgram->CodeRegisteredUnder ?? '';
            $sisInvoice = \App\Models\SageInvoice::where('Description', '=', $programName)->first();
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

    // Make sure all your other methods are included here
    // ...
}
