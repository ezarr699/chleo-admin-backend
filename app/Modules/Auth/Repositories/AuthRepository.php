<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Repository
 * @file        AuthRepository.php
 * @path        app/Modules/Auth/Repositories/AuthRepository.php
 * @description Akses data User (staff) untuk kebutuhan autentikasi.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\Auth\Repositories;

use App\Models\User;
use App\Modules\Auth\Contracts\AuthRepositoryInterface;

final class AuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
