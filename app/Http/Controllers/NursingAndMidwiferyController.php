<?php

namespace App\Http\Controllers;

use App\Models\BasicInformation;
use App\Models\BasicInformationSR;
use App\Models\CourseRegistration;
use App\Models\Courses;
use App\Models\EduroleCourses;
use App\Models\SisCourses;
use App\Models\Student;
use App\Models\StudentStudyLinkSR;
use App\Models\Study;
use App\Models\StudySR;
use App\Models\User;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NursingAndMidwiferyController extends Controller
{
    public function import(){
        return view('nurAndMid.import');
    }

    public function showStudents($studentId){

        $studentStatus= 7;
        $studentInformation = BasicInformationSR::where('StudentID', $studentId)->first();
        // return $studentInformation;

        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
            ->where('Year', 2024)
            ->where('Semester', 1)
            ->exists();
    
        if ($checkRegistration) {
            $checkRegistration = collect($this->getStudentRegistration($studentId));
            $courseIds = $checkRegistration->pluck('CourseID')->toArray();
            
            $checkRegistration = EduroleCourses::query()->whereIn('Name', $courseIds)->get();
            
            
            
            return view('allStudents.registrationPage', compact('studentStatus','studentId','checkRegistration','studentInformation'));
        }
        $student = Student::where('student_number', $studentId)->first();
        if(!$student){
            return back()->with('error', 'Student not found.');
        }
        $groupedCourses = Courses::where('Student', $studentId)->get();
        // return $studentCourses;

        $ssl = StudentStudyLinkSR::where('student_id', $studentId)->get();
        // return $ssl;

        $studentDetails = BasicInformationSR::join('student_study_link_s_r_s as ssl', 'basic_information_s_r_s.StudentID', '=', 'ssl.student_id')
            ->join('study_s_r_s as ss', 'ssl.study_id', '=', 'ss.study_id')
            ->where('basic_information_s_r_s.StudentID', $studentId)
            ->select('basic_information_s_r_s.*','ss.*') // Specify the columns you need
            ->first();
        $programmeCode = $studentDetails->study_shortname;
        $courses = $this->queryAllCoursesAttachedToProgrammeAndYears($programmeCode)->get();

        // Group courses by CodeRegisteredUnder
        $groupedCoursesAll = $courses->groupBy('CodeRegisteredUnder');
        
        // return $groupedCoursesAll;
        // return $studentCourses;
        $studentDetails = $studentDetails->first();
        
        return view('nurAndMid.adminRegisterStudent', compact('student', 'groupedCourses','studentId','groupedCoursesAll','studentDetails'));
    }

    private function queryAllCoursesAttachedToProgrammeAndYears($programmeName){
        $results = SisCourses::select(
            'courses.ID',
            'courses.Name as CourseCode',
            'courses.CourseDescription as CourseName',
            'study.Name as Programme',
            'schools.Name as School',
            'programmes.ProgramName as CodeRegisteredUnder',
            DB::raw("
                CASE
                    WHEN programmes.ProgramName LIKE '%y1' THEN 'YEAR 1'
                    WHEN programmes.ProgramName LIKE '%y2' THEN 'YEAR 2'
                    WHEN programmes.ProgramName LIKE '%y3' THEN 'YEAR 3'
                    WHEN programmes.ProgramName LIKE '%y4' THEN 'YEAR 4'
                    WHEN programmes.ProgramName LIKE '%y5' THEN 'YEAR 5'
                    WHEN programmes.ProgramName LIKE '%y6' THEN 'YEAR 6'
                    WHEN programmes.ProgramName LIKE '%y8' THEN 'YEAR 1'
                    WHEN programmes.ProgramName LIKE '%y9' THEN 'YEAR 2'
                END AS YearOfStudy
            "),
            DB::raw("
                CASE
                    WHEN programmes.ProgramName LIKE '%-DE-%' THEN 'DISTANCE'
                    WHEN programmes.ProgramName LIKE '%-FT-%' THEN 'FULLTIME'
                END as StudyMode
            ")
        )
        ->join('program-course-link', 'courses.ID', '=', 'program-course-link.CourseID')
        ->join('programmes', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->join('study-program-link', 'programmes.ID', '=', 'study-program-link.ProgramID')
        ->join('study', 'study-program-link.StudyID', '=', 'study.ID')
        ->join('schools', 'study.ParentID', '=', 'schools.ID')
        ->join('student-study-link', 'study.ID', '=', 'student-study-link.StudyID')
        ->where('study.ShortName', $programmeName);

        return $results;
    }

    public function viewStudents(Request $request){
        $academicYear= 2024;
        $courseName = null;
        $courseId = null;
        if($request->input('student-number')){
            $students = Student::query()
                        ->where('student_number', 'like', '%' . $request->input('student-number') . '%')
                        ->where('status','=', 7)
                        ->get();
            if($students){
                $studentNumbers = $students->pluck('student_number')->toArray();
                $results = $this->getAppealStudentDetails($academicYear, $studentNumbers)->paginate(30);
            }else{
                return back()->with('error', 'NOT FOUND.');               
            }
        }else{
            $studentNumbers = Student::where('status', 7)->pluck('student_number')->toArray();
            $results = $this->getAppealStudentDetailsNurAndMid($academicYear, $studentNumbers)->paginate(30);
        }
        // return $results;
        return view('nurAndMid.viewStudents', compact('results', 'courseName', 'courseId'));
    }
    
    public function uploadStudents(Request $request) {
        set_time_limit(1200000);
        // return "We here";
    
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'programme' => 'required',
        ]);
    
        // Get the academic year, term, and programme from the form
        $academicYear = $request->input('academicYear');
        $term = 1;
        $status = 7;
        $programme = $request->input('programme');
    
        // Find the study_id from the StudySR model
        $study = StudySR::where('study_shortname', $programme)->first();
        if (!$study) {
            return redirect()->back()->with('error', 'Programme not found.');
        }
        $studyId = $study->study_id;
    
        // Process the uploaded file
        if ($request->hasFile('excelFile')) {
            $file = $request->file('excelFile');
    
            // Initialize the Box/Spout reader
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());
    
            $isHeaderRow = true; // Flag to identify the header row
            $studentData = [];
    
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    if ($isHeaderRow) {
                        $isHeaderRow = false;
                        continue;
                    }
    
                    $nrcNumber = $row->getCellAtIndex(0)->getValue();
                    $firstName = $row->getCellAtIndex(1)->getValue();
                    $middleName = $row->getCellAtIndex(2)->getValue();
                    $surname = $row->getCellAtIndex(3)->getValue();
                    $sex = $row->getCellAtIndex(4)->getValue();
                    $studymode = $row->getCellAtIndex(5)->getValue();
    
                    $studentData[] = [
                        'nrcNumber' => $nrcNumber,
                        'firstName' => $firstName,
                        'middleName' => $middleName,
                        'surname' => $surname,
                        'sex' => $sex,
                        'studymode' => $studymode
                    ];
                }
            }
    
            $reader->close();
    
            // Split studentData into chunks
            $chunkSize = 10; // Adjust this as needed
            $chunks = array_chunk($studentData, $chunkSize);
    
            // Generate the student ID sequence
            $latestStudent = Student::orderBy('student_number', 'desc')->first();
            $nextStudentNumber = 240300301;
    
            try {
                // Process each chunk
                foreach ($chunks as $chunk) {
                    foreach ($chunk as $data) {
                        // Check if the GovernmentID already exists in BasicInformationSR
                        $existingRecord = BasicInformationSR::where('GovernmentID', $data['nrcNumber'])->first();
                        if ($existingRecord) {
                            $existingStudent = Student::where('student_number', $existingRecord->StudentID)->first();
                            if ($existingStudent) {
                                // Ensure the status is 7
                                if ($existingStudent->status != 7) {
                                    $existingStudent->update(['status' => 7]);
                                }
                            } else {
                                // Create new student with status 7
                                Student::create([
                                    'student_number' => $existingRecord->StudentID,
                                    'academic_year' => $academicYear,
                                    'term' => $term,
                                    'status' => $status
                                ]);
                            }
    
                            $studentNumber = $existingRecord->StudentID;
                        } else {
                            // New student and BasicInformationSR record
                            $studentNumber = $nextStudentNumber++;
                            Student::create([
                                'student_number' => $studentNumber,
                                'academic_year' => $academicYear,
                                'term' => $term,
                                'status' => $status
                            ]);
                            BasicInformationSR::create([
                                'FirstName' => $data['firstName'],
                                'MiddleName' => $data['middleName'],
                                'Surname' => $data['surname'],
                                'Sex' => $data['sex'],
                                'StudentID' => $studentNumber,
                                'DateOfBirth' => '2000-01-01',
                                'PlaceOfBirth' => 'Null',
                                'Nationality' => 'Zambian',
                                'PostalCode' => 'Null',
                                'StreetName' => 'Null',
                                'Town' => 'Null',
                                'Country' => 'Zambia',
                                'HomePhone' => 'Null',
                                'MobilePhone' => 'Null',
                                'Disability' => 'Null',
                                'DisabilityType' => 'Null',
                                'MaritalStatus' => 'Null',
                                'Status' => 'Null',
                                'GovernmentID' => $data['nrcNumber'],
                                'PrivateEmail' => $studentNumber . '@lmmu.ac.zm',
                                'StudyType' => $data['studymode'],
                            ]);
                            $lastSslId = StudentStudyLinkSR::max('ssl_id');
                            $newSslId = $lastSslId ? $lastSslId + 1 : 1; // Start from 1 if table is empty

                            StudentStudyLinkSR::create([
                                'ssl_id' => $newSslId,
                                'student_id' => $studentNumber,
                                'study_id' => $studyId
                            ]);
                        }
    
                        // Create a user account if it doesn't exist
                        $existingUser = User::where('email', $studentNumber . '@lmmu.ac.zm')->first();
                        if (!$existingUser) {
                            $user = User::create([
                                'name' => $studentNumber,
                                'email' => $studentNumber . '@lmmu.ac.zm',
                                'password' => bcrypt('12345678'),
                            ]);
    
                            // Create the "Student" role if it doesn't exist
                            $studentRole = Role::firstOrCreate(['name' => 'Student']);
                            $user->assignRole($studentRole);
    
                            // Find or create the "Student" permission
                            $studentPermission = Permission::firstOrCreate(['name' => 'Student']);
                            $user->givePermissionTo($studentPermission);
                        }
    
                        // Set and save courses for the student
                        $this->setAndSaveCoursesForNurAndMid($programme, $studentNumber);
                    }
                }
    
                // Provide a success message
                return redirect()->back()->with('success', 'Students imported successfully and the dockets have been sent.');
            } catch (\Throwable $e) {
                // Handle any unexpected errors during import
                return redirect()->back()->with('error', 'Failed to upload students: ' . $e->getMessage());
            }
        }
    
        // Handle errors or validation failures
        return redirect()->back()->with('error', 'Failed to upload students.');
    }
    
    private function setAndSaveCoursesForNurAndMid($programmeName, $studentId) {
        // Drop existing courses for the student
        Courses::where('Student', $studentId)->delete();
    
        $coursesForStudent = Study::where('study.ShortName', $programmeName)
            ->join('study-program-link', 'study.ID', '=', 'study-program-link.StudyID')
            ->join('programmes', 'study-program-link.ProgramID', '=', 'programmes.ID')
            ->join('program-course-link', 'programmes.ID', '=', 'program-course-link.ProgramID')
            ->join('courses', 'program-course-link.CourseID', '=', 'courses.ID')
            ->where('programmes.Year', 1)
            ->select('courses.Name')
            ->get();
    
        foreach ($coursesForStudent as $course) {
            Courses::create([
                'Student' => $studentId,
                'Program' => $programmeName,
                'Course' => $course->Name,
                'Grade' => '',
            ]);
        }
    }
    
    
    
    
    
}
