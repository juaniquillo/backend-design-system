# Backend Design System

[![CI](https://github.com/juaniquillo/backend-design-system/actions/workflows/ci.yml/badge.svg)](https://github.com/juaniquillo/backend-design-system/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/github/release/juaniquillo/backend-design-system.svg)](https://github.com/juaniquillo/backend-design-system/releases)
[![License](https://img.shields.io/github/license/juaniquillo/backend-design-system.svg)](LICENSE.md)
[![PHP](https://img.shields.io/badge/php-%5E8.3-777bb4.svg)](https://www.php.net)
[![Laravel](https://img.shields.io/badge/laravel-12%7C13-ff2d20.svg)](https://laravel.com)

A Laravel design system for building UIs in PHP. Components are plain PHP objects,
rendered through Blade, with no styling of their own: every class they wear comes from
a theme file belonging to a design.

```php
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;

echo Button::make()->label('Save')->variant(ButtonVariant::Primary);
```

## Features

- **PHP first.** Components are fluent objects implementing `Htmlable`. Echo them in
  Blade, store them in variables or nest them inside other components.
- **Designs, not skins.** A design is a folder of theme files plus a dark-mode rule.
  Switching designs is a config key.
- **Themes are plain files.** One file per component, returning an array of Tailwind
  classes keyed by enum values. A missing key is an error, not a silently unstyled
  element.
- **Accessible by default.** Field association, ids, `required`, `disabled`,
  `aria-invalid`, `aria-describedby` and live regions are wired by the components.
- **No baseline JavaScript.** Every component is server-rendered HTML.
- **Tailwind friendly.** Published themes live under `resources/views`, so Tailwind's
  default scan finds their classes without an `@source` line.

## Requirements

- PHP `^8.3`
- Laravel `12` or `13`
- [`juaniquillo/laravel-backend-component`](https://github.com/juaniquillo/laravel-backend-component) `^0.12`
- [`juaniquillo/base-backend-component`](https://github.com/juaniquillo/base-backend-component) `^0.1`

## Installation

```bash
composer require juaniquillo/backend-design-system
```

The service provider is auto-discovered:

```php
Juaniquillo\BackendDesignSystem\BackendDesignSystemServiceProvider::class
```

Publish the config and the bundled theme files:

```bash
php artisan vendor:publish --tag=backend-design-system-config
php artisan vendor:publish --tag=backend-design-system-themes
```

The config has sensible defaults even if you do not publish it. Publishing the themes
is what lets Tailwind scan the classes used by the bundled designs.

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

In Blade, echo the component directly. It is `Htmlable`, so Blade does not escape it:

```blade
{{ Button::make()->label('Save changes')->variant(ButtonVariant::Primary) }}
```

## How it works

- **Component.** A design component holds named slots, builds a
  `MainBackendComponent`, and exposes it through `component()` or renders it through
  `toHtml()`. Every component starts with `make()`.
- **Design.** A `Design` is a key, a label, a `ThemeManager` and a `DarkMode`. The
  provider binds the design selected by `backend-design-system.default` as a singleton,
  so components resolve it automatically.
- **Theme.** Each design folder contains one `.blade.php` file per component. The file
  returns an array of Tailwind classes keyed by variant, size or state.

## Configuration

`config/backend-design-system.php`:

```php
return [
    'default' => 'base',

    'designs' => [
        'base' => [
            'label' => 'Base',
            'manager' => Juaniquillo\BackendDesignSystem\Designs\BaseThemeManager::class,
            'dark' => 'none',
        ],

        'base-dark' => [
            'label' => 'Base Dark',
            'manager' => Juaniquillo\BackendDesignSystem\Designs\BaseDarkThemeManager::class,
            'dark' => 'always',
        ],

        'base-dual' => [
            'label' => 'Base Dual',
            'manager' => Juaniquillo\BackendDesignSystem\Designs\BaseDualThemeManager::class,
            'dark' => 'adaptive',
        ],
    ],
];
```

Each design accepts:

| Key | Meaning |
|---|---|
| `label` | Human-readable name |
| `manager` | A `ThemeManager` class that knows its theme folder |
| `path` | A folder under `resources/` instead of a manager |
| `dark` | `none` (light only), `always` (dark only) or `adaptive` (both) |

`path` is resolved with `resource_path()`, so `'path' => 'views/my-design'` points at
`resources/views/my-design`.

### Bundled designs

| Key | Dark mode | Theme folder |
|---|---|---|
| `base` | `none` | `resources/views/backend-design-system/base` |
| `base-dark` | `always` | `resources/views/backend-design-system/base-dark` |
| `base-dual` | `adaptive` | `resources/views/backend-design-system/base-dual` |

Switch the active design through the config:

```php
'default' => 'base-dual',
```

Clear the config cache after changing it in a cached environment:

```bash
php artisan config:clear
```

### Passing a design explicitly

`make()` resolves the bound design when called with no argument. Pass a design to
override it for one component tree:

```php
use Juaniquillo\BackendDesignSystem\Designs\Design;

$design = app(Design::class);

Button::make($design)->label('Save');
```

### Custom application design

Create the theme files under `resources/views` and point the config at the folder:

```php
'designs' => [
    'tenant' => [
        'label' => 'Tenant',
        'path' => 'views/designs/tenant',
        'dark' => 'adaptive',
    ],
],
```

`resources/views/designs/tenant/button.blade.php` then supplies the button classes.

### Custom theme manager

A packaged design can ship a manager with its folder baked in:

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

## Components

Every component has `make(?Design $design = null)` and `attributes(array $attributes)`.

| Component | Fluent API |
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

### Variants and sizes

```php
use Juaniquillo\BackendDesignSystem\Components\Badge;
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;
use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;
use Juaniquillo\BackendDesignSystem\Theme\Size;

Button::make()->label('Delete')->variant(ButtonVariant::Danger);
Button::make()->label('Small')->size(Size::Sm);
Button::make()->label('Disabled')->disabled();

Badge::make()->label('Active')->variant(BadgeVariant::Success);
```

| Enum | Cases |
|---|---|
| `ButtonVariant` | `Default`, `Primary`, `Secondary`, `Danger`, `Ghost` |
| `BadgeVariant` | `Default`, `Success`, `Info`, `Warning`, `Danger` |
| `AlertVariant` | `Info`, `Success`, `Warning`, `Danger` |
| `Size` | `Sm`, `Md`, `Lg` |

### Cards and composition

`component()` returns the underlying backend component, which is what nested slots
accept:

```php
use Juaniquillo\BackendDesignSystem\Components\Card;
use Juaniquillo\BackendDesignSystem\Components\Button;

echo Card::make()
    ->title('Billing')
    ->body('Your plan renews on the first of every month.')
    ->footer(Button::make()->label('Manage')->component());
```

### Forms and fields

`Field::input()` accepts either a control name or a built component. `Field` owns the
label/control association, so it writes `id`, `for`, `required` and the error `aria`
attributes onto the control you give it.

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

### Alerts

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

### Extra attributes

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
- `Alert::live()` produces the correct live region role.
- `required` and `disabled` are rendered as real HTML attributes, not just visual
  state.

The package does not read Laravel's validation error bag automatically. Pass the error
text to `Field::error()`.

## Theming

A theme file returns an array of Tailwind classes. Keys are the string values of the
public enums, plus utility keys used by the components.

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

A design folder must provide:

| File | Required keys |
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

A missing file or key throws, so a typo surfaces during rendering instead of producing an
unstyled element.

### Tailwind

The bundled theme files live in the package and are used for rendering. Publish them so
Tailwind can scan their classes:

```bash
php artisan vendor:publish --tag=backend-design-system-themes
```

They land in `resources/views/backend-design-system`, which is inside Tailwind's normal
scan. No `@source` line is needed.

The bundled managers always read the package's theme files. To change the classes
yourself, create an application design with `path` or a custom manager. Editing the
published copies alone does not change what the bundled designs render.

### Dark mode

| Value | Meaning |
|---|---|
| `none` | Light only; no `dark:` classes |
| `always` | Dark only |
| `adaptive` | Light and dark classes in the same theme files |

`base`, `base-dark` and `base-dual` demonstrate the three modes.

## Testing

```bash
composer qa         # analyse → rector:check → lint:check → test:types → test:unit
composer test:unit  # Pest only (no --parallel on Windows)
composer test:types # 100% type coverage gate
```

The package's tests render every component with every bundled design and assert the
Blade output for each component, including states and accessibility attributes.

## Workbench

A disposable Laravel app lives in `workbench/`:

```bash
composer build
composer serve
```

## AI skill

Install the Boost skill for agents building UIs with the package:

```bash
php artisan boost:add-skill https://github.com/juaniquillo/backend-design-system
```

## Structure

| Path | Purpose |
|---|---|
| `src/` | Package source (`Juaniquillo\BackendDesignSystem`) |
| `config/` | Publishable `backend-design-system.php` configuration |
| `resources/views/backend-design-system/` | Bundled theme files for the three designs |
| `resources/boost/skills/` | Boost skill for building UIs with the package |
| `tests/` | Pest feature, unit and architecture tests |
| `workbench/` | Disposable Laravel app powered by Orchestra Testbench |
| `.agents/skills/` | Laravel best-practices skill for package development |

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Contributing

Run `composer qa` before opening a pull request.

## License

MIT. See [LICENSE.md](LICENSE.md).
