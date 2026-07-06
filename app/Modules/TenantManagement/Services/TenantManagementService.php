<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Service
 * @file        TenantManagementService.php
 * @path        app/Modules/TenantManagement/Services/TenantManagementService.php
 * @description Business logic pengelolaan tenant: list, detail, buat,
 *              suspend/resume, hapus, domain, statistik. Hanya
 *              mendelegasikan ke TenantApiRepositoryInterface (HTTP client
 *              ke chleo-backend) — tidak ada akses database lokal.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\TenantManagement\Services;

use App\Modules\TenantManagement\Contracts\TenantApiRepositoryInterface;

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
     * @return array<string, mixed>
     */
    public function create(string $slug, array $admin): array
    {
        return $this->tenantApiRepository->create($slug, $admin);
    }

    /** @return array<string, mixed> */
    public function setSuspended(string $id, bool $suspended): array
    {
        return $this->tenantApiRepository->setSuspended($id, $suspended);
    }

    public function delete(string $id): void
    {
        $this->tenantApiRepository->delete($id);
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

    /** @return array{total: int, active: int, suspended: int} */
    public function stats(): array
    {
        return $this->tenantApiRepository->stats();
    }
}
