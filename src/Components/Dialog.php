<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class Dialog implements DesignComponent
{
    use IsDesignComponent;

    private string|DesignComponent|BackendComponent|null $title = null;

    private string|DesignComponent|BackendComponent|null $body = null;

    private string|DesignComponent|BackendComponent|null $footer = null;

    private int $titleLevel = 2;

    private bool $open = false;

    private ?string $id = null;

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
     * has. A dialog is an independent layer, so it starts at 2.
     */
    public function titleLevel(int $level): static
    {
        $this->titleLevel = max(1, min(6, $level));

        return $this;
    }

    public function open(bool $open = true): static
    {
        $this->open = $open;

        return $this;
    }

    public function id(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $title = $this->title === null ? null : $this->unwrap($this->title);
        $body = $this->body === null ? null : $this->unwrap($this->body);
        $footer = $this->footer === null ? null : $this->unwrap($this->footer);

        $contents = [];

        if ($title !== null) {
            $titleSection = $title instanceof BackendComponent
                ? $this->section($title, 'title')
                : $this->heading($title);

            if ($this->id !== null) {
                $titleSection->setAttribute('id', $this->id.'-title');
            }

            $contents[] = $titleSection;
        }

        if ($body !== null) {
            $contents[] = $this->section($body, 'body');
        }

        if ($footer !== null) {
            $contents[] = $this->section($footer, 'foot');
        }

        $dialog = $this->makeComponent(ComponentEnum::DIALOG)
            ->setThemeManager($this->design->themeManager)
            ->setContents($contents)
            ->setTheme('dialog', 'container')
            ->setTheme('radius', 'md');

        if ($this->id !== null) {
            $dialog->setAttribute('id', $this->id);

            if ($title !== null) {
                $dialog->setAttribute('aria-labelledby', $this->id.'-title');
            }
        }

        if ($this->open) {
            $dialog->setAttribute('open', 'open');
        }

        return $dialog->setAttributes($this->attributes);
    }

    private function heading(string $text): BackendComponent
    {
        $enum = match ($this->titleLevel) {
            1 => ComponentEnum::H1,
            3 => ComponentEnum::H3,
            4 => ComponentEnum::H4,
            5 => ComponentEnum::H5,
            6 => ComponentEnum::H6,
            default => ComponentEnum::H2,
        };

        return $this->makeComponent($enum)
            ->setThemeManager($this->design->themeManager)
            ->setContent($text)
            ->setTheme('dialog', 'title')
            ->setTheme('text', 'lg');
    }

    private function section(string|BackendComponent $content, string $variant): BackendComponent
    {
        return $this->makeComponent(ComponentEnum::DIV)
            ->setThemeManager($this->design->themeManager)
            ->setContent($content)
            ->setTheme('dialog', $variant);
    }
}
