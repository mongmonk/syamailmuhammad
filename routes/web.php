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
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;


Route::get('/', function () {
    return view('welcome');
})->name('home')->middleware(['security.headers', 'cache.headers:short']);
// Proxy akses publik untuk /storage/* agar varian gambar galeri dapat dilayani meski symlink/permission bermasalah
Route::get('/storage/{path}', [\App\Http\Controllers\StorageProxyController::class, 'serve'])
    ->where('path', '.*')
    ->name('storage.serve')
    ->middleware(['security.headers', 'cache.headers:long']);

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

// Dashboard routes
Route::get('/dashboard', function () {
    return view('dashboard.user.index');
})->name('dashboard')->middleware(['security.headers', 'auth', 'not.banned', 'cache.headers:none']);

// Admin dashboard routes (SSR Blade)
Route::middleware(['security.headers', 'auth', 'ensure.active', 'role.admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('dashboard.admin.index');
    })->name('admin.index');

    // Admin - Users (SSR)
// Route model binding: gunakan GalleryItem untuk parameter {post} di admin
Route::model('post', \App\Models\GalleryItem::class);
    Route::get('/users', [\App\Http\Controllers\Admin\UsersPageController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [\App\Http\Controllers\Admin\UsersPageController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [\App\Http\Controllers\Admin\UsersPageController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UsersPageController::class, 'edit'])->name('admin.users.edit');
    Route::patch('/users/{user}', [\App\Http\Controllers\Admin\UsersPageController::class, 'update'])->name('admin.users.update');
// Admin - Posts (SSR)
Route::get('/posts', [\App\Http\Controllers\Admin\PostsPageController::class, 'index'])->name('admin.posts.index');
Route::get('/posts/create', [\App\Http\Controllers\Admin\PostsPageController::class, 'create'])->name('admin.posts.create');
Route::post('/posts', [\App\Http\Controllers\Admin\PostsPageController::class, 'store'])->name('admin.posts.store');
Route::get('/posts/{post}/edit', [\App\Http\Controllers\Admin\PostsPageController::class, 'edit'])->name('admin.posts.edit');
Route::patch('/posts/{post}', [\App\Http\Controllers\Admin\PostsPageController::class, 'update'])->name('admin.posts.update');
Route::delete('/posts/{post}', [\App\Http\Controllers\Admin\PostsPageController::class, 'destroy'])->name('admin.posts.destroy');

// Admin - Audit (SSR)
Route::get('/audit', [\App\Http\Controllers\Admin\AuditPageController::class, 'index'])->name('admin.audit.index');
Route::get('/audit/export', [\App\Http\Controllers\Admin\AuditPageController::class, 'export'])->name('admin.audit.export');
// Admin - Chapters (SSR)
Route::get('/chapters', [\App\Http\Controllers\Admin\ChaptersPageController::class, 'index'])->name('admin.chapters.index');
Route::get('/chapters/create', [\App\Http\Controllers\Admin\ChaptersPageController::class, 'create'])->name('admin.chapters.create');
Route::post('/chapters', [\App\Http\Controllers\Admin\ChaptersPageController::class, 'store'])->name('admin.chapters.store');
Route::get('/chapters/{chapter}/edit', [\App\Http\Controllers\Admin\ChaptersPageController::class, 'edit'])->name('admin.chapters.edit');
Route::patch('/chapters/{chapter}', [\App\Http\Controllers\Admin\ChaptersPageController::class, 'update'])->name('admin.chapters.update');
Route::delete('/chapters/{chapter}', [\App\Http\Controllers\Admin\ChaptersPageController::class, 'destroy'])->name('admin.chapters.destroy');

// Admin - Hadiths (SSR)
Route::get('/hadiths', [\App\Http\Controllers\Admin\HadithsPageController::class, 'index'])->name('admin.hadiths.index');
Route::get('/hadiths/create', [\App\Http\Controllers\Admin\HadithsPageController::class, 'create'])->name('admin.hadiths.create');
Route::post('/hadiths', [\App\Http\Controllers\Admin\HadithsPageController::class, 'store'])->name('admin.hadiths.store');
Route::get('/hadiths/{hadith}/edit', [\App\Http\Controllers\Admin\HadithsPageController::class, 'edit'])->name('admin.hadiths.edit');
Route::patch('/hadiths/{hadith}', [\App\Http\Controllers\Admin\HadithsPageController::class, 'update'])->name('admin.hadiths.update');
Route::delete('/hadiths/{hadith}', [\App\Http\Controllers\Admin\HadithsPageController::class, 'destroy'])->name('admin.hadiths.destroy');
});
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
    Route::post('/progress', [ProgressController::class, 'store'])->name('progress.store');
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

// Public SSR - Posts
Route::get('/posts', [\App\Http\Controllers\PostsPublicPageController::class, 'index'])->name('posts.index')->middleware(['security.headers', 'cache.headers:short']);
Route::get('/posts/{slug}', [\App\Http\Controllers\PostsPublicPageController::class, 'show'])->name('posts.show')->middleware(['security.headers', 'cache.headers:short']);
