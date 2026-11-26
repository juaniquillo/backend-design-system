# Changelog

All notable changes to `backend-design-system` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v0.3.0] - 2026-11-26

### Added

- Per-item `attributes` on `Tabs` and `Breadcrumb`, applied to the generated `<a>` or `<span>` after `href`, `aria-current` and the theme.
- `CellBag` cells in `Table` headers, rows and footer, with attributes and design-system themes on the `<th>` or `<td>`, plus `['cells' => [...], 'attributes' => [...]]` rows with attributes on the `<tr>`.

## [v0.2.1] - 2026-11-24

### Changed

- Split the component, accessibility and theming documentation out of `README.md` into `docs/components.md` and `docs/theming.md`.

## [v0.2.0] - 2026-11-23

### Added

- `Table`, `Dialog` and `Tabs` components, with theme files for all three bundled designs.

## [v0.1.0] - 2026-11-22

### Added

- `juaniquillo/laravel-backend-component` and `juaniquillo/base-backend-component` as direct dependencies.
- `config/backend-design-system.php`, published under the `backend-design-system-config` tag.
- Bundled `base`, `base-dark` and `base-dual` designs, with theme managers and publishable theme files under `backend-design-system-themes`.
- A container binding for the current `Design`, resolved from `backend-design-system.default`.
- `Text`, `Heading`, `Link`, `Divider`, `Stack`, `Inline`, `Accordion`, `Breadcrumb` and `ChoiceGroup` components, with theme files for all three designs.
- `State::Loading` and `State::Current`, plus `Button::loading()`, `Link::current()` and `Link::external()`.
- `HeadingLevel` for type-safe heading theme keys.
- Content slots accept design components directly, so `component()` is no longer needed to nest a component.
- Independent publish tags for the config, all themes, and each bundled design.
- `design-system:make-theme` to scaffold an application theme and register it in config.
- `--to` on `design-system:make-theme` for a custom destination under `resources/`.

### Changed

- The bundled designs' badges are chips: padded edges and small type, so a badge no longer reads as coloured text.
- `Card` body and footer keep their section theme when the content is a component, so padding and the separating rule survive a component footer.
- `Card` titles built from components keep the card's title chrome, matching body and footer.
- Package classes now live under `Juaniquillo\BackendDesignSystem`, grouped into `Components`, `Concerns`, `Contracts`, `Designs` and `Theme`.
- `Button::make()` and the other components resolve the bound `Design` when none is passed.

### Fixed

- `Field` now wires custom error components with the error id and preserves any existing `aria-describedby` values on the input.
- `design-system:make-theme` now scopes duplicate detection to the `designs` array, respects existing config indentation, and leaves a non-literal default such as `env()` unchanged.

### Removed

- `DesignRegistry` and `DesignSystemCollection`; the package no longer ships a showcase.
