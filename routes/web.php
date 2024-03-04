<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);
Route::get('/verify/{studentNumber}', 'App\Http\Controllers\DocketController@verifyStudent')->name('docket.verify');
Route::get('/verifyNmcz/{studentNumber}', 'App\Http\Controllers\DocketController@verifyStudentNmcz')->name('docket.verifyNmcz');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/send-test-email/{id}', 'App\Http\Controllers\EmailController@sendTestEmail');


Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
	Route::resource('user', 'App\Http\Controllers\UserController');
    Route::resource('roles', 'App\Http\Controllers\RolesController');
    Route::resource('permissions', 'App\Http\Controllers\PermissionsController');
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);
    Route::get('{page}', ['as' => 'page.index', 'uses' => 'App\Http\Controllers\PageController@index']);
    Route::get('/students/exam/results/{studentNumber}', 'App\Http\Controllers\DocketController@students2023ExamResults')->name('docket.students2023ExamResults');

    Route::group(['namespace' => 'App\Http\Controllers'], function () {
	Route::get('/report', 'ReportController@registeredStudents')->name('registered.students');
        Route::get('/export-data', 'ReportController@exportRegisteredStudents')->name('export.data');

        Route::middleware('can:Administrator')->group(function () {        
            Route::group(['prefix' => 'user'], function () {
                Route::get('', 'UserController@index')->name('users.index');
                Route::get('/create', 'UserController@create')->name('users.create');
                Route::post('/store', 'UserController@store')->name('users.store');
                Route::get('/{user}/show', 'UserController@show')->name('users.show');
                Route::get('/{user}/edit', 'UserController@edit')->name('users.edit');
                Route::post('/{user}/resetUserPassword', 'UserController@resetUserPassword')->name('users.resetUserPassword');
                Route::patch('/{user}/update', 'UserController@update')->name('users.update');
                Route::post('/{user}/delete', 'UserController@destroy')->name('users.destroy');
                Route::post('/{user}/resetPassword', 'UserController@resetPassword')->name('admin.resetPassword');
                Route::get('/import/Students', 'StudentsController@importStudentsFromBasicInformation')->name('students.import');
                Route::get('/index/viewStudents/{id?}', 'StudentsController@viewAllStudents')->name('students.index');
                Route::get('/viewStudents/showStudent/{studentNumber}', 'StudentsController@showStudent')->name('students.showStudent');
            });
            
        
        });

        Route::middleware('can:Dosa')->group(function () {
            
            Route::prefix('dosa')->group(function () {
                Route::get('/registrationCheck', 'AcademicQueriesController@registrationCheck')->name('academics.registrationCheck');
            });

        });

        Route::middleware('can:Student')->group(function () {
            
            Route::prefix('student')->group(function () {
                Route::get('/viewDocket', 'StudentsController@viewDocket')->name('student.viewDocket');
                Route::get('/viewResults', 'StudentsController@viewResults')->name('student.viewResults');
                Route::get('/coursesRegistration/{studentId}', 'StudentsController@studentRegisterForCourses')->name('student.coursesRegistration');
                Route::post('/submitCourseRegistration', 'StudentsController@submitCourseRegistration')->name('student.submitCourseRegistration');
            });

        });

        Route::middleware('can:Examination')->group(function () {
            Route::prefix('docket')->group(function () {
                Route::get('/index/{id?}', 'DocketController@index')->name('docket.index');
                Route::get('/indexSupsAndDef/{id?}', 'DocketController@indexSupsAndDef')->name('docket.indexSupsAndDef');
                Route::get('/exportAppealingStudents', 'DocketController@exportAppealStudents')->name('docket.exportAppealStudents');
                // Route::get('/sendEmailNotice', 'DocketController@sendEmailNotice')->name('docket.sendEmailNotice');
                Route::get('/docket.indexNmcz/{id?}', 'DocketController@indexNmcz')->name('docket.indexNmcz');
                Route::get('/import', 'DocketController@import')->name('docket.import');
                
                Route::get('/importSupsAndDef', 'DocketController@importSupsAndDef')->name('docket.importSupsAndDef');
                Route::get('/importNmcz', 'DocketController@importNmcz')->name('docket.importNmcz');
                Route::get('/showStudent/{studentNumber}', 'DocketController@showStudent')->name('docket.showStudent');
                Route::get('/showStudentNmcz/{studentNumber}', 'DocketController@showStudentNmcz')->name('docket.showStudentNmcz');
                Route::post('/upload', 'DocketController@uploadStudents')->name('import.students');
                Route::post('/uploadSupsAndDef', 'DocketController@uploadStudentsSupsAndDef')->name('import.studentsSupsAndDef');
                Route::post('/uploadNmcz', 'DocketController@uploadStudentsNmcz')->name('import.studentsNmcz');
                Route::post('/updateCourses/{studentId}', 'DocketController@updateCoursesForStudent')->name('update.courses');
                Route::get('/importCourses', 'DocketController@importCourseFromSis')->name('courses.import');
                Route::get('/addCourses/{studentId}', 'DocketController@selectCourses')->name('courses.select');
                Route::post('/storeCourses/{studentId}', 'DocketController@storeCourses')->name('courses.store');
                Route::get('/viewExaminationList/{coursedId}', 'DocketController@viewExaminationList')->name('courses.examlist');
                Route::get('/exportListExamList/{coursedId}', 'DocketController@exportListExamList')->name('courses.exportListExamList');
                Route::get('/assignStudentsRoles', 'DocketController@assignStudentsRoles')->name('docket.assignStudentsRoles');
                Route::get('/resetAllStudentsPasswords', 'DocketController@resetAllStudentsPasswords')->name('docket.resetAllStudentsPassword');
                Route::get('/createAccountsForStudentsNotInUsersTableAndSendEmails', 'DocketController@createAccountsForStudentsNotInUsersTableAndSendEmails')->name('docket.createAccountsForStudentsNotInUsersTableAndSendEmails');
                Route::get('updateNameInUsersTableToMatchStudentIdCollectedFromBasicInformationUsingEmail', 'DocketController@updateNameInUsersTableToMatchStudentIdCollectedFromBasicInformationUsingEmail')->name('docket.updateNameInUsersTableToMatchStudentIdCollectedFromBasicInformationUsingEmail');
                Route::get('/exportCoursesToPdfWithStudentsTakingThem/{courseID}', 'DocketController@exportCoursesToPdfWithStudentsTakingThem')->name('docket.exportCoursesToPdfWithStudentsTakingThem');
                Route::get('bulkExportAllCoursesToPdfWithStudentsTakingThem', 'DocketController@bulkExportAllCoursesToPdfWithStudentsTakingThem')->name('docket.bulkExportAllCoursesToPdfWithStudentsTakingThem');
                Route::get('resetStudent/{studentNumber}', 'DocketController@resetStudent')->name('docket.resetStudent');
                
            });

        });

        Route::middleware('can:Academics')->group(function () {
            
            Route::prefix('academics')->group(function () {
                Route::get('/index', 'AcademicQueriesController@index')->name('academics.index');

                

                
                Route::GET('/viewAllCoursesWithResults',  'AcademicQueriesController@viewAllCoursesWithResults')->name('viewAllCoursesWithResults');
                Route::get('/exportAllCoursesWithResults', 'AcademicQueriesController@exportAllCoursesWithResults')->name('exportAllCoursesWithResults');

                Route::GET('/viewAllStudentsRegisteredInASpecificAcademicYear',  'AcademicQueriesController@viewAllStudentsRegisteredInASpecificAcademicYear')->name('viewAllStudentsRegisteredInASpecificAcademicYear');
                Route::get('/exportAllStudentsRegisteredInASpecificAcademicYear/{academicYear}', 'AcademicQueriesController@exportAllStudentsRegisteredInASpecificAcademicYear')->name('exportAllStudentsRegisteredInASpecificAcademicYear');

                Route::GET('/viewAllProgrammesPerSchool',  'AcademicQueriesController@viewAllProgrammesPerSchool')->name('viewAllProgrammesPerSchool');
                Route::get('/exportAllProgrammesPerSchool/{schoolName}', 'AcademicQueriesController@exportAllProgrammesPerSchool')->name('exportAllProgrammesPerSchool');

                Route::GET('/viewStudentsFromSpecificIntakeYearTakingAProgramme',  'AcademicQueriesController@viewStudentsFromSpecificIntakeYearTakingAProgramme')->name('viewStudentsFromSpecificIntakeYearTakingAProgramme');
                Route::get('/exportStudentsFromSpecificIntakeYearTakingAProgramme/{intakeName}/{programmeName}', 'AcademicQueriesController@exportStudentsFromSpecificIntakeYearTakingAProgramme')->name('exportStudentsFromSpecificIntakeYearTakingAProgramme');

                Route::GET('/viewRegisteredStudentsFromSpecificIntakeYearTakingAProgramme',  'AcademicQueriesController@viewRegisteredStudentsFromSpecificIntakeYearTakingAProgramme')->name('viewRegisteredStudentsFromSpecificIntakeYearTakingAProgramme');
                Route::get('/exportRegisteredStudentsFromSpecificIntakeYearTakingAProgramme/{intakeName}/{programmeName}', 'AcademicQueriesController@exportRegisteredStudentsFromSpecificIntakeYearTakingAProgramme')->name('exportRegisteredStudentsFromSpecificIntakeYearTakingAProgramme');
                
                Route::GET('/viewRegisteredStudentsPerYearInYearOfStudy',  'AcademicQueriesController@viewRegisteredStudentsPerYearInYearOfStudy')->name('viewRegisteredStudentsPerYearInYearOfStudy');
                Route::GET('/exportRegisteredStudentsPerYearInYearOfStudy/{yearOfStudy}/{academicYear}',  'AcademicQueriesController@exportRegisteredStudentsPerYearInYearOfStudy')->name('exportRegisteredStudentsPerYearInYearOfStudy');

                Route::GET('/viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy',  'AcademicQueriesController@viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy')->name('viewRegisteredStudentsAccordingToProgrammeAndYearOfStudy');
                Route::GET('/exportRegisteredStudentsAccordingToProgrammeAndYearOfStudy/{yearOfStudy}/{academicYear}/{programmeName}',  'AcademicQueriesController@exportRegisteredStudentsAccordingToProgrammeAndYearOfStudy')->name('exportRegisteredStudentsAccordingToProgrammeAndYearOfStudy');

                Route::GET('/viewRegisteredAndUnregisteredPerYear',  'AcademicQueriesController@viewRegisteredAndUnregisteredPerYear')->name('viewRegisteredAndUnregisteredPerYear');
                Route::GET('/exportRegisteredAndUnregisteredPerYear/{academicYear}',  'AcademicQueriesController@exportRegisteredAndUnregisteredPerYear')->name('exportRegisteredAndUnregisteredPerYear');

                Route::GET('/viewAllCoursesAttachedToProgramme',  'AcademicQueriesController@viewAllCoursesAttachedToProgramme')->name('viewAllCoursesAttachedToProgramme');
                Route::GET('/exportAllCoursesAttachedToProgramme',  'AcademicQueriesController@exportAllCoursesAttachedToProgramme')->name('exportAllCoursesAttachedToProgramme');
                
                Route::GET('/viewUnregisteredStudentsEligibleForRegistration',  'AcademicQueriesController@viewUnregisteredStudentsEligibleForRegistration')->name('viewUnregisteredStudentsEligibleForRegistration');

                Route::get('/examMdificationAuditTrail', 'AcademicQueriesController@examMdificationAuditTrail')->name('examMdificationAuditTrail');

                Route::GET('/viewStudentsUnderNaturalScienceSchool',  'AcademicQueriesController@viewStudentsUnderNaturalScienceSchool')->name('viewStudentsUnderNaturalScienceSchool');
                Route::POST('/exportStudentsUnderNaturalScienceSchool',  'AcademicQueriesController@exportStudentsUnderNaturalScienceSchool')->name('exportStudentsUnderNaturalScienceSchool');
                
                Route::get('/gradesArchiveImport',  'AcademicQueriesController@gradesArchiveImport')->name('academics.GradesArchiveImport');
                Route::get('/gradesArchiveView',  'AcademicQueriesController@gradesArchiveView')->name('academics.GradesArchiveView');
                Route::post('/uploadGradesToArchive',  'AcademicQueriesController@uploadGradesToArchive')->name('academics.UploadradesToArchive');
                
                Route::get('/showResultsArchived/{studentID}',  'AcademicQueriesController@showStudentsArchivedResults')->name('archivedResults.showStudent');
                Route::get('/index', 'AcademicQueriesController@index')->name('academics.index');
                //dynamic drop down view students specific intake taking programme
                Route::get('/getProgrammesBySchool',  'AcademicQueriesController@getProgrammesBySchoolDynamicForm')->name('getProgrammesBySchoolDynamicForm'); 
                        
            });
        });
        Route::middleware('can:Finance')->group(function () {

            Route::prefix('finance')->group(function () {
                Route::get('/index', 'FinanceQueriesController@index')->name('finance.index');
                Route::get('/viewSumOfAllTransactionsOfEachStudent', 'FinanceQueriesController@viewSumOfAllTransactionsOfEachStudent')->name('viewSumOfAllTransactionsOfEachStudent');
                Route::get('/exportAllPaymentInformation', 'FinanceQueriesController@exportAllPaymentInformation')->name('exportAllPaymentInformation');
                Route::get('/viewInvoicesPerProgramme','FinanceQueriesController@viewInvoicesPerProgramme')->name('finance.ViewInvoicesPerProgramme');
                Route::get('/exportAllProgrammeInvoices','FinanceQueriesController@exportAllProgrammeInvoices')->name('finance.ExportAllProgrammeInvoices');
                   
            });
        });

        

        
    });
            
});

