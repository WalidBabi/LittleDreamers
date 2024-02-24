<?php

use App\Http\Controllers\Api\ChildFormController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RecommendationController;
use App\Http\Controllers\Api\SearchController;

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

//User Auth
Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
Route::get('/user-details', [PassportAuthController::class, 'getUserDetails']);
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [PassportAuthController::class, 'logout']);
});

//Admin Auth
Route::post('AdminRegister', [PassportAuthController::class, 'AdminRegister']);
Route::post('AdminLogin', [PassportAuthController::class, 'AdminLogin']);

//display products
Route::get('/products', [ProductController::class, 'index']);
//display products details
Route::get('/products/{id}', [ProductController::class, 'show']);
//Form Submisstion
Route::post('/Form', [ChildFormController::class,'processForm']);
//Return Recommendations
Route::get('/recommendations', [RecommendationController::class,'recommendations']);
//search 
Route::get('/search', [SearchController::class, 'search']);