<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Students — accessible by all authenticated users
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Fee Collection — accessible by all authenticated users
    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::post('/fees', [FeeController::class, 'store'])->name('fees.store');
    Route::get('/fees/{fee}/receipt', [FeeController::class, 'receipt'])->name('fees.receipt');
    Route::delete('/fees/{fee}', [FeeController::class, 'destroy'])->name('fees.destroy');

    // Inquiries — accessible by all authenticated users
    Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
    Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');
    Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])->name('inquiries.update');
    Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
    Route::post('/inquiries/{inquiry}/convert', [InquiryController::class, 'convert'])->name('inquiries.convert');

    // Timetable — accessible by all authenticated users
    Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
    Route::post('/timetable', [TimetableController::class, 'store'])->name('timetable.store');
    Route::put('/timetable/{timetable}', [TimetableController::class, 'update'])->name('timetable.update');
    Route::delete('/timetable/{timetable}', [TimetableController::class, 'destroy'])->name('timetable.destroy');

    // Attendance — accessible by all authenticated users
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        // Teachers
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

        // Salaries
        Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
        Route::post('/salaries', [SalaryController::class, 'store'])->name('salaries.store');
        Route::delete('/salaries/{salary}', [SalaryController::class, 'destroy'])->name('salaries.destroy');

        // Expenses
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

        // Accounts
        Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // Approve attendance
        Route::post('/attendance/{attendance}/approve', [AttendanceController::class, 'approve'])->name('attendance.approve');
    });
});
