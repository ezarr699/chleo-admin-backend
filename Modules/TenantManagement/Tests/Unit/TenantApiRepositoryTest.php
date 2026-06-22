<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Test > Unit
 * @file        TenantApiRepositoryTest.php
 * @path        Modules/TenantManagement/Tests/Unit/TenantApiRepositoryTest.php
 * @description Unit test TenantApiRepository: memastikan request HTTP yang
 *              dikirim ke chleo-backend (method, URL, header token, body)
 *              benar, dan response di-map dengan benar.
 * @covers      Modules/TenantManagement/Repositories/TenantApiRepository.php
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

use Illuminate\Support\Facades\Http;
use Modules\TenantManagement\Repositories\TenantApiRepository;

beforeEach(function () {
    config(['services.chleo.base_url' => 'http://chleo-backend.test', 'services.chleo.management_token' => 'secret-token']);
    $this->repository = new TenantApiRepository();
});

it('sends the management token and correct method/url when suspending a tenant', function () {
    Http::fake([
        'chleo-backend.test/*' => Http::response(['success' => true, 'data' => ['id' => 'acme', 'suspended' => true]]),
    ]);

    $result = $this->repository->setSuspended('acme', true);

    expect($result['suspended'])->toBeTrue();

    Http::assertSent(fn ($request) => $request->method() === 'PATCH'
        && $request->url() === 'http://chleo-backend.test/api/v1/tenants/acme'
        && $request->hasHeader('X-Management-Token', 'secret-token')
        && $request['suspended'] === true);
});

it('returns null from find() when chleo-backend returns 404', function () {
    Http::fake([
        'chleo-backend.test/*' => Http::response(['success' => false], 404),
    ]);

    expect($this->repository->find('missing'))->toBeNull();
});

it('throws a ValidationException when chleo-backend returns 422', function () {
    Http::fake([
        'chleo-backend.test/*' => Http::response([
            'success' => false,
            'errors' => ['slug' => ['Subdomain sudah digunakan.']],
        ], 422),
    ]);

    expect(fn () => $this->repository->create('acme', [
        'name' => 'Admin',
        'email' => 'admin@acme.test',
        'password' => 'Password123!',
    ]))->toThrow(Illuminate\Validation\ValidationException::class);
});
