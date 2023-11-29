<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShortUrlController;

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

// - перенести эти маршруты в админку
Route::resource('short_url', ShortUrlController::class)
    ->only([
        'create', 'edit'
    ])
    ->missing(function (Request $request) {
        return response()->json([
            'status' => 'error'
        ]);
    });

Route::resource('short_url', ShortUrlController::class)
    ->only([
        'index', 'store', 'show', 'update', 'destroy'
    ])
    ->missing(function (Request $request) {
        return response()->json([
            'status' => 'error'
        ]);
    });

Route::get('/api/short_url/search', [ShortUrlController::class, 'search']);




// - роуты действия для альтернативной интеграции api
Route::get('/api/short_url/storeShortUrl', [ShortUrlController::class, 'storeShortUrl']);
// - возрощать другой ответ - более простой для обработки (status, resource, error)
