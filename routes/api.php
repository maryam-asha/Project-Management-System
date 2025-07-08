<?php

use App\Http\Controllers\Api\AttachmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\NotificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('login',  [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class,'logout']);

    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{project}/add-users', [ProjectController::class, 'addUsers']);
    Route::post('projects/{project}/remove-users', [ProjectController::class, 'removeUsers']);
    Route::post('projects/{project}/assign-team', [ProjectController::class, 'assignTeam']);

    Route::apiResource('teams', TeamController::class);
    Route::post('teams/{team}/add-users', [TeamController::class, 'addUsers']);
    Route::post('teams/{team}/remove-users', [TeamController::class, 'removeUsers']);

    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('notifications', NotificationController::class);
    Route::post('notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::apiResource('comments', CommentController::class);
    Route::post('tasks/{task}/comments', [CommentController::class, 'storeForTask']);
    Route::post('projects/{project}/comments', [CommentController::class, 'storeForProject']);
    Route::post('tasks/{task}/attachments', [AttachmentController::class, 'storeForTask']);
    Route::post('projects/{project}/attachments', [AttachmentController::class, 'storeForProject']);
    Route::post('comments/{comment}/attachments', [AttachmentController::class, 'storeForComment']);
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy']);
    Route::get('attachments/{attachment}', [AttachmentController::class, 'show']);

});

