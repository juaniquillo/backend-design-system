<?php

use Juaniquillo\BackendDesignSystem\Theme\State;

// Base design: navigation tabs, light only.
return [
    'container' => 'border-b border-zinc-300 text-sm',
    'list' => 'flex flex-wrap gap-2',
    'item' => 'flex',
    'link' => 'border-b-2 border-transparent px-3 py-2 text-zinc-600 hover:border-zinc-300 hover:text-zinc-900',
    State::Current->value => 'border-b-2 border-zinc-900 px-3 py-2 font-medium text-zinc-900',
];
