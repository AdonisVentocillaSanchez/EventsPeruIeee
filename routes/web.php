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

Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');


Route::middleware(['auth'])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::resources([
        'events' => 'Event\EventController',
        'committeeDetails' => 'Event\CommitteeDetailController',
        'eventThemeDetails' => 'Event\EventThemDetailController',
        'hostDetails' => 'Event\HostDetailController',
        'imageDetails' => 'Event\ImageDetailController',
        'locationDetails' => 'Event\LocationDetailController',
        'registrationPayments' => 'Event\RegistrationPaymentController',
        'speakers' => 'Event\SpeakerController',
    ]);
});

