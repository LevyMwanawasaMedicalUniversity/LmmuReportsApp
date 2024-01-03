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

Auth::routes();
Route::get('/verify/{studentNumber}', 'App\Http\Controllers\DocketController@verifyStudent')->name('docket.verify');
Route::get('/verifyNmcz/{studentNumber}', 'App\Http\Controllers\DocketController@verifyStudentNmcz')->name('docket.verifyNmcz');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/send-test-email/{id}', 'App\Http\Controllers\EmailController@sendTestEmail');
Auth::routes();

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
                Route::patch('/{user}/update', 'UserController@update')->name('users.update');
                Route::post('/{user}/delete', 'UserController@destroy')->name('users.destroy');
                Route::post('/{user}/resetPassword', 'UserController@resetPassword')->name('admin.resetPassword');
                
            });
            
        
        });

        Route::middleware('can:Examination')->group(function () {
            Route::prefix('docket')->group(function () {
                Route::get('/index/{id?}', 'DocketController@index')->name('docket.index');
                Route::get('/exportAppealingStudents', 'DocketController@exportAppealStudents')->name('docket.exportAppealStudents');
                // Route::get('/sendEmailNotice', 'DocketController@sendEmailNotice')->name('docket.sendEmailNotice');
                Route::get('/docket.indexNmcz/{id?}', 'DocketController@indexNmcz')->name('docket.indexNmcz');
                Route::get('/import', 'DocketController@import')->name('docket.import');
                Route::get('/importNmcz', 'DocketController@importNmcz')->name('docket.importNmcz');
                Route::get('/showStudent/{studentNumber}', 'DocketController@showStudent')->name('docket.showStudent');
                Route::get('/showStudentNmcz/{studentNumber}', 'DocketController@showStudentNmcz')->name('docket.showStudentNmcz');
                Route::post('/upload', 'DocketController@uploadStudents')->name('import.students');
                Route::post('/uploadNmcz', 'DocketController@uploadStudentsNmcz')->name('import.studentsNmcz');
                Route::post('/updateCourses/{studentId}', 'DocketController@updateCoursesForStudent')->name('update.courses');
                Route::get('/importCourses', 'DocketController@importCourseFromSis')->name('courses.import');
                Route::get('/addCourses/{studentId}', 'DocketController@selectCourses')->name('courses.select');
                Route::post('/storeCourses/{studentId}', 'DocketController@storeCourses')->name('courses.store');
                Route::get('/viewExaminationList/{coursedId}', 'DocketController@viewExaminationList')->name('courses.examlist');
                Route::get('/exportListExamList/{coursedId}', 'DocketController@exportListExamList')->name('courses.exportListExamList');
                Route::get('/resetAllStudentsPasswords', 'DocketController@resetAllStudentsPasswords')->name('docket.resetAllStudentsPassword');
                Route::get('createAccountsForStudentsNotInUsersTableAndSendEmails', 'DocketController@createAccountsForStudentsNotInUsersTableAndSendEmails')->name('docket.createAccountsForStudentsNotInUsersTableAndSendEmails');
    
    
                
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


                
                Route::GET('/viewUnregisteredStudentsEligibleForRegistration',  'AcademicQueriesController@viewUnregisteredStudentsEligibleForRegistration')->name('viewUnregisteredStudentsEligibleForRegistration');
                


                //dynamic drop down view students specific intake taking programme
                Route::get('/getProgrammesBySchool',  'AcademicQueriesController@getProgrammesBySchoolDynamicForm')->name('getProgrammesBySchoolDynamicForm'); 
                        
            });
        });
        Route::middleware('can:Finance')->group(function () {

            Route::prefix('finance')->group(function () {
                Route::get('/index', 'FinanceQueriesController@index')->name('finance.index');

                Route::get('/viewSumOfAllTransactionsOfEachStudent', 'FinanceQueriesController@viewSumOfAllTransactionsOfEachStudent')->name('viewSumOfAllTransactionsOfEachStudent');
            });
        });

        

        
    });
            
});

