<?php

declare(strict_types=1);

use Juaniquillo\BackendDesignSystem\Components\Alert;
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Components\Card;
use Juaniquillo\BackendDesignSystem\Components\Label;
use Juaniquillo\BackendDesignSystem\Components\Select;
use Juaniquillo\BackendDesignSystem\Designs\Design;

it('escapes text content', function () {
    $design = $this->app->make(Design::class);
    $payload = '<script>alert("x")</script>';

    $view = $this->blade('{{ $component }}', [
        'component' => Alert::make($design)
            ->title($payload)
            ->body($payload),
    ]);

    $view->assertDontSee('<script>', false)
        ->assertSee('&lt;script&gt;', false)
        ->assertSee('&quot;x&quot;', false);
});

it('escapes labels', function () {
    $design = $this->app->make(Design::class);
    $payload = '<script>alert("x")</script>';

    $view = $this->blade('{{ $component }}', [
        'component' => Label::make($design)->text($payload),
    ]);

    $view->assertDontSee('<script>', false)
        ->assertSee('&lt;script&gt;', false);
});

it('escapes option labels', function () {
    $design = $this->app->make(Design::class);
    $payload = '<script>alert("x")</script>';

    $view = $this->blade('{{ $component }}', [
        'component' => Select::make($design)
            ->name('choice')
            ->options(['x' => $payload]),
    ]);

    $view->assertDontSee('<script>', false)
        ->assertSee('&lt;script&gt;', false);
});

it('renders backend component slots as markup', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Card::make($design)
            ->title('Billing')
            ->body('Your plan renews monthly.')
            ->footer(Button::make($design)->label('Manage')->component()),
    ]);

    $view->assertSee('<button', false)
        ->assertSee('Manage')
        ->assertDontSee('&lt;button', false);
});
