<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class AuditLog extends Model
{
    protected $table = 'audit_entries';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'actor_authenticated' => 'boolean',
            'metadata' => 'array',
            'occurred_at' => 'datetime',
        ];
    }
}
