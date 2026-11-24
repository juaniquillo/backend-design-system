<?php

use Juaniquillo\BackendDesignSystem\Theme\State;

// Base dark: navigation tabs, dark only.
return [
    'container' => 'border-b border-zinc-700 text-sm',
    'list' => 'flex flex-wrap gap-2',
    'item' => 'flex',
    'link' => 'border-b-2 border-transparent px-3 py-2 text-zinc-400 hover:border-zinc-700 hover:text-zinc-100',
    State::Current->value => 'border-b-2 border-zinc-100 px-3 py-2 font-medium text-zinc-100',
];
