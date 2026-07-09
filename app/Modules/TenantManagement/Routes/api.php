<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Route
 * @file        api.php
 * @path        app/Modules/TenantManagement/Routes/api.php
 * @description Route HTTP manajemen tenant, hanya untuk staff yang sudah
 *              login (auth:sanctum).
 * @since       v1.0.0
 * ============================================================
 */

use Illuminate\Support\Facades\Route;
use App\Modules\TenantManagement\Controllers\TenantController;
use App\Modules\TenantManagement\Controllers\TenantDomainController;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('tenants',         [TenantController::class, 'index']);
    Route::post('tenants',        [TenantController::class, 'store']);
    Route::get('tenants/stats',   [TenantController::class, 'stats']);
    Route::get('tenants/logs',    [TenantController::class, 'logs']);
    Route::get('tenants/{id}',    [TenantController::class, 'show']);
    Route::put('tenants/{id}',    [TenantController::class, 'updateDetails']);
    Route::patch('tenants/{id}',  [TenantController::class, 'update']);
    Route::delete('tenants/{id}', [TenantController::class, 'destroy']);
    Route::post('tenants/{id}/restore', [TenantController::class, 'restore']);

    Route::post('tenants/{tenantId}/domains',               [TenantDomainController::class, 'store']);
    Route::delete('tenants/{tenantId}/domains/{domainId}',  [TenantDomainController::class, 'destroy']);
});
