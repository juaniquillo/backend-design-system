<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Designs;

use Juaniquillo\BaseBackendComponent\Concerns\IsThemeManager;
use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;

/**
 * Resolves the bundled dark-only theme files.
 */
final class BaseDarkThemeManager implements ThemeManager
{
    use IsThemeManager;

    public function __construct()
    {
        $this->setDefaultPath(__DIR__.'/../../resources/views/backend-design-system/base-dark');
        $this->setThemeExtension('.blade.php');
    }
}
