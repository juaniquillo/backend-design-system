<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\Look;
use Juaniquillo\BackendDesignSystem\Theme\State;
use Juaniquillo\BackendDesignSystem\Theme\ThemeVariants;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class Link implements DesignComponent
{
    use IsDesignComponent;

    private string|DesignComponent|BackendComponent $label = '';

    private string $href = '';

    private bool $current = false;

    private bool $external = false;

    public function label(string|DesignComponent|BackendComponent $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function href(string $href): static
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Mark the link as the current page. It receives aria-current and the
     * current state theme.
     */
    public function current(bool $current = true): static
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Mark the link as opening in a new tab. It receives target and rel.
     */
    public function external(bool $external = true): static
    {
        $this->external = $external;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $variants = ThemeVariants::make()
            ->look(Look::Default)
            ->state($this->current ? State::Current : null);

        $link = $this->makeComponent(ComponentEnum::LINK)
            ->setThemeManager($this->design->themeManager)
            ->setContent($this->unwrap($this->label))
            ->setTheme('link', $variants->all());

        if ($this->href !== '') {
            $link->setAttribute('href', $this->href);
        }

        if ($this->current) {
            $link->setAttribute('aria-current', 'page');
        }

        if ($this->external) {
            $link->setAttribute('target', '_blank');
            $link->setAttribute('rel', 'noopener noreferrer');
        }

        return $link->setAttributes($this->attributes);
    }
}
