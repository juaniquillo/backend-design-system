<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\Size;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class Stack implements DesignComponent
{
    use IsDesignComponent;

    /** @var array<int, DesignComponent|BackendComponent> */
    private array $children = [];

    private Size $space = Size::Md;

    /** @var array<string, int|string> */
    private array $attributes = [];

    /**
     * @param  array<int, DesignComponent|BackendComponent>  $children
     */
    public function children(array $children): static
    {
        $this->children = $children;

        return $this;
    }

    public function space(Size $space): static
    {
        $this->space = $space;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $contents = [];

        foreach ($this->children as $child) {
            $contents[] = $child instanceof DesignComponent ? $child->component() : $child;
        }

        return $this->makeComponent(ComponentEnum::DIV)
            ->setThemeManager($this->design->themeManager)
            ->setContents($contents)
            ->setTheme('stack', 'container')
            ->setTheme('space', $this->spaceKey())
            ->setAttributes($this->attributes);
    }

    private function spaceKey(): string
    {
        return match ($this->space) {
            Size::Sm => 'sm',
            Size::Md => 'md',
            Size::Lg => 'lg',
        };
    }
}
