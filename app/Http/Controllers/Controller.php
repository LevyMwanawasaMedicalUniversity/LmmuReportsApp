<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Mail\DefSupDocket;
use App\Mail\NotificationEmail;
use App\Mail\SendAnEmail;
use App\Mail\SendMailNmcz;
use App\Models\AllCourses;
use App\Models\BasicInformation;
use App\Models\BasicInformationSR;
use App\Models\CourseElectives;
use App\Models\CourseRegistration;
use App\Models\Courses;
use App\Models\EduroleCourses;
use App\Models\Grade;
use App\Models\Grades;
use App\Models\GradesModified;
use App\Models\GradesPublished;
use App\Models\LMMAXCourseAssessmentScores;
use App\Models\Program;
use App\Models\ProgramCourseLink;
use App\Models\SageClient;
use App\Models\SageInvoice;
use App\Models\SagePostAR;
use App\Models\Schools;
use App\Models\SchoolsSR;
use App\Models\SisCourses;
use App\Models\SisReportsSageInvoices;
use App\Models\Student;
use App\Models\StudentStudyLink;
use App\Models\Study;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
// use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function exportData($headers, $rowData, $results, $filename)
    {
        $filePath = storage_path('app/' . $filename);

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        $headerRow = WriterEntityFactory::createRowFromArray($headers);
        $writer->addRow($headerRow);

        foreach ($results as $result) {
            $data = [];
            foreach ($rowData as $field) {
                $data[] = $result->$field;
            }

            $dataRow = WriterEntityFactory::createRowFromArray($data);
            $writer->addRow($dataRow);
        }
        $writer->close();
        return response()->download($filePath, $filename . '.xlsx')->deleteFileAfterSend();
    }

    public function getAllProgrammesPerSchool($schoolName){
        $results = $this->queryAllProgrammesPerSchool($schoolName);        
        return $results;
    }

    public function getAllStudentsRegisteredInASpecificAcademicYear($academicYear){
        $results = $this->queryAllStudentsRegisteredInASpecificAcademicYear($academicYear);
        return $results;            
    }

    public function getStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName){
        $results = $this->queryStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName);
        return $results;            
    }

    public function getRegisteredStudentsFromSpecificIntakeYearTakingAProgramme($intakeName, $programmeName){
        $results = $this->queryRegisteredStudentsFromSpecificIntakeYearTakingAProgramme($intakeName, $programmeName);
        return $results;
    }

    public function getStudentRegistrationFromEdurole($studentId){
        $checkRegistration = CourseElectives::select('courses.Name')
                ->join('courses', 'course-electives.CourseID', '=', 'courses.ID')
                ->where('course-electives.StudentID', $studentId)
                ->where(function ($query) {
                    $query->where('course-electives.Year', 2024)
                            ->orWhereNull('course-electives.Year');
                })
                ->get();
        return $checkRegistration;
    }

    public function getStudentRegistration($studentId){
        $checkRegistration = CourseRegistration::where('StudentID', $studentId)
                ->where('Year', 2024)
                // ->where('Semester', 1)
                ->get();
        return $checkRegistration;
    }

    public function setAndSaveTheCourses($studentId) {
        $dataArray = $this->getCoursesForFailedStudents($studentId);
    
        if (!$dataArray) {
            $dataArray = $this->findUnregisteredStudentCourses($studentId);
        }
    
        if (empty($dataArray)) {
            return; // No data to insert, so exit early
        }
    
        $studentCourses = Courses::where('Student', $studentId)
            ->whereIn('Course', array_column($dataArray, 'Course'))
            ->get()
            ->pluck('Course')
            ->toArray();
    
        $coursesToInsert = [];
    
        foreach ($dataArray as $item) {
            $course = $item['Course'];
    
            if (!in_array($course, $studentCourses)) {
                $coursesToInsert[] = [
                    'Student' => $item['Student'],
                    'Program' => $item['Program'],
                    'Course' => $course,
                    'Grade' => $item['Grade'],
                ];
    
                // Update the list of existing courses for the student
                $studentCourses[] = $course;
            }
        }
    
        if (!empty($coursesToInsert)) {
            // Batch insert the new courses
            Courses::insert($coursesToInsert);
        }
    }

    public function getRegisteredStudentsPerYearInYearOfStudy($yearOfStudy, $academicYear){
        $query = $this->queryRegisteredStudentsPerYearInYearOfStudy($academicYear);        
        $results = $query->where('programmes.ProgramName', 'LIKE', '%' .$yearOfStudy)
                    ->groupBy('basic-information.ID');
        return $results;
    }

    public function getRegisteredStudentsAccordingToProgrammeAndYearOfStudy($academicYear, $yearOfStudy,$programmeName){
        $results = $this->queryRegisteredStudentsAccordingToProgrammeAndYearOfStudy($academicYear, $yearOfStudy,$programmeName);
        return $results;
    }

    public function getRegisteredAndUnregisteredPerYear($academicYear){
        $results= $this->queryRegisteredAndUnregisteredPerYear($academicYear);
        return $results;
    }

    public function getAppealStudentDetails($academicYear,$studentNumbers){
        $results= $this->queryAppealStudentDetails($academicYear,$studentNumbers);
        return $results;
    }

    public function getAppealStudentDetailsNurAndMid($academicYear,$studentNumbers){
        $results= $this->queryAppealStudentDetailsNurAndMid($academicYear,$studentNumbers);
        return $results;
    }

    public function getAppealNMCZStudentDetails($academicYear,$studentNumbers){
        $results= $this->queryAppealNMCZStudentDetails($academicYear,$studentNumbers);
        return $results;
    }

    public function getStudent2023ExamResults($studentNumber,$academicYear){
        $results = $this->queryStudentResults($studentNumber,$academicYear);
        return $results;
    }

    public function getAllStudentExamResults($studentNumber){
        $results = $this->queryAllStudentResults($studentNumber);
        return $results;
    }

    private function queryAllStudentResults($studentNumber){
        $results = Grade::select('StudentNo', 'ProgramNo', 'CourseNo', 'Grade','AcademicYear')
            ->where('StudentNo', $studentNumber)
            
            ->get();
    
        return $results;
    }

    private function queryStudentResults($studentNumber, $academicYear){
        $results = Grade::select('StudentNo', 'ProgramNo', 'CourseNo', 'Grade')
            ->where('StudentNo', $studentNumber)
            ->where('AcademicYear', $academicYear)
            ->get();
    
        return $results;
    }

    public function getCoursesWithResults(){
        $results = $this->queryCoursesWithResults();
        return $results;
    }

    public function getExamModificationAuditTrail(){
        $results = $this->queryExamModificationAuditTrail();
        return $results;
    }

    private function queryExamModificationAuditTrail(){
        $gradesModified = GradesModified::select([
                'grade-modified.ID',
                'grade-modified.GradeID',
                'grade-modified.StudentID',
                'grade-modified.CID',
                'grade-modified.CA',
                'grade-modified.Exam',
                'grade-modified.Total',
                'grade-modified.Grade',
                'grade-modified.DateTime',
                'bi.FirstName as SubmittedByFirstName',
                'bi.Surname as SubmittedBySurname',
                'bi2.FirstName as ReviewedByFirstName',
                'bi2.Surname as ReviewedBySurname',
                'bi3.FirstName as ApprovedByFirstName',
                'bi3.Surname as ApprovedBySurname',
                'grade-modified.Type'
            ])
            ->join('basic-information as bi', 'bi.ID', '=', 'grade-modified.SubmittedBy')
            ->join('basic-information as bi2', 'bi2.ID', '=', 'grade-modified.ReviewedBy')
            ->join('basic-information as bi3', 'bi3.ID', '=', 'grade-modified.ApprovedBy')
            ->orderBy('grade-modified.DateTime', 'desc');

        return $gradesModified;
    }
    
    private function queryCoursesWithResults(){
        $results = SisCourses::select(
            'courses.Name AS CourseCode',
            'courses.CourseDescription AS CourseName',
            'p.ProgramName',
            's.Name AS Programme',
            's2.Name AS School',
            'bi.FirstName',
            'bi.Surname',
            DB::raw("(CASE WHEN COUNT(CASE WHEN g.AcademicYear = 2023 THEN g.CAMarks END) = 0 THEN 'No Results' ELSE 'Results Found' END) AS UnpublishedResults")
        )
        ->leftJoin('grades as g', function ($join) {
            $join->on('courses.Name', '=', 'g.CourseNo')
                ->where('g.AcademicYear', '=', 2023);
        })
        ->join('program-course-link as pcl', 'courses.ID', '=', 'pcl.CourseID')
        ->join('programmes as p', 'pcl.ProgramID', '=', 'p.ID')
        ->join('study-program-link as spl', 'p.ID', '=', 'spl.ProgramID')
        ->join('study as s', 'spl.StudyID', '=', 's.ID')
        ->join('schools as s2', 's.ParentID', '=', 's2.ID')
        ->join('basic-information as bi', 'bi.ID', '=', 's.ProgrammesAvailable')
        ->groupBy('p.ProgramName', 'courses.Name');
    
        return $results;
    }

    public function getStudentsToImport(){
        $results = $this->queryStudentsToImport();
        return $results;
    }

    public function getStudentsFromLMMAX(){
        $results = LMMAXCourseAssessmentScores::distinct('student_id')
            ->get()
            ->map(function ($item) {
                $item->student_id = (string) $item->student_id;
                return $item;
            });
        return $results;
    }

    public function getStudentsToImportToExportToCsv(){
        $results = $this->queryStudentsToImportToExportToCsv();
        return $results;
    }

    private function queryStudentsToImportToExportToCsv() {
        $results = BasicInformation::select(
            'basic-information.FirstName',
            'basic-information.MiddleName',
            'basic-information.Surname',
            'basic-information.Sex',
            'basic-information.ID AS StudentID',
            'basic-information.GovernmentID',
            'basic-information.PrivateEmail',
            'basic-information.MobilePhone',
            'applicants.Progress' // Include the Progress column from applicants table
        )
        ->leftJoin('grades-published', 'grades-published.StudentNo', '=', 'basic-information.ID')
        ->leftJoin('applicants', 'applicants.StudentID', '=', 'basic-information.ID') // Left join applicants table
        ->where(function($query) {
            $query->whereRaw('LENGTH(`basic-information`.`ID`) > 7')
                ->where('grades-published.AcademicYear', 2023)
                ->where('basic-information.StudyType', '!=', 'Staff')
                ->groupBy('basic-information.ID');
        })
        // ->orWhere(function($query) {
        //     $query->where('basic-information.ID', 'like', '240%')
        //         ->where('applicants.Progress', 'Accepted');
        // })
        ->groupBy('basic-information.ID'); // Group by StudentID to ensure uniqueness
        
        return $results;
    }

    private function queryStudentsToImport() {
        $results = BasicInformation::select(
            'basic-information.FirstName',
            'basic-information.MiddleName',
            'basic-information.Surname',
            'basic-information.Sex',
            'basic-information.ID AS StudentID',
            'basic-information.GovernmentID',
            'basic-information.PrivateEmail',
            'basic-information.MobilePhone',
            'applicants.Progress' // Include the Progress column from applicants table
        )
        ->leftJoin('grades-published', 'grades-published.StudentNo', '=', 'basic-information.ID')
        ->leftJoin('applicants', 'applicants.StudentID', '=', 'basic-information.ID') // Left join applicants table
        ->where(function($query) {
            $query->whereRaw('LENGTH(`basic-information`.`ID`) > 7')
                ->where('grades-published.AcademicYear', 2023)
                ->where('basic-information.StudyType', '!=', 'Staff')
                ->groupBy('basic-information.ID');
        })
        // ->orWhere(function($query) {
        //     $query->where('basic-information.ID', 'like', '240%')
        //         ->where('applicants.Progress', 'Accepted');
        // })
        ->groupBy('basic-information.ID'); // Group by StudentID to ensure uniqueness
        
        return $results;
    }

    private function queryAppealNMCZStudentDetails($academicYear, $studentNumbers) {
        $results = Schools::select(
            'basic-information.FirstName',
            'basic-information.MiddleName',
            'basic-information.Surname',
            'basic-information.Sex',
            'student-study-link.StudentID',
            'basic-information.GovernmentID',
            'basic-information.PrivateEmail',
            'basic-information.MobilePhone',
            'programmes.ProgramName AS "Programme Code"',
            'study.Name',
            'schools.Description',
            'basic-information.StudyType',
            'balances.Amount',
            DB::raw("CASE
                WHEN `course-electives`.StudentID IS NOT NULL THEN 'REGISTERED'
                ELSE 'NO REGISTRATION'
            END AS `RegistrationStatus`"),
            DB::raw("CASE 
                WHEN programmes.ProgramName LIKE '%1' THEN 'YEAR 1'
                WHEN programmes.ProgramName LIKE '%2' THEN 'YEAR 2'
                WHEN programmes.ProgramName LIKE '%3' THEN 'YEAR 3'
                WHEN programmes.ProgramName LIKE '%4' THEN 'YEAR 4'
                WHEN programmes.ProgramName LIKE '%5' THEN 'YEAR 5'
                WHEN programmes.ProgramName LIKE '%6' THEN 'YEAR 6'
                WHEN programmes.ProgramName IS NULL THEN 'NO REGISTRATION'
                ELSE 'NO REGISTRATION'
            END AS `YearOfStudy`")
        )
        ->leftJoin('study', 'schools.ID', '=', 'study.ParentID')
        ->leftJoin('student-study-link', 'study.ID', '=', 'student-study-link.StudyID')
        ->leftJoin('course-electives', function ($join) use ($academicYear) {
            $join->on('student-study-link.StudentID', '=', 'course-electives.StudentID')
                ->where('course-electives.Year', '=', $academicYear);
        })
        ->leftJoin('courses', 'course-electives.CourseID', '=', 'courses.ID')
        ->leftJoin('program-course-link', 'courses.ID', '=', 'program-course-link.CourseID')
        ->leftJoin('programmes', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->leftJoin('basic-information', 'student-study-link.StudentID', '=', 'basic-information.ID')
        ->leftJoin('balances','balances.StudentID','=','basic-information.ID')
        ->whereRaw('LENGTH(`basic-information`.`ID`) > 7')
        ->where(function ($query) use ($studentNumbers) {
            $query->where('basic-information.StudyType', '=', 'Fulltime')
                ->orWhere('basic-information.StudyType', '=', 'Distance');
        })
        ->where('basic-information.StudyType', '!=', 'Staff')
        ->whereIn('student-study-link.StudentID', $studentNumbers)
        ->groupBy('student-study-link.StudentID');
    
        return $results;
    }

    
    private function queryAppealStudentDetailsNurAndMid($academicYear, $studentNumbers) {
        $results = BasicInformationSR::select(
            'basic_information_s_r_s.FirstName',
            'basic_information_s_r_s.MiddleName',
            'basic_information_s_r_s.Surname',
            'basic_information_s_r_s.Sex',
            'basic_information_s_r_s.StudentID',
            // 'student_study_link_s_r_s.student_id AS StudentID',
            'basic_information_s_r_s.GovernmentID',
            'basic_information_s_r_s.PrivateEmail',
            'basic_information_s_r_s.MobilePhone',
            // 'program_s_r_s.program_name AS "Programme Code"',
            // 'study.study_shortname AS ShortName',
            // 'study.study_name as Name',
            // 'schools_s_r_s.school_name AS Description',
            'basic_information_s_r_s.StudyType'
        )
        ->whereIn('basic_information_s_r_s.StudentID', $studentNumbers);
    
        return $results;
    }
    

    private function queryAppealStudentDetails($academicYear, $studentNumbers) {
        $results = Schools::select(
            'basic-information.FirstName',
            'basic-information.MiddleName',
            'basic-information.Surname',
            'basic-information.Sex',
            'student-study-link.StudentID',
            'basic-information.GovernmentID',
            'basic-information.PrivateEmail',
            'basic-information.MobilePhone',
            'programmes.ProgramName AS "Programme Code"',
            'study.ShortName',
            'study.Name',
            'programmes.Year',
            'schools.Description',
            'basic-information.StudyType',
            'balances.Amount',
            DB::raw("CASE
                WHEN `course-electives`.StudentID IS NOT NULL THEN 'REGISTERED'
                ELSE 'NO REGISTRATION'
            END AS `RegistrationStatus`"),
            DB::raw("CASE 
                WHEN programmes.ProgramName LIKE '%1' THEN 'YEAR 1'
                WHEN programmes.ProgramName LIKE '%2' THEN 'YEAR 2'
                WHEN programmes.ProgramName LIKE '%3' THEN 'YEAR 3'
                WHEN programmes.ProgramName LIKE '%4' THEN 'YEAR 4'
                WHEN programmes.ProgramName LIKE '%5' THEN 'YEAR 5'
                WHEN programmes.ProgramName LIKE '%6' THEN 'YEAR 6'                
                WHEN programmes.ProgramName LIKE '%8' THEN 'YEAR 1'
                WHEN programmes.ProgramName LIKE '%9' THEN 'YEAR 2'
                WHEN programmes.ProgramName IS NULL THEN 'NO REGISTRATION'
                ELSE 'NO REGISTRATION'
            END AS `YearOfStudy`")
        )
        ->leftJoin('study', 'schools.ID', '=', 'study.ParentID')
        ->leftJoin('student-study-link', 'study.ID', '=', 'student-study-link.StudyID')
        ->leftJoin('course-electives', function ($join) use ($academicYear) {
            $join->on('student-study-link.StudentID', '=', 'course-electives.StudentID')
                ->where('course-electives.Year', '=', $academicYear);
        })
        ->leftJoin('courses', 'course-electives.CourseID', '=', 'courses.ID')
        ->leftJoin('program-course-link', 'courses.ID', '=', 'program-course-link.CourseID')
        ->leftJoin('programmes', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->leftJoin('basic-information', 'student-study-link.StudentID', '=', 'basic-information.ID')
        ->leftJoin('balances','balances.StudentID','=','basic-information.ID')
        ->whereRaw('LENGTH(`basic-information`.`ID`) > 7')
        ->where(function ($query) use ($studentNumbers) {
            $query->where('basic-information.StudyType', '=', 'Fulltime')
                ->orWhere('basic-information.StudyType', '=', 'Distance');
        })
        ->where('basic-information.StudyType', '!=', 'Staff')
        ->whereIn('student-study-link.StudentID', $studentNumbers)
        ->groupBy('student-study-link.StudentID');
    
        return $results;
    }

    public function getCoursesForFailedStudentsForCurrentAcademicYear($studentId) {
        $failedCourses = [];
        
        $academicYear = 2023;

        $results = Grade::select('StudentNo', 'ProgramNo', 'CourseNo', 'Grade')
            ->where('StudentNo', $studentId)
            ->where('AcademicYear', $academicYear)
            ->whereIn('Grade', ['D+','NE','DEF'])
            ->get();
        
        foreach ($results as $row) {
            $failedCourses[] = [
                'Student' => $row->StudentNo,
                'Program' => $row->ProgramNo, // Replace with the program
                'Course' => $row->CourseNo,
                'Grade' => $row->Grade,
            ];
        }
    
        return $failedCourses;
    }

    public function getCoursesForFailedStudents($studentId) {
        $failedCourses = [];
    
        // Retrieve failed students' grades
        $failedStudents = Grade::select('StudentNo', 'ProgramNo', 'CourseNo', 'Grade')
            ->whereNotIn('AcademicYear', ['2024'])
            ->whereIn('StudentNo', function ($query) {
                $query->select('StudentNo')
                    ->from('grades')
                    ->whereNotIn('Grade', ['A+', 'A', 'B+', 'B', 'C+', 'C', 'P', 'EX']);
            })
            ->where('StudentNo', $studentId)
            ->orderBy('StudentNo')
            ->get();
    
        // Loop through failed students' grades
        foreach ($failedStudents as $row) {
            $student = $row->StudentNo;
            $program = $row->ProgramNo;
            $course = $row->CourseNo;
            $grade = $row->Grade;
    
            // Count the number of repeated instances of the course for the student in previous years
            $repeatedCourses = Grade::where('CourseNo', $course)
                ->where('StudentNo', $student)
                ->where('AcademicYear', '<', '2024') // Consider only previous academic years
                ->get();
    
            $duplicateCount = count($repeatedCourses);
    
            // If the course is repeated
            if ($duplicateCount > 1) {
                // Check if the course has been cleared in previous years
                $cleared = Grade::where('StudentNo', $student)
                    ->where('CourseNo', $course)
                    ->where('AcademicYear', '<', '2024') // Consider only previous academic years
                    ->whereIn('Grade', ['A+', 'A', 'B+', 'B', 'C+', 'C', 'P', 'EX'])
                    ->orderBy('Grade')
                    ->get();
    
                $ifCleared = count($cleared);
    
                if ($ifCleared === 0) {
                    // If the course hasn't been cleared, add it to the failed courses list
                    $failedCourses[] = [
                        'Student' => $student,
                        'Program' => $program, // Use program from original failed instance
                        'Course' => $course,
                        'Grade' => $grade,
                    ];
                }
            } else {
                // If the course is not repeated, check if the grade is failing and add it to the failed courses list
                if (!in_array($grade, ['A+', 'A', 'B+', 'B', 'C+', 'C', 'P', 'EX'])) {
                    $failedCourses[] = [
                        'Student' => $student,
                        'Program' => $program, // Use program from original failed instance
                        'Course' => $course,
                        'Grade' => $grade,
                    ];
                }
            }
        }
        $coursesArray = [];
        if (!empty($failedCourses)) {
            foreach ($failedCourses as $failedCourse) {
                $coursesArray[] = $failedCourse['Course'];
            }
        }
        $coursesNamesArray = [];
        if (!empty($failedCourses)) {
            foreach ($failedCourses as $failedCourse) {
                $coursesNamesArray[] = $failedCourse['Program'];
            }
        }

        $studentsProgramme = $this->getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId, $coursesArray)->get();
        if($studentsProgramme->isEmpty()){
            $studentsProgramme = $this->getAllCoursesAttachedToProgrammeNamesForAStudentBasedOnCourses($studentId, $coursesNamesArray)->get();
        }
        if($studentsProgramme->isEmpty()){
            $failedCourses = [];
        }
    
        return $failedCourses;
    }

    public function getDefferedOrSuplementaryCourses(){
        $results = $this->queryDefferedOrSuplementaryCourses();
        return $results;
    }

    private function queryDefferedOrSuplementaryCourses(){
        $courses = SisCourses::select('courses.ID','courses.Name','courses.CourseDescription')
            ->join('grades-published', 'Name', '=', 'CourseNo')
            ->whereIn('Grade', ['D+', 'NE','DEF'])
            ->where('AcademicYear', 2023)
            ->groupBy('Name')
            ->get();

        $studentCourses = Courses::pluck('Course'); 
        $results = $courses->whereIn('Name', $studentCourses);

        return $results;
    }

    public function sendEmailNotification($studentID) {               

        $privateEmail = BasicInformation::find($studentID);
    
        if ($privateEmail) {
            $email = trim($privateEmail->PrivateEmail);
        } else {
            // Handle the case where there's no BasicInformation record with the provided $studentID
            // For example, you might want to log an error message and return
            Log::error("No BasicInformation record found for student ID: $studentID");
            return "No BasicInformation record found for student ID: $studentID";
        }
    
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // $email is a valid email address
            $sendingEmail = $email;
        } else {
            // $email is not a valid email address
            $sendingEmail = 'azwel.simwinga@lmmu.ac.zm';
        }
        // Mail::to($sendingEmail)->send(new NotificationEmail($studentID));        
    
        return "Test email sent successfully!";
    }

    public function sendTestEmail($studentID) {
        $studentResults = $this->getStudentResults($studentID);
        $student = Student::where('student_number', $studentID)->first();
        $status = $student->status;
        $view = '';
        $pdfPath = null;
    
        switch ($status) {
            case 1:
                $courses = $this->getStudentCourses($studentID);
                $view = 'emails.pdf';
                break;
            case 2:
                $courses = $this->getStudentCourses($studentID);
                $view = 'emails.pdf';
                break;
            case 4:
                $courses = $this->getStudentCourses($studentID);
                $view = 'emails.pdf';
                break;
            case 3:
                $courses = Courses::where('Student', $studentID)->get();
                $view = 'emails.pdfSudAndDef';
                $pdf = PDF::loadView($view, compact('studentResults', 'courses'));
                $fileName = $studentID . '.pdf';
                $pdfPath = storage_path('app/' . $fileName);
                $pdf->save($pdfPath);
                break;
        }
    
        $privateEmail = BasicInformation::find($studentID);
        $email = trim($privateEmail->PrivateEmail);
        $sendingEmail = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'azwel.simwinga@lmmu.ac.zm';
    
        $mailClass = $status == 3 ? new DefSupDocket($pdfPath,$studentID) : new SendAnEmail($studentID);
    
        // Dispatch the email sending job to the queue
        dispatch(new SendEmailJob($sendingEmail, $mailClass));
    
        if ($pdfPath) {
            unlink($pdfPath);
        }
    
        return "Test email sent successfully!";
    }

    public function checkIfStudentIsRegistered($studentId) {
        $results = $this->queryIfStudentIsRegistered($studentId);
        return $results;
    }

    private function queryIfStudentIsRegistered($studentId) {
        $result = BasicInformation::query()
            ->select([
                'basic-information.FirstName',
                'basic-information.MiddleName',
                'basic-information.Surname',
                'basic-information.StudyType',
                'basic-information.Sex',
                'basic-information.PrivateEmail',
                'student-study-link.StudentID',
                'basic-information.GovernmentID',
                'study.Name as ProgrammeName',
                'schools.Name as School',
                'courses.Name as CourseName',
                'courses.CourseDescription as CourseDescription',
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
                    END AS 'YearOfStudy'
                ")
            ])
            ->join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
            ->join('study', 'study.ID', '=', 'student-study-link.StudyID')
            ->join('schools', 'study.ParentID', '=', 'schools.ID')
            ->join('course-electives', function ($join) {
                $join->on('course-electives.StudentID', '=', 'basic-information.ID')
                    ->where('course-electives.Year', '=', 2024);
            })
            ->join('courses', 'course-electives.CourseID', '=', 'courses.ID')
            ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
            ->join('programmes', 'programmes.ID', '=', 'program-course-link.ProgramID')
            ->where('course-electives.StudentID', $studentId)
            ->where('course-electives.Year', 2024)
            ->groupBy('courses.Name');

        return $result;
    }
    

    public function setAndUpdateCoursesForCurrentYear($studentId) {

        $dataArray = $this->getCoursesForFailedStudentsForCurrentAcademicYear($studentId);
    
        if (empty($dataArray)) {
            return; // No data to insert, so exit early
        }
    
        // Delete all existing courses for the student
        Courses::where('Student', $studentId)->delete();
    
        $coursesToInsert = [];
    
        foreach ($dataArray as $item) {
            $course = $item['Course'];
    
            // Check if Grade is "No Value" and Course is "No Value"
            if ($item['Course'] === "No Value" && $course === "No Value") {
                // If Grade and Course are both "No Value", don't insert these rows
                continue;
            }
    
            $coursesToInsert[] = [
                'Student' => $item['Student'],
                'Program' => $item['Program'],
                'Course' => $course,
                'Grade' => $item['Grade'],
            ];
        }
    
        if (!empty($coursesToInsert)) {
            // Batch insert the new courses
            Courses::insert($coursesToInsert);
        }
    }

    public function setAndSaveCoursesForCurrentYear($studentId) {
        $dataArray = $this->getCoursesForFailedStudentsForCurrentAcademicYear($studentId);
    
        if (!$dataArray) {
            $dataArray = $this->findUnregisteredStudentCourses($studentId);
        }
    
        if (empty($dataArray)) {
            return; // No data to insert, so exit early
        }
    
        $studentCourses = Courses::where('Student', $studentId)
            ->whereIn('Course', array_column($dataArray, 'Course'))
            ->get()
            ->pluck('Course')
            ->toArray();
    
        $coursesToInsert = [];
    
        foreach ($dataArray as $item) {
            $course = $item['Course'];
    
            if (!in_array($course, $studentCourses)) {
                $coursesToInsert[] = [
                    'Student' => $item['Student'],
                    'Program' => $item['Program'],
                    'Course' => $course,
                    'Grade' => $item['Grade'],
                ];
    
                // Update the list of existing courses for the student
                $studentCourses[] = $course;
            }
        }
    
        if (!empty($coursesToInsert)) {
            // Batch insert the new courses
            Courses::insert($coursesToInsert);
        }
    }

    public function getStudentResults($studentId){
        // try{
            
        // }catch(Exception $e){
            
        // }
        $academicYear= 2023;
        $student = Student::query()
                        ->where('student_number','=', $studentId)
                        ->first();
        if($student){
            $getStudentNumber = $student->student_number;
            $studentNumbers = [$getStudentNumber];
            $studentResults = $this->getAppealStudentDetails($academicYear, $studentNumbers)->first();
        }else{
            return back()->with('error', 'NOT FOUND.');               
        }

        return $studentResults;
    }
        
        
    public function getStudentCourses($studentId){
            
        
        $this->setAndSaveTheCourses($studentId);
        // Retrieve all unique Student values from the Course model
        $courses = Courses::where('Student', $studentId)->get();
        // return $courses;

        // Pass the $students variable to the view
        // return view('your.view.name', compact('students'));
        
        return $courses;
    }

    public function getSisCourses(){
        $sisCourses = SisCourses::all();
        return $sisCourses;
    }

    public function getStudentStudyLink(){
        $studentStudyLink = StudentStudyLink::all();
        return $studentStudyLink;
    }

    public function getProgramCourseLink(){
        $programCourseLink = ProgramCourseLink::all();
        return $programCourseLink;
    }
    
    public function getProgrammes(){
        $programmes = Program::all();
        return $programmes;
    }

    public function getStudy(){
        $study = Study::all();
        return $study;
    }

    public function getSchools(){
        $schools = Schools::all();
        return $schools;
    }

    public function getBasicInformation(){
        $basicInformation = BasicInformation::query()
            ->where(function ($query) {
                $query->where('StudyType', 'Fulltime')
                    ->orWhere('StudyType', 'Distance');
            })
            ->where('StudyType', '!=', 'Staff')
            ->whereRaw('LENGTH(`ID`) > 7')
            ->where('Status','!=', 'Suspended')
            ->get();
        return $basicInformation;
    }

    public function getYearOfStudy($courseNos) {
        $firstNumbers = [];
    
        foreach ($courseNos as $courseNo) {
            // Use regular expression to remove the letters and get the numbers
            $numbers = preg_replace('/[a-zA-Z]/', '', $courseNo);
    
            // Check if $numbers is not empty
            if ($numbers !== '') {
                // Get the first number
                $firstNumber = $numbers[0];
    
                // Add the first number to the array
                $firstNumbers[] = $firstNumber;
            }
        }
    
        // Find the highest number
        if($firstNumbers){
            $highestNumber = max($firstNumbers);
            if($highestNumber == 8 ){
                $highestNumber = 1;
            }elseif($highestNumber == 9){
                $highestNumber = 2;
            }else{
                $highestNumber = $highestNumber;
            }
            return $highestNumber + 1;
        }else{   
            return 1;
        }
    }

    // public function findUnregisteredNewStudentCourses($studentId) {    
    //     // Perform the query to get grades
    //     $gradesCheck = Grades::query()
    //         ->where('grades-published.StudentNo', $studentId)
    //         ->whereNotIn('AcademicYear', ['2023'])
    //         ->get();

    //     // Extract course numbers from the results
    //     $courseNumbers = $gradesCheck->pluck('CourseNo')->toArray();
    //     $getProgramme = EduroleCourses::select('p.ProgramName')
    //         ->join('program-course-link as pcl', 'courses.ID', '=', 'pcl.CourseID')
    //         ->join('programmes as p', 'pcl.ProgramID', '=', 'p.ID')
    //         ->join('study-program-link as spl', 'spl.ProgramID', '=', 'p.ID')
    //         ->join('study as s', 'spl.StudyID', '=', 's.ID')
    //         ->join('student-study-link as ssl2', 'ssl2.StudyID', '=', 's.ID')
    //         ->join('grades-published as gp', 'gp.StudentNo', '=', 'ssl2.StudentID')
    //         ->where('ssl2.StudentID', $studentId)
    //         ->whereIn('courses.Name', $courseNumbers)            
    //         ->first();   
    //     $programmeName = $getProgramme->ProgramName;
    //     // Calculate the current year of study
    //     $currentYearOfStudy = $this->getYearOfStudy($courseNumbers);        

        
    //     $level = '%' . $currentYearOfStudy;

    //     $courses = EduroleCourses::join('program-course-link as pcl', 'courses.ID', '=', 'pcl.CourseID')
    //         ->join('programmes as p', 'pcl.ProgramID', '=', 'p.ID')
    //         ->join('study-program-link as spl', 'spl.ProgramID', '=', 'p.ID')
    //         ->join('study as s', 'spl.StudyID', '=', 's.ID')
    //         ->join('student-study-link as ssl2', 'ssl2.StudyID', '=', 's.ID')
    //         ->where('p.ProgramName',  $programmeName)
    //         ->where('ssl2.StudentID', $studentId)
    //         ->select('courses.Name','courses.CourseDescription')
    //         ->get();
        
    //     if(count($courses) >0){
    //         foreach ($courses as $course) {
    //             $studentCourses[] = [
    //                 'Student' => $studentId,
    //                 'Program' => $course->CourseDescription, // You mentioned to replace with the program
    //                 'Course' => $course->Name,
    //                 'Grade' => null, // You need to define $grade2 here
    //             ];
    //         }

    //         return $studentCourses;
    //     }else{
    //         $studentCourses[] = [
    //             'Student' => $studentId,
    //             'Program' => "NO VALUE", // You mentioned to replace with the program
    //             'Course' => "NO VALUE",
    //             'Grade' => "NO VALUE", // You need to define $grade2 here
    //         ];
    //         return $studentCourses;
    //     }           
    // }

    
    public function findUnregisteredStudentCourses($studentId) {    
        // Perform the query to get grades
        $gradesCheck = Grades::query()
            ->where('grades-published.StudentNo', $studentId)
            ->whereIn('AcademicYear', ['2023'])
            ->get();

        // Extract course numbers from the results
        $courseNumbers = $gradesCheck->pluck('CourseNo')->toArray();

        // Calculate the current year of study
        $currentYearOfStudy = $this->getYearOfStudy($courseNumbers);
        try{
        
            $programmeForStudents = EduroleCourses::select('p.ProgramName')
                ->join('program-course-link as pcl', 'courses.ID', '=', 'pcl.CourseID')
                ->join('programmes as p', 'pcl.ProgramID', '=', 'p.ID')
                ->join('study-program-link as spl', 'spl.ProgramID', '=', 'p.ID')
                ->join('study as s', 'spl.StudyID', '=', 's.ID')
                ->join('student-study-link as ssl2', 'ssl2.StudyID', '=', 's.ID')
                ->join('grades-published as gp', 'gp.StudentNo', '=', 'ssl2.StudentID')
                ->where('ssl2.StudentID', $studentId)
                ->whereIn('courses.Name', $courseNumbers)            
                ->first();
            if($programmeForStudents == null){
                $firstYear = 'Y1';
                $studentsNewProgramme = BasicInformation::select('p.ProgramName')
                    ->join('student-study-link as ssl', 'ssl.StudentID', '=', 'basic-information.ID')
                    ->join('study as s', 'ssl.StudyID', '=', 's.ID')
                    ->join('study-program-link as spl', 'spl.StudyID', '=', 's.ID')
                    ->join('programmes as p', 'spl.ProgramID', '=', 'p.ID')
                    ->where('basic-information.ID', $studentId)
                    ->where('p.ProgramName', 'like', '%' .$firstYear)
                    ->first();
            
                $programmeName = $studentsNewProgramme->ProgramName;
            } else {
                $programmeName = $programmeForStudents->ProgramName;
            }  
            if($programmeName == null){
                throw new Exception("Programme name is null");
            }   
        
            // Find the last occurrence of "Y" and get the number after it
            $yearNumber = substr($programmeName, strrpos($programmeName, 'Y') + 1);

            // Increment the year number
            $incrementedYearNumber = $yearNumber + 1;

            // Replace the old year number with the incremented one
            if($programmeForStudents == null){
                
                $updatedProgrammeName = $programmeName;
            }else{
                $updatedProgrammeName = str_replace("Y".$yearNumber, "Y".$incrementedYearNumber, $programmeName);
            }
            $courses = EduroleCourses::join('program-course-link as pcl', 'courses.ID', '=', 'pcl.CourseID')
                ->join('programmes as p', 'pcl.ProgramID', '=', 'p.ID')
                ->join('study-program-link as spl', 'spl.ProgramID', '=', 'p.ID')
                ->join('study as s', 'spl.StudyID', '=', 's.ID')
                ->join('student-study-link as ssl2', 'ssl2.StudyID', '=', 's.ID')
                ->where('p.ProgramName', '=', $updatedProgrammeName)
                // ->where('p.ProgramName', 'like', '%' .$level)
                ->where('ssl2.StudentID', $studentId)
                ->select('courses.Name','courses.CourseDescription')
                ->get();
        }catch(Exception $e){    
        
            $level = '%' . $currentYearOfStudy;

            $courses = EduroleCourses::join('program-course-link as pcl', 'courses.ID', '=', 'pcl.CourseID')
                ->join('programmes as p', 'pcl.ProgramID', '=', 'p.ID')
                ->join('study-program-link as spl', 'spl.ProgramID', '=', 'p.ID')
                ->join('study as s', 'spl.StudyID', '=', 's.ID')
                ->join('student-study-link as ssl2', 'ssl2.StudyID', '=', 's.ID')
                // ->where('p.ProgramName', '=', $updatedProgrammeName)
                ->where('p.ProgramName', 'like', '%' .$level)
                ->where('ssl2.StudentID', $studentId)
                ->select('courses.Name','courses.CourseDescription')
                ->get();
        }
        
        if(count($courses) >0){
            foreach ($courses as $course) {
                $studentCourses[] = [
                    'Student' => $studentId,
                    'Program' => $course->CourseDescription, // You mentioned to replace with the program
                    'Course' => $course->Name,
                    'Grade' => null, // You need to define $grade2 here
                ];
            }

            return $studentCourses;
        }else{
            $studentCourses[] = [
                'Student' => $studentId,
                'Program' => "NO VALUE", // You mentioned to replace with the program
                'Course' => "NO VALUE",
                'Grade' => "NO VALUE", // You need to define $grade2 here
            ];
            return $studentCourses;
        }           
    }
    

    private function queryRegisteredAndUnregisteredPerYear($academicYear) {
        $results = Schools::select(
            'basic-information.FirstName',
            'basic-information.MiddleName',
            'basic-information.Surname',
            'basic-information.Sex',
            'student-study-link.StudentID',
            'basic-information.GovernmentID',
            'basic-information.PrivateEmail',
            'basic-information.MobilePhone',
            'programmes.ProgramName AS "Programme Code"',
            'study.Name',
            'schools.Description AS "School"',
            'basic-information.StudyType',
            DB::raw("CASE
                WHEN `course-electives`.StudentID IS NOT NULL THEN 'REGISTERED'
                ELSE 'NO REGISTRATION'
            END AS `RegistrationStatus`"),
            DB::raw("CASE 
                WHEN programmes.ProgramName LIKE '%1' THEN 'YEAR 1'
                WHEN programmes.ProgramName LIKE '%2' THEN 'YEAR 2'
                WHEN programmes.ProgramName LIKE '%3' THEN 'YEAR 3'
                WHEN programmes.ProgramName LIKE '%4' THEN 'YEAR 4'
                WHEN programmes.ProgramName LIKE '%5' THEN 'YEAR 5'
                WHEN programmes.ProgramName LIKE '%6' THEN 'YEAR 6'
                WHEN programmes.ProgramName IS NULL THEN 'NO REGISTRATION'
                ELSE 'NO REGISTRATION'
            END AS `YearOfStudy`")
        )
        ->leftJoin('study', 'schools.ID', '=', 'study.ParentID')
        ->leftJoin('student-study-link', 'study.ID', '=', 'student-study-link.StudyID')
        ->leftJoin('course-electives', function($join) use ($academicYear) {
            $join->on('student-study-link.StudentID', '=', 'course-electives.StudentID')
                ->where('course-electives.Year', '=', $academicYear);
        })
        ->leftJoin('courses', 'course-electives.CourseID', '=', 'courses.ID')
        ->leftJoin('program-course-link', 'courses.ID', '=', 'program-course-link.CourseID')
        ->leftJoin('programmes', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->leftJoin('basic-information', 'student-study-link.StudentID', '=', 'basic-information.ID')
        ->whereRaw('LENGTH(`basic-information`.`ID`) > 7')
        ->where(function ($query) {
            $query->where('basic-information.StudyType', '=', 'Fulltime')
                ->orWhere('basic-information.StudyType', '=', 'Distance');
        })
        ->where('basic-information.StudyType', '!=', 'Staff')
        ->groupBy('student-study-link.StudentID');
    
        return $results;
    }

    private function queryRegisteredStudentsAccordingToProgrammeAndYearOfStudy($academicYear, $yearOfStudy,$programmeName){
        $queryEdurole = $this->queryRegistrationsFromeEduroleBasedOnReturningAndNewlyAdmittedStudents($academicYear);
        $querySisReports = $this->queryRegistrationsFromSisReportsBasedOnReturningAndNewlyAdmittedStudents($academicYear);
        $resultsSisReports = $querySisReports->where('program_s_r_s.program_name', 'LIKE', '%' .$yearOfStudy)
                        ->where('study_s_r_s.study_shortname', '=',$programmeName);
        $resultsEdurole = $queryEdurole->where('programmes.ProgramName', 'LIKE', '%' .$yearOfStudy)
                        ->where('study.ShortName', '=',$programmeName);

        $combinedResults = $resultsSisReports->unionAll($resultsEdurole);

        return $resultsEdurole;
        // $results = $resultsSisReports->merge($resultsEdurole);
        // return $results;
    }
    

    private function queryRegisteredStudentsPerYearInYearOfStudy($academicYear){
        $results = Schools::select(
            'basic-information.FirstName',
            'basic-information.MiddleName',
            'basic-information.Surname',
            'basic-information.StudyType',
            'basic-information.Sex',
            'student-study-link.StudentID',
            'basic-information.GovernmentID',
            DB::raw("CASE
                WHEN programmes.ProgramName LIKE '%y1' THEN 'YEAR 1'
                WHEN programmes.ProgramName LIKE '%y2' THEN 'YEAR 2'
                WHEN programmes.ProgramName LIKE '%y3' THEN 'YEAR 3'
                WHEN programmes.ProgramName LIKE '%y4' THEN 'YEAR 4'
                WHEN programmes.ProgramName LIKE '%y5' THEN 'YEAR 5'
                ELSE 'YEAR 6'
            END AS YearOfStudy"),
            'study.Name',
            'schools.Description'
        )
        ->join('study', 'schools.ID', '=', 'study.ParentID')
        ->join('student-study-link', 'student-study-link.StudyID', '=', 'study.ID')
        ->join('course-electives', 'student-study-link.StudentID', '=', 'course-electives.StudentID')
        ->join('courses', 'course-electives.CourseID', '=', 'courses.ID')
        ->join('program-course-link', 'program-course-link.CourseID', '=', 'courses.ID')
        ->join('programmes', 'programmes.ID', '=', 'program-course-link.ProgramID')
        ->join('basic-information', 'basic-information.ID', '=', 'course-electives.StudentID')
        ->whereRaw('LENGTH(`basic-information`.`ID`) > 7')
        ->where(function ($query) {
            $query->where('basic-information.StudyType', '=', 'Fulltime')
                ->orWhere('basic-information.StudyType', '=', 'Distance');
        })
        ->where('course-electives.Year', '=', $academicYear);

        return $results;
    }

    private function queryAllStudentsRegisteredInASpecificAcademicYear($academicYear){
        $results = BasicInformation::select(
                    'basic-information.FirstName',
                    'basic-information.MiddleName',
                    'basic-information.Surname',
                    'basic-information.PrivateEmail',
                    'basic-information.ID'
                )
                ->join('course-electives AS ce', 'ce.StudentID', '=', 'basic-information.ID')
                ->whereRaw('LENGTH(`basic-information`.`ID`) > 7')
                ->where(function ($query) {
                    $query->where('basic-information.StudyType', '=', 'Fulltime')
                        ->orWhere('basic-information.StudyType', '=', 'Distance');
                })
                ->where('ce.Year', '=', $academicYear)
                ->groupBy('ce.StudentID');      
            return $results;
    }

    private function queryAllProgrammesPerSchool($schoolName){
        $results = Study::select(
                'study.Name',
                'study.ShortName',
                'schools.Name as SchoolName',
                'study.Delivery'
            )
            ->join('schools', 'study.ParentID', '=', 'schools.ID')
            ->where('schools.Name', '=', $schoolName);       
        return $results;
    }

    private function queryStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName){
        $results = BasicInformation::select(
                'edurole.basic-information.FirstName',
                'edurole.basic-information.MiddleName',
                'edurole.basic-information.Surname',
                's.Name',
                'edurole.basic-information.ID',
                'edurole.basic-information.GovernmentID',
                'edurole.basic-information.StudyType',
                'edurole.basic-information.Sex'
            )
            ->join('student-study-link AS ssl2', 'basic-information.ID', '=', 'ssl2.StudentID')
            ->leftJoin('study AS s', 'ssl2.StudyID', '=', 's.ID')
            ->whereRaw('LENGTH(`basic-information`.`ID`) > 7')
            ->where(function ($query) {
                $query->where('basic-information.StudyType', '=', 'Fulltime')
                    ->orWhere('basic-information.StudyType', '=', 'Distance');
            })
            ->where('s.ShortName', '=', $programmeName)
            ->where('ssl2.StudentID', 'LIKE', $intakeNumber . '%');             
    
        return $results;
    }

    private function queryRegisteredStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName){
        $query = $this->queryStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName);
        $results = $query->join('course-electives AS ce', 'ce.StudentID', '=', 'ssl2.StudentID')
                ->join('courses AS c', 'ce.CourseID', '=', 'c.ID')
                ->join('program-course-link AS pcl', 'pcl.CourseID', '=', 'c.ID')
                ->join('programmes AS p', 'p.ID', '=', 'pcl.ProgramID')
                ->select(
                    'edurole.basic-information.FirstName',
                    'edurole.basic-information.MiddleName',
                    'edurole.basic-information.Surname',
                    's.Name',
                    'edurole.basic-information.ID',
                    'edurole.basic-information.GovernmentID',
                    'edurole.basic-information.StudyType',
                    'edurole.basic-information.Sex',
                    'p.ProgramName',
                    DB::raw("CASE 
                        WHEN SUBSTRING(p.ProgramName, -2) LIKE 'Y1' THEN 'YEAR 1'
                        WHEN SUBSTRING(p.ProgramName, -2) LIKE 'Y2' THEN 'YEAR 2'
                        WHEN SUBSTRING(p.ProgramName, -2) LIKE 'Y3' THEN 'YEAR 3'
                        WHEN SUBSTRING(p.ProgramName, -2) LIKE 'Y4' THEN 'YEAR 4'
                        WHEN SUBSTRING(p.ProgramName, -2) LIKE 'Y5' THEN 'YEAR 5'
                        ELSE 'YEAR 6'
                    END AS YearOfStudy")
                )
                ->where('ce.Year', '=', '2023')
                ->groupBy('ssl2.StudentID');
        return $results;
    }

    public function getSumOfAllTransactionsOfEachStudent(){        
        $results = $this->querySumOfAllTransactionsOfEachStudent();
        return $results;
    }

    public function exportDataFromArray($headers, $rowData, $results, $filename){
        $filePath = storage_path('app/' . $filename);

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($filePath);

        $headerRow = WriterEntityFactory::createRowFromArray($headers);
        $writer->addRow($headerRow);

        foreach ($results as $result) {
            $data = [];
            foreach ($rowData as $field) {
                // Check if the field exists in the result array
                if (isset($result[$field])) {
                    $data[] = $result[$field];
                } else {
                    $data[] = '';
                }
            }

            $dataRow = WriterEntityFactory::createRowFromArray($data);
            $writer->addRow($dataRow);
        }

        $writer->close();

        return response()->download($filePath, $filename . '.xlsx')->deleteFileAfterSend();
    }

    public function getAllCoursesAttachedToProgramme(){
        $results = $this->queryAllCoursesAttachedToProgramme();
        return $results;
    }
    
    public function setAndSaveResultsForCurrentStudent($studentNumber,$academicYear){
        $dataArray = $this->getResultsForCurrentStudent($studentNumber,$academicYear);       
    
        $coursesToInsert = [];
    
        foreach ($dataArray as $item) {
                        
            $coursesToInsert[] = [
                'StudentNo' => $item['StudentNo'],
                'ProgramNo' => $item['ProgramNo'],
                'CourseNo' => $item['CourseNo'],
                'Grade' => $item['Grade'],
                'AcademicYear' => $item['AcademicYear'],
                'Semester' => $item['Semester'],
                'ExamMarks' => $item['ExamMarks'],
                'CAMarks' => $item['CAMarks'],
                'KeySet' => $item['KeySet'],
                'TotalMarks' => $item['TotalMarks'],
                'Points' => $item['Points'],
                'Comment' => $item['Comment'],
                'PeriodID' => $item['PeriodID'],
                'Published' => $item['Published'],
                'usertime' => $item['usertime'],
                'userdate' => $item['userdate'],
                'user' => $item['user']
            ];            
        }
    
        if (!empty($coursesToInsert)) {
            // Batch insert the new courses
            foreach ($coursesToInsert as $course) {
                GradesPublished::updateOrInsert(
                    [
                        'StudentNo' => $course['StudentNo'],
                        'CourseNo' => $course['CourseNo'],
                        'AcademicYear' => $course['AcademicYear']
                    ],
                    $course
                );
            }
        }

        return $coursesToInsert;
    }

    public function getResultsForCurrentStudent($studentNumber,$academicYear){
        
        $resultsForStudent = [];

        $results = Grade::select('usertime',
                    'userdate','AcademicYear','Semester','user',
                    'StudentNo', 'ProgramNo','ExamMarks','CAMarks',
                    'KeySet','TotalMarks','Points','Comment',
                    'CourseNo','Published', 'Grade','PeriodID')
            ->where('StudentNo', $studentNumber)
            ->where('AcademicYear', $academicYear)
            ->get();
        
        foreach ($results as $row) {
            $resultsForStudent[] = [
                'StudentNo' => $row->StudentNo,
                'ProgramNo' => $row->ProgramNo, // Replace with the program
                'CourseNo' => $row->CourseNo,
                'Grade' => $row->Grade,
                'AcademicYear' => $row->AcademicYear,
                'Semester' => $row->Semester,
                'ExamMarks' => $row->ExamMarks,
                'CAMarks' => $row->CAMarks,
                'KeySet' => $row->KeySet,
                'TotalMarks' => $row->TotalMarks,
                'Points' => $row->Points,
                'Comment' => $row->Comment,
                'PeriodID' => $row->PeriodID,
                'Published' => $row->Published,
                'usertime' => $row->usertime,
                'userdate' => $row->userdate,
                'user' => $row->user
            ];

        }

        return $resultsForStudent;
    }

    public function setAndUpdateCourses($studentId) {
        $dataArray = $this->getCoursesForFailedStudents($studentId);
    
        if (!$dataArray) {
            $dataArray = $this->findUnregisteredStudentCourses($studentId);
        }
    
        if (empty($dataArray)) {
            return; // No data to insert, so exit early
        }
    
        // Check if there are rows with "No Value" for the specific 'Student'
        $hasNoValueCourses = Courses::where('Student', $studentId)
            ->where('Course', 'No Value')
            ->exists();
    
        $studentCourses = Courses::where('Student', $studentId)
            ->whereIn('Course', array_column($dataArray, 'Course'))
            ->get();
    
        $coursesToInsert = [];
    
        foreach ($dataArray as $item) {
            $course = $item['Course'];
    
            // Check if Grade is "No Value" and Course is "No Value"
            if ($item['Course'] === "No Value" && $course === "No Value") {
                // If Grade and Course are both "No Value", don't insert these rows
                continue;
            }
    
            if (!in_array($course, $studentCourses->pluck('Course')->toArray())) {
                $coursesToInsert[] = [
                    'Student' => $item['Student'],
                    'Program' => $item['Program'],
                    'Course' => $course,
                    'Grade' => $item['Grade'],
                ];
    
                // Update the list of existing courses for the student
                $studentCourses->push(['Course' => $course]);
            }
        }
    
        if (!empty($coursesToInsert) && $hasNoValueCourses) {
            // Delete rows with "No Value" entries
            Courses::where('Student', $studentId)
                ->delete();
            
            // Batch insert the new courses
            Courses::insert($coursesToInsert);
        }
    }

    public function getInvoicesPerProgramme(){
        $results = $this->queryInvoicesPerProgramme();
        return $results;
    }

    private function queryInvoicesPerProgramme(){
        $results = SageInvoice::select(
            'AutoIndex',
            'InvNumber',
            'Description',
            'InvDate',
            'InvTotExcl'
        )->where('InvNumber', 'like', 'TPD%')
        ->orderby('Description');
        return $results;
    }
        

    public function getStudentsUnderNaturalScienceSchool($academicYear,$courseCode){
        $results = $this->queryStudentsUnderNaturalScienceSchool($academicYear,$courseCode);
        return $results;
    }

    private function queryStudentsUnderNaturalScienceSchool($academicYear, $courseCode){

        if(empty($courseCode)){
            $courseCode = ['PHY101','MAT101','BIO101','CHM101'];
        } else {
            $courseCode = [$courseCode];
        }
        $results = Study::select(
            'basic-information.FirstName',
            'basic-information.Surname',
            'basic-information.ID',
            'basic-information.GovernmentID',
            'basic-information.StudyType',
            'basic-information.Sex',
            'study.Name as ProgrammeName',
            'programmes.ProgramName',
            'study.ShortName as Programme Code',
            'study.StudyType as Mode Of Study',
            'schools.Name as SchoolName',
            'basic-information.FirstName as First Name',
            'basic-information.Surname as Last Name'
        )
        ->join('student-study-link', 'study.ID', '=', 'student-study-link.StudyID')
        ->join('basic-information', 'basic-information.ID', '=', 'student-study-link.StudentID')
        ->join('course-electives', function ($join) use ($academicYear) {
            $join->on('course-electives.StudentID', '=', 'basic-information.ID')
                ->where('course-electives.Year', '=', $academicYear);
        })
        ->join('program-course-link', 'program-course-link.CourseID', '=', 'course-electives.CourseID')
        ->join('courses', 'courses.ID', '=', 'course-electives.CourseID')
        ->join('programmes', 'programmes.ID', '=', 'program-course-link.ProgramID')
        ->join('schools', 'study.ParentID', '=', 'schools.ID')
        ->whereIn('courses.Name', $courseCode)
        ->whereRaw('LENGTH(`basic-information`.`ID`) >= 7')
        ->groupBy('basic-information.ID');
    
        return $results;
    }

    public function getStudentsPayments($studentId){
        $results = $this->queryStudentsPayments($studentId);
        return $results;
    }

    private function queryStudentsPayments($studentId){
        $studentIds = $this->getStudentsFromLMMAX()->pluck('student_id')->toArray();
        $latestInvoiceDates = SagePostAR::select('AccountLink', DB::raw('MAX(TxDate) AS LatestTxDate'))
            ->where(function($query) {
                $query->where('Description', 'like', '%-%-%')
                    ->orWhere('Reference', 'like', 'INV%');
            })
            ->where('Debit', '>', 0)
            ->groupBy('AccountLink');
        $results = SageClient::select    (
            'DCLink',
            'Account',
            'Name',
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0  
                WHEN pa.TxDate > \'2023-01-01\' THEN 0
                ELSE pa.Credit 
                END) AS TotalPaymentBefore2023'),
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0    
                WHEN pa.TxDate < \'2024-01-01\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2024'),            
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0  
                WHEN pa.TxDate < \'2023-01-01\' THEN 0
                WHEN pa.TxDate > \'2023-12-31\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2023'),
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0             
                ELSE pa.Credit 
                END) AS TotalPayments'),
            DB::raw('CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN \'Invoiced\' ELSE \'Not Invoiced\' END AS "2023InvoiceStatus"'),
            DB::raw('CASE WHEN YEAR(lid.LatestTxDate) = 2024 THEN \'Invoiced\' ELSE \'Not Invoiced\' END AS "2024InvoiceStatus"'),
            DB::raw('FORMAT(lid.LatestTxDate, \'yyyy-MM-dd\') AS LatestInvoiceDate')
        )
        ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
        ->leftJoinSub($latestInvoiceDates, 'lid', function ($join) {
            $join->on('pa.AccountLink', '=', 'lid.AccountLink');
        })
        ->where('Account', $studentId)
        ->groupBy('DCLink', 'Account', 'Name', 'lid.LatestTxDate', DB::raw('FORMAT(lid.LatestTxDate, \'yyyy-MM-dd\')'), DB::raw('CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN \'Invoiced\' ELSE \'Not Invoiced\' END'));
        return $results;
    }

    private function querySumOfAllTransactionsOfEachStudent(){
        $latestInvoiceDates = SagePostAR::select('AccountLink', DB::raw('MAX(TxDate) AS LatestTxDate'))
            ->where(function($query) {
                $query->where('Description', 'like', '%-%-%')
                    ->orWhere('Reference', 'like', 'INV%');
            })
            ->where('Debit', '>', 0)
            ->groupBy('AccountLink');

        $academicYear = '2024';
        $studentIds = $this->getStudentsFromLMMAX()->pluck('student_id')->toArray();

        $studentInformation = Schools::select(
            'basic-information.FirstName',
            'basic-information.MiddleName',
            'basic-information.Surname',
            'basic-information.Sex',
            'student-study-link.StudentID',
            'basic-information.GovernmentID',
            'basic-information.PrivateEmail',
            'basic-information.MobilePhone',
            'programmes.ProgramName AS ProgrammeCode',
            'study.Name AS StudyName',
            'study.ShortName AS ShortName',
            'schools.Description AS School',
            'basic-information.StudyType',
            'programmes.Year AS YearOfStudy',
            DB::raw("CASE
                WHEN `course-electives`.StudentID IS NOT NULL THEN 'REGISTERED'
                ELSE 'NO REGISTRATION'
            END AS RegistrationStatus")
        )
        ->leftJoin('study', 'schools.ID', '=', 'study.ParentID')
        ->leftJoin('student-study-link', 'study.ID', '=', 'student-study-link.StudyID')
        ->leftJoin('course-electives', function($join) use ($academicYear) {
            $join->on('student-study-link.StudentID', '=', 'course-electives.StudentID')
                ->where('course-electives.Year', '=', $academicYear);
        })
        ->leftJoin('courses', 'course-electives.CourseID', '=', 'courses.ID')
        ->leftJoin('program-course-link', 'courses.ID', '=', 'program-course-link.CourseID')
        ->leftJoin('programmes', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->leftJoin('basic-information', 'student-study-link.StudentID', '=', 'basic-information.ID')
        ->whereRaw('LENGTH(`basic-information`.`ID`) > 7')
        ->where(function ($query) {
            $query->where('basic-information.StudyType', '=', 'Fulltime')
                ->orWhere('basic-information.StudyType', '=', 'Distance');
        })
        ->where('basic-information.StudyType', '!=', 'Staff')
        ->whereIn('student-study-link.StudentID', $studentIds)
        ->groupBy('student-study-link.StudentID')
        ->get();

        $studentPaymentInformation = SageClient::select    (
            'DCLink',
            'Account',
            'Name',
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0
                WHEN pa.TxDate > \'2023-01-01\' THEN 0
                ELSE pa.Credit 
                END) AS TotalPaymentBefore2023'),
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0  
                WHEN pa.TxDate < \'2024-01-01\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2024'),            
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0 
                WHEN pa.TxDate < \'2023-01-01\' THEN 0
                WHEN pa.TxDate > \'2023-12-31\' THEN 0 
                ELSE pa.Credit 
                END) AS TotalPayment2023'),
            DB::raw('SUM(CASE 
                WHEN pa.Description LIKE \'%reversal%\' THEN 0  
                WHEN pa.Description LIKE \'%FT%\' THEN 0
                WHEN pa.Description LIKE \'%DE%\' THEN 0  
                WHEN pa.Description LIKE \'%[A-Za-z]+-[A-Za-z]+-[0-9][0-9][0-9][0-9]-[A-Za-z][0-9]%\' THEN 0          
                ELSE pa.Credit 
                END) AS TotalPayments'),
            DB::raw('SUM(pa.Credit) as TotalCredit'),
            DB::raw('SUM(pa.Debit) as TotalDebit'),
            DB::raw('SUM(pa.Debit) - SUM(pa.Credit) as TotalBalance'),
            DB::raw('CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN \'Invoiced\' ELSE \'Not Invoiced\' END AS "2023InvoiceStatus"'),
            DB::raw('CASE WHEN YEAR(lid.LatestTxDate) = 2024 THEN \'Invoiced\' ELSE \'Not Invoiced\' END AS "2024InvoiceStatus"'),
            DB::raw('FORMAT(lid.LatestTxDate, \'yyyy-MM-dd\') AS LatestInvoiceDate')
        )
        ->join('LMMU_Live.dbo.PostAR as pa', 'pa.AccountLink', '=', 'DCLink')
        ->leftJoinSub($latestInvoiceDates, 'lid', function ($join) {
            $join->on('pa.AccountLink', '=', 'lid.AccountLink');
        })
        ->groupBy('DCLink', 'Account', 'Name', 'lid.LatestTxDate', DB::raw('FORMAT(lid.LatestTxDate, \'yyyy-MM-dd\')'), DB::raw('CASE WHEN YEAR(lid.LatestTxDate) = 2023 THEN \'Invoiced\' ELSE \'Not Invoiced\' END'))
        ->get();

        $studentInvoices = SisReportsSageInvoices::all();
        $studentInvoices = $studentInvoices->toArray();
        $studentInformation = $studentInformation->toArray();
        $studentPaymentInformation = $studentPaymentInformation->toArray();

        $mergedResults = [];

        foreach ($studentInformation as $student) {
            $mergedResult = $student;

            // Find the corresponding payment information for this student
            foreach ($studentPaymentInformation as $paymentInformation) {
                if ($paymentInformation['Account'] == $student['StudentID']) {
                    // Merge the payment information into the result
                    $mergedResult = array_merge($mergedResult, $paymentInformation);
                    break;
                }
            }

            $mergedResults[] = $mergedResult;
        }
        $allResults = [];

        foreach ($mergedResults as $student) {
            // Find the corresponding invoice information for this student
            foreach ($studentInvoices as $invoice) {
                // Get the first three characters of the StudentID
                $studentIdStart = substr($student['StudentID'], 0, 3);
        
                // Set the YearOfInvoice based on the start of the StudentID
                switch ($studentIdStart) {
                    case '190':
                        $invoice['InvoiceYearOfInvoice'] = '2019';
                        $invoice['InvoiceYearOfStudy'] = 'Y2';
                        $invoice['InvoiceProgrammeCode'] = $student['ShortName'];
                        $invoice['InvoiceModeOfStudy'] = $student['StudyType'];
                        break;
                    case '210':
                        $invoice['InvoiceYearOfInvoice'] = '2021';
                        $invoice['InvoiceYearOfStudy'] = 'Y2';
                        $invoice['InvoiceProgrammeCode'] = $student['ShortName'];
                        $invoice['InvoiceModeOfStudy'] = $student['StudyType'];
                        break;
                    case '220':
                        $invoice['InvoiceYearOfInvoice'] = '2022';
                        $invoice['InvoiceYearOfStudy'] = 'Y2';
                        $invoice['InvoiceProgrammeCode'] = $student['ShortName'];
                        $invoice['InvoiceModeOfStudy'] = $student['StudyType'];
                        break;
                    case '230':
                        $invoice['InvoiceYearOfInvoice'] = '2023';
                        $invoice['InvoiceYearOfStudy'] = 'Y2';
                        $invoice['InvoiceProgrammeCode'] = $student['ShortName'];
                        $invoice['InvoiceModeOfStudy'] = $student['StudyType'];
                        break;
                    case '240':
                        $invoice['InvoiceYearOfInvoice'] = '2024';
                        $invoice['InvoiceYearOfStudy'] = 'Y1';
                        $invoice['InvoiceProgrammeCode'] = $student['ShortName'];
                        $invoice['InvoiceModeOfStudy'] = $student['StudyType'];
                        break;
                    default:
                        $invoice['InvoiceYearOfInvoice'] = '2019';
                        $invoice['InvoiceYearOfStudy'] = 'Y2';
                        $invoice['InvoiceProgrammeCode'] = $student['ShortName'];
                        $invoice['InvoiceModeOfStudy'] = $student['StudyType'];
                }
        
                // Merge the invoice information into the result
                $student = array_merge($student, $invoice);
                break;
            }
            $allResults[] = $student;
        }

        return $allResults;
    }

    public function getAllCoursesAttachedToProgrammeForAStudentBasedOnCourses($studentId,$courses){
        $results = $this->queryAllCoursesAttachedToProgrammeForAStudent($studentId)
                    ->whereIn('courses.Name', $courses);
        return $results;
    }

    public function getAllCoursesAttachedToProgrammeNamesForAStudentBasedOnCourses($studentId, $courses){
        $results = $this->queryAllCoursesAttachedToProgrammeForAStudent($studentId)
                    ->where(function($query) use ($courses) {
                        foreach ($courses as $course) {
                            $query->orWhere('courses.CourseDescription', 'LIKE', '%' . $course . '%');
                        }
                    });
        return $results;
    }

    public function getAllCoursesAttachedToProgrammeForAStudent($studentId){
        $results = $this->queryAllCoursesAttachedToProgrammeForAStudent($studentId);
        return $results;
    }

    private function queryAllCoursesAttachedToProgrammeForAStudent($studentId){
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
        ->where('student-study-link.StudentID', $studentId);

        return $results;
    }

    public function getCoursesInASpecificProgrammeCode($programmeCode){
        $results = $this->queryCoursesInASpecificProgrammeCode($programmeCode);
        return $results;
    }

    public function getRegistrationsFromeEduroleBasedOnReturningAndNewlyAdmittedStudents( $academicYear){
        $results = $this->queryRegistrationsFromeEduroleBasedOnReturningAndNewlyAdmittedStudents( $academicYear);
        return $results;
    }

    private function queryRegistrationsFromeEduroleBasedOnReturningAndNewlyAdmittedStudents($academicYear){
        $results = BasicInformation::select(
            'basic-information.FirstName',
            'basic-information.MiddleName',
            'basic-information.Surname',
            'basic-information.ID',
            'basic-information.Sex',
            'basic-information.GovernmentID',
            'basic-information.PrivateEmail',
            'basic-information.MobilePhone',
            DB::raw('study.Name AS ProgrammeName'),
            DB::raw('study.ShortName AS ProgrammeCode'),
            DB::raw('schools.Description AS School'),
            'basic-information.StudyType',
            DB::raw("
                CASE 
                    WHEN `basic-information`.ID LIKE '240%' THEN 'NEWLY ADMITTED'
                    ELSE 'RETURNING STUDENT'
                END AS 'StudentType'
            "),
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
                    ELSE 'NO REGISTRATION'
                END AS YearOfStudy
            ")
        )
        ->join('student-study-link', 'student-study-link.StudentID', '=', 'basic-information.ID')
        ->join('study', 'student-study-link.StudyID', '=', 'study.ID')
        ->join('schools', 'study.ParentID', '=', 'schools.ID')
        ->join('course-electives', function ($join) use ($academicYear) {
            $join->on('basic-information.ID', '=', 'course-electives.StudentID')
                ->where('course-electives.Year', '=', $academicYear);
        })
        ->join('courses', 'course-electives.CourseID', '=', 'courses.ID')
        ->join('program-course-link', 'courses.ID', '=', 'program-course-link.CourseID')
        ->join('programmes', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->whereRaw('LENGTH(`basic-information`.ID) > 7')
        ->groupBy('basic-information.ID');

        return $results;
    }

    public function getRegistrationsFromSisReportsBasedOnReturningAndNewlyAdmittedStudents($academicYear){
        $results = $this->queryRegistrationsFromSisReportsBasedOnReturningAndNewlyAdmittedStudents($academicYear);
        return $results;
    }

    private function queryRegistrationsFromSisReportsBasedOnReturningAndNewlyAdmittedStudents($academicYear) {
        $results = BasicInformationSR::select(
            'basic_information_s_r_s.FirstName',
            'basic_information_s_r_s.MiddleName',
            'basic_information_s_r_s.Surname',
            'basic_information_s_r_s.StudentID as ID',
            'basic_information_s_r_s.GovernmentID',
            'basic_information_s_r_s.Sex',
            'basic_information_s_r_s.PrivateEmail',
            'basic_information_s_r_s.MobilePhone',
            DB::raw('study_s_r_s.study_name AS ProgrammeName'),
            DB::raw('study_s_r_s.study_shortname AS ProgrammeCode'),
            DB::raw('schools_s_r_s.school_name AS School'),
            'basic_information_s_r_s.StudyType',
            DB::raw("
                CASE 
                    WHEN `basic_information_s_r_s`.StudentID LIKE '240%' THEN 'NEWLY ADMITTED'
                    ELSE 'RETURNING STUDENT'
                END AS 'StudentType'
            "), 
            DB::raw("
                CASE 
                    WHEN program_s_r_s.program_name LIKE '%y1' THEN 'YEAR 1'
                    WHEN program_s_r_s.program_name LIKE '%y2' THEN 'YEAR 2'
                    WHEN program_s_r_s.program_name LIKE '%y3' THEN 'YEAR 3'
                    WHEN program_s_r_s.program_name LIKE '%y4' THEN 'YEAR 4'
                    WHEN program_s_r_s.program_name LIKE '%y5' THEN 'YEAR 5'
                    WHEN program_s_r_s.program_name LIKE '%y6' THEN 'YEAR 6'
                    WHEN program_s_r_s.program_name LIKE '%y8' THEN 'YEAR 1'
                    WHEN program_s_r_s.program_name LIKE '%y9' THEN 'YEAR 2'
                    ELSE 'NO REGISTRATION'
                END AS YearOfStudy
            ")           
        )
        ->join('student_study_link_s_r_s', 'student_study_link_s_r_s.student_id', '=', 'basic_information_s_r_s.StudentID')
        ->join('study_s_r_s', 'student_study_link_s_r_s.study_id', '=', 'study_s_r_s.study_id')
        ->join('schools_s_r_s', 'study_s_r_s.parent_id', '=', 'schools_s_r_s.school_id') 
        ->join('course_registration', function ($join) use ($academicYear) {
            $join->on('basic_information_s_r_s.StudentID', '=', 'course_registration.StudentID')
                ->where('course_registration.Year', '=', $academicYear);
        })
        ->join('courses_s_r_s', 'course_registration.CourseID', '=', 'courses_s_r_s.course_name')
        ->join('program_course_links_s_r_s', 'courses_s_r_s.course_id', '=', 'program_course_links_s_r_s.course_id')
        ->join('program_s_r_s', 'program_course_links_s_r_s.program_id', '=', 'program_s_r_s.programme_id')
        ->whereRaw('LENGTH(`basic_information_s_r_s`.StudentID) > 7')
        // ->whereNotIn('basic_information_s_r_s.StudentID', $eduroleArray)
        ->distinct('basic_information_s_r_s.StudentID');
        
        return $results;
    }

    private function queryCoursesInASpecificProgrammeCode($programmeCode){        
        $results = SisCourses::select(
            'courses.ID',
            'courses.Name as CourseCode',
            'courses.CourseDescription as CourseName',
            
        )
        ->join('program-course-link', 'courses.ID', '=', 'program-course-link.CourseID')
        ->join('programmes', 'program-course-link.ProgramID', '=', 'programmes.ID')
        ->where('programmes.ProgramName', $programmeCode)
        ->select('courses.Name')
        ->distinct();
        return $results;            
    }

    private function queryAllCoursesAttachedToProgramme(){
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
        ->join('schools', 'study.ParentID', '=', 'schools.ID');

        return $results;
    }

    
}