<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');



Route::get('user', 'UserController@index');
Route::get('user/{id}', 'UserController@getUser');
Route::post('user/{id}', 'UserController@updateUser');
Route::post('user/delete/{id}', 'UserController@deleteUser')->name('user.delete');
Route::get('register', 'UserController@register');
Route::post('register', 'UserController@addUser');
Route::get('login', 'UserController@login')->name('login');
Route::post('login', 'UserController@login');
Route::get('logout', 'UserController@logout');

//
Route::resource('category', 'CategoryController');
Route::get('/manage/{key}', 'CategoryController@index');
Route::get('manage/{category}', 'CategoryController@manage');
Route::get('category/getdetails/{key}/{id}', 'CategoryController@getDetails');
Route::get('category/getsubvalues/{id}', 'CategoryController@getSubvalues');
Route::get('categories', 'CategoryController@categories');
Route::post('categories', 'CategoryController@setCategory');

//
Route::resource('employee', 'EmployeeController');
Route::get('employees', 'EmployeeController@index');
Route::get('employee/new', 'EmployeeController@show')->name('employee.new');
Route::post('employee/deleteall', 'EmployeeController@deleteAll')->name('employee.deleteall');
Route::post('employee/deleteallinactive', 'EmployeeController@deleteAllInactive')->name('employee.deleteallinactive');
Route::get('employee/view/{id}', 'EmployeeController@view');
Route::get('employee/get/{id}', 'EmployeeController@getEmployeeJson');
Route::get('employee/download/{filename}', 'EmployeeController@downloadFile');
Route::get('employee/downloadall/{employeeId}', 'EmployeeController@downloadAllFiles');
Route::get('employee/{id}', 'EmployeeController@show');
Route::post('employee/{id}', 'EmployeeController@update');
Route::post('employee/delete/{id}', 'EmployeeController@destroy')->name('employee.delete');
Route::post('employee/updateImage/{id}', 'EmployeeController@updateImage');
Route::post('employee/deleteImage/{id}', 'EmployeeController@deleteImage');
Route::post('employee/getbasic/{id}', 'EmployeeController@getEmployeeBasicDetails');
Route::post('employee/transferemployee/{id}', 'EmployeeController@transferEmployee');

// Application
Route::get('applicants', 'ApplicantController@index');
Route::get('application-form', 'ApplicantController@new');
Route::get('application-success', 'ApplicantController@success');
Route::post('application-form/{id}', 'ApplicantController@update');
Route::post('applicant/deleteall', 'ApplicantController@deleteAll')->name('applicant.deleteall');
Route::get('applicant/{id}', 'ApplicantController@show');
Route::get('applicant-process/{id}', 'ApplicantController@process');
Route::get('applicant-hire/{id}', 'ApplicantController@hire');
Route::delete('applicant/{id}', 'ApplicantController@destroy')->name('applicant.destroy');

// ManHour
Route::get('manhour', 'ManhourController@index');
Route::get('manhour/inputall/{date}', 'ManhourController@inputAll')->name('manhour.inputall');
Route::post('manhour/inputall', 'ManhourController@recordAll');
Route::post('manhour/inputallfilter', 'ManhourController@filterDateAll')->name('manhour.filterdateall');
Route::get('manhour/input', 'ManhourController@input')->name('manhour.input');
Route::get('manhour/search/{id}', 'ManhourController@search');
Route::get('manhour/input/{id}', 'ManhourController@input');
Route::get('manhour/getPrev/{id}', 'ManhourController@getPrev');
Route::get('manhour/getNext/{id}', 'ManhourController@getNext');
Route::post('manhour/record/{id}', 'ManhourController@record');
Route::get('manhour/getrecord/{id}/{date}', 'ManhourController@getRecord');
Route::get('manhour/dailyrecord', 'ManhourController@viewNow');
Route::post('manhour/dailyrecord', 'ManhourController@filterDate')->name('manhour.filterdate');
Route::get('manhour/dailyrecord/{mode}/{datefrom}/{dateto}', 'ManhourController@viewRange')->name('manhour.viewrange');
Route::get('manhour/viewrecord/{id}', 'ManhourController@viewRecordNow');
Route::get('manhour/viewrecord/{id}/{year}-{month}-{day}', 'ManhourController@viewRecord');
Route::post('manhour/viewrecord/{id}', 'ManhourController@setRecordDate');
Route::get('manhour/viewrecordcollated/{datefrom}/{dateto}', 'ManhourController@viewRecordCollated');
Route::post('manhour/viewrecordcollated', 'ManhourController@setRecordDateCollated');
Route::get('manhour/attendance/', 'ManhourController@viewAttendace')->name('manhour.attendance');
Route::get('manhour/defineholiday/{year}', 'ManhourController@defineHoliday');
Route::post('manhour/defineholiday/', 'ManhourController@defineHolidayPost')->name('manhour.defineholiday');
Route::get('manhour/getholiday/{date}', 'ManhourController@getHolidayOnDate');
Route::post('manhour/saveholiday', 'ManhourController@saveHoliday');
Route::post('manhour/deleteholiday', 'ManhourController@deleteHoliday');
Route::get('manhour/inputcsv', 'ManhourController@inputCsv');
Route::post('manhour/inputcsv', 'ManhourController@postCsvFile')->name('manhour.postcsv');
Route::post('manhour/postcsvrecords', 'ManhourController@postCsvRecords')->name('manhour.postcsvrecords');

