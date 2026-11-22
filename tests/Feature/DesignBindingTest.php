<?php

declare(strict_types=1);

use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Designs\DarkMode;
use Juaniquillo\BackendDesignSystem\Designs\Design;

it('merges the package configuration', function () {
    expect(config('backend-design-system.default'))->toBe('base')
        ->and(config('backend-design-system.designs'))->toHaveKeys(['base', 'base-dark', 'base-dual']);
});

it('binds the configured design', function () {
    $design = $this->app->make(Design::class);

    expect($design)->toBeInstanceOf(Design::class)
        ->and($design->key)->toBe('base')
        ->and($design->label)->toBe('Base')
        ->and($design->darkMode)->toBe(DarkMode::None);
});

it('resolves a bundled design', function (string $key, DarkMode $darkMode) {
    config(['backend-design-system.default' => $key]);

    $this->app->forgetInstance(Design::class);

    $design = $this->app->make(Design::class);

    expect($design->key)->toBe($key)
        ->and($design->darkMode)->toBe($darkMode);
})->with([
    ['base', DarkMode::None],
    ['base-dark', DarkMode::Always],
    ['base-dual', DarkMode::Adaptive],
]);

it('resolves the design selected in configuration', function () {
    config([
        'backend-design-system.default' => 'midnight',
        'backend-design-system.designs.midnight' => [
            'label' => 'Midnight',
            'path' => 'views/backend-design-system/midnight',
            'dark' => 'always',
        ],
    ]);

    $this->app->forgetInstance(Design::class);

    $design = $this->app->make(Design::class);

    expect($design->key)->toBe('midnight')
        ->and($design->label)->toBe('Midnight')
        ->and($design->darkMode)->toBe(DarkMode::Always);
});

it('fails for an unknown default design', function () {
    config(['backend-design-system.default' => 'missing']);

    $this->app->forgetInstance(Design::class);

    expect(fn () => $this->app->make(Design::class))
        ->toThrow(InvalidArgumentException::class);
});

it('fails when a design has no manager or path', function () {
    config([
        'backend-design-system.default' => 'broken',
        'backend-design-system.designs.broken' => ['label' => 'Broken'],
    ]);

    $this->app->forgetInstance(Design::class);

    expect(fn () => $this->app->make(Design::class))
        ->toThrow(InvalidArgumentException::class);
});

it('uses the bound design when a component is made without one', function () {
    $component = Button::make()->label('Save')->component();

    expect($component)->toBeInstanceOf(MainBackendComponent::class);
});

it('renders a button with a bundled design', function (string $key, string $class) {
    config(['backend-design-system.default' => $key]);

    $this->app->forgetInstance(Design::class);

    $html = Button::make()->label('Save')->toHtml();

    expect($html)->toContain('<button')
        ->toContain($class);
})->with([
    ['base', 'bg-white'],
    ['base-dark', 'bg-zinc-900'],
    ['base-dual', 'dark:bg-zinc-900'],
]);
