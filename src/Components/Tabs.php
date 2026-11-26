<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BackendDesignSystem\Theme\State;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class Tabs implements DesignComponent
{
    use IsDesignComponent;

    /** @var array<int, array{label: string|DesignComponent|BackendComponent, href?: string|null, current?: bool, attributes?: array<string, int|string|null>}> */
    private array $items = [];

    private string $label = 'Tabs';

    /**
     * @param  array<int, array{label: string|DesignComponent|BackendComponent, href?: string|null, current?: bool, attributes?: array<string, int|string|null>}>  $items
     */
    public function items(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $list = $this->makeComponent(ComponentEnum::UL)
            ->setThemeManager($this->design->themeManager)
            ->setTheme('tabs', 'list');

        foreach ($this->items as $item) {
            $label = $this->unwrap($item['label']);
            $href = $item['href'] ?? null;
            $current = $item['current'] ?? false;
            $hasHref = is_string($href) && $href !== '';

            $tab = $this->makeComponent($hasHref ? ComponentEnum::LINK : ComponentEnum::SPAN)
                ->setThemeManager($this->design->themeManager)
                ->setContent($label)
                ->setTheme('tabs', $current ? State::Current->value : 'link');

            if ($hasHref) {
                $tab->setAttribute('href', $href);
            }

            if ($current) {
                $tab->setAttribute('aria-current', 'page');
            }

            $tab->setAttributes($item['attributes'] ?? []);

            $list->setContent(
                $this->makeComponent(ComponentEnum::LI)
                    ->setThemeManager($this->design->themeManager)
                    ->setContents([$tab])
                    ->setTheme('tabs', 'item'),
            );
        }

        return $this->makeComponent(ComponentEnum::DIV)
            ->setThemeManager($this->design->themeManager)
            ->setContents([$list])
            ->setTheme('tabs', 'container')
            ->setAttribute('role', 'navigation')
            ->setAttribute('aria-label', $this->label)
            ->setAttributes($this->attributes);
    }
}
