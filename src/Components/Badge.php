<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;
use Juaniquillo\BackendDesignSystem\Theme\ThemeVariants;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class Badge implements DesignComponent
{
    use IsDesignComponent;

    private string|DesignComponent|BackendComponent $label = '';

    private BadgeVariant $variant = BadgeVariant::Default;

    public function label(string|DesignComponent|BackendComponent $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function variant(BadgeVariant $variant): static
    {
        $this->variant = $variant;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        return $this->makeComponent(ComponentEnum::SPAN)
            ->setThemeManager($this->design->themeManager)
            ->setContent($this->unwrap($this->label))
            ->setTheme('badge', ThemeVariants::make()->look($this->variant)->all())
            ->setTheme('radius', 'sm')
            ->setAttributes($this->attributes);
    }
}
