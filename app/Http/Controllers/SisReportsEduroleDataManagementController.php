<?php

namespace App\Http\Controllers;

use App\Mail\CronJobEmail;
use App\Models\BasicInformationSR;
use App\Models\CourseElectives;
use App\Models\CourseRegistration;
use App\Models\CoursesSR;
use App\Models\MoodleUserEnrolments;
use App\Models\ProgramCourseLink;
use App\Models\ProgramCourseLinksSR;
use App\Models\ProgramSR;
use App\Models\SchoolsSR;
use App\Models\StudentStudyLinkSR;
use App\Models\StudySR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SisReportsEduroleDataManagementController extends Controller
{
    public function importOrUpdateSisReportsEduroleData(){
        set_time_limit(120000000);
        $this->importBasicInformationFromEdurole();
        $this->importCoursesFromEdurole();
        $this->importStudentStudyLinkFromEdurole();
        $this->importProgramCourseLinkFromEdurole();
        $this->importProgrammesFromEdurole();
        $this->importStudyFromEdurole();
        $this->importSchoolsFromEdurole();
        // $studentIds = CourseElectives::pluck('StudentID')
        //                 ->unique()
        //                 ->toArray();
        // $studentIdSisReports = CourseRegistration::pluck('StudentID')
        //                 ->unique()
        //                 ->toArray();
        // $moodleController = new MoodleController();        
        // $moodleController->addStudentsFromEduroleToMoodleAndEnrollInCourses($studentIds); 
        // $moodleController->addStudentsToMoodleAndEnrollInCourses($studentIdSisReports);

        return redirect()->back()->with('success', 'Data imported successfully');
    }

    public function importOrUpdateMoodleWithEduroleData(){

        // return "we are here";
        ini_set('memory_limit', '1024M'); // Increase memory limit
        set_time_limit(12000000);
        // MoodleUserEnrolments::where('timeend', '>', 0)        
        //     ->update(['timeend' => strtotime('2025-12-31')]);
        // Mail::to('ict.lmmu@lmmu.ac.zm')->send(new CronJobEmail());
        $studentIds = CourseElectives::where('course-electives.Year', 2025)
                        ->pluck('StudentID')
                        ->unique()
                        ->toArray();
        // $studentIdSisReports = CourseRegistration::pluck('StudentID')
        //                 ->unique()
        //                 ->toArray();
        $moodleController = new MoodleController();
        // $sisReportsEduroleDataManagementController = new SisReportsEduroleDataManagementController();
        // $sisReportsEduroleDataManagementController->importOrUpdateSisReportsEduroleData();
        
        $moodleController->addStudentsFromEduroleToMoodleAndEnrollInCourses($studentIds); 

        // $studentsController = new StudentsController();
        // $studentsController->importStudentsFromLMMAX();
        // $moodleController->addStudentsToMoodleAndEnrollInCourses($studentIdSisReports);
        // MoodleUserEnrolments::where('timeend', '>', 0)        
        //     ->update(['timeend' => strtotime('2025-12-31')]);

        // Mail::to('ict.lmmu@lmmu.ac.zm')->send(new CronJobEmail());     
        $this->info('Students enrolled successfully.');
        // Log::info('Students enrolled successfully.');
    }
    
    private function importCoursesFromEdurole(){
        set_time_limit(120000000);
        $getCoursesFromSis = $this->getSisCourses();
    
        foreach($getCoursesFromSis as $course){
            CoursesSR::updateOrCreate(
                ['course_id' => intval($course->ID)],
                [
                    'course_name' => $course->Name ?? '',
                    'course_description' => $course->CourseDescription ?? ''
                ]
            );
        }            
    }

    private function importStudentStudyLinkFromEdurole(){
        set_time_limit(120000000);
        $getStudentStudyLinkFromSis = $this->getStudentStudyLink();
        
        foreach($getStudentStudyLinkFromSis as $studentStudyLink){
            StudentStudyLinkSR::updateOrCreate(
                ['ssl_id' => intval($studentStudyLink->ID)],
                [
                    'study_id' => intval($studentStudyLink->StudyID),
                    'student_id' => intval($studentStudyLink->StudentID)
                ]);
        }
    }

    private function importProgramCourseLinkFromEdurole(){
        set_time_limit(120000000);
        $getProgramCourseLinkFromSis = $this->getProgramCourseLink();
        
        foreach($getProgramCourseLinkFromSis as $programCourseLink){
            ProgramCourseLinksSR::updateOrCreate(
                ['pcl_id' => intval($programCourseLink->ID)],
                [
                    'program_id' => intval($programCourseLink->ProgramID),
                    'course_id' => intval($programCourseLink->CourseID)
                ]);
        }
    }

    private function importProgrammesFromEdurole(){
        set_time_limit(120000000);
        $getProgrammesFromSis = $this->getProgrammes();

        foreach($getProgrammesFromSis as $programme){
            ProgramSR::updateOrCreate(
                ['programme_id' => intval($programme->ID)],
                [
                    'program_name' => $programme->ProgramName ?? ''
                ]);
        }
    }

    private function importStudyFromEdurole(){
        set_time_limit(120000000);
        $getStudyFromSis = $this->getStudy();

        foreach($getStudyFromSis as $study){
            StudySR::updateOrCreate(
                ['study_id' => intval($study->ID)],
                [
                    'study_name' => $study->Name ?? '',
                    'study_shortname' => $study->ShortName ?? '',
                    'parent_id' => intval($study->ParentID)
                ]);
        }
    }

    private function importSchoolsFromEdurole(){
        set_time_limit(120000000);
        $getSchoolsFromSis = $this->getSchools();

        foreach($getSchoolsFromSis as $school){
            SchoolsSR::updateOrCreate(
                ['school_id' => intval($school->ID)],
                [
                    'school_name' => $school->Name ?? ''
                ]);
        }
    }

    private function importBasicInformationFromEdurole(){
        set_time_limit(120000000);
        $getBasicInformationFromSis = $this->getBasicInformation();

        foreach($getBasicInformationFromSis as $basicInformation){
            BasicInformationSR::updateOrCreate(
                ['StudentID' => intval($basicInformation->ID)],
                [
                    'FirstName' => $basicInformation->FirstName ?? '',
                    'MiddleName' => $basicInformation->MiddleName ?? '',
                    'Surname' => $basicInformation->Surname ?? '',
                    'Sex' => $basicInformation->Sex ?? '',
                    'StudentID' => intval($basicInformation->ID),
                    'GovernmentID' => $basicInformation->GovernmentID ?? '',
                    'DateOfBirth' => $basicInformation->DateOfBirth ?? '',
                    'PlaceOfBirth' => $basicInformation->PlaceOfBirth ?? '',
                    'Nationality' => $basicInformation->Nationality ?? '',
                    'StreetName' => $basicInformation->StreetName ?? '',
                    'PostalCode' => $basicInformation->PostalCode ?? '',
                    'Town' => $basicInformation->Town ?? '',
                    'Country' => $basicInformation->Country ?? '',
                    'HomePhone' => $basicInformation->HomePhone ?? '',
                    'MobilePhone' => $basicInformation->MobilePhone ?? '',
                    'Disability' => $basicInformation->Disability ?? '',
                    'DisabilityType' => $basicInformation->DissabilityType ?? '',
                    'PrivateEmail' => $basicInformation->PrivateEmail ?? '',
                    'MaritalStatus' => $basicInformation->MaritalStatus ?? '',
                    'StudyType' => $basicInformation->StudyType ?? '',
                    'Status' => $basicInformation->Status ?? ''
                ]);
        }
    }

    // private function 
}
