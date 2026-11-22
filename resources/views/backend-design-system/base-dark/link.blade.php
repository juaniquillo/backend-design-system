<?php

use Juaniquillo\BackendDesignSystem\Theme\Look;
use Juaniquillo\BackendDesignSystem\Theme\State;

// Base dark: inline links with a current state, dark only.
return [
    Look::Default->value => 'text-blue-300 underline underline-offset-2 hover:text-blue-200',
    State::Current->value => 'font-semibold text-zinc-100 no-underline',
];
