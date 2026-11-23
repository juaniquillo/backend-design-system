<?php

declare(strict_types=1);

use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;
use Juaniquillo\BackendDesignSystem\Theme\Size;
use Juaniquillo\BackendDesignSystem\Theme\State;
use Juaniquillo\BackendDesignSystem\Theme\ThemeVariants;

it('builds look, size and state variants', function () {
    expect(ThemeVariants::make()
        ->look(ButtonVariant::Primary)
        ->size(Size::Sm)
        ->state(State::Disabled)
        ->all())->toBe(['primary', 'size.sm', 'state.disabled']);
});

it('prefixes plain size and state names', function () {
    expect(ThemeVariants::make()
        ->look('default')
        ->size('md')
        ->state('loading')
        ->all())->toBe(['default', 'size.md', 'state.loading']);
});

it('ignores null size and state values', function () {
    expect(ThemeVariants::make()
        ->look('default')
        ->size(null)
        ->state(null)
        ->all())->toBe(['default']);
});

it('rejects empty look names', function () {
    expect(fn () => ThemeVariants::make()->look(''))
        ->toThrow(InvalidArgumentException::class);
});

it('rejects dotted look names', function () {
    expect(fn () => ThemeVariants::make()->look('size.md'))
        ->toThrow(InvalidArgumentException::class);
});

it('rejects dotted size and state names', function () {
    expect(fn () => ThemeVariants::make()->size('size.md'))
        ->toThrow(InvalidArgumentException::class)
        ->and(fn () => ThemeVariants::make()->state('state.disabled'))
        ->toThrow(InvalidArgumentException::class);
});
