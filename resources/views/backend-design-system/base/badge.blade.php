<?php

use Juaniquillo\BackendDesignSystem\Theme\BadgeVariant;

// Base design: light only, chips with padded edges.
return [
    BadgeVariant::Default->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-zinc-200 text-black',
    BadgeVariant::Success->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-green-100 text-green-900',
    BadgeVariant::Info->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-900',
    BadgeVariant::Warning->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-yellow-100 text-yellow-900',
    BadgeVariant::Danger->value => 'inline-flex items-center px-2.5 py-1 text-xs font-medium bg-red-100 text-red-900',
];
