<?php

namespace App\Console\Commands;

use App\Http\Controllers\MoodleController;
// use App\Http\Controllers\SisReportsEduroleDataManagementController;  // Not enrolling from SIS Reports
use App\Http\Controllers\StudentsController;
use App\Mail\CronJobEmail;
use App\Models\CourseElectives;
// use App\Models\CourseRegistration;  // Not enrolling from SIS Reports
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnrollStudentsCommand extends Command
{
    protected $signature = 'bulk:enroll';

    protected $description = 'Enroll students in Moodle in bulk';

    public function handle()
    {
        Mail::to('ict.lmmu@lmmu.ac.zm')->send(new CronJobEmail());
        // Use a more reasonable timeout - 30 minutes instead of ~139 days
        set_time_limit(1800);
        $studentIds = CourseElectives::pluck('StudentID')
                        ->unique()
                        ->toArray();
        // Process in smaller batches of 50 students
        $batchSize = 50;
        $studentBatches = array_chunk($studentIds, $batchSize);
        
        $moodleController = new MoodleController();
        
        // Not enrolling from SIS Reports - comment out this code
        // $sisReportsEduroleDataManagementController = new SisReportsEduroleDataManagementController();
        // $sisReportsEduroleDataManagementController->importOrUpdateSisReportsEduroleData();
        
        foreach ($studentBatches as $index => $batch) {
            $this->info('Processing batch ' . ($index + 1) . ' of ' . count($studentBatches));
            $moodleController->addStudentsFromEduroleToMoodleAndEnrollInCourses($batch);
            // Add a short delay between batches to prevent overloading the server
            if ($index < count($studentBatches) - 1) {
                sleep(2);
            }
        }
        
        $this->info('Students enrolled successfully.');
        Log::info('Students enrolled successfully.');
    }
}
