<?php

declare(strict_types=1);

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Components\Field;
use Juaniquillo\BackendDesignSystem\Components\Input;
use Juaniquillo\BackendDesignSystem\Components\Label;
use Juaniquillo\BackendDesignSystem\Components\Select;
use Juaniquillo\BackendDesignSystem\Components\Text;
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

it('wires a design component input without component()', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Field::make($design)
            ->label('Email')
            ->input(Input::make($design)->name('email')->type('email'))
            ->error('That address is already taken.')
            ->required(),
    ]);

    $view->assertSee('for="email"', false)
        ->assertSee('id="email"', false)
        ->assertSee('type="email"', false)
        ->assertSee('required="required"', false)
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('aria-describedby="email-error"', false)
        ->assertSee('id="email-error"', false);
});

it('wires design component labels, selects and errors without component()', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Field::make($design)
            ->label(Label::make($design)->text('Country'))
            ->input(Select::make($design)->name('country')->options(['es' => 'Spain']))
            ->error(Text::make($design)->text('Pick a country.')),
    ]);

    $view->assertSee('for="country"', false)
        ->assertSee('id="country"', false)
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('aria-describedby="country-error"', false)
        ->assertSee('id="country-error"', false)
        ->assertSee('Pick a country.', false);
});
