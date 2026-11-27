<?php

declare(strict_types=1);

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\Factories\ComponentFactory;
use Juaniquillo\BackendDesignSystem\Components\Badge;
use Juaniquillo\BackendDesignSystem\Components\Button;
use Juaniquillo\BackendDesignSystem\Components\Checkbox;
use Juaniquillo\BackendDesignSystem\Components\Link;
use Juaniquillo\BackendDesignSystem\Components\Radio;
use Juaniquillo\BackendDesignSystem\Components\Text;
use Juaniquillo\BackendDesignSystem\Designs\Design;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

function iconLabel(): BackendComponent
{
    return ComponentFactory::make(ComponentEnum::SPAN)->setContents([
        ComponentFactory::make(ComponentEnum::SPAN)->setAttribute('class', 'icon-mask'),
        ' Docs',
    ]);
}

it('renders a component label inside the link', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Link::make($design)->label(iconLabel())->href('/docs'),
    ]);

    $view->assertSee('<a', false)
        ->assertSee('href="/docs"', false)
        ->assertSee('class="icon-mask"', false)
        ->assertSee('Docs', false);
});

it('renders a design component label inside the link', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Link::make($design)
            ->label(Text::make($design)->text('Docs')->inline())
            ->href('/docs'),
    ]);

    $view->assertSee('<a', false)
        ->assertSee('<span', false)
        ->assertSee('Docs', false);
});

it('renders string link labels byte-identically to before', function () {
    $design = $this->app->make(Design::class);

    $html = Link::make($design)->label('Docs')->href('/docs')->toHtml();

    expect($html)->toContain('>Docs</a>');
});

it('escapes html in string link labels', function () {
    $design = $this->app->make(Design::class);
    $payload = '<script>alert("x")</script>';

    $view = $this->blade('{{ $component }}', [
        'component' => Link::make($design)->label($payload)->href('/docs'),
    ]);

    $view->assertDontSee('<script>', false)
        ->assertSee('&lt;script&gt;', false);
});

it('keeps link states with a component label', function () {
    $design = $this->app->make(Design::class);

    $view = $this->blade('{{ $component }}', [
        'component' => Link::make($design)
            ->label(iconLabel())
            ->href('/docs')
            ->current()
            ->external(),
    ]);

    $view->assertSee('aria-current="page"', false)
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false)
        ->assertSee('class="icon-mask"', false);
});

it('renders component labels in buttons and badges', function () {
    $design = $this->app->make(Design::class);

    $buttonView = $this->blade('{{ $component }}', [
        'component' => Button::make($design)->label(iconLabel())->disabled(),
    ]);

    $buttonView->assertSee('<button', false)
        ->assertSee('disabled="disabled"', false)
        ->assertSee('class="icon-mask"', false);

    $badgeView = $this->blade('{{ $component }}', [
        'component' => Badge::make($design)->label(iconLabel()),
    ]);

    $badgeView->assertSee('<span', false)
        ->assertSee('class="icon-mask"', false);
});

it('renders string button and badge labels byte-identically to before', function () {
    $design = $this->app->make(Design::class);

    expect(Button::make($design)->label('Save')->toHtml())->toContain('>Save</button>')
        ->and(Badge::make($design)->label('New')->toHtml())->toContain('>New</span>');
});

it('renders component labels in checkboxes and radios', function () {
    $design = $this->app->make(Design::class);

    $checkboxView = $this->blade('{{ $component }}', [
        'component' => Checkbox::make($design)->name('terms')->label(iconLabel())->checked(),
    ]);

    $checkboxView->assertSee('checked="checked"', false)
        ->assertSee('class="icon-mask"', false);

    $radioView = $this->blade('{{ $component }}', [
        'component' => Radio::make($design)->name('plan')->value('pro')->label(iconLabel())->checked(),
    ]);

    $radioView->assertSee('checked="checked"', false)
        ->assertSee('class="icon-mask"', false);
});

it('renders string checkbox and radio labels byte-identically to before', function () {
    $design = $this->app->make(Design::class);

    expect(Checkbox::make($design)->name('terms')->label('Terms')->toHtml())->toContain('Terms')
        ->and(Radio::make($design)->name('plan')->value('free')->label('Free')->toHtml())->toContain('Free');
});
