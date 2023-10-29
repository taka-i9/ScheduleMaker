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

Route::get('/home/schedule/new', [App\Http\Controllers\ScheduleController::class, 'new'])->name('schedule.new');

Route::get('/home/schedule/list', [App\Http\Controllers\ScheduleController::class, 'list'])->name('schedule.list');

Route::post('/home/schedule/add', [App\Http\Controllers\ScheduleController::class, 'add'])->name('schedule.add');

Route::get('/home/schedule/detail', [App\Http\Controllers\ScheduleController::class, 'detail'])->name('schedule.detail');

Route::get('/home/schedule/edit', [App\Http\Controllers\ScheduleController::class, 'edit'])->name('schedule.edit');

Route::post('/home/schedule/delete', [App\Http\Controllers\ScheduleController::class, 'delete'])->name('schedule.delete');

Route::get('/home/schedule/get_template', [App\Http\Controllers\ScheduleController::class, 'get_template'])->name('schedule.get_template');

Route::get('/home/todo/new', [App\Http\Controllers\ToDoController::class, 'new'])->name('todo.new');

Route::get('/home/todo/list', [App\Http\Controllers\ToDoController::class, 'list'])->name('todo.list');

Route::post('/home/todo/add', [App\Http\Controllers\ToDoController::class, 'add'])->name('todo.add');

Route::get('/home/todo/detail', [App\Http\Controllers\ToDoController::class, 'detail'])->name('todo.detail');

Route::get('/home/todo/edit', [App\Http\Controllers\ToDoController::class, 'edit'])->name('todo.edit');

Route::post('/home/todo/delete', [App\Http\Controllers\ToDoController::class, 'delete'])->name('todo.delete');

Route::get('/home/todo/get_template', [App\Http\Controllers\ToDoController::class, 'get_template'])->name('todo.get_template');

Route::get('/home/workflow/new', [App\Http\Controllers\WorkFlowController::class, 'new']);

Route::get('/home/workflow/list', [App\Http\Controllers\WorkFlowController::class, 'list'])->name('workflow.list');

Route::post('/home/workflow/add', [App\Http\Controllers\WorkFlowController::class, 'add'])->name('workflow.add');

Route::get('/home/workflow/detail', [App\Http\Controllers\WorkFlowController::class, 'detail'])->name('workflow.detail');

Route::post('/home/workflow/edit', [App\Http\Controllers\WorkFlowController::class, 'edit_form'])->name('workflow.edit_form');

Route::post('/home/workflow/update', [App\Http\Controllers\WorkFlowController::class, 'update'])->name('workflow.update');

Route::post('/home/workflow/delete', [App\Http\Controllers\WorkFlowController::class, 'delete'])->name('workflow.delete');

Route::get('/home/representation/schedule', [App\Http\Controllers\RepresentationController::class, 'schedule'])->name('representation.schedule');

Route::get('/home/representation/todo', [App\Http\Controllers\RepresentationController::class, 'todo'])->name('representation.todo');

Route::post('/home/representation/todo/update', [App\Http\Controllers\RepresentationController::class, 'todo_update'])->name('representation.todo_update');

Route::post('/home/representation/todo/done', [App\Http\Controllers\RepresentationController::class, 'todo_done'])->name('representation.todo_done');