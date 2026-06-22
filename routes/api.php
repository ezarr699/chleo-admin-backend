<?php
/**
 * ============================================================
 * @layer       Route
 * @file        api.php
 * @path        routes/api.php
 * @description Aggregator: memuat Modules/*\/Routes/api.php milik setiap
 *              modul. Aplikasi ini single-context (bukan multi-tenant),
 *              jadi tidak ada split central/tenant seperti di chleo-backend.
 * @ref         https://laravel.com/docs/13.x/routing
 * ============================================================
 */

use Illuminate\Support\Facades\Route;

foreach (glob(base_path('Modules/*/Routes/api.php')) as $moduleRoutes) {
    Route::prefix('v1')->group($moduleRoutes);
}
