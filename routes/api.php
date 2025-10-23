<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\PostPublicController;
use App\Http\Controllers\GalleryApiController;

 // API Auth (JWT Bearer)
Route::post('/auth/login', [AuthController::class, 'loginApi'])
    ->middleware(['security.headers', 'throttle:auth']);
Route::post('/auth/refresh', [AuthController::class, 'refreshToken'])
    ->middleware(['security.headers', 'throttle:auth']);
Route::post('/auth/revoke', [AuthController::class, 'revokeToken'])
    ->middleware(['security.headers', 'throttle:auth']);

// Protected API routes (JWT + not banned)
Route::middleware(['security.headers', 'jwt', 'not.banned'])->group(function () {
    // Introspeksi token: profil singkat user saat ini
    Route::get('/me', function (Request $request) {
        $user = $request->user() ?: Auth::user();
        return response()->json([
            'user' => $user ? $user->only(['id','name','email','phone','status','role']) : null,
        ]);
    });
});

// Admin Users management (JWT + admin only)
Route::middleware(['security.headers', 'jwt', 'role.admin'])->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::patch('/{user}', [UserController::class, 'update']);
    Route::delete('/{user}', [UserController::class, 'destroy']);
});

// Admin Posts management (JWT + admin only)
Route::middleware(['security.headers', 'jwt', 'role.admin'])->prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::post('/', [PostController::class, 'store']);
    Route::patch('/{post}', [PostController::class, 'update']);
    Route::delete('/{post}', [PostController::class, 'destroy']);
});

// Public endpoints
Route::middleware(['security.headers', 'cache.headers:short'])->group(function () {
    // Legacy posts JSON (tetap untuk kompatibilitas)
    Route::get('/posts', [PostPublicController::class, 'index']);
    Route::get('/posts/{slug}', [PostPublicController::class, 'show']);

    // Galeri publik (baru)
    Route::get('/gallery', [GalleryApiController::class, 'index']);
});