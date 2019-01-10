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
Route::get('/', 'Auth\LoginController@showLoginForm');

Auth::routes();
//Social Login
Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/home', 'HomeController@index')->name('home');

Route::group([
    'as' => 'admin.',
    'prefix' => 'admin',
    'namespace' => 'Admin',
    'middleware' => ['auth', 'admin']], function(){
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
});

Route::group([
    'as' => 'user.',
    'prefix' => 'user',
    'namespace' => 'User',
    'middleware' => ['auth', 'user']], function(){
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    Route::resource('project', 'ProjectController');
    Route::resource('release', 'ReleaseController');
    Route::resource('sprint', 'SprintController');
    Route::get('/sprint/getRelease/{id}', 'SprintController@ajaxGetRelease');
    Route::resource('meeting', 'MeetingController');
    Route::get('/meeting/getUser/{id}', 'MeetingController@ajaxGetUser');
    
    Route::post('/document-version/store/{id}', 'DocumentVersionController@store')->name('document_version.store');
    Route::post('/project/{id}/store-document-default', 'DocumentController@storeDefault')->name('document_default.store');
    Route::get('/project/{id}/create-document', 'DocumentController@create')->name('document.create');
    Route::post('/project/{id}/store-document', 'DocumentController@store')->name('document.store');
    Route::delete('/document/{id}', 'DocumentController@destroy')->name('document.destroy');
});
Route::get('/sprint/getRelease/{id}', 'User\SprintController@ajaxGetRelease');
Route::get('/getTree/{id}', 'TreeViewController@getTree')->name('getTree');
Route::get('/getDocument/search/{id}', 'User\DocumentController@searchDocument')->name('searchDocument');
