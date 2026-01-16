<?php

use App\Http\Controllers\InterviewController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentComplianceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');




Route::resource("users",UserController::class)->only(['create','store'])->middleware("permission:users.create");

Route::resource("users",UserController::class)->only(['edit','update'])->middleware("permission:users.edit");

Route::resource("users",UserController::class)->only(['destroy'])->middleware("permission:users.delete");

Route::resource("users",UserController::class)->only(['index','show'])->middleware("permission:users.view|users.create|users.update|users.delete");

Route::get("all-users",[UserController::class,'allUsers']);
// Route::resource("users",UserController::class);
// Roles Routes

// Route::resource("roles",RoleController::class);

Route::resource("roles",RoleController::class)->only(['create','store'])->middleware("permission:roles.create");

Route::resource("roles",RoleController::class)->only(['edit','update'])->middleware("permission:roles.edit");

Route::resource("roles",RoleController::class)->only(['destroy'])->middleware("permission:roles.delete");

Route::resource("roles",RoleController::class)->only(['index','show'])->middleware("permission:roles.view|roles.create|roles.update|roles.delete");


// Interview Routes

// Route::resource("interview",InterviewController::class)->only(['create','store'])->middleware("permission:interview.create");

// Route::resource("interview",InterviewController::class)->only(['edit','update'])->middleware("permission:interview.edit");

// Route::resource("interview",InterviewController::class)->only(['destroy'])->middleware("permission:interview.delete");

// Route::resource("interview",InterviewController::class)->only(['index','show'])->middleware("permission:interview.view|interview.create|interview.update|interview.delete");


Route::resource("interview",InterviewController::class);


// Students

Route::resource("student",StudentController::class);


// Student Compliance Profile

Route::post('/student/{student}/compliance-profiles',[StudentComplianceController::class,'store']);

//  Student Documents Upload

Route::post("students/{student}/documents",[StudentDocumentController::class,'store']);

require __DIR__.'/settings.php';
