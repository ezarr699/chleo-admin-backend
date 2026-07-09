<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Repository
 * @file        TenantApiRepository.php
 * @path        app/Modules/TenantManagement/Repositories/TenantApiRepository.php
 * @description Akses data tenant lewat HTTP API chleo-backend
 *              (server-to-server, header X-Management-Token), bukan lewat
 *              database lokal — aplikasi ini tidak punya akses langsung ke
 *              database tenants/domains milik chleo-backend.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\TenantManagement\Repositories;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Modules\TenantManagement\Contracts\TenantApiRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TenantApiRepository implements TenantApiRepositoryInterface
{
    /** @return array{data: array<int, array<string, mixed>>, meta: array<string, mixed>} */
    public function paginate(int $perPage = 15): array
    {
        $response = $this->client()->get('tenants', ['per_page' => $perPage]);

        $this->throwIfFailed($response);

        return [
            'data' => $response->json('data'),
            'meta' => $response->json('meta'),
        ];
    }

    /** @return array<string, mixed>|null */
    public function find(string $id): ?array
    {
        $response = $this->client()->get("tenants/{$id}");

        if ($response->status() === 404) {
            return null;
        }

        $this->throwIfFailed($response);

        return $response->json('data');
    }

    /**
     * @param  array<string, mixed>  $admin
     * @return array<string, mixed>
     */
    public function create(string $slug, array $admin): array
    {
        $response = $this->client()->post('tenants', ['slug' => $slug, 'admin' => $admin]);

        $this->throwIfFailed($response);

        return $response->json('data');
    }

    /** @return array<string, mixed> */
    public function setSuspended(string $id, bool $suspended): array
    {
        $response = $this->client()->patch("tenants/{$id}", ['suspended' => $suspended]);

        $this->throwIfFailed($response, $id);

        return $response->json('data');
    }

    public function delete(string $id): void
    {
        $response = $this->client()->delete("tenants/{$id}");

        $this->throwIfFailed($response, $id);
    }

    public function restore(string $id): void
    {
        $response = $this->client()->post("tenants/{$id}/restore");

        $this->throwIfFailed($response, $id);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(string $id, array $payload): array
    {
        $response = $this->client()->put("tenants/{$id}", $payload);

        $this->throwIfFailed($response, $id);

        return $response->json('data');
    }

    /** @return array<string, mixed> */
    public function addDomain(string $tenantId, string $domain): array
    {
        $response = $this->client()->post("tenants/{$tenantId}/domains", ['domain' => $domain]);

        $this->throwIfFailed($response, $tenantId);

        return $response->json('data');
    }

    /** @return array<string, mixed> */
    public function removeDomain(string $tenantId, int $domainId): array
    {
        $response = $this->client()->delete("tenants/{$tenantId}/domains/{$domainId}");

        $this->throwIfFailed($response, $tenantId);

        return $response->json('data');
    }

    /** @return array{total: int, active: int, suspended: int} */
    public function stats(): array
    {
        $response = $this->client()->get('tenants/stats');

        $this->throwIfFailed($response);

        return $response->json('data');
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('services.chleo.base_url'), '/').'/api/v1/')
            ->withHeaders(['X-Management-Token' => config('services.chleo.management_token')])
            ->acceptJson();
    }

    private function throwIfFailed(Response $response, ?string $tenantId = null): void
    {
        if ($response->status() === 404) {
            throw new NotFoundHttpException($tenantId ? "Tenant {$tenantId} tidak ditemukan." : 'Tenant tidak ditemukan.');
        }

        if ($response->status() === 422) {
            throw ValidationException::withMessages($response->json('errors') ?? []);
        }

        if ($response->failed()) {
            throw new \RuntimeException("Gagal menghubungi chleo-backend (HTTP {$response->status()}).");
        }
    }
}
