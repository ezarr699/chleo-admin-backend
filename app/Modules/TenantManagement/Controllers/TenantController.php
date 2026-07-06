<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Controller
 * @file        TenantController.php
 * @path        app/Modules/TenantManagement/Controllers/TenantController.php
 * @description Handle HTTP request manajemen tenant: list, detail, buat,
 *              suspend/resume, hapus, statistik. Hanya proxy tipis ke
 *              TenantManagementService (yang memanggil chleo-backend).
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\TenantManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Modules\TenantManagement\Requests\StoreTenantRequest;
use App\Modules\TenantManagement\Requests\UpdateTenantSuspensionRequest;
use App\Modules\TenantManagement\Services\TenantManagementService;

final class TenantController extends Controller
{
    public function __construct(
        private readonly TenantManagementService $tenantManagementService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 15);
        $result = $this->tenantManagementService->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar tenant berhasil diambil.',
            'data' => $result['data'],
            'meta' => $result['meta'],
        ]);
    }

    public function store(StoreTenantRequest $request): JsonResponse
    {
        $tenant = $this->tenantManagementService->create(
            $request->string('slug')->toString(),
            $request->array('admin'),
        );

        return response()->json([
            'success' => true,
            'message' => 'Tenant berhasil dibuat.',
            'data' => $tenant,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail tenant berhasil diambil.',
            'data' => $this->tenantManagementService->find($id),
        ]);
    }

    public function update(UpdateTenantSuspensionRequest $request, string $id): JsonResponse
    {
        $tenant = $this->tenantManagementService->setSuspended($id, $request->boolean('suspended'));

        return response()->json([
            'success' => true,
            'message' => $tenant['suspended']
                ? 'Tenant berhasil ditangguhkan.'
                : 'Tenant berhasil diaktifkan kembali.',
            'data' => $tenant,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->tenantManagementService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Tenant berhasil dihapus.',
            'data' => null,
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Statistik tenant berhasil diambil.',
            'data' => $this->tenantManagementService->stats(),
        ]);
    }
}
