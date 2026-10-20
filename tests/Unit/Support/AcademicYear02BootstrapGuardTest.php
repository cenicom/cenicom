<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Tests\Support\AcademicYear02BootstrapGuard;

final class AcademicYear02BootstrapGuardTest extends TestCase
{
    public function test_accepts_environment_without_cache_override(): void
    {
        $errors =
            AcademicYear02BootstrapGuard::findConfigurationCacheOverrides(
                [],
                [],
            );

        $this->assertSame([], $errors);
    }

    public function test_rejects_process_cache_override(): void
    {
        $errors =
            AcademicYear02BootstrapGuard::findConfigurationCacheOverrides(
                ['APP_CONFIG_CACHE' => 'C:/tmp/config.php'],
                [],
            );

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString(
            'APP_CONFIG_CACHE',
            implode(' ', $errors),
        );
    }

    public function test_rejects_cache_override_in_environment_file(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'aca-year02-');

        self::assertNotFalse($file);

        try {
            file_put_contents(
                $file,
                "APP_CONFIG_CACHE=/tmp/alternate-config.php\n",
            );

            $errors =
                AcademicYear02BootstrapGuard::findConfigurationCacheOverrides(
                    [],
                    [$file],
                );

            $this->assertNotEmpty($errors);
            $this->assertStringContainsString(
                'APP_CONFIG_CACHE definido',
                implode(' ', $errors),
            );
        } finally {
            @unlink($file);
        }
    }

    public function test_ignores_commented_cache_setting(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'aca-year02-');

        self::assertNotFalse($file);

        try {
            file_put_contents(
                $file,
                "# APP_CONFIG_CACHE=/tmp/alternate-config.php\n",
            );

            $errors =
                AcademicYear02BootstrapGuard::findConfigurationCacheOverrides(
                    [],
                    [$file],
                );

            $this->assertSame([], $errors);
        } finally {
            @unlink($file);
        }
    }


    public function test_rejects_empty_cache_override_in_environment_file(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'aca-year02-');

        self::assertNotFalse($file);

        try {
            file_put_contents($file, "APP_CONFIG_CACHE=\"\"\n");

            $errors =
                AcademicYear02BootstrapGuard::findConfigurationCacheOverrides(
                    [],
                    [$file],
                );

            $this->assertNotEmpty($errors);
            $this->assertStringContainsString(
                'APP_CONFIG_CACHE definido',
                implode(' ', $errors),
            );
        } finally {
            @unlink($file);
        }
    }
}
