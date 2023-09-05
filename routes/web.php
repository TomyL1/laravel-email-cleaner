<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\FileController;


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

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes(['register'=>false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// uploadd
Route::get('/upload', [FileController::class, 'uploadView'])->name('upload.view')->middleware('auth');  // Using the auth middleware to ensure only logged-in users can access.
Route::post('/upload', [FileController::class, 'store'])->name('upload.file')->middleware('auth');  // Using the auth middleware to ensure only logged-in users can access.

// Dashboard
Route::get('/', [FileController::class, 'dashboard'])->name('dashboard')->middleware('auth');  // Using the auth middleware to ensure only logged-in users can access.

// Download
Route::get('/download/{file}', [FileController::class, 'download'])->name('download.file')->middleware('auth');

// View file
Route::get('/view-file/{file}', [FileController::class, 'viewFile'])->name('view.file');

// Save file
Route::post('/save-file/{file}/{encoding}', [FileController::class, 'saveFile'])->name('save.file')->middleware('auth');
