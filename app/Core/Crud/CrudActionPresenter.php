<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudActionPresenterInterface;

final readonly class CrudActionPresenter implements
    CrudActionPresenterInterface
{
    /**
     * Presenta acciones CRUD previamente filtradas.
     *
     * La autorización pertenece a CrudActionFilter.
     *
     * El Presenter no genera URLs ni conoce componentes visuales.
     *
     * @param array<int, CrudAction> $actions
     *
     * @return array<int, CrudActionPresentation>
     */
    public function present(
        array $actions,
    ): array {
        return array_map(
            static fn (CrudAction $action): CrudActionPresentation =>
                new CrudActionPresentation(
                    action: $action,
                    label: self::label($action),
                    href: null,
                    variant: self::variant($action),
                    size: 'md',
                    icon: null,
                ),
            $actions,
        );
    }

    private static function label(CrudAction $action): string
    {
        return match ($action->operation()->name()) {
            CrudOperations::VIEW => 'Ver',
            CrudOperations::CREATE => 'Crear',
            CrudOperations::UPDATE => 'Editar',
            CrudOperations::DELETE => 'Eliminar',
            default => ucfirst($action->operation()->name()),
        };
    }

    private static function variant(CrudAction $action): string
    {
        return match ($action->operation()->name()) {
            CrudOperations::DELETE => 'danger',
            CrudOperations::CREATE => 'success',
            CrudOperations::UPDATE => 'primary',
            CrudOperations::VIEW => 'secondary',
            default => 'secondary',
        };
    }
}
