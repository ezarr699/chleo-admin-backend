<?php
/**
 * ============================================================
 * @file        2026_07_09_074616_create_tenant_logs_table.php
 * @description Tabel log aktivitas tenant: rekam siapa yang membuat tenant,
 *              kapan dibuat, dan status terkini (aktif/ditangguhkan/deleted).
 *              Karena chleo-backend tidak menyimpan creator info,
 *              chleo-admin-backend menyimpannya secara lokal.
 * @since       v1.0.0
 * ============================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');                          // UUID dari chleo-backend
            $table->string('subdomain')->nullable();
            $table->string('admin_name');
            $table->string('admin_email');
            $table->string('admin_password_masked')->default('••••••••'); // selalu masked
            $table->foreignId('created_by_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->enum('status', ['aktif', 'ditangguhkan', 'deleted'])->default('aktif');
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_logs');
    }
};
