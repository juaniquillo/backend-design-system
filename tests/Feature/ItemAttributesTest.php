<?php

declare(strict_types=1);

use Juaniquillo\BackendComponents\Utils\CellBag;
use Juaniquillo\BackendDesignSystem\Components\Breadcrumb;
use Juaniquillo\BackendDesignSystem\Components\Table;
use Juaniquillo\BackendDesignSystem\Components\Tabs;
use Juaniquillo\BackendDesignSystem\Designs\Design;

it('renders tabs item attributes on the link', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Tabs::make($design)->items([
            ['label' => 'All', 'href' => '#all', 'attributes' => ['x-on:click.prevent' => "tab = 'all'", 'data-tab' => 'all']],
            ['label' => 'Mine', 'href' => '#mine'],
        ]),
    ]);

    $view->assertSee('x-on:click.prevent', false)
        ->assertSee('data-tab="all"', false)
        ->assertSee("tab = 'all'", false);
});

it('lets tabs item attributes override href and aria-current', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Tabs::make($design)->items([
            ['label' => 'General', 'href' => '/settings', 'current' => true, 'attributes' => ['href' => '/override', 'aria-current' => 'false']],
        ]),
    ]);

    $view->assertSee('href="/override"', false)
        ->assertDontSee('href="/settings"', false)
        ->assertSee('aria-current="false"', false)
        ->assertDontSee('aria-current="page"', false);
});

it('renders breadcrumb item attributes on the crumb only', function () {
    $design = $this->app->make(Design::class);

    $html = Breadcrumb::make($design)->items([
        ['label' => 'Home', 'href' => '/', 'attributes' => ['data-crumb' => 'home']],
        ['label' => 'Docs', 'href' => '/docs'],
        ['label' => 'Current'],
    ])->toHtml();

    expect(substr_count($html, 'data-crumb="home"'))->toBe(1)
        ->and($html)->toContain('href="/"')
        ->and($html)->toContain('aria-hidden="true"');
});

it('lets breadcrumb item attributes override href and aria-current', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Breadcrumb::make($design)->items([
            ['label' => 'Home', 'href' => '/', 'attributes' => ['href' => '/override']],
            ['label' => 'Current', 'attributes' => ['aria-current' => 'false']],
        ]),
    ]);

    $view->assertSee('href="/override"', false)
        ->assertDontSee('href="/"', false)
        ->assertSee('aria-current="false"', false);
});

it('renders items without attributes byte-identically to before', function () {
    $design = $this->app->make(Design::class);

    $tabsPlain = Tabs::make($design)->items([
        ['label' => 'General', 'href' => '/settings', 'current' => true],
        ['label' => 'Billing', 'href' => '/settings/billing'],
    ])->toHtml();

    $tabsEmptyAttributes = Tabs::make($design)->items([
        ['label' => 'General', 'href' => '/settings', 'current' => true, 'attributes' => []],
        ['label' => 'Billing', 'href' => '/settings/billing', 'attributes' => []],
    ])->toHtml();

    expect($tabsEmptyAttributes)->toBe($tabsPlain);

    $breadcrumbPlain = Breadcrumb::make($design)->items([
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Current'],
    ])->toHtml();

    $breadcrumbEmptyAttributes = Breadcrumb::make($design)->items([
        ['label' => 'Home', 'href' => '/', 'attributes' => []],
        ['label' => 'Current', 'attributes' => []],
    ])->toHtml();

    expect($breadcrumbEmptyAttributes)->toBe($breadcrumbPlain);

    $tablePlain = Table::make($design)
        ->headers(['Name', 'Role'])
        ->rows([['Alice', 'Admin']])
        ->toHtml();

    $tableCells = Table::make($design)
        ->headers(['Name', 'Role'])
        ->rows([['cells' => ['Alice', 'Admin'], 'attributes' => []]])
        ->toHtml();

    expect($tableCells)->toBe($tablePlain);
});

it('renders table cellbag attributes and themes on cells', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Table::make($design)
            ->headers([
                'Name',
                new CellBag(content: 'Role', attributes: ['data-head' => 'role']),
            ])
            ->rows([
                ['Alice', new CellBag(content: 'Admin', attributes: ['data-cell' => 'role'])],
                ['cells' => ['Bob', 'Editor'], 'attributes' => ['data-row' => 'bob']],
            ])
            ->footer(['Total', new CellBag(content: '2', attributes: ['data-foot' => 'count'])]),
    ]);

    $view->assertSee('data-head="role"', false)
        ->assertSee('data-cell="role"', false)
        ->assertSee('data-row="bob"', false)
        ->assertSee('data-foot="count"', false)
        ->assertSee('scope="col"', false);
});

it('renders table cellbag themes through the design theme manager', function () {
    $design = $this->app->make(Design::class);

    $html = Table::make($design)
        ->headers([new CellBag(content: 'Name', theme: ['table' => 'cell'])])
        ->rows([[new CellBag(content: 'Alice', theme: ['table' => 'cell'])]])
        ->toHtml();

    expect($html)->toContain('Name')
        ->and($html)->toContain('Alice')
        ->and($html)->toContain('px-4 py-2 align-middle')
        ->and($html)->not->toContain('px-3 py-3');
});

it('passes item attribute values through the attribute bag', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Tabs::make($design)->items([
            ['label' => 'General', 'href' => '/settings', 'attributes' => ['data-tip' => 'a"b']],
        ]),
    ]);

    $view->assertSee('data-tip="a\\"b"', false);
});
