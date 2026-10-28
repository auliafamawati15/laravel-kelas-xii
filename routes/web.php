<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    FilmController,
    KritikController,
    AuthController,
    AdminController,
};
use App\Http\Controllers\Admin\{
    FilmController as AdminFilmController,
    UserController as AdminUserController,
    CommentController as AdminCommentController,
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Di sini tempat mendaftarkan seluruh rute website film.
*/

// --- Rute Halaman Publik (Film) ---
Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');

// --- Rute Komentar / Kritik Film ---
Route::post('/movies/{film}/comments', [KritikController::class, 'store'])->name('comments.store');
Route::delete('/comments/{comment}', [KritikController::class, 'destroy'])->name('comments.destroy');

// --- Rute Autentikasi (Guest Only) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// --- Rute Logout (Auth Only) ---
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// --- Rute Khusus Administrator (Proteksi: Auth + Role Admin) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. Dashboard Utama Admin
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // 2. CRUD Kelola Film
    Route::resource('films', AdminFilmController::class);

    // 3. Kelola Pengguna (Users)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // 4. Kelola Komentar / Ulasan (Comments)
    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
});
