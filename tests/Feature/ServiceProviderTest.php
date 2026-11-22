<?php

declare(strict_types=1);

use Juaniquillo\BackendDesignSystem\BackendDesignSystemServiceProvider;

it('registers the service provider', function () {
    $provider = $this->app->getProvider(BackendDesignSystemServiceProvider::class);

    expect($provider)->toBeInstanceOf(BackendDesignSystemServiceProvider::class);
});

it('boots the service provider', function () {
    $loadedProviders = $this->app->getLoadedProviders();

    expect($loadedProviders[BackendDesignSystemServiceProvider::class] ?? false)->toBeTrue();
});
