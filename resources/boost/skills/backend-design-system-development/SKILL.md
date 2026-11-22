---
name: backend-design-system-development
description: Build Laravel UIs with juaniquillo/backend-design-system. Use when creating or changing Blade views, forms, buttons, alerts, cards or other UI with the package's PHP components, choosing a design, publishing Tailwind theme files, or adding a custom design or theme.
---

# Backend Design System

Use the package's PHP components instead of hand-written HTML. Components are fluent,
implement `Htmlable`, and get every Tailwind class from the active design's theme files.

## When to use this skill

Use it when the task involves:

- buttons, badges, cards, alerts or labels in a Blade view;
- forms, fields, inputs, textareas, selects, checkboxes or radios;
- choosing or switching the active design;
- publishing or editing theme files;
- adding a custom design for Tailwind;
- fixing markup, accessibility attributes or missing styles in a UI built with the package.

Do not edit files under `vendor/`. Application designs and theme files belong in
`resources/views`; configuration belongs in `config/backend-design-system.php`.

## Setup

```bash
composer require juaniquillo/backend-design-system

php artisan vendor:publish --tag=backend-design-system-config
php artisan vendor:publish --tag=backend-design-system-themes
```

The provider is auto-discovered. The config defaults work without publishing it.
Publishing the themes is what lets Tailwind scan the bundled classes.

## Core rules

1. Build UI with component objects, not raw HTML.
2. Use the variant and size enums; never pass raw strings for looks.
3. Let `make()` resolve the bound design. Pass a `Design` only to override it.
4. Use `component()` when a component goes inside another component.
5. Use `attributes()` for extra HTML or Livewire attributes, not for styling.
6. Put Tailwind classes in theme files, not in component calls.
7. Keep form controls inside `Field` when they need labels or error wiring.
8. Use `Alert::live()` only for alerts that appear after page load.

## Quick start

```php
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;
use Juaniquillo\BackendDesignSystem\Theme\Size;

echo Button::make()
    ->label('Save changes')
    ->variant(ButtonVariant::Primary)
    ->size(Size::Lg);
```

In Blade, echo the object directly:

```blade
{{ Button::make()->label('Save changes')->variant(ButtonVariant::Primary) }}
```

`Htmlable` means Blade does not escape the rendered HTML.

## Component reference

Every component has `make(?Design $design = null)` and `attributes(array $attributes)`.

| Component | Methods |
|---|---|
| `Alert` | `variant(AlertVariant)`, `title()`, `body()`, `live(bool)` |
| `Badge` | `label(string)`, `variant(BadgeVariant)` |
| `Button` | `label(string)`, `variant(ButtonVariant)`, `size(Size)`, `type(string)`, `disabled(bool)` |
| `Card` | `title()`, `body()`, `footer()`, `titleLevel(int)` |
| `Checkbox` | `name()`, `value()`, `label()`, `checked(bool)`, `disabled(bool)`, `required(bool)` |
| `Field` | `label()`, `input()`, `error()`, `id()`, `required(bool)` |
| `Form` | `action()`, `method()`, `fields(array)`, `actions()`, `csrf(bool)`, `methodInput(bool)` |
| `Input` | `type()`, `name()`, `value()`, `placeholder()`, `disabled(bool)`, `required(bool)` |
| `Label` | `text()`, `for()` |
| `Radio` | `name()`, `value()`, `label()`, `checked(bool)`, `disabled(bool)`, `required(bool)` |
| `Select` | `name()`, `options(array)`, `value()`, `disabled(bool)`, `required(bool)` |
| `Textarea` | `name()`, `rows(int)`, `value()`, `placeholder()`, `disabled(bool)`, `required(bool)` |

Namespaces:

```php
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
```

### Enums

```php
use Juaniquillo\BackendDesignSystem\Theme\AlertVariant;
use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;
use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;
use Juaniquillo\BackendDesignSystem\Theme\Size;
```

