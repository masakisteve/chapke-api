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

Route::post('/login', [UserdataController::class, 'login']);
Route::post('/register', [UserdataController::class, 'register']);

Route::post('/withdraw_money', [TransactionsController::class, 'withdraw_money']);
Route::post('/user_balance', [TransactionsController::class, 'user_balance']);
Route::post('/send_money', [TransactionsController::class, 'send_money']);

Route::post('/get_notification_count', [NotificationsController::class, 'get_notification_count']);
Route::post('/get_contacts', [UserdataController::class, 'get_contacts']);
Route::post('/pin_change', [UserdataController::class, 'pin_change']);

Route::get('/account_statements/{id}', [TransactionsController::class, 'account_statements']);
Route::get('/get_notifications/{id}', [NotificationsController::class, 'get_notifications']);

Route::post('/initiate_request', [PaymentrequestsController::class, 'initiate_request']);
Route::get('/get_payment_request', [PaymentrequestsController::class, 'get_payment_request']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 