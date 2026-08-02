<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery;

use App\Core\Navigation\Discovery\Contracts\NavigationDiscoveryInterface;

final readonly class NavigationAutoDiscovery implements NavigationDiscoveryInterface
{
    public function __construct(
        private string $modulesPath,
    ) {
    }

    /**
     * @return list<string>
     */
    public function discover(): array
    {
        $files = glob(
            $this->modulesPath . '/*/navigation.php'
        );

        if ($files === false) {
            return [];
        }

        sort($files);

        return array_values($files);
    }
}
