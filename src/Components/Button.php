<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\ButtonVariant;
use Juaniquillo\BackendDesignSystem\Theme\Size;
use Juaniquillo\BackendDesignSystem\Theme\State;
use Juaniquillo\BackendDesignSystem\Theme\ThemeVariants;

final class Button implements DesignComponent
{
    use IsDesignComponent;

    private string $label = '';

    private ButtonVariant $variant = ButtonVariant::Default;

    private Size $size = Size::Md;

    private string $type = 'button';

    private bool $disabled = false;

    /** @var array<string, int|string> */
    private array $attributes = [];

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function variant(ButtonVariant $variant): static
    {
        $this->variant = $variant;

        return $this;
    }

    public function size(Size $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        // One list of variant keys: the look, then the state. State
        // variants are affordances, so a theme never needs a
        // "primary-disabled" for every combination.
        // One list of variant keys: the look, its size, and the state.
        $variants = ThemeVariants::make()
            ->look($this->variant)
            ->size($this->size)
            ->state($this->disabled ? State::Disabled : null);

        $button = $this->makeComponent(ComponentEnum::BUTTON)
            ->setThemeManager($this->design->themeManager)
            ->setContent($this->label)
            ->setTheme('button', $variants->all())
            ->setTheme('radius', 'md')
            ->setAttribute('type', $this->type);

        if ($this->disabled) {
            $button->setAttribute('disabled', 'disabled');
        }

        return $button->setAttributes($this->attributes);
    }
}
