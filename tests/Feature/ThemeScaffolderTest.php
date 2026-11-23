<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Juaniquillo\BackendDesignSystem\Console\ThemeScaffolder;

beforeEach(function () {
    $this->files = new Filesystem;
    $this->base = sys_get_temp_dir().'/backend-design-system-'.uniqid();
    $this->configPath = $this->base.'/config/backend-design-system.php';
    $this->resourcesPath = $this->base.'/resources';
    $this->themesPath = $this->resourcesPath.'/views/backend-design-system';
    $this->scaffolder = new ThemeScaffolder(
        $this->files,
        $this->configPath,
        $this->resourcesPath,
        dirname(__DIR__, 2).'/config/backend-design-system.php',
        dirname(__DIR__, 2).'/resources/views/backend-design-system',
    );
});

afterEach(function () {
    $this->files->deleteDirectory($this->base);
});

it('scaffolds a theme and registers it', function () {
    $result = $this->scaffolder->make('app');

    expect($result['theme'])->toBe($this->themesPath.'/app')
        ->and($result['path'])->toBe('views/backend-design-system/app')
        ->and($result['defaultChanged'])->toBeTrue()
        ->and($this->files->exists($result['theme'].'/button.blade.php'))->toBeTrue()
        ->and($this->files->exists($this->configPath))->toBeTrue();

    $config = require $this->configPath;

    expect($config['default'])->toBe('app')
        ->and($config['designs'])->toHaveKeys(['base', 'base-dark', 'base-dual', 'app'])
        ->and($config['designs']['app']['label'])->toBe('App')
        ->and($config['designs']['app']['path'])->toBe('views/backend-design-system/app')
        ->and($config['designs']['app']['dark'])->toBe('none')
        ->and($config['designs']['app'])->not->toHaveKey('manager');
});

it('keeps the default design when asked', function () {
    $this->scaffolder->make('tenant', 'base-dark', null, true);

    $config = require $this->configPath;

    expect($config['default'])->toBe('base')
        ->and($config['designs']['tenant']['dark'])->toBe('always');
});

it('infers the dark mode from the source design', function () {
    $this->scaffolder->make('dual', 'base-dual');

    $config = require $this->configPath;

    expect($config['designs']['dual']['dark'])->toBe('adaptive');
});

it('accepts an explicit dark mode', function () {
    $this->scaffolder->make('custom', 'base', 'always');

    $config = require $this->configPath;

    expect($config['designs']['custom']['dark'])->toBe('always');
});

it('uses a custom destination path', function () {
    $result = $this->scaffolder->make('tenant', 'base', null, false, false, 'views/designs/tenant');

    expect($result['theme'])->toBe($this->resourcesPath.'/views/designs/tenant')
        ->and($result['path'])->toBe('views/designs/tenant')
        ->and($this->files->exists($result['theme'].'/button.blade.php'))->toBeTrue();

    $config = require $this->configPath;

    expect($config['designs']['tenant']['path'])->toBe('views/designs/tenant');
});

it('normalizes a custom destination path', function () {
    $result = $this->scaffolder->make('tenant', 'base', null, false, false, 'views\\designs//tenant/');

    expect($result['path'])->toBe('views/designs/tenant')
        ->and($result['theme'])->toBe($this->resourcesPath.'/views/designs/tenant');
});

it('checks the designs array instead of top-level config keys', function () {
    $this->files->ensureDirectoryExists(dirname($this->configPath));
    $this->files->put($this->configPath, <<<'PHP'
<?php

return [
    'default' => 'base',
    'app' => 'not-a-design',
    'designs' => [
        // design-system:designs
    ],
];
PHP);

    $this->scaffolder->make('app');

    $config = require $this->configPath;

    expect($config['designs'])->toHaveKey('app');
});

