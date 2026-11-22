<?php

declare(strict_types=1);

namespace Juaniquillo\BackendDesignSystem\Designs;

use Juaniquillo\BaseBackendComponent\Concerns\IsThemeManager;
use Juaniquillo\BaseBackendComponent\Contracts\ThemeManager;

/**
 * Resolves the bundled adaptive theme files.
 */
final class BaseDualThemeManager implements ThemeManager
{
    use IsThemeManager;

    public function __construct()
    {
        $this->setDefaultPath(__DIR__.'/../../resources/views/backend-design-system/base-dual');
        $this->setThemeExtension('.blade.php');
    }
}
