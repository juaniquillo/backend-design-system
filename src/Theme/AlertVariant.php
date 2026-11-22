<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Theme;

enum AlertVariant: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
}
