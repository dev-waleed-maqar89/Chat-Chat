<?php

use App\Events\Main\Users\Online;
use App\Http\Controllers\Main\ChatController;
use App\Http\Controllers\Main\mainController;
use App\Http\Controllers\Main\UserController;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
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

Route::get('/getAuthUser', function () {
    $user = Auth::user();
    return response()->json(['user' => $user]);
});
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout']);
Route::post('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [userController::class, 'show']);
Route::get('/conversations', [ChatController::class, 'index']);
Route::post('/conversations/search', [ChatController::class, 'search']);
Route::get('/users/{userId}/chat', [UserController::class, 'showChat']);
Route::get('/conversations/{id}', [ChatController::class, 'show']);
Route::post('/conversations/sendMessage/', [ChatController::class, 'sendMessage']);
Route::post('/conversations/{id}/accept', [ChatController::class, 'accept']);
Route::post('/conversations/{id}/markAsRead', [ChatController::class, 'markAsRead']);
Route::get('{any}', mainController::class)->where('any', '.*');