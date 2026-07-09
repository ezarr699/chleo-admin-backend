<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Controller
 * @file        TenantController.php
 * @path        app/Modules/TenantManagement/Controllers/TenantController.php
 * @description Handle HTTP request manajemen tenant: list, detail, buat,
 *              suspend/resume, hapus, statistik, dan log. Hanya proxy
 *              tipis ke TenantManagementService.
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\TenantManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Modules\TenantManagement\Requests\StoreTenantRequest;
use App\Modules\TenantManagement\Requests\UpdateTenantRequest;
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
            'data'    => $result['data'],
            'meta'    => $result['meta'],
        ]);
    }

    public function store(StoreTenantRequest $request): JsonResponse
    {
        $tenant = $this->tenantManagementService->create(
            $request->string('slug')->toString(),
            $request->array('admin'),
            $request->user(),    // rekam siapa yang membuat
        );

        return response()->json([
            'success' => true,
            'message' => 'Tenant berhasil dibuat.',
            'data'    => $tenant,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail tenant berhasil diambil.',
            'data'    => $this->tenantManagementService->find($id),
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
            'data'    => null,
        ]);
    }

    public function restore(string $id): JsonResponse
    {
        $this->tenantManagementService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Tenant berhasil dipulihkan.',
            'data'    => null,
        ]);
    }

    public function updateDetails(UpdateTenantRequest $request, string $id): JsonResponse
    {
        $tenant = $this->tenantManagementService->updateTenant(
            $id,
            ['admin' => $request->array('admin')]
        );

        return response()->json([
            'success' => true,
            'message' => 'Detail tenant berhasil diperbarui.',
            'data'    => $tenant,
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Statistik tenant berhasil diambil.',
            'data'    => $this->tenantManagementService->stats(),
        ]);
    }

    /**
     * GET /tenants/logs
     * Query params: per_page, sort (asc|desc), status (aktif|ditangguhkan|deleted)
     */
    public function logs(Request $request): JsonResponse
    {
        $perPage   = (int) $request->integer('per_page', 15);
        $sortOrder = $request->string('sort', 'desc')->lower()->toString();
        $sortOrder = in_array($sortOrder, ['asc', 'desc'], true) ? $sortOrder : 'desc';
        $status    = $request->filled('status') ? $request->string('status')->toString() : null;

        $paginator = $this->tenantManagementService->logs($perPage, $sortOrder, $status);

        $items = collect($paginator->items())->map(fn($log) => [
            'id'                   => $log->id,
            'tenant_id'            => $log->tenant_id,
            'subdomain'            => $log->subdomain,
            'admin_name'           => $log->admin_name,
            'admin_email'          => $log->admin_email,
            'admin_password_masked'=> $log->admin_password_masked,
            'created_by'           => $log->createdBy ? [
                'id'    => $log->createdBy->id,
                'name'  => $log->createdBy->name,
                'email' => $log->createdBy->email,
            ] : null,
            'status'               => $log->status,
            'created_at'           => $log->created_at?->toIso8601String(),
        ])->all();

        return response()->json([
            'success' => true,
            'message' => 'Log tenant berhasil diambil.',
            'data'    => $items,
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }
}
