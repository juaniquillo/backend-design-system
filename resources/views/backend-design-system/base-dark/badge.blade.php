<?php

use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;

// Base dark: dark only, chips with padded edges.
return [
    BadgeVariant::Default->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-zinc-800 text-zinc-100',
    BadgeVariant::Success->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-green-950 text-green-200',
    BadgeVariant::Info->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-blue-950 text-blue-200',
    BadgeVariant::Warning->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-yellow-950 text-yellow-200',
    BadgeVariant::Danger->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-red-950 text-red-200',
];
