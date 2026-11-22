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
use Juaniquillo\BackendDesignSystem\Theme\AlertVariant;

it('renders the expected blade output', function (callable $factory, array $assertions) {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => $factory($design),
    ]);

    foreach ($assertions as $assertion) {
        $view->assertSee($assertion, false);
    }
})->with('component markup');

it('renders state and accessibility attributes', function (callable $factory, array $assertions) {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => $factory($design),
    ]);

    foreach ($assertions as $assertion) {
        $view->assertSee($assertion, false);
    }
})->with('component states');

dataset('component markup', [
    'alert' => [
        fn (Design $design) => Alert::make($design)->title('Notice')->body('Your trial ends soon.'),
        ['<div', '<b', 'Notice', '</b>', '<p', 'Your trial ends soon.', '</p>', '</div>'],
    ],
    'badge' => [
        fn (Design $design) => Badge::make($design)->label('New'),
        ['<span', 'New', '</span>'],
    ],
    'button' => [
        fn (Design $design) => Button::make($design)->label('Save')->type('submit'),
        ['<button', 'type="submit"', 'Save', '</button>'],
    ],
    'card' => [
        fn (Design $design) => Card::make($design)
            ->title('Billing')
            ->body('Your plan renews monthly.')
            ->footer('Footer'),
        ['<div', '<h3', 'Billing', '</h3>', 'Your plan renews monthly.', 'Footer', '</div>'],
    ],
    'checkbox' => [
        fn (Design $design) => Checkbox::make($design)
            ->name('terms')
            ->value('accepted')
            ->label('I accept the terms')
            ->checked()
            ->required(),
        [
            '<label',
            'type="checkbox"',
            'name="terms"',
            'value="accepted"',
            'checked="checked"',
            'required="required"',
            'I accept the terms',
            '</label>',
        ],
    ],
    'field' => [
        fn (Design $design) => Field::make($design)
            ->label('Email')
            ->input(Input::make($design)->name('email')->component())
            ->error('That address is already taken.')
            ->required(),
        [
            '<div',
            '<label',
            'for="email"',
            'Email',
            '<input',
            'id="email"',
            'name="email"',
            'required="required"',
            'aria-invalid="true"',
            'aria-describedby="email-error"',
            'That address is already taken.',
            'id="email-error"',
            '</div>',
        ],
    ],
    'form' => [
        fn (Design $design) => Form::make($design)
            ->action('/subscribe')
            ->method('post')
            ->fields([Field::make($design)->label('Email')->input('form-email')])
            ->actions(Button::make($design)->label('Subscribe')->type('submit')),
        [
            '<form',
            'method="POST"',
            'action="/subscribe"',
            'Email',
            'name="form-email"',
            '<button',
            'type="submit"',
            'Subscribe',
            '</form>',
        ],
    ],
    'input' => [
        fn (Design $design) => Input::make($design)
            ->name('email')
            ->type('email')
            ->value('you@example.com')
            ->placeholder('you@example.com')
            ->required(),
        [
            '<input',
            'type="email"',
            'name="email"',
            'value="you@example.com"',
            'placeholder="you@example.com"',
            'required="required"',
        ],
    ],
    'label' => [
        fn (Design $design) => Label::make($design)->text('Email')->for('email'),
        ['<label', 'for="email"', 'Email', '</label>'],
    ],
    'radio' => [
        fn (Design $design) => Radio::make($design)
            ->name('plan')
            ->value('pro')
            ->label('Pro')
            ->checked(),
        ['<label', 'type="radio"', 'name="plan"', 'value="pro"', 'checked="checked"', 'Pro', '</label>'],
    ],
    'select' => [
        fn (Design $design) => Select::make($design)
            ->name('country')
            ->options(['' => 'Choose', 'es' => 'Spain'])
            ->value('es')
            ->required(),
        [
            '<select',
            'name="country"',
            '<option',
            'value=""',
            'Choose',
            'value="es"',
            'selected="selected"',
            'Spain',
            'required="required"',
            '</select>',
        ],
    ],
    'textarea' => [
        fn (Design $design) => Textarea::make($design)
            ->name('notes')
            ->rows(3)
            ->placeholder('Anything we should know?')
            ->value('Hello')
            ->required(),
        [
            '<textarea',
            'name="notes"',
            'rows="3"',
            'placeholder="Anything we should know?"',
            'required="required"',
            'Hello',
            '</textarea>',
        ],
    ],
]);

dataset('component states', [
    'disabled button' => [
        fn (Design $design) => Button::make($design)->label('Save')->disabled(),
        ['disabled="disabled"'],
    ],
    'disabled input' => [
        fn (Design $design) => Input::make($design)->name('email')->disabled(),
        ['disabled="disabled"'],
    ],
    'disabled select' => [
        fn (Design $design) => Select::make($design)->name('country')->options(['' => 'Choose'])->disabled(),
        ['disabled="disabled"'],
    ],
    'disabled textarea' => [
        fn (Design $design) => Textarea::make($design)->name('notes')->disabled(),
        ['disabled="disabled"'],
    ],
    'disabled checkbox' => [
        fn (Design $design) => Checkbox::make($design)->name('terms')->label('Terms')->disabled(),
        ['disabled="disabled"'],
    ],
    'disabled radio' => [
        fn (Design $design) => Radio::make($design)->name('plan')->value('free')->label('Free')->disabled(),
        ['disabled="disabled"'],
    ],
    'live warning alert' => [
        fn (Design $design) => Alert::make($design)->variant(AlertVariant::Warning)->live()->title('Careful')->body('Body'),
        ['role="alert"'],
    ],
    'live info alert' => [
        fn (Design $design) => Alert::make($design)->variant(AlertVariant::Info)->live()->title('Heads up')->body('Body'),
        ['role="status"'],
    ],
]);
