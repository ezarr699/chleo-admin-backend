<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Provider
 * @file        AuthServiceProvider.php
 * @path        app/Modules/Auth/AuthServiceProvider.php
 * @description Bootstrap modul Auth: binding interface ke implementasi.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\Auth;

use Illuminate\Support\ServiceProvider;
use App\Modules\Auth\Contracts\AuthRepositoryInterface;
use App\Modules\Auth\Repositories\AuthRepository;

final class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
    }
}
