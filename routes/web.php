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
Route::get('categories', 'CategoryController@categories');
Route::post('categories', 'CategoryController@setCategory');

// Route::resource('employee', 'EmployeeController');
Route::get('employee', 'EmployeeController@index');
Route::get('employee/new', 'EmployeeController@show');
Route::get('employee/{id}', 'EmployeeController@show');
Route::post('employee/{id}', 'EmployeeController@update');
Route::get('employee/delete/{id}', 'EmployeeController@destroy');
Route::post('employee/updateImage/{id}', 'EmployeeController@updateImage');
Route::post('employee/deleteImage/{id}', 'EmployeeController@deleteImage');

Route::get('application-form', 'ApplicantController@new');
Route::post('application-form', 'ApplicantController@update');
Route::post('applicant', 'ApplicantController@index');
