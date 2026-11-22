<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\Look;

final class Divider implements DesignComponent
{
    use IsDesignComponent;

    /** @var array<string, int|string> */
    private array $attributes = [];

    protected function build(): MainBackendComponent
    {
        return $this->makeComponent(ComponentEnum::DIV)
            ->setThemeManager($this->design->themeManager)
            ->setTheme('divider', Look::Default->value)
            ->setAttribute('role', 'separator')
            ->setAttributes($this->attributes);
    }
}
