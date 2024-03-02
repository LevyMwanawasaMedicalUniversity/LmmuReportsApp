<?php

namespace App\Http\Controllers;

use App\Models\BasicInformation;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StudentsController extends Controller
{
    public function importStudentsFromBasicInformation()
    {
        set_time_limit(12000000);
        // Join BasicInformation with GradesPublished and select the student IDs
        $studentIds = $this->getStudentsToImport()->pluck('StudentID')->toArray();

        // Split studentIds into chunks to avoid MySQL placeholder limit
        $studentIdsChunks = array_chunk($studentIds, 1000); // Adjust the chunk size as needed

        // Check if the student IDs already exist in the students table and update their status
        foreach ($studentIdsChunks as $studentIdsChunk) {
            Student::whereIn('student_number', $studentIdsChunk)
                ->where('status', '!=', 4) // Exclude students with status 4
                ->update(['status' => 4]);
        }

        // Insert new students with a status of 4 and create accounts for them
        foreach ($studentIdsChunks as $studentIdsChunk) {
            $studentsToInsert = [];
            foreach ($studentIdsChunk as $studentId) {
                $studentsToInsert[] = [
                    'student_number' => $studentId,
                    'academic_year' => 2024,
                    'term' => 1,
                    'status' => 4
                ];
            }

            // Batch insert new students
            Student::insert($studentsToInsert);
        }

        // Get all existing users
        $existingUsers = User::whereIn('name', $studentIds)->get()->keyBy('name');

        // Create accounts for each student
        foreach ($studentIdsChunks as $studentIdsChunk) {
            foreach ($studentIdsChunk as $studentId) {
                // Check if the student number exists in the students table
                $student = Student::where('student_number', $studentId)->first();
                if ($student) {
                    // If the student number exists, check if a user account exists for the student
                    $user = User::where('name', $studentId)->first();
                    if (!$user) {
                        // If a user account doesn't exist, create it
                        $this->createUserAccount($studentId);
                    }
                } else {
                    // If the student number doesn't exist, insert the student and create a user account
                    Student::create([
                        'student_number' => $studentId,
                        'academic_year' => 2024,
                        'term' => 1,
                        'status' => 4
                    ]);
                    $this->createUserAccount($studentId);
                }
            }
        }
        // Provide a success message
        return redirect()->back()->with('success', 'Students imported successfully and accounts created.');
    }

    private function createUserAccount($studentId)
    {
        // Get the student's email from BasicInformation
        $basicInfo = BasicInformation::find($studentId);
        $email = $basicInfo->PrivateEmail;
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $email = $studentId . $email;
        }
        try {
            $user = User::create([
                'name' => $studentId,
                'email' => $email,
                'password' => '12345678',
            ]);

            // Assign roles and permissions to the user
            $studentRole = Role::firstOrCreate(['name' => 'Student']);
            $studentPermission = Permission::firstOrCreate(['name' => 'Student']);
            $user->assignRole($studentRole);
            $user->givePermissionTo($studentPermission);
        } catch (Exception $e) {
            // Handle any errors during user account creation
        }
    }

    public function viewAllStudents(Request $request){
        $academicYear= 2024;
        $courseName = null;
        $courseId = null;
        if($request->input('student-number')){
            $student = Student::query()
                        ->where('student_number','=', $request->input('student-number'))
                        ->where('status','=', 4)
                        ->first();
            if($student){
                $getStudentNumber = $student->student_number;
                $studentNumbers = [$getStudentNumber];
                $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::where('status', 4)->pluck('student_number')->toArray();
            $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(15);
        }
        return view('allStudents.index', compact('results','courseName','courseId'));
    }
}
