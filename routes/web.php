<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VCSConsoleController;
use App\Http\Controllers\ILMSConsoleController;
use App\Http\Controllers\EC2ConsoleController;
use App\Http\Controllers\WBConsoleController;
use App\Http\Controllers\APIActivityController;

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

Route::get('/', function () { return redirect('/login'); });

Route::match(['get', 'post'], '/register', fn() => abort(404));

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () 
    {
        //Main Pages
        Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');

        Route::get('/api-activities',[APIActivityController::class,'getActivities'])->name('/api-activities');
        Route::get('/api-activities/{id}',[APIActivityController::class,'getActivity'])->name('/api-activities');

        //VCS Routes
        Route::get('/vcs-console',[VCSConsoleController::class,'getCredentials'])->name('/vcs-console');
        Route::post('/vcs-console',[VCSConsoleController::class,'addCredentials'])->name('/vcs-console');
        Route::get('/vcs-console/{id}',[VCSConsoleController::class,'deleteCredentials'])->name('/vcs-console');

        //iLMS Routes
        Route::get('/ilms-console',[ILMSConsoleController::class,'getCredentials'])->name('/ilms-console');
        Route::post('/ilms-console',[ILMSConsoleController::class,'addCredentials'])->name('/ilms-console');
        Route::get('/ilms-console/{id}',[ILMSConsoleController::class,'deleteCredentials'])->name('/ilms-console');

        
        //AWS Routes
        Route::get('/ec2-console',[EC2ConsoleController::class,'getCredentials'])->name('/ec2-console');
        Route::post('/ec2-console',[EC2ConsoleController::class,'addCredentials'])->name('/ec2-console');
        Route::get('/ec2-console/{id}',[EC2ConsoleController::class,'deleteCredentials'])->name('/ec2-console');

        //whereBy Routes
        Route::get('/whereby-console',[WBConsoleController::class,'getCredentials'])->name('/ec2-console');
        Route::post('/whereby-console',[WBConsoleController::class,'addCredentials'])->name('/ec2-console');
        Route::get('/whereby-console/{id}',[WBConsoleController::class,'deleteCredentials'])->name('/ec2-console');

    }
);
