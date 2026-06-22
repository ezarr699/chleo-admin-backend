<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Repository
 * @file        AuthRepository.php
 * @path        Modules/Auth/Repositories/AuthRepository.php
 * @description Akses data User (staff) untuk kebutuhan autentikasi.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace Modules\Auth\Repositories;

use App\Models\User;
use Modules\Auth\Contracts\AuthRepositoryInterface;

final class AuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
