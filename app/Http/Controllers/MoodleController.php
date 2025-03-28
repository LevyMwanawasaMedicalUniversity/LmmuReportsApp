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
            // Validate required student data
            if (empty($student->FirstName) || empty($student->Surname) || 
                empty($student->PrivateEmail) || empty($student->ID) || empty($student->NRC)) {
                Log::error("Missing required student data for AD account creation: {$student->ID}");
                return false;
            }

            // Escape parameters to prevent command injection
            $firstName = escapeshellarg($student->FirstName);
            $lastName = escapeshellarg($student->Surname);
            $email = escapeshellarg($student->PrivateEmail);
            $username = escapeshellarg($student->ID);
            
            // Generate a secure password instead of using NRC directly
            $securePassword = escapeshellarg($this->generateSecurePassword($student->NRC));
            
            // Fetch PowerShell script path and LDAP parameters from .env
            $powershellScript = storage_path(env('POWERSHELL_SCRIPT_PATH', 'scripts/create_ad_user.ps1'));
            $ldapServer = escapeshellarg(env('LDAP_SERVER', 'ldap.example.com'));
            $ldapPort = escapeshellarg(env('LDAP_PORT', 389));
            $ldapBaseDN = escapeshellarg(env('LDAP_BASE_DN', 'ou=Users,dc=example,dc=com'));
            $domain = escapeshellarg(env('LDAP_DOMAIN', 'example.com'));
            $adminUsername = escapeshellarg(env('LDAP_ADMIN_USERNAME', 'admin'));
            $adminPassword = escapeshellarg(env('LDAP_ADMIN_PASSWORD', 'YourAdminPassword'));
            
            // Ensure the script exists
            if (!file_exists($powershellScript)) {
                Log::error("PowerShell script not found at: $powershellScript");
                return false;
            }
            
            // Build command with properly escaped arguments
            $command = "powershell -ExecutionPolicy Bypass -File \"$powershellScript\" -FirstName $firstName -LastName $lastName -Email $email -Username $username -Password $securePassword -LdapServer $ldapServer -LdapPort $ldapPort -LdapBaseDN $ldapBaseDN -Domain $domain -AdminUsername $adminUsername -AdminPassword $adminPassword";

            // Execute with proper output capture
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);
            
            // Convert output array to string for logging
            $outputString = implode("\n", $output);
            
            // Check both return code and output for better error detection
            if ($returnCode === 0 && strpos($outputString, 'Success') !== false) {
                Log::info("Active Directory account created for student ID: {$student->ID}");
                return true;
            } else {
                Log::error("Failed to create Active Directory account for student ID: {$student->ID}. Return code: $returnCode, Output: $outputString");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error executing PowerShell script for student ID: {$student->ID}. Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate a secure password based on a seed value
     * 
     * @param string $seed A seed value (like NRC)
     * @return string A secure password
     */
    private function generateSecurePassword($seed)
    {
        // Create a hash based on the seed and a random salt
        $base = hash('sha256', $seed . uniqid(mt_rand(), true));
        
        // Ensure password has uppercase, lowercase, number and special char
        $uppercase = chr(rand(65, 90)); // A-Z
        $lowercase = chr(rand(97, 122)); // a-z
        $number = chr(rand(48, 57)); // 0-9
        $special = chr(rand(33, 47)); // special character
        
        // Take first 8 chars from hash and add required character types
        $password = substr($base, 0, 8) . $uppercase . $lowercase . $number . $special;
        
        // Shuffle the password
        return str_shuffle($password);
    }
}
