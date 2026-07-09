<?php
/**
 * ============================================================
 * @module      TenantManagement
 * @layer       Model
 * @file        TenantLog.php
 * @path        app/Modules/TenantManagement/Models/TenantLog.php
 * @description Eloquent model untuk tabel tenant_logs — menyimpan
 *              log aktivitas pembuatan & perubahan status tenant secara lokal.
 * @since       v1.0.0
 * ============================================================
 */

namespace App\Modules\TenantManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TenantLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'subdomain',
        'admin_name',
        'admin_email',
        'admin_password_masked',
        'created_by_user_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Admin portal staff yang membuat tenant ini. */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
