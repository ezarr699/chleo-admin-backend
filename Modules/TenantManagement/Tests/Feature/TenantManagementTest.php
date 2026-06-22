<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Test > Feature
 * @file        TenantManagementTest.php
 * @path        Modules/TenantManagement/Tests/Feature/TenantManagementTest.php
 * @description Test HTTP endpoint manajemen tenant dari sisi admin-backend.
 *              chleo-backend di-fake lewat Http::fake() — test ini tidak
 *              butuh chleo-backend sungguhan berjalan.
 * @covers      Modules/TenantManagement/Controllers/TenantController.php
 * @covers      Modules/TenantManagement/Controllers/TenantDomainController.php
 * @covers      Modules/TenantManagement/Repositories/TenantApiRepository.php
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

use App\Models\User;
use Illuminate\Support\Facades\Http;

function fakeTenant(array $overrides = []): array
{
    return array_merge([
        'id' => 'acme',
        'subdomain' => 'acme.localhost',
        'suspended' => false,
        'domains' => [['id' => 1, 'domain' => 'acme.localhost']],
        'created_at' => now()->toIso8601String(),
    ], $overrides);
}

it('rejects tenant management requests when not authenticated as staff', function () {
    $this->getJson('/api/v1/tenants')->assertStatus(401);
});

it('lists tenants', function () {
    Http::fake([
        '*/api/v1/tenants*' => Http::response([
            'success' => true,
            'data' => [fakeTenant()],
            'meta' => ['current_page' => 1, 'per_page' => 15, 'total' => 1],
        ]),
    ]);

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tenants')
        ->assertStatus(200)
        ->assertJsonPath('data.0.id', 'acme')
        ->assertJsonPath('meta.total', 1);

    Http::assertSent(fn ($request) => $request->hasHeader('X-Management-Token')
        && str_contains($request->url(), '/api/v1/tenants'));
});

it('creates a tenant', function () {
    Http::fake([
        '*/api/v1/tenants' => Http::response(['success' => true, 'data' => fakeTenant()], 201),
    ]);

    $this->actingAs(User::factory()->create())
        ->postJson('/api/v1/tenants', [
            'slug' => 'acme',
            'admin' => ['name' => 'Admin Acme', 'email' => 'admin@acme.test', 'password' => 'Password123!'],
        ])
        ->assertStatus(201)
        ->assertJsonPath('data.id', 'acme');
});

it('propagates a 422 from chleo-backend when the slug is taken', function () {
    Http::fake([
        '*/api/v1/tenants' => Http::response([
            'success' => false,
            'message' => 'Data tidak valid.',
            'errors' => ['slug' => ['Subdomain sudah digunakan.']],
        ], 422),
    ]);

    $this->actingAs(User::factory()->create())
        ->postJson('/api/v1/tenants', [
            'slug' => 'acme',
            'admin' => ['name' => 'Admin Acme', 'email' => 'admin@acme.test', 'password' => 'Password123!'],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

it('suspends a tenant', function () {
    Http::fake([
        '*/api/v1/tenants/acme' => Http::response(['success' => true, 'data' => fakeTenant(['suspended' => true])]),
    ]);

    $this->actingAs(User::factory()->create())
        ->patchJson('/api/v1/tenants/acme', ['suspended' => true])
        ->assertStatus(200)
        ->assertJsonPath('data.suspended', true);
});

it('returns 404 when chleo-backend reports the tenant is missing', function () {
    Http::fake([
        '*/api/v1/tenants/missing' => Http::response(['success' => false, 'message' => 'Tenant tidak ditemukan.'], 404),
    ]);

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tenants/missing')
        ->assertStatus(404);
});

it('deletes a tenant', function () {
    Http::fake([
        '*/api/v1/tenants/acme' => Http::response(['success' => true, 'data' => null]),
    ]);

    $this->actingAs(User::factory()->create())
        ->deleteJson('/api/v1/tenants/acme')
        ->assertStatus(200);
});

it('returns tenant stats', function () {
    Http::fake([
        '*/api/v1/tenants/stats' => Http::response([
            'success' => true,
            'data' => ['total' => 3, 'active' => 2, 'suspended' => 1],
        ]),
    ]);

    $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/tenants/stats')
        ->assertStatus(200)
        ->assertJsonPath('data.suspended', 1);
});
