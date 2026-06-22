<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Request
 * @file        StoreTenantRequest.php
 * @path        Modules/TenantManagement/Requests/StoreTenantRequest.php
 * @description Validasi untuk endpoint POST /api/v1/tenants. Validasi
 *              format saja di sini — keunikan slug ditegakkan oleh
 *              chleo-backend (yang punya akses ke data tenant sesungguhnya)
 *              dan dipropagasikan sebagai 422 oleh TenantApiRepository.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace Modules\TenantManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTenantRequest extends FormRequest
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
            'slug' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-z0-9-]+$/'],
            'admin' => ['required', 'array'],
            'admin.name' => ['required', 'string', 'max:255'],
            'admin.email' => ['required', 'email'],
            'admin.password' => ['required', 'string', 'min:8'],
        ];
    }
}
