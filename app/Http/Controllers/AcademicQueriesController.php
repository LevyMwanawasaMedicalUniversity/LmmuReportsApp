<?php

namespace App\Http\Controllers;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Illuminate\Http\Request;

class AcademicQueriesController extends Controller
{
    public function index(){
        
        return view('academics.index');
    } 

    //All students registered in a specific academic Year, regardless of programme
    public function viewAllStudentsRegisteredInASpecificAcademicYear(Request $request){

        $academicYear = $request->input('academicYear');

        if ($academicYear === null) {
            $results = [];
        } else {
            $results = $this->getAllStudentsRegisteredInASpecificAcademicYear($academicYear)->paginate('20');
        }        
        return view('academics.reports.viewAllStudentsRegisteredInASpecificAcademicYear',compact('results','academicYear'));
    }      

    public function exportAllStudentsRegisteredInASpecificAcademicYear($academicYear){

        $headers = [
            'First Name',
            'Middle Name',
            'Surname',
            'Email',
            'Student Number'
        ];
        
        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'PrivateEmail',
            'ID',
        ];
        
        $results = $this->getAllStudentsRegisteredInASpecificAcademicYear($academicYear)->get();        
        $filename = 'AllStudentsRegisteredInASpecificAcademicYear' . $academicYear;
        
