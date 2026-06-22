<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Provider
 * @file        AuthServiceProvider.php
 * @path        Modules/Auth/AuthServiceProvider.php
 * @description Bootstrap modul Auth: binding interface ke implementasi.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace Modules\Auth;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\Contracts\AuthRepositoryInterface;
use Modules\Auth\Repositories\AuthRepository;

final class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
    }
}
