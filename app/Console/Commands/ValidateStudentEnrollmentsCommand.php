<?php

namespace App\Console\Commands;

use App\Http\Controllers\MoodleController;
use App\Models\CourseElectives;
use App\Models\MoodleUsers;
use App\Models\MoodleUserEnrolments;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidateStudentEnrollmentsCommand extends Command
{
    protected $signature = 'enrollments:validate {--fix : Fix any enrollment issues found}';

    protected $description = 'Validate and optionally fix Moodle student enrollments';

    protected $moodleController;

    public function __construct()
    {
        parent::__construct();
        $this->moodleController = new MoodleController();
    }

    public function handle()
    {
        // Set a reasonable timeout
        set_time_limit(1800);
        
        $shouldFix = $this->option('fix');
        
        $this->info('Starting enrollment validation...');
        
        // Get all students with expected enrollments from Edurole
        $studentsWithExpectedEnrollments = CourseElectives::select('StudentID')
            ->distinct()
            ->get()
            ->pluck('StudentID')
            ->toArray();
            
        $this->info('Found ' . count($studentsWithExpectedEnrollments) . ' students with expected enrollments');
        
        // Stats counters
        $totalStudents = count($studentsWithExpectedEnrollments);
        $studentsWithoutMoodleAccount = 0;
        $studentsWithMissingEnrollments = 0;
        $studentsWithExtraEnrollments = 0;
        $studentsWith504Errors = 0;
        $enrollmentsFixed = 0;
        
        // Process in batches to avoid timeouts
        $batchSize = 50;
        $studentBatches = array_chunk($studentsWithExpectedEnrollments, $batchSize);
        
        foreach ($studentBatches as $index => $batch) {
            $this->info('Processing validation batch ' . ($index + 1) . ' of ' . count($studentBatches));
            
            foreach ($batch as $studentId) {
                $this->validateStudentEnrollment($studentId, $shouldFix, $studentsWithoutMoodleAccount, 
                                              $studentsWithMissingEnrollments, $studentsWithExtraEnrollments, 
                                              $studentsWith504Errors, $enrollmentsFixed);
            }
            
            // Add a short delay between batches
            if ($index < count($studentBatches) - 1) {
                sleep(2);
            }
        }
        
        // Output summary
        $this->info('=== Validation Summary ===');
        $this->info("Total students checked: $totalStudents");
        $this->info("Students without Moodle accounts: $studentsWithoutMoodleAccount");
        $this->info("Students with missing enrollments: $studentsWithMissingEnrollments");
        $this->info("Students with extra enrollments: $studentsWithExtraEnrollments");
        $this->info("Students with potential 504 errors: $studentsWith504Errors");
        
        if ($shouldFix) {
            $this->info("Enrollments fixed: $enrollmentsFixed");
        }
        
        Log::info("Enrollment validation completed. Students checked: $totalStudents, Fixed: $enrollmentsFixed");
    }
    
    private function validateStudentEnrollment($studentId, $shouldFix, &$studentsWithoutMoodleAccount, 
                                           &$studentsWithMissingEnrollments, &$studentsWithExtraEnrollments, 
                                           &$studentsWith504Errors, &$enrollmentsFixed)
    {
        // First check if student has a Moodle account
        $moodleAccount = MoodleUsers::where('username', $studentId)->first();
        
        if (!$moodleAccount) {
            $this->warn("Student $studentId does not have a Moodle account");
            $studentsWithoutMoodleAccount++;
            
            if ($shouldFix) {
                // Create user account and enroll in courses
                $this->info("Creating Moodle account for student $studentId");
                $this->moodleController->addStudentsFromEduroleToMoodleAndEnrollInCourses([$studentId]);
                $enrollmentsFixed++;
            }
            
            return;
        }
        
        // Get expected enrollments from CourseElectives
        $expectedCourses = CourseElectives::where('StudentID', $studentId)
            ->pluck('CourseID')
            ->toArray();
            
        // Get actual enrollments from Moodle
        $actualEnrollments = DB::table('mdl_user_enrolments')
            ->join('mdl_enrol', 'mdl_enrol.id', '=', 'mdl_user_enrolments.enrolid')
            ->join('mdl_course', 'mdl_course.id', '=', 'mdl_enrol.courseid')
            ->where('mdl_user_enrolments.userid', $moodleAccount->id)
            ->select('mdl_course.idnumber as course_id')
            ->get()
            ->pluck('course_id')
            ->toArray();
            
        // Check for missing enrollments
        $missingEnrollments = array_diff($expectedCourses, $actualEnrollments);
        if (count($missingEnrollments) > 0) {
            $this->warn("Student $studentId is missing " . count($missingEnrollments) . " enrollments");
            $studentsWithMissingEnrollments++;
            
            // If missing too many enrollments, might have been affected by 504 error
            if (count($missingEnrollments) > 3 && count($expectedCourses) > 5) {
                $this->error("Student $studentId may have been affected by 504 errors");
                $studentsWith504Errors++;
            }
            
            if ($shouldFix) {
                // Enroll student in missing courses
                $this->info("Fixing missing enrollments for student $studentId");
                foreach ($missingEnrollments as $courseId) {
                    $this->moodleController->enrollUserIntoCourses($moodleAccount->id, [$courseId]);
                }
                $enrollmentsFixed++;
            }
        }
        
        // Check for extra enrollments (not in expected courses)
        $extraEnrollments = array_diff($actualEnrollments, $expectedCourses);
        if (count($extraEnrollments) > 0) {
            $this->warn("Student $studentId has " . count($extraEnrollments) . " extra enrollments");
            $studentsWithExtraEnrollments++;
            
            // We don't remove extra enrollments by default, as they might be legitimate
            // If needed, can be implemented later
        }
    }
}
