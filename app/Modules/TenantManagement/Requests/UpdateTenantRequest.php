<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Request
 * @file        UpdateTenantRequest.php
 * @path        app/Modules/TenantManagement/Requests/UpdateTenantRequest.php
 * @description Validasi request untuk update tenant details.
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\TenantManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin.name'     => ['required', 'string', 'max:255'],
            'admin.email'    => ['required', 'email', 'max:255'],
            'admin.password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
