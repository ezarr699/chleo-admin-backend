<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Controller
 * @file        TenantDomainController.php
 * @path        Modules/TenantManagement/Controllers/TenantDomainController.php
 * @description Handle HTTP request manajemen domain tenant: tambah dan
 *              hapus. Hanya proxy tipis ke TenantManagementService.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace Modules\TenantManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\TenantManagement\Requests\StoreTenantDomainRequest;
use Modules\TenantManagement\Services\TenantManagementService;

final class TenantDomainController extends Controller
{
    public function __construct(
        private readonly TenantManagementService $tenantManagementService,
    ) {}

    public function store(StoreTenantDomainRequest $request, string $tenantId): JsonResponse
    {
        $tenant = $this->tenantManagementService->addDomain(
            $tenantId,
            $request->string('domain')->toString(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Domain berhasil ditambahkan.',
            'data' => $tenant,
        ], 201);
    }

    public function destroy(string $tenantId, int $domainId): JsonResponse
    {
        $tenant = $this->tenantManagementService->removeDomain($tenantId, $domainId);

        return response()->json([
            'success' => true,
            'message' => 'Domain berhasil dihapus.',
            'data' => $tenant,
        ]);
    }
}
