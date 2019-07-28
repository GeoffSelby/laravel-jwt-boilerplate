<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/register', 'AuthController@register');
Route::post('/auth/login', 'AuthController@login');
Route::post('/auth/logout', 'AuthController@logout')->middleware('auth:api');
Route::post('/auth/refresh_token', 'AuthController@refreshToken')->middleware('auth:api');
Route::get('/auth/user', 'UserController@getAuthUser');
Route::post('/auth/forgot_password', 'ForgotPasswordController@sendForgotPasswordEmail');
Route::post('/auth/reset_password', 'PasswordResetController@resetPassword');

Route::post('/email/verify/{id}', 'VerificationController@verify')->name('verification.verify');
Route::post('/email/resend', 'VerificationController@resend')->name('verification.resend');