| Enum | Cases |
|---|---|
| `ButtonVariant` | `Default`, `Primary`, `Secondary`, `Danger`, `Ghost` |
| `BadgeVariant` | `Default`, `Success`, `Info`, `Warning`, `Danger` |
| `AlertVariant` | `Info`, `Success`, `Warning`, `Danger` |
| `Size` | `Sm`, `Md`, `Lg` |

## Common patterns

### Button

```php
Button::make()->label('Delete')->variant(ButtonVariant::Danger);
Button::make()->label('Small')->size(Size::Sm);
Button::make()->label('Disabled')->disabled();
Button::make()->label('Submit')->type('submit');
```

### Badge and alert

```php
Badge::make()->label('Active')->variant(BadgeVariant::Success);

Alert::make()
    ->variant(AlertVariant::Warning)
    ->title('Careful')
    ->body('This action cannot be undone.')
    ->live();
```

`live()` uses `role="alert"` for warning/danger and `role="status"` for the other
variants. Do not enable it for alerts already present at page load.

### Card with a nested action

```php
Card::make()
    ->title('Billing')
    ->body('Your plan renews monthly.')
    ->footer(Button::make()->label('Manage')->component());
```

`component()` is required when passing a design component into another component.

### Field with a built control

```php
Field::make()
    ->label('Email')
    ->input(Input::make()->type('email')->name('email')->component())
    ->error('That address is already taken.')
    ->required();
```

`Field::input()` also accepts a control name, in which case it builds a text input:

```php
Field::make()->label('Email')->input('email')->required();
```

### Form

```php
Form::make()
    ->action('/subscribe')
    ->method('post')
    ->fields([
        Field::make()->label('Email')->input('email')->required(),
        Checkbox::make()->name('terms')->label('I accept the terms')->required(),
    ])
    ->actions(Button::make()->label('Subscribe')->type('submit'));
```

`Form` does not add Laravel's CSRF field or `_method` input by default. Opt in when the
application needs them:

```php
Form::make()->csrf()->methodInput();
```

### Inputs, selects and textareas

```php
Input::make()->name('email')->type('email')->placeholder('you@example.com')->required();

Select::make()
    ->name('country')
    ->options(['es' => 'Spain', 'fr' => 'France'])
    ->value('es');

Textarea::make()->name('notes')->rows(3)->value('Anything we should know?');

Radio::make()->name('plan')->value('pro')->label('Pro')->checked();
```

`Select::options()` is keyed by option value.

### Extra attributes

```php
Button::make()
    ->label('Save')
    ->attributes(['wire:click' => 'save', 'data-test' => 'save-button']);
```

Use `attributes()` only for HTML or Livewire behaviour. Styling belongs in themes.

## Designs

The provider binds the design selected by `backend-design-system.default`:

```php
'default' => 'base',
```

Bundled designs:

| Key | Dark mode |
|---|---|
| `base` | Light only |
| `base-dark` | Dark only |
| `base-dual` | Adaptive, with `dark:` classes |

Resolve the active design when you need it:

```php
use Juaniquillo\BackendDesignSystem\Designs\Design;

$design = app(Design::class);
```

Override the design for one component tree by passing it to `make()`:

```php
Button::make($design)->label('Save');
```

### Custom design in the application

Create a theme folder under `resources/views` and point the config at it:

```php
'designs' => [
    'tenant' => [
        'label' => 'Tenant',
        'path' => 'views/designs/tenant',
        'dark' => 'adaptive',
    ],
],
```

`path` is resolved with `resource_path()`. The folder must contain the theme files listed
below.

### Custom packaged design

Create a manager that composes `IsThemeManager`:

```php
use Juaniquillo\BaseBackendComponent\Concerns\IsThemeManager;
use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;

final class TenantThemeManager implements ThemeManager
{
    use IsThemeManager;

    public function __construct()
    {
        $this->setDefaultPath(resource_path('views/designs/tenant'));
        $this->setThemeExtension('.blade.php');
    }
}
```

