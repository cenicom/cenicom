<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\ModuleProviderValidator;
use Tests\Fixtures\Providers\BlogServiceProvider;
use Tests\Fixtures\Providers\FakeClass;
use Tests\TestCase;

final class ModuleProviderValidatorTest extends TestCase
{
    private ModuleProviderValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new ModuleProviderValidator();
    }

    public function test_accepts_valid_service_provider(): void
    {
        $this->assertTrue(
            $this->validator->validate(
                BlogServiceProvider::class
            )
        );
    }

    //MPV-002
    //Objetivo
    //Certificar que el validador rechaza una clase inexistente.
    public function test_rejects_non_existing_provider(): void
    {
        $this->assertFalse(
            $this->validator->validate(
                'Fake\\Provider'
            )
        );
    }


    //MPV-003
    //Rechazar una clase que existe pero no hereda de ServiceProvider
    public function test_rejects_non_service_provider(): void
    {
        $this->assertFalse(
            $this->validator->validate(
                FakeClass::class
            )
        );
    }

    //MPV-004
    //Objetivo
    //Verificar que el validador es determinista: para la misma entrada siempre produce el mismo resultado y no mantiene estado interno.
    public function test_validate_is_stateless(): void
    {
        $provider = BlogServiceProvider::class;

        $this->assertTrue(
            $this->validator->validate($provider)
        );

        $this->assertTrue(
            $this->validator->validate($provider)
        );

        $this->assertTrue(
            $this->validator->validate($provider)
        );
    }
}
