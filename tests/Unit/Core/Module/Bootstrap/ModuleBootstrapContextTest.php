<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\ModuleBootstrapContext;
use App\Core\Module\DTO\ModuleDefinition;
use Tests\TestCase;

final class ModuleBootstrapContextTest extends TestCase
{
    public function test_creates_context_with_manifest_path(): void
    {
        // Arrange MBC-001
        $manifestPath = '/modules/Blog/module.php';

        // Act
        $context = new ModuleBootstrapContext(
            $manifestPath
        );

        // Assert
        $this->assertSame(
            $manifestPath,
            $context->manifestPath()
        );
    }

    //MBC-002 — El contexto inicia sin ModuleDefinition
    public function test_starts_without_definition(): void
    {
        // Arrange MBC-002
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        // Assert
        $this->assertFalse(
            $context->hasDefinition()
        );

        $this->assertNull(
            $context->definition()
        );
    }

    //MBC-003 — El contexto inicia sin excepción
    public function test_starts_without_exception(): void
    {
        // Arrange MBC-003
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        // Assert
        $this->assertFalse(
            $context->hasException()
        );

        $this->assertNull(
            $context->exception()
        );
    }

    //MBC-004 — Almacena correctamente un ModuleDefinition
    //Como ModuleDefinition es un DTO readonly, podemos construir una instancia sencilla para la prueba.
    public function test_stores_definition(): void
    {
        // Arrange MBC004
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.php',
            providers: [],
            enabled: true,
        );

        // Act
        $context->setDefinition(
            $definition
        );

        // Assert
        $this->assertSame(
            $definition,
            $context->definition()
        );
    }

    //MBC-005 — hasDefinition() cambia a true
    public function test_reports_definition_exists(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.php',
            providers: [],
            enabled: true,
        );

        // Act
        $context->setDefinition(
            $definition
        );

        // Assert
        $this->assertTrue(
            $context->hasDefinition()
        );
    }

    //MBC-006 — Almacena correctamente una excepción
    public function test_stores_exception(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $exception = new \RuntimeException(
            'Pipeline failed.'
        );

        // Act
        $context->setException(
            $exception
        );

        // Assert
        $this->assertSame(
            $exception,
            $context->exception()
        );
    }

    //MBC-007 — hasException() cambia a true
    public function test_reports_exception_exists(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $exception = new \RuntimeException(
            'Pipeline failed.'
        );

        // Act
        $context->setException(
            $exception
        );

        // Assert
        $this->assertTrue(
            $context->hasException()
        );
    }

    //MBC-008 — clearException() elimina la excepción
    public function test_clears_exception(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $exception = new \RuntimeException(
            'Pipeline failed.'
        );

        $context->setException(
            $exception
        );

        // Act
        $context->clearException();

        // Assert
        $this->assertFalse(
            $context->hasException()
        );

        $this->assertNull(
            $context->exception()
        );
    }

    //MBC-009
    //El objetivo es demostrar que las operaciones sobre el contexto no
    //alteran el manifestPath.
    public function test_manifest_path_remains_immutable(): void
    {
        // Arrange
        $manifestPath = '/modules/Blog/module.php';

        $context = new ModuleBootstrapContext(
            $manifestPath
        );

        $definition = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: $manifestPath,
            providers: [],
            enabled: true,
        );

        // Act
        $context->setDefinition($definition);

        $context->setException(
            new \RuntimeException('Pipeline failed.')
        );

        $context->clearException();

        // Assert
        $this->assertSame(
            $manifestPath,
            $context->manifestPath()
        );
    }

    //🔴 MBC-010 — test_does_not_allow_overwriting_definition()
    public function test_does_not_allow_overwriting_definition(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $definitionOne = new ModuleDefinition(
            name: 'Blog',
            namespace: 'Modules\\Blog',
            basePath: '/modules/Blog',
            manifestPath: '/modules/Blog/module.php',
            providers: [],
            enabled: true,
        );

        $definitionTwo = new ModuleDefinition(
            name: 'Users',
            namespace: 'Modules\\Users',
            basePath: '/modules/Users',
            manifestPath: '/modules/Users/module.php',
            providers: [],
            enabled: true,
        );

        $context->setDefinition(
            $definitionOne
        );

        // Assert
        $this->expectException(
            \LogicException::class
        );

        $this->expectExceptionMessage(
            'Module definition has already been assigned.'
        );

        // Act
        $context->setDefinition(
            $definitionTwo
        );
    }

    /*🔴 MBC-011 — La primera excepción prevalece
        Objetivo
    **Una vez registrada una excepción, cualquier intento posterior de registrar otra no debe reemplazarla.
        Paso 1 (RED)
    */
    public function test_keeps_the_first_exception(): void
    {
        // Arrange
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.php'
        );

        $firstException = new \RuntimeException(
            'First exception.'
        );

        $secondException = new \RuntimeException(
            'Second exception.'
        );

        // Act
        $context->setException(
            $firstException
        );

        $context->setException(
            $secondException
        );

        // Assert
        $this->assertSame(
            $firstException,
            $context->exception()
        );
    }

    public function test_starts_without_skipped_state(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/Blog/module.json',
        );

        $this->assertFalse(
            $context->isSkipped()
        );
    }

    //🚢 MBC-012 — Certificar estado skipped
    public function test_marks_context_as_skipped(): void
    {
        $context = new ModuleBootstrapContext(
            '/modules/DisabledModule/module.json'
        );

        $context->markSkipped();

        $this->assertTrue(
            $context->isSkipped()
        );
    }
}
