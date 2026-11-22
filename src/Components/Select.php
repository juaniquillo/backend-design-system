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

final class Select implements DesignComponent
{
    use IsDesignComponent;

    private string $name = '';

    /** @var array<string, string> */
    private array $options = [];

    private ?string $value = null;

    private bool $disabled = false;

    private bool $required = false;

    /** @var array<string, int|string> */
    private array $attributes = [];

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * The options, keyed by value with their labels as values.
     *
     * @param  array<string, string>  $options
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function value(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $variants = ThemeVariants::make()
            ->look(Look::Default)
            ->state($this->disabled ? State::Disabled : null);

        $select = $this->makeComponent(ComponentEnum::SELECT)
            ->setThemeManager($this->design->themeManager)
            ->setTheme('select', $variants->all())
            ->setTheme('radius', 'sm');

        if ($this->name !== '') {
            $select->setAttribute('name', $this->name);
        }

        foreach ($this->options as $value => $label) {
            $option = $this->makeComponent(ComponentEnum::OPTION)
                ->setThemeManager($this->design->themeManager)
                ->setAttribute('value', (string) $value)
                ->setContent($label);

            if ($this->value !== null && (string) $value === $this->value) {
                $option->setAttribute('selected', 'selected');
            }

            $select->setContent($option);
        }

        if ($this->disabled) {
            $select->setAttribute('disabled', 'disabled');
        }

        if ($this->required) {
            $select->setAttribute('required', 'required');
        }

        return $select->setAttributes($this->attributes);
    }
}
