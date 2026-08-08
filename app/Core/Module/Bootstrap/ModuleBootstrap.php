<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Bootstrap\Events\ModuleBootstrapCompleted;
use App\Core\Module\Bootstrap\Events\ModuleBootstrapping;
use App\Core\Module\Bootstrap\Events\ModuleFailed;
use App\Core\Module\Bootstrap\Events\ModuleRegistered;
use App\Core\Module\Bootstrap\Events\ModuleSkipped;

use App\Core\Module\Lifecycle\ModuleLifecycleManager;



final class ModuleBootstrap
{
    public function __construct(
        private readonly ModuleManifestFinderInterface $manifestFinder,
        private readonly ModuleBootstrapPipelineInterface $pipeline,
        private readonly ModuleRegistryInterface $registry,
        private readonly ModuleProviderRegistrarInterface $providerRegistrar,
        private readonly ModuleLifecycleManager $lifecycle,
    ) {}

    /**
     * Bootstraps all discovered modules
     * and returns an execution report.
     */
    public function bootstrap(): ModuleBootstrapReport
    {
        $report = new ModuleBootstrapReport();

        $report->metrics()->start();

        $manifests = $this->manifestFinder->find();

        event(
            new ModuleBootstrapping(
                count($manifests)
            )
        );

        $registered = 0;

        $failed = 0;

        foreach ($manifests as $manifestPath) {

            $context = $this->processManifest($manifestPath);

            if ($context->isSkipped()) {

                event(
                    new ModuleSkipped(
                        $manifestPath,
                        'disabled'
                    )
                );

                $report->addSkipped(
                    $manifestPath,
                    'disabled'
                );

                $report
                    ->metrics()
                    ->incrementSkipped();

                continue;
            }

            if ($context->hasException()) {

                if ($context->definition() !== null) {
                    $this->lifecycle->failed(
                        $context->definition()->name
                    );
                }

                $failed++;

                event(
                    new ModuleFailed(
                        $manifestPath,
                        $context->exception()
                    )
                );

                $report->addFailed(
                    $manifestPath,
                    $context->exception()
                );

                $report
                    ->metrics()
                    ->incrementFailed();

                continue;
            }


            $definition = $context->definition();

            if ($definition === null) {
                continue;
            }

            if (! $context->wasModuleRegistered()) {
                continue;
            }

            $this->lifecycle->discovered(
                $definition->name
            );

            $this->lifecycle->registered(
                $definition->name
            );

            $this->lifecycle->booting(
                $definition->name
            );

            $this->lifecycle->booted(
                $definition->name
            );

            $this->lifecycle->running(
                $definition->name
            );

            $registered++;

            event(
                new ModuleRegistered(
                    $definition->name,
                    $definition->providers
                )
            );

            $report->addRegistered(
                $definition->name,
                $definition->providers
            );

            $report
                ->metrics()
                ->incrementRegistered();
        }


        event(
            new ModuleBootstrapCompleted(
                $registered,
                $failed
            )
        );

        $report
            ->metrics()
            ->complete();

        return $report;
    }

    /**
     * Summary of processManifest
     * @param string $manifestPath
     * @return ModuleBootstrapContext
     */
    private function processManifest(string $manifestPath): ModuleBootstrapContext
    {

        $context = new ModuleBootstrapContext(
            $manifestPath
        );

        $this->pipeline->process($context);

        return $context;
    }

    /**
     *
     * @param ModuleBootstrapReport $report
     * @param int $registered
     * @param int $failed
     * @return void
     */
}
