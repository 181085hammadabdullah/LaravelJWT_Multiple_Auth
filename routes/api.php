<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@authenticate');
Route::get('open', 'DataController@open');


Route::post('Employeeregister', 'EmployeeController@register');
Route::post('Employeelogin', 'EmployeeController@authenticate');

Route::post('Workerregister', 'WorkerController@register');
Route::post('Workerlogin', 'WorkerController@authenticate');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', 'UserController@getAuthenticatedUser');
    Route::get('worker', 'WorkerController@getAuthenticatedUser');
    Route::get('admin', 'AdminController@getAuthenticatedUser');
    Route::get('closed', 'DataController@closed');
});