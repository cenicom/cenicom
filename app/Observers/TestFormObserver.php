<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\TestForm;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Observer del módulo TestForm.
 *
 * Gestiona los eventos del ciclo de vida del modelo.
 *
 * @package App\Observers
 */
final class TestFormObserver
{
    public function creating(TestForm $testForm): void
    {
        //
    }

    public function created(TestForm $testForm): void
    {
        //
    }

    public function updating(TestForm $testForm): void
    {
        //
    }

    public function updated(TestForm $testForm): void
    {
        //
    }

    public function deleting(TestForm $testForm): void
    {
        //
    }

    public function deleted(TestForm $testForm): void
    {
        //
    }

    public function restoring(TestForm $testForm): void
    {
        //
    }

    public function restored(TestForm $testForm): void
    {
        //
    }
}
