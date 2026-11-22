<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Tests;

use Illuminate\Foundation\Application;
use Juaniquillo\BackendComponents\BackendComponentsServiceProvider;
use Juaniquillo\BackendDesignSystem\BackendDesignSystemServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            BackendDesignSystemServiceProvider::class,
            BackendComponentsServiceProvider::class,
        ];
    }
}
