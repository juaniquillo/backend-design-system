<?php

use Juaniquillo\BackendDesignSystem\Theme\State;

// Base dual: breadcrumb trail, answering to the theme toggle.
return [
    'container' => 'text-sm',
    'list' => 'flex flex-wrap items-center',
    'item' => 'flex items-center',
    'link' => 'text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-200',
    State::Current->value => 'font-medium text-zinc-900 dark:text-zinc-100',
    'separator' => 'mx-2 text-zinc-400 dark:text-zinc-600',
];
