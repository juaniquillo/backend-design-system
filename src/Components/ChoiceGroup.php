<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Components;

use Juaniquillo\BackendComponents\Enums\ComponentEnum;
use Juaniquillo\BackendComponents\MainBackendComponent;
use Juaniquillo\BackendDesignSystem\Concerns\IsDesignComponent;
use Juaniquillo\BackendDesignSystem\Contracts\DesignComponent;
use Juaniquillo\BaseBackendComponent\Contracts\BackendComponent;

final class ChoiceGroup implements DesignComponent
{
    use IsDesignComponent;

    private string $legend = '';

    /** @var array<int, DesignComponent|BackendComponent> */
    private array $choices = [];

    private string|DesignComponent|BackendComponent|null $error = null;

    private ?string $id = null;

    public function legend(string $legend): static
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * @param  array<int, DesignComponent|BackendComponent>  $choices
     */
    public function choices(array $choices): static
    {
        $this->choices = $choices;

        return $this;
    }

    public function error(string|DesignComponent|BackendComponent|null $error): static
    {
        $this->error = $error;

        return $this;
    }

    public function id(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    protected function build(): MainBackendComponent
    {
        $errorText = $this->error === null ? null : $this->unwrap($this->error);

        $contents = [
            $this->makeComponent(ComponentEnum::LEGEND)
                ->setThemeManager($this->design->themeManager)
                ->setContent($this->legend)
                ->setTheme('choice-group', 'legend'),
        ];

        $choices = [];

        foreach ($this->choices as $choice) {
            $choices[] = $choice instanceof DesignComponent ? $choice->component() : $choice;
        }

        if ($choices !== []) {
            $contents[] = $this->makeComponent(ComponentEnum::DIV)
                ->setThemeManager($this->design->themeManager)
                ->setContents($choices)
                ->setTheme('choice-group', 'choices');
        }

        $fieldset = $this->makeComponent(ComponentEnum::FIELDSET)
            ->setThemeManager($this->design->themeManager)
            ->setContents($contents)
            ->setTheme('choice-group', 'container');

        if ($this->id !== null) {
            $fieldset->setAttribute('id', $this->id);
        }

        if ($errorText !== null) {
            $fieldset->setAttribute('aria-invalid', 'true');

            $error = $this->makeComponent(ComponentEnum::DIV)
                ->setThemeManager($this->design->themeManager)
                ->setContent($errorText)
                ->setTheme('choice-group', 'error');

            if ($this->id !== null) {
                $errorId = $this->id.'-error';

                $error->setAttribute('id', $errorId);
                $fieldset->setAttribute('aria-describedby', $errorId);
            }

            $fieldset->setContent($error);
        }

        return $fieldset->setAttributes($this->attributes);
    }
}
