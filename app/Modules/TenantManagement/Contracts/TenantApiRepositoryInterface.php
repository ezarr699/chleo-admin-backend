<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Contract (Interface)
 * @file        TenantApiRepositoryInterface.php
 * @path        app/Modules/TenantManagement/Contracts/TenantApiRepositoryInterface.php
 * @description Kontrak untuk implementasi Repository TenantManagement.
 *              Berbeda dari Repository biasa: implementasinya memanggil
 *              HTTP API chleo-backend, bukan database lokal — aplikasi ini
 *              tidak pernah menyentuh database tenant/domain secara langsung.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\TenantManagement\Contracts;

interface TenantApiRepositoryInterface
{
    /** @return array{data: array<int, array<string, mixed>>, meta: array<string, mixed>} */
    public function paginate(int $perPage = 15): array;

    /** @return array<string, mixed>|null */
    public function find(string $id): ?array;

    /**
     * @param  array<string, mixed>  $admin  ['name' => string, 'email' => string, 'password' => string]
     * @return array<string, mixed>
     */
    public function create(string $slug, array $admin): array;

    /** @return array<string, mixed> */
    public function setSuspended(string $id, bool $suspended): array;

    public function delete(string $id): void;

    public function restore(string $id): void;

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(string $id, array $payload): array;

    /** @return array<string, mixed> */
    public function addDomain(string $tenantId, string $domain): array;

    /** @return array<string, mixed> */
    public function removeDomain(string $tenantId, int $domainId): array;

    /** @return array{total: int, active: int, suspended: int} */
    public function stats(): array;
}
