<?php

use Juaniquillo\BackendDesignSystem\Theme\State;

// Base dark: breadcrumb trail, dark only.
return [
    'container' => 'text-sm',
    'list' => 'flex flex-wrap items-center',
    'item' => 'flex items-center',
    'link' => 'text-blue-300 hover:text-blue-200',
    State::Current->value => 'font-medium text-zinc-100',
    'separator' => 'mx-2 text-zinc-600',
];
