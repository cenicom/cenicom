<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Permission;

final class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
    ];

     /**
     * Usuarios asociados al rol.
     *
     * @return BelongsToMany<User, Role>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'role_user'
        );
    }

    /**
     * Permisos asociados al rol.
     *
     * @return BelongsToMany<Permission, Role>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_role'
        );
    }
}
