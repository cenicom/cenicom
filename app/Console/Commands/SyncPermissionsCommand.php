<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Security\Permissions\Contracts\PermissionRegistryInterface;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

final class SyncPermissionsCommand extends Command
{
    protected $signature = 'cenicom:permissions-sync';

    protected $description = 'Synchronize declared permission definitions with the database.';

    public function __construct(
        private readonly PermissionRegistryInterface $permissions,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $created = 0;
        $updated = 0;
        $unchanged = 0;

        try {
            DB::transaction(function () use (
                &$created,
                &$updated,
                &$unchanged,
            ): void {
                foreach ($this->permissions->all() as $definition) {
                    $permission = Permission::query()->firstOrNew([
                        'name' => $definition->name,
                    ]);

                    $isNew = ! $permission->exists;

                    $permission->description = $definition->description;
                    $permission->module = $definition->module;

                    if (
                        ! $isNew
                        && ! $permission->isDirty([
                            'description',
                            'module',
                        ])
                    ) {
                        $unchanged++;

                        continue;
                    }

                    $permission->save();

                    if ($isNew) {
                        $created++;
                    } else {
                        $updated++;
                    }
                }
            });
        } catch (Throwable $exception) {
            $this->components->error(
                'Permission synchronization failed: '
                . $exception->getMessage()
            );

            return self::FAILURE;
        }

        $this->newLine();

        $this->table(
            ['Operation', 'Count'],
            [
                ['Created', $created],
                ['Updated', $updated],
                ['Unchanged', $unchanged],
            ]
        );

        $this->components->info(
            'Permission catalog synchronized successfully.'
        );

        return self::SUCCESS;
    }
}
