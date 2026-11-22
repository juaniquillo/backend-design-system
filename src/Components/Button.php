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

    private bool $loading = false;

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

    /**
     * Mark the button as busy. A loading button is disabled and carries
     * aria-busy, so a second click cannot start the same action twice.
     */
    public function loading(bool $loading = true): static
    {
        $this->loading = $loading;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        // One list of variant keys: the look, its size, and the state. A
        // loading button replaces the disabled state so the two cursor
        // utilities never compete.
        $variants = ThemeVariants::make()
            ->look($this->variant)
            ->size($this->size)
            ->state($this->disabled && ! $this->loading ? State::Disabled : null)
            ->state($this->loading ? State::Loading : null);

        $button = $this->makeComponent(ComponentEnum::BUTTON)
            ->setThemeManager($this->design->themeManager)
            ->setContent($this->label)
            ->setTheme('button', $variants->all())
            ->setTheme('radius', 'md')
            ->setAttribute('type', $this->type);

        if ($this->disabled || $this->loading) {
            $button->setAttribute('disabled', 'disabled');
        }

        if ($this->loading) {
            $button->setAttribute('aria-busy', 'true');
        }

        return $button->setAttributes($this->attributes);
    }
}
