<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class Table implements DesignComponent
{
    use IsDesignComponent;

    /** @var array<int, string|DesignComponent|BackendComponent> */
    private array $headers = [];

    /** @var array<int, array<int, string|DesignComponent|BackendComponent>> */
    private array $rows = [];

    /** @var array<int, string|DesignComponent|BackendComponent> */
    private array $footer = [];

    private string|DesignComponent|BackendComponent|null $caption = null;

    private string|DesignComponent|BackendComponent|null $empty = null;

    /**
     * @param  array<int, string|DesignComponent|BackendComponent>  $headers
     */
    public function headers(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param  array<int, array<int, string|DesignComponent|BackendComponent>>  $rows
     */
    public function rows(array $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @param  array<int, string|DesignComponent|BackendComponent>  $footer
     */
    public function footer(array $footer): static
    {
        $this->footer = $footer;

        return $this;
    }

    public function caption(string|DesignComponent|BackendComponent $caption): static
    {
        $this->caption = $caption;

        return $this;
    }

    public function empty(string|DesignComponent|BackendComponent $empty): static
    {
        $this->empty = $empty;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $sections = [];

        if ($this->caption !== null) {
            $sections[] = $this->makeComponent(ComponentEnum::CAPTION)
                ->setThemeManager($this->design->themeManager)
                ->setContent($this->unwrap($this->caption))
                ->setTheme('table', 'caption');
        }

        if ($this->headers !== []) {
            $cells = [];

            foreach ($this->headers as $header) {
                $cells[] = $this->makeComponent(ComponentEnum::TH)
                    ->setThemeManager($this->design->themeManager)
                    ->setContent($this->unwrap($header))
                    ->setTheme('table', 'header')
                    ->setAttribute('scope', 'col');
            }

            $sections[] = $this->makeComponent(ComponentEnum::THEAD)
                ->setThemeManager($this->design->themeManager)
                ->setContents([
                    $this->makeComponent(ComponentEnum::TR)
                        ->setThemeManager($this->design->themeManager)
                        ->setContents($cells)
                        ->setTheme('table', 'row'),
                ])
                ->setTheme('table', 'head');
        }

        if ($this->rows !== []) {
            $bodyRows = [];

            foreach ($this->rows as $row) {
                $cells = [];

                foreach ($row as $cell) {
                    $cells[] = $this->makeComponent(ComponentEnum::TD)
                        ->setThemeManager($this->design->themeManager)
                        ->setContent($this->unwrap($cell))
                        ->setTheme('table', 'cell');
                }

                $bodyRows[] = $this->makeComponent(ComponentEnum::TR)
                    ->setThemeManager($this->design->themeManager)
                    ->setContents($cells)
                    ->setTheme('table', 'row');
            }

            $sections[] = $this->makeComponent(ComponentEnum::TBODY)
                ->setThemeManager($this->design->themeManager)
                ->setContents($bodyRows)
                ->setTheme('table', 'body');
        } elseif ($this->empty !== null) {
            $emptyCell = $this->makeComponent(ComponentEnum::TD)
                ->setThemeManager($this->design->themeManager)
                ->setContent($this->unwrap($this->empty))
                ->setTheme('table', 'empty');

            $headerCount = count($this->headers);

            if ($headerCount > 1) {
                $emptyCell->setAttribute('colspan', (string) $headerCount);
            }

            $sections[] = $this->makeComponent(ComponentEnum::TBODY)
                ->setThemeManager($this->design->themeManager)
                ->setContents([
                    $this->makeComponent(ComponentEnum::TR)
                        ->setThemeManager($this->design->themeManager)
                        ->setContents([$emptyCell])
                        ->setTheme('table', 'row'),
                ])
                ->setTheme('table', 'body');
        }

        if ($this->footer !== []) {
            $cells = [];

            foreach ($this->footer as $cell) {
                $cells[] = $this->makeComponent(ComponentEnum::TD)
                    ->setThemeManager($this->design->themeManager)
                    ->setContent($this->unwrap($cell))
                    ->setTheme('table', 'cell');
            }

            $sections[] = $this->makeComponent(ComponentEnum::TFOOT)
                ->setThemeManager($this->design->themeManager)
                ->setContents([
                    $this->makeComponent(ComponentEnum::TR)
                        ->setThemeManager($this->design->themeManager)
                        ->setContents($cells)
                        ->setTheme('table', 'row'),
                ])
                ->setTheme('table', 'foot');
        }

        $table = $this->makeComponent(ComponentEnum::TABLE)
            ->setThemeManager($this->design->themeManager)
            ->setContents($sections)
            ->setTheme('table', 'table');

        return $this->makeComponent(ComponentEnum::DIV)
            ->setThemeManager($this->design->themeManager)
            ->setContents([$table])
            ->setTheme('table', 'container')
            ->setAttributes($this->attributes);
    }
}
