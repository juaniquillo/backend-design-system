<?php

use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;

// Base dual: chips with padded edges, answering to the theme toggle.
return [
    BadgeVariant::Default->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-zinc-200 text-black dark:bg-zinc-800 dark:text-zinc-100',
    BadgeVariant::Success->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-green-100 text-green-900 dark:bg-green-950 dark:text-green-200',
    BadgeVariant::Info->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-900 dark:bg-blue-950 dark:text-blue-200',
    BadgeVariant::Warning->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-yellow-100 text-yellow-900 dark:bg-yellow-950 dark:text-yellow-200',
    BadgeVariant::Danger->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-red-100 text-red-900 dark:bg-red-950 dark:text-red-200',
];
