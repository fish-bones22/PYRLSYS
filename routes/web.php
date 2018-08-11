<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('user', 'UserController@index');
Route::get('user/{id}', 'UserController@getUser');
Route::post('user/{id}', 'UserController@updateUser');
Route::get('user/delete/{id}', 'UserController@deleteUser');
Route::get('register', 'UserController@register');
Route::post('register', 'UserController@addUser');
Route::get('login', 'UserController@login');
Route::post('login', 'UserController@login');
Route::get('logout', 'UserController@logout');


Route::resource('category', 'CategoryController');
Route::get('/manage/{key}', 'CategoryController@index');
Route::get('manage/{category}', 'CategoryController@manage');
Route::get('category/getdetails/{id}', 'CategoryController@getDetails');
Route::get('category/getsubvalues/{id}', 'CategoryController@getSubvalues');
Route::get('categories', 'CategoryController@categories');
Route::post('categories', 'CategoryController@setCategory');

// Route::resource('employee', 'EmployeeController');
Route::get('employees', 'EmployeeController@index');
Route::get('employee/new', 'EmployeeController@show');
Route::get('employee/{id}', 'EmployeeController@show');
Route::post('employee/{id}', 'EmployeeController@update');
Route::get('employee/delete/{id}', 'EmployeeController@destroy');
Route::post('employee/updateImage/{id}', 'EmployeeController@updateImage');
Route::post('employee/deleteImage/{id}', 'EmployeeController@deleteImage');
Route::post('employee/getbasic/{id}', 'EmployeeController@getEmployeeBasicDetails');

Route::get('applicants', 'ApplicantController@index');
Route::get('application-form', 'ApplicantController@new');
Route::post('application-form/{id}', 'ApplicantController@update');
Route::get('applicant/{id}', 'ApplicantController@show');
Route::get('applicant-process/{id}', 'ApplicantController@process');
Route::get('applicant-hire/{id}', 'ApplicantController@hire');
Route::delete('applicant/{id}', 'ApplicantController@destroy')->name('applicant.destroy');

Route::get('manhour/input', 'ManhourController@input');
Route::get('manhour/input/{id}', 'ManhourController@input');
Route::get('manhour/getNext/{id}', 'ManhourController@getNext');
Route::post('manhour/record/{id}', 'ManhourController@record');
Route::get('manhour', 'ManhourController@index');
Route::get('manhour/getrecord/{id}/{date}', 'ManhourController@getRecord');
Route::get('manhour/dailyrecord', 'ManhourController@viewNow');
Route::post('manhour/dailyrecord', 'ManhourController@filterDate')->name('manhour.filterdate');
Route::get('manhour/dailyrecord/{datefrom}/{dateto}', 'ManhourController@viewRange')->name('manhour.viewrange');
Route::get('manhour/viewrecord/{id}', 'ManhourController@viewRecord');

Route::get('otrequests', 'OtRequestController@index')->name('otrequest');
Route::get('otrequest/new', 'OtRequestController@new')->name('otrequest.new');
Route::post('otrequest/new', 'OtRequestController@add');
Route::post('otrequest/getemployees/{dept}', 'OtRequestController@getEmployees');
Route::post('otrequest/approve/{id}', 'OtRequestController@approve')->name('otrequest.approve');
Route::post('otrequest/deny/{id}', 'OtRequestController@deny')->name('otrequest.deny');
Route::post('otrequest/checkemployeerecord/{id}/{date}', 'OtRequestController@getOtRequestForEmployee');
