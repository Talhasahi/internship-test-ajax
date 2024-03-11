<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
Route::get('/', [TaskController::class, 'index']);
Route::get('/tasks', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::post('/tasks/{task}/complete', [TaskController::class, 'markAsComplete'])->name('tasks.markAsComplete');
Route::get('/tasks/sortByPriority', [TaskController::class, 'sortByPriority']);
Route::post('/tasks/{task}/update-priority', [TaskController::class, 'updatePriority']);