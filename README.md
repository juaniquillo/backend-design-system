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

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Quick start](#quick-start)
- [How it works](#how-it-works)
- [Configuration](#configuration)
- [Scaffold a theme](#scaffold-an-application-theme)
- [Components](#components)
- [Accessibility](#accessibility)
- [Theming](#theming)
- [Testing](#testing)
- [Workbench](#workbench)
- [AI skill](#ai-skill)
- [Structure](#structure)
- [Support](#support)
- [Security](#security)
- [Credits](#credits)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [License](#license)

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

Publish only what you need. Each tag is independent:

```bash
# Just the config
php artisan vendor:publish --tag=backend-design-system-config

# All bundled themes
php artisan vendor:publish --tag=backend-design-system-themes

# Or one theme only
php artisan vendor:publish --tag=backend-design-system-theme-base
php artisan vendor:publish --tag=backend-design-system-theme-base-dark
php artisan vendor:publish --tag=backend-design-system-theme-base-dual
```

The config has sensible defaults even if you do not publish it. Tailwind needs to see the
theme classes, so either publish the themes or add an `@source` entry for the package
views. See [Tailwind](#tailwind) for both approaches.

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

### Scaffold an application theme

The fastest way to create an editable theme is the Artisan command:

```bash
php artisan design-system:make-theme app
```

It copies the bundled `base` theme to `resources/views/backend-design-system/app`,
adds an `app` design with a `path` entry to `config/backend-design-system.php`, and makes
it the default design.

If no config exists yet, the command publishes the package config, including `base`,
`base-dark` and `base-dual`. The published file becomes application-owned and will not
automatically receive new bundled entries from later package releases.

If the existing `default` is not a string literal, for example
`env('DESIGN_SYSTEM_DEFAULT', 'base')`, the command leaves it unchanged and prints a
warning. Set the new design as default manually.

Use `--to` to put the theme somewhere else under `resources/`:

```bash
php artisan design-system:make-theme tenant --to=views/designs/tenant
```

This writes to `resources/views/designs/tenant` and stores
`'path' => 'views/designs/tenant'`.

Options:

| Option | Meaning |
|---|---|
| `--from=base` | Source design: `base`, `base-dark` or `base-dual` |
| `--dark=none` | Override the dark mode: `none`, `always` or `adaptive` |
| `--to=views/designs/tenant` | Destination under `resources/`; defaults to `views/backend-design-system/{name}` |
| `--keep-default` | Add the design without changing the default |
| `--force` | Overwrite an existing theme folder |

The copied theme lives under `resources/views`, so Tailwind scans it without publishing
the package themes. The design is app-owned from that point on; the bundled designs stay
package-owned.

## Components

Every component has `make(?Design $design = null)` and `attributes(array $attributes)`.

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

### Cards and composition

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

### Forms and fields

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

### Text and headings

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

### Links and dividers

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

### Layout

```php
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

### Disclosure and breadcrumbs

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

### Tables, dialogs and tabs

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

### Choice groups

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
- `ChoiceGroup` wraps grouped choices in a `<fieldset>` with a `<legend>` and wires its
  error to the fieldset.
- `Link::current()` adds `aria-current="page"`, and external links add a safe `rel`.
- `Accordion` uses native `<details>`/`<summary>`, so disclosure works without JavaScript.
- `Alert::live()` produces the correct live region role.
- `required` and `disabled` are rendered as real HTML attributes, not just visual
  state.
- `Button::loading()` disables the button and adds `aria-busy="true"`.

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
    State::Loading->value => 'opacity-75 cursor-wait',
];
```

A design folder must provide:

| File | Required keys |
|---|---|
| `accordion` | `container`, `summary`, `content` |
| `alert` | `info`, `success`, `warning`, `danger` |
| `badge` | `default`, `success`, `info`, `warning`, `danger` |
| `breadcrumb` | `container`, `list`, `item`, `link`, `state.current`, `separator` |
| `button` | `default`, `primary`, `secondary`, `danger`, `ghost`, `size.sm`, `size.md`, `size.lg`, `state.disabled`, `state.loading` |
| `card` | `container`, `title`, `body`, `foot` |
| `checkbox` | `container`, `box`, `label` |
| `choice-group` | `container`, `legend`, `choices`, `error` |
| `dialog` | `container`, `title`, `body`, `foot` |
| `divider` | `default` |
| `field` | `container`, `error` |
| `form` | `container`, `actions` |
| `heading` | `h1`, `h2`, `h3`, `h4`, `h5`, `h6` |
| `inline` | `container` |
| `input` | `default`, `state.disabled` |
| `label` | `default` |
| `link` | `default`, `state.current` |
| `radio` | `container`, `box`, `label` |
| `select` | `default`, `state.disabled` |
| `stack` | `container` |
| `table` | `container`, `table`, `caption`, `head`, `header`, `body`, `row`, `cell`, `foot`, `empty` |
| `tabs` | `container`, `list`, `item`, `link`, `state.current` |
| `textarea` | `default`, `state.disabled` |
| `radius` | `none`, `sm`, `md`, `full` |
| `space` | `xs`, `sm`, `md`, `lg` |
| `text` | `xs`, `sm`, `md`, `lg` |

A missing file or key throws, so a typo surfaces during rendering instead of producing an
unstyled element.

### Tailwind

The bundled theme files live in the package and are used for rendering. Tailwind needs to
scan their classes. Choose one approach.

#### Publishing the themes

```bash
php artisan vendor:publish --tag=backend-design-system-themes
```

They land in `resources/views/backend-design-system`, which is inside Tailwind's normal
scan. No `@source` line is needed with Tailwind v4. With Tailwind v3, add
`./resources/views/backend-design-system/**/*.blade.php` to the `content` array.

#### Using the default classes

If you keep the package themes in place, point Tailwind at the package views:

```css
/* resources/css/app.css */
@source '../../vendor/juaniquillo/backend-design-system/resources/views/**/*.blade.php';
```

With Tailwind v3, add the same path to the `content` array:

```js
content: [
    './vendor/juaniquillo/backend-design-system/resources/views/**/*.blade.php',
],
```

The bundled managers always read the package's theme files. To change the classes
yourself, create an application design with `path` or a custom manager. Editing the
published copies alone does not change what the bundled designs render.

### Editing a bundled design

Publishing copies the themes into your application, but the bundled designs stay
package-owned. To copy only one design, use
`php artisan vendor:publish --tag=backend-design-system-theme-base`. To edit the copy,
point an application design at the published folder and make it the default:

```php
'designs' => [
    'base-editable' => [
        'label' => 'Base (editable)',
        'path' => 'views/backend-design-system/base',
        'dark' => 'none',
    ],
],
```

Then edit `resources/views/backend-design-system/base/*.blade.php`. The same pattern works
for `base-dark` (`dark => 'always'`) and `base-dual` (`dark => 'adaptive'`). Republish
with `--force` if you want to reset the copy to the package version.

For a fully independent design, keep your own theme folder and manager; that remains the
recommended path for production themes.

### Dark mode

| Value | Meaning |
|---|---|
| `none` | Light only; no `dark:` classes |
| `always` | Dark only |
| `adaptive` | Light and dark classes in the same theme files |

`base`, `base-dark` and `base-dual` demonstrate the three modes.

Adaptive designs emit `dark:` utilities and follow your Tailwind dark-mode strategy:
`media` by default, or `class`/`selector` when the application uses a theme toggle.

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

## Support

- [Issues](https://github.com/juaniquillo/backend-design-system/issues)
- [Source](https://github.com/juaniquillo/backend-design-system)

## Security

Please report vulnerabilities privately through
[GitHub's security advisories](https://github.com/juaniquillo/backend-design-system/security/advisories/new)
rather than opening a public issue.

## Credits

- [Victor Sánchez](https://github.com/juaniquillo)
- [`juaniquillo/laravel-backend-component`](https://github.com/juaniquillo/laravel-backend-component)
- [`juaniquillo/base-backend-component`](https://github.com/juaniquillo/base-backend-component)

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Contributing

Run `composer qa` before opening a pull request.

## License

MIT. See [LICENSE.md](LICENSE.md).
