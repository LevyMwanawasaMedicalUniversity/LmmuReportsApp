<?php

namespace App\Console\Commands;

use App\Http\Controllers\MoodleController;
use App\Http\Controllers\StudentsController;
use App\Models\CourseElectives;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnrollStudentsCommand extends Command
{
    protected $signature = 'bulk:enroll';

    protected $description = 'Enroll students in Moodle in bulk';

    public function handle()
    {
        set_time_limit(12000000);
        $studentIds = CourseElectives::pluck('StudentID')
                        ->unique()
                        ->toArray();
        $moodleController = new MoodleController();
        $moodleController->addStudentsFromEduroleToMoodleAndEnrollInCourses($studentIds);
        $this->info('Students enrolled successfully.');
        Log::info('Students enrolled successfully.');
    }
}
