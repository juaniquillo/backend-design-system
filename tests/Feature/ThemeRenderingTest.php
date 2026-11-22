<?php

declare(strict_types=1);

use Juaniquillo\BackendDesignSystem\Components\Accordion;
use Juaniquillo\BackendDesignSystem\Components\Alert;
use Juaniquillo\BackendDesignSystem\Components\Badge;
use Juaniquillo\BackendDesignSystem\Components\Breadcrumb;
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Components\Card;
use Juaniquillo\BackendDesignSystem\Components\Checkbox;
use Juaniquillo\BackendDesignSystem\Components\ChoiceGroup;
use Juaniquillo\BackendDesignSystem\Components\Divider;
use Juaniquillo\BackendDesignSystem\Components\Field;
use Juaniquillo\BackendDesignSystem\Components\Form;
use Juaniquillo\BackendDesignSystem\Components\Heading;
use Juaniquillo\BackendDesignSystem\Components\Inline;
use Juaniquillo\BackendDesignSystem\Components\Input;
use Juaniquillo\BackendDesignSystem\Components\Label;
use Juaniquillo\BackendDesignSystem\Components\Link;
use Juaniquillo\BackendDesignSystem\Components\Radio;
use Juaniquillo\BackendDesignSystem\Components\Select;
use Juaniquillo\BackendDesignSystem\Components\Stack;
use Juaniquillo\BackendDesignSystem\Components\Text;
use Juaniquillo\BackendDesignSystem\Components\Textarea;
use Juaniquillo\BackendDesignSystem\Designs\Design;

it('renders every component with a bundled design', function (string $key) {
    config(['backend-design-system.default' => $key]);

    $this->app->forgetInstance(Design::class);

    $design = $this->app->make(Design::class);

    $components = [
        Accordion::make($design)->summary('Details')->content('Hidden content'),
        Alert::make($design)->title('Notice')->body('Your trial ends soon.'),
        Badge::make($design)->label('New'),
        Breadcrumb::make($design)->items([
            ['label' => 'Home', 'href' => '/'],
            ['label' => 'Current'],
        ]),
        Button::make($design)->label('Save'),
        Button::make($design)->label('Saving')->loading(),
        Card::make($design)->title('Billing')->body('Your plan renews monthly.')->footer('Footer'),
        Checkbox::make($design)->name('terms')->label('I accept the terms'),
        ChoiceGroup::make($design)->legend('Plan')->choices([
            Radio::make($design)->name('plan')->value('free')->label('Free'),
        ]),
        Divider::make($design),
        Field::make($design)
            ->label('Email')
            ->input(Input::make($design)->name('email')->component()),
        Form::make($design)
            ->action('/subscribe')
            ->fields([Field::make($design)->label('Email')->input('form-email')])
            ->actions(Button::make($design)->label('Subscribe')),
        Heading::make($design)->text('Section')->level(2),
        Inline::make($design)->children([
            Button::make($design)->label('One'),
            Button::make($design)->label('Two'),
        ]),
        Input::make($design)->name('email'),
        Label::make($design)->text('Email'),
        Link::make($design)->label('Docs')->href('/docs')->current(),
        Radio::make($design)->name('plan')->value('free')->label('Free'),
        Select::make($design)->name('country')->options(['' => 'Choose', 'es' => 'Spain']),
        Stack::make($design)->children([
            Text::make($design)->text('One'),
            Text::make($design)->text('Two'),
        ]),
        Text::make($design)->text('Body copy'),
        Textarea::make($design)->name('notes'),
    ];

    foreach ($components as $component) {
        expect($component->toHtml())->not->toBeEmpty();
    }
})->with(['base', 'base-dark', 'base-dual']);
