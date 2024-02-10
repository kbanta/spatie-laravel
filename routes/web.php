<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// superadmin routes
Route::group(['prefix' => 'superadmin/','middleware' => ['role:superadmin']], function () {
    Route::get('/superhome', [App\Http\Controllers\SuperadminController::class, 'index'])->name('superadmindashboard');
    // User Crud routes
    Route::get('/superhome/users', [App\Http\Controllers\SuperadminController::class, 'users'])->name('users');
    Route::post('/superhome/register', [App\Http\Controllers\SuperadminController::class, 'registerUser'])->name('registerUser');
    Route::get('/superhome/users/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'edituser']);
    Route::patch('/superhome/users/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updateuser']);
    Route::patch('/superhome/users/deleteuser/{id}', [App\Http\Controllers\SuperadminController::class, 'deleteuser'])->name('delete-user');

    //Role Crud routes
    Route::get('/superhome/roles', [App\Http\Controllers\SuperadminController::class, 'roles'])->name('roles');
    Route::post('/superhome/registerRole', [App\Http\Controllers\SuperadminController::class, 'registerRole'])->name('registerRole');
    Route::get('/superhome/roles/edit/{id}', [App\Http\Controllers\SuperadminController::class, 'editrole']);
    Route::patch('/superhome/roles/update/{id}', [App\Http\Controllers\SuperadminController::class, 'updaterole']);
    Route::patch('/superhome/roles/deleterole/{id}', [App\Http\Controllers\SuperadminController::class, 'deleterole'])->name('delete-role');

});
