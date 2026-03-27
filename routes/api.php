<?php

use App\Http\Controllers\Api\V1\TicketCommentController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth:sanctum', 'api.org', 'throttle:api'])->group(function () {
    // Tickets
    Route::middleware('api.scope:tickets:read')->group(function () {
        Route::get('/tickets', [TicketController::class, 'index']);
        Route::get('/tickets/{publicId}', [TicketController::class, 'show']);
    });
    Route::middleware('api.scope:tickets:write')->group(function () {
        Route::post('/tickets', [TicketController::class, 'store']);
        Route::patch('/tickets/{publicId}', [TicketController::class, 'update']);
    });

    // Comments
    Route::middleware('api.scope:comments:read')->group(function () {
        Route::get('/tickets/{publicId}/comments', [TicketCommentController::class, 'index']);
    });
    Route::middleware('api.scope:comments:write')->group(function () {
        Route::post('/tickets/{publicId}/comments', [TicketCommentController::class, 'store']);
    });

    // Webhooks
    Route::middleware('api.scope:webhooks:manage')->group(function () {
        Route::get('/webhooks', [WebhookController::class, 'index']);
        Route::post('/webhooks', [WebhookController::class, 'store']);
        Route::patch('/webhooks/{id}', [WebhookController::class, 'update']);
        Route::delete('/webhooks/{id}', [WebhookController::class, 'destroy']);
    });
});
