# Changelog

All notable changes to `backend-design-system` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `juaniquillo/laravel-backend-component` and `juaniquillo/base-backend-component` as direct dependencies.
- `config/backend-design-system.php`, published under the `backend-design-system-config` tag.
- Bundled `base`, `base-dark` and `base-dual` designs, with theme managers and publishable theme files under `backend-design-system-themes`.
- A container binding for the current `Design`, resolved from `backend-design-system.default`.

### Changed

- Package classes now live under `Juaniquillo\BackendDesignSystem`, grouped into `Components`, `Concerns`, `Contracts`, `Designs` and `Theme`.
- `Button::make()` and the other components resolve the bound `Design` when none is passed.

### Removed

- `DesignRegistry` and `DesignSystemCollection`; the package no longer ships a showcase.

