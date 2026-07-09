<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Service
 * @file        TenantManagementService.php
 * @path        app/Modules/TenantManagement/Services/TenantManagementService.php
 * @description Business logic pengelolaan tenant: list, detail, buat,
 *              suspend/resume, hapus, domain, statistik. Mendelegasikan
 *              ke TenantApiRepositoryInterface (HTTP ke chleo-backend) dan
 *              mencatat log aktivitas di tabel lokal tenant_logs.
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\TenantManagement\Services;

use App\Models\User;
use App\Modules\TenantManagement\Contracts\TenantApiRepositoryInterface;
use App\Modules\TenantManagement\Models\TenantLog;
use Illuminate\Pagination\LengthAwarePaginator;

final class TenantManagementService
{
    public function __construct(
        private readonly TenantApiRepositoryInterface $tenantApiRepository,
    ) {}

    /** @return array{data: array<int, array<string, mixed>>, meta: array<string, mixed>} */
    public function paginate(int $perPage = 15): array
    {
        return $this->tenantApiRepository->paginate($perPage);
    }

    /** @return array<string, mixed> */
    public function find(string $id): array
    {
        $tenant = $this->tenantApiRepository->find($id);

        abort_if($tenant === null, 404, 'Tenant tidak ditemukan.');

        return $tenant;
    }

    /**
     * @param  array<string, mixed>  $admin
     * @param  User|null  $createdBy  Staff yang membuat tenant (dari auth())
     * @return array<string, mixed>
     */
    public function create(string $slug, array $admin, ?User $createdBy = null): array
    {
        $tenant = $this->tenantApiRepository->create($slug, $admin);

        // Catat log pembuatan tenant secara lokal
        TenantLog::create([
            'tenant_id'             => $tenant['id'],
            'subdomain'             => $tenant['subdomain'] ?? $slug,
            'admin_name'            => $admin['name'] ?? '',
            'admin_email'           => $admin['email'] ?? '',
            'admin_password_masked' => '••••••••',
            'created_by_user_id'    => $createdBy?->id,
            'status'                => 'aktif',
        ]);

        return $tenant;
    }

    /** @return array<string, mixed> */
    public function setSuspended(string $id, bool $suspended): array
    {
        $tenant = $this->tenantApiRepository->setSuspended($id, $suspended);

        // Update status di log lokal
        TenantLog::where('tenant_id', $id)
            ->update(['status' => $suspended ? 'ditangguhkan' : 'aktif']);

        return $tenant;
    }

    public function delete(string $id): void
    {
        $this->tenantApiRepository->delete($id);

        // Tandai sebagai deleted di log lokal
        TenantLog::where('tenant_id', $id)
            ->update(['status' => 'deleted']);
    }

    public function restore(string $id): void
    {
        $this->tenantApiRepository->restore($id);

        // Kembalikan status ke aktif di log lokal
        TenantLog::where('tenant_id', $id)
            ->update(['status' => 'aktif']);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateTenant(string $id, array $payload): array
    {
        $tenant = $this->tenantApiRepository->update($id, $payload);

        // Jika data admin berubah, kita bisa update log lokal juga agar mencerminkan admin yang baru
        if (isset($payload['admin']['name']) || isset($payload['admin']['email'])) {
            $updates = [];
            if (isset($payload['admin']['name'])) {
                $updates['admin_name'] = $payload['admin']['name'];
            }
            if (isset($payload['admin']['email'])) {
                $updates['admin_email'] = $payload['admin']['email'];
            }
            if (!empty($updates)) {
                TenantLog::where('tenant_id', $id)->update($updates);
            }
        }

        return $tenant;
    }

    /** @return array<string, mixed> */
    public function addDomain(string $tenantId, string $domain): array
    {
        return $this->tenantApiRepository->addDomain($tenantId, $domain);
    }

    /** @return array<string, mixed> */
    public function removeDomain(string $tenantId, int $domainId): array
    {
        return $this->tenantApiRepository->removeDomain($tenantId, $domainId);
    }

    /** @return array{total: int, active: int, suspended: int, deleted: int} */
    public function stats(): array
    {
        $apiStats = $this->tenantApiRepository->stats();

        // Tambahkan jumlah deleted dari log lokal
        $deleted = TenantLog::where('status', 'deleted')->count();

        return array_merge($apiStats, ['deleted' => $deleted]);
    }

    /**
     * Ambil daftar log tenant (paginated, filterable, sortable).
     *
     * @param  int     $perPage
     * @param  string  $sortOrder  'asc' | 'desc'
     * @param  string|null  $status  'aktif' | 'ditangguhkan' | 'deleted' | null (semua)
     * @return LengthAwarePaginator
     */
    public function logs(int $perPage = 15, string $sortOrder = 'desc', ?string $status = null): LengthAwarePaginator
    {
        $query = TenantLog::with('createdBy')
            ->orderBy('created_at', $sortOrder);

        if ($status !== null && in_array($status, ['aktif', 'ditangguhkan', 'deleted'], true)) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }
}
