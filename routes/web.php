<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Artisan;


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

// upload
Route::get('/upload', [FileController::class, 'uploadView'])->name('upload.view')->middleware('auth');  // Using the auth middleware to ensure only logged-in users can access.
Route::post('/upload', [FileController::class, 'store'])->name('upload.file')->middleware('auth');  // Using the auth middleware to ensure only logged-in users can access.

// Dashboard
Route::get('/', [FileController::class, 'dashboard'])->name('dashboard')->middleware('auth');  // Using the auth middleware to ensure only logged-in users can access.

// Download
Route::get('/download/{file}', [FileController::class, 'download'])->name('download.file')->middleware('auth');

// Download original
Route::get('/download-original/{file}', [FileController::class, 'downloadOriginal'])->name('download.original')->middleware('auth');

// View file
Route::get('/view-file/{file}', [FileController::class, 'viewFile'])->name('view.file');

// Save file
Route::post('/save-file/{file}/{encoding}', [FileController::class, 'saveFile'])->name('save.file')->middleware('auth');

// Revert file
Route::post('/revert-file/{file}', [FileController::class, 'revertFile'])->name('revert.file')->middleware('auth');

// Submit to process
Route::post('/submit-to-process/{file}', [FileController::class, 'submitToProcess'])->name('submitToProcess.file')->middleware('auth');

// Add Names
Route::post('/add-names/{file}', [FileController::class, 'addNames'])->name('addNames.file')->middleware('auth');

// Finalize file
Route::post('/finalize-file/{file}', [FileController::class, 'finalizeFile'])->name('finalize.file')->middleware('auth');

// Delete file
Route::post('/delete-file/{file}', [FileController::class, 'deleteFile'])->name('delete.file')->middleware('auth');

// Deliverable only
Route::post('/save-deliver-only/{file}', [FileController::class, 'saveDeliverOnly'])->name('saveDeliverOnly.file')->middleware('auth');

// Cron job
Route::get('/run-cron', function () {
    $token = request()->get('token');

    if($token !== '1f4G7jK9lp3NoPQR56sTuvwxZ78mzy0c') {
        return response('Invalid token', 403);
    }

    Artisan::call('files:process');
    return response('Cron Job Run Successfully', 200);
});

// Symlink
Route::get('/symlink', function () {
    $token = request()->get('token');

    if ($token !== '1f4G7jK9lp3NoPQR56sTuvwxZ78mzy0c') {
        return response('Invalid token', 403);
    }

    $targetFolder = base_path() . '/storage/app/uploads';
    $linkFolder = public_path() . '/uploads';
    symlink($targetFolder, $linkFolder);

    return response($targetFolder . ' has been symlinked to ' . $linkFolder, 200);
});

// DB run migrations
Route::get('/db-migrate', function () {
    $token = request()->get('token');

    if ($token !== '1f4G7jK9lp3NoPQR56sTuvwxZ78mzy0c') {
        return response('Invalid token', 403);
    }

    if (!defined("STDIN")) {
        define("STDIN", fopen('php://stdin', 'r'));
    }

    Artisan::call('migrate');
    return response('DB Migrated Successfully', 200);
});
