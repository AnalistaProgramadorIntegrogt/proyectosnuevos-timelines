<?php

use Illuminate\Support\Facades\Route;
use App\Models\Task;
use App\Models\User;
use App\Mail\TaskStartsSoonMail;

Route::get('/mail-preview', function () {
    $task = Task::first() ?? new Task(['title' => 'Diseño de Planos Estructurales']);
    $user = User::first() ?? new User(['name' => 'Arquitecto Prueba']);
    return new TaskStartsSoonMail($task, $user, 1);
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Azure OAuth Routes
Route::get('/auth/microsoft', [App\Http\Controllers\Auth\AzureAuthController::class, 'redirect'])->name('auth.microsoft');
Route::get('/auth/microsoft/callback', [App\Http\Controllers\Auth\AzureAuthController::class, 'callback'])->name('auth.microsoft.callback');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management (Admin Only)
    Route::resource('users', App\Http\Controllers\UserController::class)->middleware('can:manage-users');

    Route::get('/projects', [App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [App\Http\Controllers\ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [App\Http\Controllers\ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::get('/deliverables/{version}/download', [App\Http\Controllers\DeliverableController::class, 'download'])->name('deliverables.download');

    // Task routes
    Route::get('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [App\Http\Controllers\TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
    Route::post('/tasks/{task}/submit', [App\Http\Controllers\TaskController::class, 'submit'])->name('tasks.submit');
    Route::post('/tasks/{task}/reopen', [App\Http\Controllers\TaskController::class, 'reopen'])->name('tasks.reopen');

    // Task list editor routes
    Route::get('/projects/{project}/tasks/edit-list', [App\Http\Controllers\ProjectTaskListController::class, 'index'])->name('projects.tasks.edit-list');
    Route::get('/projects/{project}/roadmap', [App\Http\Controllers\ProjectController::class, 'roadmap'])->name('projects.roadmap');
    Route::put('/projects/tasks/{task}', [App\Http\Controllers\ProjectTaskListController::class, 'update'])->name('projects.tasks.update');
    Route::post('/projects/{project}/tasks/reorder', [App\Http\Controllers\ProjectTaskListController::class, 'reorder'])->name('projects.tasks.reorder');
    Route::post('/projects/groups/{group}/tasks', [App\Http\Controllers\ProjectTaskListController::class, 'store'])->name('projects.tasks.store');
    Route::post('/projects/{project}/groups', [App\Http\Controllers\ProjectTaskListController::class, 'storeGroup'])->name('projects.groups.store');
    Route::put('/projects/groups/{group}', [App\Http\Controllers\ProjectTaskListController::class, 'updateGroup'])->name('projects.groups.update');
    Route::delete('/projects/groups/{group}', [App\Http\Controllers\ProjectTaskListController::class, 'deleteGroup'])->name('projects.groups.delete');
    Route::post('/projects/{project}/groups/reorder', [App\Http\Controllers\ProjectTaskListController::class, 'reorderGroups'])->name('projects.groups.reorder');

    Route::prefix('templates')->name('templates.')->middleware('can:manage-templates')->group(function () {
        Route::get('/', [App\Http\Controllers\TemplateController::class, 'index'])->name('index');
        Route::get('/{template}/edit', [App\Http\Controllers\TemplateController::class, 'edit'])->name('edit');
        Route::get('/{template}/versions', [App\Http\Controllers\TemplateController::class, 'versions'])->name('versions');
    });

    Route::get('/repository', App\Http\Livewire\Documents\DocumentExplorer::class)->name('repository.index');
});
