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

// Auth::routes();
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');

Route::middleware(['auth'])->group(function () {
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    // Route::get('/', function () {
    //     return view('prices');
    // });
    Route::redirect('/', '/prices');
    Route::get('/prices', 'HomeController@prices')->name('prices');
    Route::post('/changeprice', 'HomeController@changeprice');
    Route::post('/delitem', 'HomeController@delitem');
    
    Route::get('/products', 'HomeController@products')->name('products');
    Route::post('/changeproduct', 'HomeController@changeproduct');
    Route::post('/delproduct', 'HomeController@delproduct');
    Route::post('/saveNewDefMarja', 'HomeController@saveNewDefMarja');
});