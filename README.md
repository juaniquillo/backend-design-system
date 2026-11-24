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

## Documentation

- [Components and Accessibility](docs/components.md)
- [Theming and Configuration](docs/theming.md)

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
theme classes, so either publish the themes, scaffold an application theme, or add an
`@source` entry in `resources/css/app.css`:

```css
@source '../../vendor/juaniquillo/backend-design-system/resources/views/**/*.blade.php';
```

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

## Components

The package ships 24 server-rendered components covering typography, layout, forms,
feedback, disclosure, navigation, tables and dialogs. Slots accept strings, design
components or backend components directly, and wire accessibility attributes (`for`/`id`,
`required`, `disabled`, `aria-invalid`, `aria-describedby`, `aria-labelledby`,
`aria-current`, live region roles) automatically.

```php
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Components\Card;

echo Card::make()
    ->title('Billing')
    ->body('Your plan renews on the first of every month.')
    ->footer(Button::make()->label('Manage'));
```

See [docs/components.md](docs/components.md) for the full component reference, enum
cases, usage examples and accessibility details.

## Theming

Three bundled designs ship with the package:

| Key | Dark mode | Theme folder |
|---|---|---|
| `base` | `none` | `resources/views/backend-design-system/base` |
| `base-dark` | `always` | `resources/views/backend-design-system/base-dark` |
| `base-dual` | `adaptive` | `resources/views/backend-design-system/base-dual` |

To scaffold an editable application theme under `resources/views` and register it as the
default design in `config/backend-design-system.php`:

```bash
php artisan design-system:make-theme app
```

See [docs/theming.md](docs/theming.md) for configuration options, custom designs and
theme managers, required theme keys, Tailwind setup and dark mode rules.

## Testing

```bash
composer qa         # analyse → rector:check → lint:check → test:types → test:unit
composer test:unit  # Pest only (no --parallel on Windows)
composer test:types # 100% type coverage gate
```

The package's tests render every component with every bundled design and assert the
Blade output for each component, including states and accessibility attributes.

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
| `docs/` | Component, accessibility and theming documentation |
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
