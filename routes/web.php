<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\HadithController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\UserNoteController;
use App\Http\Controllers\ProgressController;


Route::get('/', function () {
    return view('welcome');
})->name('home')->middleware(['security.headers', 'cache.headers:short']);

// Chapter routes
Route::get('/chapters', [ChapterController::class, 'index'])->name('chapters.index')->middleware(['security.headers', 'cache.headers:long']);
Route::get('/chapters/{chapter}', [ChapterController::class, 'show'])->name('chapters.show')->middleware(['security.headers', 'auth', 'ensure.active', 'cache.headers:none']);

// Hadith routes
Route::get('/hadiths/{hadith}', [HadithController::class, 'show'])->name('hadiths.show')->middleware(['security.headers', 'auth', 'ensure.active', 'cache.headers:none']);

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware(['security.headers', 'guest']);
Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware(['security.headers', 'guest']);
Route::post('/register', [AuthController::class, 'register'])->middleware(['security.headers', 'throttle:auth']);
Route::post('/login', [AuthController::class, 'login'])->middleware(['security.headers', 'throttle:auth']);

// API-friendly aliases per spec
Route::post('/auth/register', [AuthController::class, 'register'])->middleware(['security.headers', 'throttle:auth']);
Route::post('/auth/login', [AuthController::class, 'login'])->middleware(['security.headers', 'throttle:auth']);
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout')->middleware(['security.headers', 'auth']);

// Password reset routes
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request')->middleware(['security.headers', 'guest']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email')->middleware(['security.headers', 'guest', 'throttle:auth']);
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset')->middleware(['security.headers', 'guest']);
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update')->middleware(['security.headers', 'guest', 'throttle:auth']);

// Email verification routes

// Profile routes
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show')->middleware(['security.headers', 'auth', 'not.banned']);
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware(['security.headers', 'auth', 'not.banned']);
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware(['security.headers', 'auth', 'not.banned']);
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware(['security.headers', 'auth', 'not.banned']);

// Audio routes
Route::get('/audio/{audioFile}/stream', [AudioController::class, 'stream'])->name('audio.stream')->middleware(['security.headers', 'throttle:media']);
Route::get('/audio/{audioFile}/url', [AudioController::class, 'getAudioUrl'])->name('audio.url')->middleware(['security.headers', 'throttle:media']);

// Search routes
Route::get('/search/form', [SearchController::class, 'form'])->name('search.form')->middleware(['security.headers', 'auth', 'ensure.active', 'cache.headers:none', 'throttle:search']);
Route::get('/search/advanced', [SearchController::class, 'advanced'])->name('search.advanced')->middleware(['security.headers', 'cache.headers:short', 'throttle:search']);
// Bookmark & Notes routes (auth + verified)
Route::middleware(['security.headers', 'auth', 'not.banned'])->group(function () {
    // Progress
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    // Bookmarks
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', [BookmarkController::class, 'store'])->name('bookmarks.store');
    Route::put('/bookmarks/{hadith}', [BookmarkController::class, 'update'])->name('bookmarks.update');
    Route::delete('/bookmarks/{hadith}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    // User Notes
    Route::get('/notes', [UserNoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/{hadith}', [UserNoteController::class, 'show'])->name('notes.show'); // return JSON of existing note
    Route::post('/notes', [UserNoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{hadith}', [UserNoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{hadith}', [UserNoteController::class, 'destroy'])->name('notes.destroy');
});
Route::get('/search', [SearchController::class, 'search'])->name('search')->middleware(['security.headers', 'cache.headers:short', 'throttle:search']);
Route::get('/search/history', [SearchController::class, 'history'])->name('search.history')->middleware(['security.headers', 'cache.headers:short', 'throttle:search']);
Route::delete('/search/history', [SearchController::class, 'clearHistory'])->name('search.history.clear')->middleware(['security.headers', 'auth']);
