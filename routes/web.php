<?php

use App\Http\Controllers\Api\AuthController;
use App\Mail\EmailVerificationMail;
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
    return "Laravel Inventory App";
});

Route::get('/get-user/{email}', [AuthController::class, 'getUser']);

// this route will be used to verify user's email later on.
// Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);



Route::fallback(function () {

    return view("404");

});