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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('branch', 'BranchController')->middleware(['auth']);
Route::resource('employee', 'EmployeeController')->middleware(['auth']);
Route::resource('journalist', 'JournalistController')->middleware(['auth']);
Route::resource('magazine', 'MagazineController')->middleware(['auth']);
Route::resource('magissue', 'MagissueController')->middleware(['auth']);
Route::resource('writes', 'WritesController')->middleware(['auth']);
Route::resource('sells', 'SellsController')->middleware(['auth']);
Route::match(['get'], 'city/search/{code}', 'CityController@search');
Route::match(['get'], 'branch/search/{code}', 'BranchController@search');
