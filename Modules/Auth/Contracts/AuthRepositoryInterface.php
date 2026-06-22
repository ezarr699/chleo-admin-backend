<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Contract (Interface)
 * @file        AuthRepositoryInterface.php
 * @path        Modules/Auth/Contracts/AuthRepositoryInterface.php
 * @description Kontrak untuk implementasi Repository Auth.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace Modules\Auth\Contracts;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User;
}
