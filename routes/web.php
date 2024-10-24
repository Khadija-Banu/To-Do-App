<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::resource('tasks', TaskController::class)->middleware('auth');
Route::patch('/tasks/{task}/complete', [TaskController::class, 'markAsCompleted'])->name('tasks.complete');
Route::post('/tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
Route::get('/task/{id}', [TaskController::class, 'assignCreate'])->name('create.assign');
Route::get('/create/task', [TaskController::class, 'createTaskPermissions'])->name('create.task.permission');
Route::post('/create/task/assign', [TaskController::class, 'updateEmployeePermissions'])->name('create.task.assign');
