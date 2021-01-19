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
})->name('welcome');

Auth::routes();
Route::post('/user/logout', 'HomeController@logout');
Route::get('/profile', 'HomeController@profile')->name('profile');
Route::post('/profile', 'HomeController@update_profile');

Route::get('/home', 'HomeController@index')->name('home');

Route::post('/addcontact', 'ContactsController@addcontact');
Route::get('/contacts', 'ContactsController@get');

Route::get('/conversation/{id}', 'ContactsController@getMessagesFor');

Route::get('/Gonversation/{id}', 'ContactsController@getMessagesForGroup');

Route::post('/conversation/send', 'ContactsController@send');
Route::post('/contacts/remove', 'ContactsController@removeContact');

Route::post('/group/removegroup', 'GroupController@removePersonfromGroup');
Route::post('/group/updateInfo', 'GroupController@UpdateGroup');

Route::get('/group/participant', 'GroupController@getParticipant');

Route::get('/group/GroupforUser/{id}', 'GroupController@getGroupforUser');

Route::post('/group/create', 'GroupController@store');
