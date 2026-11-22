<?php

use Juaniquillo\BackendDesignSystem\Theme\Look;
use Juaniquillo\BackendDesignSystem\Theme\State;

// Base design: light only.
return [
    Look::Default->value => 'border border-zinc-400 bg-white px-3 py-2 w-full',
    State::Disabled->value => 'opacity-50 cursor-not-allowed',
];
