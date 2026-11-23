<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Juaniquillo\BackendDesignSystem\Console\ThemeScaffolder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class MakeThemeCommand extends Command
{
    public function __construct(
        private readonly ThemeScaffolder $scaffolder,
    ) {
        parent::__construct();

        $this->setName('design-system:make-theme');
        $this->setDescription('Scaffold an application theme and add it to the design system config');
        $this->addArgument('name', InputArgument::OPTIONAL, 'Design key and theme folder name', 'app');
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Bundled design to copy', 'base');
        $this->addOption('dark', null, InputOption::VALUE_REQUIRED, 'Dark mode: none, always or adaptive');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Destination path under resources/ (default: views/backend-design-system/{name})');
        $this->addOption('keep-default', null, InputOption::VALUE_NONE, 'Do not change the default design');
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Overwrite an existing theme folder');
    }

    public function handle(): int
    {
        $name = $this->argument('name');
        $from = $this->option('from');
        $dark = $this->option('dark');
        $to = $this->option('to');
        $keepDefault = (bool) $this->option('keep-default');
        $force = (bool) $this->option('force');

        if (! is_string($name) || ! is_string($from)) {
            $this->error('The design name and source design must be strings.');

            return self::FAILURE;
        }

        if (! is_string($dark) && $dark !== null) {
            $this->error('The dark option must be a string.');

            return self::FAILURE;
        }

        if (! is_string($to) && $to !== null) {
            $this->error('The destination option must be a string.');

            return self::FAILURE;
        }

        try {
            $result = $this->scaffolder->make($name, $from, $dark, $keepDefault, $force, $to);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Theme [{$name}] created at {$result['theme']}.");
        $this->info("Design [{$name}] added to {$result['config']}.");

        if ($keepDefault) {
            $this->line("Set 'default' => '{$name}' to use it.");
        } elseif ($result['defaultChanged']) {
            $this->info("Default design set to [{$name}].");
        } else {
            $this->warn("Default design was not changed because 'default' is not a string literal. Set it manually.");
        }

        $this->line('Run php artisan config:clear if the config is cached.');

        return self::SUCCESS;
    }
}
