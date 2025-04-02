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

            // Get LDAP configuration from .env
            $ldapServer = env('LDAP_SERVER', 'ldap.example.com');
            $ldapPort = intval(env('LDAP_PORT', 389));
            $ldapBaseDN = env('LDAP_BASE_DN', 'ou=Users,dc=example,dc=com');
            $domain = env('LDAP_DOMAIN', 'example.com');
            $adminUsername = env('LDAP_ADMIN_USERNAME', 'admin');
            $adminPassword = env('LDAP_ADMIN_PASSWORD', 'YourAdminPassword');
            
            // Initialize LDAP connection
            $ldapConn = ldap_connect($ldapServer, $ldapPort);
            if (!$ldapConn) {
                Log::error("Failed to connect to LDAP server for student ID: {$student->ID}");
                return false;
            }
            
            // Set LDAP options
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
            
            // Bind with admin credentials
            $ldapBindDN = $adminUsername . '@' . $domain;
            $ldapBind = @ldap_bind($ldapConn, $ldapBindDN, $adminPassword);
            if (!$ldapBind) {
                Log::error("Failed to bind to LDAP server for student ID: {$student->ID}. Error: " . ldap_error($ldapConn));
                ldap_close($ldapConn);
                return false;
            }
            
            // Prepare user attributes
            $userDN = "CN={$student->FirstName} {$student->Surname}," . $ldapBaseDN;
            $samAccountName = $student->ID;
            $userPrincipalName = $student->ID . '@' . $domain;
            $displayName = $student->FirstName . ' ' . $student->Surname;
            
            // Setup user entry attributes
            $userInfo = [
                'cn' => $displayName,
                'sAMAccountName' => $samAccountName,
                'userPrincipalName' => $userPrincipalName,
                'givenName' => $student->FirstName,
                'sn' => $student->Surname,
                'displayName' => $displayName,
                'mail' => $student->PrivateEmail,
                'objectClass' => [
                    'top',
                    'person', 
                    'organizationalPerson', 
                    'user'
                ],
                'userAccountControl' => '512', // Normal account, enabled
            ];
            
            // Add the user entry
            $result = @ldap_add($ldapConn, $userDN, $userInfo);
            
            if (!$result) {
                Log::error("Failed to add user to Active Directory for student ID: {$student->ID}. Error: " . ldap_error($ldapConn));
                ldap_close($ldapConn);
                return false;
            }
            
            // Set user password
            $userPassword = $student->NRC; // Consider using a more secure password
            $encodedPassword = '"{SHA}"' . base64_encode(pack('H*', sha1($userPassword)));
            $passwordData = ['unicodePwd' => $encodedPassword];
            
            $pwdResult = @ldap_modify($ldapConn, $userDN, $passwordData);
            if (!$pwdResult) {
                Log::error("Failed to set password for Active Directory account: {$student->ID}. Error: " . ldap_error($ldapConn));
                // Note: User is created but without password set
            }
            
            // Close LDAP connection
            ldap_close($ldapConn);
            
            Log::info("Active Directory account created for student ID: {$student->ID}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error creating Active Directory account for student ID: {$student->ID}. Error: " . $e->getMessage());
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
