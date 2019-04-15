<?php
use App\Http\Controllers\AuthController;
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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api){
    $api->post('signup', 'App\Http\Controllers\AuthController@signup');
    $api->post('login' ,'App\Http\Controllers\AuthController@login');
    $api->group(['middleware' => 'auth:api'], function ($api) {
        $api->get('logout', 'App\Http\Controllers\AuthController@logout');
        $api->resource('users','App\Http\Controllers\UserController');
        $api->resource('issues' , 'App\Http\Controllers\IssueController');
    });

});
