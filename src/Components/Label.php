<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\Look;

final class Label implements DesignComponent
{
    use IsDesignComponent;

    private string $text = '';

    private ?string $for = null;

    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function for(?string $for = null): static
    {
        $this->for = $for;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $label = $this->makeComponent(ComponentEnum::LABEL)
            ->setThemeManager($this->design->themeManager)
            ->setContent($this->text)
            ->setTheme('label', Look::Default->value);

        if ($this->for !== null) {
            $label->setAttribute('for', $this->for);
        }

        return $label->setAttributes($this->attributes);
    }
}
