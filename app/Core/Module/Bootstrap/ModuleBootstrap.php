<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleBootstrapReporterInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Module\Lifecycle\ModuleLifecycleManager;

final class ModuleBootstrap
{
    public function __construct(
        private readonly ModuleManifestFinderInterface $manifestFinder,
        private readonly ModuleBootstrapPipelineInterface $pipeline,
        private readonly ModuleLifecycleManager $lifecycle,
        private readonly ModuleBootstrapReporterInterface $reporter,
    ) {}

    public function bootstrap(): ModuleBootstrapReport
    {
        $report = new ModuleBootstrapReport();

        $report->metrics()->start();

        $manifests = $this->manifestFinder->find();

        foreach ($manifests as $manifestPath) {
            $this->reporter->moduleDiscovered(
                $manifestPath
            );

            $context = new ModuleBootstrapContext(
                $manifestPath
            );

            $this->pipeline->process($context);

            $this->recordResult(
                $context,
                $report
            );

            $this->finalizeLifecycle($context);
        }

        $report->metrics()->complete();

        return $report;
    }

    private function recordResult(
        ModuleBootstrapContext $context,
        ModuleBootstrapReport $report
    ): void {
        /*
     * SKIPPED
     *
     * Un módulo puede ser skipped sin haber generado
     * una definición. Por eso esta comprobación debe
     * realizarse antes de consultar la definición.
     */
        if ($context->isSkipped()) {
            $moduleName = $context->definition()?->name
                ?? $context->manifestPath();

            $report->addSkipped(
                $moduleName,
                'Module skipped during bootstrap.'
            );

            $report->metrics()->incrementSkipped();

            return;
        }

        /*
     * FAILURE
     */
        if ($context->hasException()) {
            $moduleName = $context->definition()?->name
                ?? $context->manifestPath();

            $exception = $context->exception();

            if ($exception === null) {
                return;
            }

            $report->addFailed(
                $moduleName,
                $exception
            );

            $report->metrics()->incrementFailed();

            return;
        }

        /*
     * REGISTERED
     */
        if ($context->wasModuleRegistered()) {
            $moduleName = $context->definition()?->name
                ?? $context->manifestPath();

            $report->addRegistered(
                $moduleName,
                []
            );

            $report->metrics()->incrementRegistered();
        }
    }

    private function finalizeLifecycle(
        ModuleBootstrapContext $context
    ): void {
        $definition = $context->definition();

        /*
         * Sin definición no podemos identificar el módulo.
         */
        if ($definition === null) {
            return;
        }

        $module = $definition->name;

        /*
         * El lifecycle de un módulo solamente puede iniciarse
         * una vez.
         */
        if ($this->lifecycle->has($module)) {
            return;
        }

        /*
         * DISCOVERED
         */
        $this->lifecycle->discovered($module);

        /*
         * SKIPPED
         *
         * SKIPPED no forma parte de ModuleState.
         */
        if ($context->isSkipped()) {
            return;
        }

        /*
         * El módulo debe haber sido registrado antes
         * de continuar hacia BOOTING.
         */
        if (! $context->wasModuleRegistered()) {
            return;
        }

        /*
         * REGISTERED
         */
        $this->lifecycle->registered($module);

        /*
         * BOOTING
         */
        $this->lifecycle->booting($module);

        /*
         * FAILURE
         */
        if ($context->hasException()) {
            $this->lifecycle->failed($module);

            return;
        }

        /*
         * BOOTED
         */
        $this->lifecycle->booted($module);

        /*
         * RUNNING
         */
        $this->lifecycle->running($module);
    }

    private function reportResult(
        ModuleBootstrapContext $context
    ): void {
        if ($context->isSkipped()) {
            $this->reporter->moduleSkipped(
                $context->definition(),
                'Module skipped during bootstrap.'
            );

            return;
        }

        if ($context->hasException()) {
            $exception = $context->exception();

            if ($exception !== null) {
                $this->reporter->moduleFailed(
                    $context->manifestPath(),
                    $exception
                );
            }

            return;
        }

        if ($context->definition() !== null) {
            $this->reporter->moduleLoaded(
                $context->definition()
            );
        }
    }
}
