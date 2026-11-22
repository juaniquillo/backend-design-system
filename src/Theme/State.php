<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Theme;

/**
 * The states a component can be in, beyond its look.
 */
enum State: string
{
    case Disabled = 'state.disabled';
    case Loading = 'state.loading';
    case Current = 'state.current';
}
