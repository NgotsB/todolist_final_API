<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

// Public Routes

// Authentication Routes
Route::post('/authenticate', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

//Verification Route
// web.php

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');



// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

     // Users Routes
     Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index');
        Route::get('/users/{id}', 'show');
        Route::patch('/users/{id}', 'patch');
        Route::post('/users', 'store');
        Route::put('/users/{id}', 'update');
        Route::patch('/users/password/change', 'changePassword');
    });


    //Logout User
    Route::post('/logout', [AuthController::class, 'logout']);
});
