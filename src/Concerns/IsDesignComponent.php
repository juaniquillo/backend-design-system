<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Concerns;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\Factories\ComponentFactory;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Designs\Design;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;
use RuntimeException;

/**
 * The boilerplate every design component shares.
 *
 * A component declares its slots and what it builds; the trait supplies
 * make(), the design it renders with, the attribute escape hatch, the
 * Htmlable bridge and slot unwrapping. build() is abstract on purpose: a
 * component that forgets it fails when the class is declared, not when a
 * page renders.
 */
trait IsDesignComponent
{
    private readonly Design $design;

    /** @var array<string, int|string> */
    private array $attributes = [];

    public static function make(?Design $design = null): static
    {
        return new static($design ?? app(Design::class));
    }

    private function __construct(Design $design)
    {
        $this->design = $design;
    }

    /**
     * @param  array<string, int|string>  $attributes
     */
    public function attributes(array $attributes): static
    {
        $this->attributes = [...$this->attributes, ...$attributes];

        return $this;
    }

    /**
     * A slot accepts a string, a design component or a backend component.
     * Design components are unwrapped here so the tree is built from
     * backend components only.
     */
    protected function unwrap(string|DesignComponent|BackendComponent $content): string|BackendComponent
    {
        return $content instanceof DesignComponent ? $content->component() : $content;
    }

    public function component(): MainBackendComponent
    {
        return $this->build();
    }

    abstract protected function build(): MainBackendComponent;

    public function toHtml(): string
    {
        return $this->component()->toHtml();
    }

    /**
     * The factory returns a union; a design component always needs the
     * concrete class, and the trait narrows it once for every component.
     */
    protected function makeComponent(ComponentEnum|string $name): MainBackendComponent
    {
        $component = ComponentFactory::make($name);

        if (! $component instanceof MainBackendComponent) {
            throw new RuntimeException('Design components build MainBackendComponent instances.');
        }

        return $component;
    }
}
