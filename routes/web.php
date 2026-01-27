<?php

use App\Http\Controllers\ComplianceMessageController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\InterviewRecordingController;
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

Route::get('/health', fn() => 'OK');



Route::get("all-users", [UserController::class, 'allUsers']);

// Route::resource("users",UserController::class)->only(['create','store'])->middleware("permission:users.create");

// Route::resource("users",UserController::class)->only(['edit','update'])->middleware("permission:users.edit");

// Route::resource("users",UserController::class)->only(['destroy'])->middleware("permission:users.delete");

// Route::resource("users",UserController::class)->only(['index','show'])->middleware("permission:users.view|users.create|users.update|users.delete");

Route::resource("users", UserController::class);
// Roles Routes

Route::resource("roles", RoleController::class);

// Route::resource("roles",RoleController::class)->only(['create','store'])->middleware("permission:roles.create");

// Route::resource("roles",RoleController::class)->only(['edit','update'])->middleware("permission:roles.edit");

// Route::resource("roles",RoleController::class)->only(['destroy'])->middleware("permission:roles.delete");

// Route::resource("roles",RoleController::class)->only(['index','show'])->middleware("permission:roles.view|roles.create|roles.update|roles.delete");


// Interview Routes

// Route::resource("interview",InterviewController::class)->only(['create','store'])->middleware("permission:interview.create");

// Route::resource("interview",InterviewController::class)->only(['edit','update'])->middleware("permission:interview.edit");

// Route::resource("interview",InterviewController::class)->only(['destroy'])->middleware("permission:interview.delete");

// Route::resource("interview",InterviewController::class)->only(['index','show'])->middleware("permission:interview.view|interview.create|interview.update|interview.delete");


Route::resource("interview", InterviewController::class);
Route::get('/session/{interviewId}', [InterviewController::class, 'interviewSession']);
Route::get('/compliance', [InterviewController::class, 'msg']);
Route::get('/cas', [InterviewController::class, 'cas']);
Route::get('/setup', [InterviewController::class, 'setup']);
Route::get('/questions', [InterviewController::class, 'questions']);

// Students

Route::resource("student", StudentController::class);
Route::post("/student/login", [StudentController::class, 'studentLogin']);
Route::get("/student/me", [StudentController::class, 'me'])->middleware('student.jwt');


// Student Compliance Profile

Route::post('/students/{student}/compliance-profiles', [StudentComplianceController::class, 'store']);
Route::get('/students/compliance-profiles', [StudentComplianceController::class, 'index']);

//  Student Documents Upload

Route::post("/students/{student}/documents", [StudentDocumentController::class, 'store']);

// Interview

Route::post('/interviews', [InterviewController::class, 'store']);

Route::get('/interviews/all-interviews', [InterviewController::class, 'allInterviews']);

Route::get('/interviews/{interview}', [InterviewController::class, 'show']);

Route::post('/interviews/{interview}/generate-questions', [InterviewController::class, 'generateQuestions']);
Route::post('/interviews/{interview}/start', [InterviewController::class, 'start']);

Route::post('/interviews/{interview}/recording', [InterviewRecordingController::class, 'upload']);

Route::post("/int", [InterviewController::class, 'recordupload']);


Route::get('/interviews/{interview}/compliance-message', [ComplianceMessageController::class, 'index']);
Route::post('interviews/{interview}/compliance-message', [ComplianceMessageController::class, 'store']);

require __DIR__ . '/settings.php';
