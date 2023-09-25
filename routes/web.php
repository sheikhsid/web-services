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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::match(['get', 'post'], '/register', fn() => abort(404));

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () 
    {
        Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');

        Route::get('/ec2-console',[EC2ConsoleController::class,'getCredentials'])->name('/ec2-console');
        Route::post('/ec2-console',[EC2ConsoleController::class,'addCredentials'])->name('/ec2-console');
        Route::get('/ec2-console/{id}',[EC2ConsoleController::class,'deleteCredentials'])->name('/ec2-console');
        
    }
);
