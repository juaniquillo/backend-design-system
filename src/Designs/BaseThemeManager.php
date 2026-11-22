<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Designs;

use Juaniquillo\BaseBackendComponent\Concerns\IsThemeManager;
use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;

/**
 * Resolves the bundled base theme files.
 */
final class BaseThemeManager implements ThemeManager
{
    use IsThemeManager;

    public function __construct()
    {
        $this->setDefaultPath(__DIR__.'/../../resources/views/backend-design-system/base');
        $this->setThemeExtension('.blade.php');
    }
}
