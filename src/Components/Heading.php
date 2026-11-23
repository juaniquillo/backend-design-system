<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\HeadingLevel;

final class Heading implements DesignComponent
{
    use IsDesignComponent;

    private string $text = '';

    private HeadingLevel $level = HeadingLevel::H2;

    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    /**
     * The heading level. Integers are clamped to the six levels HTML has.
     */
    public function level(HeadingLevel|int $level): static
    {
        $this->level = $level instanceof HeadingLevel
            ? $level
            : $this->levelFromInt($level);

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $enum = match ($this->level) {
            HeadingLevel::H1 => ComponentEnum::H1,
            HeadingLevel::H2 => ComponentEnum::H2,
            HeadingLevel::H3 => ComponentEnum::H3,
            HeadingLevel::H4 => ComponentEnum::H4,
            HeadingLevel::H5 => ComponentEnum::H5,
            HeadingLevel::H6 => ComponentEnum::H6,
        };

        return $this->makeComponent($enum)
            ->setThemeManager($this->design->themeManager)
            ->setContent($this->text)
            ->setTheme('heading', $this->level->value)
            ->setAttributes($this->attributes);
    }

    private function levelFromInt(int $level): HeadingLevel
    {
        return match (max(1, min(6, $level))) {
            1 => HeadingLevel::H1,
            2 => HeadingLevel::H2,
            3 => HeadingLevel::H3,
            4 => HeadingLevel::H4,
            5 => HeadingLevel::H5,
            default => HeadingLevel::H6,
        };
    }
}
