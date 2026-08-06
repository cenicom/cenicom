app_path(--->
    PathResolver.php
    public function app(string $path = ''): string
    {
        $base = $this->appBase ?? app_path();

        return $base
            . ($path !== ''
                ? DIRECTORY_SEPARATOR . $path
                : '');
    }
    ModuleDiscovery.php
    public function discover(): array
    {
        $modulesPath = app_path('Modules');

        if (! File::exists($modulesPath)) {
            return [];
        }

        return File::directories($modulesPath);
    }

resource_path( -->
    PathResolver.php
    public function resource(string $path = ''): string
    {
        $base = $this->resourceBase ?? resource_path();

        return $base
            . ($path !== ''
                ? DIRECTORY_SEPARATOR . $path
                : '');
    }

database_path( -->
    PathResolver.php
    public function database(string $path = ''): string
    {
        $base = $this->databaseBase ?? database_path();

        return $base
            . ($path !== ''
                ? DIRECTORY_SEPARATOR . $path
                : '');
    }

base_path( --> 
    ManifestGenerator.php
    private function manifestFile(string $module): string
    {
        return base_path(
            "modules/{$module}.json"
        );
    }
    ManifestLoader.php
    public function manifestFile(string $module): string
    {
        return base_path(
            "{$this->manifestPath}/{$module}.json"
        );
    }
    PathResolver.php
    public function database(string $path = ''): string
    {
        $base = $this->databaseBase ?? database_path();

        return $base
            . ($path !== ''
                ? DIRECTORY_SEPARATOR . $path
                : '');
    }
    public function routes(string $path = ''): string
    {
        $base = $this->routesBase ?? base_path('routes');

        return $base
            . ($path !== ''
                ? DIRECTORY_SEPARATOR . $path
                : '');
    }
    public function base(string $path = ''): string
    {
        return base_path($path);
    }
    StubManager.php
    private function resolvePath(string $stub): string
    {
        $stub = ltrim($stub, '/\\');

        if (! str_ends_with($stub, '.stub')) {
            $stub .= '.stub';
        }

        return base_path(
            self::STUB_PATH . DIRECTORY_SEPARATOR . $stub
        );
    }

