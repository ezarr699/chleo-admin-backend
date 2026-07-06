<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Provider
 * @file        TenantManagementServiceProvider.php
 * @path        app/Modules/TenantManagement/TenantManagementServiceProvider.php
 * @description Bootstrap modul TenantManagement: binding interface ke
 *              implementasi HTTP client.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\TenantManagement;

use Illuminate\Support\ServiceProvider;
use App\Modules\TenantManagement\Contracts\TenantApiRepositoryInterface;
use App\Modules\TenantManagement\Repositories\TenantApiRepository;

final class TenantManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TenantApiRepositoryInterface::class, TenantApiRepository::class);
    }
}
