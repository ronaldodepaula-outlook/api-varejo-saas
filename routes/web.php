<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ApiDocsController;

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

// Swagger UI (documentação da API)
Route::get('/docs', [ApiDocsController::class, 'swagger']);
Route::get('/api/openapi.json', [ApiDocsController::class, 'openapi']);


// Página para resetar senha via token
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');

// Formulário envia a nova senha
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');
