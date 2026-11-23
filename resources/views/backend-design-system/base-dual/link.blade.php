<?php

use Juaniquillo\BackendDesignSystem\Theme\Look;
use Juaniquillo\BackendDesignSystem\Theme\State;

// Base dual: inline links with a current state, answering to the theme toggle.
return [
    Look::Default->value => 'text-blue-700 underline underline-offset-2 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-200',
    State::Current->value => 'font-semibold text-zinc-900 no-underline dark:text-zinc-100',
];
