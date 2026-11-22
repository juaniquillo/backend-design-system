<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Theme;

enum BadgeVariant: string
{
    case Default = 'default';
    case Success = 'success';
    case Info = 'info';
    case Warning = 'warning';
    case Danger = 'danger';
}
