<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\Size;

final class Text implements DesignComponent
{
    use IsDesignComponent;

    private string $text = '';

    private Size $size = Size::Md;

    private bool $inline = false;

    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function size(Size $size): static
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Render a span instead of a paragraph.
     */
    public function inline(bool $inline = true): static
    {
        $this->inline = $inline;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        return $this->makeComponent($this->inline ? ComponentEnum::SPAN : ComponentEnum::PARAGRAPH)
            ->setThemeManager($this->design->themeManager)
            ->setContent($this->text)
            ->setTheme('text', $this->sizeKey())
            ->setAttributes($this->attributes);
    }

    private function sizeKey(): string
    {
        return match ($this->size) {
            Size::Sm => 'sm',
            Size::Md => 'md',
            Size::Lg => 'lg',
        };
    }
}
