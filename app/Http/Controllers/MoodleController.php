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
use Adldap\Adldap; // Import the Adldap library

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
        // Use a more reasonable timeout (30 minutes)
        set_time_limit(1800);
        $failedEnrollments = [];
        
        foreach($studentIds as $studentId){
            try {
                $student = BasicInformation::where('ID', $studentId)->first();
                if (!$student) {
                    Log::error("Student with ID $studentId not found in BasicInformation");
                    $failedEnrollments[] = ['studentId' => $studentId, 'reason' => 'Student not found'];
                    continue;
                }
                
                // Create Active Directory account
                $this->createActiveDirectoryAccount($student);

                $courses = $this->getStudentRegistrationFromEdurole($studentId);
                if ($courses->isEmpty()) {
                    Log::warning("No courses found for student $studentId");
                    continue;
                }
                
                $courseIds = $courses->pluck('Name');
                $user = $this->createUserAccountIfDoesNotExist($student);
                
                if ($user) {
                    $this->assignUserToRoleIfNotAssigned($courseIds, $user->id);
                    $this->enrollUserIntoCourses($courseIds, $user->id);
                    Log::info("Successfully enrolled student $studentId in " . $courseIds->count() . " courses");
                } else {
                    Log::error("Failed to create or retrieve user account for student $studentId");
                    $failedEnrollments[] = ['studentId' => $studentId, 'reason' => 'Failed to create user account'];
                }
            } catch (\Exception $e) {
                Log::error("Error enrolling student $studentId: " . $e->getMessage());
                $failedEnrollments[] = ['studentId' => $studentId, 'reason' => $e->getMessage()];
            }
        }
        
        if (!empty($failedEnrollments)) {
            Log::warning("Failed to enroll " . count($failedEnrollments) . " students. See logs for details.");
        }
        
        return $failedEnrollments;
    }

    private function createUserAccountIfDoesNotExist($student){
        // Use a more reasonable timeout (5 minutes)
        set_time_limit(300);
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
        // Use a more reasonable timeout (5 minutes)
        set_time_limit(300);
        $assignedCourses = 0;
        $failedAssignments = 0;
        
        try {
            $roleId = 5; // Student role ID
            
            foreach($courseIds as $courseId){
                try {
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
                        $assignedCourses++;
                    } else {
                        Log::warning("Course with shortname '$courseId' not found in Moodle");
                        $failedAssignments++;
                    }
                } catch (\Exception $e) {
                    Log::error("Error assigning user $userId to course $courseId: " . $e->getMessage());
                    $failedAssignments++;
                }
            }
            
            if ($failedAssignments > 0) {
                Log::warning("Failed to assign $failedAssignments courses for user $userId");
            }
            if ($assignedCourses > 0) {
                Log::info("Successfully assigned $assignedCourses courses to user $userId");
            }
        } catch (\Exception $e) {
            Log::error('Error in assignUserToRoleIfNotAssigned: ' . $e->getMessage());
        }
    }
    
    private function enrollUserIntoCourses($courses, $userId){
        // Use a more reasonable timeout (5 minutes)
        set_time_limit(300);
        $enrolledCourses = 0;
        $failedEnrollments = 0;
        
        try {
            $enrolIds = [];

            foreach($courses as $courseId){
                try {
                    $course = MoodleCourses::where('idnumber', $courseId)->first();
                    if (!$course) {
                        Log::warning("Course with idnumber '$courseId' not found in Moodle");
                        $failedEnrollments++;
                        continue;
                    }
                    
                    $moodleCourseId = $course->id;
                    $enrolId = MoodleEnroll::where('courseid', $moodleCourseId)->first();
                    
                    if (!$enrolId) {
                        Log::warning("No enrollment method found for course $moodleCourseId");
                        $failedEnrollments++;
                        continue;
                    }
                    
                    MoodleUserEnrolments::updateOrCreate(
                        [
                            'enrolid' => $enrolId->id,                        
                            'userid' => $userId,
                        ],
                        [
                            'status' => 0, // Active enrollment
                            'timestart' => time(),
                            'timeend' => 1720959600, // Consider making this dynamic
                            'modifierid' => time(),
                            'timecreated' => time(),
                            'timemodified' => time(),
                        ]
                    );
                    
                    $enrolIds[] = $enrolId->id;
                    $enrolledCourses++;
                } catch (\Exception $e) {
                    Log::error("Error enrolling user $userId in course $courseId: " . $e->getMessage());
                    $failedEnrollments++;
                }
            }
            
            if ($failedEnrollments > 0) {
                Log::warning("Failed to enroll in $failedEnrollments courses for user $userId");
            }
            if ($enrolledCourses > 0) {
                Log::info("Successfully enrolled user $userId in $enrolledCourses courses");
            }
        } catch (\Exception $e) {
            Log::error('Error in enrollUserIntoCourses: ' . $e->getMessage());
        }
    }

    private function createActiveDirectoryAccount($student)
    {
        try {
            $powershellScript = storage_path('scripts/create_ad_user.ps1');
            $command = "powershell -ExecutionPolicy Bypass -File $powershellScript -FirstName '{$student->FirstName}' -LastName '{$student->Surname}' -Email '{$student->PrivateEmail}' -Username '{$student->ID}' -Password '{$student->NRC}'";

            $output = shell_exec($command);

            if (strpos($output, 'Success') !== false) {
                Log::info("Active Directory account created for student ID: {$student->ID}");
            } else {
                Log::error("Failed to create Active Directory account for student ID: {$student->ID}. Output: $output");
            }
        } catch (\Exception $e) {
            Log::error("Error executing PowerShell script for student ID: {$student->ID}. Error: " . $e->getMessage());
        }
    }
}
