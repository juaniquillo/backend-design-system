<?php

declare(strict_types=1);

use Juaniquillo\BackendDesignSystem\Designs\DarkMode;
use Juaniquillo\BackendDesignSystem\Designs\Design;
use Juaniquillo\BackendDesignSystem\Theme\AlertVariant;
use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;
use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;
use Juaniquillo\BackendDesignSystem\Theme\Look;
use Juaniquillo\BackendDesignSystem\Theme\Size;
use Juaniquillo\BackendDesignSystem\Theme\State;

it('provides every required theme key', function (string $designKey, string $file, array $keys) {
    config(['backend-design-system.default' => $designKey]);

    $this->app->forgetInstance(Design::class);

    $design = $this->app->make(Design::class);

    $path = $design->themeManager->getThemePath();

    expect(file_exists($path.'/'.$file.'.blade.php'))->toBeTrue();

    foreach ($keys as $key) {
        expect($design->themeManager->processTheme($file, $key))->not->toBeNull();
    }
})->with('bundled designs')->with('theme keys');

it('follows the dark mode rule', function (string $designKey) {
    config(['backend-design-system.default' => $designKey]);

    $this->app->forgetInstance(Design::class);

    $design = $this->app->make(Design::class);

    $path = $design->themeManager->getThemePath();

    if ($design->darkMode === DarkMode::Adaptive) {
        foreach ([
            'alert',
            'badge',
            'button',
            'card',
            'checkbox',
            'field',
            'input',
            'radio',
            'select',
            'textarea',
        ] as $file) {
            $themes = require $path.'/'.$file.'.blade.php';

            $hasDarkClasses = collect($themes)
                ->contains(fn (mixed $classes): bool => is_string($classes) && str_contains($classes, 'dark:'));

            expect($hasDarkClasses)->toBeTrue("{$file} has no dark: classes in {$designKey}");
        }

        return;
    }

    foreach (glob($path.'/*.blade.php') ?: [] as $file) {
        $themes = require $file;

        $hasDarkClasses = collect($themes)
            ->contains(fn (mixed $classes): bool => is_string($classes) && str_contains($classes, 'dark:'));

        expect($hasDarkClasses)->toBeFalse(basename($file)." has dark: classes in {$designKey}");
    }
})->with('bundled designs');

dataset('bundled designs', [
    'base' => ['base'],
    'base-dark' => ['base-dark'],
    'base-dual' => ['base-dual'],
]);

dataset('theme keys', [
    'alert' => ['alert', array_map(fn (AlertVariant $case): string => $case->value, AlertVariant::cases())],
    'badge' => ['badge', array_map(fn (BadgeVariant $case): string => $case->value, BadgeVariant::cases())],
    'button' => ['button', [
        ...array_map(fn (ButtonVariant $case): string => $case->value, ButtonVariant::cases()),
        ...array_map(fn (Size $case): string => $case->value, Size::cases()),
        State::Disabled->value,
    ]],
    'card' => ['card', ['container', 'title', 'body', 'foot']],
    'checkbox' => ['checkbox', ['container', 'box', 'label']],
    'field' => ['field', ['container', 'error']],
    'form' => ['form', ['container', 'actions']],
    'input' => ['input', [Look::Default->value, State::Disabled->value]],
    'label' => ['label', ['default']],
    'radio' => ['radio', ['container', 'box', 'label']],
    'select' => ['select', [Look::Default->value, State::Disabled->value]],
    'textarea' => ['textarea', [Look::Default->value, State::Disabled->value]],
    'radius' => ['radius', ['none', 'sm', 'md', 'full']],
    'space' => ['space', ['xs', 'sm', 'md', 'lg']],
    'text' => ['text', ['xs', 'sm', 'md', 'lg']],
]);
