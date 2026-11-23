<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class Accordion implements DesignComponent
{
    use IsDesignComponent;

    private string $summary = '';

    private string|BackendComponent|null $content = null;

    private bool $open = false;

    /** @var array<string, int|string> */
    private array $attributes = [];

    public function summary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function content(string|BackendComponent $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function open(bool $open = true): static
    {
        $this->open = $open;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $contents = [
            $this->makeComponent(ComponentEnum::SUMMARY)
                ->setThemeManager($this->design->themeManager)
                ->setContent($this->summary)
                ->setTheme('accordion', 'summary'),
        ];

        if ($this->content !== null) {
            $contents[] = $this->makeComponent(ComponentEnum::DIV)
                ->setThemeManager($this->design->themeManager)
                ->setContent($this->content)
                ->setTheme('accordion', 'content');
        }

        $details = $this->makeComponent(ComponentEnum::DETAILS)
            ->setThemeManager($this->design->themeManager)
            ->setContents($contents)
            ->setTheme('accordion', 'container');

        if ($this->open) {
            $details->setAttribute('open', 'open');
        }

        return $details->setAttributes($this->attributes);
    }
}
