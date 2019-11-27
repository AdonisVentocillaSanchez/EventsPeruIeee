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

Route::get('/', 'WelcomeController@index');

Auth::routes();

Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');


Route::middleware(['auth'])->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('persona/{user}', 'User\UserController@createPersonData')->name('register.create');
    Route::post('persona', 'User\UserController@storePersonData')->name('register.store');

    Route::resources([
        'committeeDetails' => 'Event\CommitteeDetailController',
        'eventThemeDetails' => 'Event\EventThemeDetailController',
        'hostDetails' => 'Event\HostDetailController',
        'imageDetails' => 'Event\ImageDetailController',
        'locationDetails' => 'Event\LocationDetailController',
        'registrationPayments' => 'Event\RegistrationPaymentController',
        'speakers' => 'Event\SpeakerController',
    ]);

    Route::post('committeeDetails/createCommittee','Event\CommitteeDetailController@createCommittee')->name('committeeDetails.createCommittee');
    
    Route::resource('attendances', 'Attend\AttendController')->except(['create', 'store']);
    
    Route::resource('events', 'Event\EventController')->except(['show']);
    
    Route::middleware(['person.data'])->group(function(){
        Route::post('attendances', 'Attend\AttendController@store')->name('attendances.store');
        Route::get('attendances/create/{event}', 'Attend\AttendController@create')->name('attendances.create');
    });

    Route::post('/payment/process', 'Attend\PaymentsController@process')->name('payment.process');
    
    Route::get('confirmations', 'ConfirmationController@show')->name('confirmations.show');
    Route::post('confirmations', 'ConfirmationController@store')->name('confirmations.store');
});


Route::get('events/{event}', 'Event\EventController@show')->name('events.show');