<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Resource
 * @file        UserResource.php
 * @path        Modules/Auth/Resources/UserResource.php
 * @description Transformasi Model User (staff) ke format JSON API response.
 * @author      [Nama Developer]
 * @since       v1.0.0
 * @ref         https://laravel.com/docs/13.x/eloquent-resources
 * ============================================================
 */

namespace Modules\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
