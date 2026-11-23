<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class Card implements DesignComponent
{
    use IsDesignComponent;

    private string|DesignComponent|BackendComponent|null $title = null;

    private string|DesignComponent|BackendComponent|null $body = null;

    private string|DesignComponent|BackendComponent|null $footer = null;

    private int $titleLevel = 3;

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

    public function footer(string|DesignComponent|BackendComponent $footer): static
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * The heading level of a string title, clamped to the six levels HTML
     * has. A card usually sits under a section heading, so it starts at 3.
     */
    public function titleLevel(int $level): static
    {
        $this->titleLevel = max(1, min(6, $level));

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $title = $this->title === null ? null : $this->unwrap($this->title);
        $body = $this->body === null ? null : $this->unwrap($this->body);
        $footer = $this->footer === null ? null : $this->unwrap($this->footer);

        $contents = [];

        if ($title !== null) {
            // A string title is a heading, so cards take part in the
            // document outline. A component title keeps the card's title
            // chrome and brings whatever it built.
            $contents[] = $title instanceof BackendComponent
                ? $this->section($title, 'title')
                : $this->heading($title);
        }

        if ($body !== null) {
            $contents[] = $this->section($body, 'body');
        }

        if ($footer !== null) {
            $contents[] = $this->section($footer, 'foot');
        }

        return $this->makeComponent(ComponentEnum::DIV)
            ->setThemeManager($this->design->themeManager)
            ->setContents($contents)
            ->setTheme('card', 'container')
            ->setTheme('radius', 'md')
            ->setAttributes($this->attributes);
    }

    private function heading(string $text): BackendComponent
    {
        $enum = match ($this->titleLevel) {
            1 => ComponentEnum::H1,
            2 => ComponentEnum::H2,
            4 => ComponentEnum::H4,
            5 => ComponentEnum::H5,
            6 => ComponentEnum::H6,
            default => ComponentEnum::H3,
        };

        return $this->makeComponent($enum)
            ->setThemeManager($this->design->themeManager)
            ->setContent($text)
            ->setTheme('card', 'title')
            ->setTheme('text', 'lg');
    }

    /**
     * Card sections are containers, not content: their theme key applies
     * whether the caller passed text or a component, so padding and the
     * separating rules survive a component placed inside them.
     */
    private function section(string|BackendComponent $content, string $variant): BackendComponent
    {
        return $this->makeComponent(ComponentEnum::DIV)
            ->setThemeManager($this->design->themeManager)
            ->setContent($content)
            ->setTheme('card', $variant);
    }
}
