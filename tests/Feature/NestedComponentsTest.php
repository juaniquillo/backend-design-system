<?php

declare(strict_types=1);

use Juaniquillo\BackendDesignSystem\Components\Accordion;
use Juaniquillo\BackendDesignSystem\Components\Alert;
use Juaniquillo\BackendDesignSystem\Components\Badge;
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Components\Card;
use Juaniquillo\BackendDesignSystem\Components\ChoiceGroup;
use Juaniquillo\BackendDesignSystem\Components\Heading;
use Juaniquillo\BackendDesignSystem\Components\Inline;
use Juaniquillo\BackendDesignSystem\Components\Radio;
use Juaniquillo\BackendDesignSystem\Components\Stack;
use Juaniquillo\BackendDesignSystem\Components\Text;
use Juaniquillo\BackendDesignSystem\Designs\Design;
use Juaniquillo\BackendDesignSystem\Theme\AlertVariant;
use Juaniquillo\BackendDesignSystem\Theme\HeadingLevel;

it('nests design components in a card without component()', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Card::make($design)
            ->title(Heading::make($design)->text('Billing')->level(HeadingLevel::H2))
            ->body(Stack::make($design)->children([
                Text::make($design)->text('Your plan renews monthly.'),
            ]))
            ->footer(Button::make($design)->label('Manage')),
    ]);

    $view->assertSee('<h2', false)
        ->assertSee('Billing', false)
        ->assertSee('border-b', false)
        ->assertSee('Your plan renews monthly.', false)
        ->assertSee('Manage', false)
        ->assertSee('border-t', false);
});

it('nests design components in an alert without component()', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Alert::make($design)
            ->variant(AlertVariant::Danger)
            ->title(Text::make($design)->text('Export failed')->inline())
            ->body(Text::make($design)->text('The file could not be generated.'))
            ->live(),
    ]);

    $view->assertSee('role="alert"', false)
        ->assertSee('Export failed', false)
        ->assertSee('The file could not be generated.', false);
});

it('nests a design component in an accordion without component()', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Accordion::make($design)
            ->summary(Inline::make($design)->children([
                Text::make($design)->text('Shipping details')->inline(),
                Badge::make($design)->label('New'),
            ]))
            ->content(Stack::make($design)->children([
                Text::make($design)->text('Ships in two business days.'),
            ])),
    ]);

    $view->assertSee('<details', false)
        ->assertSee('<summary', false)
        ->assertSee('Shipping details', false)
        ->assertSee('New', false)
        ->assertSee('Ships in two business days.', false);
});

it('nests a design component error in a choice group without component()', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => ChoiceGroup::make($design)
            ->legend('Plan')
            ->choices([
                Radio::make($design)->name('plan')->value('free')->label('Free'),
            ])
            ->id('plan')
            ->error(Text::make($design)->text('Choose a plan.')),
    ]);

    $view->assertSee('aria-invalid="true"', false)
        ->assertSee('aria-describedby="plan-error"', false)
        ->assertSee('id="plan-error"', false)
        ->assertSee('Choose a plan.', false);
});
