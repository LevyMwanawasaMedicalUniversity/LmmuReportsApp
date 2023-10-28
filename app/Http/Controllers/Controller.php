<?php

namespace App\Http\Controllers;


use App\Models\BasicInformation;
use App\Models\Grade;
use App\Models\SagePostAR;
use App\Models\Schools;
use App\Models\Study;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

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
            'study.Name',
            'schools.Description AS "School"',
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
        ->whereRaw('LENGTH(`basic-information`.`ID`) = 9')
        ->where(function ($query) use ($studentNumbers) {
            $query->where('basic-information.StudyType', '=', 'Fulltime')
                ->orWhere('basic-information.StudyType', '=', 'Distance');
        })
        ->where('basic-information.StudyType', '!=', 'Staff')
        ->whereIn('student-study-link.StudentID', $studentNumbers)
        ->groupBy('student-study-link.StudentID');
    
        return $results;
    }

    public function getCoursesForFailedStudents($studentId) {
        $failedCourses = [];
    
        $failedStudents = Grade::select('StudentNo','ProgramNo', 'CourseNo', 'Grade')
            ->whereIn('StudentNo', function ($query) {
                $query->select('StudentNo')
                    ->from('grades')
                    ->whereNotIn('Grade', ['A+', 'A', 'B+', 'B', 'C+', 'C', 'P', 'CHANG']);
            })
            ->where('StudentNo', $studentId)
            ->orderBy('StudentNo')
            ->get();
    
        foreach ($failedStudents as $row) {
            $student = $row->StudentNo;
            $program = $row->ProgramNo;
            $course = $row->CourseNo;
            $grade = $row->Grade;
    
            $repeatedCourses = Grade::select('StudentNo','ProgramNo', 'CourseNo', 'Grade')
                ->where('CourseNo', $course)
                ->where('StudentNo', $student)
                ->get();
    
            $duplicateCount = count($repeatedCourses);
    
            if ($duplicateCount > 1) {
                $cleared = Grade::select('StudentNo','ProgramNo', 'CourseNo', 'Grade')
                    ->where('StudentNo', $student)
                    ->where('CourseNo', $course)
                    ->whereIn('Grade', ['A+', 'A', 'B+', 'B', 'C+', 'C', 'P', 'CHANG'])
                    ->orderBy('Grade')
                    ->get();
    
                $ifCleared = count($cleared);
    
                if ($ifCleared === 0) {
                    continue;
                } else {
                    foreach ($cleared as $row2) {
                        $student2 = $row2->StudentNo;
                        $program2 = $row2->ProgramNo;
                        $course2 = $row2->CourseNo;
                        $grade2 = $row2->Grade;
    
                        if (!in_array($grade2, ['A+', 'A', 'B+', 'B', 'C+', 'C', 'P', 'CHANG'])) {
                            $failedCourses[] = [
                                'Student' => $student2,
                                'Program' => $program2, // Replace with the program
                                'Course' => $course2,
                                'Grade' => $grade2,
                            ];
                        }
                    }
                }
            } else {
                if (!in_array($grade, ['A+', 'A', 'B+', 'B', 'C+', 'C', 'P', 'CHANG'])) {
                    $failedCourses[] = [
                        'Student' => $student,
                        'Program' => $program, // Replace with the program
                        'Course' => $course,
                        'Grade' => $grade,
                    ];
                }
            }
        }
    
        return $failedCourses;
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
        ->whereRaw('LENGTH(`basic-information`.`ID`) = 9')
        ->where(function ($query) {
            $query->where('basic-information.StudyType', '=', 'Fulltime')
                ->orWhere('basic-information.StudyType', '=', 'Distance');
        })
        ->where('basic-information.StudyType', '!=', 'Staff')
        ->groupBy('student-study-link.StudentID');
    
        return $results;
    }

    private function queryRegisteredStudentsAccordingToProgrammeAndYearOfStudy($academicYear, $yearOfStudy,$programmeName){
        $query = $this->queryRegisteredStudentsPerYearInYearOfStudy($academicYear);
        $results = $query->where('programmes.ProgramName', 'LIKE', '%' .$yearOfStudy)
                        ->where('study.ShortName', '=',$programmeName) 
                        ->groupBy('basic-information.ID');
        return $results;
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
        ->whereRaw('LENGTH(`basic-information`.`ID`) = 9')
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
                ->whereRaw('LENGTH(`basic-information`.`ID`) = 9')
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
            ->whereRaw('LENGTH(`basic-information`.`ID`) = 9')
            ->where(function ($query) {
                $query->where('basic-information.StudyType', '=', 'Fulltime')
                    ->orWhere('basic-information.StudyType', '=', 'Distance');
            })
            ->where('s.ShortName', '=', $programmeName)
            ->where('ssl2.StudentID', 'LIKE', $intakeNumber . '%');             
    
        return $results;
    }

    private function queryRegisteredStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName)
    {
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

    private function querySumOfAllTransactionsOfEachStudent(){

        // $results = $results = BasicInformation::join('sage_database.sage_clients', 'basic_informations.ID', '=', 'sage_database.sage_clients.Account')
        // ->join('sage_post_ar', 'sage_clients.DCLink', '=', 'sage_post_ar.AccountLink')
        // ->select('basic_informations.ID', 'sage_clients.Account', 'sage_post_ar.Credit');

        $results = SagePostAR::all();

        return $results;
    }
}