Reference it with `'manager' => TenantThemeManager::class`.

## Themes

Theme files are `.blade.php` files that return arrays. They are not Blade templates.

```php
<?php

use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;
use Juaniquillo\BackendDesignSystem\Theme\Size;
use Juaniquillo\BackendDesignSystem\Theme\State;

return [
    ButtonVariant::Default->value => 'bg-white text-black border border-zinc-400',
    ButtonVariant::Primary->value => 'bg-zinc-900 text-white hover:bg-zinc-700',
    ButtonVariant::Secondary->value => 'bg-zinc-200 text-black hover:bg-zinc-300',
    ButtonVariant::Danger->value => 'bg-red-700 text-white hover:bg-red-800',
    ButtonVariant::Ghost->value => 'text-black hover:bg-zinc-100',

    Size::Sm->value => 'px-2 py-1 text-xs',
    Size::Md->value => 'px-3 py-2 text-sm',
    Size::Lg->value => 'px-4 py-2 text-base',

    State::Disabled->value => 'opacity-50 cursor-not-allowed',
];
```

Required files and keys:

| File | Keys |
|---|---|
| `alert` | `info`, `success`, `warning`, `danger` |
| `badge` | `default`, `success`, `info`, `warning`, `danger` |
| `button` | `default`, `primary`, `secondary`, `danger`, `ghost`, `size.sm`, `size.md`, `size.lg`, `state.disabled` |
| `card` | `container`, `title`, `body`, `foot` |
| `checkbox` | `container`, `box`, `label` |
| `field` | `container`, `error` |
| `form` | `container`, `actions` |
| `input` | `default`, `state.disabled` |
| `label` | `default` |
| `radio` | `container`, `box`, `label` |
| `select` | `default`, `state.disabled` |
| `textarea` | `default`, `state.disabled` |
| `radius` | `none`, `sm`, `md`, `full` |
| `space` | `xs`, `sm`, `md`, `lg` |
| `text` | `xs`, `sm`, `md`, `lg` |

A missing file or key throws when the component renders. Fix the theme, not the
component.

### Tailwind

Publish the bundled themes:

```bash
php artisan vendor:publish --tag=backend-design-system-themes
```

They land in `resources/views/backend-design-system`, inside Tailwind's normal scan, so
no `@source` line is needed.

The bundled managers read from the package. Publishing is for Tailwind scanning; editing
the published copy alone does not change what the bundled designs render. Create an
application design with `path` or a custom manager when the classes need to change.

## Accessibility

- `Field` wires `for`/`id`, `required`, `aria-invalid` and `aria-describedby` and gives
  the error element an id.
- `Checkbox` and `Radio` wrap the input in a label, so the text is part of the target.
- `Alert::live()` adds the correct live region role.
- `required` and `disabled` become real HTML attributes.

Pass validation messages to `Field::error()`; the package does not read Laravel's error
bag automatically.

## Common mistakes

- Writing raw `<button>` or `<input>` markup instead of using a component.
- Passing a string like `'primary'` where a `ButtonVariant` case is expected.
- Adding Tailwind classes with `attributes()` instead of editing or creating a theme.
- Forgetting `component()` when nesting a component in a card, form or field.
- Forgetting to publish themes, so Tailwind never sees the classes.
- Editing published bundled themes and expecting the bundled manager to use them.
- Expecting CSRF or `_method` inputs by default; call `csrf()` and `methodInput()`.
- Using `live()` on alerts that are already present at page load.
- Passing a design to every component instead of relying on the bound design.

## Verification

Render the component in Blade and assert the expected HTML:

```php
$this->blade('{{ $component }}', [
    'component' => Button::make()->label('Save'),
])
    ->assertSee('<button', false)
    ->assertSee('Save')
    ->assertSee('</button>', false);
```

Run the application's test suite after changing components, designs or themes.
