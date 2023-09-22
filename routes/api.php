<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EC2ConsoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {

    Route::resource('vcs', EC2ConsoleController::class);
    Route::post("vcs/off/{instanceId}", [EC2ConsoleController::class,'stopInstance']);
    Route::post("vcs/{instanceId}", [EC2ConsoleController::class,'startInstance']);

});
