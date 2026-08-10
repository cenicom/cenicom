<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Discovery;

use App\Core\Module\Discovery\DiscoveryException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DiscoveryExceptionTest extends TestCase
{
    public function test_extends_runtime_exception(): void
    {
        $exception = new DiscoveryException(
            'Discovery error.'
        );

        $this->assertInstanceOf(
            RuntimeException::class,
            $exception
        );
    }

    public function test_directory_not_found_factory(): void
    {
        $exception = DiscoveryException::directoryNotFound(
            '/modules'
        );

        $this->assertSame(
            'Module directory [/modules] was not found.',
            $exception->getMessage()
        );
    }

    public function test_directory_not_readable_factory(): void
    {
        $exception = DiscoveryException::directoryNotReadable(
            '/modules'
        );

        $this->assertSame(
            'Module directory [/modules] is not readable.',
            $exception->getMessage()
        );
    }

    public function test_no_modules_found_factory(): void
    {
        $exception = DiscoveryException::noModulesFound(
            '/modules'
        );

        $this->assertSame(
            'No modules were found in [/modules].',
            $exception->getMessage()
        );
    }

    public function test_manifest_not_found_factory(): void
    {
        $exception = DiscoveryException::manifestNotFound(
            '/modules/Currency/module.json'
        );

        $this->assertSame(
            'Manifest [/modules/Currency/module.json] was not found.',
            $exception->getMessage()
        );
    }

    public function test_discovery_failed_factory(): void
    {
        $exception = DiscoveryException::discoveryFailed(
            'Unexpected filesystem error.'
        );

        $this->assertSame(
            'Module discovery failed: Unexpected filesystem error.',
            $exception->getMessage()
        );
    }
}