        return $this->exportData($headers, $rowData, $results, $filename);
    }

    public function viewAllCoursesWithResults(Request $request){

        $results = $this->getCoursesWithResults()->paginate('20');
        return view('academics.reports.viewAllCoursesWithResults',compact('results'));
    }

    public function viewAllCoursesAttachedToProgramme(){
        $results = $this->getAllCoursesAttachedToProgramme()->paginate('20');
        return view('academics.reports.viewAllCoursesAttachedToProgramme',compact('results'));
    }

    public function viewStudentsUnderNaturalScienceSchool(Request $request){

        $academicYear = $request->input('academicYear');
        $courseCode = $request->input('courseCode');

        if ($academicYear === null) {            
            $academicYear = 2024;
        }

        $results = $this->getStudentsUnderNaturalScienceSchool($academicYear,$courseCode);
        
        $results = $results->paginate('20');
                
        return view('academics.reports.viewStudentsUnderNaturalScienceSchool',compact('results','academicYear','courseCode'));
    }

    public function gradesArchiveImport(){
        return view('academics.grades.gradesArchiveImport');
    }

    public function uploadGradesToArchive(Request $request){
        set_time_limit(1200000);
        // Validate the form data
        $request->validate([
            'excelFile' => 'required|mimes:xls,xlsx,csv',
            'academicYear' => 'required',
        ]);
       
        $academicYear = $request->input('academicYear');

        if ($request->hasFile('excelFile')){
            $file = $request->file('excelFile');

            // Initialize the Box/Spout reader
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file->getPathname());

            $isHeaderRow = true; // Flag to identify the header row
            $studentNumbers = [];

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    // Skip the header row
                    if ($isHeaderRow) {
                        $isHeaderRow = false;
                        continue;
                    }

                    // Assuming the student number is in the first column (index 1)
                    $studentNumber = $row->getCellAtIndex(0)->getValue();

                    $studentNumbers[] = $studentNumber;
                }
            }

            $reader->close();

            foreach ($studentNumbers as $studentNumber) {                
                $this->setAndSaveResultsForCurrentStudent($studentNumber,$academicYear);
            }

            
        }

        return redirect()->back()->with('success', 'Grades Uploaded Successfully');
    }

    

    public function gradesArchiveView(){
        
        return view('academics.grades.gradesArchiveView');
    }   

    public function exportStudentsUnderNaturalScienceSchool(Request $request)
    {
        $academicYear = $request->input('academicYear');
        $courseCode = $request->input('courseCode');

        $headers = [
            'First Name',
            'Middle Name',
            'Surname',
            'Gender',            
            'Student Number',
            'NRC',
            'Programme',
            'School',
            'Study Mode'
        ];
        
        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'Sex',
            'ID',
            'GovernmentID',
            'ProgrammeName',
            'SchoolName',
            'StudyType'
        ];
        
        $results = $this->getStudentsUnderNaturalScienceSchool($academicYear,$courseCode)->get();        
        $filename = 'AllStudentsUnderNaturalScienceSchool' . $academicYear . $courseCode;
        
        return $this->exportData($headers, $rowData, $results, $filename);
    }

    public function exportAllCoursesAttachedToProgramme(){

        $headers = [
            'Course Code',
            'Course Name',
            'Programme',
            'School',            
            'Code Registered Under',
            'Year Of Study',
            'Study Mode'
        ];
        
        $rowData = [
            'CourseCode',
            'CourseName',
            'Programme',
            'School',
            'CodeRegisteredUnder',                        
            'YearOfStudy',
            'StudyMode'
        ];
        
        $results = $this->getAllCoursesAttachedToProgramme()->get();        
        $filename = 'AllCoursesAttachedToProgramme';
        
        return $this->exportData($headers, $rowData, $results, $filename);
    }
    
    public function viewAllProgrammesPerSchool(Request $request){

        $schoolName = $request->input('schoolName');

        if ($schoolName === null) {
            $results = [];
        } else {
            $results = $this->getAllProgrammesPerSchool($schoolName)->paginate('20');
        }        
        return view('academics.reports.viewAllProgrammesPerSchool',compact('results','schoolName'));
    }
    
    public function examMdificationAuditTrail(Request $request){
        $academicYear = $request->input('academicYear');
        $results = $this->getExamModificationAuditTrail()->get();
        return view('academics.reports.examModificationAuditTrail',compact('results'));
    }

    

    public function exportAllProgrammesPerSchool($schoolName){

        $headers = [
            'Programme Name',
            'Programme Code',
            'School Name',
            'Delivery Mode'
        ];
        
        $rowData = [
            'Name',
            'ShortName',
            'SchoolName',
            'Delivery'
        ];
        
        $results = $this->getAllProgrammesPerSchool($schoolName)->get();        
        $filename = 'AllProgrammesPerSchool' . $schoolName;
        
        return $this->exportData($headers, $rowData, $results, $filename);
    }

     //Students from specific intake year taking a programme
    
    public function viewStudentsFromSpecificIntakeYearTakingAProgramme(Request $request)
    {
        $intakeName = $request->input('intakeName');
        $programmeName = $request->input('programmeName');
        $schoolName = $request->input('schoolName');

        if ($intakeName === null) {
            $results = [];
        } else {
            $results = $this->getStudentsFromSpecificIntakeYearTakingAProgramme($intakeName, $programmeName)->paginate('20');
        }        
        return view('academics.reports.viewStudentsFromSpecificIntakeYearTakingAProgramme',compact('results','intakeName','programmeName','schoolName'));
    } 

    public function exportAllCoursesWithResults(){
        $headers = [
            'Course Code',
            'Course Name',
            'Programme Code',
            'Programme',            
            'School',
            'First Name',
            'Last Name',
            'Results Status'            
        ];
        
        $rowData = [
            'CourseCode',
            'CourseName',
            'ProgramName',
            'Programme',
            'School',                        
            'FirstName',
            'Surname',
            'UnpublishedResults'
        ];
        
        $results = $this->getCoursesWithResults()->get();        
        $filename = 'CoursesWithResults';
        
        return $this->exportData($headers, $rowData, $results, $filename);
    }

    public function exportStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName){

        $headers = [
            'FIRSTNAME',
            'MIDDLENAME',
            'SURNAME',
            'GENDER',            
            'STUDENT NUMBER',
            'NRC',
            'PROGRAMME NAME',
            'STUDY MODE'            
        ];
        
        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'Sex',
            'ID',                        
            'GovernmentID',
            'Name',
            'StudyType'
        ];
        
        $results = $this->getStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName)->get();        
        $filename = 'AllStudentsFromSpecificIntakeYearTakingAProgramme' . $intakeNumber .''.$programmeName;
        
        return $this->exportData($headers, $rowData, $results, $filename);
    }

    //DYNAMIC FORM GET PROGRAMMES IN SCHOOL Students from specific intake year taking a programme

    public function getProgrammesBySchoolDynamicForm(Request $request){        
        $schoolName = $request->input('schoolName');
        $results = $this->getAllProgrammesPerSchool($schoolName)->get();
        return response()->json($results);
    }

    //Registerd Students from specific intake year taking a programme
    public function viewRegisteredStudentsFromSpecificIntakeYearTakingAProgramme(Request $request)
    {
        $intakeName = $request->input('intakeName');
        $programmeName = $request->input('programmeName');
        $schoolName = $request->input('schoolName');

        if ($intakeName === null) {
            $results = [];
        } else {
            $results = $this->getRegisteredStudentsFromSpecificIntakeYearTakingAProgramme($intakeName, $programmeName)->paginate('20');
        }        
        return view('academics.reports.viewRegisteredStudentsFromSpecificIntakeYearTakingAProgramme',compact('results','intakeName','programmeName','schoolName'));
    }

    
    public function exportRegisteredStudentsPerYearInYearOfStudy($yearOfStudy, $academicYear){

        $headers = [
            'FIRSTNAME',
            'MIDDLENAME',
            'SURNAME',
            'STUDENT NUMBER',
            'MODE OF STUDY',
            'GENDER',
            'NRC',
            'PROGRAMME',
            'SCHOOL'
        ];
        
        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'StudentID',
            'StudyType',
            'Sex',
            'GovernmentID',
            'Name',
            'Description'
        ];
        
        $results = $this->getRegisteredStudentsPerYearInYearOfStudy($yearOfStudy, $academicYear)->get();        
        $filename = 'AllRegisteredStudentsPerYearInYearOfStudy' . $yearOfStudy .' '.$academicYear;
        
        return $this->exportData($headers, $rowData, $results, $filename);

    }

    public function exportRegisteredStudentsAccordingToProgrammeAndYearOfStudy($yearOfStudy,$academicYear,$programmeName){

        $headers = [
            'FIRSTNAME',
            'MIDDLENAME',
            'SURNAME',
            'STUDENT NUMBER',
            'PROGRAMME NAME',
            'STUDY MODE',
            'GENDER',
            'NRC',            
            'SCHOOL'
        ];

        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'StudentID',            
            'Name',
            'StudyType',
            'Sex',
            'GovernmentID',
            'Description'
        ];

        $results = $this->getRegisteredStudentsAccordingToProgrammeAndYearOfStudy($academicYear, $yearOfStudy,$programmeName)->get();
        $filename = 'AllRegisteredStudentsAccordingToProgrammeAndYearOfStudy' . $academicYear .' '.$programmeName.' '.$yearOfStudy;
        return $this->exportData($headers, $rowData, $results, $filename);
    }

    public function exportRegisteredStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName){

        $headers = [
            'FIRSTNAME',
            'MIDDLENAME',
            'SURNAME',
            'GENDER',
            'STUDENT NUMBER',
            'NRC',            
            'PROGRAMME NAME',
            'STUDY MODE',
            'YEAR OF STUDY'
        ];
        
        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'Sex',
            'ID',
            'GovernmentID',
            'Name',
            'StudyType',
            'YearOfStudy'            
        ];
        
        $results = $this->getRegisteredStudentsFromSpecificIntakeYearTakingAProgramme($intakeNumber, $programmeName)->get();        
        $filename = 'AllRegisteredStudentsFromSpecificIntakeYearTakingAProgramme ' . $intakeNumber .''.$programmeName;
        
        return $this->exportData($headers, $rowData, $results, $filename);
    }

    public function exportRegisteredAndUnregisteredPerYear($academicYear){
        $headers = [
            'FIRSTNAME',
            'MIDDLENAME',
            'SURNAME',
            'GENDER',
            'STUDENT NUMBER',
            'NRC',
            'PROGRAMME',
            'MODE OF STUDY',
            'REGISTRATION',
            'YEAR OF STUDY'
        ];
        
        $rowData = [
            'FirstName',
            'MiddleName',
            'Surname',
            'Sex',
            'StudentID',
            'GovernmentID',
            'Name',
            'StudyType',
            'RegistrationStatus',
            'YearOfStudy'
        ];

        $results = $this->getRegisteredAndUnregisteredPerYear($academicYear)->get();
        $filename = 'AllRegisteredAndUnregisteredPerYear ' . $academicYear;

        return $this->exportData($headers, $rowData, $results, $filename);
    }

    public function viewRegisteredStudentsPerYearInYearOfStudy(Request $request){

        $yearOfStudy =  $request->input('yearOfStudy');
        $academicYear = $request->input('academicYear');

        if ($yearOfStudy === null) {
            $results = [];
        } else {
            $results = $this->getRegisteredStudentsPerYearInYearOfStudy($yearOfStudy, $academicYear)->paginate('20');
        }    

        return view('academics.reports.viewRegisteredStudentsPerYearInYearOfStudy',compact('results','yearOfStudy','academicYear'));
    }

    public function viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy(Request $request){
        
        $academicYear = $request->input('academicYear');
        $yearOfStudy =  $request->input('yearOfStudy');        
        $programmeName = $request->input('programmeName');
        $schoolName = $request->input('schoolName');

        if ($academicYear === null) {
            $results = [];
        } else {
            $results = $this->getRegisteredStudentsAccordingToProgrammeAndYearOfStudy($academicYear, $yearOfStudy,$programmeName)->paginate('20');
        }    

        return view('academics.reports.viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy',compact('results','academicYear','yearOfStudy','programmeName','schoolName'));
        
    }
    
    public function viewRegisteredAndUnregisteredPerYear(Request $request){
        $academicYear = $request->input('academicYear');

        if ($academicYear === null) {
            $results = [];
        } else {
            $results = $this->getRegisteredAndUnregisteredPerYear($academicYear)->paginate('20');
        }    

        return view('academics.reports.viewRegisteredAndUnregisteredPerYear',compact('results','academicYear'));
    }


    public function viewUnregisteredStudentsEligibleForRegistration(){


        return view('academics.reports.viewUnregisteredStudentsEligibleForRegistration');
    }
}
