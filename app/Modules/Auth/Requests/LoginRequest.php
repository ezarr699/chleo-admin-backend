<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Request
 * @file        LoginRequest.php
 * @path        app/Modules/Auth/Requests/LoginRequest.php
 * @description Validasi dan otorisasi untuk endpoint POST /auth/login.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * @ref         https://laravel.com/docs/13.x/validation#form-request-validation
 * ============================================================
 */

namespace App\Modules\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
