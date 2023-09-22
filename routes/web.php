<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EC2ConsoleController;

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

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () 
    {
        Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');

        Route::get('/aws-ec2',[EC2ConsoleController::class,'getCredentials'])->name('/aws-ec2');
        Route::post('/aws-ec2',[EC2ConsoleController::class,'addCredentials'])->name('/aws-ec2');
        Route::get('/aws-ec2/{id}',[EC2ConsoleController::class,'deleteCredentials'])->name('/aws-ec2');
        
    }
);