// OT Request
Route::get('otrequests', 'OtRequestController@index')->name('otrequest');
Route::post('otrequests', 'OtRequestController@index')->name('otrequest.post');
Route::get('otrequest/processed/{datefrom}/{dateto}', 'OtRequestController@viewApproved')->name('otrequest.processed');
Route::post('otrequest/processed', 'OtRequestController@filterDate')->name('otrequest.gotodate');
Route::get('otrequest/new', 'OtRequestController@new')->name('otrequest.new');
Route::post('otrequest/new', 'OtRequestController@add');
Route::post('otrequest/getemployees/{dept}', 'OtRequestController@getEmployees');
Route::get('otrequest/getholidays/{date}', 'OtRequestController@getHolidays');
Route::post('otrequest/approve/{id}', 'OtRequestController@approve')->name('otrequest.approve');
Route::post('otrequest/deny/{id}', 'OtRequestController@deny')->name('otrequest.deny');
Route::post('otrequest/checkemployeerecord/{id}/{date}', 'OtRequestController@getOtRequestForEmployee');
Route::post('otrequest/batchapprove', 'OtRequestController@batchApprove')->name('otrequest.batchapprove');

// Payroll
Route::get('payroll', 'PayrollController@index');
Route::get('payroll/view13thMonthPay', 'MiscPayableController@view13thMonthPay');
Route::get('payroll/summary/{date}', 'PayrollController@summary');
Route::post('payroll/summary', 'PayrollController@goToDateSummary')->name('payroll.gotodatesummary');
Route::get('payroll/getemployees/{date}', 'PayrollController@getEmployees');
Route::get('payroll/get/{id}/{date}', 'PayrollController@getPay');
Route::get('payroll/{id}/{date}', 'PayrollController@viewPay');
Route::get('payroll/{id}', 'PayrollController@viewNow');
Route::post('payroll/{id}', 'PayrollController@setRecordDate');
Route::get('payroll/deductibles/{id}/{date}', 'PayrollController@deductibles');

// Deductible Records
Route::get('deductibles/goto/{id}', 'DeductibleRecordController@goToDate');
Route::get('deductibles/getall/{date}', 'DeductibleRecordController@getAll');
Route::get('deductibles/getalltodate', 'DeductibleRecordController@getAllOnDate');
Route::get('deductibles/viewtodate', 'DeductibleRecordController@goToDateView');
Route::get('deductibles/view/{key}/{date}', 'DeductibleRecordController@view');
Route::get('deductibles/{id}/{date}', 'DeductibleRecordController@get')->name('deductible.get');
Route::post('deductibles/autogenerate/{date}', 'DeductibleRecordController@autogenerate')->name('deductible.autogenerate');
Route::post('deductibles/{id}', 'DeductibleRecordController@add');

// Adjustments Record
Route::get('adjustments/goto/{id}', 'AdjustmentsRecordController@goToDate');
Route::get('adjustments/getall/{date}', 'AdjustmentsRecordController@getAll');
Route::get('adjustments/getalltodate/', 'AdjustmentsRecordController@getAllOnDate');
Route::get('adjustments/{id}/{date}', 'AdjustmentsRecordController@get')->name('adjustments.get');
Route::post('adjustments/{id}', 'AdjustmentsRecordController@add');

// PDF Download
Route::get('employee/view/pdf/{id}', 'EmployeeController@printEmployeeDetailsPdf')->name('Employee.printPdf');
