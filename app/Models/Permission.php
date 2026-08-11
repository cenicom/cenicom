<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'module',
    ];

     /**
     * Roles que poseen este permiso.
     *
     * @return BelongsToMany<Role, Permission>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'permission_role'
        );
    }

    /**
     * Usuarios que poseen directamente este permiso.
     *
     * @return BelongsToMany<User, Permission>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'permission_user'
        );
    }
}
