<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\CourseMaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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

Route::get('/', [CourseController::class, 'index'])->middleware(['auth', 'verified'])->name('main.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/manage-user', [UserController::class, 'index'])->name('manage-user.index');
        Route::get('/manage-user/{user_id}', [UserController::class, 'show'])->name('manage-user.show');
        Route::put('/manage-user/{user_id}', [UserController::class, 'update'])->name('manage-user.update');
        Route::delete('/manage-user/{user_id}', [UserController::class, 'destroy'])->name('manage-user.destroy');
    });

    // Route::get('/course', [CourseController::class, 'index'])->name('course.index');
    Route::middleware(['role:teacher'])->group(function () {
        Route::post('/course', [CourseController::class, 'store'])->name('course.store');
        Route::get('/course/create', [CourseController::class, 'create'])->name('course.create');

        Route::get('/manage-course', [CourseController::class, 'index'])->name('manage-course.index');

        Route::put('/course/{course_id}', [CourseController::class, 'update'])->name('course.update');
        Route::get('/course/{course_id}/edit', [CourseController::class, 'edit'])->name('course.edit');

        Route::get('/manage-material/{course_id}', [CourseMaterialController::class, 'index'])->name('manage-material.index');
        Route::get('/manage-material/{course_material_id}/edit', [CourseMaterialController::class, 'edit'])->name('manage-material.edit');
        Route::put('/manage-material/{course_material_id}', [CourseMaterialController::class, 'update'])->name('manage-material.update');
        Route::get('/manage-material/{course_id}/create', [CourseMaterialController::class, 'create'])->name('manage-material.create');
        Route::post('/manage-material/{course_id}', [CourseMaterialController::class, 'store'])->name('manage-material.store');
    });

    Route::get('/course/{course_id}', [CourseController::class, 'show'])->name('course.show')->middleware('check.course.access');
    Route::get('/course/{course_id}/access-code', [CourseEnrollmentController::class, 'showForm'])
        ->name('course.access-code');
    Route::post('/course/{course_id}/access-code', [CourseEnrollmentController::class, 'verifyCode'])
        ->name('course.access-code.verify');
});


require __DIR__ . '/auth.php';
