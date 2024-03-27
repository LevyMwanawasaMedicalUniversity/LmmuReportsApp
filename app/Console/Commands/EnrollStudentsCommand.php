<?php

namespace App\Console\Commands;

use App\Http\Controllers\MoodleController;
use App\Http\Controllers\SisReportsEduroleDataManagementController;
use App\Http\Controllers\StudentsController;
use App\Mail\CronJobEmail;
use App\Models\CourseElectives;
use App\Models\CourseRegistration;
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
        set_time_limit(12000000);
        $studentIds = CourseElectives::pluck('StudentID')
                        ->unique()
                        ->toArray();
        $studentIdSisReports = CourseRegistration::pluck('StudentID')
                        ->unique()
                        ->toArray();
        $moodleController = new MoodleController();
        $sisReportsEduroleDataManagementController = new SisReportsEduroleDataManagementController();
        $sisReportsEduroleDataManagementController->importOrUpdateSisReportsEduroleData();
        
        $moodleController->addStudentsFromEduroleToMoodleAndEnrollInCourses($studentIds); 
        $moodleController->addStudentsToMoodleAndEnrollInCourses($studentIdSisReports);      
        $this->info('Students enrolled successfully.');
        Log::info('Students enrolled successfully.');
    }
}
