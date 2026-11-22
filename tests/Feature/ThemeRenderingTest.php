<?php

declare(strict_types=1);

use Juaniquillo\BackendDesignSystem\Components\Alert;
use Juaniquillo\BackendDesignSystem\Components\Badge;
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Components\Card;
use Juaniquillo\BackendDesignSystem\Components\Checkbox;
use Juaniquillo\BackendDesignSystem\Components\Field;
use Juaniquillo\BackendDesignSystem\Components\Form;
use Juaniquillo\BackendDesignSystem\Components\Input;
use Juaniquillo\BackendDesignSystem\Components\Label;
use Juaniquillo\BackendDesignSystem\Components\Radio;
use Juaniquillo\BackendDesignSystem\Components\Select;
use Juaniquillo\BackendDesignSystem\Components\Textarea;
use Juaniquillo\BackendDesignSystem\Designs\Design;

it('renders every component with a bundled design', function (string $key) {
    config(['backend-design-system.default' => $key]);

    $this->app->forgetInstance(Design::class);

    $design = $this->app->make(Design::class);

    $components = [
        Alert::make($design)->title('Notice')->body('Your trial ends soon.'),
        Badge::make($design)->label('New'),
        Button::make($design)->label('Save'),
        Card::make($design)->title('Billing')->body('Your plan renews monthly.')->footer('Footer'),
        Checkbox::make($design)->name('terms')->label('I accept the terms'),
        Field::make($design)
            ->label('Email')
            ->input(Input::make($design)->name('email')->component()),
        Form::make($design)
            ->action('/subscribe')
            ->fields([Field::make($design)->label('Email')->input('form-email')])
            ->actions(Button::make($design)->label('Subscribe')),
        Input::make($design)->name('email'),
        Label::make($design)->text('Email'),
        Radio::make($design)->name('plan')->value('free')->label('Free'),
        Select::make($design)->name('country')->options(['' => 'Choose', 'es' => 'Spain']),
        Textarea::make($design)->name('notes'),
    ];

    foreach ($components as $component) {
        expect($component->toHtml())->not->toBeEmpty();
    }
})->with(['base', 'base-dark', 'base-dual']);