it('leaves a non-literal default untouched', function () {
    $this->files->ensureDirectoryExists(dirname($this->configPath));
    $this->files->put($this->configPath, <<<'PHP'
<?php

return [
    'default' => env('DESIGN_SYSTEM_DEFAULT', 'base'),
    'designs' => [
        // design-system:designs
    ],
];
PHP);

    $result = $this->scaffolder->make('app');

    expect($result['defaultChanged'])->toBeFalse();

    $contents = $this->files->get($this->configPath);

    expect($contents)->toContain("'default' => env('DESIGN_SYSTEM_DEFAULT', 'base')");

    $config = require $this->configPath;

    expect($config['designs'])->toHaveKey('app');
});

it('matches the indentation of the config', function () {
    $this->files->ensureDirectoryExists(dirname($this->configPath));
    $this->files->put($this->configPath, <<<'PHP'
<?php

return [
  'default' => 'base',
  'designs' => [
    // design-system:designs
  ],
];
PHP);

    $this->scaffolder->make('app');

    $contents = $this->files->get($this->configPath);

    expect($contents)->toContain(
        "\n    'app' => [\n".
        "      'label' => 'App',\n".
        "      'path' => 'views/backend-design-system/app',\n".
        "      'dark' => 'none',\n".
        "    ],\n",
    );
});

it('refuses reserved names and invalid options', function () {
    expect(fn () => $this->scaffolder->make('base'))
        ->toThrow(InvalidArgumentException::class)
        ->and(fn () => $this->scaffolder->make('Bad Name'))
        ->toThrow(InvalidArgumentException::class)
        ->and(fn () => $this->scaffolder->make('app', 'missing'))
        ->toThrow(InvalidArgumentException::class)
        ->and(fn () => $this->scaffolder->make('app', 'base', 'sometimes'))
        ->toThrow(InvalidArgumentException::class)
        ->and(fn () => $this->scaffolder->make('app', 'base', null, false, false, ''))
        ->toThrow(InvalidArgumentException::class)
        ->and(fn () => $this->scaffolder->make('app', 'base', null, false, false, '../outside'))
        ->toThrow(InvalidArgumentException::class)
        ->and(fn () => $this->scaffolder->make('app', 'base', null, false, false, '/absolute'))
        ->toThrow(InvalidArgumentException::class)
        ->and(fn () => $this->scaffolder->make('app', 'base', null, false, false, 'C:\\theme'))
        ->toThrow(InvalidArgumentException::class);
});

it('refuses to overwrite an existing design or theme folder', function () {
    $this->scaffolder->make('app');

    expect(fn () => $this->scaffolder->make('app'))
        ->toThrow(InvalidArgumentException::class);

    $this->files->ensureDirectoryExists($this->themesPath.'/existing');

    expect(fn () => $this->scaffolder->make('existing'))
        ->toThrow(InvalidArgumentException::class);
});

it('overwrites a theme folder with force', function () {
    $this->files->ensureDirectoryExists($this->themesPath.'/app');
    $this->files->put($this->themesPath.'/app/old.txt', 'old');

    $this->scaffolder->make('app', 'base', null, false, true);

    expect($this->files->exists($this->themesPath.'/app/old.txt'))->toBeFalse()
        ->and($this->files->exists($this->themesPath.'/app/button.blade.php'))->toBeTrue();
});

it('runs the make-theme command', function () {
    $this->app->instance(ThemeScaffolder::class, $this->scaffolder);

    $this->artisan('design-system:make-theme', ['name' => 'app'])
        ->assertSuccessful();

    $config = require $this->configPath;

    expect($config['default'])->toBe('app')
        ->and($this->files->exists($this->themesPath.'/app/button.blade.php'))->toBeTrue();
});

it('runs the make-theme command with a custom destination', function () {
    $this->app->instance(ThemeScaffolder::class, $this->scaffolder);

    $this->artisan('design-system:make-theme', [
        'name' => 'tenant',
        '--to' => 'views/designs/tenant',
    ])->assertSuccessful();

    $config = require $this->configPath;

    expect($config['designs']['tenant']['path'])->toBe('views/designs/tenant')
        ->and($this->files->exists($this->resourcesPath.'/views/designs/tenant/button.blade.php'))->toBeTrue();
});
