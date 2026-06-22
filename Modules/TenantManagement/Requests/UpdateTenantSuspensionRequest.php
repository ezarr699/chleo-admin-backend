<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Request
 * @file        UpdateTenantSuspensionRequest.php
 * @path        Modules/TenantManagement/Requests/UpdateTenantSuspensionRequest.php
 * @description Validasi untuk endpoint PATCH /api/v1/tenants/{id}.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace Modules\TenantManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTenantSuspensionRequest extends FormRequest
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
            'suspended' => ['required', 'boolean'],
        ];
    }
}
