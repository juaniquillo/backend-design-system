<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Juaniquillo\BackendDesignSystem\Designs\DarkMode;
use Juaniquillo\BackendDesignSystem\Designs\Design;
use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;
use Juaniquillo\BaseBackendComponent\Themes\GenericThemeManager;

class BackendDesignSystemServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/backend-design-system.php',
            'backend-design-system',
        );

        $this->app->singleton(Design::class, fn (Application $app): Design => $this->resolveDesign($app->make(Repository::class)));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/backend-design-system.php' => $this->app->configPath('backend-design-system.php'),
        ], 'backend-design-system-config');

        $this->publishes([
            __DIR__.'/../resources/views/backend-design-system' => $this->app->resourcePath('views/backend-design-system'),
        ], 'backend-design-system-themes');

        foreach (['base', 'base-dark', 'base-dual'] as $design) {
            $this->publishes([
                __DIR__.'/../resources/views/backend-design-system/'.$design => $this->app->resourcePath('views/backend-design-system/'.$design),
            ], 'backend-design-system-theme-'.$design);
        }
    }

    private function resolveDesign(Repository $config): Design
    {
        $key = $config->get('backend-design-system.default', 'base');

        if (! is_string($key) || $key === '') {
            throw new InvalidArgumentException('The default design must be a non-empty string.');
        }

        $definition = $config->get("backend-design-system.designs.{$key}");

        if (! is_array($definition)) {
            throw new InvalidArgumentException("Unknown design '{$key}'.");
        }

        $label = $definition['label'] ?? $key;

        return new Design(
            key: $key,
            label: is_string($label) ? $label : $key,
            themeManager: $this->themeManager($key, $definition),
            darkMode: $this->darkMode($key, $definition['dark'] ?? 'none'),
        );
    }

    /**
     * @param  array<mixed>  $definition
     */
    private function themeManager(string $key, array $definition): ThemeManager
    {
        $manager = $definition['manager'] ?? null;

        if (is_string($manager) && $manager !== '') {
            if (! is_a($manager, ThemeManager::class, true)) {
                throw new InvalidArgumentException("Design '{$key}' names '{$manager}', which is not a ThemeManager.");
            }

            /** @var class-string<ThemeManager> $manager */
            return new $manager;
        }

        $path = $definition['path'] ?? null;

        if (! is_string($path) || trim($path) === '') {
            throw new InvalidArgumentException("Design '{$key}' needs a manager class or a theme path.");
        }

        return (new GenericThemeManager($this->app->resourcePath(trim($path))))
            ->setThemeExtension('.blade.php');
    }

    private function darkMode(string $key, mixed $mode): DarkMode
    {
        $darkMode = is_string($mode) ? DarkMode::tryFrom($mode) : null;

        if ($darkMode === null) {
            throw new InvalidArgumentException("Design '{$key}' has an unknown dark mode.");
        }

        return $darkMode;
    }
}
