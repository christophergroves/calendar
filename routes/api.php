<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\VenueController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/sessions/content', [SessionController::class, 'content']);
Route::get('/session/edit/dialog/content', [SessionController::class, 'getEditDialogContent']);
Route::put('/session/edit/store', [SessionController::class, 'store']);

Route::put('activity/store', [ActivityController::class, 'store']);






