<?php

namespace App\Http\Controllers;

use App\Models\MoodelUsers;
use App\Models\MoodleCourses;
use App\Models\MoodleEnroll;
use App\Models\MoodleRoleAssignments;
use App\Models\MoodleUserEnrolments;
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

    public function createUserAccountIfDoesNotExist($student){
        $user = MoodleUsers::where('username', $student->student_number)->first();
        if($user){
            return $user;
        }else{
            $user = new MoodleUsers();
            $user->confirmed = 1;
            $user->policyagreed = 0;
            $user->deleted = 0;
            $user->suspended = 0;
            $user->mnethostid = 1;
            $user->username = $student->ID;
            // $user->password = bcrypt($student->student_number);
            $user->idnumber = $student->ID;            
            $user->firstname = $student->FirstName;
            $user->lastname = $student->Surname;
            $user->email = $student->PrivateEmail;
            $user->emailstop = 0;
            $user->calenderType = 'gregorian';
            $user->auth = 'db';
            $user->confirmed = 1;
            $user->mnethostid = 1;
            $user->lang = 'en';
            $user->timemodified = time();
            $user->timecreated = time();
            $user->save();
            return $user;
        }
    }

    public function assignUserToRoleIfNotAssigned($studentNumber, $courses){
        $role = 5;
        $user = MoodleUsers::where('username', $studentNumber)->first();
        $userId = $user->id;
        $userRole = MoodleRoleAssignments::where('userid', $userId)->first();
        if($userRole){
            return $userRole;
        }else{            
            foreach($courses as $course){
                $course = MoodleCourses::where('idnumber', $course->CourseID)->first();
                $courseId = $course->id;
                $userRole = new  MoodleRoleAssignments();
                $userRole->userid = $userId;
                $userRole->roleid = $role;
                $userRole->contextid = $courseId;
                $userRole->timemodified = time();
                $userRole->timecreated = time();
                $userRole->save();
                return $userRole;
            }
        }
    }


    public function enrollUserIntoCourses($studentNumber, $courses){
        $user = MoodleUsers::where('username', $studentNumber)->first();
        $userId = $user->id;
        foreach($courses as $course){
            $getEnrolId = MoodleEnroll::where('idnumber', $course->CourseID)->first();
            $enrolId = $getEnrolId->id;
            $enrolment = new MoodleUserEnrolments();
            $enrolment->enrolid = $enrolId;
            $enrolment->userid = $userId;
            $enrolment->timestart = time();
            $enrolment->timeend = mktime(0, 0, 0, 12, 30, date("Y"));
            $enrolment->modifierid = time();
            $enrolment->timecreated = time();
            $enrolment->timemodified = time();
            $enrolment->save();
        }
    }   
}
