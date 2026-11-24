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
use Juaniquillo\BackendDesignSystem\Components\Dialog;
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
use Juaniquillo\BackendDesignSystem\Components\Table;
use Juaniquillo\BackendDesignSystem\Components\Tabs;
use Juaniquillo\BackendDesignSystem\Components\Text;
use Juaniquillo\BackendDesignSystem\Components\Textarea;
use Juaniquillo\BackendDesignSystem\Designs\Design;
use Juaniquillo\BackendDesignSystem\Theme\AlertVariant;
use Juaniquillo\BackendDesignSystem\Theme\HeadingLevel;

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
    'accordion' => [
        fn (Design $design) => Accordion::make($design)
            ->summary('Details')
            ->content('Hidden content')
            ->open(),
        ['<details', 'open="open"', '<summary', 'Details', '</summary>', 'Hidden content', '</details>'],
    ],
    'alert' => [
        fn (Design $design) => Alert::make($design)->title('Notice')->body('Your trial ends soon.'),
        ['<div', '<b', 'Notice', '</b>', '<p', 'Your trial ends soon.', '</p>', '</div>'],
    ],
    'badge' => [
        fn (Design $design) => Badge::make($design)->label('New'),
        ['<span', 'New', '</span>'],
    ],
    'breadcrumb' => [
        fn (Design $design) => Breadcrumb::make($design)->items([
            ['label' => 'Home', 'href' => '/'],
            ['label' => 'Docs', 'href' => '/docs'],
            ['label' => 'Current'],
        ]),
        [
            'role="navigation"',
            'aria-label="Breadcrumb"',
            '<ol',
            '<li',
            'href="/"',
            'Home',
            'href="/docs"',
            'Docs',
            'aria-current="page"',
            'Current',
            '</ol>',
        ],
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
    'choice-group' => [
        fn (Design $design) => ChoiceGroup::make($design)
            ->legend('Plan')
            ->choices([
                Radio::make($design)->name('plan')->value('free')->label('Free'),
                Radio::make($design)->name('plan')->value('pro')->label('Pro'),
            ])
            ->error('Choose a plan.')
            ->id('plan'),
        [
            '<fieldset',
            'id="plan"',
            '<legend',
            'Plan',
            '</legend>',
            'type="radio"',
            'name="plan"',
            'Free',
            'Pro',
            'aria-invalid="true"',
            'aria-describedby="plan-error"',
            'id="plan-error"',
            'Choose a plan.',
            '</fieldset>',
        ],
    ],
    'divider' => [
        fn (Design $design) => Divider::make($design),
        ['<div', 'role="separator"'],
    ],
    'dialog' => [
        fn (Design $design) => Dialog::make($design)
            ->id('delete-account')
            ->title('Delete account')
            ->body('This action cannot be undone.')
            ->footer('Cancel or confirm')
            ->open(),
        [
            '<dialog',
            'id="delete-account"',
            'aria-labelledby="delete-account-title"',
            'open="open"',
            '<h2',
            'id="delete-account-title"',
            'Delete account',
            '</h2>',
            'This action cannot be undone.',
            'Cancel or confirm',
            '</dialog>',
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
    'heading' => [
        fn (Design $design) => Heading::make($design)->text('Section')->level(HeadingLevel::H2),
        ['<h2', 'Section', '</h2>'],
    ],
    'inline' => [
        fn (Design $design) => Inline::make($design)->children([
            Button::make($design)->label('One'),
            Button::make($design)->label('Two'),
        ]),
        ['<div', '<button', 'One', 'Two', '</div>'],
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
    'link' => [
        fn (Design $design) => Link::make($design)->label('Docs')->href('/docs'),
        ['<a', 'href="/docs"', 'Docs', '</a>'],
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
    'stack' => [
        fn (Design $design) => Stack::make($design)->children([
            Text::make($design)->text('One'),
            Text::make($design)->text('Two'),
        ]),
        ['<div', '<p', 'One', 'Two', '</div>'],
    ],
    'table' => [
        fn (Design $design) => Table::make($design)
            ->caption('Team members')
            ->headers(['Name', 'Role'])
            ->rows([
                ['Alice', 'Admin'],
                ['Bob', 'Editor'],
            ])
            ->footer(['Total', '2']),
        [
            '<table',
            '<caption',
            'Team members',
            '</caption>',
            '<thead',
            '<th',
            'scope="col"',
            'Name',
            'Role',
            '</thead>',
            '<tbody',
            '<td',
            'Alice',
            'Admin',
            'Bob',
            'Editor',
            '</tbody>',
            '<tfoot',
            'Total',
            '2',
            '</tfoot>',
            '</table>',
        ],
    ],
    'tabs' => [
        fn (Design $design) => Tabs::make($design)->items([
            ['label' => 'General', 'href' => '/settings', 'current' => true],
            ['label' => 'Billing', 'href' => '/settings/billing'],
            ['label' => 'Enterprise'],
        ]),
        [
            'role="navigation"',
            'aria-label="Tabs"',
            '<ul',
            '<li',
            'href="/settings"',
            'aria-current="page"',
            'General',
            'href="/settings/billing"',
            'Billing',
            '<span',
            'Enterprise',
            '</ul>',
        ],
    ],
    'text' => [
        fn (Design $design) => Text::make($design)->text('Body copy'),
        ['<p', 'Body copy', '</p>'],
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
    'loading button' => [
        fn (Design $design) => Button::make($design)->label('Saving')->loading(),
        ['disabled="disabled"', 'aria-busy="true"', 'cursor-wait'],
    ],
    'current link' => [
        fn (Design $design) => Link::make($design)->label('Docs')->href('/docs')->current(),
        ['aria-current="page"'],
    ],
    'external link' => [
        fn (Design $design) => Link::make($design)->label('Docs')->href('https://example.com')->external(),
        ['target="_blank"', 'rel="noopener noreferrer"'],
    ],
    'inline text' => [
        fn (Design $design) => Text::make($design)->text('Inline')->inline(),
        ['<span', 'Inline', '</span>'],
    ],
    'heading clamp' => [
        fn (Design $design) => Heading::make($design)->text('Deep')->level(9),
        ['<h6', 'Deep', '</h6>'],
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
    'empty table with colspan' => [
        fn (Design $design) => Table::make($design)
            ->headers(['Name', 'Email', 'Role'])
            ->empty('No members found.'),
        ['<tbody', '<td', 'colspan="3"', 'No members found.', '</tbody>'],
    ],
    'dialog title level clamp' => [
        fn (Design $design) => Dialog::make($design)->title('Deep')->titleLevel(9),
        ['<h6', 'Deep', '</h6>'],
    ],
    'custom tabs aria label' => [
        fn (Design $design) => Tabs::make($design)
            ->label('Account sections')
            ->items([['label' => 'Profile', 'href' => '/profile']]),
        ['aria-label="Account sections"'],
    ],
]);

it('keeps the footer theme around a component footer', function () {
    // The footer is a container, not content: a component placed inside it
    // must not escape the padding and the separating rule.
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Card::make($design)
            ->title('Billing')
            ->footer(Button::make($design)->label('Manage')->component()),
    ]);

    $view->assertSee('border-t', false)
        ->assertSee('Manage', false);
});
