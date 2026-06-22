<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Provider
 * @file        TenantManagementServiceProvider.php
 * @path        Modules/TenantManagement/TenantManagementServiceProvider.php
 * @description Bootstrap modul TenantManagement: binding interface ke
 *              implementasi HTTP client.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace Modules\TenantManagement;

use Illuminate\Support\ServiceProvider;
use Modules\TenantManagement\Contracts\TenantApiRepositoryInterface;
use Modules\TenantManagement\Repositories\TenantApiRepository;

final class TenantManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TenantApiRepositoryInterface::class, TenantApiRepository::class);
    }
}
