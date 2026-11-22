<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;

final class Label implements DesignComponent
{
    use IsDesignComponent;

    private string $text = '';

    private ?string $for = null;

    /** @var array<string, int|string> */
    private array $attributes = [];

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
            ->setTheme('label', 'default');

        if ($this->for !== null) {
            $label->setAttribute('for', $this->for);
        }

        return $label->setAttributes($this->attributes);
    }
}
