<?php

use Juaniquillo\BackendDesignSystem\Theme\Look;
use Juaniquillo\BackendDesignSystem\Theme\State;

// Base design: inline links with a current state, light only.
return [
    Look::Default->value => 'text-blue-700 underline underline-offset-2 hover:text-blue-900',
    State::Current->value => 'font-semibold text-zinc-900 no-underline',
];
