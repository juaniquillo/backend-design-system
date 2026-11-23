<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;
use Juaniquillo\BackendDesignSystem\BackendDesignSystemServiceProvider;

it('publishes the package config', function () {
    $paths = ServiceProvider::pathsToPublish(
        BackendDesignSystemServiceProvider::class,
        'backend-design-system-config',
    );

    $normalize = static fn (string $path): string => str_replace('\\', '/', $path);

    expect($paths)->toHaveCount(1);

    $source = array_key_first($paths);
    $destination = $paths[$source];

    expect($normalize($source))->toEndWith('config/backend-design-system.php')
        ->and($normalize($destination))->toBe($normalize(config_path('backend-design-system.php')));
});

it('publishes the bundled themes', function () {
    $paths = ServiceProvider::pathsToPublish(
        BackendDesignSystemServiceProvider::class,
        'backend-design-system-themes',
    );

    $normalize = static fn (string $path): string => str_replace('\\', '/', $path);

    expect($paths)->toHaveCount(1);

    $source = array_key_first($paths);
    $destination = $paths[$source];

    expect($normalize($source))->toEndWith('resources/views/backend-design-system')
        ->and($normalize($destination))->toBe($normalize(resource_path('views/backend-design-system')));
});

it('publishes a single bundled design', function (string $design) {
    $paths = ServiceProvider::pathsToPublish(
        BackendDesignSystemServiceProvider::class,
        'backend-design-system-theme-'.$design,
    );

    $normalize = static fn (string $path): string => str_replace('\\', '/', $path);

    expect($paths)->toHaveCount(1);

    $source = array_key_first($paths);
    $destination = $paths[$source];

    expect($normalize($source))->toEndWith('resources/views/backend-design-system/'.$design)
        ->and($normalize($destination))->toBe($normalize(resource_path('views/backend-design-system/'.$design)));
})->with(['base', 'base-dark', 'base-dual']);
