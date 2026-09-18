<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;

final class FeatureTestBuilder
{
    /**
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return [
            'namespace' => $module->featureTestNamespace(),
            'featureTest' => $module->featureTestClass(),
            'model' => $module->modelClass(),
            'qualifiedModel' => $module->qualifiedModel(),
            'route' => $module->routeName(),
            'viewPrefix' => $module->viewPrefix(),
            'authentication' => $this->buildAuthentication($module),
        ];
    }

    private function buildAuthentication(ModuleData $module): string
    {
        $security = $module->security();

        if ($security === null || ! $security->requiresAuth()) {
            return '';
        }

        return <<<'PHP'
        $user = User::factory()->create();

        $this->actingAs($user);
    PHP;
    }
}
