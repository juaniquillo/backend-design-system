<?php

use Juaniquillo\BackendDesignSystem\Theme\State;

// Base dual: navigation tabs, answering to the theme toggle.
return [
    'container' => 'border-b border-zinc-300 text-sm dark:border-zinc-700',
    'list' => 'flex flex-wrap gap-2',
    'item' => 'flex',
    'link' => 'border-b-2 border-transparent px-3 py-2 text-zinc-600 hover:border-zinc-300 hover:text-zinc-900 dark:text-zinc-400 dark:hover:border-zinc-700 dark:hover:text-zinc-100',
    State::Current->value => 'border-b-2 border-zinc-900 px-3 py-2 font-medium text-zinc-900 dark:border-zinc-100 dark:text-zinc-100',
];
