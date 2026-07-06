<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Service
 * @file        AuthService.php
 * @path        app/Modules/Auth/Services/AuthService.php
 * @description Business logic autentikasi: verifikasi kredensial login
 *              staff (user internal Chleo, terpisah dari user tenant).
 * @author      [Nama Developer]
 * @since       v1.0.0
 * @ref         https://laravel.com/docs/13.x/sanctum
 * ============================================================
 */

namespace App\Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Modules\Auth\Contracts\AuthRepositoryInterface;

final class AuthService
{
    public function __construct(
        private readonly AuthRepositoryInterface $authRepository,
    ) {}

    /**
     * Verifikasi kredensial login. Melempar ValidationException jika tidak valid.
     */
    public function attempt(string $email, string $password): User
    {
        $user = $this->authRepository->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        return $user;
    }
}
