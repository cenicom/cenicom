<?php

declare(strict_types=1);

namespace App\Core\Generator;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;

/**
 * Clase base para los generadores del CN Generator.
 *
 * Proporciona la infraestructura común para todos los generadores
 * especializados, manteniendo una arquitectura desacoplada y
 * preparada para futuras extensiones.
 *
 * Esta clase no contiene lógica específica de generación; dicha
 * responsabilidad corresponde a las implementaciones concretas.
 */
abstract class BaseGenerator implements GeneratorInterface
{
    /**
     * Crea una nueva instancia del generador base.
     */
    public function __construct(
        protected StubManager $stubManager,
        protected FileWriter $fileWriter,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    abstract public function supports(ModuleData $module): bool;

    /**
     * {@inheritDoc}
     */
    abstract public function generate(ModuleData $module): GeneratorResult;

    protected function render(
        string $stub,
        array $variables
    ): string {
        $content = $this->stubManager->render(
            $stub,
            $variables
        );

        return $content;
    }

    /**
     * Genera un archivo a partir de un stub.
     *
     * Centraliza el flujo de renderizado y escritura para
     * todos los generadores del CN Generator.
     *
     * @param string $stub Nombre del stub.
     * @param string $path Ruta destino.
     * @param array<string,mixed> $variables Variables del stub.
     */
    protected function generateFile(
        string $stub,
        string $path,
        array $variables
    ): bool {

        if ($this->fileWriter->exists($path)) {
            return false;
        }

        $content = $this->render(
            $stub,
            $variables
        );

        $this->write(
            $path,
            $content
        );

        return true;
    }

    protected function write(
        string $path,
        string $content
    ): void {
        $this->fileWriter->write(
            $path,
            $content
        );
    }
}
