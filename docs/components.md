# Components

Every component starts with `make(?Design $design = null)` and accepts extra HTML or
Livewire attributes through `attributes(array $attributes)`.

## Contents

- [Component reference](#component-reference)
- [Variants and sizes](#variants-and-sizes)
- [Cards and composition](#cards-and-composition)
- [Forms and fields](#forms-and-fields)
- [Alerts](#alerts)
- [Text and headings](#text-and-headings)
- [Links and dividers](#links-and-dividers)
- [Layout](#layout)
- [Disclosure and breadcrumbs](#disclosure-and-breadcrumbs)
- [Tables, dialogs and tabs](#tables-dialogs-and-tabs)
- [Choice groups](#choice-groups)
- [Extra attributes](#extra-attributes)
- [Accessibility](#accessibility)

## Component reference

| Component | Fluent API |
|---|---|
| `Accordion` | `summary()`, `content()`, `open(bool)` |
| `Alert` | `variant(AlertVariant)`, `title()`, `body()`, `live(bool)` |
| `Badge` | `label(string)`, `variant(BadgeVariant)` |
| `Breadcrumb` | `items(array)` |
| `Button` | `label(string)`, `variant(ButtonVariant)`, `size(Size)`, `type(string)`, `disabled(bool)`, `loading(bool)` |
| `Card` | `title()`, `body()`, `footer()`, `titleLevel(int)` |
| `Checkbox` | `name()`, `value()`, `label()`, `checked(bool)`, `disabled(bool)`, `required(bool)` |
| `ChoiceGroup` | `legend(string)`, `choices(array)`, `error()`, `id(?string)` |
| `Dialog` | `title()`, `body()`, `footer()`, `titleLevel(int)`, `open(bool)`, `id(?string)` |
| `Divider` | attributes only |
| `Field` | `label()`, `input()`, `error()`, `id()`, `required(bool)` |
| `Form` | `action()`, `method()`, `fields(array)`, `actions()`, `csrf(bool)`, `methodInput(bool)` |
| `Heading` | `text(string)`, `level(int\|HeadingLevel)` |
| `Inline` | `children(array)`, `space(Size)` |
| `Input` | `type()`, `name()`, `value()`, `placeholder()`, `disabled(bool)`, `required(bool)` |
| `Label` | `text()`, `for()` |
| `Link` | `label(string)`, `href(string)`, `current(bool)`, `external(bool)` |
| `Radio` | `name()`, `value()`, `label()`, `checked(bool)`, `disabled(bool)`, `required(bool)` |
| `Select` | `name()`, `options(array)`, `value()`, `disabled(bool)`, `required(bool)` |
| `Stack` | `children(array)`, `space(Size)` |
| `Table` | `headers(array)`, `rows(array)`, `footer(array)`, `caption()`, `empty()` |
| `Tabs` | `items(array)`, `label(string)` |
| `Text` | `text(string)`, `size(Size)`, `inline(bool)` |
| `Textarea` | `name()`, `rows(int)`, `value()`, `placeholder()`, `disabled(bool)`, `required(bool)` |

## Variants and sizes

```php
use Juaniquillo\BackendDesignSystem\Components\Badge;
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;
use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;
use Juaniquillo\BackendDesignSystem\Theme\Size;

Button::make()->label('Delete')->variant(ButtonVariant::Danger);
Button::make()->label('Small')->size(Size::Sm);
Button::make()->label('Disabled')->disabled();
Button::make()->label('Saving')->loading();

Badge::make()->label('Active')->variant(BadgeVariant::Success);
```

`Button::loading()` also disables the button and adds `aria-busy="true"`.
`Button::type()` defaults to `button`; set `type('submit')` when it submits a form.

| Enum | Cases |
|---|---|
| `ButtonVariant` | `Default`, `Primary`, `Secondary`, `Danger`, `Ghost` |
| `BadgeVariant` | `Default`, `Success`, `Info`, `Warning`, `Danger` |
| `AlertVariant` | `Info`, `Success`, `Warning`, `Danger` |
| `HeadingLevel` | `H1`, `H2`, `H3`, `H4`, `H5`, `H6` |
| `Size` | `Sm`, `Md`, `Lg` |
| `State` | `Disabled`, `Loading`, `Current` |

## Cards and composition

Slots take strings, design components or backend components, so a component nests
inside another without `component()`:

`Card::titleLevel()` defaults to 3 and is clamped to the six HTML heading levels.

```php
use Juaniquillo\BackendDesignSystem\Components\Card;
use Juaniquillo\BackendDesignSystem\Components\Button;

echo Card::make()
    ->title('Billing')
    ->body('Your plan renews on the first of every month.')
    ->footer(Button::make()->label('Manage'));
```

`component()` remains available when you need the underlying backend component's
full API (attributes, themes, Livewire) before a slot takes it.

## Forms and fields

`Field::input()` accepts a control name, a design component or a built backend
component. `Field` owns the label/control association, so it writes `id`, `for`,
`required` and the error `aria` attributes onto the control you give it.

```php
use Juaniquillo\BackendDesignSystem\Components\Checkbox;
use Juaniquillo\BackendDesignSystem\Components\Field;
use Juaniquillo\BackendDesignSystem\Components\Form;
use Juaniquillo\BackendDesignSystem\Components\Button;

echo Form::make()
    ->action('/subscribe')
    ->method('post')
    ->fields([
        Field::make()->label('Email')->input('email')->required(),
        Checkbox::make()->name('terms')->label('I accept the terms')->required(),
    ])
    ->actions(Button::make()->label('Subscribe')->type('submit'));
```

`Form` does not emit Laravel's CSRF field or `_method` input by default. Opt in when you
want them:

```php
Form::make()->csrf()->methodInput();
```

For POST forms in a Laravel app, call `csrf()`; without the token Laravel rejects the
request with 419. `methodInput()` emits the hidden `_method` field needed for PUT, PATCH
and DELETE.

## Alerts

```php
use Juaniquillo\BackendDesignSystem\Components\Alert;
use Juaniquillo\BackendDesignSystem\Theme\AlertVariant;

echo Alert::make()
    ->variant(AlertVariant::Danger)
    ->title('Export failed')
    ->body('The file could not be generated.')
    ->live();
```

`live()` adds `role="alert"` for warning and danger alerts, and `role="status"` for the
others. Leave it off for alerts that are already on the page at load.

## Text and headings

```php
use Juaniquillo\BackendDesignSystem\Components\Heading;
use Juaniquillo\BackendDesignSystem\Components\Text;
use Juaniquillo\BackendDesignSystem\Theme\HeadingLevel;
use Juaniquillo\BackendDesignSystem\Theme\Size;

Heading::make()->text('Billing')->level(HeadingLevel::H2);
Text::make()->text('Your plan renews monthly.')->size(Size::Sm);
Text::make()->text('Inline note')->inline();
```

`Text` renders a paragraph by default and a span when `inline()` is set. `Heading`
renders `h1` through `h6`; `level()` accepts a `HeadingLevel` case or an integer clamped
to that range.

## Links and dividers

```php
use Juaniquillo\BackendDesignSystem\Components\Divider;
use Juaniquillo\BackendDesignSystem\Components\Link;

Link::make()->label('Documentation')->href('/docs');
Link::make()->label('Documentation')->href('/docs')->current();
Link::make()->label('External')->href('https://example.com')->external();

Divider::make();
```

Current links receive `aria-current="page"`. External links receive
`target="_blank"` and `rel="noopener noreferrer"`.

## Layout

```php
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Components\Inline;
use Juaniquillo\BackendDesignSystem\Components\Stack;
use Juaniquillo\BackendDesignSystem\Components\Text;
use Juaniquillo\BackendDesignSystem\Theme\Size;

Stack::make()
    ->space(Size::Sm)
    ->children([
        Text::make()->text('First'),
        Text::make()->text('Second'),
    ]);

Inline::make()
    ->space(Size::Sm)
    ->children([
        Button::make()->label('Cancel'),
        Button::make()->label('Save'),
    ]);
```

## Disclosure and breadcrumbs

```php
use Juaniquillo\BackendDesignSystem\Components\Accordion;
use Juaniquillo\BackendDesignSystem\Components\Breadcrumb;

Accordion::make()
    ->summary('Shipping details')
    ->content('Ships in two business days.')
    ->open();

Breadcrumb::make()->items([
    ['label' => 'Home', 'href' => '/'],
    ['label' => 'Billing', 'href' => '/billing'],
    ['label' => 'Invoices'],
]);
```

`Accordion` uses native `<details>`/`<summary>`, so no JavaScript is required. The last
breadcrumb item is the current page.

## Tables, dialogs and tabs

```php
use Juaniquillo\BackendDesignSystem\Components\Badge;
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Components\Dialog;
use Juaniquillo\BackendDesignSystem\Components\Table;
use Juaniquillo\BackendDesignSystem\Components\Tabs;
use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;

Table::make()
    ->caption('Team members')
    ->headers(['Name', 'Status'])
    ->rows([
        ['Alice', Badge::make()->label('Active')->variant(BadgeVariant::Success)],
    ])
    ->empty('No members found.');

Dialog::make()
    ->id('delete-account')
    ->title('Delete account')
    ->body('This action cannot be undone.')
    ->footer(Button::make()->label('Confirm'))
    ->open();

Tabs::make()->items([
    ['label' => 'General', 'href' => '/settings', 'current' => true],
    ['label' => 'Billing', 'href' => '/settings/billing'],
]);
```

`Table` renders `<th scope="col">` headers and spans its `empty()` row across all header
columns when `rows` is empty. `Dialog` renders a native `<dialog>` element and wires
`aria-labelledby` when both `id` and `title` are given. `Tabs` renders a navigation list
with `aria-current="page"` on the active tab.

## Choice groups

```php
use Juaniquillo\BackendDesignSystem\Components\ChoiceGroup;
use Juaniquillo\BackendDesignSystem\Components\Radio;

ChoiceGroup::make()
    ->legend('Plan')
    ->choices([
        Radio::make()->name('plan')->value('free')->label('Free'),
        Radio::make()->name('plan')->value('pro')->label('Pro'),
    ])
    ->id('plan')
    ->error('Choose a plan.');
```

`ChoiceGroup` wraps its choices in a `<fieldset>` with a `<legend>`, and wires the error
to the fieldset when an `id` is provided.

## Extra attributes

`attributes()` merges HTML and Livewire attributes onto the primary element:

```php
Button::make()
    ->label('Save')
    ->attributes(['wire:click' => 'save', 'data-test' => 'save-button']);
```

## Accessibility

- `Field` associates its label and control through `for`/`id`, and wires `required`,
  `aria-invalid` and `aria-describedby` when you pass an error.
- `Checkbox` and `Radio` wrap their input in a label, so the text is part of the click
  target.
- `ChoiceGroup` wraps grouped choices in a `<fieldset>` with a `<legend>` and wires its
  error to the fieldset.
- `Link::current()` and active `Tabs` items add `aria-current="page"`, and external links
  add a safe `rel`.
- `Accordion` uses native `<details>`/`<summary>`, so disclosure works without JavaScript.
- `Dialog` uses native `<dialog>` and wires `aria-labelledby` when given both an `id` and
  a `title`.
- `Table` adds `scope="col"` to every header cell and spans its `empty()` row across the
  header columns.
- `Alert::live()` produces the correct live region role.
- `required` and `disabled` are rendered as real HTML attributes, not just visual
  state.
- `Button::loading()` disables the button and adds `aria-busy="true"`.

The package does not read Laravel's validation error bag automatically. Pass the error
text to `Field::error()`.
