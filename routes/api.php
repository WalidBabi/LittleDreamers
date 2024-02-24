<?php

use App\Http\Controllers\Api\ChildFormController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RecommendationController;

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

Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);

Route::get('/user-details', [PassportAuthController::class, 'getUserDetails']);

 
Route::middleware('auth:api')->group(function () {
    Route::post('logout', [PassportAuthController::class, 'logout']);
});

//display products
Route::get('/products', [ProductController::class, 'index']);
//display products details
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::post('/Form', [ChildFormController::class,'processForm']);

Route::get('/recommendations', [RecommendationController::class,'recommendations']);