<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\AlertVariant;
use Juaniquillo\BackendDesignSystem\Theme\ThemeVariants;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class Alert implements DesignComponent
{
    use IsDesignComponent;

    private AlertVariant $variant = AlertVariant::Info;

    private string|DesignComponent|BackendComponent|null $title = null;

    private string|DesignComponent|BackendComponent|null $body = null;

    private bool $live = false;

    public function variant(AlertVariant $variant): static
    {
        $this->variant = $variant;

        return $this;
    }

    public function title(string|DesignComponent|BackendComponent $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function body(string|DesignComponent|BackendComponent $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Announce the alert when it appears.
     *
     * Off by default: a static alert on a page should not be read out on
     * load. When on, failures interrupt (role=alert) and the rest wait for
     * a pause (role=status).
     */
    public function live(bool $live = true): static
    {
        $this->live = $live;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $title = $this->title === null ? null : $this->unwrap($this->title);
        $body = $this->body === null ? null : $this->unwrap($this->body);

        $contents = [];

        if ($title !== null) {
            $contents[] = $title instanceof BackendComponent
                ? $title
                : $this->makeComponent(ComponentEnum::BOLD)
                    ->setThemeManager($this->design->themeManager)
                    ->setContent($title);
        }

        if ($body !== null) {
            $contents[] = $body instanceof BackendComponent
                ? $body
                : $this->makeComponent(ComponentEnum::PARAGRAPH)
                    ->setThemeManager($this->design->themeManager)
                    ->setContent($body);
        }

        $attributes = [];

        if ($this->live) {
            $attributes['role'] = in_array($this->variant, [AlertVariant::Warning, AlertVariant::Danger], true) ? 'alert' : 'status';
        }

        return $this->makeComponent(ComponentEnum::DIV)
            ->setThemeManager($this->design->themeManager)
            ->setContents($contents)
            ->setTheme('alert', ThemeVariants::make()->look($this->variant)->all())
            ->setTheme('radius', 'sm')
            ->setAttributes([...$attributes, ...$this->attributes]);
    }
}
