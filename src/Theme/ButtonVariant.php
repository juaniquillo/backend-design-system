<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Theme;

enum ButtonVariant: string
{
    case Default = 'default';
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Danger = 'danger';
    case Ghost = 'ghost';
}
