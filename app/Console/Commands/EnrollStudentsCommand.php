<?php

namespace App\Console\Commands;

use App\Http\Controllers\MoodleController;
use App\Http\Controllers\SisReportsEduroleDataManagementController;
use App\Http\Controllers\StudentsController;
use App\Mail\CronJobEmail;
use App\Models\CourseElectives;
use App\Models\CourseRegistration;
use App\Models\MoodleUserEnrolments;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnrollStudentsCommand extends Command
{
    protected $signature = 'bulk:enroll';

    protected $description = 'Enroll students in Moodle in bulk';

    public function handle()
    {
        
        set_time_limit(12000000);
        MoodleUserEnrolments::where('timeend', '>', 0)        
            ->update(['timeend' => strtotime('2025-12-31')]);
        Mail::to('ict.lmmu@lmmu.ac.zm')->send(new CronJobEmail());
        $studentIds = CourseElectives::pluck('StudentID')
                        ->where('course-electives.Year', 2025)
                        ->unique()
                        ->toArray();
        // $studentIdSisReports = CourseRegistration::pluck('StudentID')
        //                 ->unique()
        //                 ->toArray();
        $moodleController = new MoodleController();
        // $sisReportsEduroleDataManagementController = new SisReportsEduroleDataManagementController();
        // $sisReportsEduroleDataManagementController->importOrUpdateSisReportsEduroleData();
        
        $moodleController->addStudentsFromEduroleToMoodleAndEnrollInCourses($studentIds); 

        $studentsController = new StudentsController();
        $studentsController->importStudentsFromLMMAX();
        // $moodleController->addStudentsToMoodleAndEnrollInCourses($studentIdSisReports);
        MoodleUserEnrolments::where('timeend', '>', 0)        
            ->update(['timeend' => strtotime('2025-12-31')]);

        Mail::to('ict.lmmu@lmmu.ac.zm')->send(new CronJobEmail());     
        $this->info('Students enrolled successfully.');
        // Log::info('Students enrolled successfully.');
    }
}
