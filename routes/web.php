<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    FilmController,
    AuthController,
    AdminController,
    AdminFilmController,
    KritikController,
};

// Public Film Routes
Route::get('/', [FilmController::class, 'movieHome'])->name('home');
Route::get('/movies', [FilmController::class, 'movies'])->name('movies');
Route::get('/movies/{film}', [FilmController::class, 'show'])->name('movies.show');
Route::get('/movies/genre/{genre}', [FilmController::class, 'moviesByGenre'])->name('genre');

// Comments Route (Protected)
Route::post('/movies/{film}/comments', [KritikController::class, 'store'])->name('comments.store')->middleware('auth');


// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // Film & Cast Management
    Route::get('/admin/films', [AdminFilmController::class, 'index'])->name('admin.films.index');
    Route::get('/admin/films/create', [AdminFilmController::class, 'create'])->name('admin.films.create');
    Route::post('/admin/films', [AdminFilmController::class, 'store'])->name('admin.films.store');
    Route::get('/admin/films/{film}/edit', [AdminFilmController::class, 'edit'])->name('admin.films.edit');
    Route::put('/admin/films/{film}', [AdminFilmController::class, 'update'])->name('admin.films.update');
    Route::delete('/admin/films/{film}', [AdminFilmController::class, 'destroy'])->name('admin.films.destroy');

    // Cast Management on Film
    Route::post('/admin/films/{film}/cast', [AdminFilmController::class, 'addCast'])->name('admin.films.cast.add');
    Route::delete('/admin/films/{film}/cast/{peran}', [AdminFilmController::class, 'removeCast'])->name('admin.films.cast.remove');
});

