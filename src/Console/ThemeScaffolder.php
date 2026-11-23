<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

/**
 * Copies a bundled design into the application and registers it in config.
 */
final readonly class ThemeScaffolder
{
    public function __construct(
        private Filesystem $files,
        private string $configPath,
        private string $resourcesPath,
        private string $packageConfigPath,
        private string $packageThemesPath,
    ) {}

    /**
     * @return array{config: string, defaultChanged: bool, path: string, theme: string}
     */
    public function make(
        string $name,
        string $from = 'base',
        ?string $dark = null,
        bool $keepDefault = false,
        bool $force = false,
        ?string $to = null,
    ): array {
        $this->assertName($name);
        $this->assertSource($from);

        $darkMode = $this->darkMode($from, $dark);
        $source = $this->packageThemesPath.'/'.$from;
        $relativePath = $this->destinationPath($name, $to);
        $destination = $this->resourcesPath.'/'.$relativePath;

        if (! $this->files->isDirectory($source)) {
            throw new InvalidArgumentException("The bundled design [{$from}] is missing from the package.");
        }

        if ($this->files->isDirectory($destination) && ! $force) {
            throw new InvalidArgumentException("The theme folder [{$destination}] already exists. Use --force to overwrite it.");
        }

        $this->ensureConfig();

        $contents = $this->files->get($this->configPath);

        if ($this->hasDesign($this->configPath, $name)) {
            throw new InvalidArgumentException("The design [{$name}] already exists in {$this->configPath}.");
        }

        if ($force) {
            $this->files->deleteDirectory($destination);
        }

        if (! $this->files->copyDirectory($source, $destination)) {
            throw new InvalidArgumentException("Could not copy the theme to [{$destination}].");
        }

        [$indent, $unit] = $this->designIndentation($contents);

        $contents = $this->insertDesign(
            $contents,
            $this->designEntry($name, $darkMode, $relativePath, $indent, $unit),
        );

        $defaultChanged = false;

        if (! $keepDefault) {
            $updated = $this->setDefault($contents, $name);

            if ($updated !== null) {
                $contents = $updated;
                $defaultChanged = true;
            }
        }

        $this->files->put($this->configPath, $contents);

        return [
            'config' => $this->configPath,
            'defaultChanged' => $defaultChanged,
            'path' => $relativePath,
            'theme' => $destination,
        ];
    }

    private function assertName(string $name): void
    {
        if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $name) !== 1) {
            throw new InvalidArgumentException('The design name must use lowercase letters, numbers and hyphens.');
        }

        if (in_array($name, ['base', 'base-dark', 'base-dual', 'default', 'designs'], true)) {
            throw new InvalidArgumentException("The design name [{$name}] is reserved.");
        }
    }

    private function assertSource(string $from): void
    {
        if (! in_array($from, ['base', 'base-dark', 'base-dual'], true)) {
            throw new InvalidArgumentException("Unknown bundled design [{$from}]. Use base, base-dark or base-dual.");
        }
    }

    private function darkMode(string $from, ?string $dark): string
    {
        if ($dark !== null && $dark !== '') {
            if (! in_array($dark, ['none', 'always', 'adaptive'], true)) {
                throw new InvalidArgumentException("Unknown dark mode [{$dark}]. Use none, always or adaptive.");
            }

            return $dark;
        }

        return match ($from) {
            'base-dark' => 'always',
            'base-dual' => 'adaptive',
            default => 'none',
        };
    }

    private function destinationPath(string $name, ?string $to): string
    {
        if ($to === null) {
            return 'views/backend-design-system/'.$name;
        }

        $raw = str_replace('\\', '/', $to);

        if ($raw === '' || str_contains($raw, "\0")) {
            throw new InvalidArgumentException('The destination path cannot be empty.');
        }

        if (str_starts_with($raw, '/') || preg_match('/^[A-Za-z]:/', $raw) === 1) {
            throw new InvalidArgumentException('The destination path must be relative to resources/.');
        }

        $normalized = preg_replace('#/+#', '/', trim($raw, '/'));

        if (! is_string($normalized)) {
            throw new InvalidArgumentException('The destination path is invalid.');
        }

        foreach (explode('/', $normalized) as $segment) {
            if (in_array($segment, ['', '.', '..'], true) || preg_match('/^[A-Za-z0-9._ -]+$/', $segment) !== 1) {
                throw new InvalidArgumentException('The destination path contains an invalid segment.');
            }
        }

        return $normalized;
    }

    private function ensureConfig(): void
    {
        if ($this->files->exists($this->configPath)) {
            return;
        }

        $this->files->ensureDirectoryExists(dirname($this->configPath));

        if (! $this->files->copy($this->packageConfigPath, $this->configPath)) {
            throw new InvalidArgumentException("Could not publish the config to [{$this->configPath}].");
        }
    }

    private function hasDesign(string $path, string $name): bool
    {
        try {
            $config = $this->files->getRequire($path);
        } catch (Throwable $exception) {
            throw new InvalidArgumentException("Could not read the config at [{$path}].", 0, $exception);
        }

        if (! is_array($config) || ! isset($config['designs']) || ! is_array($config['designs'])) {
            throw new InvalidArgumentException("The config at [{$path}] must return a designs array.");
        }

        return array_key_exists($name, $config['designs']);
    }

    private function insertDesign(string $contents, string $entry): string
    {
        $updated = preg_replace_callback(
            '/^([ \t]*\/\/ design-system:designs[ \t]*\n)/m',
            fn (array $matches): string => $matches[1].$entry,
            $contents,
            1,
            $count,
        );

        if ($count === 1 && is_string($updated)) {
            return $updated;
        }

        $updated = preg_replace_callback(
            "/('designs'\s*=>\s*\[\n)/",
            fn (array $matches): string => $matches[1].$entry,
            $contents,
            1,
            $count,
        );

        if ($count === 1 && is_string($updated)) {
            return $updated;
        }

        throw new InvalidArgumentException('Could not find the designs array in the package configuration.');
    }

    private function setDefault(string $contents, string $name): ?string
    {
        $updated = preg_replace(
            "/('default'\s*=>\s*)(['\"])([^'\"]*)\\2/",
            "$1'{$name}'",
            $contents,
            1,
            $count,
        );

        return $count === 1 && is_string($updated) ? $updated : null;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function designIndentation(string $contents): array
    {
        $designsIndent = '    ';

        if (preg_match("/^([ \t]*)'designs'\s*=>\s*\[/m", $contents, $matches) === 1) {
            $designsIndent = $matches[1];
        }

        if (preg_match('/^([ \t]*)\/\/ design-system:designs/m', $contents, $matches) === 1) {
            $entryIndent = $matches[1];
        } else {
            $entryIndent = $designsIndent.'    ';
        }

        $unit = '    ';

        if (str_starts_with($entryIndent, $designsIndent) && strlen($entryIndent) > strlen($designsIndent)) {
            $unit = substr($entryIndent, strlen($designsIndent));
        }

        return [$entryIndent, $unit];
    }

    private function designEntry(string $name, string $dark, string $path, string $indent, string $unit): string
    {
        $label = Str::headline($name);

        return $indent."'{$name}' => [\n".
            $indent.$unit."'label' => '{$label}',\n".
            $indent.$unit."'path' => '{$path}',\n".
            $indent.$unit."'dark' => '{$dark}',\n".
            $indent."],\n";
    }
}
