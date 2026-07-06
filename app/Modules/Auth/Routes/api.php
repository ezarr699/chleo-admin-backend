<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Route
 * @file        api.php
 * @path        app/Modules/Auth/Routes/api.php
 * @description Route HTTP untuk modul Auth: login, logout, me.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\AuthController;

Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
});
