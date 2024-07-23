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
        $homeController = new HomeController();
        return $homeController->index();
    }

    public function addStudentsToMoodleAndEnrollInCourses($studentIds){   
        set_time_limit(12000000);    
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

    public function addStudentsFromEduroleToMoodleAndEnrollInCourses($studentIds){   
        set_time_limit(12000000);    
        foreach($studentIds as $studentId){            
            $student = BasicInformation::where('ID', $studentId)->first();
            $courses = $this->getStudentRegistrationFromEdurole($studentId);
            $courseIds = $courses->pluck('Name');
            $user = $this->createUserAccountIfDoesNotExist($student);
            if ($user) {
                $this->assignUserToRoleIfNotAssigned($courseIds, $user->id);
                $this->enrollUserIntoCourses($courseIds, $user->id);
            }
        }
    }

    private function createUserAccountIfDoesNotExist($student){
        set_time_limit(12000000);
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

            return $existingUser;
        } catch (\Exception $e) {
            // Log::error('Error creating user account: ' . $e->getMessage());
            return null;
        }
    }
    
    private function assignUserToRoleIfNotAssigned($courseIds, $userId){
        set_time_limit(12000000);
        try {
            $roleId = 5; // Assuming role ID 5 is the default role
            
            // $existingUserRole = MoodleRoleAssignments::where('userid', $userId)->first();

            foreach($courseIds as $courseId){
                $course = MoodleCourses::where('shortname', $courseId)->first();                
                if ($course) {
                    MoodleRoleAssignments::updateOrCreate(
                        [
                            'userid' => $userId,
                            'contextid' => $course->id
                        ],
                        [
                            'roleid' => 5,
                            'timemodified' => time(),
                        ]
                    );
                }
            }
            // }
        } catch (\Exception $e) {
            // Log::error('Error assigning user to role: ' . $e->getMessage());
        }
    }
    
    private function enrollUserIntoCourses($courses, $userId){
        set_time_limit(12000000);  
        $date = '2024-12-31';
        $timeend = strtotime($date);      
    
        try {
            $enrolIds = [];

            foreach($courses as $course){
                $course = MoodleCourses::where('idnumber', $course)->first();
                $courseId = $course->id;

                $enrolId = MoodleEnroll::where('courseid', $courseId)->first();
                // Log::info($enrolId);
                // $checkIfUserIsEnrolled = MoodleUserEnrolments::where('userid', $userId)->where('enrolid', $enrolId->id)->first();

                
                MoodleUserEnrolments::updateOrCreate(
                    [
                        'enrolid' => $enrolId->id,                        
                        'userid' => $userId,
                    ],
                    [
                        'status' => 0, // Assuming status 0 is 'active
                        'timestart' => time(),
                        'timeend' => $timeend,
                        'modifierid' => time(),
                        'timecreated' => time(),
                        'timemodified' => time(),
                    ]
                );                

                $enrolIds[] = $enrolId->id;
            }
            // Delete all enrollments where enrolid is not in $enrolIds
            // MoodleUserEnrolments::where('userid', $userId)->whereNotIn('enrolid', $enrolIds)->delete();
        } catch (\Exception $e) {
            // Log::error('Error enrolling user into courses: ' . $e->getMessage());
        }
    }    
}
