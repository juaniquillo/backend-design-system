<?php

declare(strict_types=1);

use Juaniquillo\BackendDesignSystem\Designs\DarkMode;
use Juaniquillo\BackendDesignSystem\Designs\Design;
use Juaniquillo\BackendDesignSystem\Theme\AlertVariant;
use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;
use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;
use Juaniquillo\BackendDesignSystem\Theme\HeadingLevel;
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
            'accordion',
            'alert',
            'badge',
            'breadcrumb',
            'button',
            'card',
            'checkbox',
            'choice-group',
            'divider',
            'field',
            'heading',
            'input',
            'link',
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

it('shapes every badge as a chip', function (string $designKey) {
    // A badge is a chip, not coloured text: the padding and small type are
    // part of the design, so a new bundled design has to carry them too.
    config(['backend-design-system.default' => $designKey]);

    $this->app->forgetInstance(Design::class);

    $badges = require $this->app->make(Design::class)->themeManager->getThemePath().'/badge.blade.php';

    foreach ($badges as $variant => $classes) {
        expect($classes)->toContain('px-', 'py-', 'text-xs');
    }
})->with('bundled designs');

dataset('theme keys', [
    'accordion' => ['accordion', ['container', 'summary', 'content']],
    'alert' => ['alert', array_map(fn (AlertVariant $case): string => $case->value, AlertVariant::cases())],
    'badge' => ['badge', array_map(fn (BadgeVariant $case): string => $case->value, BadgeVariant::cases())],
    'breadcrumb' => ['breadcrumb', ['container', 'list', 'item', 'link', State::Current->value, 'separator']],
    'button' => ['button', [
        ...array_map(fn (ButtonVariant $case): string => $case->value, ButtonVariant::cases()),
        ...array_map(fn (Size $case): string => $case->value, Size::cases()),
        State::Disabled->value,
        State::Loading->value,
    ]],
    'card' => ['card', ['container', 'title', 'body', 'foot']],
    'checkbox' => ['checkbox', ['container', 'box', 'label']],
    'choice-group' => ['choice-group', ['container', 'legend', 'choices', 'error']],
    'divider' => ['divider', [Look::Default->value]],
    'field' => ['field', ['container', 'error']],
    'form' => ['form', ['container', 'actions']],
    'heading' => ['heading', array_map(fn (HeadingLevel $case): string => $case->value, HeadingLevel::cases())],
    'inline' => ['inline', ['container']],
    'input' => ['input', [Look::Default->value, State::Disabled->value]],
    'label' => ['label', [Look::Default->value]],
    'link' => ['link', [Look::Default->value, State::Current->value]],
    'radio' => ['radio', ['container', 'box', 'label']],
    'select' => ['select', [Look::Default->value, State::Disabled->value]],
    'stack' => ['stack', ['container']],
    'textarea' => ['textarea', [Look::Default->value, State::Disabled->value]],
    'radius' => ['radius', ['none', 'sm', 'md', 'full']],
    'space' => ['space', ['xs', 'sm', 'md', 'lg']],
    'text' => ['text', ['xs', 'sm', 'md', 'lg']],
]);
