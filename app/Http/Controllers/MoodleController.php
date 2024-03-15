<?php

namespace App\Http\Controllers;

use App\Models\MoodelUsers;
use App\Models\MoodleCourses;
use App\Models\MoodleUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MoodleController extends Controller
{
    public function index(){
        $moodleUsers = MoodleUsers::take(500)->get();
        $moodelCourses = MoodleCourses::take(500)->get();
        $courseContextId = $this->getCourseContextId(3043);
        return $moodelCourses;
    }
    
    public function createUsersAndEnroll(Request $request){
        // Extract user details and course enrollment data from the request
        $userData = $request->input('user');
        $courseDatas = $request->input('course');

        // Construct payload for creating users
        $userPayload = [
            'wstoken' => 'bb6348e026169d641707e4a2bf2fdc9a',
            'wsfunction' => 'core_user_create_users',
            'moodlewsrestformat' => 'json',
            'users' => [$userData]
            // Include other necessary parameters for creating users
        ];

        // Send request to create users
        $userResponse = Http::post('https://lmmuelearning.lmmu.ac.zm/webservice/rest/server.php', $userPayload);

        if ($userResponse->successful()) {
            // User created successfully, proceed with role assignment and course enrollment
            $user = $userResponse->json()[0]; // Assuming the response contains the created user's details
            $userId = $user['id'];

            foreach($courseDatas as $courseData){
                // Assign role to the user
                $this->assignUserRole($userId, $courseData['courseId']);

                // Enroll user in courses
                $this->enrollUserIntoCourses($userId, $courseData);
            }

            return response()->json(['message' => 'User created, role assigned, and enrolled in course(s) successfully'], 200);
        } else {
            // Error creating user
            $errorMessage = $userResponse->json()['error']['message'];
            return response()->json(['error' => $errorMessage], $userResponse->status());
        }
    }

    public function assignUserRole($userId, $courseId){
        // Get the context ID of the course
        $courseContextId = $this->getCourseContextId($courseId);

        if (!$courseContextId) {
            // Error: Unable to retrieve the context ID of the course
            return response()->json(['error' => 'Unable to retrieve the context ID of the course'], 500);
        }

        // Construct payload for assigning role
        $rolePayload = [
            'wstoken' => 'bb6348e026169d641707e4a2bf2fdc9a',
            'wsfunction' => 'core_role_assign_roles',
            'moodlewsrestformat' => 'json',
            'assignments' => [
                [
                    'roleid' => 5, // Replace with the appropriate role ID (e.g., Student)
                    'userid' => $userId,
                    'contextid' => $courseContextId, // Use the retrieved context ID of the course
                ]
            ]
            // Include other necessary parameters for role assignment
        ];

        // Send request to assign role
        $response = Http::post('https://lmmuelearning.lmmu.ac.zm/webservice/rest/server.php', $rolePayload);

        if ($response->successful()) {
            // Role assigned successfully
            return;
        } else {
            // Error assigning role
            $errorMessage = $response->json()['error']['message'];
            // Handle error appropriately
        }
    }

    public function getCourseContextId($courseId){
        // Construct payload for fetching course contents
        $payload = [
            'wstoken' => 'bb6348e026169d641707e4a2bf2fdc9a',
            'wsfunction' => 'core_course_get_contents',
            'moodlewsrestformat' => 'json',
            'courseid' => $courseId
        ];

        // Send request to fetch course contents
        $response = Http::post('https://lmmuelearning.lmmu.ac.zm/webservice/rest/server.php', $payload);

        if ($response->successful()) {
            // Parse the response to extract the context ID
            $courseContents = $response->json();

            // Assuming the context ID is in the first section
            $firstSection = $courseContents[0];
            $courseContextId = $firstSection['id'];

            return $courseContents;
        } else {
            // Error fetching course contents
            return $response->json();
        }
    }

    public function enrollUserIntoCourses($userId, $courseData){
        // Construct payload for enrolling user in courses
        $enrollmentPayload = [
            'wstoken' => 'bb6348e026169d641707e4a2bf2fdc9a',
            'wsfunction' => 'enrol_manual_enrol_users',
            'moodlewsrestformat' => 'json',
            'enrolments' => [
                [
                    'roleid' => 5, // Replace with the appropriate role ID (e.g., Student)
                    'userid' => $userId,
                    'courseid' => 1, // Replace with the ID of the course to enroll the user in
                ]
            ]
            // Include other necessary parameters for enrolling users into courses
        ];

        // Send request to enroll user in courses
        $response = Http::post('https://lmmuelearning.lmmu.ac.zm/webservice/rest/server.php', $enrollmentPayload);

        if ($response->successful()) {
            // User enrolled in course successfully
            return;
        } else {
            // Error enrolling user in course
            $errorMessage = $response->json()['error']['message'];
            // Handle error appropriately
        }
    }
}
