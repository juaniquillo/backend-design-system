<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendComponents\Utils\CellBag;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

use function Juaniquillo\BackendComponents\isCellBag;

final class Table implements DesignComponent
{
    use IsDesignComponent;

    /** @var array<int, string|int|DesignComponent|BackendComponent|CellBag> */
    private array $headers = [];

    /** @var array<int, array<int, string|int|DesignComponent|BackendComponent|CellBag>|array{cells: array<int, string|int|DesignComponent|BackendComponent|CellBag>, attributes?: array<string, int|string|null>}> */
    private array $rows = [];

    /** @var array<int, string|int|DesignComponent|BackendComponent|CellBag> */
    private array $footer = [];

    private string|DesignComponent|BackendComponent|null $caption = null;

    private string|DesignComponent|BackendComponent|null $empty = null;

    /**
     * @param  array<int, string|int|DesignComponent|BackendComponent|CellBag>  $headers
     */
    public function headers(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param  array<int, array<int, string|int|DesignComponent|BackendComponent|CellBag>|array{cells: array<int, string|int|DesignComponent|BackendComponent|CellBag>, attributes?: array<string, int|string|null>}>  $rows
     */
    public function rows(array $rows): static
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @param  array<int, string|int|DesignComponent|BackendComponent|CellBag>  $footer
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

    private function makeTableCell(ComponentEnum $type, string|int|DesignComponent|BackendComponent|CellBag $cell, string $themeKey): MainBackendComponent
    {
        $raw = isCellBag($cell) ? $cell->content : $cell;

        $content = $raw instanceof DesignComponent ? $raw->component() : $raw;

        $component = $this->makeComponent($type)
            ->setThemeManager($this->design->themeManager)
            ->setContent($content)
            ->setTheme('table', $themeKey);

        if (isCellBag($cell) && $cell->theme !== null) {
            $component->setThemes($cell->theme);
        }

        if ($type === ComponentEnum::TH) {
            $component->setAttribute('scope', 'col');
        }

        if (isCellBag($cell) && $cell->attributes !== null) {
            $component->setAttributes($cell->attributes);
        }

        return $component;
    }

    /**
     * @param  array<int, string|int|DesignComponent|BackendComponent|CellBag>|array{cells: array<int, string|int|DesignComponent|BackendComponent|CellBag>, attributes?: array<string, int|string|null>}  $row
     * @return array{cells: array<int, string|int|DesignComponent|BackendComponent|CellBag>, attributes: array<string, int|string|null>}
     */
    private function normalizeRow(array $row): array
    {
        if (array_key_exists('cells', $row)) {
            /** @var array{cells: array<int, string|int|DesignComponent|BackendComponent|CellBag>, attributes?: array<string, int|string|null>} $row */
            return [
                'cells' => $row['cells'],
                'attributes' => $row['attributes'] ?? [],
            ];
        }

        /** @var array<int, string|int|DesignComponent|BackendComponent|CellBag> $row */
        return [
            'cells' => $row,
            'attributes' => [],
        ];
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
                $cells[] = $this->makeTableCell(ComponentEnum::TH, $header, 'header');
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
                $normalized = $this->normalizeRow($row);

                $cells = [];

                foreach ($normalized['cells'] as $cell) {
                    $cells[] = $this->makeTableCell(ComponentEnum::TD, $cell, 'cell');
                }

                $bodyRow = $this->makeComponent(ComponentEnum::TR)
                    ->setThemeManager($this->design->themeManager)
                    ->setContents($cells)
                    ->setTheme('table', 'row');

                if ($normalized['attributes'] !== []) {
                    $bodyRow->setAttributes($normalized['attributes']);
                }

                $bodyRows[] = $bodyRow;
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
                $cells[] = $this->makeTableCell(ComponentEnum::TD, $cell, 'cell');
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
