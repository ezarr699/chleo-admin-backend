<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Request
 * @file        StoreTenantDomainRequest.php
 * @path        Modules/TenantManagement/Requests/StoreTenantDomainRequest.php
 * @description Validasi untuk endpoint POST /api/v1/tenants/{id}/domains.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace Modules\TenantManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTenantDomainRequest extends FormRequest
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
            'domain' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9-]+$/'],
        ];
    }
}
