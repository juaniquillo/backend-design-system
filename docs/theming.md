# Theming and Configuration

[← Back to README](../README.md) · [← Components and Accessibility](components.md)

A design is a folder of theme files plus a dark-mode rule. Components carry no styling of
their own: every Tailwind class they render comes from the active design's theme files.

## Contents

- [Configuration](#configuration)
- [Bundled designs](#bundled-designs)
- [Passing a design explicitly](#passing-a-design-explicitly)
- [Custom application design](#custom-application-design)
- [Custom theme manager](#custom-theme-manager)
- [Scaffold an application theme](#scaffold-an-application-theme)
- [Theme files](#theme-files)
- [Tailwind](#tailwind)
- [Editing a bundled design](#editing-a-bundled-design)
- [Dark mode](#dark-mode)

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

## Bundled designs

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

## Passing a design explicitly

`make()` resolves the bound design when called with no argument. Pass a design to
override it for one component tree:

```php
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Designs\Design;

$design = app(Design::class);

Button::make($design)->label('Save');
```

## Custom application design

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

## Custom theme manager

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

## Scaffold an application theme

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

## Theme files

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

## Tailwind

The bundled theme files live in the package and are used for rendering. Tailwind needs to
scan their classes. Choose one approach.

### Publishing the themes

```bash
php artisan vendor:publish --tag=backend-design-system-themes
```

They land in `resources/views/backend-design-system`, which is inside Tailwind's normal
scan. No `@source` line is needed with Tailwind v4. With Tailwind v3, add
`./resources/views/backend-design-system/**/*.blade.php` to the `content` array.

### Using the default classes

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

## Editing a bundled design

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

## Dark mode

| Value | Meaning |
|---|---|
| `none` | Light only; no `dark:` classes |
| `always` | Dark only |
| `adaptive` | Light and dark classes in the same theme files |

`base`, `base-dark` and `base-dual` demonstrate the three modes.

Adaptive designs emit `dark:` utilities and follow your Tailwind dark-mode strategy:
`media` by default, or `class`/`selector` when the application uses a theme toggle.
