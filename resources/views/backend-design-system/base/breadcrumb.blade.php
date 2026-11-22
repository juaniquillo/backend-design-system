<?php

use Juaniquillo\BackendDesignSystem\Theme\State;

// Base design: breadcrumb trail, light only.
return [
    'container' => 'text-sm',
    'list' => 'flex flex-wrap items-center',
    'item' => 'flex items-center',
    'link' => 'text-blue-700 hover:text-blue-900',
    State::Current->value => 'font-medium text-zinc-900',
    'separator' => 'mx-2 text-zinc-400',
];
