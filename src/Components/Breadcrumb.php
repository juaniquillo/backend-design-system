<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\State;

final class Breadcrumb implements DesignComponent
{
    use IsDesignComponent;

    /** @var array<int, array{label: string, href?: string|null, attributes?: array<string, int|string|null>}> */
    private array $items = [];

    /**
     * @param  array<int, array{label: string, href?: string|null, attributes?: array<string, int|string|null>}>  $items
     */
    public function items(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $list = $this->makeComponent(ComponentEnum::OL)
            ->setThemeManager($this->design->themeManager)
            ->setTheme('breadcrumb', 'list');

        $count = count($this->items);

        foreach ($this->items as $index => $item) {
            $label = $item['label'];
            $href = $item['href'] ?? null;
            $current = $index === $count - 1;
            $hasHref = is_string($href) && $href !== '';

            $link = $this->makeComponent($hasHref && ! $current ? ComponentEnum::LINK : ComponentEnum::SPAN)
                ->setThemeManager($this->design->themeManager)
                ->setContent($label)
                ->setTheme('breadcrumb', $current ? State::Current->value : 'link');

            if ($hasHref && ! $current) {
                $link->setAttribute('href', $href);
            }

            if ($current) {
                $link->setAttribute('aria-current', 'page');
            }

            $link->setAttributes($item['attributes'] ?? []);

            $contents = [$link];

            if (! $current) {
                $contents[] = $this->makeComponent(ComponentEnum::SPAN)
                    ->setThemeManager($this->design->themeManager)
                    ->setContent('/')
                    ->setTheme('breadcrumb', 'separator')
                    ->setAttribute('aria-hidden', 'true');
            }

            $list->setContent(
                $this->makeComponent(ComponentEnum::LI)
                    ->setThemeManager($this->design->themeManager)
                    ->setContents($contents)
                    ->setTheme('breadcrumb', 'item'),
            );
        }

        return $this->makeComponent(ComponentEnum::DIV)
            ->setThemeManager($this->design->themeManager)
            ->setContents([$list])
            ->setTheme('breadcrumb', 'container')
            ->setAttribute('role', 'navigation')
            ->setAttribute('aria-label', 'Breadcrumb')
            ->setAttributes($this->attributes);
    }
}
