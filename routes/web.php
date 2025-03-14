<?php

use App\Http\Controllers\DocketController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContinousAssessmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SisReportsEduroleDataManagementController;
use App\Http\Controllers\AcademicQueriesController;
use App\Http\Controllers\NursingAndMidwiferyController;
use App\Http\Controllers\FinanceQueriesController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        if(Auth::user()->hasRole('Administrator') || Auth::user()->hasRole('Academics') || Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Developer')) {
            return redirect()->route('landing.page');
        } else {
            return redirect('/home');
        }
    } else {
        return view('welcome');
    }
});

Route::get('/testsAssess', [DocketController::class, 'testAssess']);
Auth::routes(['register' => false]);
Route::get('/verify/{studentNumber}', [DocketController::class, 'verifyStudent'])->name('docket.verify');
Route::get('/verifyNmcz/{studentNumber}', [DocketController::class, 'verifyStudentNmcz'])->name('docket.verifyNmcz');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/exportGraduants', [StudentsController::class, 'getGraduatedStudents']);
Route::get('/send-test-email/{id}', [EmailController::class, 'sendTestEmail']);

Route::group(['middleware' => 'auth'], function () {
    Route::middleware(['can:Finance', 'can:Academics'])->group(function () {
        Route::get('/landing', [HomeController::class, 'landingPage'])->name('landing.page');
        Route::get('/fetchData/{academicYear}', [HomeController::class, 'fetchData']);
    });

    Route::resource('user', UserController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class);

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::get('{page}', [PageController::class, 'index'])->name('page.index');

    Route::get('/students/exam/results/{studentNumber}', [DocketController::class, 'students2023ExamResults'])->name('studentsExamResults');
    Route::get('/students/caResult/viewCaComponents/{courseId}/', [ContinousAssessmentController::class, 'viewCaComponents'])->name('docket.viewCaComponents');
    Route::get('/students/caResult/viewCaComponentsWithComponent/{courseId}/', [ContinousAssessmentController::class, 'viewCaComponentsWithComponent'])->name('docket.viewCaComponentsWithComponent');
    Route::get('/students/caResult/viewInSpecificCaComponent/{courseId}/{caType}', [ContinousAssessmentController::class, 'viewInSpecificCaComponent'])->name('docket.viewInSpecificCaComponent');

    Route::group(['namespace' => 'App\Http\Controllers'], function () {
	Route::get('/report', 'ReportController@registeredStudents')->name('registered.students');
    Route::get('/export-data', 'ReportController@exportRegisteredStudents')->name('export.data');
    
    Route::middleware('can:Administrator')->group(function () {   
        Route::GET('/importOrUpdateSisReportsEduroleData/Update', 'SisReportsEduroleDataManagementController@importOrUpdateSisReportsEduroleData')->name('importOrUpdatexSisReportsEduroleData.admin');     
        Route::group(['prefix' => 'user'], function () {

            Route::get('/user/searchForUser', 'UserController@searchForUser')->name('users.searchForUser');
            Route::get('', 'UserController@index')->name('users.index');
            Route::get('/create', 'UserController@create')->name('users.create');
            Route::post('/store', 'UserController@store')->name('users.store');
            Route::get('/{user}/show', 'UserController@show')->name('users.show');
            Route::get('/{user}/edit', 'UserController@edit')->name('users.edit');
            Route::post('/{user}/resetUserPassword', 'UserController@resetUserPassword')->name('users.resetUserPassword');
            Route::patch('/{user}/update', 'UserController@update')->name('users.update');
            Route::post('/{user}/delete', 'UserController@destroy')->name('users.destroy');
            Route::post('/{user}/resetPassword', 'UserController@resetPassword')->name('admin.resetPassword');
            Route::post('/import/Students', 'StudentsController@importStudentsFromBasicInformation')->name('students.import');
            Route::get('/import/single/students', 'StudentsController@importSingleStudent')->name('students.importSingleStudent');
            Route::post('/upload/single/students', 'StudentsController@uploadSingleStudent')->name('students.uploadSingleStudent');
            Route::get('/index/viewStudents/{id?}', 'StudentsController@viewAllStudents')->name('students.index');
            Route::get('/viewStudents/showStudent/{studentNumber}', 'StudentsController@registerStudent')->name('students.showStudent');
            Route::post('/viewStudents/submitRegistration', 'StudentsController@adminSubmitCourses')->name('sumbitRegistration.student');
            Route::DELETE('/viewStudents/deleteEntireRegistration', 'StudentsController@deleteEntireRegistration')->name('deleteEntireRegistration.student');
            Route::DELETE('/viewStudents/deleteCourseInRegistration', 'StudentsController@deleteCourseInRegistration')->name('deleteCourseInRegistration.student');
            Route::DELETE('/viewStudents/deleteCourseFromNMCZCourses', 'StudentsController@deleteCourseFromNMCZCourses')->name('deleteCourseFromNMCZCourses.student');
            Route::GET('/viewStudents/printIDCard/{studentId}', 'StudentsController@printIDCard')->name('printIDCard.student');
            
            Route::post('/importStudentsToMoodle', 'StudentsController@bulkEnrollOnMooodle')->name('bulkEnrollOnMooodle');
            Route::post('/importStudentsFromEduroleToMoodle', 'StudentsController@bulkEnrollFromEduroleOnMooodle')->name('bulkEnrollFromEduroleOnMooodle');

            // Moodle Status Dashboard Routes
            Route::get('/moodle/status', 'MoodleStatusController@index')->name('moodle.status');
            Route::get('/moodle/check-student/{studentId}', 'MoodleStatusController@checkStudentStatus')->name('moodle.check-student');

        });
        
        Route::middleware('can:Administrator')->group(function () {   
            Route::get('/importOrUpdateSisReportsEduroleData/Update', [SisReportsEduroleDataManagementController::class, 'importOrUpdateSisReportsEduroleData'])->name('importOrUpdatexSisReportsEduroleData.admin');     
            Route::get('/importOrUpdateMoodleWithEduroleData/Update', [SisReportsEduroleDataManagementController::class, 'importOrUpdateMoodleWithEduroleData'])->name('importOrUpdateMoodleWithEduroleData.admin');     
            
            Route::group(['prefix' => 'user'], function () {
                Route::get('/user/searchForUser', [UserController::class, 'searchForUser'])->name('users.searchForUser');
                Route::get('', [UserController::class, 'index'])->name('users.index');
                Route::get('/create', [UserController::class, 'create'])->name('users.create');
                Route::post('/store', [UserController::class, 'store'])->name('users.store');
                Route::get('/{user}/show', [UserController::class, 'show'])->name('users.show');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
                Route::post('/{user}/resetUserPassword', [UserController::class, 'resetUserPassword'])->name('users.resetUserPassword');
                Route::patch('/{user}/update', [UserController::class, 'update'])->name('users.update');
                Route::post('/{user}/delete', [UserController::class, 'destroy'])->name('users.destroy');
                Route::post('/{user}/resetPassword', [UserController::class, 'resetPassword'])->name('admin.resetPassword');
                
                // Student related routes
                Route::post('/import/Students', [StudentsController::class, 'importStudentsFromBasicInformation'])->name('students.import');
                Route::post('/import/importStudentsFromLMMAX', [StudentsController::class, 'importStudentsFromLMMAX'])->name('students.importStudentsFromLMMAX');
                Route::get('/import/single/students', [StudentsController::class, 'importSingleStudent'])->name('students.importSingleStudent');
                Route::post('/upload/single/students', [StudentsController::class, 'uploadSingleStudent'])->name('students.uploadSingleStudent');
                Route::get('/index/viewStudents/{id?}', [StudentsController::class, 'viewAllStudents'])->name('students.index');
                Route::get('/viewStudents/showStudent/{studentNumber}', [StudentsController::class, 'registerStudent'])->name('students.showStudent');
                Route::post('/viewStudents/submitRegistration', [StudentsController::class, 'adminSubmitCourses'])->name('sumbitRegistration.student');
                Route::delete('/viewStudents/deleteEntireRegistration', [StudentsController::class, 'deleteEntireRegistration'])->name('deleteEntireRegistration.student');
                Route::delete('/viewStudents/deleteCourseInRegistration', [StudentsController::class, 'deleteCourseInRegistration'])->name('deleteCourseInRegistration.student');
                Route::delete('/viewStudents/deleteCourseFromNMCZCourses', [StudentsController::class, 'deleteCourseFromNMCZCourses'])->name('deleteCourseFromNMCZCourses.student');
                Route::get('/viewStudents/printIDCard/{studentId}', [StudentsController::class, 'printIDCard'])->name('printIDCard.student');
                Route::get('/viewStudents/studentNurandMid/{studentId}', [StudentsController::class, 'printIDCardStudentNurandMid'])->name('printIDCard.studentNurandMid');
                
                Route::post('/importStudentsToMoodle', [StudentsController::class, 'bulkEnrollOnMooodle'])->name('bulkEnrollOnMooodle');
                Route::post('/importStudentsFromEduroleToMoodle', [StudentsController::class, 'bulkEnrollFromEduroleOnMooodle'])->name('bulkEnrollFromEduroleOnMooodle');
            });
        });

        Route::middleware('can:Dosa')->group(function () {
            Route::prefix('dosa')->group(function () {
                Route::get('/registrationCheck', [AcademicQueriesController::class, 'registrationCheck'])->name('academics.registrationCheck');
            });
        });

        Route::middleware('can:Student')->group(function () {
            Route::prefix('student')->group(function () {
                Route::get('/viewDocket', [StudentsController::class, 'viewDocket'])->name('student.viewDocket');
                
                Route::get('/viewSupplementaryDocket/{studentId?}', [StudentsController::class, 'viewSupplementaryDocket'])->name('student.viewSupplementaryDocket');
                Route::get('/viewResults', [StudentsController::class, 'viewResults'])->name('student.viewResults');
                Route::get('/coursesRegistration/{studentId}', [StudentsController::class, 'studentRegisterForCourses'])->name('student.coursesRegistration');
                Route::get('/nmczRegistration/{id?}', [StudentsController::class, 'studentNMCZRegisterForRepeatCourses'])->name('nmcz.registration');
                Route::post('/submitCourseRegistration', [StudentsController::class, 'studentSubmitCourseRegistration'])->name('student.submitCourseRegistration');
            });
        });

        Route::middleware('can:Examination')->group(function () {
            Route::prefix('nurAndMid')->group(function () {
                Route::get('/importNurAndMid', [NursingAndMidwiferyController::class, 'import'])->name('nurAndMid.import');
                Route::post('/uploadNurAndMid', [NursingAndMidwiferyController::class, 'uploadStudents'])->name('nurAndMid.uploadStudents');
                Route::get('/viewNurAndMid', [NursingAndMidwiferyController::class, 'viewStudents'])->name('nurAndMid.viewStudents');
                Route::get('/showNurAndMid/{id}', [NursingAndMidwiferyController::class, 'showStudents'])->name('nurAndMid.showStudent');
            });

            Route::prefix('docket')->group(function () {
                Route::get('/index/{id?}', [DocketController::class, 'index'])->name('docket.index');
                Route::get('/indexSupsAndDef/{id?}', [DocketController::class, 'indexSupsAndDef'])->name('docket.indexSupsAndDef');
                Route::get('/exportAppealingStudents', [DocketController::class, 'exportAppealStudents'])->name('docket.exportAppealStudents');
                Route::get('/sendEmailNotice', [DocketController::class, 'sendEmailNotice'])->name('docket.sendEmailNotice');
                Route::get('/docketIndexNmcz/{id?}', [DocketController::class, 'indexNmcz'])->name('docket.indexNmcz');
                Route::get('/docketIndexNmczRepeating/{id?}', [DocketController::class, 'indexNmczRepeating'])->name('docket.indexNmczRepeating');
                Route::get('/import', [DocketController::class, 'import'])->name('docket.import');
                Route::get('/importNMCZRepeat', [DocketController::class, 'nmczRepeatImport'])->name('docket.nmczRepeatImport');
                Route::get('/importSupsAndDef', [DocketController::class, 'importSupsAndDef'])->name('docket.importSupsAndDef');
                Route::get('/importNmcz', [DocketController::class, 'importNmcz'])->name('docket.importNmcz');
                Route::get('/showStudent/{studentNumber}', [DocketController::class, 'showStudentDocket'])->name('docket.showStudent');
                Route::get('/showStudentNmcz/{studentNumber}', [DocketController::class, 'showStudentNmcz'])->name('docket.showStudentNmcz');
                Route::post('/upload', [DocketController::class, 'uploadStudents'])->name('import.students');
                Route::post('/uploadNMCZRepeatStudents', [DocketController::class, 'uploadNMCZRepeatStudents'])->name('import.uploadNMCZRepeatStudents');
                Route::post('/uploadSupsAndDef', [DocketController::class, 'uploadStudentsSupsAndDef'])->name('import.studentsSupsAndDef');
                Route::post('/uploadNmcz', [DocketController::class, 'uploadStudentsNmcz'])->name('import.studentsNmcz');
                Route::post('/updateCourses/{studentId}', [DocketController::class, 'updateCoursesForStudent'])->name('update.courses');
                Route::get('/importCourses', [DocketController::class, 'importCourseFromSis'])->name('courses.import');
                Route::get('/addCourses/{studentId}', [DocketController::class, 'selectCourses'])->name('courses.select');
                Route::post('/storeCourses/{studentId}', [DocketController::class, 'storeCourses'])->name('courses.store');
                Route::get('/viewExaminationList/{coursedId}', [DocketController::class, 'viewExaminationList'])->name('courses.examlist');
                Route::get('/exportListExamList/{coursedId}', [DocketController::class, 'exportListExamList'])->name('courses.exportListExamList');
                Route::get('/assignStudentsRoles', [DocketController::class, 'assignStudentsRoles'])->name('docket.assignStudentsRoles');
                Route::get('/resetAllStudentsPasswords', [DocketController::class, 'resetAllStudentsPasswords'])->name('docket.resetAllStudentsPassword');
                Route::get('/createAccountsForStudentsNotInUsersTableAndSendEmails', [DocketController::class, 'createAccountsForStudentsNotInUsersTableAndSendEmails'])->name('docket.createAccountsForStudentsNotInUsersTableAndSendEmails');
                Route::get('/updateNameInUsersTableToMatchStudentIdCollectedFromBasicInformationUsingEmail', [DocketController::class, 'updateNameInUsersTableToMatchStudentIdCollectedFromBasicInformationUsingEmail'])->name('docket.updateNameInUsersTableToMatchStudentIdCollectedFromBasicInformationUsingEmail');
                Route::get('/exportCoursesToPdfWithStudentsTakingThem/{courseID}', [DocketController::class, 'exportCoursesToPdfWithStudentsTakingThem'])->name('docket.exportCoursesToPdfWithStudentsTakingThem');
                Route::get('/bulkExportAllCoursesToPdfWithStudentsTakingThem', [DocketController::class, 'bulkExportAllCoursesToPdfWithStudentsTakingThem'])->name('docket.bulkExportAllCoursesToPdfWithStudentsTakingThem');
                Route::get('/resetStudent/{studentNumber}', [DocketController::class, 'resetStudent'])->name('docket.resetStudent');
            });
        });

        Route::middleware('can:Academics')->group(function () {        
            Route::prefix('academics')->group(function () {
                Route::get('/index', [AcademicQueriesController::class, 'index'])->name('academics.index');            
                Route::get('/viewAllCoursesWithResults', [AcademicQueriesController::class, 'viewAllCoursesWithResults'])->name('viewAllCoursesWithResults');
                Route::get('/exportAllCoursesWithResults', [AcademicQueriesController::class, 'exportAllCoursesWithResults'])->name('exportAllCoursesWithResults');
                Route::get('/manageAdmissions', [AcademicQueriesController::class, 'manageAdmissions'])->name('academics.ManageAdmissions');
                Route::get('/viewAllStudentsRegisteredInASpecificAcademicYear', [AcademicQueriesController::class, 'viewAllStudentsRegisteredInASpecificAcademicYear'])->name('viewAllStudentsRegisteredInASpecificAcademicYear');
                Route::get('/exportAllStudentsRegisteredInASpecificAcademicYear/{academicYear}', [AcademicQueriesController::class, 'exportAllStudentsRegisteredInASpecificAcademicYear'])->name('exportAllStudentsRegisteredInASpecificAcademicYear');
                Route::get('/academicsEmailAnnouncement', [AcademicQueriesController::class, 'emailAnnouncement'])->name('academics.EmailAnnouncement');
                Route::get('/viewAllProgrammesPerSchool', [AcademicQueriesController::class, 'viewAllProgrammesPerSchool'])->name('viewAllProgrammesPerSchool');
                Route::get('/exportAllProgrammesPerSchool/{schoolName}', [AcademicQueriesController::class, 'exportAllProgrammesPerSchool'])->name('exportAllProgrammesPerSchool');
                Route::get('/viewStudentsFromSpecificIntakeYearTakingAProgramme', [AcademicQueriesController::class, 'viewStudentsFromSpecificIntakeYearTakingAProgramme'])->name('viewStudentsFromSpecificIntakeYearTakingAProgramme');
                Route::get('/exportStudentsFromSpecificIntakeYearTakingAProgramme/{intakeName}/{programmeName}', [AcademicQueriesController::class, 'exportStudentsFromSpecificIntakeYearTakingAProgramme'])->name('exportStudentsFromSpecificIntakeYearTakingAProgramme');
                Route::get('/viewRegisteredStudentsFromSpecificIntakeYearTakingAProgramme', [AcademicQueriesController::class, 'viewRegisteredStudentsFromSpecificIntakeYearTakingAProgramme'])->name('viewRegisteredStudentsFromSpecificIntakeYearTakingAProgramme');
                Route::get('/exportRegisteredStudentsFromSpecificIntakeYearTakingAProgramme/{intakeName}/{programmeName}', [AcademicQueriesController::class, 'exportRegisteredStudentsFromSpecificIntakeYearTakingAProgramme'])->name('exportRegisteredStudentsFromSpecificIntakeYearTakingAProgramme');
                Route::get('/viewRegisteredStudentsPerYearInYearOfStudy', [AcademicQueriesController::class, 'viewRegisteredStudentsPerYearInYearOfStudy'])->name('viewRegisteredStudentsPerYearInYearOfStudy');
                Route::get('/exportRegisteredStudentsPerYearInYearOfStudy/{yearOfStudy}/{academicYear}', [AcademicQueriesController::class, 'exportRegisteredStudentsPerYearInYearOfStudy'])->name('exportRegisteredStudentsPerYearInYearOfStudy');
                Route::get('/viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy', [AcademicQueriesController::class, 'viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy'])->name('viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy');
                Route::get('/exportRegisteredStudentsAccordingToProgrammeAndYearOfStudy/{yearOfStudy}/{academicYear}/{programmeName}', [AcademicQueriesController::class, 'exportRegisteredStudentsAccordingToProgrammeAndYearOfStudy'])->name('exportRegisteredStudentsAccordingToProgrammeAndYearOfStudy');
                Route::get('/viewRegisteredAndUnregisteredPerYear', [AcademicQueriesController::class, 'viewRegisteredAndUnregisteredPerYear'])->name('viewRegisteredAndUnregisteredPerYear');
                Route::get('/exportRegisteredAndUnregisteredPerYear/{academicYear}', [AcademicQueriesController::class, 'exportRegisteredAndUnregisteredPerYear'])->name('exportRegisteredAndUnregisteredPerYear');
                Route::get('/viewAllCoursesAttachedToProgramme', [AcademicQueriesController::class, 'viewAllCoursesAttachedToProgramme'])->name('viewAllCoursesAttachedToProgramme');
                Route::get('/exportAllCoursesAttachedToProgramme', [AcademicQueriesController::class, 'exportAllCoursesAttachedToProgramme'])->name('exportAllCoursesAttachedToProgramme');
                Route::get('/viewUnregisteredStudentsEligibleForRegistration', [AcademicQueriesController::class, 'viewUnregisteredStudentsEligibleForRegistration'])->name('viewUnregisteredStudentsEligibleForRegistration');
                Route::get('/examMdificationAuditTrail', [AcademicQueriesController::class, 'examMdificationAuditTrail'])->name('examMdificationAuditTrail');
                Route::get('/viewStudentsUnderNaturalScienceSchool', [AcademicQueriesController::class, 'viewStudentsUnderNaturalScienceSchool'])->name('viewStudentsUnderNaturalScienceSchool');
                Route::post('/exportStudentsUnderNaturalScienceSchool', [AcademicQueriesController::class, 'exportStudentsUnderNaturalScienceSchool'])->name('exportStudentsUnderNaturalScienceSchool');
                Route::get('/gradesArchiveImport', [AcademicQueriesController::class, 'gradesArchiveImport'])->name('academics.GradesArchiveImport');
                Route::get('/gradesArchiveView', [AcademicQueriesController::class, 'gradesArchiveView'])->name('academics.GradesArchiveView');
                Route::post('/uploadGradesToArchive', [AcademicQueriesController::class, 'uploadGradesToArchive'])->name('academics.UploadradesToArchive');
                Route::get('/showResultsArchived/{studentID}', [AcademicQueriesController::class, 'showStudentsArchivedResults'])->name('archivedResults.showStudent');
                Route::get('/getProgrammesBySchool', [AcademicQueriesController::class, 'getProgrammesBySchoolDynamicForm'])->name('getProgrammesBySchoolDynamicForm');
            });
        });

        Route::middleware('can:Finance')->group(function () {
            Route::prefix('finance')->group(function () {
                Route::get('/index', [FinanceQueriesController::class, 'index'])->name('finance.index');
                Route::get('/viewSumOfAllTransactionsOfEachStudent', [FinanceQueriesController::class, 'viewSumOfAllTransactionsOfEachStudent'])->name('viewSumOfAllTransactionsOfEachStudent');
                Route::get('/exportAllPaymentInformation', [FinanceQueriesController::class, 'exportAllPaymentInformation'])->name('exportAllPaymentInformation');
                Route::get('/viewInvoicesPerProgramme', [FinanceQueriesController::class, 'viewInvoicesPerProgramme'])->name('finance.ViewInvoicesPerProgramme');
                Route::get('/exportAllProgrammeInvoices', [FinanceQueriesController::class, 'exportAllProgrammeInvoices'])->name('finance.ExportAllProgrammeInvoices');
            });
        });
    });
});
