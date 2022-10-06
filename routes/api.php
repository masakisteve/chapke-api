<?php

use App\Http\Controllers\API\NotificationsController;
use App\Http\Controllers\API\PaymentrequestsController;
use App\Http\Controllers\API\TransactionsController;
use App\Http\Controllers\API\UserdataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/add-notifications', [NotificationsController::class, 'store']);
Route::post('/add-paymentrequests', [PaymentrequestsController::class, 'store']);
Route::post('/add-transactions', [TransactionsController::class, 'store']);
Route::post('/add-userdata', [UserdataController::class, 'store']);

Route::get('/get-notifications', [NotificationsController::class, 'index']);
Route::get('/get-paymentrequests', [PaymentrequestsController::class, 'index']);
Route::get('/get-transactions', [TransactionsController::class, 'index']);
Route::get('/get-userdata', [UserdataController::class, 'index']);

Route::post('/edit-notifications/{id}', [NotificationsController::class, 'edit']);
Route::post('/edit-paymentrequests/{id}', [PaymentrequestsController::class, 'edit']);
Route::post('/edit-transactions/{id}', [TransactionsController::class, 'edit']);
Route::post('/edit-userdata/{id}', [UserdataController::class, 'edit']);

Route::put('/update-notifications/{id}', [NotificationsController::class, 'update']);
Route::put('/update-paymentrequests/{id}', [PaymentrequestsController::class, 'update']);
Route::put('/update-transactions/{id}', [TransactionsController::class, 'update']);
Route::put('/update-userdata/{id}', [UserdataController::class, 'update']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
