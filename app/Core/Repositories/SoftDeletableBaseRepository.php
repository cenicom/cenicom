<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Contracts\SoftDeletableRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class SoftDeletableBaseRepository extends BaseRepository implements SoftDeletableRepositoryInterface
{
    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function restore(int|string $id): bool
    {
        return (bool) $this->query()
            ->onlyTrashed()
            ->findOrFail($id)
            ->restore();
    }

    public function forceDelete(int|string $id): bool
    {
        return (bool) $this->query()
            ->onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();
    }
}
