<?php

use App\Http\Controllers\Api\FavouriteController;
use App\Http\Controllers\Api\GlovoRatingController;
use App\Http\Controllers\Api\GooglePlacesController;
use App\Http\Controllers\Api\KebabController;
use App\Http\Controllers\Api\MeatTypeController;
use App\Http\Controllers\Api\PyszneRatingController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SauceTypeController;
use App\Http\Controllers\Api\CommentController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/user-login', [AuthController::class, 'userLogin']);
Route::post('/admin-login', [AuthController::class, 'adminLogin']);

Route::middleware(['auth:sanctum'])->post('/logout-from-all', [AuthController::class, 'logoutFromAll']);

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'getUser']);
    Route::get('first-login', [UserController::class, 'isFirstLogin']);
    Route::put('change-username', [UserController::class, 'changeUsername']);
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::get('favourites', [FavouriteController::class, 'getFavourites']);
    Route::prefix('comments')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [CommentController::class, 'getUserComments']);
        Route::put('{comment}', [CommentController::class, 'editComment']);
        Route::delete('{comment}', [CommentController::class, 'removeComment']);
    });
});

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{id}', [UserController::class, 'getUserById']);
    Route::delete('delete-user/{id}', [UserController::class, 'destroy']);
    Route::post('change-password-first-login', [UserController::class, 'changePasswordForFirstLogin']);
    Route::delete('delete-comment/{comment}', [CommentController::class, 'adminRemoveComment']);
});

Route::prefix('kebabs')->group(function () {
    Route::get('{kebab}', [KebabController::class, 'show']);
    Route::get('/', [KebabController::class, 'index']);
    Route::get('{kebab}/comments', [CommentController::class, 'getCommentsByKebabId']);
    Route::middleware(['auth:sanctum'])->post('{kebab}/comments', [CommentController::class, 'addComment']);
    Route::middleware(['auth:sanctum'])->post('{kebab}/favourite', [FavouriteController::class, 'addToFavourites']);
    Route::middleware(['auth:sanctum'])->delete('{kebab}/favourite', [FavouriteController::class, 'removeFromFavourites']);
    Route::middleware(['auth:sanctum', 'admin'])->post('/', [KebabController::class, 'store']);
    Route::middleware(['auth:sanctum', 'admin'])->put('{kebab}', [KebabController::class, 'update']);
    Route::middleware(['auth:sanctum', 'admin'])->delete('{kebab}', [KebabController::class, 'destroy']);
    Route::middleware(['auth:sanctum', 'admin'])->post('{kebab}/pysznepl-refresh-review', [PyszneRatingController::class, 'getRating']);
    Route::middleware(['auth:sanctum', 'admin'])->post('{kebab}/glovo-refresh-review', [GlovoRatingController::class, 'getRating']);
    Route::middleware(['auth:sanctum', 'admin'])->post('{kebab}/google-refresh-review', [GooglePlacesController::class, 'getKebabDetails']);
});

Route::prefix('meattypes')->group(function () {
    Route::get('/', [MeatTypeController::class, 'index']);
    Route::middleware(['auth:sanctum', 'admin'])->post('/', [MeatTypeController::class, 'store']);
    Route::middleware(['auth:sanctum', 'admin'])->put('{id}', [MeatTypeController::class, 'update']);
    Route::middleware(['auth:sanctum', 'admin'])->delete('{id}', [MeatTypeController::class, 'destroy']);
});

Route::prefix('saucetypes')->group(function () {
    Route::get('/', [SauceTypeController::class, 'index']);
    Route::middleware(['auth:sanctum', 'admin'])->post('/', [SauceTypeController::class, 'store']);
    Route::middleware(['auth:sanctum', 'admin'])->put('{id}', [SauceTypeController::class, 'update']);
    Route::middleware(['auth:sanctum', 'admin'])->delete('{id}', [SauceTypeController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('reports', [ReportController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/reports')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::delete('{report}', [ReportController::class, 'destroy']);
    Route::put('{report}/accept', [ReportController::class, 'acceptReport']);
    Route::put('{report}/refuse', [ReportController::class, 'refuseReport']);
});

