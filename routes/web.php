<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/home/schedule/new', function () {
    return view('scheduleRegistrationForm');
});

Route::get('/home/schedule/list', [App\Http\Controllers\ScheduleController::class, 'list']);

Route::post('/home/schedule/list', [App\Http\Controllers\ScheduleController::class, 'list'])->name('schedule.list');

Route::post('/home/schedule/add', [App\Http\Controllers\ScheduleController::class, 'add'])->name('schedule.add');

Route::get('/home/todo/new', function () {
    return view('todoRegistrationForm');
});

Route::post('/home/todo/add', [App\Http\Controllers\ToDoController::class, 'add'])->name('todo.add');

Route::get('/home/workflow/new', function () {
    return view('workflowRegistrationForm');
});

Route::post('/home/workflow/add', [App\Http\Controllers\WorkFlowController::class, 'add'])->name('workflow.add');

Route::post('/home/workflow/edit', [App\Http\Controllers\WorkFlowController::class, 'edit_form'])->name('workflow.edit_form');

Route::post('/home/workflow/update', [App\Http\Controllers\WorkFlowController::class, 'update'])->name('workflow.update');