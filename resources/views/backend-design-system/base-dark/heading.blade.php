<?php

use Juaniquillo\BackendDesignSystem\Theme\HeadingLevel;

// Base dark: heading scale, dark only.
return [
    HeadingLevel::H1->value => 'text-3xl font-bold text-zinc-100',
    HeadingLevel::H2->value => 'text-2xl font-bold text-zinc-100',
    HeadingLevel::H3->value => 'text-xl font-bold text-zinc-100',
    HeadingLevel::H4->value => 'text-lg font-semibold text-zinc-100',
    HeadingLevel::H5->value => 'text-base font-semibold text-zinc-100',
    HeadingLevel::H6->value => 'text-sm font-semibold text-zinc-100',
];
