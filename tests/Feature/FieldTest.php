<?php

declare(strict_types=1);

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Components\Field;
use Juaniquillo\BackendDesignSystem\Components\Input;
use Juaniquillo\BackendDesignSystem\Designs\Design;

it('wires a custom error component into the input describedby', function () {
    $design = $this->app->make(Design::class);

    $error = (new MainBackendComponent(ComponentEnum::DIV))
        ->setContent('Custom error');

    $view = $this->blade('{{ $component }}', [
        'component' => Field::make($design)
            ->label('Email')
            ->input(Input::make($design)->name('email')->component())
            ->error($error),
    ]);

    $view->assertSee('aria-describedby="email-error"', false)
        ->assertSee('id="email-error"', false)
        ->assertSee('Custom error');
});

it('keeps an existing aria-describedby value', function () {
    $design = $this->app->make(Design::class);

    $input = Input::make($design)
        ->name('email')
        ->attributes(['aria-describedby' => 'email-help'])
        ->component();

    $view = $this->blade('{{ $component }}', [
        'component' => Field::make($design)
            ->label('Email')
            ->input($input)
            ->error('That address is already taken.'),
    ]);

    $view->assertSee('aria-describedby="email-help email-error"', false)
        ->assertSee('id="email-error"', false)
        ->assertSee('That address is already taken.');
});

it('does not repeat the error id in aria-describedby', function () {
    $design = $this->app->make(Design::class);

    $input = Input::make($design)
        ->name('email')
        ->attributes(['aria-describedby' => 'email-error'])
        ->component();

    $view = $this->blade('{{ $component }}', [
        'component' => Field::make($design)
            ->label('Email')
            ->input($input)
            ->error('That address is already taken.'),
    ]);

    $view->assertSee('aria-describedby="email-error"', false)
        ->assertDontSee('email-error email-error', false);
});
