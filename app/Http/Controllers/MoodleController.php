<?php

namespace App\Http\Controllers;

use App\Models\BasicInformation;
use App\Models\MoodelUsers;
use App\Models\MoodleCourses;
use App\Models\MoodleEnroll;
use App\Models\MoodleRoleAssignments;
use App\Models\MoodleUserEnrolments;
use App\Models\MoodleUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleController extends Controller
{
    public function index(){
        $moodleUsers = MoodleUsers::take(500)->get();
        $moodelCourses = MoodleCourses::take(500)->get();
        $courseContextId = $this->getCourseContextId(3043);
        return $moodelCourses;
    }

    public function addStudentsToMoodleAndEnrollInCourses($studentIds){       
        foreach($studentIds as $studentId){            
            $student = BasicInformation::where('ID', $studentId)->first();
            $courses = $this->getStudentRegistration($studentId);
            $courseIds = $courses->pluck('CourseID');
            $user = $this->createUserAccountIfDoesNotExist($student);
            if ($user) {
                $this->assignUserToRoleIfNotAssigned($courseIds, $user->id);
                $this->enrollUserIntoCourses($courseIds, $user->id);
            }
        }
    }

    private function createUserAccountIfDoesNotExist($student){
        try {
            $existingUser = MoodleUsers::where('username', $student->ID)->first();
            
            if ($existingUser) {
                return $existingUser;
            } else {
                return MoodleUsers::create([
                    'confirmed' => 1,
                    'policyagreed' => 0,
                    'deleted' => 0,
                    'suspended' => 0,
                    'mnethostid' => 1,
                    'username' => $student->ID,
                    'idnumber' => $student->ID,
                    'firstname' => $student->FirstName,
                    'lastname' => $student->Surname,
                    'email' => $student->PrivateEmail,
                    'emailstop' => 0,
                    'calenderType' => 'gregorian',
                    'auth' => 'db',
                    'lang' => 'en',
                    'timemodified' => time(),
                    'timecreated' => time(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating user account: ' . $e->getMessage());
            return null;
        }
    }
    
    private function assignUserToRoleIfNotAssigned($courseIds, $userId){
        try {
            $roleId = 5; // Assuming role ID 5 is the default role
            
            $existingUserRole = MoodleRoleAssignments::where('userid', $userId)->first();
            
            if (!$existingUserRole) {
                foreach($courseIds as $courseId){
                    $course = MoodleCourses::where('idnumber', $courseId)->first();
                    
                    if ($course) {
                        MoodleRoleAssignments::create([
                            'userid' => $userId,
                            'roleid' => $roleId,
                            'contextid' => $course->id,
                            'timemodified' => time(),
                            'timecreated' => time(),
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error assigning user to role: ' . $e->getMessage());
        }
    }
    
    private function enrollUserIntoCourses($courseIds, $userId){
        try {
            foreach($courseIds as $courseId){
                $enrolId = MoodleEnroll::where('idnumber', $courseId)->value('id');
                
                if ($enrolId) {
                    MoodleUserEnrolments::create([
                        'enrolid' => $enrolId,
                        'userid' => $userId,
                        'timestart' => time(),
                        'timeend' => mktime(0, 0, 0, 12, 30, date("Y")),
                        'modifierid' => time(),
                        'timecreated' => time(),
                        'timemodified' => time(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error enrolling user into courses: ' . $e->getMessage());
        }
    }    
}
